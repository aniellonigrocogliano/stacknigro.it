<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bio;

class BioController extends Controller
{
    public function index()
    {
        $bio = Bio::first();
        return view('backend.bio.index', compact('bio'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string',
        ]);

        $bio = Bio::firstOrNew([]);
        $bio->content = $request->input('content');
        $bio->save();

        return redirect()->back()->with('success', 'Bio salvata con successo!');
    }
}