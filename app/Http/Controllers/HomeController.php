<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\Contact;
use App\Models\Project;

class HomeController extends Controller
{
    public function index()
    {
        $site = SiteSetting::first();

        // BIO
        $bioRaw = $site?->bio ?? '';
        $parts = preg_split('/<!--\s*more\s*-->/', $bioRaw, 2);
        $bioExcerpt = $parts[0] ?? '';
        $hasMore = count($parts) > 1;

        // Skills home (4 random)
        $homeSkills = Skill::inRandomOrder()->limit(4)->get();

        // Contatti
        $contacts = Contact::orderBy('sort')->get();

        // Progetti home (3 random) + immagini (per mostrare cover in card)
        $homeProjects = Project::where('is_published', 1)
            ->with(['images' => fn ($q) => $q->orderByDesc('is_cover')->orderBy('sort_order')->orderBy('id')])
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('public.home', compact(
            'site',
            'bioExcerpt',
            'hasMore',
            'homeSkills',
            'contacts',
            'homeProjects'
        ));
    }
}

