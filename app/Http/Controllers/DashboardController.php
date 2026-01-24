<?php

namespace App\Http\Controllers;

use App\Models\InboxConversation;
use App\Models\Project;
use App\Models\Skill;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // INBOX KPI
        $inboxUnread = InboxConversation::query()
            ->whereNull('deleted_at')
            ->whereNull('archived_at')
            ->whereNull('read_at')
            ->count();

        $inboxToReply = InboxConversation::query()
            ->whereNull('deleted_at')
            ->whereNull('replied_at')
            ->count();

        $inboxToday = InboxConversation::query()
            ->whereNull('deleted_at')
            ->whereDate('created_at', now()->toDateString())
            ->count();

        // PREVENTIVI KPI (source=quote)
        $quotes30 = InboxConversation::query()
            ->whereNull('deleted_at')
            ->where('source', 'quote')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $quoteAvgTotal = InboxConversation::query()
            ->whereNull('deleted_at')
            ->where('source', 'quote')
            ->whereNotNull('quote_payload')
            ->selectRaw("AVG(CAST(JSON_UNQUOTE(JSON_EXTRACT(quote_payload, '$.total')) AS DECIMAL(10,2))) as v")
            ->value('v');

        $quoteMaxTotal = InboxConversation::query()
            ->whereNull('deleted_at')
            ->where('source', 'quote')
            ->whereNotNull('quote_payload')
            ->selectRaw("MAX(CAST(JSON_UNQUOTE(JSON_EXTRACT(quote_payload, '$.total')) AS DECIMAL(10,2))) as v")
            ->value('v');

        // PROGETTI
        $projectsTotal = Project::count();
        $projectsPublished = Project::where('is_published', 1)->count();
        $projectsDraft = Project::where('is_published', 0)->count();

        // FOTO PROGETTI (tabella project_images dal DB)
        $projectImagesTotal = (int) DB::table('project_images')->count();

        // Skills
        $skillsTotal = Skill::count();

        // Visite (placeholder, poi GA)
        $visitsTotal = 0;

        // Site settings completeness
        $site = SiteSetting::query()->first();
        $checks = [
            'Hero title' => !empty($site?->hero_title),
            'Hero subtitle' => !empty($site?->hero_subtitle),
            'Logo' => !empty($site?->logo_path),
            'Favicon' => !empty($site?->favicon_path),
            'Bio' => !empty($site?->bio),
        ];

        $checksDone = collect($checks)->filter()->count();
        $checksTotal = count($checks);
        $siteCompletion = $checksTotal ? (int) round(($checksDone / $checksTotal) * 100) : 0;

        // Ultimi messaggi
        $lastMessages = InboxConversation::query()
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        // Ultimi preventivi
        $lastQuotes = InboxConversation::query()
            ->whereNull('deleted_at')
            ->where('source', 'quote')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        // CHART: messaggi ultimi 7 giorni (bar)
        $days = collect(range(6, 0))->map(fn ($i) => now()->subDays($i)->toDateString());
        $countsByDay = InboxConversation::query()
            ->whereNull('deleted_at')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw("DATE(created_at) as d, COUNT(*) as c")
            ->groupBy('d')
            ->pluck('c', 'd');

        $chartInboxLabels = $days->map(fn ($d) => date('D', strtotime($d)))->values();
        $chartInboxData = $days->map(fn ($d) => (int) ($countsByDay[$d] ?? 0))->values();

        // CHART: preventivi ultimi 6 mesi (line, count)
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'));
        $countsByMonth = InboxConversation::query()
            ->whereNull('deleted_at')
            ->where('source', 'quote')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as m, COUNT(*) as c")
            ->groupBy('m')
            ->pluck('c', 'm');

        $chartQuoteLabels = $months->map(fn ($m) => date('M', strtotime($m . '-01')))->values();
        $chartQuoteData = $months->map(fn ($m) => (int) ($countsByMonth[$m] ?? 0))->values();

        return view('admin.dashboard', compact(
            'inboxUnread',
            'inboxToReply',
            'inboxToday',
            'quotes30',
            'quoteAvgTotal',
            'quoteMaxTotal',
            'projectsTotal',
            'projectsPublished',
            'projectsDraft',
            'projectImagesTotal',
            'skillsTotal',
            'visitsTotal',
            'siteCompletion',
            'checks',
            'lastMessages',
            'lastQuotes',
            'chartInboxLabels',
            'chartInboxData',
            'chartQuoteLabels',
            'chartQuoteData',
        ));
    }
}
