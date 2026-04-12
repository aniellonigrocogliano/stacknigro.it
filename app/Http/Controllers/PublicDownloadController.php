<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\DownloadAsset;
use App\Models\DownloadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicDownloadController extends Controller
{
    /**
     * GET /download  (name: public.download.index)
     */
    public function index(Request $request)
    {
        $downloads = Download::query()
            ->where('is_active', 1)
            ->with([
                'suite:id,name,slug',
                'languages:id,key,label,fa_icon,is_active',
                'versions' => function ($q) {
                    $q->where('is_active', 1)
                      ->orderByDesc('is_latest')
                      ->orderByDesc('released_at')
                      ->orderByDesc('id');
                },
                'versions.assets' => function ($q) {
                    $q->where('is_active', 1)
                      ->orderBy('sort_order')
                      ->orderBy('id');
                },
            ])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $downloads->transform(function ($d) {
            $d->latestVersion = $d->versions->first();
            $d->formats = collect();

            if ($d->latestVersion) {
                $d->formats = $d->latestVersion->assets->pluck('format')->unique()->values();
            }

            $d->downloads_total = (int) $d->versions->flatMap(fn ($v) => $v->assets)->sum('downloads_count');
            return $d;
        });

        return view('public.downloads.index', compact('downloads'));
    }

    /**
     * GET /download/{slug} (name: public.download.show)
     */
    public function show(string $slug)
    {
        $download = Download::query()
            ->where('is_active', 1)
            ->where(function ($q) use ($slug) {
                $q->where('slug', $slug);
                if (ctype_digit($slug)) {
                    $q->orWhere('id', (int) $slug);
                }
            })
            ->with([
                'suite:id,name,slug',
                'languages:id,key,label,fa_icon,is_active',
                'versions' => function ($q) {
                    $q->where('is_active', 1)
                      ->orderByDesc('is_latest')
                      ->orderByDesc('released_at')
                      ->orderByDesc('id');
                },
                'versions.assets' => function ($q) {
                    $q->where('is_active', 1)
                      ->orderBy('sort_order')
                      ->orderBy('id');
                },
            ])
            ->firstOrFail();

        $latestVersion = $download->versions->first();

        return view('public.downloads.show', compact('download', 'latestVersion'));
    }

    /**
     * GET /download/file/{assetId} (name: public.download.file)
     */
    public function file(Request $request, int $assetId)
    {
        $asset = DownloadAsset::query()
            ->where('id', $assetId)
            ->where('is_active', 1)
            ->with(['version.download'])
            ->firstOrFail();

        if (!$asset->version || !$asset->version->is_active) abort(404);
        if (!$asset->version->download || !$asset->version->download->is_active) abort(404);

        // best effort counters/log
        try {
            $asset->increment('downloads_count');

            if (class_exists(DownloadLog::class)) {
                DownloadLog::create([
                    'asset_id'   => $asset->id,
                    'ip_address' => (string) $request->ip(),
                    'user_agent' => (string) $request->userAgent(),
                    'referer'    => (string) $request->headers->get('referer'),
                ]);
            }
        } catch (\Throwable $e) {
            // non bloccare il download
        }

        $path = (string) $asset->stored_path;

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $abs = Storage::disk('local')->path($path);
        $name = $asset->original_name ?: basename($path);

        return response()->download($abs, $name);
    }
}
