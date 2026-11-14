<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

/**
 * Articles du blog. Le contenu HTML de chaque article est dans database/seeders/articles/{slug}.html.
 * Relançable sans doublon : les articles sont mis à jour par leur slug.
 *
 *   php artisan db:seed --class=ArticlesSeeder
 */
class ArticlesSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'slug' => 'combien-coute-un-site-internet',
                'title' => 'Combien coûte un site internet en 2026 ?',
                'seo_title' => 'Combien coûte un site internet en 2026 ? Prix détaillés | Lovaweb',
                'description' => "Prix d'un site vitrine, e-commerce ou sur mesure : fourchettes réalistes, ce qui fait varier le devis et les coûts annuels à prévoir. Le guide clair de Lovaweb.",
                'excerpt' => "Site vitrine, e-commerce, sur mesure : les vraies fourchettes de prix, ce qui fait grimper un devis et les frais à prévoir chaque année.",
                'category' => 'Budget',
                'reading_time' => 8,
                'published_at' => '2026-11-14 09:00:00',
            ],
            [
                'slug' => 'site-vitrine-ou-application-web',
                'title' => 'Site vitrine ou application web : que choisir ?',
                'seo_title' => 'Site vitrine ou application web : lequel choisir ? | Lovaweb',
                'description' => "Site vitrine ou application web : différences, exemples concrets, budgets et délais. Les bonnes questions pour choisir la solution adaptée à votre activité.",
                'excerpt' => "Présenter votre activité ou offrir un vrai service en ligne ? Les différences concrètes, exemples à l'appui, pour faire le bon choix.",
                'category' => 'Conseils',
                'reading_time' => 7,
                'published_at' => '2026-11-14 08:00:00',
            ],
            [
                'slug' => 'pourquoi-refaire-son-site',
                'title' => 'Pourquoi (et quand) refaire son site internet ?',
                'seo_title' => 'Pourquoi refaire son site internet ? 8 signes qui ne trompent pas | Lovaweb',
                'description' => "Site lent, pas adapté au mobile, invisible sur Google, impossible à modifier ? 8 signes qu'il est temps de refaire votre site, et comment réussir la refonte.",
                'excerpt' => "Lenteur, mobile, référencement, image de marque : 8 signes qu'il est temps de passer à la refonte, et comment la réussir sans perdre vos positions Google.",
                'category' => 'Refonte',
                'reading_time' => 7,
                'published_at' => '2026-11-14 07:00:00',
            ],
            [
                'slug' => 'laravel-vs-wordpress',
                'title' => 'Laravel vs WordPress : quelle technologie pour votre site ?',
                'seo_title' => 'Laravel vs WordPress : le comparatif honnête | Lovaweb',
                'description' => "Laravel ou WordPress ? Comparatif honnête : performances, sécurité, coût, autonomie, évolutivité. Quand choisir l'un ou l'autre selon votre projet.",
                'excerpt' => "Deux approches très différentes pour créer un site. Comparatif honnête — performances, sécurité, coût, évolutivité — et quand choisir l'un ou l'autre.",
                'category' => 'Technique',
                'reading_time' => 8,
                'published_at' => '2026-11-14 06:00:00',
            ],
            [
                'slug' => 'comment-ameliorer-son-referencement',
                'title' => 'Comment améliorer son référencement sur Google ?',
                'seo_title' => 'Comment améliorer son référencement Google ? 12 actions concrètes | Lovaweb',
                'description' => "12 actions concrètes pour améliorer le référencement de votre site : technique, contenu, SEO local, fiche Google Business et avis. Le guide pratique pour TPE et indépendants.",
                'excerpt' => "Technique, contenu, SEO local, avis clients : 12 actions concrètes, classées par priorité, pour gagner des visiteurs depuis Google.",
                'category' => 'SEO',
                'reading_time' => 10,
                'published_at' => '2026-11-14 05:00:00',
            ],
        ];

        foreach ($articles as $data) {
            $data['content'] = file_get_contents(__DIR__ . "/articles/{$data['slug']}.html");

            Article::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
