{{--
    Intro animée + formes du hero (voir resources/js/hero-intro.js).
    À placer en premier enfant de <section class="hero hero--animated" data-hero>.

    Le script inline décide AVANT le premier rendu si l'intro doit être jouée :
    une fois par session, pas d'ancre dans l'URL, pas de prefers-reduced-motion.
    Si le JS ne démarre pas l'intro dans les 4 s, la couche est retirée : la page
    reste toujours utilisable.
--}}
<script>
    (function () {
        try {
            var root = document.documentElement;
            if (location.hash
                || window.matchMedia('(prefers-reduced-motion: reduce)').matches
                || sessionStorage.getItem('lova:intro-played')) return;
            root.classList.add('intro-pending');
            setTimeout(function () {
                if (!root.classList.contains('intro-running')) root.classList.remove('intro-pending');
            }, 4000);
        } catch (e) {}
    })();
</script>

<svg class="hero-defs" width="0" height="0" aria-hidden="true" focusable="false">
    <defs>
        <linearGradient id="lw-shape-gradient" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#4F46E5" />
            <stop offset="1" stop-color="#22D3EE" />
        </linearGradient>
    </defs>
</svg>

{{-- Les formes sont générées en JS à partir du tableau SHAPES --}}
<div class="hero-shapes" data-hero-shapes aria-hidden="true">
    <div class="hero-scan" data-hero-scan></div>
</div>

<div class="hero-intro" data-hero-intro>
    <div class="hero-intro__stage" aria-hidden="true">
        <div class="hero-intro__frame" data-intro-frame></div>
        <span class="hero-intro__pulse" data-intro-pulse></span>
        <span class="hero-intro__pulse" data-intro-pulse></span>
        <span class="hero-intro__dot" data-intro-dot></span>
    </div>
    <button type="button" class="hero-intro__skip" data-intro-skip aria-label="Passer l'animation d'introduction">
        Passer
    </button>
</div>
