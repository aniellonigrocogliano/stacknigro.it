<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::orderBy('sort')->orderBy('id')->get();
        return view('admin.contacts.index', compact('contacts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:80'],
            'fa_icon' => ['nullable','string','max:80'],
            'value' => ['required','string','max:190'],
        ]);

        // sort automatico in coda
        $data['sort'] = (int) Contact::max('sort') + 1;

        Contact::create($data);

        return back()->with('success', 'Contatto aggiunto.');
    }

    public function update(Request $request, Contact $contact)
    {
        $data = $request->validate([
            'name' => ['required','string','max:80'],
            'fa_icon' => ['nullable','string','max:80'],
            'value' => ['required','string','max:190'],
        ]);

        $contact->update($data);

        return back()->with('success', 'Contatto aggiornato.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return back()->with('success', 'Contatto eliminato.');
    }
}
