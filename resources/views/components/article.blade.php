@props(['article', 'related'])

@php
    $articleUrl = route('blog.show', $article);
@endphp

<x-app>
    <x-slot:title>{{ $article->meta_title }}</x-slot>
    <x-slot:description>{{ $article->description }}</x-slot>
    <x-slot:image>{{ $article->image_url }}</x-slot>
    <x-slot:head>
        <meta property="article:published_time" content="{{ $article->published_at->toIso8601String() }}">
        <meta property="article:modified_time" content="{{ $article->updated_at->toIso8601String() }}">
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'BlogPosting',
                    '@id' => $articleUrl . '#article',
                    'headline' => $article->title,
                    'description' => $article->description,
                    'url' => $articleUrl,
                    'mainEntityOfPage' => $articleUrl,
                    'image' => $article->image_url,
                    'datePublished' => $article->published_at->toIso8601String(),
                    'dateModified' => $article->updated_at->toIso8601String(),
                    'inLanguage' => 'fr-FR',
                    'articleSection' => $article->category,
                    'wordCount' => count(preg_split('/\s+/u', trim(strip_tags($article->content)))),
                    'author' => ['@id' => url('/') . '#lohan-vapaille'],
                    'publisher' => ['@id' => url('/') . '#lovaweb'],
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => route('blog.index')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $article->title, 'item' => $articleUrl],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
    </x-slot>

    <article class="blog-article">
        <header class="about-hero blog-article-hero">
            <div class="container blog-narrow">
                <nav class="blog-breadcrumb" aria-label="Fil d'Ariane">
                    <a href="{{ route('home') }}">Accueil</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ route('blog.index') }}">Blog</a>
                </nav>
                @if ($article->category)
                    <div class="eyebrow"><span class="eyebrow-dot"></span> {{ $article->category }}</div>
                @endif
                <h1>{{ $article->title }}</h1>
                <p class="blog-meta">
                    Par <a href="{{ route('qui-suis-je') }}">Lohan Vapaille</a>, Lovaweb ·
                    <time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->locale('fr')->isoFormat('D MMMM YYYY') }}</time>
                    · {{ $article->reading_minutes }} min de lecture
                </p>
            </div>
        </header>

        <section class="blog-article-body">
            <div class="container blog-narrow">
                <div class="blog-prose">
                    {{ $slot }}
                </div>

                <aside class="glass-card blog-cta">
                    <h2>Un projet de site&nbsp;? Parlons-en.</h2>
                    <p>Je suis Lohan Vapaille, développeur web freelance en Seine-et-Marne (77). Décrivez-moi votre
                        besoin en quelques lignes : je vous réponds rapidement avec des conseils et un devis gratuit,
                        sans engagement.</p>
                    <a href="#" class="btn-rdv" @click.prevent="contactOpen = true">Demander mon devis gratuit</a>
                </aside>
            </div>
        </section>
    </article>

    @if ($related->isNotEmpty())
        <section class="section-alt blog-related">
            <div class="container">
                <div class="section-head">
                    <h2>À lire aussi</h2>
                </div>
                <div class="blog-grid">
                    @foreach ($related as $item)
                        <x-article-card :article="$item" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-app>
