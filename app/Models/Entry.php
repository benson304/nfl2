<?php

namespace App\Models;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class Entry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'entry_name',
        'changes_remaining',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function playerChangeHistory()
    {
        return $this->hasMany(PlayerChangeHistory::class);
    }

    public function calculateTotalPoints(): float
    {
        $scoringService = new \App\Services\ScoringService();
        return $scoringService->calculateTotalPoints($this->players->flatMap->stats);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPlayerPoints($player_id,$round)
    {
        $points=Entry::select('entries.id as id','players.name as name','teams.name as team')->leftJoin('entry_player', 'entries.id', '=', 'entry_player.entry_id')
            ->leftJoin('players', 'entry_player.player_id', '=', 'players.id')
            ->leftJoin('teams', 'players.team_id', '=', 'teams.id')
            ->leftJoin('games',function($join) {
                $join->on(\DB::raw('( teams.id = games.home_team_id OR teams.id = games.away_team_id) and 1 '),'=',\DB::raw('1'));
            })
            ->leftJoin('player_stats',function($join) {
                $join->on('players.id', '=', 'player_stats.player_id')
                    ->on('games.id', '=', 'player_stats.game_id');
            })->where('entries.id',$this->id)->where('players.id',$player_id)->where('games.round',$round)
            ->whereRaw('entry_player.created_at < games.kickoff')
            ->where(function ($query) {
                $query->whereNull('entry_player.removed_at')
                    ->orWhereRaw('entry_player.removed_at > games.kickoff');
            })->sum('player_stats.points');

        return($points??0);

    }

    public function swapWithFlex($currentPlayerId,$swapWithId)
    {

        if ($this->user_id !== auth()->id()) {
            abort(403);
        }

//        $validated = $request->validate([
//            'player1_id' => 'required|exists:players,id',
//            'player2_id' => 'required|exists:players,id',
//        ]);

        // Get both players
        $player1 = EntryPlayer::where('entry_id', $this->id)
            ->where('player_id', $currentPlayerId)
            ->whereNull('removed_at')
            ->firstOrFail();

        $player2 = EntryPlayer::where('entry_id', $this->id)
            ->where('player_id', $swapWithId)
            ->whereNull('removed_at')
            ->firstOrFail();

        // Verify one position is FLEX
        if (!in_array('FLEX', [$player1->roster_position, $player2->roster_position])) {
            return back()->withErrors(['message' => 'One position must be FLEX to swap']);
        }

        // Get the player details
        $p1 = Player::findOrFail($currentPlayerId);
        $p2 = Player::findOrFail($swapWithId);

        // Verify the non-FLEX player can be placed in FLEX (RB/WR/TE only)
        if ($player1->roster_position === 'FLEX') {
            if (!in_array($p2->position, ['RB', 'WR', 'TE'])) {
                return back()->withErrors(['message' => 'Only RB/WR/TE positions can be moved to FLEX']);
            }
        } else {
            if (!in_array($p1->position, ['RB', 'WR', 'TE'])) {
                return back()->withErrors(['message' => 'Only RB/WR/TE positions can be moved to FLEX']);
            }
        }

//        // Check if either player is locked
//        $lockedPlayers = collect();
//        if($upcomingGames = Game::where('kickoff', '>=', now())
//            ->where('kickoff', '<=', now()->addDays(2))
//            ->first()) {
//            foreach ([$p1, $p2] as $player) {
//                $currentGame = Game::where(function ($query) use ($player) {
//                    $query->where('home_team_id', $player->team_id)
//                        ->orWhere('away_team_id', $player->team_id);
//                })
//                    ->where('kickoff', '<=', now())
//                    ->where('kickoff', '>=', now()->subDays(2))
//                    ->first();
//
//                if ($currentGame) {
//                    $lockedPlayers->push($player);
//                }
//            }
//        }
//
//        if ($lockedPlayers->count() > 0) {
//            return back()->withErrors(['message' => 'Cannot swap locked players']);
//        }

        // Perform the swap
        $temp = $player1->roster_position;
        $player1->roster_position = $player2->roster_position;
        $player2->roster_position = $temp;

        $player1->save();
        $player2->save();

        return back()->with('success', 'Positions successfully swapped');
    }
    public function current_players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'entry_player')
            ->using(EntryPlayer::class)
            ->whereNull('entry_player.removed_at')
            ->withPivot('roster_position')
            ->withPivot('removed_at')
            ->withTimestamps();
    }
    public function changed_players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'entry_player')
            ->using(EntryPlayer::class)
            ->whereNotNull('entry_player.removed_at')
            ->withPivot('roster_position')
            ->withPivot('removed_at')
            ->withTimestamps();
    }
    public function playerlist()
    {
        return $this->hasMany(EntryPlayer::class);
    }
    public function getChangesRemaining(): float
    {
        return (10-$this->playerList()->count());
    }
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'entry_player')
            ->using(EntryPlayer::class)
            ->withPivot('roster_position')
            ->withPivot('removed_at')
            ->withTimestamps();
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($entry) {
            \Log::info('Entry updating event fired', ['entry' => $entry]);
        });

        static::updated(function ($entry) {
            \Log::info('Entry updated event fired', ['entry' => $entry]);
        });

        Event::listen('eloquent.*', function($event, $data) {
            \Log::info('Eloquent Event: ' . $event, ['data' => $data]);
        });
    }
}
