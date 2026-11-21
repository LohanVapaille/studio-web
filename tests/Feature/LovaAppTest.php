<?php

namespace Tests\Feature;

use App\Filament\Resources\LovaApps\Pages\CreateLovaApp;
use App\Filament\Resources\LovaApps\Pages\ListLovaApps;
use App\Models\LovaApp;
use App\Models\Project;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LovaAppTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_visible_apps_are_shown_on_the_homepage(): void
    {
        LovaApp::create(['name' => 'LovaTrip', 'tagline' => 'Vos voyages organisés', 'status' => 'live', 'url' => 'https://trip.example.test', 'is_visible' => true, 'has_ai' => true, 'features' => ['Itinéraire IA']]);
        LovaApp::create(['name' => 'LovaTask', 'status' => 'building', 'is_visible' => true]);
        LovaApp::create(['name' => 'LovaSecret', 'status' => 'idea', 'is_visible' => false]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Applications Lova')
            ->assertSee('LovaTrip')
            ->assertSee('https://trip.example.test', false)
            ->assertSee('Itinéraire IA')
            ->assertSee('LovaTask')
            ->assertSee('Bientôt')
            ->assertDontSee('LovaSecret');
    }

    public function test_apps_come_before_client_projects_only_when_one_is_available(): void
    {
        $app = LovaApp::create(['name' => 'LovaTask', 'status' => 'building', 'is_visible' => true]);

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertGreaterThan(strpos($html, 'id="projets"'), strpos($html, 'id="applications"'));
        $this->assertStringContainsString('Voir mes projets', $html);

        $app->update(['status' => 'live', 'url' => 'https://task.example.test']);

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertLessThan(strpos($html, 'id="projets"'), strpos($html, 'id="applications"'));
        $this->assertStringContainsString('Découvrir les apps Lova', $html);
    }

    public function test_apps_section_is_hidden_when_no_app_is_visible(): void
    {
        $this->get('/')->assertOk()->assertDontSee('id="applications"', false);
    }

    public function test_slug_and_monthly_cost_are_computed(): void
    {
        $app = LovaApp::create([
            'name' => 'LovaCalendar',
            'cost_items' => [['label' => 'Hébergement', 'amount' => '5.5'], ['label' => 'API IA', 'amount' => '12']],
        ]);

        $this->assertSame('lovacalendar', $app->slug);
        $this->assertEquals(17.5, $app->monthly_cost);
        $this->assertSame('Ca', $app->monogram);
    }

    public function test_project_presentation_keeps_layout_and_strips_scripts(): void
    {
        $project = new Project(['title' => 'Test', 'presentation' => '<h2>Objectif</h2><ul><li>Un</li></ul><blockquote><p>Citation</p></blockquote><script>alert(1)</script>']);

        $html = $project->presentation_html;

        $this->assertStringContainsString('<h2>Objectif</h2>', $html);
        $this->assertStringContainsString('<li>', $html);
        $this->assertStringContainsString('<blockquote>', $html);
        $this->assertStringNotContainsString('<script', $html);
    }

    public function test_admin_can_create_an_app(): void
    {
        $this->actingAs(User::factory()->create());
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(ListLovaApps::class)->assertOk();

        Livewire::test(CreateLovaApp::class)
            ->fillForm(['name' => 'LovaNote', 'status' => 'beta', 'pricing_model' => 'freemium', 'accent_color' => '#22d3ee', 'is_visible' => true])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('lova_apps', ['name' => 'LovaNote', 'slug' => 'lovanote', 'status' => 'beta']);
    }
}
