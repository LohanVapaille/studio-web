<?php

use App\Models\Article;
use App\Models\Project;
use App\Services\GoogleReviews;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;

Route::get('/', function (GoogleReviews $google) {
    $featuredProjects = Project::where('is_featured', true)->latest()->take(3)->get();
    $googleReviews = $google->get();

    return view('welcome', compact('featuredProjects', 'googleReviews'));
})->name('home');

Route::view('/qui-suis-je', 'about')->name('qui-suis-je');

Route::get('/projets', function () {
    $projects = Project::with('technologies')->latest()->get();

    return view('index', compact('projects'));
})->name('projets');

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
    ];

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
