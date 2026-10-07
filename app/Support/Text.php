<?php

namespace App\Support;

use App\Models\Club;
use Illuminate\Support\Str;

/** Mise en forme des libellés FFVolley (fournis en MAJUSCULES sans accents). */
class Text
{
    private const SMALL_WORDS = ['de', 'du', 'des', 'la', 'le', 'les', 'et', 'en', 'au', 'aux', 'à', 'sur'];

    private const KEEP_UPPER = ['VB', 'VBC', 'MUC', 'AS', 'ASC', 'RC', 'RCA', 'FFVB', 'LMVB', 'UAG', 'IMS', 'CTPVB', 'FEP', 'II', 'III'];

    private const ACCENTS = [
        'feminin' => 'féminin', 'feminine' => 'féminine', 'feminines' => 'féminines',
        'universite' => 'université', 'riviere' => 'rivière', 'arlesien' => 'arlésien',
        'saleen' => 'saléen', 'elite' => 'élite', 'equipe' => 'équipe', 'evenement' => 'événement',
        'pole' => 'pôle', 'monesie' => 'monésie', 'reguliere' => 'régulière',
        'lycee' => 'lycée', 'salee' => 'salée', 'trinite' => 'trinité',
        'schoelcher' => 'schœlcher', 'eclair' => 'éclair', 'garcons' => 'garçons', 'dowm' => 'down',
    ];

    public static function title(?string $value): string
    {
        if ($value === null || trim($value) === '') {
            return '';
        }

        $parts = preg_split('/(\s+|-|\/)/u', trim($value), -1, PREG_SPLIT_DELIM_CAPTURE);
        $out = [];
        foreach ($parts as $i => $word) {
            if ($word === '' || preg_match('/^(\s+|-|\/)$/u', $word)) {
                $out[] = $word;
                continue;
            }
            $upper = Str::upper($word);
            if (in_array($upper, self::KEEP_UPPER, true) || preg_match('/^[A-Z]{1,2}\d+$/', $upper)) {
                $out[] = $upper;
                continue;
            }
            // Sigles à points : A.S.C. → ASC
            if (preg_match('/^([A-Za-z]\.){2,}[A-Za-z]?\.?$/', $word)) {
                $upper = str_replace('.', '', $upper);
                $out[] = $upper;
                continue;
            }
            $lower = Str::lower($word);
            $lower = self::ACCENTS[$lower] ?? $lower;
            // Élision : « D'ARLET » devient « d'Arlet »
            if (preg_match("/^([dlt])'(.+)$/u", $lower, $m)) {
                $rest = self::ACCENTS[$m[2]] ?? $m[2];
                $out[] = ($i === 0 ? Str::upper($m[1]) : $m[1])."'".Str::ucfirst($rest);
                continue;
            }
            $out[] = ($i > 0 && in_array($lower, self::SMALL_WORDS, true)) ? $lower : Str::ucfirst($lower);
        }

        return implode('', $out);
    }

    /** Nom d'équipe à afficher : nom du club (+ numéro d'équipe éventuel) ou libellé FFVolley mis en forme. */
    public static function teamName(?string $raw, ?Club $club = null): string
    {
        $raw = trim((string) $raw);
        if ($club) {
            return preg_match('/\s(\d)$/', $raw, $m) ? $club->name.' '.$m[1] : $club->name;
        }

        return self::title($raw);
    }

    /** Initiales pour les pastilles de club sans logo. */
    public static function initials(string $name): string
    {
        $words = array_values(array_filter(
            preg_split('/[\s\-\']+/u', $name),
            fn ($w) => mb_strlen($w) > 2 || (mb_strlen($w) > 0 && mb_strtoupper($w) === $w)
        ));

        return mb_strtoupper(mb_substr(implode('', array_map(fn ($w) => mb_substr($w, 0, 1), array_slice($words, 0, 3))), 0, 3));
    }
}
