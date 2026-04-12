<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\InboxConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PublicContactController extends Controller
{
    public function create()
    {
        $contacts = Contact::query()
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        return view('public.contacts', compact('contacts'));
    }

    public function store(Request $request)
    {
        // 1) Validazione input + captcha (token + session)
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:120'],
            'email'            => ['required', 'email', 'max:190'],
            'phone'            => ['nullable', 'string', 'max:50'],
            'subject'          => ['nullable', 'string', 'max:180'],
            'how_found'        => ['nullable', 'string', 'max:20'],
            'user_message'     => ['required', 'string'],
            'privacy_accepted' => ['accepted'],

            // Stacknigro CAPTCHA
            'snct'             => ['required', 'string'], // token
            'sncs'             => ['required', 'string'], // session_id
        ], [
            'snct.required' => 'Per favore, completa la verifica di sicurezza.',
            'sncs.required' => 'Sessione di sicurezza mancante. Ricarica la pagina e riprova.',
        ]);

        // 2) Config
        $secret = (string) config('services.sn_captcha.secret');
        $verifyUrl = (string) config('services.sn_captcha.verify_url', 'https://captcha.stacknigro.it/api/siteverify.php');

        if ($secret === '') {
            return back()
                ->withInput()
                ->withErrors(['snct' => 'Errore di configurazione (Secret Key mancante).']);
        }

        // 3) Verifica token server-to-server
        try {
            $payload = [
                'secret'     => $secret,
                'token'      => $data['snct'],
                'session_id' => $data['sncs'],
                'ip'         => $request->ip(),
            ];

            $resp = Http::timeout(10)->asForm()->post($verifyUrl, $payload);

            // Decodifica JSON robusta (senza usare $resp->json() che può fallire)
            $rawBody = (string) $resp->body();
            $json = json_decode($rawBody, true);
            $jsonOk = is_array($json);

            if (!$resp->ok() || !$jsonOk) {
                Log::warning('SN CAPTCHA verify failed (non-JSON or HTTP fail)', [
                    'status' => $resp->status(),
                    'session_id' => $data['sncs'],
                    'token_prefix' => substr($data['snct'], 0, 8) . '…',
                ]);

                return back()
                    ->withInput()
                    ->withErrors(['snct' => 'Verifica di sicurezza non superata. Riprova.']);
            }

            if (data_get($json, 'success') !== true) {
                Log::warning('SN CAPTCHA verify failed (success=false)', [
                    'status' => $resp->status(),
                    'session_id' => $data['sncs'],
                    'token_prefix' => substr($data['snct'], 0, 8) . '…',
                    'error' => data_get($json, 'error'),
                ]);

                return back()
                    ->withInput()
                    ->withErrors(['snct' => 'Verifica di sicurezza non superata. Riprova.']);
            }
        } catch (\Throwable $e) {
            Log::error('SN CAPTCHA siteverify exception', [
                'message' => $e->getMessage(),
                'class' => get_class($e),
            ]);

            return back()
                ->withInput()
                ->withErrors(['snct' => 'Servizio di verifica momentaneamente non disponibile.']);
        }

        // 4) Se CAPTCHA valido => salva inbox
        InboxConversation::create([
            'source'              => $request->input('source', 'contact'),
            'name'                => $data['name'],
            'email'               => $data['email'],
            'phone'               => $data['phone'] ?? null,
            'subject'             => $data['subject'] ?? null,
            'how_found'           => $data['how_found'] ?? null,
            'user_message'        => $data['user_message'],
            'privacy_accepted'    => 1,
            'privacy_accepted_at' => now(),
            'quote_payload'       => $request->input('quote_payload'),
            'quote_summary'       => $request->input('quote_summary'),
            'ip_address'          => $request->ip(),
            'user_agent'          => substr((string) $request->userAgent(), 0, 1000),
        ]);

        return back()->with('success', 'Messaggio inviato! Ti risponderò al più presto.');
    }
}
