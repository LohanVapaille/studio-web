<?php

namespace Tests\Feature;

use App\Filament\Resources\Messages\Pages\ManageMessages;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\RelationManagers\FeaturesRelationManager;
use App\Filament\Resources\Projects\RelationManagers\ImagesRelationManager;
use App\Models\Message;
use App\Models\Project;
use App\Models\Technology;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPortfolioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel('admin');
        $this->actingAs(User::factory()->create());
    }

    public function test_les_pages_s_affichent(): void
    {
        $project = Project::create(['title' => 'Bulle de bonheur', 'main_image' => 'img/WEB/bdb1.jpg']);
        $project->technologies()->attach(Technology::create(['name' => 'Laravel']));
        $project->features()->create(['title' => 'Back-office', 'description' => 'Gestion des créneaux']);
        Message::create(['name' => 'Client', 'email' => 'client@example.com', 'message' => 'Bonjour']);

        $this->get('/admin')->assertOk()->assertSee('Messages non lus');
        $this->get('/admin/projects')->assertOk()->assertSee('Bulle de bonheur');
        $this->get('/admin/projects/create')->assertOk();
        $this->get("/admin/projects/{$project->id}/edit")->assertOk()->assertSee('Caractéristiques')->assertSee('Galerie');
        $this->get('/admin/technologies')->assertOk()->assertSee('Laravel');
        $this->get('/admin/messages')->assertOk()->assertSee('client@example.com');
        $this->get('/admin/profile')->assertOk();
        $this->get('/admin/parcours')->assertOk();
    }

    public function test_creer_un_projet_avec_technologies_et_fonctionnalites(): void
    {
        $php = Technology::create(['name' => 'PHP']);

        Livewire::test(CreateProject::class)
            ->fillForm([
                'title' => 'Nouveau site',
                'url' => 'https://exemple.fr',
                'is_featured' => true,
                'technologies' => [$php->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $project = Project::where('title', 'Nouveau site')->firstOrFail();
        $this->assertTrue($project->is_featured);
        $this->assertSame(['PHP'], $project->technologies->pluck('name')->all());
        $this->assertSame('nouveau-site', $project->slug);
        $this->assertTrue($project->show_on_lovaweb);
    }

    public function test_crud_des_caracteristiques_et_images(): void
    {
        $project = Project::create(['title' => 'Projet']);

        Livewire::test(FeaturesRelationManager::class, ['ownerRecord' => $project, 'pageClass' => EditProject::class])
            ->callAction(TestAction::make('create')->table(), ['title' => 'SEO', 'description' => 'Optimisé', 'icon_class' => 'bx bx-search'])
            ->assertHasNoFormErrors();
        $this->assertSame('SEO', $project->features()->first()->title);

        $feature = $project->features()->first();
        Livewire::test(FeaturesRelationManager::class, ['ownerRecord' => $project, 'pageClass' => EditProject::class])
            ->callAction(TestAction::make('edit')->table($feature), ['title' => 'Référencement'])
            ->callAction(TestAction::make('delete')->table($feature));
        $this->assertDatabaseCount('project_features', 0);

        $image = $project->images()->create(['path' => 'img/WEB/bdb1.jpg']);
        Livewire::test(ImagesRelationManager::class, ['ownerRecord' => $project, 'pageClass' => EditProject::class])
            ->assertCanSeeTableRecords([$image])
            ->callAction(TestAction::make('edit')->table($image), ['caption' => 'Accueil'])
            ->assertHasNoFormErrors();
        $this->assertSame('Accueil', $image->fresh()->caption);
    }

    public function test_supprimer_un_projet_nettoie_ses_donnees(): void
    {
        $project = Project::create(['title' => 'À supprimer']);
        $project->images()->create(['path' => 'img/WEB/x.jpg']);
        $project->features()->create(['title' => 'A', 'description' => 'B']);
        $project->technologies()->attach(Technology::create(['name' => 'CSS']));

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->callAction('delete');

        $this->assertDatabaseCount('project_images', 0);
        $this->assertDatabaseCount('project_features', 0);
        $this->assertDatabaseCount('project_technology', 0);
        $this->assertDatabaseHas('technologies', ['name' => 'CSS']);
    }

    public function test_lire_un_message_le_marque_comme_lu(): void
    {
        $message = Message::create(['name' => 'Client', 'email' => 'c@example.com', 'message' => 'Bonjour']);

        Livewire::test(ManageMessages::class)
            ->mountAction(TestAction::make('view')->table($message));

        $this->assertTrue($message->fresh()->is_read);
    }
}
