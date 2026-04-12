<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\SiteSetting;
use App\Models\Skill;

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

    $disk = Storage::disk('public');
    $dir  = 'site';

    $logoRelPath    = "$dir/logo.webp";
    $faviconRelPath = "$dir/favicon-32.png";

    // elimina vecchi file (nomi fissi)
    $disk->delete([$logoRelPath, $faviconRelPath]);

    $manager = new ImageManager(new Driver());
    $img = $manager->read($request->file('logo')->getRealPath());

    // LOGO: WebP con trasparenza (se presente nel file sorgente)
    // (niente flatten/background!)
    $logoBytes = (string) $img
        ->scaleDown(width: 600)   // scegli tu la width
        ->toWebp(85);             // qualità

    $disk->put($logoRelPath, $logoBytes);

    // FAVICON: meglio PNG, e qui puoi fare crop senza deformare
    $faviconBytes = (string) $img
        ->cover(32, 32)
        ->toPng();

    $disk->put($faviconRelPath, $faviconBytes);

    $settings->logo_path = $logoRelPath;       // site/logo.webp
    $settings->favicon_path = $faviconRelPath; // site/favicon-32.png
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
public function tinymceUpload(Request $request)
{
    $request->validate([
        'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'], // 5MB
    ]);

    $path = $request->file('file')->store('tinymce', 'public');

    return response()->json([
        'location' => asset('storage/' . $path),
    ]);
}
}
