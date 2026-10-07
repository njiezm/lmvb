<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/** Réglages du site (clé / valeur), mis en cache. Utiliser le helper setting('cle'). */
class Setting extends Model
{
    public const DEFAULTS = [
        'site_name' => 'Ligue Martiniquaise de Volley-Ball',
        'site_tagline' => 'Le volley-ball en Martinique : championnats, clubs, sélections et beach-volley.',
        'president_name' => 'Maëva Antiste',
        'president_title' => 'Présidente de la Ligue Martiniquaise de Volley-Ball',
        'president_photo' => '',
        'president_message' => '',
        'address' => 'Maison des Sports, Pointe de la Vierge, rue du Petit Pavois, 97200 Fort-de-France',
        'phone' => '0596 55 66 72',
        'email' => 'secretariatvolleymque@gmail.com',
        'facebook_url' => 'https://www.facebook.com/LMVB972/',
        'instagram_url' => '',
        'youtube_url' => '',
        'hero_title' => 'Le volley vit en Martinique',
        'hero_subtitle' => 'Résultats en direct de la FFVolley, classements, clubs, sélections et beach-volley : toute la ligue au même endroit.',
        'license_url' => 'https://www.ffvolley.org/',
    ];

    protected $fillable = ['key', 'value', 'type'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = Cache::rememberForever('settings.all', fn () => static::query()->pluck('value', 'key')->all());

        $value = $all[$key] ?? null;

        return ($value === null || $value === '') ? ($default ?? self::DEFAULTS[$key] ?? null) : $value;
    }

    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        }
        Cache::forget('settings.all');
    }
}
