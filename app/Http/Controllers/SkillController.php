<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Skill;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::all(); // Recupera tutte le skill
        return view('backend.skills.index', compact('skills')); // Passa $skills alla view
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:20',
            'icon' => 'required|string|max:255',
        ]);

        Skill::create($request->only('name', 'color', 'icon'));

        return redirect()->back()->with('success', 'Skill aggiunta con successo.');
    }

    public function destroy($id)
    {
        $skill = Skill::findOrFail($id);
        $skill->delete();

        return redirect()->route('admin.skills.index')->with('success', 'Skill eliminata con successo!');
    }
}
