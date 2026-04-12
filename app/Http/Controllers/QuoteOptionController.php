<?php

namespace App\Http\Controllers;

use App\Models\QuoteOption;
use App\Models\QuoteLevel;
use Illuminate\Http\Request;

class QuoteOptionController extends Controller
{
    // CREA UNA NUOVA OPZIONE
    public function store(Request $request)
    {
        $data = $request->validate([
            'level_id' => ['required', 'exists:quote_levels,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'hours' => ['nullable', 'numeric', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
            'pivot_is_required' => ['nullable', 'boolean'],
            'pivot_is_hidden_by_default' => ['nullable', 'boolean'],
        ]);

        $option = QuoteOption::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'hours' => $data['hours'] ?? 0,
            'price' => $data['price'] ?? 0,
            'is_active' => (bool)($data['is_active'] ?? false),
            'is_default' => (bool)($data['is_default'] ?? false),
        ]);

        // Collega l'opzione appena creata al livello selezionato
        $level = QuoteLevel::find($data['level_id']);
        $level->options()->attach($option->id, [
            'is_required' => (bool)($data['pivot_is_required'] ?? false),
            'is_hidden_by_default' => (bool)($data['pivot_is_hidden_by_default'] ?? false),
            'sort_order' => 0,
        ]);

        return back()->with('success', 'Opzione creata e collegata al livello.');
    }

    // COLLEGA UN'OPZIONE ESISTENTE AL LIVELLO
    public function attach(Request $request)
    {
        $data = $request->validate([
            'level_id' => ['required', 'exists:quote_levels,id'],
            'option_id' => ['required', 'exists:quote_options,id'],
        ]);

        $level = QuoteLevel::find($data['level_id']);

        // Collega solo se non è già collegata
        if (!$level->options()->where('quote_option_id', $data['option_id'])->exists()) {
            $level->options()->attach($data['option_id'], [
                'is_required' => false,
                'is_hidden_by_default' => false,
                'sort_order' => 0,
            ]);
            return back()->with('success', 'Opzione collegata con successo.');
        }

        return back()->withErrors(['option_id' => 'Questa opzione è già collegata a questo livello.']);
    }

    // AGGIORNA I DATI PRINCIPALI DELL'OPZIONE
    public function update(Request $request, QuoteOption $quoteOption)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'hours' => ['nullable', 'numeric', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable'], // rimosso boolean per gestire l'hidden
            'is_default' => ['nullable'],
        ]);

        $quoteOption->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'hours' => $data['hours'] ?? 0,
            'price' => $data['price'] ?? 0,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : false,
            'is_default' => $request->has('is_default') ? (bool)$request->is_default : false,
        ]);

        return back()->with('success', 'Dati globali opzione aggiornati.');
    }
    // ELIMINA TOTALMENTE L'OPZIONE DAL DATABASE
    public function destroy(QuoteOption $quoteOption)
    {
        // Rimuove associazioni e cancella
        $quoteOption->levels()->detach();
        if (method_exists($quoteOption, 'packages')) {
            $quoteOption->packages()->detach();
        }
        $quoteOption->delete();

        return back()->with('success', 'Opzione eliminata definitivamente.');
    }
}
