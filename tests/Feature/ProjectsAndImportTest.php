<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Project;
use App\Models\Technology;
use App\Models\TimelineEntry;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProjectsAndImportTest extends TestCase
{
    use RefreshDatabase;

    // ---------- Site public ----------

    public function test_lovaweb_n_affiche_que_les_projets_web_visibles(): void
    {
        Project::create(['title' => 'Site visible', 'type' => 'web', 'short_description' => 'Un site']);
        Project::create(['title' => 'Site masqué', 'type' => 'web', 'show_on_lovaweb' => false]);
        Project::create(['title' => 'Logo client', 'type' => 'graphic', 'show_on_portfolio' => true]);

        $this->get('/projets')
            ->assertOk()
            ->assertSee('Site visible')
            ->assertSee(route('projets.show', 'site-visible'), false)
            ->assertDontSee('Site masqué')
            ->assertDontSee('Logo client');

        $this->get('/projets/site-masque')->assertNotFound();
        $this->get('/projets/logo-client')->assertNotFound();
    }

    public function test_page_projet_complete(): void
    {
        $project = Project::create([
            'title' => 'HuntersTeam',
            'short_description' => 'Plateforme pour un club de MMA',
            'presentation' => 'Premier paragraphe.<br><br>Deuxième<script>alert(1)</script>',
            'role' => 'Développeur full-stack',
            'url' => 'https://huntersteam.fr',
            'mockup_image' => 'img/WEB/mockuphuntersteam.jpg',
        ]);
        $project->technologies()->attach(Technology::create(['name' => 'Laravel']));
        $project->features()->create(['title' => 'Inscription en ligne', 'description' => 'Via HelloAsso', 'icon_class' => 'bx bx-user']);
        $project->images()->create(['path' => 'img/WEB/huntersteam1.jpg', 'caption' => 'Accueil du site']);
        Project::create(['title' => 'Autre projet']);

        $this->get('/projets/huntersteam')
            ->assertOk()
            ->assertSee('<h1>HuntersTeam</h1>', false)
            ->assertSee('Développeur full-stack')
            ->assertSee('Laravel')
            ->assertSee('Inscription en ligne')
            ->assertSee('bx bx-user', false)
            ->assertSee('Accueil du site')
            ->assertSee('mockuphuntersteam.jpg', false)
            ->assertSee('https://huntersteam.fr', false)
            ->assertSee('Autre projet')
            ->assertDontSee('<script>alert(1)</script>', false);

        $this->get('/sitemap.xml')->assertSee(route('projets.show', $project));
    }

    public function test_les_images_absentes_sont_servies_par_le_portfolio(): void
    {
        config(['services.portfolio.url' => 'https://portfolio.test']);

        $this->assertStringEndsWith('/img/WEB/bdb1.jpg', Project::assetUrl('img/WEB/bdb1.jpg'));
        $this->assertStringStartsNotWith('https://portfolio.test', Project::assetUrl('img/WEB/bdb1.jpg'));
        $this->assertSame('https://portfolio.test/img/logo/logo%20kdev.png', Project::assetUrl('img/logo/logo kdev.png'));
    }

    // ---------- Import du portfolio ----------

    public function test_import_du_portfolio_fusionne_sans_doublon(): void
    {
        $this->fakePortfolioDatabase();

        // Projet déjà présent sur Lovaweb, avec ses propres textes
        $existing = Project::create(['title' => 'Bulle de bonheur', 'short_description' => 'Texte Lovaweb', 'main_image' => 'img/WEB/bdb1.jpg']);
        $existing->features()->create(['title' => 'Déjà là', 'description' => '…']);
        Technology::create(['name' => 'PHP']);

        $this->artisan('portfolio:import', ['--connection' => 'portfolio_test'])->assertSuccessful();

        // Projet existant : rattaché, textes Lovaweb conservés, champs vides complétés
        $existing->refresh();
        $this->assertSame('portfolio-web:5', $existing->legacy_ref);
        $this->assertSame('Texte Lovaweb', $existing->short_description);
        $this->assertSame('Avril 2025', $existing->date_label);
        $this->assertTrue($existing->show_on_lovaweb);
        $this->assertTrue($existing->show_on_portfolio);
        $this->assertTrue($existing->is_featured_portfolio);
        $this->assertSame(['Déjà là'], $existing->features->pluck('title')->all());
        $this->assertSame(['PHP'], $existing->technologies->pluck('name')->all());

        // Projet web nouveau : créé, masqué sur Lovaweb
        $alley = Project::where('legacy_ref', 'portfolio-web:4')->firstOrFail();
        $this->assertSame('AlleyOOP', $alley->title);
        $this->assertFalse($alley->show_on_lovaweb);
        $this->assertSame('bx bxl-php', $alley->features->first()->icon_class);
        $this->assertSame(2, $alley->images()->count());

        // Projet graphique
        $gfx = Project::where('legacy_ref', 'portfolio-gfx:2')->firstOrFail();
        $this->assertSame('graphic', $gfx->type);
        $this->assertSame(['Logos', 'Design'], $gfx->tags);
        $this->assertSame('img/seinestreet/minia.png', $gfx->main_image);
        $this->assertSame(['Adobe Illustrator'], $gfx->technologies->pluck('name')->all());
        $this->assertSame('design', Technology::where('name', 'Adobe Illustrator')->value('category'));

        // Parcours et messages
        $this->assertSame(1, TimelineEntry::count());
        $message = Message::where('source', 'portfolio')->firstOrFail();
        $this->assertSame("J'aimerais un \"devis\"", $message->message);
        $this->assertSame('Demande', $message->subject);

        // Relancer l'import ne crée aucun doublon et respecte les réglages modifiés depuis
        $existing->update(['show_on_portfolio' => false]);
        $this->artisan('portfolio:import', ['--connection' => 'portfolio_test'])->assertSuccessful();

        $this->assertSame(3, Project::count());
        $this->assertSame(2, $alley->images()->count());
        $this->assertSame(1, TimelineEntry::count());
        $this->assertSame(1, Message::where('source', 'portfolio')->count());
        $this->assertFalse($existing->fresh()->show_on_portfolio);
    }

    public function test_simulation_n_enregistre_rien(): void
    {
        $this->fakePortfolioDatabase();

        $this->artisan('portfolio:import', ['--connection' => 'portfolio_test', '--dry-run' => true])->assertSuccessful();

        $this->assertSame(0, Project::count());
        $this->assertSame(0, Technology::count());
    }

    /** Base portfolio miniature, même structure que vapaille_portfolio. */
    private function fakePortfolioDatabase(): void
    {
        config(['database.connections.portfolio_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        $schema = Schema::connection('portfolio_test');
        $db = DB::connection('portfolio_test');

        $schema->create('techno', function (Blueprint $t) { $t->integer('id_techno'); $t->string('nom_techno'); });
        $schema->create('techno_gfx', function (Blueprint $t) { $t->integer('id_techno_gfx'); $t->string('nom_techno_gfx'); });
        $schema->create('fk_techno', function (Blueprint $t) { $t->integer('id_projet'); $t->integer('id_techno'); });
        $schema->create('fk_techno_gfx', function (Blueprint $t) { $t->integer('id_projet_gfx'); $t->integer('id_techno_gfx'); });
        $schema->create('caracteristiques', function (Blueprint $t) { $t->integer('id'); $t->integer('id_projet'); $t->string('h3'); $t->text('explication'); $t->text('balisei_cara'); });
        $schema->create('imagesweb', function (Blueprint $t) { $t->integer('id_imagesweb'); $t->integer('id_projetweb'); $t->text('lien'); });
        $schema->create('imagesgfx', function (Blueprint $t) { $t->integer('id'); $t->integer('id_projetgfx'); $t->text('lien'); });
        $schema->create('parcours', function (Blueprint $t) { $t->integer('id'); $t->string('annee'); $t->string('titre'); $t->string('etablissement'); $t->text('description')->nullable(); $t->date('date_tri'); });
        $schema->create('messages', function (Blueprint $t) { $t->integer('id_message'); $t->text('nom'); $t->text('objet'); $t->text('contenu'); $t->text('mail'); $t->date('date'); });
        $schema->create('projetsweb', function (Blueprint $t) {
            $t->integer('id_projet');
            foreach (['nom', 'categorie', 'date', 'lien', 'main_img', 'mockup', 'presentation', 'role', 'duree', 'lil-description', 'etat', 'interet'] as $c) {
                $t->text($c);
            }
            $t->boolean('selected');
        });
        $schema->create('projetsgfx', function (Blueprint $t) {
            $t->integer('id');
            foreach (['titre', 'lil-text', 'minia', 'cara1', 'cara2', 'image1', 'explication', 'date', 'interet', 'categorie', 'etat', 'role'] as $c) {
                $t->text($c);
            }
        });

        $web = ['categorie' => 'Développement web', 'lien' => '', 'mockup' => '', 'presentation' => "\r\nTexte<br><br>\r\n\r\nSuite", 'role' => 'Dev', 'duree' => '3 mois', 'lil-description' => 'Résumé portfolio', 'etat' => 'Actif', 'interet' => 'Projet étudiant'];
        $db->table('projetsweb')->insert([
            $web + ['id_projet' => 4, 'nom' => 'AlleyOOP', 'date' => 'Janvier 2025', 'main_img' => 'img/WEB/alleyoop1.jpg', 'selected' => 0],
            $web + ['id_projet' => 5, 'nom' => 'Bulle de bonheur ', 'date' => 'Avril 2025', 'main_img' => 'img/WEB/bdb1.jpg', 'selected' => 1],
        ]);
        $db->table('projetsgfx')->insert([
            'id' => 2, 'titre' => 'Logos SeineStreet', 'lil-text' => 'Trois logos', 'minia' => 'img/seinestreet/minia.png',
            'cara1' => 'Logos', 'cara2' => ' Design ', 'image1' => 'img/seinestreet/logo1.png', 'explication' => 'Explication',
            'date' => '2023', 'interet' => 'Association', 'categorie' => 'Création Graphique', 'etat' => 'Terminé', 'role' => 'Graphiste',
        ]);
        $db->table('caracteristiques')->insert([
            ['id' => 1, 'id_projet' => 4, 'h3' => 'Découverte de PHP', 'explication' => 'Premier projet PHP', 'balisei_cara' => "<i class='bx  bxl-php' ></i>"],
            ['id' => 2, 'id_projet' => 5, 'h3' => 'Réservation', 'explication' => 'Créneaux', 'balisei_cara' => "<i class='bx bx-lock'></i>"],
        ]);
        $db->table('imagesweb')->insert([
            ['id_imagesweb' => 1, 'id_projetweb' => 4, 'lien' => 'img/WEB/alleyoop1.jpg'],
            ['id_imagesweb' => 2, 'id_projetweb' => 4, 'lien' => 'img/WEB/alleyoop2.jpg'],
        ]);
        $db->table('imagesgfx')->insert(['id' => 1, 'id_projetgfx' => 2, 'lien' => 'img/seinestreet/logo1.png']);
        $db->table('techno')->insert([['id_techno' => 3, 'nom_techno' => 'php'], ['id_techno' => 1, 'nom_techno' => 'HTML ']]);
        $db->table('techno_gfx')->insert(['id_techno_gfx' => 2, 'nom_techno_gfx' => 'Adobe Illustrator']);
        $db->table('fk_techno')->insert([['id_projet' => 5, 'id_techno' => 3], ['id_projet' => 4, 'id_techno' => 1]]);
        $db->table('fk_techno_gfx')->insert(['id_projet_gfx' => 2, 'id_techno_gfx' => 2]);
        $db->table('parcours')->insert(['id' => 1, 'annee' => 'Septembre 2024', 'titre' => 'BUT MMI', 'etablissement' => 'IUT', 'description' => null, 'date_tri' => '2024-09-05']);
        $db->table('messages')->insert(['id_message' => 1, 'nom' => 'Client', 'objet' => 'Demande', 'contenu' => 'J&#039;aimerais un &quot;devis&quot;', 'mail' => 'client@example.com', 'date' => '2026-06-09']);
    }
}
