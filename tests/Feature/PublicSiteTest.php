<?php

namespace Tests\Feature;

use App\Models\BeachEvent;
use App\Models\Club;
use App\Models\Contact;
use App\Models\NewsletterSubscriber;
use Database\Seeders\CategorySeeder;
use Database\Seeders\NewsSeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([CategorySeeder::class, SettingSeeder::class, TeamSeeder::class, NewsSeeder::class]);
    }

    public function test_main_pages_render(): void
    {
        $club = Club::create(['name' => 'Club Test', 'slug' => 'club-test', 'color' => '#242868', 'active' => true]);

        foreach (['/', '/competitions', '/matchs', '/matchs/resultats', '/matchs/calendrier', '/clubs', '/clubs/club-test',
            '/actualites', '/actualites/saison-2026-2027-c-est-reparti', '/beach', '/beach/calendrier', '/selections',
            '/galerie', '/la-ligue', '/mentions-legales', '/contact', '/login', '/sitemap.xml'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_home_shows_the_president_word(): void
    {
        $this->get('/')->assertOk()->assertSee('Le mot de la Présidente')->assertSee('Maëva Antiste');
    }

    public function test_unknown_pages_return_404(): void
    {
        $this->get('/actualites/inexistante')->assertNotFound()->assertSee('Balle dehors');
    }

    public function test_contact_form_stores_message(): void
    {
        $this->post('/contact', [
            'name' => 'Jean Dupont', 'email' => 'jean@example.com', 'subject' => 'Licence / inscription',
            'message' => 'Bonjour, je souhaite prendre une licence.',
        ])->assertRedirect('/contact')->assertSessionHas('success');

        $this->assertSame(1, Contact::count());
    }

    public function test_contact_form_rejects_bots(): void
    {
        $this->post('/contact', [
            'name' => 'Bot', 'email' => 'bot@example.com', 'subject' => 'Autre',
            'message' => 'Spam spam spam spam', 'website' => 'http://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertSame(0, Contact::count());
    }

    public function test_newsletter_subscription_and_unsubscription(): void
    {
        $this->post('/newsletter', ['newsletter_email' => 'Fan@Example.com'])->assertSessionHas('newsletter');
        $subscriber = NewsletterSubscriber::firstOrFail();
        $this->assertSame('fan@example.com', $subscriber->email);

        $this->get('/newsletter/desinscription/'.$subscriber->token)->assertRedirect('/');
        $this->assertNotNull($subscriber->fresh()->unsubscribed_at);
    }

    public function test_beach_registration_respects_capacity(): void
    {
        $event = BeachEvent::create([
            'title' => 'Open de Sainte-Luce', 'slug' => 'open-sainte-luce', 'description' => 'Tournoi 2x2',
            'start_date' => now()->addMonth(), 'end_date' => now()->addMonth()->addDay(), 'location' => 'Sainte-Luce',
            'type' => 'tournament', 'status' => 'upcoming', 'max_teams' => 1, 'registration_open' => true,
        ]);
        $payload = fn ($team) => [
            'team_name' => $team, 'player1_name' => 'A', 'player1_email' => 'a@x.fr',
            'player2_name' => 'B', 'player2_email' => 'b@x.fr', 'phone' => '0696 12 34 56',
        ];

        $this->post("/beach/{$event->slug}/inscription", $payload('Les Requins'))->assertSessionHas('success');
        $this->post("/beach/{$event->slug}/inscription", $payload('Les Dauphins'))->assertSessionHas('error');

        $this->assertSame(1, $event->fresh()->registered_teams);
        $this->assertSame(1, $event->registrations()->count());
    }
}
