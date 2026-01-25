<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;

class LegalAdminController extends Controller
{
    public function edit()
    {
        // 1 sola riga di settings
        $settings = SiteSetting::query()->firstOrFail();

        return view('admin.legal.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = SiteSetting::query()->firstOrFail();

        $validated = $request->validate([
            // testi lunghi da TinyMCE
            'privacy_policy' => ['nullable', 'string'],
            'cookie_policy'  => ['nullable', 'string'],

            // banner cookie
            'cookie_banner_enabled'   => ['nullable', 'boolean'],
            'cookie_banner_text'      => ['nullable', 'string'],
            'cookie_accept_text'      => ['nullable', 'string', 'max:80'],
            'cookie_reject_text'      => ['nullable', 'string', 'max:80'],
            'cookie_more_info_text'   => ['nullable', 'string', 'max:120'],
        ]);

        // checkbox => 0/1
        $validated['cookie_banner_enabled'] = $request->boolean('cookie_banner_enabled');

        // aggiorna timestamp policy (utile per "ultima modifica" sul frontend)
        $validated['privacy_updated_at'] = now();

        $settings->update($validated);

        return back()->with('success', 'Policy e banner aggiornati.');
    }
}
