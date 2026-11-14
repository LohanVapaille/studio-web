<x-app>
    <x-slot:title>Lovaweb - Blog : conseils pour créer et faire vivre votre site internet</x-slot>
    <x-slot:description>Le blog de Lovaweb : prix d'un site internet, refonte, référencement Google, choix de la
        technologie… Des conseils concrets de Lohan Vapaille, développeur web freelance en Seine-et-Marne (77).</x-slot>

    <section class="about-hero">
        <div class="container">
            <div class="section-head" style="margin-bottom: 0;">
                <div class="eyebrow"><span class="eyebrow-dot"></span> Blog</div>
                <h1 class="blog-index-title">Conseils pour votre site internet</h1>
                <p>Budget, technologie, refonte, référencement : des réponses claires et sans jargon pour prendre les
                    bonnes décisions avant de lancer votre projet web.</p>
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="blog-grid">
                @foreach ($articles as $article)
                    <x-article-card :article="$article" />
                @endforeach
            </div>
        </div>
    </section>
</x-app>
