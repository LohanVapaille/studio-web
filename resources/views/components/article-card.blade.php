@props(['article'])

<a href="{{ route('blog.show', $article) }}" class="glass-card blog-card">
    @if ($article->category)
        <span class="blog-card-tag">{{ $article->category }}</span>
    @endif
    <h3>{{ $article->title }}</h3>
    <p>{{ $article->summary }}</p>
    <span class="blog-card-meta">{{ $article->reading_minutes }} min de lecture · Lire l'article →</span>
</a>
