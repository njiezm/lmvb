<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /** Valeur d'un réglage du site (Admin > Réglages), avec valeur par défaut. */
    function setting(string $key, mixed $default = null): mixed
    {
        try {
            return Setting::get($key, $default);
        } catch (\Throwable) {
            return $default ?? Setting::DEFAULTS[$key] ?? null;
        }
    }
}

if (! function_exists('safe_html')) {
    /** HTML saisi dans l'admin, filtré (balises de mise en forme uniquement, pas de scripts). */
    function safe_html(?string $html): string
    {
        return clean((string) $html, 'lmvb');
    }
}
