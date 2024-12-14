<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Player;
use App\Models\Game;
use App\Models\Transaction;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Services\RosterPositionService;
use App\Services\ScoringService;

class EntryController extends Controller
{
    public function index()
    {
        $entries = Entry::where('user_id', auth()->id())
            ->with(['players'])
            ->get();

        return view('entries.index', [
            'entries' => $entries
        ]);
    }


    public function update(Request $request, Entry $entry)
    {
        if ($entry->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'drop_player_id' => 'required|exists:players,id',
            'add_player_id' => 'required|exists:players,id',
            'position' => 'required|string'
        ]);

        if ($entry->changes_remaining <= 0) {
            throw ValidationException::withMessages([
                'changes' => ['No roster changes remaining for this entry.']
            ]);
        }

        // Get the dropped player and their current points
        $droppedPlayer = Player::findOrFail($validated['drop_player_id']);
        $addedPlayer = Player::findOrFail($validated['add_player_id']);

        // Check if player's game has started
        $currentGame = Game::where(function($query) use ($droppedPlayer) {
            $query->where('home_team_id', $droppedPlayer->team_id)
                ->orWhere('away_team_id', $droppedPlayer->team_id);
        })
        ->where('kickoff', '<=', Carbon::now())
        ->first();

        if ($currentGame) {
            throw ValidationException::withMessages([
                'drop_player_id' => ['Cannot drop a player after their game has started.']
            ]);
        }

        // Check team limit for added player
        $teamPlayerCount = $entry->players()
            ->where('team_id', $addedPlayer->team_id)
            ->where('players.id', '!=', $validated['drop_player_id'])
            ->count();

        if ($teamPlayerCount >= 2) {
            throw ValidationException::withMessages([
                'add_player_id' => ['You cannot have more than 2 players from the same team.']
            ]);
        }

        // Calculate points at drop using ScoringService
        $scoringService = new ScoringService();
        $pointsAtDrop = $scoringService->calculatePlayerPoints($droppedPlayer);

        // Record the change in history
        $entry->playerChangeHistory()->create([
            'player_id' => $validated['drop_player_id'],
            'action' => 'DROP',
            'roster_position' => $validated['position'],
            'points_at_drop' => $pointsAtDrop,
            'processed_at' => now()
        ]);

        // Update the roster
        $entry->players()->detach($validated['drop_player_id']);
        $entry->players()->attach($validated['add_player_id'], [
            'position' => $validated['position']
        ]);

        // Decrement changes remaining
        $entry->decrement('changes_remaining');

        return redirect()->back()->with('success', 'Player changed successfully');
    }

    public function create()
    {
        $players = Player::with('team')
        ->where('is_active', true)
        ->get()
        ->groupBy('position');

        return view('entries.create', compact('players'));
    }

    public function roster(Entry $entry)
    {
        if ($entry->user_id !== auth()->id()) {
            abort(403);
        }

        $entry->load(['players' => function($query) {
            $query->with('team')->select('players.*');
        }]);

        // Get players that are locked (game has started and still games to play this weekend (assumes games only sat & sundays, 48hr window) )

        //Are there upcoming games (<48hrs) then check for locking players. This will unlock all players once the last games end sunday evening.
        $lockedPlayers = collect();
        if($upcomingGames = Game::where('kickoff', '>=', Carbon::now())->where('kickoff', '<=', Carbon::now()->addDays(2)->toDateTimeString())->first()) {
            foreach ($entry->players as $player) {
                $currentGame = Game::where(function ($query) use ($player) {
                    $query->where('home_team_id', $player->team_id)
                        ->orWhere('away_team_id', $player->team_id);
                })
                    ->where('kickoff', '<=', Carbon::now())->where('kickoff', '>=', Carbon::now()->subDays(2)->toDateTimeString())
                    ->first();

                if ($currentGame) {
                    $lockedPlayers->push($player);
                }
            }
        }

        // Calculate total points and points by position using ScoringService
        $scoringService = new ScoringService();
        $totalPoints = $scoringService->calculateTotalPoints($entry->players->flatMap->stats);
        $pointsByPosition = $scoringService->calculatePointsByPosition($entry);

        // Get historical players
        $historicalPlayers = Transaction::where('entry_id', $entry->id)
            ->with(['droppedPlayer', 'droppedPlayer.team'])
            ->get()
            ->map(function ($transaction) {
                return [
                    'name' => $transaction->droppedPlayer->name . ' (' . $transaction->droppedPlayer->team->name . ')',
                    'roster_position' => $transaction->roster_position,
                    'total_points' => 0, // You may want to calculate this
                    'removed_at' => $transaction->created_at
                ];
            });

        $playersActive = $entry->players->count();
        $changesRemaining = $entry->changes_remaining;

        return view('entries.roster', [
            'entry' => $entry->load('players.team'),
            'players' => Player::with('team')->get(),
            'totalPoints' => $totalPoints,
            'pointsByPosition' => $pointsByPosition,
            'lockedPlayers' => $lockedPlayers,
            'historicalPlayers' => $historicalPlayers,
            'playersActive' => $playersActive,
            'changesRemaining' => $changesRemaining
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->entries()->count() >= 4) {
            return back()->withErrors(['message' => 'You cannot create more than 4 entries.']);
        }


        $validator = Validator::make($request->all(), [
            'entry_name' => 'required|string|max:255',
            'players' => [
                'array',
                'required',
                'required_array_keys:QB,RB1,RB2,WR1,WR2,WR3,TE,FLEX',
                function (string $attribute, mixed $value, Closure $fail) {
                    $teamPlayerCount = array();
                    foreach ($value as $player_id) {
                        $player = Player::findOrFail($player_id);

                        if(array_key_exists($player->team_id, $teamPlayerCount))
                        {
                            if (in_array($player_id, $teamPlayerCount[$player->team_id])) {
                                $fail("You cannot choose the same player more than once.");
                            }

                            array_push($teamPlayerCount[$player->team_id], $player_id);
                            if(count($teamPlayerCount[$player->team_id])>2){
                                $fail("You cannot have more than 2 players from the same team.");
                            }
                        }
                        else
                        {
                            $teamPlayerCount[$player->team_id] = array($player_id);
                        }
                    }
                },
            ],
            'players.*'=>'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //TODO: show better failed message for field

        // Check team limit for added player
        $entry = Entry::create([
            'user_id' => auth()->id(),
            'entry_name' => $request->input('entry_name'),
            'changes_remaining' => 2,
            'is_active' => true,
        ]);

        // Attach players with their positions
        foreach ($request->players as $position => $playerId) {
            $entry->players()->attach($playerId, [
                'roster_position' => $position
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Entry created successfully!');
    }

    public function addPlayer(Request $request, Entry $entry)
    {
        if ($entry->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $validated = $request->validate([
                'player_id' => 'required|exists:players,id',
                'roster_position' => 'required|in:QB,RB1,RB2,WR1,WR2,WR3,TE,FLEX'
            ]);

            $player = Player::findOrFail($validated['player_id']);

            $rosterService = new RosterPositionService();
            $eligiblePlayers = $rosterService->getEligiblePlayersForPosition($validated['roster_position'], $entry->players);

            if (!$eligiblePlayers->where('id', $player->id)->exists()) {
                return response()->json([
                    'errors' => [
                        'player_id' => ['Player is not eligible for this roster position.']
                    ]
                ], 422);
            }

            // Check team limit
            $teamPlayerCount = $entry->players()
                ->where('team_id', $player->team_id)
                ->count();

            if ($teamPlayerCount >= 2) {
                return response()->json([
                    'errors' => [
                        'player_id' => ['You cannot have more than 2 players from the same team.']
                    ]
                ], 422);
            }

            // Check position limits and roster composition
            $this->validateRosterComposition($entry, $validated['roster_position']);

            $entry->players()->attach($player->id, [
                'roster_position' => $validated['roster_position']
            ]);

            return response()->json(['message' => 'Player added successfully']);

        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function processTransaction(Request $request, Entry $entry)
{
    if ($entry->user_id !== auth()->id()) {
        abort(403);
    }

    $validated = $request->validate([
        'dropped_player_id' => 'required|exists:players,id',
        'added_player_id' => 'required|exists:players,id',
        'position' => 'required|string'
    ]);

    if ($entry->changes_remaining <= 0) {
        throw ValidationException::withMessages([
            'transaction' => ['No roster changes remaining for this entry.']
        ]);
    }

    try {
        DB::beginTransaction();

        // First, save the history record for the dropped player with correct defaults
        DB::table('entry_player_history')->insert([
            'entry_id' => $entry->id,
            'player_id' => $validated['dropped_player_id'],
            'roster_position' => $validated['position'],
            'wildcard_points' => 0.0,
            'divisional_points' => 0.0,
            'conference_points' => 0.0,
            'superbowl_points' => 0.0,
            'total_points' => 0.0,
            'removed_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Now handle the player swap
        $entry->players()->detach($validated['dropped_player_id']);
        $entry->players()->attach($validated['added_player_id'], [
            'roster_position' => $validated['position']
        ]);

        // Create the transaction record
        Transaction::create([
            'entry_id' => $entry->id,
            'dropped_player_id' => $validated['dropped_player_id'],
            'added_player_id' => $validated['added_player_id'],
            'roster_position' => $validated['position'],
            'processed_at' => now()
        ]);

        // Decrement changes remaining
        $entry->decrement('changes_remaining');

        DB::commit();
        return redirect()->back()->with('success', 'Player changed successfully');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Failed to change player: ' . $e->getMessage());
    }
}

    private function validateRosterComposition(Entry $entry, string $newPosition, ?string $removedPosition = null)
    {
        $positions = $entry->players()
            ->where('position', '!=', $removedPosition)
            ->pluck('position')
            ->push($newPosition);

        $qbCount = $positions->filter(fn($pos) => $pos === 'QB')->count();
        $rbCount = $positions->filter(fn($pos) => $pos === 'RB')->count();
        $wrCount = $positions->filter(fn($pos) => $pos === 'WR')->count();
        $teCount = $positions->filter(fn($pos) => $pos === 'TE')->count();

        if ($qbCount > 1) {
            throw ValidationException::withMessages(['position' => ['Only 1 QB allowed']]);
        }
        if ($rbCount > 2) {
            throw ValidationException::withMessages(['position' => ['Maximum 2 RBs allowed']]);
        }
        if ($wrCount > 3) {
            throw ValidationException::withMessages(['position' => ['Maximum 3 WRs allowed']]);
        }
        if ($teCount > 1) {
            throw ValidationException::withMessages(['position' => ['Only 1 TE allowed']]);
        }
    }
}
