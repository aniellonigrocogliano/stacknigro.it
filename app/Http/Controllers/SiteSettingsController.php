<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SiteSettingsController extends Controller
{
    private function settings(): SiteSetting
    {
        return SiteSetting::firstOrCreate([]);
    }

    public function edit()
    {
        $settings = $this->settings();
        return view('admin.hero', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = $this->settings();

        $data = $request->validate([
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        $settings->hero_title = $data['hero_title'] ?? $settings->hero_title;
        $settings->hero_subtitle = $data['hero_subtitle'] ?? $settings->hero_subtitle;

        if ($request->hasFile('logo')) {

            // elimina vecchi
            if ($settings->logo_path) Storage::disk('public')->delete($settings->logo_path);
            if ($settings->favicon_path) Storage::disk('public')->delete($settings->favicon_path);

            // salva logo originale
            $logoPath = $request->file('logo')->store('site', 'public');

            // genera favicon 32x32 PNG
            $faviconRelPath = 'site/favicon-32.png';
            $faviconAbsPath = storage_path('app/public/' . $faviconRelPath);
$manager = new ImageManager(new Driver());

$manager->read(Storage::disk('public')->path($logoPath))
    ->resize(32, 32)
    ->save($faviconAbsPath);
            $settings->logo_path = $logoPath;
            $settings->favicon_path = $faviconRelPath;
        }

        $settings->save();

        return back()->with('success', 'Hero e logo aggiornati!');
    }
}

