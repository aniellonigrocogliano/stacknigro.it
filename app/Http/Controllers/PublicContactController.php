<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\InboxConversation;
use Illuminate\Http\Request;

class PublicContactController extends Controller
{
    public function create()
    {
        // SOLO AGGIUNTA: contatti (email/pec/telefono ecc.) da tabella contacts
        $contacts = Contact::query()
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        return view('public.contacts', compact('contacts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:120'],
            'email'           => ['required', 'email', 'max:190'],
            'phone'           => ['nullable', 'string', 'max:50'],
            'subject'         => ['nullable', 'string', 'max:180'],
            'how_found'       => ['nullable', 'string', 'max:20'],
            'user_message'    => ['required', 'string'],
            'privacy_accepted'=> ['accepted'], // deve essere 1
        ]);

        InboxConversation::create([
            'source'              => $request->input('source', 'contact'), // "contact" default
            'name'                => $data['name'],
            'email'               => $data['email'],
            'phone'               => $data['phone'] ?? null,
            'subject'             => $data['subject'] ?? null,
            'how_found'           => $data['how_found'] ?? null,
            'user_message'        => $data['user_message'],
            'privacy_accepted'    => 1,
            'privacy_accepted_at' => now(),
            'quote_payload'       => $request->input('quote_payload'), // se lo riusi nei preventivi
            'ip_address'          => $request->ip(),
            'user_agent'          => substr((string) $request->userAgent(), 0, 1000),
        ]);

        return back()->with('success', 'Messaggio inviato! Ti risponderò al più presto.');
    }
}
