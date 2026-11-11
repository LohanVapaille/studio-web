{{-- Rideau de transition entre pages (voir resources/js/page-transition.js) --}}
<div class="page-transition" data-page-transition aria-hidden="true">
    @for ($i = 0; $i < 5; $i++)
        <span class="pt-band {{ $i === 2 ? 'pt-band--accent' : '' }}"></span>
    @endfor
    <div class="pt-logo">
        <span class="pt-logo__text">Lova Web</span>
        <span class="pt-logo__loader"></span>
    </div>
</div>
