<?php

namespace App\Http\Controllers;

use App\Models\QuoteLevel;
use App\Models\QuoteOption;
use Illuminate\Http\Request;

class QuoteLevelController extends Controller
{
    public function index()
    {
        $levels = QuoteLevel::withCount(['options'])->orderBy('level')->orderBy('sort_order')->get();
        return view('admin.quotes.levels.index', compact('levels'));
    }

    public function create()
    {
        return view('admin.quotes.levels.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'level' => ['required','integer','min:1','max:20'],
            'title' => ['required','string','max:255'],
            'selection_type' => ['required','in:single,multi'],
            'is_required' => ['nullable','boolean'],
            'is_active' => ['nullable','boolean'],
            'sort_order' => ['nullable','integer','min:0','max:9999'],
        ]);

        QuoteLevel::create([
            'level' => $data['level'],
            'title' => $data['title'],
            'selection_type' => $data['selection_type'],
            'is_required' => (bool)($data['is_required'] ?? false),
            'is_active' => (bool)($data['is_active'] ?? true),
            'sort_order' => (int)($data['sort_order'] ?? 0),
        ]);

        return redirect()->route('admin.quote-levels.index')->with('success', 'Livello creato.');
    }

    public function edit(QuoteLevel $quoteLevel)
    {
        $quoteLevel->load(['options' => fn($q) => $q->orderBy('quote_level_option.sort_order')->orderBy('quote_level_option.id')]);

        $allOptions = QuoteOption::orderBy('label')->get();

        // per precompilare pivot su tabella
        $pivotByOptionId = $quoteLevel->options->keyBy('id')->map(fn($opt) => [
            'is_active' => (int)($opt->pivot->is_active ?? 1),
            'sort_order' => (int)($opt->pivot->sort_order ?? 0),
        ]);

        return view('admin.quotes.levels.edit', compact('quoteLevel','allOptions','pivotByOptionId'));
    }

    public function update(Request $request, QuoteLevel $quoteLevel)
    {
        $data = $request->validate([
            'level' => ['required','integer','min:1','max:20'],
            'title' => ['required','string','max:255'],
            'selection_type' => ['required','in:single,multi'],
            'is_required' => ['nullable','boolean'],
            'is_active' => ['nullable','boolean'],
            'sort_order' => ['nullable','integer','min:0','max:9999'],
        ]);

        $quoteLevel->update([
            'level' => $data['level'],
            'title' => $data['title'],
            'selection_type' => $data['selection_type'],
            'is_required' => (bool)($data['is_required'] ?? false),
            'is_active' => (bool)($data['is_active'] ?? true),
            'sort_order' => (int)($data['sort_order'] ?? 0),
        ]);

        return back()->with('success', 'Livello aggiornato.');
    }

    public function destroy(QuoteLevel $quoteLevel)
    {
        $quoteLevel->delete();
        return redirect()->route('admin.quote-levels.index')->with('success', 'Livello eliminato.');
    }

    /**
     * Salva assegnazione opzioni al livello + pivot (sort_order, is_active)
     */
    public function syncOptions(Request $request, QuoteLevel $quoteLevel)
    {
        $data = $request->validate([
            'options' => ['array'],
            'options.*.id' => ['required','integer','exists:quote_options,id'],
            'options.*.sort_order' => ['nullable','integer','min:0','max:9999'],
            'options.*.is_active' => ['nullable','boolean'],
        ]);

        $sync = [];

        foreach (($data['options'] ?? []) as $row) {
            $sync[(int)$row['id']] = [
                'sort_order' => (int)($row['sort_order'] ?? 0),
                'is_active' => (int)($row['is_active'] ?? 1),
            ];
        }

        $quoteLevel->options()->sync($sync);

        return back()->with('success', 'Opzioni del livello aggiornate.');
    }
}
