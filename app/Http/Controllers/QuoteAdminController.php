<?php

namespace App\Http\Controllers;

use App\Models\QuoteLevel;
use App\Models\QuoteOption;
use App\Models\QuoteRule;
use App\Models\QuotePackage;
use Illuminate\Http\Request;

class QuoteAdminController extends Controller
{
    /**
     * Schermata principale del preventivatore (Gestione Tab)
     */
    public function index(Request $request)
    {
        // 1. Identifichiamo il Tab attivo (default: levels)
        $tab = $request->query('tab', 'levels');

        // 2. Carichiamo i Livelli (Base per quasi tutti i tab)
        $levels = QuoteLevel::orderBy('level')->orderBy('sort_order')->get();

        // --------------------------------------------------------------------------
        // LOGICA TAB: OPTIONS (Associazione Opzioni <-> Livelli)
        // --------------------------------------------------------------------------
        $selectedLevelId = $request->query('level_id');
        $selectedLevel = null;
        $availableOptions = collect();

        if ($selectedLevelId) {
            $selectedLevel = QuoteLevel::with(['options' => function ($q) {
                $q->orderBy('quote_level_option.sort_order')->orderBy('quote_options.id');
            }])->find($selectedLevelId);
        } elseif ($levels->isNotEmpty()) {
            $selectedLevel = $levels->first();
            $selectedLevelId = $selectedLevel->id;
        }

        if ($selectedLevel) {
            $linked = $selectedLevel->options->pluck('id')->all();
            $availableOptions = QuoteOption::query()
                ->when(count($linked), fn($q) => $q->whereNotIn('id', $linked))
                ->orderBy('name')
                ->get();
        }

        // --------------------------------------------------------------------------
        // LOGICA TAB: RULES (Regole di visibilità tra opzioni)
        // --------------------------------------------------------------------------
        $rulesLevelId = $request->query('rules_level_id');
        $rulesTargetLevel = null;
        $rules = collect();
        $triggerLevels = collect();
        $rulesTargetOptions = collect();

        // Se non c'è un ID, prendiamo il primo livello utile per le regole (L2+)
        if (!$rulesLevelId && $levels->count() > 1) {
            $rulesTargetLevel = $levels->where('level', '>', 1)->first() ?? $levels->first();
            $rulesLevelId = $rulesTargetLevel->id;
        } elseif ($rulesLevelId) {
            $rulesTargetLevel = QuoteLevel::with('options')->find($rulesLevelId);
        }

        if ($rulesTargetLevel) {
            // Livelli che possono scatenare una regola (quelli precedenti al target)
            $triggerLevels = QuoteLevel::with(['options' => function($q) {
                    $q->orderBy('name');
                }])
                ->where('level', '<', $rulesTargetLevel->level)
                ->orderBy('level', 'asc')
                ->get();

            $rulesTargetOptions = $rulesTargetLevel->options()->orderBy('name')->get();
            $optionsIds = $rulesTargetOptions->pluck('id')->toArray();

            $rules = QuoteRule::with(['triggerOption', 'targetLevel', 'targetOption', 'triggerLevel'])
                ->where(function($q) use ($rulesTargetLevel, $optionsIds) {
                    $q->where('target_level_id', $rulesTargetLevel->id)
                      ->orWhereIn('target_option_id', $optionsIds);
                })
                ->orderBy('sort_order')
                ->get();
        }

        // --------------------------------------------------------------------------
        // LOGICA TAB: PACKAGES (Sistemata: Carichiamo sempre i dati)
        // --------------------------------------------------------------------------
        // FIX: Abbiamo tolto l'IF. Ora i pacchetti sono caricati anche se entri nel tab 'levels'.
        $packages = QuotePackage::with('options')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Ritorno della vista con tutti i dati necessari
        return view('admin.quotes.index', compact(
            'tab',
            'levels',
            'selectedLevelId',
            'selectedLevel',
            'availableOptions',
            'rulesLevelId',
            'rulesTargetLevel',
            'triggerLevels',
            'rules',
            'rulesTargetOptions',
            'packages'
        ));
    }
}
