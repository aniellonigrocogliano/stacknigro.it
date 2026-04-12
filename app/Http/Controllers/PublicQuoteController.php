<?php

namespace App\Http\Controllers;

use App\Models\InboxConversation;
use App\Models\QuoteLevel;
use App\Models\QuoteRule;
use App\Models\QuotePackage;
use Illuminate\Http\Request;

class PublicQuoteController extends Controller
{
    // Aggiunto il parametro $slug (opzionale)
    public function index(Request $request, $slug = null)
    {
        // 1. Carichiamo i PACCHETTI (Le Card)
        $packages = QuotePackage::query()
            ->where('is_active', 1)
            ->with('options.levels')
            ->orderBy('sort_order')
            ->get();

        // --- NUOVA LOGICA SLUG ---
        $selectedPackage = null;
        if ($slug) {
            $selectedPackage = QuotePackage::where('slug', $slug)
                ->where('is_active', 1)
                ->first();

            // Se lo slug è errato, reindirizziamo alla pagina base
            if (!$selectedPackage) {
                return redirect()->route('public.quotes.index');
            }
        }
        // -------------------------

        // 2. Carichiamo i LIVELLI (Il Wizard)
        $levels = QuoteLevel::query()
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with(['options' => function ($q) {
                $q->where('quote_options.is_active', 1)
                  ->orderBy('quote_level_option.sort_order')
                  ->orderBy('quote_options.sort_order')
                  ->orderBy('quote_options.id');
            }])
            ->get();

        $rules = QuoteRule::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        // Preparazione dati puliti per JS (Wizard)
        $levelsForJs = $levels->map(function ($l) {
            return [
                'id' => (int) $l->id,
                'name' => (string) $l->name,
                'selection_type' => (string) $l->selection_type,
            ];
        })->values();

        $rulesForJs = $rules->map(function ($r) {
            return [
                'id' => (int) $r->id,
                'trigger_option_id' => (int) $r->trigger_option_id,
                'action_type' => (string) $r->action_type,
                'target_level_id' => $r->target_level_id ? (int) $r->target_level_id : null,
                'target_option_id' => $r->target_option_id ? (int) $r->target_option_id : null,
            ];
        })->values();

        $ruleTargetOptionIds = $rules->whereIn('action_type', ['show_option'])->pluck('target_option_id')->filter()->unique()->values();
        $ruleTargetLevelIds = $rules->whereIn('action_type', ['show_level', 'hide_level'])->pluck('target_level_id')->filter()->unique()->values();

        return view('public.quotes', [
            'packages' => $packages,
            'selectedPackage' => $selectedPackage, // <-- PASSATO ALLA VISTA
            'levels' => $levels,
            'rules' => $rules,
            'levelsForJs' => $levelsForJs,
            'rulesForJs' => $rulesForJs,
            'ruleTargetOptionIds' => $ruleTargetOptionIds,
            'ruleTargetLevelIds' => $ruleTargetLevelIds,
        ]);
    }

    public function store(Request $request)
    {
        $mode = $request->input('mode', 'send');
        $packageId = $request->input('package_id');

        $rules = [
            'privacy_accepted' => ['accepted'],
            'quote_payload'    => ['nullable'],
            'quote_summary'    => ['nullable', 'string'],
        ];

        if ($mode !== 'anonymous') {
            $rules = array_merge($rules, [
                'name'         => ['required', 'string', 'max:120'],
                'email'        => ['required', 'email', 'max:190'],
                'phone'        => ['nullable', 'string', 'max:50'],
                'subject'      => ['nullable', 'string', 'max:180'],
                'how_found'    => ['nullable', 'string', 'max:20'],
                'user_message' => ['nullable', 'string'],
            ]);
        }

        $data = $request->validate($rules);

        $quoteSummary = $request->input('quote_summary', '');
        $quotePayload = $request->input('quote_payload');

        if ($packageId) {
            $package = QuotePackage::find($packageId);
            if ($package) {
                $packageInfo = "PACCHETTO SCELTO: {$package->name}\n";
                $packageInfo .= "PREZZO PROMO: € " . number_format($package->promo_price, 2, ',', '.') . "\n";
                $packageInfo .= "DESCRIZIONE: {$package->description}\n";

                $quotePayload = json_encode(['package_id' => $package->id, 'package_name' => $package->name]);
                $quoteSummary = $packageInfo . "\n---\n" . $quoteSummary;
            }
        }

        $finalMessage = trim((string)($data['user_message'] ?? ''));
        $finalMessage = ($finalMessage !== '') ? $finalMessage . "\n\n---\n" . $quoteSummary : $quoteSummary;

        InboxConversation::create([
          'source' => $packageId ? 'package' : 'quote',
            'name'                => ($mode === 'anonymous') ? 'Preventivo anonimo' : $data['name'],
            'email'               => ($mode === 'anonymous') ? 'anonimo@stacknigro.it' : $data['email'],
            'phone'               => $data['phone'] ?? null,
            'subject'             => $data['subject'] ?? ($packageId ? 'Acquisto Pacchetto' : 'Richiesta preventivo'),
            'how_found'           => $data['how_found'] ?? null,
            'user_message'        => $finalMessage,
            'privacy_accepted'    => 1,
            'privacy_accepted_at' => now(),
            'quote_payload'       => $quotePayload,
            'ip_address'          => $request->ip(),
            'user_agent'          => substr((string) $request->userAgent(), 0, 1000),
        ]);

        $msg = ($mode === 'anonymous')
            ? 'Preventivo calcolato in modo anonimo.'
            : 'Richiesta inviata con successo! Ti risponderò al più presto.';

        // Invece di back(), potresti voler tornare alla rotta pulita per resettare l'URL
        return redirect()->route('public.quotes.index')->with('success', $msg);
    }
}
