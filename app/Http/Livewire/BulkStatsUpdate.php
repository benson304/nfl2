<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Game;
use App\Models\PlayerStats;
use League\Csv\Reader;


class BulkStatsUpdate extends Component
{
    use WithFileUploads;

    public $games;
    public $statsFile;
    public $importType = 'csv';
    public $previewData = [];
    public $mapping = [];
    public $processing = false;

    protected $rules = [
        'statsFile' => 'required|file|mimes:csv,txt|max:1024',
    ];

    public function mount()
    {
        $this->games = Game::whereIn('status', ['scheduled', 'in_progress'])
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('kickoff')
            ->get();
    }

    public function updatedStatsFile()
    {
        $this->validate();

        $csv = Reader::createFromPath($this->statsFile->path());
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(',');
        // Add these lines to handle the quotes
        $csv->setEnclosure('"');
        $csv->setEscape('\\');

        // Preview first 5 rows
        $this->previewData = iterator_to_array($csv->getRecords(), true);
        $this->previewData = array_slice($this->previewData, 0, 5);
    }
    public function import()
    {
        $this->validate();
        $this->processing = true;

        try {
            $csv = Reader::createFromPath($this->statsFile->path());
            $csv->setHeaderOffset(0);
            $csv->setDelimiter(',');

            // Clean up headers by trimming whitespace
            $headers = array_map('trim', $csv->getHeader());

            \DB::beginTransaction();

            foreach ($csv->getRecords() as $record) {
                // Clean up record keys and ensure all required fields exist
                $record = array_combine(
                    array_map('trim', array_keys($record)),
                    array_map('trim', $record)
                );

                // Verify required fields exist
                if (!isset($record['game_id']) || !isset($record['player_id'])) {
                    throw new \Exception('Required fields missing: game_id and player_id are required');
                }

                PlayerStats::updateOrCreate(
                    [
                        'game_id' => (int)$record['game_id'],
                        'player_id' => (int)$record['player_id'],
                    ],
                    [
                        'points' => ($record['points'] ?? 0),
                        'passing_yards' => ($record['passing_yards'] ?? 0),
                        'passing_tds' => ($record['passing_tds'] ?? 0),
                        'interceptions' => ($record['interceptions'] ?? 0),
                        'rushing_yards' => ($record['rushing_yards'] ?? 0),
                        'rushing_tds' => ($record['rushing_tds'] ?? 0),
                        'receptions' => ($record['receptions'] ?? 0),
                        'receiving_yards' => ($record['receiving_yards'] ?? 0),
                        'receiving_tds' => ($record['receiving_tds'] ?? 0),
                        'two_point_conversions' => ($record['two_point_conversions'] ?? 0),
                        'fumbles_lost' => ($record['fumbles_lost'] ?? 0),
                        'offensive_fumble_return_td' => ($record['offensive_fumble_return_td'] ?? 0)
                    ]
                );
            }

            \DB::commit();
            session()->flash('success', 'Stats imported successfully');
            $this->dispatch('stats-updated');
            $this->dispatch('close-modal', 'bulk-stats-update');

        } catch (\Exception $e) {
            \DB::rollBack();
            session()->flash('error', 'Error importing stats: ' . $e->getMessage());
        }

        $this->processing = false;
    }
    public function render()
    {
        return view('livewire.admin.bulk-stats-update');
    }
}
