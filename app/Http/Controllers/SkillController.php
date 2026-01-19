<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::orderBy('sort')->orderBy('id')->get();
        return view('admin.skills.index', compact('skills'));
    }

    public function create()
    {
        return view('admin.skills.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'color' => ['nullable','string','max:30'],
            'fa_icon' => ['nullable','string','max:80'],
            'description' => ['nullable','string'],
            'sort' => ['nullable','integer','min:0'],
        ]);

        $data['sort'] = $data['sort'] ?? 0;

        Skill::create($data);

        return redirect()->route('admin.skills.index')->with('success', 'Skill creata.');
    }

    public function edit(Skill $skill)
    {
        return view('admin.skills.edit', compact('skill'));
    }

    public function update(Request $request, Skill $skill)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'color' => ['nullable','string','max:30'],
            'fa_icon' => ['nullable','string','max:80'],
            'description' => ['nullable','string'],
            'sort' => ['nullable','integer','min:0'],
        ]);

        $data['sort'] = $data['sort'] ?? 0;

        $skill->update($data);

        return redirect()->route('admin.skills.index')->with('success', 'Skill aggiornata.');
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();
        return redirect()->route('admin.skills.index')->with('success', 'Skill eliminata.');
    }
}
