<?php

namespace App\Livewire;

use App\Models\League;
use App\Services\LiveAuctionDataService;
use Livewire\Component;

class BroadcastView extends Component
{
    public int $leagueId;

    public string $lastUpdated;

    protected LiveAuctionDataService $dataService;

    public function boot(LiveAuctionDataService $dataService)
    {
        $this->dataService = $dataService;
    }

    public function mount(int $leagueId)
    {
        $this->leagueId = $leagueId;
        $this->lastUpdated = now()->format('H:i:s');
    }

    public function refreshData(): void
    {
        $this->lastUpdated = now()->format('H:i:s');
        $this->forgetComputed('payload');
    }

    public function getPayloadProperty(): array
    {
        $league = League::findOrFail($this->leagueId);
        return $this->dataService->buildPayload($league);
    }

    public function render()
    {
        return view('livewire.broadcast-view', [
            'payload' => $this->payload,
            'lastUpdated' => $this->lastUpdated,
        ]);
    }
}
