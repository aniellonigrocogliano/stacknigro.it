<?php

namespace App\Http\Controllers;

use App\Models\Suite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSuiteController extends Controller
{
    public function index()
    {
        $suites = Suite::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(25);

        return view('admin.download.suites.index', compact('suites'));
    }

    public function create()
    {
        return view('admin.download.suites.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:190'],
            'slug'        => ['nullable', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'sort_order'  => ['nullable', 'integer'],
        ]);

        // ✅ checkbox switch (se non spuntata non arriva)
        $data['is_active']  = $request->has('is_active') ? 1 : 0;
        $data['sort_order'] = (int)($data['sort_order'] ?? 0);

        // ✅ slug auto se vuoto + unico
        $base = trim((string)($data['slug'] ?? '')) !== ''
            ? $data['slug']
            : $data['name'];

        $baseSlug = Str::slug($base);
        $slug = $baseSlug;
        $i = 2;
        while (Suite::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i;
            $i++;
        }
        $data['slug'] = $slug;

        $suite = Suite::create($data);

        return redirect()
            ->route('admin.download.suites.edit', $suite->id)
            ->with('success', 'Suite creata correttamente.');
    }

    public function edit($id)
    {
        $suite = Suite::findOrFail($id);
        return view('admin.download.suites.edit', compact('suite'));
    }

    public function update(Request $request, $id)
    {
        $suite = Suite::findOrFail($id);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:190'],
            'slug'        => ['nullable', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'sort_order'  => ['nullable', 'integer'],
        ]);

        $data['is_active']  = $request->has('is_active') ? 1 : 0;
        $data['sort_order'] = (int)($data['sort_order'] ?? ($suite->sort_order ?? 0));

        // ✅ slug: se vuoto, lo rigenero dal nome (così non rompi il NOT NULL)
        $base = trim((string)($data['slug'] ?? '')) !== ''
            ? $data['slug']
            : $data['name'];

        $baseSlug = Str::slug($base);
        $slug = $baseSlug;
        $i = 2;
        while (Suite::where('slug', $slug)->where('id', '!=', $suite->id)->exists()) {
            $slug = $baseSlug . '-' . $i;
            $i++;
        }
        $data['slug'] = $slug;

        $suite->update($data);

        return redirect()
            ->route('admin.download.suites.edit', $suite->id)
            ->with('success', 'Suite aggiornata.');
    }

    public function destroy($id)
    {
        $suite = Suite::findOrFail($id);

        // FK downloads.suite_id è ON DELETE SET NULL, quindi è sicuro.
        $suite->delete();

        return redirect()
            ->route('admin.download.suites.index')
            ->with('success', 'Suite eliminata.');
    }

    public function toggle($id)
    {
        $suite = Suite::findOrFail($id);
        $suite->is_active = !$suite->is_active;
        $suite->save();

        return redirect()
            ->back()
            ->with('success', 'Stato suite aggiornato.');
    }
}
