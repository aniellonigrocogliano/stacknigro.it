<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Models\InboxConversation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CronController extends Controller
{
    public function inboxCheck(Request $request)
    {
        // 🔐 Sicurezza: token obbligatorio
        $token = $request->query('token');
        abort_unless(
            $token && hash_equals(env('CRON_TOKEN'), $token),
            403
        );

        // 🔁 ultimo messaggio già notificato (anti-spam)
        $lastId = (int) Cache::get('inbox_last_notified_id', 0);

        // 📬 nuovi messaggi NON LETTI
        $newUnread = InboxConversation::whereNull('read_at')
            ->whereNull('archived_at')   // ignora archiviati
            ->whereNull('deleted_at')    // ignora cancellati
            ->where('id', '>', $lastId)
            ->orderBy('id')
            ->get();

        if ($newUnread->isEmpty()) {
            return response('OK - no new messages', 200);
        }

        $latestId = (int) $newUnread->last()->id;

        $totalUnread = InboxConversation::whereNull('read_at')
            ->whereNull('archived_at')
            ->whereNull('deleted_at')
            ->count();

        $to = env('INBOX_ALERT_TO') ?: config('mail.from.address');

        // ✉️ invio notifica
        Mail::raw(
            "Hai nuovi messaggi non letti: {$newUnread->count()}\n".
            "Totale non letti: {$totalUnread}\n\n".
            "Ultimo messaggio:\n".
            "Oggetto: ".$newUnread->last()->subject."\n".
            "Email: ".$newUnread->last()->email."\n\n".
            "Apri inbox admin: ".url('/admin/contacts'),
            function ($m) use ($to) {
                $m->to($to)->subject('📬 Nuovi messaggi in Inbox');
            }
        );

        // 🔒 memorizza ultimo ID notificato
        Cache::put(
            'inbox_last_notified_id',
            $latestId,
            now()->addDays(30)
        );

        return response('OK - notified', 200);
    }
    public function captchaRollup(Request $request)
{
    // 🔐 Sicurezza: token obbligatorio
    $token = $request->query('token');
    abort_unless(
        $token && hash_equals(env('CRON_TOKEN'), $token),
        403
    );

    $now = now();

    // raw retention (giorni)
    $retentionDays = (int) (env('CAPTCHA_LOG_RETENTION_DAYS') ?: 7);
    $cutoff = $now->copy()->subDays($retentionDays)->startOfDay();

    // ✅ Ricalcolo solo ultimi 2 giorni: oggi + ieri
    $days = [
        $now->toDateString(),
        $now->copy()->subDay()->toDateString(),
    ];

    $siteIds = DB::table('captcha_sites')
        ->where('is_active', 1)
        ->pluck('id');

    $processedSites = 0;

    foreach ($siteIds as $siteId) {

        foreach ($days as $day) {
            $start = Carbon::parse($day)->startOfDay();
            $end = (clone $start)->addDay(); // [start, end)

            // Requests
            $requestsTotal = DB::table('captcha_requests')
                ->where('site_id', $siteId)
                ->where('created_at', '>=', $start)
                ->where('created_at', '<', $end)
                ->count();

            $uniqueIps = DB::table('captcha_requests')
                ->where('site_id', $siteId)
                ->where('created_at', '>=', $start)
                ->where('created_at', '<', $end)
                ->distinct('ip')
                ->count('ip');

            // Sessions (stato finale nella stessa riga)
            $baseSessions = DB::table('captcha_sessions')
                ->where('site_id', $siteId)
                ->where('created_at', '>=', $start)
                ->where('created_at', '<', $end);

            $challengeCount = (clone $baseSessions)
                ->where('session_status', 'challenge')
                ->count();

            $verifiedCount = (clone $baseSessions)
                ->where('session_status', 'verified')
                ->count();

            $silentCount = (clone $baseSessions)
                ->where('session_status', 'silent')
                ->count();

            $pendingCount = (clone $baseSessions)
                ->where('session_status', 'pending')
                ->count();

            $avgRisk = (clone $baseSessions)->avg('risk_score');
            $maxRisk = (clone $baseSessions)->max('risk_score');

            // UPSERT su UNIQUE(site_id, DAY)
            DB::table('captcha_stats_daily')->updateOrInsert(
                [
                    'site_id' => $siteId,
                    'DAY' => $day,
                ],
                [
                    'requests_total' => $requestsTotal,
                    'requests_unique_ips' => $uniqueIps,
                    'challenge_count' => $challengeCount,
                    'verified_count' => $verifiedCount,
                    'silent_count' => $silentCount,
                    'pending_count' => $pendingCount,
                    'avg_risk_score' => $avgRisk,
                    'max_risk_score' => $maxRisk,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        // Stato: non serve ai calcoli, ma è utile come traccia
        DB::table('captcha_stats_state')->updateOrInsert(
            ['site_id' => $siteId],
            [
                'last_request_id' => 0,
                'last_session_id' => 0,
                'last_run_at' => $now,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $processedSites++;
    }

    // Retention raw: tieni solo ultimi X giorni
    DB::table('captcha_requests')
        ->where('created_at', '<', $cutoff)
        ->delete();

    DB::table('captcha_sessions')
        ->where('created_at', '<', $cutoff)
        ->delete();

    return response()->json([
        'status' => 'ok',
        'ran_at' => $now->toDateTimeString(),
        'sites_processed' => $processedSites,
        'days_recalculated' => $days,
        'retention_days' => $retentionDays,
    ]);
}

}
