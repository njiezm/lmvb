<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Club;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    private function club(string $name): Club
    {
        return Club::create(['name' => $name, 'slug' => \Illuminate\Support\Str::slug($name), 'color' => '#242868', 'active' => true]);
    }

    public function test_public_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', ['name' => 'X', 'email' => 'x@x.fr', 'password' => 'secret123', 'password_confirmation' => 'secret123'])->assertNotFound();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_users_without_role_cannot_access_admin(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_inactive_accounts_cannot_log_in(): void
    {
        User::factory()->create(['email' => 'off@lmvb.fr', 'password' => 'Secret12345', 'role' => 'super_admin', 'active' => false]);

        $this->post('/login', ['email' => 'off@lmvb.fr', 'password' => 'Secret12345'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_club_admin_is_limited_to_their_club(): void
    {
        $own = $this->club('Club A');
        $other = $this->club('Club B');
        $admin = User::factory()->create(['role' => 'club_admin', 'club_id' => $own->id]);

        $this->actingAs($admin);
        $this->get('/admin')->assertOk();
        $this->get("/admin/clubs/{$own->slug}/edit")->assertOk();
        $this->get("/admin/clubs/{$other->slug}/edit")->assertForbidden();
        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/settings')->assertForbidden();
        $this->post('/admin/sync')->assertForbidden();
        $this->get('/admin/clubs')->assertRedirect("/admin/clubs/{$own->slug}/edit");
    }

    public function test_club_admin_news_are_attached_to_their_club_and_sanitized(): void
    {
        $own = $this->club('Club A');
        $other = $this->club('Club B');
        $category = Category::create(['name' => 'Championnats', 'slug' => 'match', 'color' => '#000000']);
        $admin = User::factory()->create(['role' => 'club_admin', 'club_id' => $own->id]);

        $this->actingAs($admin)->post('/admin/news', [
            'title' => 'Victoire à domicile',
            'excerpt' => 'Belle soirée',
            'content' => '<p>Bravo</p><script>alert(1)</script><img src="x" onerror="alert(2)">',
            'category_id' => $category->id,
            'club_id' => $other->id, // tentative de publier pour un autre club
            'status' => 'published',
            'featured' => 1,
        ])->assertRedirect('/admin/news');

        $news = News::firstOrFail();
        $this->assertSame($own->id, $news->club_id);
        $this->assertFalse($news->featured);
        $this->assertNotNull($news->published_at);
        $this->assertStringNotContainsString('<script', $news->content);
        $this->assertStringNotContainsString('onerror', $news->content);

        $foreign = News::create(['title' => 'Autre', 'slug' => 'autre', 'excerpt' => 'x', 'content' => 'x', 'category_id' => $category->id, 'club_id' => $other->id, 'status' => 'draft']);
        $this->get("/admin/news/{$foreign->slug}/edit")->assertForbidden();
    }

    public function test_super_admin_can_upload_a_club_logo_as_webp(): void
    {
        $club = $this->club('Club Logo');
        $admin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)->put("/admin/clubs/{$club->slug}", [
            'name' => 'Club Logo',
            'color' => '#123456',
            'active' => 1,
            'logo' => UploadedFile::fake()->image('logo.png', 1200, 1200),
        ])->assertRedirect('/admin/clubs');

        $logo = $club->fresh()->logo;
        $this->assertStringEndsWith('.webp', $logo);
        $this->assertFileExists(public_path($logo));
        [$width] = getimagesize(public_path($logo));
        $this->assertSame(600, $width);
        File::delete(public_path($logo));
    }
}
