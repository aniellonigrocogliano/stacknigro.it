<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\Language;
use App\Models\Suite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminDownloadController extends Controller
{
    public function index()
    {
        $downloads = Download::query()
            ->with(['suite', 'languages'])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(25);

        return view('admin.download.items.index', compact('downloads'));
    }

    public function create()
    {
        $suites = Suite::query()->orderBy('sort_order')->orderBy('name')->get();
        $languages = Language::query()->where('is_active', 1)->orderBy('sort_order')->orderBy('label')->get();

        return view('admin.download.items.create', compact('suites', 'languages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'suite_id'    => ['nullable', 'integer', 'exists:suites,id'],
            'title'       => ['required', 'string', 'max:190'],
            'slug'        => ['nullable', 'string', 'max:190', 'unique:downloads,slug'],
            'description' => ['nullable', 'string'],
            'type'        => ['required', 'string', 'max:60'],  // cli/installer/portable/library/docs...
            'platform'    => ['nullable', 'string', 'max:60'],  // windows/linux/macos/all...
            'is_active'   => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer'],
            'language_ids'=> ['nullable', 'array'],
            'language_ids.*' => ['integer', 'exists:languages,id'],
        ]);

        $data['slug'] = trim((string)($data['slug'] ?? '')) !== ''
            ? Str::slug($data['slug'])
            : Str::slug($data['title']);

        $data['is_active'] = (int)($data['is_active'] ?? 0) === 1;
        $data['sort_order'] = (int)($data['sort_order'] ?? 0);
        $data['platform'] = isset($data['platform']) && trim((string)$data['platform']) !== '' ? trim((string)$data['platform']) : null;
        $data['suite_id'] = isset($data['suite_id']) && (int)$data['suite_id'] > 0 ? (int)$data['suite_id'] : null;

        // Evita collisioni slug post-slugify
        $baseSlug = $data['slug'];
        $i = 2;
        while (Download::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . $i;
            $i++;
        }

        $download = Download::create($data);

        // Salva pivot linguaggi (tutti allo stesso livello)
        $languageIds = $data['language_ids'] ?? [];
        if (method_exists($download, 'languages')) {
            $download->languages()->sync($languageIds);
        }

        return redirect()
            ->route('admin.download.items.edit', $download->id)
            ->with('success', 'Download creato correttamente.');
    }

    public function edit($id)
    {
        $download = Download::query()->with(['suite', 'languages'])->findOrFail($id);
        $suites = Suite::query()->orderBy('sort_order')->orderBy('name')->get();
        $languages = Language::query()->orderBy('sort_order')->orderBy('label')->get();

        // Le versioni le gestisci con controller dedicato, ma qui è comodo mostrarle.
        $versions = method_exists($download, 'versions')
            ? $download->versions()->orderByDesc('released_at')->orderByDesc('id')->get()
            : [];

        return view('admin.download.items.edit', compact('download', 'suites', 'languages', 'versions'));
    }

    public function update(Request $request, $id)
    {
        $download = Download::findOrFail($id);

        $data = $request->validate([
            'suite_id'    => ['nullable', 'integer', 'exists:suites,id'],
            'title'       => ['required', 'string', 'max:190'],
            'slug'        => ['nullable', 'string', 'max:190', 'unique:downloads,slug,' . $download->id],
            'description' => ['nullable', 'string'],
            'type'        => ['required', 'string', 'max:60'],
            'platform'    => ['nullable', 'string', 'max:60'],
            'is_active'   => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer'],
            'language_ids'=> ['nullable', 'array'],
            'language_ids.*' => ['integer', 'exists:languages,id'],
        ]);

        if (isset($data['slug']) && trim((string)$data['slug']) !== '') {
            $data['slug'] = Str::slug($data['slug']);
            // Evita collisioni slug post-slugify
            $baseSlug = $data['slug'];
            $i = 2;
            while (Download::where('slug', $data['slug'])->where('id', '!=', $download->id)->exists()) {
                $data['slug'] = $baseSlug . '-' . $i;
                $i++;
            }
        } else {
            unset($data['slug']); // non cambiare slug se vuoto
        }

        $data['is_active'] = (int)($data['is_active'] ?? 0) === 1;
        $data['sort_order'] = (int)($data['sort_order'] ?? $download->sort_order ?? 0);
        $data['platform'] = isset($data['platform']) && trim((string)$data['platform']) !== '' ? trim((string)$data['platform']) : null;
        $data['suite_id'] = isset($data['suite_id']) && (int)$data['suite_id'] > 0 ? (int)$data['suite_id'] : null;

        $download->update($data);

        // Pivot
        $languageIds = $data['language_ids'] ?? [];
        if (method_exists($download, 'languages')) {
            $download->languages()->sync($languageIds);
        }

        return redirect()
            ->route('admin.download.items.edit', $download->id)
            ->with('success', 'Download aggiornato.');
    }

    public function destroy($id)
    {
        $download = Download::query()->with(['versions.assets'])->findOrFail($id);

        // Pulizia file su storage (consigliata)
        if (method_exists($download, 'versions')) {
            foreach ($download->versions as $version) {
                if (method_exists($version, 'assets')) {
                    foreach ($version->assets as $asset) {
                        try {
                            \Storage::disk('local')->delete($asset->stored_path);
                        } catch (\Throwable $e) {
                            // ignora: non bloccare eliminazione DB
                        }
                    }
                }
            }
        }

        $download->delete();

        return redirect()
            ->route('admin.download.items.index')
            ->with('success', 'Download eliminato.');
    }

    public function toggle($id)
    {
        $download = Download::findOrFail($id);
        $download->is_active = !$download->is_active;
        $download->save();

        return redirect()
            ->back()
            ->with('success', 'Stato download aggiornato.');
    }
}
