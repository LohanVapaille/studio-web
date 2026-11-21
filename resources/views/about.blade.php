<x-app>
    <x-slot:title>Lovaweb - Qui suis-je — Lohan Vapaille, développeur web freelance (77)</x-slot>
        <x-slot:description>Découvrez Lovaweb &amp; Lohan Vapaille, développeur web freelance en Seine-et-Marne (77) : parcours,
            compétences (Laravel, PHP, React) et méthode. Sites qualité agence au prix freelance, en Île-de-France et
            partout en France.</x-slot>

            <section class="about-hero">
                <div class="container">
                    <div class="about-intro">
                        <div>
                            <div class="eyebrow"><span class="eyebrow-dot"></span> Qui suis-je</div>
                            <h1>Lohan Vapaille, développeur web freelance en Seine-et-Marne (77).</h1>
                            <p>
                                Je m'appelle Lohan Vapaille. Basé en Seine-et-Marne (77), je conçois des sites et
                                applications
                                web sur mesure pour des clients en Île-de-France, partout en France et à
                                l'international, du
                                cahier des charges jusqu'à la mise en ligne. Mon parcours en BUT Métiers du Multimédia
                                et de l'Internet
                                m'a donné une formation complète : développement, design, gestion de projet — ce qui me
                                permet d'accompagner un client de bout en bout, sans intermédiaire.
                            </p>
                            <p>
                                Au-delà du code, ce qui me motive c'est de transformer une idée en quelque chose de
                                concret
                                et d'utile. J'aime comprendre le besoin réel derrière une demande avant de me lancer
                                dans la
                                technique, et soigner chaque détail jusqu'à la livraison. Mon objectif : vous livrer un
                                site
                                de qualité agence, à un prix de freelance bien plus abordable — avec un devis gratuit
                                dès la
                                présentation de votre projet.
                            </p>
                            <p class="signature">Lohan Vapaille — Développeur web freelance</p>
                        </div>
                        <div class="about-photo">
                            @php
                                $fanShots = [
                                    ['file' => 'music1.jpg', 'name' => 'Green Montana'],
                                    ['file' => 'mel1.jpg', 'name' => 'Blog de Mélanie Jamain'],
                                    ['file' => 'tim1.jpg', 'name' => "Techniques d'intégration multimédia"],
                                    ['file' => 'huntersteam1.jpg', 'name' => 'Hunters Team'],
                                    ['file' => 'tedy1.jpg', 'name' => 'TedyCRPE'],
                                ];
                            @endphp
                            <div class="sites-fan" role="img"
                                aria-label="Aperçu de sites réalisés par Lohan Vapaille : {{ collect($fanShots)->pluck('name')->join(', ') }}">
                                @foreach ($fanShots as $shot)
                                    <div class="fan-card" style="--i: {{ $loop->index }}; --n: {{ $loop->count }}" aria-hidden="true">
                                        <span class="fan-bar"><i></i><i></i><i></i></span>
                                        <img src="{{ asset('img/fan/' . $shot['file']) }}" alt="" width="960" height="480"
                                            loading="{{ $loop->last ? 'eager' : 'lazy' }}" decoding="async">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="competences">
                <div class="container">
                    <div class="section-head">
                        <div class="eyebrow"><span class="eyebrow-dot"></span> Compétences</div>
                        <h2>Les outils que j'utilise au quotidien</h2>
                        <p>Une stack complète pour gérer un projet du backend au frontend.</p>
                    </div>
                    <div class="skills-grid">
                        <div class="glass-card skill-group">
                            <h3>Backend</h3>
                            <ul>
                                <li>PHP</li>
                                <li>Laravel</li>
                                <li>MySQL</li>
                                <li>API REST</li>
                            </ul>
                        </div>
                        <div class="glass-card skill-group">
                            <h3>Frontend</h3>
                            <ul>
                                <li>HTML / SCSS</li>
                                <li>JavaScript</li>
                                <li>React</li>
                                <li>Alpine.js</li>
                            </ul>
                        </div>
                        <div class="glass-card skill-group">
                            <h3>Outils & méthodes</h3>
                            <ul>
                                <li>Git / GitHub</li>
                                <li>Gestion de projet</li>
                                <li>Cahier des charges</li>
                                <li>UI / UX Design</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section id="ia" class="section-alt">
                <div class="container">
                    <div class="section-head">
                        <div class="eyebrow"><span class="eyebrow-dot"></span> Transparence</div>
                        <h2>Comment j'utilise l'intelligence artificielle</h2>
                        <p>J'utilise l'IA dans mes créations, et je préfère vous le dire clairement. C'est un outil
                            que j'ai appris à maîtriser, pas un pilote automatique.</p>
                    </div>
                    <div class="ai-grid">
                        <div class="glass-card ai-card">
                            <span class="ai-tag">Avec l'IA</span>
                            <h3>Pour aller plus vite et apprendre</h3>
                            <ul>
                                <li>Accélérer le développement, pour passer plus de temps sur ce qui compte pour vous</li>
                                <li>Apprendre et approfondir de nouvelles technologies plus rapidement</li>
                            </ul>
                        </div>
                        <div class="glass-card ai-card ai-card--manual">
                            <span class="ai-tag">Sans IA</span>
                            <h3>Pour tout ce qui touche à votre site en production</h3>
                            <ul>
                                <li>La maintenance</li>
                                <li>Les sauvegardes</li>
                                <li>La mise en ligne</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section id="certifications">
                <div class="container">
                    <div class="section-head">
                        <div class="eyebrow"><span class="eyebrow-dot"></span> Certifications</div>
                        <h2>Des compétences vérifiables</h2>
                        <p>Chaque certification peut être vérifiée en ligne auprès de l'organisme qui l'a délivrée.</p>
                    </div>
                    <div class="certs-grid">
                        <a class="glass-card cert-card" href="https://directory.opquast.com/fr/certificat/WBFSDI/"
                            target="_blank" rel="noopener">
                            <span class="cert-issuer">Opquast</span>
                            <h3>Mobiliser un référentiel qualité numérique</h3>
                            <p>Niveau <strong>Expert</strong> (900 points) : qualité web, accessibilité et bonnes
                                pratiques.</p>
                            <span class="cert-meta">Délivrée en avril 2026 · Valide jusqu'en 2029</span>
                            <span class="cert-link">Vérifier le certificat <span aria-hidden="true">↗</span><span class="sr-only"> (nouvel onglet)</span></span>
                        </a>
                        <a class="glass-card cert-card"
                            href="https://academy.claude.com/verify/8f52ac5da3657a0d037b859e68b606e2" target="_blank"
                            rel="noopener">
                            <span class="cert-issuer">Claude Academy · Anthropic</span>
                            <h3>Claude Code in action</h3>
                            <p>Formation à l'utilisation de l'IA dans le développement logiciel avec Claude Code.</p>
                            <span class="cert-meta">Badge obtenu en novembre 2026</span>
                            <span class="cert-link">Vérifier le badge <span aria-hidden="true">↗</span><span class="sr-only"> (nouvel onglet)</span></span>
                        </a>
                    </div>
                </div>
            </section>

            <section id="parcours">
                <div class="container">
                    <div class="section-head">
                        <div class="eyebrow"><span class="eyebrow-dot"></span> Parcours</div>
                        <h2>Comment j'en suis arrivé là</h2>
                        <p>Quelques étapes clés de mon parcours.</p>
                    </div>
                    <ol class="timeline">
                        @foreach ($timeline as $entry)
                            <li class="timeline-item">
                                <time class="timeline-date" datetime="{{ $entry->sorted_at->format('Y-m') }}">{{ $entry->period_label }}</time>
                                <h3>{{ $entry->title }}</h3>
                                @if ($entry->institution)
                                    <div class="timeline-place">{{ $entry->institution }}</div>
                                @endif
                                @if ($entry->description)
                                    <p>{{ $entry->description }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            </section>
</x-app>