@props(['project', 'withTech' => false])

<a href="{{ route('projets.show', $project) }}" class="glass-card project-card">
    <div class="project-thumb" role="img" aria-label="Aperçu du projet {{ $project->title }}"
        @if ($project->main_image_url)
            style="background-image: url('{{ $project->main_image_url }}'); background-size: cover; background-position: center;"
        @endif>
    </div>
    <div class="project-body">
        @if ($project->category)
            <span class="project-tag">{{ $project->category }}</span>
        @endif
        <h3>{{ $project->title }}</h3>
        <p>{{ $project->short_description }}</p>

        @if ($withTech && $project->technologies->isNotEmpty())
            <p class="project-tech">{{ $project->technologies->pluck('name')->join(' · ') }}</p>
        @endif

        <span class="project-more">Voir le projet <span aria-hidden="true">→</span></span>
    </div>
</a>
