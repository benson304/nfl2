<?php

namespace App\Providers;

use App\Http\Livewire\PositionSwapper;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\View\Components\AdminLayout;
use Livewire\Livewire;
use App\Http\Livewire\PlayerSelector;
use App\Http\Livewire\BulkStatsUpdate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('player-selector', PlayerSelector::class);
        Livewire::component('position-swapper', PositionSwapper::class);
        Livewire::component('admin.modals.bulk-stats-update', BulkStatsUpdate::class);
        Blade::component('admin-layout', AdminLayout::class);
    }
}
