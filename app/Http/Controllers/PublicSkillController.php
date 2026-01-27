<?php

namespace App\Http\Controllers;

use App\Models\Skill;

class PublicSkillController extends Controller
{
    // HOME → 4 random
    public function home()
    {
        $homeSkills = Skill::inRandomOrder()
            ->limit(4)
            ->get();

        return view('home', compact('homeSkills'));
    }

    // PAGINA SKILLS → tutte alfabetiche
    public function index()
    {
        $skills = Skill::orderBy('name')->get();

        return view('public.skills', compact('skills'));
    }
}
