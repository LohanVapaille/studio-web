<?php

use App\Models\Article;
use App\Models\LovaApp;
use App\Models\Project;
use App\Models\Review;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProjectController;

Route::get('/', function () {
    $featuredProjects = Project::onLovaweb()->where('is_featured', true)->ordered()->take(3)->get();
    $reviews = Review::published()->get();
    $lovaApps = LovaApp::visible()->get();
    // Les apps Lova passent devant les projets clients dès qu'au moins une est utilisable :
    // une rangée de « Bientôt » en haut de page ferait projet inachevé.
    $appsFirst = $lovaApps->contains(fn (LovaApp $app) => $app->isAvailable());

    return view('welcome', compact('featuredProjects', 'reviews', 'lovaApps', 'appsFirst'));
})->name('home');

Route::view('/qui-suis-je', 'about')->name('qui-suis-je');

Route::view('/mentions-legales', 'legal')->name('mentions-legales');

Route::get('/projets', [ProjectController::class, 'index'])->name('projets');
Route::get('/projets/{project:slug}', [ProjectController::class, 'show'])->name('projets.show');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{article}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/contact', function () {
    return redirect()->route('home');
});

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/sitemap.xml', function () {
    $pages = [
        ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'weekly'],
        ['loc' => route('projets'), 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['loc' => route('qui-suis-je'), 'priority' => '0.7', 'changefreq' => 'monthly'],
        ['loc' => route('blog.index'), 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['loc' => route('mentions-legales'), 'priority' => '0.2', 'changefreq' => 'yearly'],
    ];

    // Date de dernière modification réelle quand on la connaît
    $lastUpdate = fn (...$dates) => ($max = collect($dates)->filter()->max()) ? substr((string) $max, 0, 10) : null;
    $projectsUpdated = Project::onLovaweb()->max('updated_at');
    $pages[0]['lastmod'] = $lastUpdate($projectsUpdated, Review::max('updated_at'));
    $pages[1]['lastmod'] = $lastUpdate($projectsUpdated);
    $pages[3]['lastmod'] = $lastUpdate(Article::published()->max('updated_at'));
    $pages = array_map('array_filter', $pages);

    foreach (Project::onLovaweb()->ordered()->get(['slug', 'updated_at']) as $project) {
        $pages[] = [
            'loc' => route('projets.show', $project),
            'priority' => '0.6',
            'changefreq' => 'monthly',
            'lastmod' => $project->updated_at?->toDateString(),
        ];
    }

    foreach (Article::published()->latest('published_at')->get(['slug', 'updated_at']) as $article) {
        $pages[] = [
            'loc' => route('blog.show', $article),
            'priority' => '0.7',
            'changefreq' => 'monthly',
            'lastmod' => $article->updated_at->toDateString(),
        ];
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($pages as $page) {
        $lastmod = isset($page['lastmod']) ? "<lastmod>{$page['lastmod']}</lastmod>" : '';
        $xml .= "  <url><loc>{$page['loc']}</loc>{$lastmod}<changefreq>{$page['changefreq']}</changefreq><priority>{$page['priority']}</priority></url>\n";
    }
    $xml .= '</urlset>';

    return response($xml, 200)->header('Content-Type', 'application/xml');
})->name('sitemap');
