<?php

namespace App\Http\Controllers;

use App\Models\InboxConversation;
use App\Models\QuoteLevel;
use App\Models\QuoteRule;
use Illuminate\Http\Request;

class PublicQuoteController extends Controller
{
    public function index()
    {
        // livelli attivi + opzioni attive + pivot
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

        // regole (tutte, poi in JS decidiamo)
        $rules = QuoteRule::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        // per evitare l’errore Blade dentro @json con fn() / array []
        // preparo qui i dati “puliti” per JS
        $levelsForJs = $levels->map(function ($l) {
            return [
                'id' => (int) $l->id,
                'name' => (string) $l->name,
                'selection_type' => (string) $l->selection_type, // 'single' | 'multi'
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

        // IDs opzioni che “possono” comparire perché target di show_option (anche se non default)
        $ruleTargetOptionIds = $rules
            ->whereIn('action_type', ['show_option'])
            ->pluck('target_option_id')
            ->filter()
            ->unique()
            ->values();

        // (opzionale) IDs livelli toccati da show/hide level
        $ruleTargetLevelIds = $rules
            ->whereIn('action_type', ['show_level', 'hide_level'])
            ->pluck('target_level_id')
            ->filter()
            ->unique()
            ->values();

        return view('public.quotes', [
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
        $mode = $request->input('mode', 'send'); // send | anonymous

        // payload calcolato lato JS
        $quotePayload = $request->input('quote_payload'); // json string
        $quoteSummary = (string) $request->input('quote_summary', 'Preventivo');

        // ANONIMO
        if ($mode === 'anonymous') {
            $request->validate([
                'privacy_accepted' => ['accepted'],
                'quote_summary' => ['required', 'string'],
                'quote_payload' => ['nullable'],
            ]);

            InboxConversation::create([
                'source'              => 'quote',
                'name'                => 'Preventivo anonimo',
                'email'               => 'anonimo@stacknigro.it',
                'phone'               => null,
                'subject'             => 'Preventivo anonimo',
                'how_found'           => null,
                'user_message'        => $quoteSummary,     // qui mettiamo il riepilogo + totale
                'privacy_accepted'    => 1,
                'privacy_accepted_at' => now(),
                'quote_payload'       => $quotePayload,
                'ip_address'          => $request->ip(),
                'user_agent'          => substr((string) $request->userAgent(), 0, 1000),
            ]);

            return back()->with('success', 'Preventivo calcolato in modo anonimo nessun dato è stato registrato.');
        }

        // INVIO CON DATI (form)
        // QUI re-usi i campi della tua tabella inbox_conversations
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:120'],
            'email'            => ['required', 'email', 'max:190'],
            'phone'            => ['nullable', 'string', 'max:50'],
            'subject'          => ['nullable', 'string', 'max:180'],
            'how_found'        => ['nullable', 'string', 'max:20'],
            'user_message'     => ['nullable', 'string'], // nel partial magari è required, qui lo lascio soft
            'privacy_accepted' => ['accepted'],
            'quote_payload'    => ['nullable'],
            'quote_summary'    => ['nullable', 'string'],
        ]);

        // user_message finale: se l’utente scrive qualcosa, lo teniamo + appendiamo riepilogo preventivo
        $finalMessage = trim((string)($data['user_message'] ?? ''));
        if ($finalMessage !== '') {
            $finalMessage .= "\n\n---\n" . $quoteSummary;
        } else {
            $finalMessage = $quoteSummary;
        }

        InboxConversation::create([
            'source'              => 'quote',
            'name'                => $data['name'],
            'email'               => $data['email'],
            'phone'               => $data['phone'] ?? null,
            'subject'             => $data['subject'] ?? 'Richiesta preventivo',
            'how_found'           => $data['how_found'] ?? null,
            'user_message'        => $finalMessage,
            'privacy_accepted'    => 1,
            'privacy_accepted_at' => now(),
            'quote_payload'       => $quotePayload,
            'ip_address'          => $request->ip(),
            'user_agent'          => substr((string) $request->userAgent(), 0, 1000),
        ]);

        return back()->with('success', 'Preventivo inviato e salvato riceverai una risposta al più presto.');
    }
}
