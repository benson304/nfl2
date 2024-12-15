<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Team;
use App\Models\Player;
use App\Models\PlayerStat;
use App\Models\Entry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Stat;

class GameController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $games = Game::with(['homeTeam', 'awayTeam'])
            ->orderBy('round')
            ->orderBy('kickoff')
            ->paginate(20);

        return view('admin.games.index', compact('games'));
    }
    public function stats(Game $game)
    {
        $playerStats = PlayerStats::where('game_id', $game->id)
            ->with(['player'])
            ->get();

        return view('admin.games.stats', [
            'game' => $game,
            'playerStats' => $playerStats
        ]);
    }
    public function create()
    {
        $teams = Team::all();
        return view('admin.games.create', compact('teams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'home_team_id' => 'required|exists:teams,id',
            'away_team_id' => 'required|exists:teams,id',
            'round' => 'required|string',
            'kickoff' => 'required|date',
            'status' => 'required|in:scheduled,in_progress,finished',
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0'
        ]);

        $game = Game::create($validated);

        return redirect()->route('admin.games.show', $game)
            ->with('success', 'Game created successfully');
    }
    public function show(Game $game)
    {
        $teams = Team::all();
        return view('admin.games.show', compact('game', 'teams'));
    }
    public function edit(Game $game)
    {
        $teams = Team::all();
        return view('admin.games.edit', compact('game', 'teams'));
    }

    public function update(Request $request, Game $game)
    {

        $validated = $request->validate([
            'home_team_id' => 'sometimes|exists:teams,id',
            'away_team_id' => 'sometimes|exists:teams,id',
            'round' => 'sometimes|string',
            'kickoff' => 'sometimes|date',
            'status' => 'sometimes|in:scheduled,in_progress,finished',
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0'
        ]);

        DB::transaction(function () use ($game, $validated) {
            $game->update($validated);

            if (isset($validated['status']) && $validated['status'] === 'finished') {
                $this->handleGameCompletion($game);
            }
        });

        return redirect()->route('admin.games.edit', $game)
            ->with('success', 'Game updated successfully');
    }

    public function destroy(Game $game)
    {
        // Soft delete or hard delete based on your requirements
        $game->delete();

        return redirect()->route('admin.games.index')
            ->with('success', 'Game deleted successfully');
    }
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'stats_file' => 'required|file|mimes:csv,txt'
        ]);

        try {
            $path = $request->file('stats_file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            $headers = array_shift($data);

            foreach ($data as $row) {
                PlayerStats::updateOrCreate(
                    [
                        'player_id' => $row[0],
                        'game_id' => $row[1]
                    ],
                    [
                        'stats' => json_encode(array_combine($headers, $row))
                    ]
                );
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }



    public function bulkUpdateStats(Request $request)
{
    $validated = $request->validate([
        'stats' => 'required|array',
        'stats.*' => 'array',
        'stats.*.name' => 'required|string',
        'stats.*.value' => 'required|numeric'
    ]);

    DB::transaction(function () use ($validated) {
        foreach ($validated['stats'] as $gameId => $stats) {
            $game = Game::findOrFail($gameId);

            // Parse the stat name and find the corresponding player
            $statParts = explode('_', $stats['name']);
            $playerId = $statParts[0];
            $statType = implode('_', array_slice($statParts, 1));

            // Update or create the player stats
            $playerStats = PlayerStats::firstOrCreate([
                'game_id' => $game->id,
                'player_id' => $playerId
            ]);

            // Update the specific stat
            $playerStats->update([
                $statType => $stats['value']
            ]);
        }
    });

    return response()->json([
        'success' => true,
        'message' => 'Stats updated successfully'
    ]);
}

    public function showStats(Game $game)
    {
        $playerStats = $game->playerStats()->with('player')->get();
        return view('admin.games.stats', compact('game', 'playerStats'));
    }

    private function handleGameCompletion(Game $game)
    {
        // Update team playoff statuses if needed
        if ($game->round !== 'Super Bowl') {
            $losingTeam = $game->home_score > $game->away_score
                ? $game->awayTeam
                : $game->homeTeam;

            $losingTeam->update(['is_playoff_team' => false]);
        }

        // Lock player stats
        $game->playerStats()->update(['locked' => true]);

        // Recalculate points for all affected entries
        $this->recalculateEntryPoints($game);
    }

    private function recalculateEntryPoints(Game $game)
    {
        // Trigger point calculations for all affected entries
        $affectedPlayers = $game->playerStats->pluck('player_id')->unique();
        $affectedEntries = DB::table('entry_player')
            ->whereIn('player_id', $affectedPlayers)
            ->pluck('entry_id')
            ->unique();

        // Update entry points
        foreach ($affectedEntries as $entryId) {
            $entry = Entry::find($entryId);
            $entry->updateTotalPoints();
        }
    }
}
