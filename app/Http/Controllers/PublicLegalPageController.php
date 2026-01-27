<?php

namespace App\Http\Controllers;

use App\Models\LegalPage;

class PublicLegalPageController extends Controller
{
    public function privacyPolicy()
    {
        // record già esistenti in legal_pages
        // type: 'privacy' | 'cookies'
        $privacy = LegalPage::where('type', 'privacy')->first();
        $cookies = LegalPage::where('type', 'cookie')->first();

        return view('public.privacy-policy', compact('privacy', 'cookies'));
    }
}
