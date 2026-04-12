<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\DownloadVersion;
use Illuminate\Http\Request;

class AdminDownloadVersionController extends Controller
{
    public function index($downloadId)
    {
        $download = Download::findOrFail($downloadId);

        $versions = DownloadVersion::query()
            ->where('download_id', $download->id)
            ->orderByDesc('released_at')
            ->orderByDesc('id')
            ->paginate(25);

        return view('admin.download.versions.index', compact('download', 'versions'));
    }

    public function create($downloadId)
    {
        $download = Download::findOrFail($downloadId);
        return view('admin.download.versions.create', compact('download'));
    }

    public function store(Request $request, $downloadId)
    {
        $download = Download::findOrFail($downloadId);

        $data = $request->validate([
            'version'     => ['required', 'string', 'max:50'],
            'changelog'   => ['nullable', 'string'],
            'released_at' => ['nullable', 'date'],
            'is_active'   => ['nullable', 'boolean'],
            'is_latest'   => ['nullable', 'boolean'],
        ]);

        // Unica per download
        if (DownloadVersion::where('download_id', $download->id)->where('version', $data['version'])->exists()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Versione già presente per questo download.');
        }

        $data['download_id'] = $download->id;
        $data['is_active'] = (int)($data['is_active'] ?? 0) === 1;
        $data['is_latest'] = (int)($data['is_latest'] ?? 0) === 1;

        if ($data['is_latest']) {
            // reset others
            DownloadVersion::where('download_id', $download->id)->update(['is_latest' => 0]);
        }

        $version = DownloadVersion::create($data);

        return redirect()
            ->route('admin.download.versions.index', $download->id)
            ->with('success', 'Versione creata.');
    }

    public function edit($downloadId, $id)
    {
        $download = Download::findOrFail($downloadId);
        $version = DownloadVersion::where('download_id', $download->id)->findOrFail($id);

        return view('admin.download.versions.edit', compact('download', 'version'));
    }

    public function update(Request $request, $downloadId, $id)
    {
        $download = Download::findOrFail($downloadId);
        $version = DownloadVersion::where('download_id', $download->id)->findOrFail($id);

        $data = $request->validate([
            'version'     => ['required', 'string', 'max:50'],
            'changelog'   => ['nullable', 'string'],
            'released_at' => ['nullable', 'date'],
            'is_active'   => ['nullable', 'boolean'],
            'is_latest'   => ['nullable', 'boolean'],
        ]);

        // Unica per download
        if (DownloadVersion::where('download_id', $download->id)
            ->where('version', $data['version'])
            ->where('id', '!=', $version->id)
            ->exists()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Versione già presente per questo download.');
        }

        $data['is_active'] = (int)($data['is_active'] ?? 0) === 1;
        $data['is_latest'] = (int)($data['is_latest'] ?? 0) === 1;

        if ($data['is_latest']) {
            DownloadVersion::where('download_id', $download->id)->update(['is_latest' => 0]);
        }

        $version->update($data);

        return redirect()
            ->route('admin.download.versions.index', $download->id)
            ->with('success', 'Versione aggiornata.');
    }

    public function destroy($downloadId, $id)
    {
        $download = Download::findOrFail($downloadId);
        $version = DownloadVersion::query()
            ->with('assets')
            ->where('download_id', $download->id)
            ->findOrFail($id);

        // Elimina file fisici associati
        if (method_exists($version, 'assets')) {
            foreach ($version->assets as $asset) {
                try {
                    \Storage::disk('local')->delete($asset->stored_path);
                } catch (\Throwable $e) {
                    // ignora
                }
            }
        }

        $version->delete();

        return redirect()
            ->route('admin.download.versions.index', $download->id)
            ->with('success', 'Versione eliminata.');
    }

    public function toggle($downloadId, $id)
    {
        $download = Download::findOrFail($downloadId);
        $version = DownloadVersion::where('download_id', $download->id)->findOrFail($id);

        $version->is_active = !$version->is_active;
        $version->save();

        return redirect()
            ->back()
            ->with('success', 'Stato versione aggiornato.');
    }

    public function setLatest($downloadId, $id)
    {
        $download = Download::findOrFail($downloadId);
        $version = DownloadVersion::where('download_id', $download->id)->findOrFail($id);

        DownloadVersion::where('download_id', $download->id)->update(['is_latest' => 0]);
        $version->is_latest = 1;
        $version->save();

        return redirect()
            ->back()
            ->with('success', 'Versione impostata come latest.');
    }
}
