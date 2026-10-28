import "./bootstrap";

// Apparition des blocs au défilement
document.addEventListener("DOMContentLoaded", () => {
    if (
        !("IntersectionObserver" in window) ||
        window.matchMedia("(prefers-reduced-motion: reduce)").matches
    ) {
        return;
    }

    // seulement ce qui est sous l'écran au chargement, pour éviter un clignotement en haut de page
    const targets = [
        ...document.querySelectorAll(
            "main .section-head, main .glass-card, main .cta-card, main .timeline-item",
        ),
    ].filter((el) => el.getBoundingClientRect().top > window.innerHeight);

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add("is-visible");
                observer.unobserve(entry.target);
            });
        },
        { rootMargin: "0px 0px -60px 0px" },
    );

    targets.forEach((el) => {
        const index = [...el.parentElement.children].indexOf(el);
        el.style.animationDelay = `${(index % 4) * 80}ms`;
        el.classList.add("reveal");
        observer.observe(el);
    });
});
