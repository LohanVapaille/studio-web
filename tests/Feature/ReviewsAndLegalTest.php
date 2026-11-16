<?php

namespace Tests\Feature;

use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewsAndLegalTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_only_published_reviews_with_google_logo(): void
    {
        Review::query()->delete();
        Review::create(['name' => 'Client Google', 'quote' => 'Super travail', 'rating' => 5, 'source' => 'google']);
        Review::create(['name' => 'Client masqué', 'quote' => 'Invisible', 'rating' => 4, 'source' => 'google', 'is_published' => false]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Client Google')
            ->assertSee('aria-label="Avis Google"', false)
            ->assertDontSee('Client masqué');
    }

    public function test_reviews_section_is_hidden_when_empty(): void
    {
        Review::query()->delete();

        $this->get('/')->assertOk()->assertDontSee('id="avis"', false);
    }

    public function test_admin_can_manage_reviews(): void
    {
        \Filament\Facades\Filament::setCurrentPanel('admin');
        $this->actingAs(\App\Models\User::factory()->create());
        $review = Review::first();

        $this->get('/admin/reviews')->assertOk()->assertSee($review->name);
        $this->get('/admin/reviews/create')->assertOk();
        $this->get("/admin/reviews/{$review->id}/edit")->assertOk();

        \Livewire\Livewire::test(\App\Filament\Resources\Reviews\Pages\CreateReview::class)
            ->fillForm(['name' => 'Nouveau client', 'quote' => 'Très bon travail', 'rating' => 4, 'source' => 'google'])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('reviews', ['name' => 'Nouveau client', 'rating' => 4, 'source' => 'google']);
    }

    public function test_legal_page_and_sitemap(): void
    {
        $this->get('/mentions-legales')
            ->assertOk()
            ->assertSee('132 744 673')
            ->assertSee('O2SWITCH');

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee(route('mentions-legales'));
    }
}
