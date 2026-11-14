<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $seoTitle = $title ?? 'Lovaweb - Lohan Vapaille — Développeur web freelance en Seine-et-Marne (77) & Île-de-France';
        $seoDescription = $description ?? "Lovaweb — Lohan Vapaille, développeur web freelance basé en Seine-et-Marne (77). Sites vitrines, e-commerce et plateformes sur mesure, qualité agence au prix freelance. Devis gratuit, Île-de-France, France et international.";
        $seoUrl = url()->current();
        $seoImage = isset($image) ? trim((string) $image) : asset('img/allsites.png');
    @endphp
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="author" content="Lohan Vapaille">
    <meta name="robots" content="{{ $robots ?? 'index, follow, max-image-preview:large' }}">
    <link rel="canonical" href="{{ $seoUrl }}">
    <meta name="theme-color" content="#4338ca">

    <!-- Référencement local -->
    <meta name="geo.region" content="FR-77">
    <meta name="geo.placename" content="Seine-et-Marne, Île-de-France">

    <!-- Open Graph / réseaux sociaux -->
    <meta property="og:type" content="website">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:site_name" content="Lovaweb — Lohan Vapaille">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">

    <!-- Données structurées -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Person',
                '@id' => url('/') . '#lohan-vapaille',
                'name' => 'Lohan Vapaille',
                'jobTitle' => 'Développeur web freelance',
                'url' => route('qui-suis-je'),
                'image' => asset('img/logo_lova.jpg'),
                'worksFor' => ['@id' => url('/') . '#lovaweb'],
                'knowsAbout' => ['Développement web', 'Laravel', 'PHP', 'React', 'JavaScript', 'UI/UX Design', 'SEO', 'E-commerce'],
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressRegion' => 'Seine-et-Marne',
                    'addressCountry' => 'FR',
                ],
            ],
            [
                '@type' => 'ProfessionalService',
                '@id' => url('/') . '#lovaweb',
                'name' => 'Lovaweb — Lohan Vapaille, développeur web freelance',
                'description' => "Création de sites internet sur mesure (vitrines, e-commerce, plateformes) par Lohan Vapaille, développeur web freelance en Seine-et-Marne (77). Qualité agence, prix freelance, devis gratuit.",
                'url' => url('/'),
                'logo' => asset('img/logo_lova.jpg'),
                'image' => $seoImage,
                'founder' => ['@id' => url('/') . '#lohan-vapaille'],
                'priceRange' => '€€',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressRegion' => 'Seine-et-Marne (77), Île-de-France',
                    'addressCountry' => 'FR',
                ],
                'areaServed' => [
                    ['@type' => 'AdministrativeArea', 'name' => 'Seine-et-Marne'],
                    ['@type' => 'AdministrativeArea', 'name' => 'Île-de-France'],
                    ['@type' => 'Country', 'name' => 'France'],
                    'International',
                ],
                'makesOffer' => collect(['Création de site vitrine', 'Création de site e-commerce', 'Plateforme e-learning', 'Plateforme communautaire', 'Refonte de site internet', 'Maintenance de site web'])
                    ->map(fn ($s) => ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => $s]])->all(),
            ],
            [
                '@type' => 'WebSite',
                '@id' => url('/') . '#website',
                'url' => url('/'),
                'name' => 'Lovaweb — Lohan Vapaille',
                'inLanguage' => 'fr-FR',
                'publisher' => ['@id' => url('/') . '#lovaweb'],
            ],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
    {{ $head ?? '' }}

    <!-- Polices Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo_lova.jpg') }}">

    <!-- Transition entre pages : le rideau couvre la page avant le premier rendu -->
    <script>
        try {
            if (sessionStorage.getItem('lova:page-transition')) {
                sessionStorage.removeItem('lova:page-transition');
                document.documentElement.classList.add('pt-arriving');
                // filet de sécurité si le JS ne se charge pas
                setTimeout(function () { document.documentElement.classList.remove('pt-arriving'); }, 3000);
            }
        } catch (e) {}
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js"></script>

    <!-- Vite -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>

<body x-data="{
    contactOpen: false,
    toastOpen: {{ session('success') || session('error') ? 'true' : 'false' }},
    toastType: '{{ session('success') ? 'success' : (session('error') ? 'error' : '') }}'
}" @click="window.lovaTransition && window.lovaTransition.handle($event)">

    <x-page-transition />

    <header x-data="{ menuOpen: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 8"
        :class="{ 'is-scrolled': scrolled, 'menu-open': menuOpen }">
        <div class="header-inner">
            <div class="logo">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('img/logo_lova.jpg') }}" alt="Lovaweb — Lohan Vapaille, développeur web freelance" class="logo-img">
                    <span class="logo-text">Web</span>
                </a>
            </div>
            <nav @click="menuOpen = false" aria-label="Navigation principale">
                <a href="{{ route('home') }}#services" class="nav-link">Services</a>
                <a href="{{ route('home') }}#projets" class="nav-link">Projets</a>
                <a href="{{ route('home') }}#methode" class="nav-link">Méthode</a>
                <a href="{{ route('home') }}#tarifs" class="nav-link">Tarifs</a>
                <a href="{{ route('home') }}#maintenance" class="nav-link">Maintenance</a>
                <a href="{{ route('qui-suis-je') }}"
                    class="nav-link {{ request()->routeIs('qui-suis-je') ? 'is-active' : '' }}">Qui suis-je</a>
                <a href="{{ route('blog.index') }}"
                    class="nav-link {{ request()->routeIs('blog.*') ? 'is-active' : '' }}">Blog</a>
                <a href="#" class="btn-rdv nav-cta" @click.prevent="contactOpen = true">Prendre rendez-vous</a>
            </nav>
            <a href="#" class="btn-rdv header-cta" @click.prevent="contactOpen = true">Prendre rendez-vous</a>
            <button type="button" class="menu-toggle" @click="menuOpen = !menuOpen" :aria-expanded="menuOpen"
                aria-label="Ouvrir le menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>

    <!-- Injection du contenu -->
    <main>
        {{ $slot }}
    </main>

    <footer>
        <div class="container footer-inner">
            <div class="footer-brand">
                <a href="{{ route('home') }}" class="logo">
                    <img src="{{ asset('img/logo_lova.jpg') }}" alt="Logo Lovaweb" class="logo-img">
                    <span class="logo-text">Web</span>
                </a>
                <p>Lohan Vapaille, développeur web freelance en Seine-et-Marne (77). Sites vitrines, e-commerce et plateformes sur mesure, qualité agence au prix freelance. Île-de-France, toute la France et international.</p>
            </div>
            <div class="footer-col">
                <h4>Navigation</h4>
                <a href="{{ route('home') }}#services">Services</a>
                <a href="{{ route('projets') }}">Projets</a>
                <a href="{{ route('home') }}#tarifs">Tarifs</a>
                <a href="{{ route('home') }}#maintenance">Maintenance</a>
                <a href="{{ route('qui-suis-je') }}">Qui suis-je</a>
                <a href="{{ route('blog.index') }}">Blog</a>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <p>Un projet, une idée, une question&nbsp;? Devis gratuit et sans engagement.</p>
                <a href="#" class="btn-secondary" @click.prevent="contactOpen = true">Demander un devis</a>
            </div>
        </div>
        <div class="container footer-bottom">
            © {{ date('Y') }} Lovaweb — Lohan Vapaille, développeur web freelance (77). Tous droits réservés.
        </div>
    </footer>

    <!-- Bouton flottant de contact -->
    <button class="contact-fab" @click="contactOpen = true" aria-label="Ouvrir le formulaire de contact">✉</button>

    <!-- Pop-up de contact overlay -->
    <div class="contact-overlay" x-show="contactOpen" x-transition.opacity @click.self="contactOpen = false"
        style="display: none;">
        <div class="contact-modal" x-show="contactOpen" x-transition>
            <button class="contact-close" @click="contactOpen = false" aria-label="Fermer">✕</button>
            <h3>Parlons de votre projet</h3>
            <p class="desc desc1">Décrivez votre besoin, même en quelques mots : je reviens vers vous rapidement
                avec des conseils et un devis gratuit.</p>
            <p class="desc desc2">L'envoi d'un message et le devis sont gratuits et sans engagement.</p>
            <form method="POST" action="{{ route('contact.store') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Nom</label>
                    <input type="text" id="name" name="name" placeholder="Votre nom" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="vous@exemple.com"
                        value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" placeholder="Décrivez votre projet en quelques mots"
                        required>{{ old('message') }}</textarea>
                </div>
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
                @error('email') <p class="form-error">{{ $message }}</p> @enderror
                @error('message') <p class="form-error">{{ $message }}</p> @enderror
                <button type="submit" class="btn-submit">Envoyer le message</button>
            </form>


        </div>
    </div>

    @if (session('success') || session('error'))
        <div class="toast toast-{{ session('success') ? 'success' : 'error' }}" x-show="toastOpen" x-transition
            x-init="setTimeout(() => toastOpen = false, 5000)">
            <span class="toast-icon">{{ session('success') ? '✓' : '✕' }}</span>
            <p>{{ session('success') ?? session('error') }}</p>
            <button @click="toastOpen = false" aria-label="Fermer">✕</button>
        </div>
    @endif
</body>

</html>