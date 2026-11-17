<x-app>
    <x-slot:title>Lovaweb - Réalisations — Lohan Vapaille, développeur web freelance (77)</x-slot>
        <x-slot:description>Portfolio de Lovaweb & de Lohan Vapaille, développeur web freelance en Seine-et-Marne (77) :
            sites vitrines, e-commerce et plateformes sur mesure réalisés pour des clients en Île-de-France et en
            France.</x-slot>

            <section class="about-hero">
                <div class="container">
                    <div class="section-head" style="margin-bottom: 0;">
                        <div class="eyebrow"><span class="eyebrow-dot"></span> Projets</div>
                        <h1 style="font-size: 2.4rem; font-weight: 700; margin-bottom: 14px;">Toutes mes réalisations
                            web</h1>
                        <p>Sites vitrines, e-commerce et plateformes conçus par Lohan Vapaille — projets clients,
                            personnels et étudiants, du premier site web à aujourd'hui.</p>
                    </div>
                </div>
            </section>

            <section>
                <div class="container">
                    <div class="projects-grid">
                        @foreach ($projects as $project)
                            <x-project-card :project="$project" with-tech />
                        @endforeach
                </div>
                </div>
            </section>
</x-app>