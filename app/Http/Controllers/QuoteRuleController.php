<?php

namespace App\Http\Controllers;

use App\Models\QuoteLevel;
use App\Models\QuoteOption;
use App\Models\QuoteRule;
use Illuminate\Http\Request;

class QuoteRuleController extends Controller
{
    // Questi devono corrispondere ESATTAMENTE a quelli definiti nell'ENUM del tuo database (colonna 3)
    private array $actionTypes = [
        'show_level', 'hide_level',
        'show_option', 'hide_option',
        'require_option', 'add_hours', 'add_price'
    ];

    public function store(Request $request)
    {
        // 1. Validiamo solo quello che esiste davvero nella tabella
        $data = $request->validate([
            'trigger_option_id' => ['required', 'exists:quote_options,id'],
            'action_type'       => ['required', 'in:' . implode(',', $this->actionTypes)],
            'target_level_id'   => ['nullable', 'exists:quote_levels,id'],
            'target_option_id'  => ['nullable', 'exists:quote_options,id'],
            'sort_order'        => ['nullable', 'integer'],
        ]);

        // 2. Creiamo la regola (Laravel userà solo queste 5 chiavi)
        QuoteRule::create($data);

        return back()->with('success', 'Regola salvata correttamente.');
    }

    public function destroy(QuoteRule $quoteRule)
    {
        $quoteRule->delete();
        return back()->with('success', 'Regola eliminata.');
    }

    // ... (Mantieni gli altri metodi create/edit se ti servono, ma assicurati che non usino trigger_level_id)
}
