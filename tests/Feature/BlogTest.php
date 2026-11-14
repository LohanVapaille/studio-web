<?php

namespace Tests\Feature;

use App\Filament\Resources\Articles\Pages\CreateArticle;
use App\Filament\Resources\Articles\Pages\EditArticle;
use App\Filament\Resources\Articles\Pages\ListArticles;
use App\Models\Article;
use App\Models\User;
use Database\Seeders\ArticlesSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ArticlesSeeder::class);
    }

    private function actingAsAdmin(): static
    {
        Filament::setCurrentPanel('admin');

        return $this->actingAs(User::factory()->create());
    }

    // ---------- Site public ----------

    public function test_le_blog_liste_les_articles_publies(): void
    {
        $this->get('/blog')
            ->assertOk()
            ->assertSee('Combien coûte un site internet en 2026 ?')
            ->assertSee('Laravel vs WordPress');
    }

    public function test_un_article_s_affiche_avec_sa_mise_en_forme(): void
    {
        $this->get('/blog/combien-coute-un-site-internet')
            ->assertOk()
            ->assertSee('<title>Combien coûte un site internet en 2026 ? Prix détaillés | Lovaweb</title>', false)
            ->assertSee('class="lead"', false)
            ->assertSee('<blockquote>', false)
            ->assertSee('<table', false)
            ->assertSee('"@type":"BlogPosting"', false);
    }

    public function test_brouillons_et_articles_programmes_sont_invisibles(): void
    {
        Article::create(['title' => 'Brouillon', 'slug' => 'brouillon', 'description' => 'd', 'content' => '<p>x</p>']);
        Article::create(['title' => 'Programmé', 'slug' => 'programme', 'description' => 'd', 'content' => '<p>x</p>', 'published_at' => now()->addDay()]);

        $this->get('/blog/brouillon')->assertNotFound();
        $this->get('/blog/programme')->assertNotFound();
        $this->get('/blog')->assertDontSee('Brouillon')->assertDontSee('Programmé');
        $this->get('/sitemap.xml')->assertOk()->assertDontSee('/blog/brouillon')->assertSee('/blog/laravel-vs-wordpress');
    }

    // ---------- Back-office ----------

    public function test_le_back_office_est_protege(): void
    {
        $this->get('/admin/articles')->assertRedirect('/admin/login');
    }

    public function test_les_pages_du_back_office_s_affichent(): void
    {
        $this->actingAsAdmin();

        $this->get('/admin/articles')->assertOk();
        $this->get('/admin/articles/create')->assertOk();
        $this->get('/admin/articles/laravel-vs-wordpress/edit')->assertOk();
    }

    public function test_la_liste_affiche_les_articles(): void
    {
        $this->actingAsAdmin();

        Livewire::test(ListArticles::class)
            ->assertCanSeeTableRecords(Article::all())
            ->assertSee('Publié');
    }

    public function test_creer_un_article_remplit_le_slug_et_cree_un_brouillon(): void
    {
        $this->actingAsAdmin();

        Livewire::test(CreateArticle::class)
            ->set('data.title', 'Mon nouvel article')
            ->assertSchemaStateSet(['slug' => 'mon-nouvel-article'])
            ->fillForm([
                'description' => 'Une description',
                'content' => '<h2>Partie</h2><p>Du texte <strong>en gras</strong>.</p>',
                'category' => 'SEO',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $article = Article::where('slug', 'mon-nouvel-article')->firstOrFail();
        $this->assertNull($article->published_at);
        $this->assertStringContainsString('<strong>en gras</strong>', $article->content);
    }

    public function test_un_slug_deja_utilise_est_refuse(): void
    {
        $this->actingAsAdmin();

        Livewire::test(CreateArticle::class)
            ->fillForm(['title' => 'X', 'slug' => 'laravel-vs-wordpress', 'description' => 'd', 'content' => '<p>x</p>'])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);
    }

    public function test_enregistrer_sans_rien_changer_conserve_la_mise_en_forme(): void
    {
        $this->actingAsAdmin();

        $article = Article::where('slug', 'combien-coute-un-site-internet')->firstOrFail();
        $before = $article->renderedContent();

        Livewire::test(EditArticle::class, ['record' => $article->getRouteKey()])
            ->call('save')
            ->assertHasNoFormErrors();

        $normalize = fn (string $html) => preg_replace(['/>\s+</', '/\s+/'], ['><', ' '], trim($html));
        $this->assertSame($normalize($before), $normalize($article->fresh()->renderedContent()));
    }
}
