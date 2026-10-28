<x-app>
    <x-slot:title>Studio Lohan — Solutions web modernes</x-slot>

        <section class="hero">
            <div class="blob blob-1" data-depth="1"></div>
            <div class="blob blob-2" data-depth="1.6"></div>
            <div class="hero-inner">
                <div class="eyebrow"><span class="eyebrow-dot"></span> Disponible pour de nouveaux projets</div>
                <h1>Des solutions web <span class="accent">modernes</span>, pensées pour votre croissance.</h1>
                <p>De l'idée au site fonctionnel, je conçois des plateformes sur mesure — vitrines, e-commerce ou
                    applications métier — avec une attention particulière portée à chaque détail.</p>
                <div class="hero-ctas">
                    <a href="#" class="btn-rdv" @click.prevent="contactOpen = true">Prendre rendez-vous</a>
                    <a href="#projets" class="btn-secondary">Voir mes projets</a>
                </div>
                <ul class="hero-points">
                    <li>Devis gratuit et sans engagement</li>
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
                    <h2>Ce que je peux construire pour vous</h2>
                    <p>Du site vitrine à la plateforme sur mesure, chaque projet est pensé selon vos besoins réels.</p>
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
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $service['icon'] !!}</svg>
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
                            <a href="{{ $project->url }}" target="_blank" rel="noopener" class="glass-card project-card">
                        @else
                                <div class="glass-card project-card">
                            @endif
                                <div class="project-thumb" @if ($project->main_image)
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
                            <span class="process-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
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
                    <p>Chaque projet est unique : le prix dépend de vos besoins, pas d'une grille toute faite.</p>
                </div>
                <div class="glass-card pricing-card">
                    <div class="pricing-content">
                        <h3>Un devis sur mesure, offert</h3>
                        <p>Contactez-moi pour en parler : je vous envoie un devis détaillé et adapté à votre budget.
                            Même si vous n'avez qu'une petite idée, je serai heureux de vous apporter des conseils.</p>
                        <ul class="pricing-points">
                            <li>Devis 100&nbsp;% gratuit</li>
                            <li>Aucun engagement</li>
                            <li>Conseils offerts, même pour une simple idée</li>
                            <li>Un tarif clair, sans mauvaise surprise</li>
                        </ul>
                    </div>
                    <div class="pricing-aside">
                        <span class="pricing-label">Création de site</span>
                        <span class="pricing-amount">Sur devis</span>
                        <span class="pricing-free">Gratuit &amp; sans engagement</span>
                        <a href="#" class="btn-rdv" @click.prevent="contactOpen = true">Demander mon devis gratuit</a>
                    </div>
                </div>
            </div>
        </section>

        <section id="maintenance">
            <div class="container">
                <div class="section-head">
                    <div class="eyebrow"><span class="eyebrow-dot"></span> Après la mise en ligne</div>
                    <h2>Une fois le site réalisé, que se <span class="nowrap">passe-t-il&nbsp;?</span></h2>
                    <p>Votre site continue de vivre : mises à jour, sécurité, petites évolutions. Je vous propose un
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
                                <li><span>Heures d'intervention incluses</span><strong>{{ $plan['hours'] }}</strong></li>
                                <li><span>Délai de prise en charge</span><strong>{{ $plan['delay'] }}</strong></li>
                                <li><span>Heure supplémentaire</span><strong>{{ $plan['extra'] }}</strong></li>
                            </ul>
                        </div>
                    @endforeach
                </div>
                <p class="plans-note">Engagement de 6 mois, renouvelable par période de 6 mois. Heures reportables 3 mois
                    maximum.</p>
            </div>
        </section>

        <section id="avis" class="section-alt">
            <div class="container">
                <div class="section-head">
                    <div class="eyebrow"><span class="eyebrow-dot"></span> Avis clients</div>
                    <h2>Ce qu'en disent mes clients</h2>
                    <p>Trois projets, trois retours d'expérience.</p>
                </div>
                <div class="reviews-grid">
                    @foreach ([

                            ['name' => 'Oumar S', 'role' => 'HuntersTeam', 'quote' => "Le site internet convient parfaitement à ce que nous avions besoin. Nous pouvons gérer tout le contenu en autonomie grâce au back-office."],
                            ['name' => 'Sandrine V', 'role' => 'Bulle de Bonheur', 'quote' => "Prestation rapide, site internet livré dans les temps. Lohan est toujours bienveillant et conseille au mieux pour que le projet soit réussi. Je recommande vivement !"],
                            ['name' => 'Tedy F', 'class' => 'tedy-review', 'role' => 'Coach CRPE Tedy Florette', 'quote' => "Je recommande vivement les services de ce webdesigner. Dès le début du projet, il a fait preuve d'une grande réactivité et d'une excellente compréhension de mes besoins. Le résultat est parfaitement conforme à mes attentes, tant sur le plan esthétique que fonctionnel. Au-delà de ses compétences techniques, j'ai particulièrement apprécié sa disponibilité, son écoute et sa capacité à être force de proposition..."],


                        ] as $review)
                        <div class="glass-card review-card {{ $review['class'] ?? '' }}">
                            <p class="review-quote">« {{ $review['quote'] }} »</p>
                            <button type="button" class="review-toggle" aria-expanded="false" hidden>Lire plus</button>
                            <div class="review-author">
                                <div class="review-avatar"><img src="{{ asset('img/logo_lova.jpg') }}" alt="{{ $review['name'] }}"></div>
                                    <div>
                                        <div class="review-name">{{ $review['name'] }}</div>
                                        <div class="review-role">{{ $review['role'] }}</div>
                                    </div>
                            </div>
                            </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="cta-band">
            <div class="container">
                <div class="cta-card">
                    <h2>Un projet en tête&nbsp;? Parlons-en.</h2>
                    <p>Même s'il ne s'agit que d'une petite idée, écrivez-moi : je serai heureux de vous conseiller.
                        Le devis est gratuit et ne vous engage à rien.</p>
                    <a href="#" class="btn-cta" @click.prevent="contactOpen = true">Demander mon devis gratuit</a>
                </div>
            </div>
        </section>

            <script>
            document.addEventListener('DOMContentLoaded', () => {
        const hero = document.querySelector('.hero');
                const blobs = document.querySelectorAll('.hero .blob');
                if (!hero || !blobs.length) return;
        
                let mouseX = 0, mouseY = 0;
                let currentX = 0, currentY = 0;
        
        hero.addEventListener('mousemove', (e) => {
                    const rect = hero.getBoundingClientRect();
                    // position relative au centre du hero, normalisée entre -1 et 1
                    mouseX = ((e.clientX - rect.left) / rect.width - 0.5) * 2;
            mouseY = ((e.clientY - rect.top) / rect.height - 0.5) * 2;
                });
        
                function animate() {
                    currentX += (mouseX - currentX) * 0.06;
                    currentY += (mouseY - currentY) * 0.06;
        
            blobs.forEach((blob) => {
                        const depth = parseFloat(blob.dataset.depth || 1);
                        const moveX = currentX * 40 * depth;
                        const moveY = currentY * 40 * depth;
                        blob.style.transform = `translate(${moveX}px, ${moveY}px)`;
                });

            requestAnimationFrame(animate);
        }
        animate();
    });

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
