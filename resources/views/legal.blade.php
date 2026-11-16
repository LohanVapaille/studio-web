@php
    $c = config('legal.company');
    $host = config('legal.hosts.' . config('legal.host'));
    $registrar = config('legal.domain_registrar');
    $address = "{$c['street']}, {$c['postal_code']} {$c['city']}, {$c['country']}";
@endphp

<x-app>
    <x-slot:title>Mentions légales et confidentialité | Lovaweb</x-slot>
    <x-slot:description>Mentions légales du site Lovaweb (Lohan Vapaille, EI, SIREN {{ $c['siren'] }}) : éditeur, hébergeur, propriété intellectuelle et politique de confidentialité.</x-slot>
    <x-slot:robots>index, follow</x-slot>

    <article class="blog-article">
        <header class="about-hero blog-article-hero">
            <div class="container blog-narrow">
                <nav class="blog-breadcrumb" aria-label="Fil d'Ariane">
                    <a href="{{ route('home') }}">Accueil</a>
                    <span aria-hidden="true">/</span>
                    <span>Mentions légales</span>
                </nav>
                <div class="eyebrow"><span class="eyebrow-dot"></span> Informations légales</div>
                <h1>Mentions légales et politique de confidentialité</h1>
                <p class="blog-meta">Dernière mise à jour : {{ \Illuminate\Support\Carbon::parse('2026-11-16')->locale('fr')->isoFormat('D MMMM YYYY') }}</p>
            </div>
        </header>

        <section class="blog-article-body">
            <div class="container blog-narrow">
                <div class="blog-prose">
                    <p class="lead">Conformément aux articles 6-III et 19 de la loi n° 2004-575 du 21 juin 2004 pour la
                        confiance dans l'économie numérique (LCEN), voici les informations relatives au site
                        <strong>{{ preg_replace('#^https?://#', '', rtrim(url('/'), '/')) }}</strong>.</p>

                    <h2>Éditeur du site</h2>
                    <ul>
                        <li><strong>{{ $c['name'] }}</strong>, {{ $c['legal_form'] }}, exerçant sous le nom commercial
                            <strong>{{ $c['trade_name'] }}</strong></li>
                        <li>Adresse : {{ $address }}</li>
                        <li>SIREN : {{ $c['siren'] }}</li>
                        @if ($c['siret'])
                            <li>SIRET : {{ $c['siret'] }}</li>
                        @endif
                        <li>Code APE : {{ $c['ape'] }}</li>
                        <li>{{ $c['registration'] }}</li>
                        @if ($c['vat'])
                            <li>{{ $c['vat'] }}</li>
                        @endif
                        <li>E-mail : <a href="mailto:{{ $c['email'] }}">{{ $c['email'] }}</a></li>
                        @if ($c['phone'])
                            <li>Téléphone : <a href="tel:{{ preg_replace('/\s+/', '', $c['phone']) }}">{{ $c['phone'] }}</a></li>
                        @endif
                        <li>Directeur de la publication : {{ $c['publisher'] }}</li>
                    </ul>

                    <h2>Hébergement</h2>
                    <ul>
                        <li>Hébergeur : <strong>{{ $host['name'] }}</strong></li>
                        <li>Adresse : {{ $host['address'] }}</li>
                        <li>Téléphone : {{ $host['phone'] }}</li>
                        <li>Site : <a href="{{ $host['website'] }}" target="_blank" rel="noopener">{{ preg_replace('#^https?://#', '', $host['website']) }}</a></li>
                    </ul>
                    <p>Le nom de domaine est enregistré auprès de {{ $registrar['name'] }}, {{ $registrar['address'] }}.</p>

                    <h2>Propriété intellectuelle</h2>
                    <p>L'ensemble du contenu de ce site (textes, visuels, logo, mise en page, code) est la propriété de
                        {{ $c['name'] }}, sauf mention contraire. Les captures et visuels des projets présentés sont
                        reproduits avec l'accord des clients concernés, qui en conservent la propriété. Toute reproduction,
                        représentation ou adaptation, totale ou partielle, sans autorisation écrite préalable est interdite.</p>
                    <p>Les avis clients publiés sont reproduits tels que rédigés par leurs auteurs. Ceux marqués du logo
                        Google proviennent de la fiche Google Business Profile de {{ $c['trade_name'] }}.</p>

                    <h2>Responsabilité</h2>
                    <p>{{ $c['name'] }} s'efforce de fournir des informations exactes et à jour, sans pouvoir garantir
                        l'absence d'erreurs ou d'omissions. Les tarifs indiqués sur le site sont donnés à titre indicatif :
                        seul le devis signé fait foi. Les liens vers des sites tiers sont proposés pour information ;
                        leur contenu n'engage pas l'éditeur.</p>

                    <h2 id="confidentialite">Politique de confidentialité</h2>

                    <h3>Responsable du traitement</h3>
                    <p>{{ $c['name'] }} ({{ $c['trade_name'] }}), {{ $address }} —
                        <a href="mailto:{{ $c['email'] }}">{{ $c['email'] }}</a>.</p>

                    <h3>Données collectées et finalité</h3>
                    <p>Le formulaire de contact recueille votre <strong>nom</strong>, votre <strong>adresse e-mail</strong>
                        et votre <strong>message</strong>. Ces données servent uniquement à répondre à votre demande et,
                        le cas échéant, à établir un devis. La base légale est votre consentement et les mesures
                        précontractuelles prises à votre demande (art. 6.1.a et 6.1.b du RGPD).</p>
                    <p>Elles ne sont ni vendues, ni cédées, ni utilisées à des fins publicitaires. Elles sont stockées
                        chez l'hébergeur du site, en France, et transmises par e-mail à l'éditeur.</p>

                    <h3>Durée de conservation</h3>
                    <p>Les messages sont conservés au maximum 3 ans à compter du dernier échange, puis supprimés.
                        Si une prestation est conclue, les documents commerciaux sont conservés selon les durées légales
                        (10 ans pour les pièces comptables).</p>

                    <h3>Vos droits</h3>
                    <p>Vous disposez d'un droit d'accès, de rectification, d'effacement, de limitation, d'opposition et
                        de portabilité de vos données. Pour l'exercer, écrivez à
                        <a href="mailto:{{ $c['email'] }}">{{ $c['email'] }}</a>. Vous pouvez aussi introduire une
                        réclamation auprès de la <a href="https://www.cnil.fr" target="_blank" rel="noopener">CNIL</a>.</p>

                    <h3>Cookies</h3>
                    <p>Ce site n'utilise ni cookie publicitaire, ni outil de mesure d'audience. Seuls des cookies
                        techniques strictement nécessaires sont déposés (session et protection contre les envois
                        frauduleux du formulaire) ; ils ne requièrent pas de consentement et expirent au bout de
                        {{ config('session.lifetime') / 60 }} heures d'inactivité.</p>
                    <p>Les polices de caractères sont chargées depuis les serveurs de Google Fonts, ce qui transmet
                        votre adresse IP à Google lors de l'affichage des pages.</p>
                </div>
            </div>
        </section>
    </article>
</x-app>
