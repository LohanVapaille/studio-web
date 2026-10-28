<x-app>
    <x-slot:title>Qui suis-je — Studio Lohan</x-slot>

        <section class="about-hero">
            <div class="container">
                <div class="about-intro">
                    <div>
                        <div class="eyebrow"><span class="eyebrow-dot"></span> Qui suis-je</div>
                        <h1>Développeur web, étudiant en BUT MMI.</h1>
                        <p>
                            Je m'appelle Lohan. Je conçois des sites et applications web sur mesure, du cahier des
                            charges jusqu'à la mise en ligne. Mon parcours en BUT Métiers du Multimédia et de l'Internet
                            m'a donné une formation complète : développement, design, gestion de projet — ce qui me
                            permet d'accompagner un client de bout en bout, sans intermédiaire.
                        </p>
                        <p>
                            Au-delà du code, ce qui me motive c'est de transformer une idée en quelque chose de concret
                            et d'utile. J'aime comprendre le besoin réel derrière une demande avant de me lancer dans la
                            technique, et soigner chaque détail jusqu'à la livraison.
                        </p>
                        <p class="signature">Lohan — Développeur web</p>
                    </div>
                    <div class="about-photo">


                        <img src="{{ asset('img/allsites.png') }}" alt="Apercu des sites" class="lohan-photo">


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

        <section id="parcours">
            <div class="container">
                <div class="section-head">
                    <div class="eyebrow"><span class="eyebrow-dot"></span> Parcours</div>
                    <h2>Comment j'en suis arrivé là</h2>
                    <p>Quelques étapes clés de mon parcours.</p>
                </div>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-date">2023</div>
                        <h3>Entrée en BUT MMI</h3>
                        <p>Début de la formation Métiers du Multimédia et de l'Internet : développement, design et
                            gestion de projet web.</p>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-date">2024</div>
                        <h3>Premiers projets web</h3>
                        <p>Développement de mes premières applications complètes (Bulle de Bonheur, portfolio
                            personnel), en
                            autonomie sur tout le cycle de développement.</p>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-date">2025</div>
                        <h3>Premiers clients</h3>
                        <p>Réalisation de projets pour de premiers clients, avec un vrai processus : cahier des charges,
                            devis, suivi de projet (TedyCRPE, Blog de Mélanie Jamain...)</p>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-date">2026</div>
                        <h3>Entrée en 3ᵉ année de BUT MMI</h3>
                        <p>Poursuite de la formation, en parallèle du développement de mon activité freelance et de
                            projets plus ambitieux comme Tedycrpe.</p>
                    </div>
                </div>
            </div>
        </section>
</x-app>