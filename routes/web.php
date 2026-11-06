<?php

use App\Models\Project;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    $featuredProjects = Project::where('is_featured', true)->latest()->take(3)->get();

    return view('welcome', compact('featuredProjects'));
})->name('home');

Route::view('/qui-suis-je', 'about')->name('qui-suis-je');

Route::get('/projets', function () {
    $projects = Project::with('technologies')->latest()->get();

    return view('index', compact('projects'));
})->name('projets');

Route::get('/contact', function () {
    return redirect()->route('home');
});

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/sitemap.xml', function () {
    $pages = [
        ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'weekly'],
        ['loc' => route('projets'), 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['loc' => route('qui-suis-je'), 'priority' => '0.7', 'changefreq' => 'monthly'],
    ];

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($pages as $page) {
        $xml .= "  <url><loc>{$page['loc']}</loc><changefreq>{$page['changefreq']}</changefreq><priority>{$page['priority']}</priority></url>\n";
    }
    $xml .= '</urlset>';

    return response($xml, 200)->header('Content-Type', 'application/xml');
})->name('sitemap');
