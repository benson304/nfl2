<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'entry_id',
        'dropped_player_id',
        'added_player_id',
        'roster_position',
        'processed_at',
        'transaction_type',
        'notes'
    ];

    protected $dates = ['deleted_at','processed_at'];


    // Update the $with array to use consistent naming
    protected $with = ['entry', 'droppedPlayer', 'addedPlayer'];

    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }

    public function droppedPlayer()
    {
        return $this->belongsTo(Player::class, 'dropped_player_id');
    }

    public function addedPlayer()
    {
        return $this->belongsTo(Player::class, 'added_player_id');
    }

    // Update the accessors to match relationship names
    public function getDroppedPlayerNameAttribute()
    {
        return $this->droppedPlayer ? $this->droppedPlayer->name : 'Unknown Player';
    }

    public function getAddedPlayerNameAttribute()
    {
        return $this->addedPlayer ? $this->addedPlayer->name : 'Unknown Player';
    }


    public function scopeForEntry($query, $entryId)
    {
        return $query->where('entry_id', $entryId);
    }

    public function getTransactionDescription()
    {
        return "Dropped {$this->droppedPlayer->name} ({$this->droppedPlayer->team->abbreviation}) for {$this->addedPlayer->name} ({$this->addedPlayer->team->abbreviation})";
    }
}
