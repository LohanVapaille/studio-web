<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Lova Web' }}</title>
    <meta name="description"
        content="{{ $description ?? "Lova Web — conception de sites vitrines, e-commerce et plateformes sur mesure, de l'idée à la mise en ligne." }}">

    <!-- Polices Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo_lova.jpg') }}">

    <!-- Alpine.js -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js"></script>

    <!-- Vite -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>

<body x-data="{
    contactOpen: false,
    toastOpen: {{ session('success') || session('error') ? 'true' : 'false' }},
    toastType: '{{ session('success') ? 'success' : (session('error') ? 'error' : '') }}'
}">

    <header x-data="{ menuOpen: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 8"
        :class="{ 'is-scrolled': scrolled, 'menu-open': menuOpen }">
        <div class="header-inner">
            <div class="logo">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('img/logo_lova.jpg') }}" alt="Logo Lova Web" class="logo-img">
                    <span class="logo-text">Web</span>
                </a>
            </div>
            <nav @click="menuOpen = false">
                <a href="{{ route('home') }}#services" class="nav-link">Services</a>
                <a href="{{ route('home') }}#projets" class="nav-link">Projets</a>
                <a href="{{ route('home') }}#methode" class="nav-link">Méthode</a>
                <a href="{{ route('home') }}#tarifs" class="nav-link">Tarifs</a>
                <a href="{{ route('home') }}#maintenance" class="nav-link">Maintenance</a>
                <a href="{{ route('qui-suis-je') }}"
                    class="nav-link {{ request()->routeIs('qui-suis-je') ? 'is-active' : '' }}">Qui suis-je</a>
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
                    <img src="{{ asset('img/logo_lova.jpg') }}" alt="Logo Lova Web" class="logo-img">
                    <span class="logo-text">Web</span>
                </a>
                <p>Sites vitrines, e-commerce et plateformes sur mesure, de l'idée à la mise en ligne.</p>
            </div>
            <div class="footer-col">
                <h4>Navigation</h4>
                <a href="{{ route('home') }}#services">Services</a>
                <a href="{{ route('projets') }}">Projets</a>
                <a href="{{ route('home') }}#tarifs">Tarifs</a>
                <a href="{{ route('home') }}#maintenance">Maintenance</a>
                <a href="{{ route('qui-suis-je') }}">Qui suis-je</a>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <p>Un projet, une idée, une question&nbsp;? Devis gratuit et sans engagement.</p>
                <a href="#" class="btn-secondary" @click.prevent="contactOpen = true">Demander un devis</a>
            </div>
        </div>
        <div class="container footer-bottom">
            © {{ date('Y') }} Lova Web — Tous droits réservés.
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