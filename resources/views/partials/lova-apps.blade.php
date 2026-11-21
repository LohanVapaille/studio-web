{{-- Section « Applications Lova » : placée avant ou après les projets selon $appsFirst (voir routes/web.php) --}}
<section id="applications" class="apps-section">
    <div class="container">
        <div class="section-head">
            <div class="eyebrow"><span class="eyebrow-dot"></span> Applications Lova</div>
            <h2>La preuve par l'exemple&nbsp;: mes propres plateformes</h2>
            <p>Avant de construire la vôtre, je construis les miennes. Les applications Lova sont des outils
                que j'édite et fais vivre au quotidien, souvent boostés à l'IA, pour s'organiser et gagner du
                temps. Ce que je fais pour moi, je peux le faire pour vous.</p>
        </div>
        <div class="apps-grid">
            @foreach ($lovaApps as $app)
                @php
                    $available = $app->isAvailable();
                    $color = preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $app->accent_color) ? $app->accent_color : '#4f46e5';
                @endphp
                <article class="glass-card app-card {{ $app->is_featured ? 'is-featured' : '' }} {{ $available ? '' : 'is-soon' }}"
                    style="--app-color: {{ $color }}">
                    <div class="app-top">
                        <div class="app-icon">
                            @if ($app->logo_url)
                                <img src="{{ $app->logo_url }}" alt="" loading="lazy" width="56" height="56">
                            @else
                                <span aria-hidden="true">{{ $app->monogram }}</span>
                            @endif
                        </div>
                        <div class="app-badges">
                            @if ($app->has_ai)
                                <span class="app-badge app-badge--ai">IA</span>
                            @endif
                            <span class="app-badge app-badge--{{ $app->status }}">{{ $app->public_status }}</span>
                        </div>
                    </div>
                    <h3>{{ $app->name }}</h3>
                    @if ($app->category)
                        <span class="app-category">{{ $app->category }}</span>
                    @endif
                    @if ($app->tagline)
                        <p class="app-tagline">{{ $app->tagline }}</p>
                    @endif
                    @if (filled($app->features))
                        <ul class="app-features">
                            @foreach (array_slice($app->features, 0, 3) as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="app-footer">
                        <span class="app-price">{{ $app->price_label ?: (\App\Models\LovaApp::PRICING_MODELS[$app->pricing_model] ?? '') }}</span>
                        @if ($available)
                            <a href="{{ $app->url }}" class="app-cta" target="_blank" rel="noopener">{{ $app->cta_label ?: 'Ouvrir' }}</a>
                        @else
                            <span class="app-cta is-disabled">{{ $app->public_status }}</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
        <div class="apps-pitch glass-card">
            <p><strong>Envie d'un outil comme celui-ci, pensé pour votre activité&nbsp;?</strong>
                Même technologie, même soin, adapté à vos besoins.</p>
            <a href="#" class="btn-rdv" @click.prevent="contactOpen = true">Parlons de votre outil</a>
        </div>
    </div>
</section>
