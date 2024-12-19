<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'team_id',
        'position',
        'is_active',
        'status',
        'external_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function games()
    {
        $gamesHome = $this->home_games;
        $gamesAway = $this->away_games;
        // Merge collections and return single collection.
        return $gamesHome->merge($gamesAway);
    }



    public function home_games()
    {
        return $this->hasManyThrough(
            Game::class,
            Team::class,
            'id', // Foreign key on the environments table...
            'home_team_id', // Foreign key on the deployments table...
            'team_id', // Local key on the projects table...
            'id' // Local key on the environments table...
        );
    }

    public function away_games()
    {
        return $this->hasManyThrough(
            Game::class,
            Team::class,
            'id', // Foreign key on the environments table...
            'away_team_id', // Foreign key on the deployments table...
            'team_id', // Local key on the projects table...
            'id' // Local key on the environments table...
        );
    }

    public function stats(): HasMany
    {
        return $this->hasMany(PlayerStats::class);
    }

    public function getPoints($round)
    {
        if(Game::where('round', $round)->where('kickoff','<',now())->first())
        {
            $result=DB::table('games')->select('games.id','player_stats.points')
                ->leftJoin('player_stats', 'games.id', '=', 'player_stats.game_id')
                ->where('games.round',$round)->where('player_stats.player_id',$this->id)->first();
            return($result->points??0);
        }
        return null;

    }
        public function calculateWeeklyScore($gameId): float
    {
        $stats = $this->stats()->where('game_id', $gameId)->first();
        if (!$stats) return 0;

        $score = 0;

        // Passing points
        $score += floor($stats->passing_yards / 25);
        if ($stats->passing_yards >= 300) $score += 4;
        if ($stats->passing_yards >= 400) $score += 4;
        $score += $stats->passing_tds * 6;
        $score -= $stats->interceptions * 2;

        // Rushing points
        $score += floor($stats->rushing_yards / 10);
        if ($stats->rushing_yards >= 100) $score += 4;
        if ($stats->rushing_yards >= 200) $score += 4;
        $score += $stats->rushing_tds * 6;

        // Receiving points
        $score += $stats->receptions * 0.5;
        $score += floor($stats->receiving_yards / 10);
        if ($stats->receiving_yards >= 100) $score += 4;
        if ($stats->receiving_yards >= 200) $score += 4;
        $score += $stats->receiving_tds * 6;

        // Miscellaneous
        $score += $stats->two_point_conversions * 2;
        $score -= $stats->fumbles_lost * 2;

        return $score;
    }
}
