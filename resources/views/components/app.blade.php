<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lova Web</title>

    <!-- Polices Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="img/logo_lova.jpg">

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

    <header>
        <div class="header-inner">
            <div class="logo">
                <a href="{{ route('home') }}" style="display: flex; align-items: center; gap: 0.5rem;">
                    <img src="{{ asset('img/logo_lova.jpg') }}" alt="Logo Lova Web" class="logo-img">

                    <span class="logo-text">Web</span></a>

            </div>
            <nav>
                <a href="{{ route('home') }}#services" class="nav-link">Services</a>
                <a href="{{ route('home') }}#projets" class="nav-link">Projets</a>
                <a href="{{ route('home') }}#maintenance" class="nav-link">Maintenance</a>
                <a href="{{ route('qui-suis-je') }}" class="nav-link">Qui suis-je</a>
            </nav>
            <a href="#" class="btn-rdv" @click.prevent="contactOpen = true">Prendre rendez-vous</a>
        </div>
    </header>

    <!-- Injection du contenu -->
    <main>
        {{ $slot }}
    </main>

    <footer>
        © 2026 Lova Web — Tous droits réservés.
    </footer>

    <!-- Bouton flottant de contact -->
    <button class="contact-fab" @click="contactOpen = true" aria-label="Ouvrir le formulaire de contact">✉</button>

    <!-- Pop-up de contact overlay -->
    <div class="contact-overlay" x-show="contactOpen" x-transition.opacity @click.self="contactOpen = false"
        style="display: none;">
        <div class="contact-modal" x-show="contactOpen" x-transition>
            <button class="contact-close" @click="contactOpen = false" aria-label="Fermer">✕</button>
            <h3>Parlons de votre projet</h3>
            <p class="desc desc1">Décrivez votre besoin, je reviens vers vous rapidement.
            </p>
            <p class="desc desc2"> L'envoi d'un message n'est pas synonyme d'engagement.</p>
            </p>
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