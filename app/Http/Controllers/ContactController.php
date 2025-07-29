<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        $contact = Contact::first();
        return view('backend.contacts.index', compact('contact'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'pec' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
        ]);

        $contact = Contact::firstOrNew([]);
        $contact->email = $request->email;
        $contact->pec = $request->pec;
        $contact->phone = $request->phone;
        $contact->save();

        return redirect()->back()->with('success', 'Recapiti aggiornati con successo!');
    }
}