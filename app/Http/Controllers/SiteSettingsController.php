<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\SiteSetting;

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
    $settings = SiteSetting::firstOrCreate([]);

    $data = $request->validate([
        'hero_title' => ['nullable', 'string', 'max:255'],
        'hero_subtitle' => ['nullable', 'string', 'max:255'],
        'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
    ]);

    $settings->hero_title = $data['hero_title'] ?? null;
    $settings->hero_subtitle = $data['hero_subtitle'] ?? null;

    if ($request->hasFile('logo')) {
        // 1) salva logo
        $logoPath = $request->file('logo')->store('site', 'public'); // site/xxxx.png

        // 2) genera favicon 32x32 PNG
        $faviconRelPath = 'site/favicon-32.png';
        $faviconAbsPath = storage_path('app/public/' . $faviconRelPath);

        $manager = new ImageManager(new Driver());
        $img = $manager->read(Storage::disk('public')->path($logoPath));

        // Nota: toPng() = sempre favicon png
        $img->resize(32, 32)->toPng()->save($faviconAbsPath);

        // 3) salva path nel DB
        $settings->logo_path = $logoPath;
        $settings->favicon_path = $faviconRelPath;
    }

    $settings->save();

    return back()->with('success', 'Salvato!');
}
public function editBio()
{
    $settings = SiteSetting::firstOrCreate([]);
    return view('admin.bio', compact('settings'));
}

public function updateBio(Request $request)
{
    $settings = SiteSetting::firstOrCreate([]);

    $data = $request->validate([
        'bio' => ['nullable', 'string'],
    ]);

    $settings->bio = $data['bio'] ?? null;
    $settings->save();

    return back()->with('success', 'Bio salvata!');
}
}
