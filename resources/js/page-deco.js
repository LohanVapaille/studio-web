/**
 * Décor de page — petits éléments bleus et blancs sur toute la hauteur de la page.
 *
 * - Répartis régulièrement le long de la page (sous le hero), surtout sur les bords
 *   pour ne pas gêner la lecture.
 * - Chaque élément flotte en continu (animation CSS, durée/amplitude aléatoires).
 * - Au scroll, ils se déplacent à des vitesses différentes selon leur profondeur
 *   (parallaxe) et certains tournent légèrement (ScrollTrigger, scrub).
 *
 * Blade : <x-page-deco /> en premier enfant du contenu de la page.
 */
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

/* Réglages — ajuster ici la densité, les formes et les couleurs */
export const DECO = {
    spacing: 190, // px de page par élément (plus petit = plus dense)
    mobileSpacing: 320,
    max: 50,
    mobileMax: 20,
    size: [7, 24], // px
    edgeBias: 0.75, // part des éléments placés sur les bords (0 → 1)
    types: ["dot", "dot", "ring", "plus", "square", "pill"],
    tones: ["blue", "cyan", "white", "white", "blue", "outline"],
    parallax: 260, // décalage vertical max (px) sur toute la page, pour la profondeur 1
};

const rand = (min, max) => min + Math.random() * (max - min);
const pick = (list) => list[Math.floor(Math.random() * list.length)];

export function initPageDeco() {
    const layer = document.querySelector("[data-page-deco]");
    if (!layer) return;

    const host = layer.parentElement;
    const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const mobile = window.matchMedia("(max-width: 768px)").matches;

    // on commence sous le hero, qui a déjà ses propres formes
    const hero = host.querySelector("[data-hero]");
    const total = host.scrollHeight;
    const startY = hero ? hero.offsetTop + hero.offsetHeight * 0.85 : 0;
    const usable = Math.max(total - startY, 0);

    const count = Math.min(
        Math.floor(usable / (mobile ? DECO.mobileSpacing : DECO.spacing)),
        mobile ? DECO.mobileMax : DECO.max,
    );
    if (!count) return;

    const step = usable / count;
    const items = [];
    const frag = document.createDocumentFragment();

    for (let i = 0; i < count; i++) {
        const type = pick(DECO.types);
        const tone = pick(DECO.tones);
        const size = Math.round(rand(...DECO.size) * (mobile ? 0.85 : 1));
        const depth = rand(0.2, 1); // 1 = premier plan : plus gros, plus rapide
        const onEdge = Math.random() < DECO.edgeBias;
        const left = Math.random() < 0.5;
        const x = onEdge ? (left ? rand(1.5, 13) : rand(87, 98.5)) : rand(14, 86);
        const y = startY + step * i + rand(0.1, 0.9) * step;

        const el = document.createElement("span");
        el.className = `deco deco--${type} deco--${tone}`;
        el.style.left = `${x}%`;
        el.style.top = `${(y / total) * 100}%`;
        el.style.setProperty("--size", `${Math.round(size * (0.7 + depth * 0.5))}px`);
        el.style.setProperty("--o", (onEdge ? rand(0.55, 0.95) : rand(0.25, 0.45)).toFixed(2));
        // flottement : chaque élément a son propre rythme
        el.style.setProperty("--dur", `${rand(7, 13).toFixed(1)}s`);
        el.style.setProperty("--delay", `${rand(-13, 0).toFixed(1)}s`);
        el.style.setProperty("--dx", `${rand(6, 16).toFixed(1)}px`);
        el.style.setProperty("--dy", `${rand(8, 20).toFixed(1)}px`);
        el.style.setProperty("--rot", `${rand(-25, 25).toFixed(0)}deg`);
        el.innerHTML = '<span class="deco-inner"></span>';

        frag.appendChild(el);
        items.push({ el, depth, spin: type === "dot" ? 0 : rand(-120, 120) });
    }
    layer.appendChild(frag);

    if (reduced) return;

    // parallaxe au scroll sur toute la page
    const tl = gsap.timeline({
        scrollTrigger: {
            trigger: host,
            start: "top top",
            end: "bottom bottom",
            scrub: 0.8,
        },
    });
    items.forEach(({ el, depth, spin }) => {
        tl.to(el, { y: -DECO.parallax * depth, rotation: spin * depth, ease: "none" }, 0);
    });
}
