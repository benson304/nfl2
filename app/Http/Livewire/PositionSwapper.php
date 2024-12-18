<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Entry;
use App\Models\Player;

class PositionSwapper extends Component
{
    public $entry;
    public $currentPlayerId;
    public $rosterPosition;
    public $swapWithId;

    public function mount(Entry $entry, $currentPlayerId, $rosterPosition)
    {
        $this->entry = $entry;
        $this->currentPlayerId = $currentPlayerId;
        $this->rosterPosition = $rosterPosition;
    }

    public function swapPlayer()
    {
        if (!$this->swapWithId) {
            $this->dispatch('showDialog', ['type' => 'error', 'message' => 'Please select a player to swap with']);
            return;
        }


        try {
            $response = $this->entry->swapWithFlex($this->currentPlayerId, $this->swapWithId);
            $this->dispatch('showDialog', ['type' => 'success', 'message' => 'Positions successfully swapped']);
            $this->dispatch('playerUpdated');
        } catch (\Exception $e) {
            $this->dispatch('showDialog', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function render()
    {

        $eligiblePlayers = $this->entry->current_players()
            ->whereNull('entry_player.removed_at')
            ->where(function ($query) {
                $query->where('roster_position', 'FLEX')
                    ->orWhere('position', Player::whereId($this->currentPlayerId)->pluck('position')->firstOrFail());
            })
            ->where('players.id', '!=', $this->currentPlayerId)
            ->get();

        return view('livewire.position-swapper', [
            'eligiblePlayers' => $eligiblePlayers
        ]);
    }
}
