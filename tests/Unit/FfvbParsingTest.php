<?php

namespace Tests\Unit;

use App\Services\Ffvb\FfvbClient;
use App\Services\Ffvb\FfvbImporter;
use App\Support\Text;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FfvbParsingTest extends TestCase
{
    private function fixture(string $name): string
    {
        return mb_convert_encoding(file_get_contents(base_path("tests/Fixtures/ffvb/$name")), 'UTF-8', 'Windows-1252');
    }

    public function test_it_lists_competitions_with_their_group(): void
    {
        $list = FfvbClient::parseCompetitions($this->fixture('home.html'));

        $this->assertGreaterThanOrEqual(20, count($list));
        $cum = collect($list)->firstWhere('code', 'CUM');
        $this->assertSame('CHAMPIONNAT SENIOR MASCULIN - POULE UNIQUE 2025/2026', $cum['name']);
        $this->assertSame('CHAMPIONNAT SENIOR MASCULIN 2025/2026', $cum['group']);
    }

    public function test_it_parses_the_official_csv_export(): void
    {
        $rows = FfvbClient::parseMatchesCsv($this->fixture('cum.csv'));

        $this->assertCount(90, $rows);
        $match = collect($rows)->firstWhere('Match', 'CUMA002');
        $this->assertSame('2025-09-27', $match['Date']);
        $this->assertSame('STE SPORT LE RAYON', $match['EQA_nom']);
        $this->assertSame('9722556', $match['EQB_no']);
        $this->assertSame('24-26,25-16,16-25,24-26', $match['Score']);
        $this->assertSame("HALL DES ANSES D'ARLET", $match['Salle']);
    }

    public function test_it_parses_the_standings_table(): void
    {
        $rows = FfvbClient::parseStandings($this->fixture('cum.html'));

        $this->assertCount(9, $rows);
        $this->assertSame(['position' => 1, 'team_name' => 'MARTINIQUE UNIVERSITE CLUB SECTION VB', 'points' => 42, 'played' => 16, 'won' => 15, 'lost' => 1],
            array_intersect_key($rows[0], array_flip(['position', 'team_name', 'points', 'played', 'won', 'lost'])));
        $this->assertSame(46, $rows[0]['sets_for']);
        $this->assertSame(1033, $rows[0]['points_against']);
        // Pénalités de forfait : points négatifs conservés
        $this->assertSame(-48, $rows[8]['points']);
        $this->assertSame(16, $rows[8]['forfeits']);
    }

    public static function results(): array
    {
        return [
            'victoire extérieure' => [' 1/3', '24-26,25-16,16-25,24-26', '89-93', 'finished', 1, 3, null, 4],
            'forfait extérieur' => [' 3/F', '25-0,25-0,25-0', '75-0', 'finished', 3, 0, 'away', 3],
            'forfait domicile' => ['F/3', '0-25,0-25,0-25', '0-75', 'finished', 0, 3, 'home', 3],
            'non joué' => ['', '', '', 'scheduled', null, null, null, 0],
        ];
    }

    #[DataProvider('results')]
    public function test_it_parses_results(string $sets, string $score, string $total, string $status, ?int $home, ?int $away, ?string $forfeit, int $setCount): void
    {
        $r = FfvbImporter::parseResult($sets, $score, $total);

        $this->assertSame($status, $r['status']);
        $this->assertSame($home, $r['home_score']);
        $this->assertSame($away, $r['away_score']);
        $this->assertSame($forfeit, $r['forfeit']);
        $this->assertCount($setCount, $r['set_scores'] ?? []);
    }

    public function test_it_ignores_empty_dates(): void
    {
        $this->assertNull(FfvbImporter::parseDate('0000-00-00', '21:00'));
        $this->assertSame('2025-09-27 20:00', FfvbImporter::parseDate('2025-09-27', '20:00')->format('Y-m-d H:i'));
    }

    public function test_it_classifies_competitions(): void
    {
        $this->assertSame(['category' => 'senior', 'gender' => 'M', 'phase' => 'regular'],
            array_intersect_key(FfvbImporter::classify('CHAMPIONNAT SENIOR MASCULIN - POULE UNIQUE'), array_flip(['category', 'gender', 'phase'])));
        $this->assertSame('m21', FfvbImporter::classify('PLAY-OFF M18/21 FILLES')['category']);
        $this->assertSame('F', FfvbImporter::classify('PLAY-OFF M18/21 FILLES')['gender']);
        $this->assertSame('playoff', FfvbImporter::classify('PLAY-OFF M18/21 FILLES')['phase']);
        $this->assertSame('m15', FfvbImporter::classify('CHAMPIONNAT M13-M15 MASCULIN')['category']);
        $this->assertSame('playdown', FfvbImporter::classify('PLAY-DOWM M18/M21 FEMININ')['phase']);
        $this->assertSame('F', FfvbImporter::classify('TOURNOI DES CHAMPIONNES ANTILLES')['gender']);
    }

    public function test_it_formats_ffvb_labels(): void
    {
        $this->assertSame('Championnat Senior Féminin - Poule Unique', Text::title('CHAMPIONNAT SENIOR FEMININ - POULE UNIQUE'));
        $this->assertSame("Hall des Anses d'Arlet", Text::title("HALL DES ANSES D'ARLET"));
        $this->assertSame('ASC Fumerolles', Text::title('A.S.C. FUMEROLLES'));
        $this->assertSame('Championnat M18-M21 Masculin', Text::title('CHAMPIONNAT M18-M21 MASCULIN'));
    }
}
