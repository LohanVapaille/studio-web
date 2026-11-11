<x-app>
    <x-slot:title>LovaWeb - Réalisations — Lohan Vapaille, développeur web freelance (77)</x-slot>
        <x-slot:description>Portfolio de LovaWeb & de Lohan Vapaille, développeur web freelance en Seine-et-Marne (77) :
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
                            @if ($project->url)
                                <a href="{{ $project->url }}" target="_blank" rel="noopener" class="glass-card project-card">
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

                                        @if ($project->technologies->isNotEmpty())
                                            <p style="margin-top: 10px; font-size: 0.78rem; color: var(--text-muted);">
                                                {{ $project->technologies->pluck('name')->join(' · ') }}
                                            </p>
                                        @endif


                                    </div>
                                    @if ($project->url)
                                        </a>
                                    @else
                                </div>
                            @endif
                        @endforeach
                </div>
                </div>
            </section>
</x-app>