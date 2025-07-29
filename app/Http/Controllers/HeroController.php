<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HeroSetting;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class HeroController extends Controller
{
    public function index()
    {
        $hero = HeroSetting::first();
        return view('backend.hero.index', compact('hero'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp',
            'hero_image' => 'nullable|image|mimes:jpeg,jpg,png,webp',
        ]);

        $hero = HeroSetting::firstOrNew([]);
        $uploadPath = public_path('uploads/hero');

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Driver per Intervention v3
        $driver = extension_loaded('imagick') ? new ImagickDriver() : new GdDriver();
        $manager = new ImageManager($driver);

        // LOGO & FAVICON
        if ($request->hasFile('logo')) {
            // Elimina logo e favicon precedenti
            if ($hero->logo && file_exists(public_path($hero->logo))) {
                unlink(public_path($hero->logo));
            }
            if ($hero->favicon && file_exists(public_path($hero->favicon))) {
                unlink(public_path($hero->favicon));
            }

            $logo = $request->file('logo');
            $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $logo->move($uploadPath, $logoName);
            $hero->logo = 'uploads/hero/' . $logoName;

            // Favicon
            $faviconName = 'favicon_' . time() . '.png';
            $faviconPath = $uploadPath . '/' . $faviconName;

            $image = $manager->read($uploadPath . '/' . $logoName);
            $image->scale(width: 64, height: 64)->save($faviconPath);

            $hero->favicon = 'uploads/hero/' . $faviconName;
        }

        // HERO IMAGE
        if ($request->hasFile('hero_image')) {
            if ($hero->hero_image && file_exists(public_path($hero->hero_image))) {
                unlink(public_path($hero->hero_image));
            }

            $image = $request->file('hero_image');
            $imageName = 'hero_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move($uploadPath, $imageName);
            $hero->hero_image = 'uploads/hero/' . $imageName;
        }

        $hero->hero_title = $request->hero_title;
        $hero->hero_name = $request->hero_name;
        $hero->hero_subtitle = $request->hero_subtitle;
        $hero->save();

        return redirect()->back()->with('success', 'Impostazioni salvate con successo!');
    }
}
