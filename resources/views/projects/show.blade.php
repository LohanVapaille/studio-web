@php
    $projectUrl = route('projets.show', $project);
    $heroImage = $project->mockup_image_url ?? $project->main_image_url;
    $facts = array_filter([
        'Date' => $project->date_label,
        'Rôle' => $project->role,
        'Durée' => $project->duration,
        'Contexte' => $project->context,
        'Statut' => $project->status,
    ]);
    $gallery = $project->images->map(fn ($image) => [
        'src' => $image->url,
        'alt' => $image->caption ?: 'Capture du projet ' . $project->title,
    ])->values();
    // Visionneuse : le mockup en premier, puis la galerie
    $viewer = collect($heroImage ? [['src' => $heroImage, 'alt' => 'Aperçu du projet ' . $project->title]] : [])->concat($gallery)->values();
    $offset = $heroImage ? 1 : 0;
@endphp

<x-app>
    <x-slot:title>{{ $project->title }} — Réalisation Lovaweb | Lohan Vapaille</x-slot>
    <x-slot:description>{{ \Illuminate\Support\Str::limit($project->short_description ?: strip_tags($project->presentation_html), 155) }}</x-slot>
    @if ($heroImage)
        <x-slot:image>{{ $heroImage }}</x-slot>
    @endif
    <x-slot:head>
        <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                array_filter([
                    '@type' => 'CreativeWork',
                    '@id' => $projectUrl . '#projet',
                    'name' => $project->title,
                    'description' => $project->short_description,
                    'url' => $projectUrl,
                    'image' => $heroImage,
                    'genre' => $project->category,
                    'keywords' => $project->technologies->pluck('name')->join(', ') ?: null,
                    'inLanguage' => 'fr-FR',
                    'creator' => ['@id' => url('/') . '#lohan-vapaille'],
                    'publisher' => ['@id' => url('/') . '#lovaweb'],
                    'sameAs' => $project->url,
                ]),
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Projets', 'item' => route('projets')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $project->title, 'item' => $projectUrl],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
    </x-slot>

    <article class="project-page" x-data="{ gallery: @js($viewer), current: null }"
        @keydown.escape.window="current = null"
        @keydown.arrow-right.window="if (current !== null) current = (current + 1) % gallery.length"
        @keydown.arrow-left.window="if (current !== null) current = (current - 1 + gallery.length) % gallery.length">

        {{-- ---------- En-tête ---------- --}}
        <header class="about-hero project-hero">
            <div class="container">
                <nav class="blog-breadcrumb" aria-label="Fil d'Ariane">
                    <a href="{{ route('home') }}">Accueil</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ route('projets') }}">Projets</a>
                </nav>

                <div class="project-hero-text">
                    @if ($project->category)
                        <div class="eyebrow"><span class="eyebrow-dot"></span> {{ $project->category }}</div>
                    @endif
                    <h1>{{ $project->title }}</h1>
                    @if ($project->short_description)
                        <p class="project-lead">{{ $project->short_description }}</p>
                    @endif

                    <div class="project-actions">
                        @if ($project->url)
                            <a href="{{ $project->url }}" class="btn-rdv" target="_blank" rel="noopener">
                                Voir le site en ligne <span aria-hidden="true">↗</span>
                            </a>
                        @endif
                        <a href="#" class="btn-secondary" @click.prevent="contactOpen = true">Un projet similaire ?</a>
                    </div>
                </div>

                @if ($facts)
                    <dl class="project-facts">
                        @foreach ($facts as $label => $value)
                            <div>
                                <dt>{{ $label }}</dt>
                                <dd>{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif
            </div>
        </header>

        {{-- ---------- Mockup ---------- --}}
        @if ($heroImage)
            <div class="container">
                <figure class="project-mockup">
                    <button type="button" @click="current = 0" aria-label="Agrandir l'aperçu du projet">
                        <img src="{{ $heroImage }}" alt="Aperçu du projet {{ $project->title }}" fetchpriority="high">
                    </button>
                    <figcaption>Cliquez pour agrandir</figcaption>
                </figure>
            </div>
        @endif

        {{-- ---------- Présentation + technologies ---------- --}}
        <section class="project-section">
            <div class="container project-content-grid">
                <div>
                    <h2>Le projet</h2>
                    <div class="blog-prose project-prose">
                        {!! $project->presentation_html ?: e($project->short_description) !!}
                    </div>
                </div>

                @if ($project->technologies->isNotEmpty())
                    <aside class="glass-card project-stack">
                        <h2>Technologies</h2>
                        <ul>
                            @foreach ($project->technologies as $technology)
                                <li>{{ $technology->name }}</li>
                            @endforeach
                        </ul>
                    </aside>
                @endif
            </div>
        </section>

        {{-- ---------- Caractéristiques ---------- --}}
        @if ($project->features->isNotEmpty())
            <section class="project-section section-alt">
                <div class="container">
                    <div class="section-head">
                        <div class="eyebrow"><span class="eyebrow-dot"></span> Points forts</div>
                        <h2>Ce qu'il faut retenir</h2>
                    </div>
                    <div class="project-features">
                        @foreach ($project->features as $feature)
                            <div class="glass-card project-feature">
                                <span class="project-feature-icon" aria-hidden="true">
                                    <i class="{{ $feature->icon_class ?: 'bx bx-check' }}"></i>
                                </span>
                                <h3>{{ $feature->title }}</h3>
                                <p>{{ $feature->description }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- ---------- Galerie ---------- --}}
        @if ($gallery->isNotEmpty())
            <section class="project-section">
                <div class="container">
                    <div class="section-head">
                        <div class="eyebrow"><span class="eyebrow-dot"></span> Galerie</div>
                        <h2>En images</h2>
                    </div>
                    <div class="project-gallery">
                        @foreach ($gallery as $i => $image)
                            <button type="button" class="project-shot" @click="current = {{ $i + $offset }}"
                                aria-label="Agrandir : {{ $image['alt'] }}">
                                <img src="{{ $image['src'] }}" alt="{{ $image['alt'] }}" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Visionneuse --}}
            <div class="project-lightbox" x-show="current !== null" x-transition.opacity style="display: none;"
                @click.self="current = null" role="dialog" aria-modal="true" aria-label="Image agrandie">
                <button type="button" class="project-lightbox-close" @click="current = null" aria-label="Fermer">✕</button>
                <button type="button" class="project-lightbox-nav is-prev" x-show="gallery.length > 1"
                    @click="current = (current - 1 + gallery.length) % gallery.length" aria-label="Image précédente">‹</button>
                <template x-if="current !== null">
                    <img :src="gallery[current].src" :alt="gallery[current].alt">
                </template>
                <button type="button" class="project-lightbox-nav is-next" x-show="gallery.length > 1"
                    @click="current = (current + 1) % gallery.length" aria-label="Image suivante">›</button>
                <p class="project-lightbox-count" x-show="gallery.length > 1"
                    x-text="(current + 1) + ' / ' + gallery.length"></p>
            </div>
        @endif

        {{-- ---------- Projet précédent / suivant ---------- --}}
        @if ($previous || $next)
            <nav class="container project-pager" aria-label="Autres projets">
                @if ($previous)
                    <a href="{{ route('projets.show', $previous) }}" class="glass-card">
                        <span>← Projet précédent</span>
                        <strong>{{ $previous->title }}</strong>
                    </a>
                @else
                    <span></span>
                @endif
                @if ($next)
                    <a href="{{ route('projets.show', $next) }}" class="glass-card is-next">
                        <span>Projet suivant →</span>
                        <strong>{{ $next->title }}</strong>
                    </a>
                @endif
            </nav>
        @endif

        {{-- ---------- Appel à l'action ---------- --}}
        <section class="project-section">
            <div class="container blog-narrow">
                <aside class="glass-card blog-cta">
                    <h2>Vous avez un projet de ce type&nbsp;?</h2>
                    <p>Je suis Lohan Vapaille, développeur web freelance en Seine-et-Marne (77). Décrivez-moi votre
                        besoin : je vous réponds rapidement avec des conseils et un devis gratuit, sans engagement.</p>
                    <a href="#" class="btn-rdv" @click.prevent="contactOpen = true">Demander mon devis gratuit</a>
                </aside>
            </div>
        </section>
    </article>

    @if ($related->isNotEmpty())
        <section class="section-alt">
            <div class="container">
                <div class="section-head">
                    <h2>D'autres réalisations</h2>
                </div>
                <div class="projects-grid">
                    @foreach ($related as $item)
                        <x-project-card :project="$item" />
                    @endforeach
                </div>
                <div class="section-cta">
                    <a href="{{ route('projets') }}" class="btn-secondary">Voir tous les projets</a>
                </div>
            </div>
        </section>
    @endif
</x-app>
