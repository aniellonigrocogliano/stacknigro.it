<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HeroSetting;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class HeroController extends Controller
{
    public function index()
    {
        $hero = HeroSetting::first();
        return view('backend.hero.index', compact('hero'));
    }

    public function update(Request $request)
    {
        $hero = HeroSetting::firstOrNew([]);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('uploads/hero'), $logoName);
            $hero->logo = 'uploads/hero/' . $logoName;

            $faviconName = 'favicon_' . time() . '.png';
            $faviconPath = public_path('uploads/hero/' . $faviconName);

            $manager = new ImageManager(new Driver());
            $image = $manager->read(public_path('uploads/hero/' . $logoName));
            $image->scale(width: 64, height: 64);
            $image->save($faviconPath);

            $hero->favicon = 'uploads/hero/' . $faviconName;
        }

        $hero->hero_title = $request->hero_title;
        $hero->hero_name = $request->hero_name;
        $hero->hero_subtitle = $request->hero_subtitle;

        if ($request->hasFile('hero_image')) {
            $image = $request->file('hero_image');
            $imageName = 'hero_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/hero'), $imageName);
            $hero->hero_image = 'uploads/hero/' . $imageName;
        }

        $hero->save();

        return redirect()->back()->with('success', 'Impostazioni salvate con successo!');
    }
}
