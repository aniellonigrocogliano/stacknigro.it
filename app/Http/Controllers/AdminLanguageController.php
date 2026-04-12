<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;

class AdminLanguageController extends Controller
{
    public function index()
    {
        $languages = Language::query()
            ->orderBy('sort_order')
            ->orderBy('label')
            ->paginate(50);

        return view('admin.download.languages.index', compact('languages'));
    }

    public function create()
    {
        return view('admin.download.languages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key'        => ['required', 'string', 'max:120', 'unique:languages,key'],
            'label'      => ['required', 'string', 'max:190'],
            'fa_icon'    => ['required', 'string', 'max:190'],
            'is_active'  => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['key'] = strtolower(trim($data['key']));
        $data['is_active'] = (int)($data['is_active'] ?? 0) === 1;
        $data['sort_order'] = (int)($data['sort_order'] ?? 0);

        $lang = Language::create($data);

        return redirect()
            ->route('admin.download.languages.edit', $lang->id)
            ->with('success', 'Tecnologia creata correttamente.');
    }

    public function edit($id)
    {
        $language = Language::findOrFail($id);
        return view('admin.download.languages.edit', compact('language'));
    }

    public function update(Request $request, $id)
    {
        $language = Language::findOrFail($id);

        $data = $request->validate([
            'key'        => ['required', 'string', 'max:120', 'unique:languages,key,' . $language->id],
            'label'      => ['required', 'string', 'max:190'],
            'fa_icon'    => ['required', 'string', 'max:190'],
            'is_active'  => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['key'] = strtolower(trim($data['key']));
        $data['is_active'] = (int)($data['is_active'] ?? 0) === 1;
        $data['sort_order'] = (int)($data['sort_order'] ?? $language->sort_order ?? 0);

        $language->update($data);

        return redirect()
            ->route('admin.download.languages.edit', $language->id)
            ->with('success', 'Tecnologia aggiornata.');
    }

    public function destroy($id)
    {
        $language = Language::findOrFail($id);

        // Regola: eliminabile solo se ha 0 download collegati (pivot download_language).
        // Assumiamo relazione: downloads() belongsToMany.
        $linked = method_exists($language, 'downloads')
            ? $language->downloads()->count()
            : \DB::table('download_language')->where('language_id', $language->id)->count();

        if ($linked > 0) {
            return redirect()
                ->back()
                ->with('error', "Non eliminabile: tecnologia collegata a {$linked} download. Disattivala invece.");
        }

        $language->delete();

        return redirect()
            ->route('admin.download.languages.index')
            ->with('success', 'Tecnologia eliminata.');
    }

    public function toggle($id)
    {
        $language = Language::findOrFail($id);
        $language->is_active = !$language->is_active;
        $language->save();

        return redirect()
            ->back()
            ->with('success', 'Stato tecnologia aggiornato.');
    }
}
