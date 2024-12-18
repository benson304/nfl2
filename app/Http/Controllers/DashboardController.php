<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\ScoringService;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $entries = $user->entries()
            ->with([
                'players.team',
                'players.stats.game',
                'transactions.droppedPlayer',
                'transactions.addedPlayer'
            ])
            ->get();

        // Calculate total points for each entry and get roster positions
        $entries->each(function ($entry) {

            // Get point breakdown by round
//            $entry->weekly_points = $entry->players->flatMap->stats
//                ->groupBy(function ($stat) {
//
//                    return $stat->game->kickoff->format('W');
//                })
//                ->map(function ($stats) use ($entry) {
//                    return $entry->calculateTotalPoints($stats);
//                    //OLD CODE BELOW. NOT SURE WHAT IT WAS CALLING
//                    //return $stats->sum->calculatePoints();
//                });

            // Get the roster with positions for each entry
            $entry->rosters = $entry->players->map(function ($player) use ($entry) {
                return (object)[
                    'player' => $player,
                    'roster_position' => $player->pivot->position
                ];
            });
        });

        $entriesCount = $entries->count();
        $remainingEntries = 4 - $entriesCount; // Max 4 entries per user

        return view('dashboard', compact(
            'entries',
            'entriesCount',
            'remainingEntries'
        ));
    }
}
