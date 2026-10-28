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
                    <div class="glass-card service-card">
                        <div class="service-icon">①</div>
                        <h3>Sites vitrines</h3>
                        <p>Une présence en ligne qui vous ressemble : responsive, rapide et optimisée pour le
                            référencement.</p>
                    </div>
                    <div class="glass-card service-card">
                        <div class="service-icon">②</div>
                        <h3>E-commerce</h3>
                        <p>Boutiques en ligne complètes : catalogue, paiement sécurisé et gestion des commandes.</p>
                    </div>
                    <div class="glass-card service-card">
                        <div class="service-icon">③</div>
                        <h3>Plateformes e-learning</h3>
                        <p>Gestion des cours, suivi de progression et back-office complet pour piloter vos formations.
                        </p>
                    </div>
                    <div class="glass-card service-card">
                        <div class="service-icon">④</div>
                        <h3>Plateformes communautaires</h3>
                        <p>Profils, interactions et contenus partagés pensés pour l'engagement de vos utilisateurs.</p>
                    </div>
                    <div class="glass-card service-card">
                        <div class="service-icon">⑤</div>
                        <h3>Refonte de site</h3>
                        <p>Modernisation du design, de la performance et de l'ergonomie de votre site existant.</p>
                    </div>
                    <div class="glass-card service-card">
                        <div class="service-icon">⑥</div>
                        <h3>Messagerie instantanée</h3>
                        <p>Intégration de chat en temps réel pour fluidifier la communication avec vos utilisateurs.</p>
                    </div>
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

        <section id="maintenance">
            <div class="container">
                <div class="section-head">
                    <div class="eyebrow"><span class="eyebrow-dot"></span> Après la mise en ligne</div>
                    <h2>Une fois le site réalisé, que se passe-t-il ?</h2>
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

        <section id="avis">
            <div class="container">
                <div class="section-head">
                    <div class="eyebrow"><span class="eyebrow-dot"></span> Avis clients</div>
                    <h2>Ce qu'en disent mes clients</h2>
                    <p>Quatre projets, quatre retours d'expérience.</p>
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
                                <div class="review-avatar"><img src="{{ asset('img/logo_lova.jpg') }}" alt="Oumar S"></div>
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
