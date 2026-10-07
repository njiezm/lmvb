<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function edit()
    {
        $values = collect(Setting::DEFAULTS)->mapWithKeys(fn ($default, $key) => [$key => Setting::get($key, '')]);

        return view('admin.settings', compact('values'));
    }

    public function update(Request $request, ImageUploader $images)
    {
        $data = $request->validate([
            'site_name' => 'required|string|max:150',
            'site_tagline' => 'nullable|string|max:300',
            'president_name' => 'nullable|string|max:120',
            'president_title' => 'nullable|string|max:150',
            'president_message' => 'nullable|string|max:5000',
            'president_photo_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:6144',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'required|email|max:150',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'hero_title' => 'nullable|string|max:120',
            'hero_subtitle' => 'nullable|string|max:300',
            'license_url' => 'nullable|url|max:255',
        ], [], ['president_photo_file' => 'photo de la présidente']);

        unset($data['president_photo_file']);
        $currentPhoto = Setting::get('president_photo', '') ?: null;
        if ($request->hasFile('president_photo_file')) {
            $data['president_photo'] = $images->replace($request->file('president_photo_file'), $currentPhoto, 'board', 900);
        } elseif ($request->boolean('remove_president_photo')) {
            $images->delete($currentPhoto);
            $data['president_photo'] = '';
        }

        Setting::put(array_map(fn ($v) => $v ?? '', $data));
        Cache::forget('home.data');

        return back()->with('success', 'Réglages enregistrés.');
    }
}
