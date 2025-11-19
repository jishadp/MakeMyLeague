<?php

namespace App\Livewire;

use App\Models\League;
use App\Services\LiveAuctionDataService;
use Livewire\Component;

class BroadcastView extends Component
{
    public League $league;

    public string $lastUpdated;

    protected LiveAuctionDataService $dataService;

    public function boot(LiveAuctionDataService $dataService)
    {
        $this->dataService = $dataService;
    }

    public function mount(League $league)
    {
        $this->league = $league;
        $this->lastUpdated = now()->format('H:i:s');
    }

    public function refreshData(): void
    {
        $this->lastUpdated = now()->format('H:i:s');
    }

    public function getPayloadProperty(): array
    {
        return $this->dataService->buildPayload($this->league);
    }

    public function render()
    {
        return view('livewire.broadcast-view', [
            'payload' => $this->payload,
            'lastUpdated' => $this->lastUpdated,
        ]);
    }
}
