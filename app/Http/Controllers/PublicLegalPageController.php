<?php

namespace App\Http\Controllers;

use App\Models\LegalPage;

class PublicLegalPageController extends Controller
{
    public function privacyPolicy()
    {
        // type: 'privacy' | 'cookie' | 'captcha'
        $privacy = LegalPage::where('type', 'privacy')->first();
        $cookies = LegalPage::where('type', 'cookie')->first();
        $captcha = LegalPage::where('type', 'captcha')->first();

        return view('public.privacy-policy', compact('privacy', 'cookies', 'captcha'));
    }
}
