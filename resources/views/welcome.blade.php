<x-app>
    <x-slot:title>Lovaweb - Lohan Vapaille — Développeur web freelance Seine-et-Marne (77) & Île-de-France</x-slot>
        <x-slot:description>Lovaweb — Lohan Vapaille, développeur web freelance en Seine-et-Marne (77) : création de
            sites
            vitrines, e-commerce et plateformes sur mesure. Qualité agence, prix freelance. Devis gratuit en
            Île-de-France, France et international.</x-slot>
            <x-slot:head>
                <script type="application/ld+json">
        {!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect([
        ['Qui est Lohan Vapaille ?', 'Lohan Vapaille est un développeur web freelance basé en Seine-et-Marne (77). Il conçoit des sites vitrines, des boutiques e-commerce et des plateformes sur mesure pour les indépendants, TPE, PME et associations, de la maquette à la mise en ligne.'],
        ['Combien coûte la création d’un site internet ?', 'Chaque projet est différent, le prix dépend donc de vos besoins réels. En tant que freelance, je n’ai pas les frais de structure d’une agence : vous obtenez un site de qualité agence à un tarif bien plus abordable. Le devis est gratuit et sans engagement, sur simple présentation de votre projet.'],
        ['Où intervenez-vous ?', 'Je suis basé en Seine-et-Marne (77) et j’interviens dans toute l’Île-de-France (Paris, Meaux, Melun, Chelles, Marne-la-Vallée, Fontainebleau…). Je travaille aussi à distance partout en France et à l’international.'],
        ['Pourquoi choisir un développeur freelance plutôt qu’une agence ?', 'Vous avez un interlocuteur unique du début à la fin, des échanges directs et rapides, et un prix plus juste. La qualité, elle, est au niveau d’une agence : design sur mesure, code propre, site rapide, responsive et optimisé pour le référencement.'],
        ['Pourrai-je modifier mon site moi-même ?', 'Oui. Je livre un back-office simple qui vous permet de gérer vos contenus en autonomie, et je vous accompagne dans sa prise en main. Une maintenance mensuelle est aussi proposée à partir de 49 € HT/mois.'],
    ])->map(fn($f) => [
            '@type' => 'Question',
            'name' => $f[0],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]],
        ])->all(),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
                </x-slot>

                <x-page-deco />

                <section class="hero hero--animated" data-hero>
                    <x-hero-intro />
                    <div class="hero-inner">
                        <div class="eyebrow eyebrow--hero" data-hero-eyebrow>
                            <span class="eyebrow-dot"></span>
                            <span>Développeur web freelance</span>
                            <span class="eyebrow-sep" aria-hidden="true">·</span>
                            <span class="eyebrow-loc">Seine-et-Marne (77) &amp; Île-de-France</span>
                        </div>
                        <h1 data-hero-title>Un site web <span class="hl">qualité agence</span>, au prix d'un freelance.
                        </h1>
                        <p data-hero-sub>Je suis Lohan Vapaille, développeur web freelance basé en Seine-et-Marne (77).
                            De l'idée au site
                            en ligne, je conçois des sites vitrines, e-commerce et plateformes sur mesure — pour toute
                            l'Île-de-France, la France et l'international — avec une attention particulière portée à
                            chaque détail.</p>
                        <div class="hero-ctas" data-hero-ctas>
                            <a href="#" class="btn-rdv" @click.prevent="contactOpen = true">Prendre rendez-vous</a>
                            <a href="#projets" class="btn-secondary">Voir mes projets</a>
                        </div>
                        <ul class="hero-points" data-hero-points>
                            <li>Devis gratuit et sans engagement</li>
                            <li>Qualité agence, prix freelance</li>
                            <li>Accompagnement de A à Z</li>
                            <li>Back-office en autonomie</li>
                            <li>Maintenance dès 49 € HT/mois</li>
                        </ul>
                    </div>
                </section>

                <section id="services">
                    <div class="container">
                        <div class="section-head">
                            <div class="eyebrow"><span class="eyebrow-dot"></span> Services</div>
                            <h2>Création de sites internet sur mesure</h2>
                            <p>Du site vitrine à la plateforme sur mesure, chaque projet est pensé selon vos besoins
                                réels, avec le soin d'une agence web.</p>
                        </div>
                        <div class="services-grid">
                            @foreach ([
                                    ['title' => 'Sites vitrines', 'text' => "Une présence en ligne qui vous ressemble : responsive, rapide et optimisée pour le référencement.", 'icon' => '<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>'],
                                    ['title' => 'E-commerce', 'text' => "Boutiques en ligne complètes : catalogue, paiement sécurisé et gestion des commandes.", 'icon' => '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/>'],
                                    ['title' => 'Plateformes e-learning', 'text' => "Gestion des cours, suivi de progression et back-office complet pour piloter vos formations.", 'icon' => '<path d="M22 10 12 5 2 10l10 5 10-5z"/><path d="M6 12v5c3 2 9 2 12 0v-5"/>'],
                                    ['title' => 'Plateformes communautaires', 'text' => "Profils, interactions et contenus partagés pensés pour l'engagement de vos utilisateurs.", 'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>'],
                                    ['title' => 'Refonte de site', 'text' => "Modernisation du design, de la performance et de l'ergonomie de votre site existant.", 'icon' => '<path d="M23 4v6h-6M1 20v-6h6"/><path d="M3.5 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.65 4.36A9 9 0 0 0 20.5 15"/>'],
                                    ['title' => 'Messagerie instantanée', 'text' => "Intégration de chat en temps réel pour fluidifier la communication avec vos utilisateurs.", 'icon' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
                                ] as $service)
                                <div class="glass-card service-card">
                                    <div class="service-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            aria-hidden="true">{!! $service['icon'] !!}</svg>
                                    </div>
                                    <h3>{{ $service['title'] }}</h3>
                                    <p>{{ $service['text'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section id="projets">
                    <div class="container">
                        <div class="section-head">
                            <div class="eyebrow"><span class="eyebrow-dot"></span> Projets</div>
                            <h2>Quelques réalisations récentes</h2>
                            <p>Un aperçu de projets menés de bout en bout, de la conception au déploiement.</p>
                        </div>
                        <div class="projects-grid">
                            @foreach ($featuredProjects as $project)
                                @if ($project->url)
                                    <a href="{{ $project->url }}" target="_blank" rel="noopener"
                                        class="glass-card project-card">
                                @else
                                        <div class="glass-card project-card">
                                    @endif
                                        <div class="project-thumb" role="img"
                                            aria-label="Aperçu du projet {{ $project->title }}" @if ($project->main_image)
                                                style="background-image: url('{{ asset($project->main_image) }}'); background-size: cover; background-position: center;"
                                            @endif>
                                        </div>
                                        <div class="project-body">
                                            <span class="project-tag">{{ $project->category }}</span>
                                            <h3>{{ $project->title }}</h3>
                                            <p>{{ $project->short_description }}</p>




                                        </div>
                                        @if ($project->url)
                                            </a>
                                        @else
                                    </div>
                                @endif
                            @endforeach
                    </div>
                    <div class="section-cta">
                        <a href="{{ url('/projets') }}" class="btn-secondary">Voir tous les projets</a>
                    </div>
                    </div>
                </section>

                <section id="methode" class="section-alt">
                    <div class="container">
                        <div class="section-head">
                            <div class="eyebrow"><span class="eyebrow-dot"></span> Méthode</div>
                            <h2>Comment se déroule un projet</h2>
                            <p>Un cadre clair à chaque étape, pour que vous sachiez toujours où en est votre site.</p>
                        </div>
                        <div class="process-grid">
                            @foreach ([
                                    ['title' => 'Échange & cahier des charges', 'text' => "On discute de vos objectifs et de vos contraintes. Je formalise le besoin dans un cahier des charges et un devis clair, gratuit et sans engagement."],
                                    ['title' => 'Maquette & design', 'text' => "Je conçois l'interface de votre site et vous la validez avant le moindre développement."],
                                    ['title' => 'Développement', 'text' => "Intégration, back-office et tests sur tous les écrans. Vous suivez l'avancement à chaque étape."],
                                    ['title' => 'Mise en ligne & suivi', 'text' => "Déploiement, prise en main du back-office, puis maintenance si vous le souhaitez."],
                                ] as $step)
                                <div class="glass-card process-step">
                                    <span
                                        class="process-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <h3>{{ $step['title'] }}</h3>
                                    <p>{{ $step['text'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section id="tarifs">
                    <div class="container">
                        <div class="section-head">
                            <div class="eyebrow"><span class="eyebrow-dot"></span> Tarifs</div>
                            <h2>Combien coûte un site&nbsp;?</h2>
                            <p>Chaque projet est unique : le prix dépend de vos besoins, pas d'une grille toute faite.
                                Sans les frais d'une agence, vous payez un prix freelance, bien plus abordable.</p>
                        </div>
                        <div class="glass-card pricing-card">
                            <div class="pricing-content">
                                <h3>Un devis sur mesure, offert</h3>
                                <p>Présentez-moi votre projet : je vous envoie un devis gratuit, détaillé et adapté à
                                    votre budget.
                                    Même si vous n'avez qu'une petite idée, je serai heureux de vous apporter des
                                    conseils.</p>
                                <ul class="pricing-points">
                                    <li>Devis 100&nbsp;% gratuit</li>
                                    <li>Aucun engagement</li>
                                    <li>Conseils offerts, même pour une simple idée</li>
                                    <li>Un tarif clair, sans mauvaise surprise</li>
                                    <li>Qualité agence, tarif freelance</li>
                                </ul>
                            </div>
                            <div class="pricing-aside">
                                <span class="pricing-label">Création de site</span>
                                <span class="pricing-amount">Sur devis</span>
                                <span class="pricing-free">Gratuit &amp; sans engagement</span>
                                <a href="#" class="btn-rdv" @click.prevent="contactOpen = true">Demander mon devis
                                    gratuit</a>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="maintenance">
                    <div class="container">
                        <div class="section-head">
                            <div class="eyebrow"><span class="eyebrow-dot"></span> Après la mise en ligne</div>
                            <h2>Une fois le site réalisé, que se <span class="nowrap">passe-t-il&nbsp;?</span></h2>
                            <p>Votre site continue de vivre : mises à jour, sécurité, petites évolutions. Je vous
                                propose un
                                accompagnement mensuel adapté à vos besoins.</p>
                        </div>
                        <div class="plans-grid">
                            @foreach ([
                                    ['name' => 'Essentiel', 'price' => '49', 'hours' => '—', 'delay' => '72 h ouvrées', 'extra' => '40 €'],
                                    ['name' => 'Sérénité', 'price' => '79', 'hours' => '2 h', 'delay' => '48 h ouvrées', 'extra' => '40 €', 'featured' => true],
                                    ['name' => 'Évolution', 'price' => '159', 'hours' => '5 h', 'delay' => '24 h ouvrées', 'extra' => '35 €'],
                                ] as $plan)
                                <div class="glass-card plan-card {{ !empty($plan['featured']) ? 'is-featured' : '' }}">
                                    @if (!empty($plan['featured']))
                                        <span class="plan-badge">Conseillée</span>
                                    @endif
                                    <h3>{{ $plan['name'] }}</h3>
                                    <div class="plan-price">
                                        <span class="plan-amount">{{ $plan['price'] }} €</span>
                                        <span class="plan-period">HT / mois</span>
                                    </div>
                                    <ul class="plan-features">
                                        <li><span>Heures d'intervention incluses</span><strong>{{ $plan['hours'] }}</strong>
                                        </li>
                                        <li><span>Délai de prise en charge</span><strong>{{ $plan['delay'] }}</strong></li>
                                        <li><span>Heure supplémentaire</span><strong>{{ $plan['extra'] }}</strong></li>
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                        <p class="plans-note">Engagement de 6 mois, renouvelable par période de 6 mois. Heures
                            reportables 3 mois
                            maximum.</p>
                    </div>
                </section>

                @if ($reviews->isNotEmpty())
                <section id="avis" class="section-alt">
                    <div class="container">
                        @php
                            $googleReviewCount = $reviews->filter->isFromGoogle()->count();
                            $average = round($reviews->avg('rating'), 1);
                            $googleReviewUrl = config('services.google_business.review_url');
                        @endphp
                        <div class="section-head">
                            <div class="eyebrow"><span class="eyebrow-dot"></span> Avis clients</div>
                            <h2>Ce qu'en disent mes clients</h2>
                            <p class="reviews-summary">
                                <span class="reviews-stars" aria-hidden="true">★★★★★</span>
                                <strong>{{ number_format($average, 1, ',', '') }}/5</strong>
                                sur {{ $reviews->count() }} avis{{ $googleReviewCount === $reviews->count() ? ' Google' : '' }}
                            </p>
                        </div>
                        <div class="reviews-grid">
                            @foreach ($reviews as $review)
                                <div class="glass-card review-card {{ mb_strlen($review->quote) > 320 ? 'is-long' : '' }}">
                                    <div class="review-top">
                                        <div class="review-rating" aria-label="{{ $review->rating }} étoiles sur 5">
                                            {{ str_repeat('★', $review->rating) }}<span class="is-off">{{ str_repeat('★', 5 - $review->rating) }}</span>
                                        </div>
                                        @if ($review->isFromGoogle())
                                            <svg class="review-google" viewBox="0 0 48 48" role="img" aria-label="Avis Google">
                                                <path fill="#FFC107" d="M43.6 20.1H42V20H24v8h11.3C33.7 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.8 1.2 7.9 3.1l5.7-5.7C34 6.1 29.3 4 24 4 13 4 4 13 4 24s9 20 20 20 20-9 20-20c0-1.3-.1-2.6-.4-3.9z"/>
                                                <path fill="#FF3D00" d="m6.3 14.7 6.6 4.8C14.7 15.1 19 12 24 12c3.1 0 5.8 1.2 7.9 3.1l5.7-5.7C34 6.1 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/>
                                                <path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.2 35.1 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-7.9l-6.5 5C9.5 39.6 16.2 44 24 44z"/>
                                                <path fill="#1976D2" d="M43.6 20.1H42V20H24v8h11.3c-.8 2.2-2.2 4.2-4.1 5.6l6.2 5.2C37 39.2 44 34 44 24c0-1.3-.1-2.6-.4-3.9z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <p class="review-quote">« {{ $review->quote }} »</p>
                                    <button type="button" class="review-toggle" aria-expanded="false" hidden>Lire
                                        plus</button>
                                    <div class="review-author">
                                        <div class="review-avatar"><img src="{{ $review->photo_url }}"
                                                alt="{{ $review->name }}" loading="lazy"></div>
                                        <div>
                                            <div class="review-name">{{ $review->name }}</div>
                                            <div class="review-role">
                                                {{ $review->role ?: ($review->isFromGoogle() ? 'Avis Google' : '') }}@if ($review->reviewed_at) · {{ $review->reviewed_at->locale('fr')->isoFormat('MMMM YYYY') }}@endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($googleReviewUrl)
                            <div class="reviews-actions">
                                <a href="{{ $googleReviewUrl }}" class="btn-secondary" target="_blank" rel="noopener">Laisser un avis sur Google</a>
                            </div>
                        @endif
                    </div>
                </section>
                @endif

                <section id="faq">
                    <div class="container">
                        <div class="section-head">
                            <div class="eyebrow"><span class="eyebrow-dot"></span> FAQ</div>
                            <h2>Questions fréquentes</h2>
                            <p>Tout ce qu'il faut savoir avant de lancer votre projet de site internet.</p>
                        </div>
                        <div class="faq-list">
                            <details class="glass-card faq-item">
                                <summary>Qui est Lohan Vapaille ?</summary>
                                <p>Lohan Vapaille est un développeur web freelance basé en Seine-et-Marne (77). Il
                                    conçoit des sites vitrines, des boutiques e-commerce et des plateformes sur mesure
                                    pour les indépendants, TPE, PME et associations, de la maquette à la mise en ligne.
                                </p>
                            </details>
                            <details class="glass-card faq-item">
                                <summary>Combien coûte la création d’un site internet ?</summary>
                                <p>Chaque projet est différent, le prix dépend donc de vos besoins réels. En tant que
                                    freelance, je n'ai pas les frais de structure d'une agence : vous obtenez un site de
                                    qualité agence à un tarif bien plus abordable. Le devis est gratuit et sans
                                    engagement, sur simple présentation de votre projet.</p>
                            </details>
                            <details class="glass-card faq-item">
                                <summary>Où intervenez-vous ?</summary>
                                <p>Je suis basé en Seine-et-Marne (77) et j'interviens dans toute l'Île-de-France
                                    (Paris, Meaux, Melun, Chelles, Marne-la-Vallée, Fontainebleau…). Je travaille aussi
                                    à distance partout en France et à l'international.</p>
                            </details>
                            <details class="glass-card faq-item">
                                <summary>Pourquoi choisir un développeur freelance plutôt qu’une agence ?</summary>
                                <p>Vous avez un interlocuteur unique du début à la fin, des échanges directs et rapides,
                                    et un prix plus juste. La qualité, elle, est au niveau d'une agence : design sur
                                    mesure, code propre, site rapide, responsive et optimisé pour le référencement.</p>
                            </details>
                            <details class="glass-card faq-item">
                                <summary>Pourrai-je modifier mon site moi-même ?</summary>
                                <p>Oui. Je livre un back-office simple qui vous permet de gérer vos contenus en
                                    autonomie, et je vous accompagne dans sa prise en main. Une maintenance mensuelle
                                    est aussi proposée à partir de 49 € HT/mois.</p>
                            </details>
                        </div>
                    </div>
                </section>

                <section class="cta-band">
                    <div class="container">
                        <div class="cta-card">
                            <h2>Un projet en tête&nbsp;? Parlons-en.</h2>
                            <p>Même s'il ne s'agit que d'une petite idée, écrivez-moi : je serai heureux de vous
                                conseiller.
                                Le devis est gratuit et ne vous engage à rien.</p>
                            <a href="#" class="btn-cta" @click.prevent="contactOpen = true">Demander mon devis
                                gratuit</a>
                        </div>
                    </div>
                </section>

                <script>
                    // "Lire plus" sur les avis trop longs
                    document.addEventListener('DOMContentLoaded', () => {
                        document.querySelectorAll('.review-card').forEach((card) => {
                            const quote = card.querySelector('.review-quote');
                            const toggle = card.querySelector('.review-toggle');
                            if (!quote || !toggle) return;

                            const refresh = () => {
                                if (card.classList.contains('is-expanded')) return;
                                toggle.hidden = quote.scrollHeight <= quote.clientHeight + 1;
                            };

                            toggle.addEventListener('click', () => {
                                const expanded = card.classList.toggle('is-expanded');
                                toggle.setAttribute('aria-expanded', expanded);
                                toggle.textContent = expanded ? 'Lire moins' : 'Lire plus';
                            });

                            refresh();
                            window.addEventListener('resize', refresh);
                        });
                    });
                </script>
</x-app>