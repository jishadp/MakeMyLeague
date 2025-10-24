<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuctionAccessService;

class CheckLiveAuctionAccess
{
    protected $auctionAccessService;

    public function __construct(AuctionAccessService $auctionAccessService)
    {
        $this->auctionAccessService = $auctionAccessService;
    }

    public function handle(Request $request, Closure $next, $permission = 'view')
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $league = $request->route('league');
        
        if (!$league) {
            abort(404, 'League not found');
        }

        $accessCheck = $this->auctionAccessService->canUserAccessAuction($user->id, $league->id);

        if (!$accessCheck['allowed']) {
            abort(403, $accessCheck['message']);
        }

        // Store role and team info in request for controllers
        $request->merge([
            'auction_role' => $accessCheck['role'],
            'auction_team_id' => $accessCheck['team_id']
        ]);

        // Check specific permission requirements
        if ($permission === 'organizer' && $accessCheck['role'] !== 'organizer' && $accessCheck['role'] !== 'both') {
            abort(403, json_encode([
                'message' => 'Organizer Access Required',
                'details' => 'Only approved league organizers can perform this action.',
                'required_role' => 'organizer',
                'your_role' => $accessCheck['role']
            ]));
        }

        if ($permission === 'auctioneer' && !in_array($accessCheck['role'], ['auctioneer', 'both'])) {
            abort(403, json_encode([
                'message' => 'Auctioneer Access Required',
                'details' => 'You must be assigned as an auctioneer or be a team owner to place bids.',
                'required_role' => 'auctioneer',
                'your_role' => $accessCheck['role']
            ]));
        }

        return $next($request);
    }
}
