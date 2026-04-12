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
            'name' => ['required', 'string', 'max:80'],
            'fa_icon' => ['nullable', 'string', 'max:80'],
            'value' => ['required', 'string', 'max:190'],
            'href' => ['nullable', 'string', 'max:255'],
            'target_blank' => ['nullable', 'boolean'],
        ]);

        // checkbox: se non arriva → 0
        $data['target_blank'] = $request->boolean('target_blank');

        // sort automatico in coda
        $data['sort'] = (int) Contact::max('sort') + 1;

        Contact::create($data);

        return back()->with('success', 'Contatto aggiunto.');
    }

    public function update(Request $request, Contact $contact)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'fa_icon' => ['nullable', 'string', 'max:80'],
            'value' => ['required', 'string', 'max:190'],
            'href' => ['nullable', 'string', 'max:255'],
            'target_blank' => ['nullable', 'boolean'],
        ]);

        // checkbox: se non arriva → 0
        $data['target_blank'] = $request->boolean('target_blank');

        $contact->update($data);

        return back()->with('success', 'Contatto aggiornato.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return back()->with('success', 'Contatto eliminato.');
    }

    public function reorder(Request $request)
{
    $data = $request->validate([
        'ids' => ['required', 'array'],
        'ids.*' => ['integer', 'exists:contacts,id'],
    ]);

    foreach ($data['ids'] as $index => $id) {
        Contact::where('id', $id)->update(['sort' => $index + 1]);
    }

    return response()->json(['ok' => true]);
}

}
