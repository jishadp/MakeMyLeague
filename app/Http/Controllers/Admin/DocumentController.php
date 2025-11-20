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
use Illuminate\Validation\Rule;
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
            'retentionFilters' => $this->retentionFilterOptions(),
            'rosterFilters' => [
                'retention_filter' => $request->input('retention_filter', 'all'),
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
        ]))
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setPaper('a4', 'portrait');

        $filename = 'player-card-' . Str::slug($player->name) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Preview the consolidated league roster prior to PDF export.
     */
    public function previewLeagueRoster(Request $request): View
    {
        $layout = $request->input('layout', 'grid');
        $layout = in_array($layout, ['grid', 'wide'], true) ? $layout : 'grid';
        $payload = $this->prepareLeagueRosterData($request);
        $query = array_filter([
            'search' => $payload['filters']['search'] ?? null,
            'league_id' => $payload['filters']['league_id'] ?? null,
            'retention_filter' => $payload['filters']['retention_filter'] ?? null,
        ], fn ($value) => filled($value));

        return view('admin.documents.league-roster-preview', array_merge($payload, [
            'backUrl' => route('admin.documents.index', $query),
            'downloadUrl' => route('admin.documents.leagues.download', $query),
            'hideChrome' => true,
            'layout' => $layout,
        ]));
    }

    /**
     * Download a consolidated PDF of league players.
     */
    public function downloadLeagueRoster(Request $request): Response
    {
        $payload = $this->prepareLeagueRosterData($request);

        $pdf = Pdf::loadView('admin.documents.league-roster-pdf', $payload)
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setPaper('a4', 'landscape');

        $league = $payload['league'] ?? null;
        $seasonSegment = $league && $league->season ? '-s' . $league->season : '';
        $filenameLeagueSegment = $league
            ? Str::slug(($league->name ?? 'league') . $seasonSegment)
            : 'all-leagues';
        $filenameLeagueSegment = $filenameLeagueSegment ?: 'league';

        $filename = 'league-roster-' . $filenameLeagueSegment . '-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }
    
    /**
     * Prepare data set shared between the roster preview and PDF.
     */
    protected function prepareLeagueRosterData(Request $request): array
    {
        $validated = $request->validate([
            'league_id' => ['nullable', 'integer', 'exists:leagues,id'],
            'search' => ['nullable', 'string', 'max:255'],
            'retention_filter' => ['nullable', Rule::in(array_keys($this->retentionFilterOptions()))],
        ]);

        $leagueId = $validated['league_id'] ?? null;
        $search = isset($validated['search']) ? trim($validated['search']) : null;
        $retentionFilter = $validated['retention_filter'] ?? 'all';

        $playerQuery = LeaguePlayer::query()
            ->with([
                'player.position',
                'player.localBody.district.state',
                'league',
                'leagueTeam.team',
            ])
            ->when($leagueId, fn (Builder $query) => $query->where('league_id', $leagueId))
            ->when($search, function (Builder $query) use ($search) {
                $query->whereHas('player', function (Builder $playerQuery) use ($search) {
                    $playerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });

        switch ($retentionFilter) {
            case 'only_retention':
                $playerQuery->where('retention', true);
                break;
            case 'exclude_retention':
                $playerQuery->where(function (Builder $query) {
                    $query->where('retention', false)
                        ->orWhereNull('retention');
                });
                break;
        }

        $leaguePlayers = $playerQuery
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $rosterEntries = $this->buildLeagueRosterEntries($leaguePlayers);
        $league = $leagueId ? League::find($leagueId) : null;

        return [
            'players' => $rosterEntries,
            'league' => $league,
            'generatedAt' => now(),
            'retentionFilterLabel' => $this->retentionFilterOptions()[$retentionFilter] ?? 'All league players',
            'retentionFilterKey' => $retentionFilter,
            'searchTerm' => $search,
            'playerCount' => $rosterEntries->count(),
            'leagueBadge' => $this->formatLeagueBadge($league),
            'filters' => [
                'league_id' => $leagueId,
                'search' => $search,
                'retention_filter' => $retentionFilter,
            ],
        ];
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
     * Prepare roster entries for the consolidated PDF.
     */
    protected function buildLeagueRosterEntries(Collection $leaguePlayers): Collection
    {
        return $leaguePlayers
            ->filter(fn (LeaguePlayer $leaguePlayer) => $leaguePlayer->player instanceof User)
            ->values()
            ->map(function (LeaguePlayer $leaguePlayer, int $index) {
                $player = $leaguePlayer->player;
                $photoSources = $this->resolvePhotoSources($player);

                return [
                    'serial' => $index + 1,
                    'name' => $player->name,
                    'role' => optional($player->position)->name ?? 'Not assigned',
                    'place' => $this->formatPlayerLocation($player),
                    'phone' => $this->formatPlayerPhone($player),
                    'photo' => $photoSources['data_uri'] ?? $photoSources['web'],
                    'team' => optional(optional($leaguePlayer->leagueTeam)->team)->name,
                    'league_name' => optional($leaguePlayer->league)->name,
                    'league_short' => $this->formatLeagueBadge($leaguePlayer->league) ?? optional($leaguePlayer->league)->name,
                    'season' => optional($leaguePlayer->league)->season,
                    'retained' => (bool) $leaguePlayer->retention,
                    'joined_at' => $leaguePlayer->created_at,
                ];
            });
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
     * Format the player's base location in a single line.
     */
    protected function formatPlayerLocation(User $player): string
    {
        $localBody = optional($player->localBody)->name;
        $district = optional(optional($player->localBody)->district)->name;
        $state = optional(optional(optional($player->localBody)->district)->state)->name;

        $parts = collect([$localBody, $district, $state])->filter();

        return $parts->isNotEmpty() ? $parts->implode(', ') : 'No location submitted';
    }

    /**
     * Format the player's phone number for display.
     */
    protected function formatPlayerPhone(User $player): string
    {
        $number = trim((string) ($player->formatted_phone_number ?? ''));

        if ($number === '') {
            $parts = collect([$player->country_code, $player->mobile])
                ->filter(fn ($value) => filled($value));
            $number = trim($parts->implode(' '));
        }

        return $number !== '' ? $number : 'Not provided';
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
     * Provide readable options for retention filter states.
     */
    protected function retentionFilterOptions(): array
    {
        return [
            'all' => 'All league players',
            'only_retention' => 'Retention players only',
            'exclude_retention' => 'Exclude retention players',
        ];
    }

    /**
     * Format league name into compact badge text.
     */
    protected function formatLeagueBadge(?League $league): ?string
    {
        if (!$league) {
            return null;
        }

        $name = $league->name ?? '';
        $season = $league->season ?? null;

        if (stripos($name, 'Premier League') !== false) {
            $initials = collect(explode(' ', $name))
                ->filter()
                ->map(fn ($word) => Str::upper(Str::substr($word, 0, 1)))
                ->implode('');

            $initials = $initials ?: Str::upper(Str::substr($name, 0, 3));

            if ($season) {
                return trim($initials . ' ' . $season);
            }

            return $initials;
        }

        return $name;
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
