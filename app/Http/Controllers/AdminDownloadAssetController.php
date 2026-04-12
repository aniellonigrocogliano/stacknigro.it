<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\DownloadAsset;
use App\Models\DownloadVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminDownloadAssetController extends Controller
{
    public function index($downloadId, $versionId)
    {
        $download = Download::findOrFail($downloadId);
        $version = DownloadVersion::where('download_id', $download->id)->findOrFail($versionId);

        $assets = DownloadAsset::query()
            ->where('version_id', $version->id)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(50);

        return view('admin.download.assets.index', compact('download', 'version', 'assets'));
    }

    public function create($downloadId, $versionId)
    {
        $download = Download::findOrFail($downloadId);
        $version = DownloadVersion::where('download_id', $download->id)->findOrFail($versionId);

        return view('admin.download.assets.create', compact('download', 'version'));
    }

    public function store(Request $request, $downloadId, $versionId)
    {
        $download = Download::findOrFail($downloadId);
        $version  = DownloadVersion::where('download_id', $download->id)->findOrFail($versionId);

        $data = $request->validate([
            'format'     => ['required', 'string', 'max:30'], // zip/rar/exe/msi/pdf...
            'file'       => ['required', 'file', 'max:512000'], // 500MB (adatta al tuo hosting)
            'is_active'  => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mime = $file->getMimeType() ?: null;
        $size = (int)($file->getSize() ?: 0);
        $sha256 = hash_file('sha256', $file->getRealPath());

        // Path: downloads/{download_slug}/{version}/...
        $safeBase = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
        $ext = strtolower((string)$file->getClientOriginalExtension());
        $filename = $safeBase . '_' . time() . ($ext ? '.' . $ext : '');

        $dir = 'downloads/' . $download->slug . '/' . $version->version;
        $storedPath = $file->storeAs($dir, $filename, 'local');

        $asset = DownloadAsset::create([
            'version_id'      => $version->id,
            'format'          => strtolower(trim($data['format'])),
            'original_name'   => $originalName,
            'stored_path'     => $storedPath,
            'mime_type'       => $mime,
            'size_bytes'      => $size,
            'sha256'          => $sha256,
            'downloads_count' => 0,
            'is_active'       => (int)($data['is_active'] ?? 0) === 1,
            'sort_order'      => (int)($data['sort_order'] ?? 0),
        ]);

        return redirect()
            ->route('admin.download.assets.index', [$download->id, $version->id])
            ->with('success', 'File caricato correttamente.');
    }

    public function edit($downloadId, $versionId, $id)
    {
        $download = Download::findOrFail($downloadId);
        $version  = DownloadVersion::where('download_id', $download->id)->findOrFail($versionId);
        $asset    = DownloadAsset::where('version_id', $version->id)->findOrFail($id);

        return view('admin.download.assets.edit', compact('download', 'version', 'asset'));
    }

    public function update(Request $request, $downloadId, $versionId, $id)
    {
        $download = Download::findOrFail($downloadId);
        $version  = DownloadVersion::where('download_id', $download->id)->findOrFail($versionId);
        $asset    = DownloadAsset::where('version_id', $version->id)->findOrFail($id);

        $data = $request->validate([
            'format'     => ['required', 'string', 'max:30'],
            'is_active'  => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'file'       => ['nullable', 'file', 'max:512000'], // opzionale sostituzione
        ]);

        $update = [
            'format'     => strtolower(trim($data['format'])),
            'is_active'  => (int)($data['is_active'] ?? 0) === 1,
            'sort_order' => (int)($data['sort_order'] ?? $asset->sort_order ?? 0),
        ];

        // Se carichi nuovo file, sostituisci (e ricalcola sha256/size/mime/path)
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $mime = $file->getMimeType() ?: null;
            $size = (int)($file->getSize() ?: 0);
            $sha256 = hash_file('sha256', $file->getRealPath());

            $safeBase = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
            $ext = strtolower((string)$file->getClientOriginalExtension());
            $filename = $safeBase . '_' . time() . ($ext ? '.' . $ext : '');

            $dir = 'downloads/' . $download->slug . '/' . $version->version;
            $storedPath = $file->storeAs($dir, $filename, 'local');

            // elimina vecchio file (best-effort)
            try {
                Storage::disk('local')->delete($asset->stored_path);
            } catch (\Throwable $e) {
                // ignora
            }

            $update['original_name'] = $originalName;
            $update['stored_path']   = $storedPath;
            $update['mime_type']     = $mime;
            $update['size_bytes']    = $size;
            $update['sha256']        = $sha256;
        }

        $asset->update($update);

        return redirect()
            ->route('admin.download.assets.index', [$download->id, $version->id])
            ->with('success', 'Asset aggiornato.');
    }

    public function destroy($downloadId, $versionId, $id)
    {
        $download = Download::findOrFail($downloadId);
        $version  = DownloadVersion::where('download_id', $download->id)->findOrFail($versionId);
        $asset    = DownloadAsset::where('version_id', $version->id)->findOrFail($id);

        // elimina file fisico
        try {
            Storage::disk('local')->delete($asset->stored_path);
        } catch (\Throwable $e) {
            // ignora
        }

        $asset->delete();

        return redirect()
            ->route('admin.download.assets.index', [$download->id, $version->id])
            ->with('success', 'Asset eliminato.');
    }

    public function toggle($downloadId, $versionId, $id)
    {
        $download = Download::findOrFail($downloadId);
        $version  = DownloadVersion::where('download_id', $download->id)->findOrFail($versionId);
        $asset    = DownloadAsset::where('version_id', $version->id)->findOrFail($id);

        $asset->is_active = !$asset->is_active;
        $asset->save();

        return redirect()
            ->back()
            ->with('success', 'Stato asset aggiornato.');
    }
}
