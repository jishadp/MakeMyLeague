<?php

namespace App\Livewire;

use App\Models\League;
use App\Services\LiveAuctionDataService;
use Livewire\Component;

class BroadcastView extends Component
{
    public League $league;

    public array $payload = [];

    public string $lastUpdated;

    protected LiveAuctionDataService $dataService;

    public function boot(LiveAuctionDataService $dataService)
    {
        $this->dataService = $dataService;
    }

    public function mount(League $league)
    {
        $this->league = $league;
        $this->refreshData();
    }

    public function refreshData(): void
    {
        $this->payload = $this->dataService->buildPayload($this->league);
        $this->lastUpdated = now()->format('H:i:s');
    }

    public function render()
    {
        return view('livewire.broadcast-view');
    }
}
