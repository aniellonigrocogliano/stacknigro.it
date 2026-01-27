<?php

namespace App\Http\Controllers;

use App\Models\LegalPage;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class LegalAdminController extends Controller
{
    public function edit()
    {
        $site = SiteSetting::firstOrCreate([]);

        $privacy = LegalPage::firstOrCreate(
            ['type' => 'privacy'],
            ['title' => 'Privacy Policy', 'content' => null]
        );

        $cookie = LegalPage::firstOrCreate(
            ['type' => 'cookie'],
            ['title' => 'Cookie Policy', 'content' => null]
        );

        return view('admin.legal.edit', compact('site', 'privacy', 'cookie'));
    }

    public function update(Request $request)
    {
        $site = SiteSetting::firstOrCreate([]);

        $data = $request->validate([
            // legal_pages
            'privacy_title' => ['nullable','string','max:160'],
            'privacy_content' => ['nullable','string'],
            'cookie_title' => ['nullable','string','max:160'],
            'cookie_content' => ['nullable','string'],

            // banner cookie (site_settings)
            'cookie_banner_enabled' => ['nullable'],
            'cookie_consent_days' => ['required','integer','min:1','max:3650'],
            'cookie_banner_html' => ['nullable','string'],

            // analytics (site_settings)
            'analytics_provider' => ['nullable','string','max:30'],
            'analytics_measurement_id' => ['nullable','string','max:50'],
        ]);

        // upsert legal pages
        LegalPage::updateOrCreate(
            ['type' => 'privacy'],
            [
                'title' => $data['privacy_title'] ?: 'Privacy Policy',
                'content' => $data['privacy_content'] ?? null,
            ]
        );

        LegalPage::updateOrCreate(
            ['type' => 'cookie'],
            [
                'title' => $data['cookie_title'] ?: 'Cookie Policy',
                'content' => $data['cookie_content'] ?? null,
            ]
        );

        // site settings
        $site->cookie_banner_enabled = $request->boolean('cookie_banner_enabled');
        $site->cookie_consent_days = (int) $data['cookie_consent_days'];
        $site->cookie_banner_html = $data['cookie_banner_html'] ?? null;

        $site->analytics_provider = $data['analytics_provider'] ?: null;
        $site->analytics_measurement_id = $data['analytics_measurement_id'] ?: null;

        $site->save();

        return back()->with('success', 'Policy / Cookie / Banner / Analytics aggiornati.');
    }
}

