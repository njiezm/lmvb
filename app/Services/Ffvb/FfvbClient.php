<?php

namespace App\Services\Ffvb;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Lecture des données publiques de la plateforme FFVolley (ffvbbeach.org).
 *
 * - Liste des poules : page d'accueil de l'entité (vbspo_home.php)
 * - Matchs : export CSV officiel (vbspo_calendrier_export.php, typ_edition=E)
 * - Classement : tableau de la page calendrier (vbspo_calendrier.php)
 *
 * Les pages sont encodées en Windows-1252.
 */
class FfvbClient
{
    public function __construct(
        private ?string $baseUrl = null,
        private ?string $entity = null,
    ) {
        $this->baseUrl ??= config('lmvb.ffvb.base_url');
        $this->entity ??= config('lmvb.ffvb.entity');
    }

    public function entity(): string
    {
        return $this->entity;
    }

    /**
     * @return array<int, array{code: string, name: string, group: ?string}>
     */
    public function competitions(string $season): array
    {
        $html = $this->get('vbspo_home.php', ['saison' => $season, 'codent' => $this->entity]);

        return self::parseCompetitions($html);
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function matches(string $season, string $poule): array
    {
        $csv = $this->post('vbspo_calendrier_export.php', [
            'cal_saison' => $season,
            'cal_codent' => $this->entity,
            'cal_codpoule' => $poule,
            'cal_coddiv' => '',
            'cal_codtour' => '',
            'typ_edition' => 'E',
            'type' => 'RES',
            'rech_equipe' => '',
        ]);

        return self::parseMatchesCsv($csv);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function standings(string $season, string $poule): array
    {
        $html = $this->get('vbspo_calendrier.php', ['saison' => $season, 'codent' => $this->entity, 'poule' => $poule]);

        return self::parseStandings($html);
    }

    // ---------------------------------------------------------------------
    // Parsing (statique pour être testable sans réseau)
    // ---------------------------------------------------------------------

    public static function parseCompetitions(string $html): array
    {
        preg_match_all(
            "~<a href=\"#\">([^<]+)|<a href='vbspo_calendrier\.php\?[^']*poule=([A-Z0-9]+)'[^>]*>([^<]+)~u",
            $html,
            $matches,
            PREG_SET_ORDER
        );

        $group = null;
        $list = [];
        foreach ($matches as $m) {
            if (! empty($m[1])) {
                $label = trim(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8'));
                $group = in_array(mb_strtolower($label), ['compétitions', 'competitions', 'adressiers', 'contacts'], true) ? null : $label;
                continue;
            }
            $code = $m[2];
            $name = trim(html_entity_decode($m[3], ENT_QUOTES, 'UTF-8'));
            $name = trim(preg_replace('/^'.preg_quote($code, '/').'\s+/', '', $name));
            $list[$code] = ['code' => $code, 'name' => $name, 'group' => $group];
        }

        return array_values($list);
    }

    public static function parseMatchesCsv(string $csv): array
    {
        $lines = preg_split('/\r\n|\n|\r/', trim($csv));
        if (! $lines || ! str_starts_with($lines[0], 'Entit')) {
            throw new RuntimeException('Export CSV FFVolley inattendu.');
        }
        $header = array_map('trim', str_getcsv(array_shift($lines), ';', '"', ''));

        $rows = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            $values = array_map('trim', str_getcsv($line, ';', '"', ''));
            $row = [];
            foreach ($header as $i => $key) {
                if ($key !== '') {
                    $row[$key] = $values[$i] ?? '';
                }
            }
            $rows[] = $row;
        }

        return $rows;
    }

    public static function parseStandings(string $html): array
    {
        $doc = new DOMDocument;
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8"?>'.$html);
        libxml_clear_errors();
        $xpath = new DOMXPath($doc);

        $rows = [];
        foreach ($xpath->query('//tr') as $tr) {
            $cells = [];
            $link = null;
            foreach ($xpath->query('./td', $tr) as $td) {
                $cells[] = trim(preg_replace('/\s+/u', ' ', str_replace("\u{00A0}", ' ', $td->textContent)));
                $a = $td->getElementsByTagName('a');
                if ($a->length && ! $link) {
                    $link = trim($a->item(0)->getAttribute('href'));
                }
            }
            if (count($cells) < 19 || ! preg_match('/^(\d+)\.$/', $cells[0], $pos)) {
                continue;
            }
            $int = fn ($v) => $v === '' ? 0 : (int) $v;
            $rows[] = [
                'position' => (int) $pos[1],
                'team_name' => $cells[1],
                'points' => $int($cells[2]),
                'played' => $int($cells[3]),
                'won' => $int($cells[4]),
                'lost' => $int($cells[5]),
                'forfeits' => $int($cells[6]),
                'w30' => $int($cells[7]),
                'w31' => $int($cells[8]),
                'w32' => $int($cells[9]),
                'l23' => $int($cells[10]),
                'l13' => $int($cells[11]),
                'l03' => $int($cells[12]),
                'sets_for' => $int($cells[13]),
                'sets_against' => $int($cells[14]),
                'points_for' => $int($cells[16]),
                'points_against' => $int($cells[17]),
                // Certaines équipes renseignent leur site / réseaux dans ce lien.
                'link' => $link && preg_match('~^https?://~i', $link) ? $link : null,
            ];
        }

        return $rows;
    }

    // ---------------------------------------------------------------------

    private function get(string $path, array $query): string
    {
        $response = $this->http()->get($this->baseUrl.'/'.$path, $query);

        return $this->body($response, $path);
    }

    private function post(string $path, array $form): string
    {
        $response = $this->http()->asForm()->post($this->baseUrl.'/'.$path, $form);

        return $this->body($response, $path);
    }

    private function http()
    {
        return Http::withHeaders(['User-Agent' => config('lmvb.ffvb.user_agent')])
            ->timeout(config('lmvb.ffvb.timeout', 20))
            ->retry(2, 1500, throw: false);
    }

    private function body($response, string $path): string
    {
        if (! $response->successful()) {
            throw new RuntimeException("FFVolley {$path} : HTTP {$response->status()}");
        }

        $body = $response->body();

        return mb_check_encoding($body, 'UTF-8') ? $body : mb_convert_encoding($body, 'UTF-8', 'Windows-1252');
    }
}
