<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\Skill;

class HomeController extends Controller
{
    public function index()
    {
        $site = SiteSetting::first();

        // BIO: split su <!--more-->
        $bioRaw = $site?->bio ?? '';
        $parts = preg_split('/<!--\s*more\s*-->/', $bioRaw, 2);

        $bioExcerpt = $parts[0] ?? '';
        $hasMore = count($parts) > 1;

        // Skills home
        $homeSkills = Skill::inRandomOrder()->limit(4)->get();

        return view('public.home', compact(
            'site',
            'bioExcerpt',
            'hasMore',
            'homeSkills'
        ));
    }
}
