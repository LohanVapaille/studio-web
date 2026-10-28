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