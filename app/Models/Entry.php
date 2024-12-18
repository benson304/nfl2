<?php

namespace App\Models;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
