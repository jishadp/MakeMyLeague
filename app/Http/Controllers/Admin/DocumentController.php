<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\LeaguePlayer;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    /**
     * Display the admin document center.
     */
    public function index(Request $request): View
    {
        $players = $this->buildPlayerQuery($request)
            ->paginate(12)
            ->withQueryString();

        return view('admin.documents.index', [
            'players' => $players,
            'leagues' => League::orderBy('name')->get(),
            'filters' => [
                'search' => $request->input('search', ''),
                'league_id' => $request->input('league_id'),
            ],
        ]);
    }

    /**
     * Show a printable preview of the selected player's document.
     */
    public function showPlayerCard(Request $request, User $player): View
    {
        $leagueId = $request->filled('league_id') ? (int) $request->input('league_id') : null;
        $viewData = $this->buildPlayerDocumentViewData($player, $leagueId);

        return view('admin.documents.show-player', array_merge($viewData, [
            'generatedAt' => now(),
            'filters' => [
                'search' => $request->input('search', ''),
                'league_id' => $leagueId,
            ],
        ]));
    }

    /**
     * Download the selected player card as a PDF.
     */
    public function downloadPlayerCard(Request $request, User $player): Response
    {
        $leagueId = $request->filled('league_id') ? (int) $request->input('league_id') : null;
        $viewData = $this->buildPlayerDocumentViewData($player, $leagueId);

        $pdf = Pdf::loadView('admin.documents.player-card-pdf', array_merge($viewData, [
            'generatedAt' => now(),
        ]))->setPaper('a4', 'portrait');

        $filename = 'player-card-' . Str::slug($player->name) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Build the player listing query with relevant filters.
     */
    protected function buildPlayerQuery(Request $request): Builder
    {
        return User::players()
            ->with([
                'position',
                'localBody.district.state',
                'leaguePlayers.league',
                'leaguePlayers.leagueTeam.team',
            ])
            ->when($request->filled('league_id'), function (Builder $query) use ($request) {
                $query->whereHas('leaguePlayers', function (Builder $leagueQuery) use ($request) {
                    $leagueQuery->where('league_id', $request->input('league_id'));
                });
            })
            ->when($request->filled('search'), function (Builder $query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name');
    }

    /**
     * Prepare shared data for both the preview and PDF views.
     */
    protected function buildPlayerDocumentViewData(User $player, ?int $leagueId = null): array
    {
        $player->loadMissing([
            'position',
            'localBody.district.state',
            'leaguePlayers.league',
            'leaguePlayers.leagueTeam.team',
        ]);

        $leaguePlayerContext = $this->resolveLeaguePlayerContext($player, $leagueId);
        $photoSources = $this->resolvePhotoSources($player);

        return [
            'player' => $player,
            'leaguePlayerContext' => $leaguePlayerContext,
            'primaryLeague' => $leaguePlayerContext?->league,
            'primaryTeam' => $leaguePlayerContext?->leagueTeam?->team,
            'leagueNames' => $this->extractLeagueNames($player),
            'photoWebUrl' => $photoSources['web'] ?? null,
            'photoDataUri' => $photoSources['data_uri'] ?? null,
        ];
    }

    /**
     * Pick the most relevant league player entry for document context.
     */
    protected function resolveLeaguePlayerContext(User $player, ?int $leagueId = null): ?LeaguePlayer
    {
        $leaguePlayers = $player->leaguePlayers;

        if ($leaguePlayers->isEmpty()) {
            return null;
        }

        if ($leagueId) {
            $match = $leaguePlayers->firstWhere('league_id', (int) $leagueId);
            if ($match) {
                return $match;
            }
        }

        return $leaguePlayers
            ->sortByDesc(function (LeaguePlayer $leaguePlayer) {
                return $leaguePlayer->updated_at ?? $leaguePlayer->created_at;
            })
            ->first();
    }

    /**
     * Extract the unique league names associated with the player.
     */
    protected function extractLeagueNames(User $player): Collection
    {
        return $player->leaguePlayers
            ->map(function (LeaguePlayer $leaguePlayer) {
                return $leaguePlayer->league?->name;
            })
            ->filter()
            ->unique()
            ->values();
    }

    /**
     * Resolve both web and PDF friendly photo sources.
     */
    protected function resolvePhotoSources(User $player): array
    {
        $defaultAsset = asset('images/defaultplayer.jpeg');
        $webUrl = $defaultAsset;

        if (!empty($player->photo)) {
            if (Str::startsWith($player->photo, ['http://', 'https://'])) {
                $webUrl = $player->photo;
            } else {
                $webUrl = asset('storage/' . ltrim($player->photo, '/'));
            }
        }

        $dataUri = null;

        if (!empty($player->photo) && !Str::startsWith($player->photo, ['http://', 'https://'])) {
            if (Storage::disk('public')->exists($player->photo)) {
                $dataUri = $this->convertImageToDataUri(Storage::disk('public')->path($player->photo));
            }
        }

        if (!$dataUri) {
            $dataUri = $this->convertImageToDataUri(public_path('images/defaultplayer.jpeg'));
        }

        return [
            'web' => $webUrl,
            'data_uri' => $dataUri ?: $webUrl,
        ];
    }

    /**
     * Convert the provided image path to a data URI ready for PDF embedding.
     */
    protected function convertImageToDataUri(?string $path): ?string
    {
        if (!$path || !is_file($path) || !is_readable($path)) {
            return null;
        }

        $contents = @file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/jpeg';

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }
}
