<?php

namespace App\Http\Controllers;

use App\Models\QuoteLevel;
use App\Models\QuoteOption;
use App\Models\QuoteRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QuoteAdminController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'levels');

        $levels = QuoteLevel::orderBy('level')->orderBy('sort_order')->get();

        // ----------------------
        // TAB: OPTIONS
        // ----------------------
        $selectedLevelId = (int)($request->query('level_id') ?: ($levels->first()->id ?? 0));
        $selectedLevel = null;
        $availableOptions = collect();

        if ($selectedLevelId) {
            $selectedLevel = QuoteLevel::with(['options' => function ($q) {
                $q->orderBy('quote_level_option.sort_order')
                  ->orderBy('quote_options.id');
            }])->find($selectedLevelId);

            if ($selectedLevel) {
                $linked = $selectedLevel->options->pluck('id')->all();

                $availableOptions = QuoteOption::query()
                    ->when(count($linked), fn($q) => $q->whereNotIn('id', $linked))
                    ->orderBy('name')
                    ->get();
            }
        }

        // ----------------------
        // TAB: RULES (scopate per "livello target")
        // ----------------------
        // default: livello 2 (se esiste), altrimenti il primo
        $rulesLevelId = (int)($request->query('rules_level_id') ?: ($levels->get(1)->id ?? ($levels->first()->id ?? 0)));
        $rulesTargetLevel = $rulesLevelId ? QuoteLevel::find($rulesLevelId) : null;

        // opzioni del livello target
        $rulesTargetOptionIds = $rulesLevelId
            ? DB::table('quote_level_option')->where('quote_level_id', $rulesLevelId)->pluck('quote_option_id')->all()
            : [];

        $rulesTargetOptions = count($rulesTargetOptionIds)
            ? QuoteOption::whereIn('id', $rulesTargetOptionIds)->orderBy('name')->get()
            : collect();

        // trigger = opzioni del livello precedente (N-1), se esiste
        $rulesTriggerOptions = collect();
        if ($rulesTargetLevel && $rulesTargetLevel->level > 1) {
            $prevLevel = QuoteLevel::where('level', $rulesTargetLevel->level - 1)->first();
            if ($prevLevel) {
                $triggerIds = DB::table('quote_level_option')->where('quote_level_id', $prevLevel->id)->pluck('quote_option_id')->all();
                if (count($triggerIds)) {
                    $rulesTriggerOptions = QuoteOption::whereIn('id', $triggerIds)->orderBy('name')->get();
                }
            }
        }

        // regole filtrate: target_level_id = livello selezionato OR target_option_id appartenente a quel livello
        $rulesQuery = QuoteRule::with(['triggerOption', 'targetLevel', 'targetOption'])
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($rulesLevelId) {
            $rulesQuery->where(function ($q) use ($rulesLevelId, $rulesTargetOptionIds) {
                $q->where('target_level_id', $rulesLevelId);
                if (count($rulesTargetOptionIds)) {
                    $q->orWhereIn('target_option_id', $rulesTargetOptionIds);
                }
            });
        }

        $rules = $rulesQuery->get();

        return view('admin.quotes.index', compact(
            'tab',
            'levels',
            'selectedLevel',
            'selectedLevelId',
            'availableOptions',
            'rules',
            'rulesLevelId',
            'rulesTargetLevel',
            'rulesTriggerOptions',
            'rulesTargetOptions'
        ));
    }

    // ======================
    // LIVELLI (fissi)
    // ======================
    public function updateLevel(Request $request, QuoteLevel $level)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'selection_type' => ['required', Rule::in(['single', 'multi'])],
            'min_select' => ['required', 'integer', 'min:0', 'max:10'],
            'max_select' => ['nullable', 'integer', 'min:0', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $min = (int)$data['min_select'];
        $max = array_key_exists('max_select', $data) && $data['max_select'] !== null ? (int)$data['max_select'] : null;

        if ($max !== null && $max < $min) {
            return back()->withErrors(['max_select' => 'max_select deve essere >= min_select.']);
        }

        if ($data['selection_type'] === 'single') {
            $max = 1;
            if ($min > 1) $min = 1;
        }

        $level->update([
            'name' => $data['name'],
            'selection_type' => $data['selection_type'],
            'min_select' => $min,
            'max_select' => $max,
            'is_active' => (bool)($data['is_active'] ?? false),
        ]);

        return redirect()->route('quotes.index', ['tab' => 'levels'])->with('success', 'Livello aggiornato.');
    }

    // ======================
    // OPZIONI
    // ======================
    public function storeOption(Request $request)
    {
        $data = $request->validate([
            'level_id' => ['required', 'exists:quote_levels,id'],

            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'hours' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],

            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],

            // pivot
            'pivot_is_required' => ['nullable', 'boolean'],
            'pivot_is_hidden_by_default' => ['nullable', 'boolean'],
            'pivot_sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        DB::transaction(function () use ($data) {
            $opt = QuoteOption::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'hours' => $data['hours'] ?? null,
                'price' => $data['price'] ?? null,
                'is_active' => (bool)($data['is_active'] ?? true),
                'is_default' => (bool)($data['is_default'] ?? false),
            ]);

            $level = QuoteLevel::findOrFail((int)$data['level_id']);
            $level->options()->attach($opt->id, [
                'is_required' => (bool)($data['pivot_is_required'] ?? false),
                'is_hidden_by_default' => (bool)($data['pivot_is_hidden_by_default'] ?? false),
                'sort_order' => (int)($data['pivot_sort_order'] ?? 0),
            ]);
        });

        return redirect()
            ->route('quotes.index', ['tab' => 'options', 'level_id' => $data['level_id']])
            ->with('success', 'Opzione creata e collegata al livello.');
    }

    public function attachOption(Request $request)
    {
        $data = $request->validate([
            'level_id' => ['required', 'exists:quote_levels,id'],
            'option_id' => ['required', 'exists:quote_options,id'],
            'pivot_is_required' => ['nullable', 'boolean'],
            'pivot_is_hidden_by_default' => ['nullable', 'boolean'],
            'pivot_sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $level = QuoteLevel::findOrFail((int)$data['level_id']);

        $already = DB::table('quote_level_option')
            ->where('quote_level_id', $level->id)
            ->where('quote_option_id', (int)$data['option_id'])
            ->exists();

        if (!$already) {
            $level->options()->attach((int)$data['option_id'], [
                'is_required' => (bool)($data['pivot_is_required'] ?? false),
                'is_hidden_by_default' => (bool)($data['pivot_is_hidden_by_default'] ?? false),
                'sort_order' => (int)($data['pivot_sort_order'] ?? 0),
            ]);
        }

        return redirect()
            ->route('quotes.index', ['tab' => 'options', 'level_id' => $level->id])
            ->with('success', 'Opzione collegata al livello.');
    }

    public function updateOption(Request $request, QuoteOption $option)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'hours' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
            'redirect_level_id' => ['nullable', 'integer'],
        ]);

        $option->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'hours' => $data['hours'] ?? null,
            'price' => $data['price'] ?? null,
            'is_active' => (bool)($data['is_active'] ?? false),
            'is_default' => (bool)($data['is_default'] ?? false),
        ]);

        $redir = ['tab' => 'options'];
        if (!empty($data['redirect_level_id'])) $redir['level_id'] = (int)$data['redirect_level_id'];

        return redirect()->route('quotes.index', $redir)->with('success', 'Opzione aggiornata.');
    }

    public function destroyOption(QuoteOption $option)
    {
        $option->delete();
        return back()->with('success', 'Opzione eliminata.');
    }

    // ======================
    // PIVOT level<->option
    // ======================
    public function updatePivot(Request $request, QuoteLevel $level, QuoteOption $option)
    {
        $data = $request->validate([
            'is_required' => ['nullable', 'boolean'],
            'is_hidden_by_default' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        // aggiorna SOLO i campi presenti (niente reset)
        $update = [];
        if ($request->has('is_required')) $update['is_required'] = (bool)$data['is_required'];
        if ($request->has('is_hidden_by_default')) $update['is_hidden_by_default'] = (bool)$data['is_hidden_by_default'];
        if ($request->has('sort_order')) $update['sort_order'] = (int)$data['sort_order'];

        if (!empty($update)) {
            $level->options()->updateExistingPivot($option->id, $update);
        }

        return redirect()
            ->route('quotes.index', ['tab' => 'options', 'level_id' => $level->id])
            ->with('success', 'Impostazioni opzione aggiornate.');
    }

    public function detachOption(QuoteLevel $level, QuoteOption $option)
    {
        $level->options()->detach($option->id);

        return redirect()
            ->route('quotes.index', ['tab' => 'options', 'level_id' => $level->id])
            ->with('success', 'Opzione scollegata dal livello.');
    }

    // ======================
    // REGOLE
    // ======================
    public function storeRule(Request $request)
    {
        $data = $request->validate([
            'rules_level_id' => ['required', 'exists:quote_levels,id'], // livello target nel tab
            'trigger_option_id' => ['required', 'exists:quote_options,id'],
            'action_type' => ['required', Rule::in(['show_level', 'hide_level', 'show_option', 'hide_option', 'require_option'])],
            'target_level_id' => ['nullable', 'exists:quote_levels,id'],
            'target_option_id' => ['nullable', 'exists:quote_options,id'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        [$targetLevelId, $targetOptionId] = $this->normalizeRuleTarget(
            $data['action_type'],
            $data['target_level_id'] ?? null,
            $data['target_option_id'] ?? null
        );

        if ($targetLevelId === 'ERROR' || $targetOptionId === 'ERROR') {
            return back()->withErrors(['rule' => 'Target non valido per action_type.']);
        }

        QuoteRule::create([
            'trigger_option_id' => (int)$data['trigger_option_id'],
            'action_type' => $data['action_type'],
            'target_level_id' => $targetLevelId,
            'target_option_id' => $targetOptionId,
            'sort_order' => (int)($data['sort_order'] ?? 0),
        ]);

        return redirect()
            ->route('quotes.index', ['tab' => 'rules', 'rules_level_id' => (int)$data['rules_level_id']])
            ->with('success', 'Regola creata.');
    }

    public function destroyRule(QuoteRule $quoteRule)
    {
        $quoteRule->delete();
        return back()->with('success', 'Regola eliminata.');
    }

    private function normalizeRuleTarget(string $actionType, $targetLevelId, $targetOptionId): array
    {
        $targetLevelId = $targetLevelId !== null ? (int)$targetLevelId : null;
        $targetOptionId = $targetOptionId !== null ? (int)$targetOptionId : null;

        return match ($actionType) {
            'show_level', 'hide_level' => $targetLevelId ? [$targetLevelId, null] : ['ERROR', 'ERROR'],
            'show_option', 'hide_option', 'require_option' => $targetOptionId ? [null, $targetOptionId] : ['ERROR', 'ERROR'],
            default => ['ERROR', 'ERROR'],
        };
    }
}
