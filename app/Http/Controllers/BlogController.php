<?php

namespace App\Http\Controllers;

use App\Models\Article;

class BlogController extends Controller
{
    public function index()
    {
        $articles = Article::published()->latest('published_at')->get();

        return view('blog.index', compact('articles'));
    }

    public function show(Article $article)
    {
        // Brouillon ou article programmé : invisible pour le public
        abort_unless($article->published_at?->isPast(), 404);

        return view('blog.show', [
            'article' => $article,
            'related' => $article->related(),
        ]);
    }
}
