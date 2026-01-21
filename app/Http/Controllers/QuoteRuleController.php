<?php

namespace App\Http\Controllers;

use App\Models\QuoteLevel;
use App\Models\QuoteOption;
use App\Models\QuoteRule;
use Illuminate\Http\Request;

class QuoteRuleController extends Controller
{
    private array $actionTypes = [
        'show_level','hide_level',
        'require_option','auto_select_option',
        'add_hours','add_price',
        'set_hours','set_price',
    ];

    // Etichette in ITA (per select)
    private array $actionTypeLabels = [
        'show_level'         => 'Mostra un livello',
        'hide_level'         => 'Nascondi un livello',
        'require_option'     => 'Rendi obbligatoria una opzione',
        'auto_select_option' => 'Seleziona automaticamente una opzione',
        'add_hours'          => 'Aggiungi ore (range)',
        'add_price'          => 'Aggiungi prezzo (range)',
        'set_hours'          => 'Imposta ore (range)',
        'set_price'          => 'Imposta prezzo (range)',
    ];

    public function index(Request $request)
    {
        $levels = QuoteLevel::orderBy('level')->orderBy('sort_order')->get();

        $query = QuoteRule::with(['triggerLevel','triggerOption','targetLevel','targetOption'])
            ->orderBy('sort_order')
            ->latest('id');

        if ($request->filled('action_type')) {
            $query->where('action_type', $request->string('action_type'));
        }

        if ($request->filled('trigger_level_id')) {
            $query->where('trigger_level_id', (int) $request->input('trigger_level_id'));
        }

        $rules = $query->paginate(20)->withQueryString();

        $actionTypes = $this->actionTypes;
        $actionTypeLabels = $this->actionTypeLabels;

        return view('admin.quotes.rules.index', compact('rules','levels','actionTypes','actionTypeLabels'));
    }

    public function create()
    {
        // IMPORTANTISSIMO: carichiamo le opzioni per livello
        $levels = QuoteLevel::with(['options' => function ($q) {
                $q->orderBy('sort_order')->orderBy('label');
            }])
            ->orderBy('level')->orderBy('sort_order')
            ->get();

        // lista completa opzioni (serve per fallback / target)
        $options = QuoteOption::orderBy('label')->get();

        // array pronto per JS: [levelId => [{id,label},...]]
        $optionsByLevel = [];
        foreach ($levels as $lvl) {
            $optionsByLevel[$lvl->id] = $lvl->options->map(fn($o) => [
                'id'    => $o->id,
                'label' => $o->label ?? ('Opzione #'.$o->id),
            ])->values()->all();
        }

        $actionTypes = $this->actionTypes;
        $actionTypeLabels = $this->actionTypeLabels;

        return view('admin.quotes.rules.create', compact(
            'levels','options','optionsByLevel','actionTypes','actionTypeLabels'
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validateRule($request);
        QuoteRule::create($data);

        return redirect()->route('admin.quote-rules.index')->with('success', 'Regola creata.');
    }

    public function edit(QuoteRule $quoteRule)
    {
        $levels = QuoteLevel::with(['options' => function ($q) {
                $q->orderBy('sort_order')->orderBy('label');
            }])
            ->orderBy('level')->orderBy('sort_order')
            ->get();

        $options = QuoteOption::orderBy('label')->get();

        $optionsByLevel = [];
        foreach ($levels as $lvl) {
            $optionsByLevel[$lvl->id] = $lvl->options->map(fn($o) => [
                'id'    => $o->id,
                'label' => $o->label ?? ('Opzione #'.$o->id),
            ])->values()->all();
        }

        $actionTypes = $this->actionTypes;
        $actionTypeLabels = $this->actionTypeLabels;

        return view('admin.quotes.rules.edit', compact(
            'quoteRule','levels','options','optionsByLevel','actionTypes','actionTypeLabels'
        ));
    }

    public function update(Request $request, QuoteRule $quoteRule)
    {
        $data = $this->validateRule($request);
        $quoteRule->update($data);

        return back()->with('success', 'Regola aggiornata.');
    }

    public function destroy(QuoteRule $quoteRule)
    {
        $quoteRule->delete();
        return redirect()->route('admin.quote-rules.index')->with('success', 'Regola eliminata.');
    }

    private function validateRule(Request $request): array
    {
        $base = $request->validate([
            'trigger_level_id'  => ['required','exists:quote_levels,id'],
            'trigger_option_id' => ['required','exists:quote_options,id'],

            'action_type' => ['required','in:'.implode(',', $this->actionTypes)],

            'target_level_id'  => ['nullable','exists:quote_levels,id'],
            'target_option_id' => ['nullable','exists:quote_options,id'],

            'value_min' => ['nullable','integer','min:0','max:9999999'],
            'value_max' => ['nullable','integer','min:0','max:9999999'],

            'is_active'  => ['nullable','boolean'],
            'sort_order' => ['nullable','integer','min:0','max:9999'],
        ]);

        $action = $base['action_type'];

        $needsTargetLevel  = in_array($action, ['show_level','hide_level'], true);
        $needsTargetOption = in_array($action, ['require_option','auto_select_option'], true);
        $needsValues       = in_array($action, ['add_hours','add_price','set_hours','set_price'], true);

        // target level per show/hide
        if ($needsTargetLevel && empty($base['target_level_id'])) {
            abort(422, 'target_level_id richiesto per action_type='.$action);
        }

        // target option per require/auto_select
        if ($needsTargetOption && empty($base['target_option_id'])) {
            abort(422, 'target_option_id richiesto per action_type='.$action);
        }

        // valori per ore/prezzi
        if ($needsValues && ($base['value_min'] === null && $base['value_max'] === null)) {
            abort(422, 'value_min/value_max richiesti per action_type='.$action);
        }

        return [
            'trigger_level_id'  => (int)$base['trigger_level_id'],
            'trigger_option_id' => (int)$base['trigger_option_id'],
            'action_type'       => $action,

            'target_level_id'   => $base['target_level_id'] ?? null,
            'target_option_id'  => $base['target_option_id'] ?? null,

            'value_min'         => $base['value_min'] ?? null,
            'value_max'         => $base['value_max'] ?? null,

            'is_active'         => (bool)($base['is_active'] ?? true),
            'sort_order'        => (int)($base['sort_order'] ?? 0),
        ];
    }
}

