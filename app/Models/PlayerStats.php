<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerStats extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'game_id',
        'points',
        'passing_yards',
        'passing_tds',
        'interceptions',
        'rushing_yards',
        'rushing_tds',
        'receptions',
        'receiving_yards',
        'receiving_tds',
        'fumbles_lost',
        'two_point_conversions',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
