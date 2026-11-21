/**
 * Carrousel à défilement automatique ([data-carousel]).
 * - avance d'une carte toutes les 4,5 s, revient au début après la dernière
 * - se met en pause au survol, au focus clavier, au toucher et quand l'onglet est caché
 * - pas de défilement automatique si l'utilisateur préfère réduire les animations
 */
const INTERVAL = 2000;

function setupCarousel(root) {
    const track = root.querySelector("[data-carousel-track]");
    const slides = [...root.querySelectorAll("[data-carousel-slide]")];
    const dotsWrap = root.querySelector("[data-carousel-dots]");
    if (!track || slides.length < 2) return;

    const reducedMotion = window.matchMedia(
        "(prefers-reduced-motion: reduce)",
    ).matches;
    let timer = null;
    let paused = false;

    // index de la carte la plus à gauche visible
    const currentIndex = () => {
        const left = track.scrollLeft;
        let best = 0;
        slides.forEach((slide, i) => {
            if (
                Math.abs(slide.offsetLeft - slides[0].offsetLeft - left) <
                Math.abs(slides[best].offsetLeft - slides[0].offsetLeft - left)
            ) {
                best = i;
            }
        });
        return best;
    };

    const atEnd = () =>
        track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;

    const goTo = (index) => {
        const i = (index + slides.length) % slides.length;
        track.scrollTo({
            left: slides[i].offsetLeft - slides[0].offsetLeft,
            behavior: reducedMotion ? "auto" : "smooth",
        });
    };

    const next = () => (atEnd() ? goTo(0) : goTo(currentIndex() + 1));
    const prev = () =>
        track.scrollLeft <= 4
            ? goTo(slides.length - 1)
            : goTo(currentIndex() - 1);

    // Points : un par carte
    const dots = slides.map((_, i) => {
        const dot = document.createElement("button");
        dot.type = "button";
        dot.setAttribute("aria-label", `Aller à la carte ${i + 1}`);
        dot.addEventListener("click", () => {
            goTo(i);
            restart();
        });
        dotsWrap?.appendChild(dot);
        return dot;
    });

    const updateDots = () => {
        const active = atEnd() ? slides.length - 1 : currentIndex();
        dots.forEach((dot, i) =>
            dot.setAttribute("aria-current", i === active ? "true" : "false"),
        );
    };

    let raf = null;
    track.addEventListener(
        "scroll",
        () => {
            cancelAnimationFrame(raf);
            raf = requestAnimationFrame(updateDots);
        },
        { passive: true },
    );

    root.querySelector("[data-carousel-next]")?.addEventListener(
        "click",
        () => {
            next();
            restart();
        },
    );
    root.querySelector("[data-carousel-prev]")?.addEventListener(
        "click",
        () => {
            prev();
            restart();
        },
    );

    track.addEventListener("keydown", (e) => {
        if (e.key === "ArrowRight") {
            e.preventDefault();
            next();
        }
        if (e.key === "ArrowLeft") {
            e.preventDefault();
            prev();
        }
    });

    // ---------- Défilement automatique ----------
    const stop = () => {
        clearInterval(timer);
        timer = null;
    };
    const start = () => {
        if (reducedMotion || paused || timer || document.hidden) return;
        timer = setInterval(next, INTERVAL);
    };
    function restart() {
        stop();
        start();
    }

    const pause = () => {
        paused = true;
        stop();
    };
    const resume = () => {
        paused = false;
        start();
    };

    root.addEventListener("mouseenter", pause);
    root.addEventListener("mouseleave", resume);
    root.addEventListener("focusin", pause);
    root.addEventListener("focusout", (e) => {
        if (!root.contains(e.relatedTarget)) resume();
    });
    track.addEventListener("touchstart", pause, { passive: true });
    track.addEventListener("touchend", () => setTimeout(resume, 2500), {
        passive: true,
    });
    document.addEventListener("visibilitychange", () =>
        document.hidden ? stop() : start(),
    );

    // ne tourne que lorsqu'il est à l'écran
    if ("IntersectionObserver" in window) {
        new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) start();
                else stop();
            },
            { threshold: 0.3 },
        ).observe(root);
    } else {
        start();
    }

    updateDots();
}

export function initCarousels() {
    document.querySelectorAll("[data-carousel]").forEach(setupCarousel);
}
