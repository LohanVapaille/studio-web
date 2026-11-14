/**
 * Transitions entre pages — Lovaweb
 *
 * Sortie : 5 bandes verticales descendent en cascade (celle du milieu en cyan),
 * le logo apparaît avec un loader, puis on navigue.
 * Arrivée : un script inline dans le <head> ajoute `html.pt-arriving` avant le
 * premier rendu (les bandes couvrent déjà l'écran), puis ce module les fait
 * remonter en cascade inverse.
 *
 * L'interception des clics passe par Alpine, sur le <body> du layout :
 *   @click="window.lovaTransition && window.lovaTransition.handle($event)"
 * Si le JS échoue, les liens fonctionnent normalement.
 */
import { gsap } from "gsap";

export const TRANSITION_KEY = "lova:page-transition";

let resolveReady;
/** Résolue quand la page est visible (bandes remontées ou pas de transition). */
export const pageReady = new Promise((resolve) => {
    resolveReady = resolve;
});

const FILE_EXT = /\.(pdf|zip|jpe?g|png|webp|gif|svg|xml|txt|docx?)$/i;

function safeSession(fn) {
    try {
        return fn(window.sessionStorage);
    } catch {
        return null;
    }
}

export function initPageTransition() {
    const root = document.documentElement;
    const el = document.querySelector("[data-page-transition]");

    if (!el) {
        root.classList.remove("pt-arriving");
        resolveReady();
        return;
    }

    const bands = el.querySelectorAll(".pt-band");
    const logo = el.querySelector(".pt-logo");
    const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    let busy = false;

    const hide = () => {
        gsap.set(el, { visibility: "hidden", pointerEvents: "none" });
        gsap.set(bands, { y: 0, yPercent: -100 });
        gsap.set(logo, { autoAlpha: 0 });
    };

    /** Bandes en place → remontée en cascade inverse. */
    const reveal = () =>
        new Promise((resolve) => {
            gsap.set(el, { visibility: "visible", pointerEvents: "auto" });
            gsap.set(bands, { y: 0, yPercent: 0 });
            gsap.set(logo, { autoAlpha: 1, y: 0 });
            root.classList.remove("pt-arriving");

            gsap.timeline({
                onComplete() {
                    hide();
                    busy = false;
                    resolve();
                },
            })
                .to(logo, { autoAlpha: 0, y: -12, duration: 0.25, ease: "power2.in" })
                .to(
                    bands,
                    {
                        yPercent: -100,
                        duration: 0.7,
                        ease: "power3.inOut",
                        stagger: { each: 0.06, from: "end" },
                    },
                    "-=0.05",
                );
        });

    /** Descente des bandes + logo, puis navigation. */
    const leave = (href) => {
        if (busy) return;
        busy = true;

        gsap.set(el, { visibility: "visible", pointerEvents: "auto" });

        gsap.timeline({
            onComplete() {
                safeSession((s) => s.setItem(TRANSITION_KEY, "1"));
                window.location.href = href;
            },
        })
            .fromTo(
                bands,
                { y: 0, yPercent: -100 },
                { yPercent: 0, duration: 0.6, ease: "power3.inOut", stagger: 0.06 },
            )
            .fromTo(
                logo,
                { autoAlpha: 0, y: 12 },
                { autoAlpha: 1, y: 0, duration: 0.3, ease: "power2.out" },
                "-=0.15",
            );
    };

    // Arrivée sur la page
    if (root.classList.contains("pt-arriving") && !reduced) {
        reveal().then(resolveReady);
    } else {
        root.classList.remove("pt-arriving");
        hide();
        resolveReady();
    }

    // Retour arrière depuis le cache (bfcache) : la page revient avec les bandes baissées
    window.addEventListener("pageshow", (event) => {
        if (event.persisted && busy) reveal();
    });

    window.lovaTransition = {
        /** Appelée par Alpine au clic sur le body. */
        handle(event) {
            if (reduced || event.defaultPrevented) return;
            if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

            const link = event.target.closest("a[href]");
            if (!link) return;
            if (link.target && link.target !== "_self") return;
            if (link.hasAttribute("download") || link.hasAttribute("data-no-transition")) return;

            const url = new URL(link.href, window.location.href);
            if (url.origin !== window.location.origin) return;
            if (!/^https?:$/.test(url.protocol) || FILE_EXT.test(url.pathname)) return;
            // Même page (ancre ou lien vers soi-même) : pas de rideau, défilement natif.
            // On ignore le « / » final : route('home') n'en a pas, l'URL courante si.
            const samePath = (a, b) => a.replace(/\/+$/, "") === b.replace(/\/+$/, "");
            if (samePath(url.pathname, window.location.pathname) && url.search === window.location.search) return;

            event.preventDefault();
            leave(url.href);
        },
    };
}
