/**
 * Intro + hero animé — Lova Web
 * « On construit votre site sous vos yeux »
 *
 * 1. Le point       : un point cyan apparaît et pulse 2 fois
 * 2. La division    : il se divise en formes qui partent en spirale
 * 3. L'assemblage   : les formes se déforment et dessinent la maquette d'un site (desktop)
 * 4. Le démontage   : la maquette éclate, chaque forme rejoint sa place dans le hero
 * 5. Le titre       : lettres masquées, sous-titre en fondu + flou, CTA
 *
 * Le contenu du hero est dans le HTML dès le départ. L'intro est une couche fixe
 * par-dessus, activée par un script inline (`html.intro-pending`) dans
 * <x-hero-intro />. Sans JS, la page s'affiche normalement, sans formes.
 */
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { pageReady } from "./page-transition";

gsap.registerPlugin(ScrollTrigger);

/* ==========================================================================
   DONNÉES DES FORMES — c'est ici qu'on ajuste tout
   --------------------------------------------------------------------------
   type   : square | circle | ring | triangle | plus | pill
   style  : glass | gradient | outline
   depth  : front (grandes, verre) | mid (pleines, dégradé) | back (petites, contour)
   size   : [largeur, hauteur] en px de la forme géométrique (desktop)
   hero   : position finale du centre, en % de la largeur / hauteur du hero
   mobile : position sur mobile, ou false pour masquer la forme (6 max)
   wire   : rôle dans la maquette, offset (px) depuis le centre de l'écran.
            Avec `block` : la forme se déforme en un bloc w × h, rayon r.
            Sans `block` : la forme garde sa géométrie, mise à l'échelle sur w.
   ========================================================================== */
export const SHAPES = [
    {
        id: "nav",
        type: "pill",
        style: "gradient",
        depth: "mid",
        size: [132, 46],
        hero: { x: 13, y: 20 },
        mobile: { x: 2, y: 26 },
        wire: { x: 0, y: -112, w: 400, h: 30, r: 15, block: "nav" },
    },
    {
        id: "image",
        type: "square",
        style: "glass",
        depth: "front",
        size: [160, 160],
        hero: { x: 86, y: 25 },
        mobile: { x: 97, y: 16 },
        wire: { x: -95, y: 18, w: 190, h: 170, r: 16, block: "glass" },
    },
    {
        id: "photo-triangle",
        type: "triangle",
        style: "gradient",
        depth: "mid",
        size: [76, 76],
        hero: { x: 81, y: 80 },
        mobile: { x: 95, y: 72 },
        wire: { x: -122, y: 54, w: 56 },
    },
    {
        id: "photo-sun",
        type: "circle",
        style: "gradient",
        depth: "mid",
        size: [58, 58],
        hero: { x: 6, y: 56 },
        mobile: { x: 4, y: 54 },
        wire: { x: -60, y: -22, w: 30 },
    },
    {
        id: "text-1",
        type: "ring",
        style: "glass",
        depth: "front",
        size: [124, 124],
        hero: { x: 17, y: 85 },
        mobile: { x: 6, y: 92 },
        wire: { x: 105, y: -46, w: 170, h: 12, r: 6, block: "line" },
    },
    {
        id: "text-2",
        type: "plus",
        style: "outline",
        depth: "back",
        size: [52, 52],
        hero: { x: 78, y: 6 },
        mobile: false,
        wire: { x: 90, y: -18, w: 140, h: 12, r: 6, block: "line" },
    },
    {
        id: "text-3",
        type: "pill",
        style: "outline",
        depth: "back",
        size: [86, 30],
        hero: { x: 95, y: 55 },
        mobile: false,
        wire: { x: 75, y: 10, w: 110, h: 12, r: 6, block: "line" },
    },
    {
        id: "cta",
        type: "square",
        style: "gradient",
        depth: "mid",
        size: [66, 66],
        hero: { x: 21, y: 9 },
        mobile: { x: 94, y: 97 },
        wire: { x: 65, y: 58, w: 90, h: 30, r: 10, block: "cta" },
    },
    {
        id: "handle-top",
        type: "ring",
        style: "outline",
        depth: "back",
        size: [34, 34],
        hero: { x: 3, y: 34 },
        mobile: false,
        wire: { x: -220, y: -150, w: 16 },
    },
    {
        id: "handle-bottom",
        type: "circle",
        style: "outline",
        depth: "back",
        size: [24, 24],
        hero: { x: 97, y: 90 },
        mobile: false,
        wire: { x: 220, y: 150, w: 12 },
    },
];

/** Comportement par profondeur. */
const DEPTHS = {
    front: { parallax: 30, scroll: 1.5, float: 1.2, opacity: 1, zIndex: 3 },
    mid: { parallax: 18, scroll: 1.0, float: 1.0, opacity: 1, zIndex: 2 },
    back: { parallax: 8, scroll: 0.6, float: 0.7, opacity: 0.5, zIndex: 1 },
};

const CONFIG = {
    storageKey: "lova:intro-played",
    mobileQuery: "(max-width: 900px)",
    mobileMaxShapes: 6,
    mobileSizeScale: 0.55,
    repelRadius: 150, // px autour du curseur
    repelStrength: 55, // px de poussée max
    floatPeriod: [8, 14], // s, aléatoire par forme
};

/* ========================================================================== */

const rand = (min, max) => min + Math.random() * (max - min);
const mq = (query) => window.matchMedia(query).matches;

function safeSession(fn) {
    try {
        return fn(window.sessionStorage);
    } catch {
        return null;
    }
}

const GEO_SVG = {
    triangle:
        '<svg viewBox="0 0 100 100" focusable="false"><path d="M50 16 L86 80 L14 80 Z"/></svg>',
    plus: '<svg viewBox="0 0 100 100" focusable="false"><path d="M40 12h20v28h28v20H60v28H40V60H12V40h28z"/></svg>',
};

function geoRadius(type, w, h) {
    switch (type) {
        case "circle":
        case "ring":
            return Math.min(w, h) / 2;
        case "pill":
            return h / 2;
        case "square":
            return w * 0.28;
        default:
            return Math.min(w, h) * 0.2;
    }
}

/* -------------------------------------------------------------------------- */
/* Construction du DOM des formes                                              */
/* -------------------------------------------------------------------------- */

/*
 * .shape            → position (intro puis position finale)
 *   .shape-scroll   → écartement au scroll
 *     .shape-parallax → parallaxe souris + répulsion
 *       .shape-float  → trajet en 8 + rotation légère
 *         .shape-geo / .shape-block / .shape-flash
 * Chaque couche a son propre transform : aucun conflit entre les animations.
 */
function buildShape(def, sizeScale) {
    const w = Math.round(def.size[0] * sizeScale);
    const h = Math.round(def.size[1] * sizeScale);
    const wire = def.wire || {};
    const morph = Boolean(wire.block);

    const el = document.createElement("div");
    el.className = `shape shape--${def.style} shape--${def.depth}`;
    el.dataset.shape = def.id;
    el.style.width = `${w}px`;
    el.style.height = `${h}px`;
    el.innerHTML = `
        <div class="shape-scroll"><div class="shape-parallax"><div class="shape-float">
            <div class="shape-geo geo--${def.type}">${GEO_SVG[def.type] || ""}</div>
            ${morph ? `<div class="shape-block" data-block="${wire.block}"></div>` : ""}
            <div class="shape-flash"></div>
        </div></div></div>`;

    const shape = {
        def,
        el,
        w,
        h,
        morph,
        radius: geoRadius(def.type, w, h),
        rest: def.type === "circle" ? 0 : rand(-16, 16),
        depth: DEPTHS[def.depth] || DEPTHS.mid,
        scroll: el.querySelector(".shape-scroll"),
        parallax: el.querySelector(".shape-parallax"),
        float: el.querySelector(".shape-float"),
        geo: el.querySelector(".shape-geo"),
        block: el.querySelector(".shape-block"),
        flash: el.querySelector(".shape-flash"),
        final: { x: 0, y: 0 },
    };

    // le flash épouse la pièce de la maquette
    if (morph) {
        Object.assign(shape.flash.style, {
            width: `${wire.w}px`,
            height: `${wire.h}px`,
            borderRadius: `${wire.r}px`,
        });
    }

    return shape;
}

function computeFinal(shapes, hero, mobile) {
    const W = hero.offsetWidth;
    const H = hero.offsetHeight;
    shapes.forEach((s) => {
        const p = mobile ? s.def.mobile : s.def.hero;
        s.final = { x: (p.x / 100) * W, y: (p.y / 100) * H };
    });
}

function placeAtRest(shapes) {
    shapes.forEach((s) => {
        gsap.set(s.el, {
            xPercent: -50,
            yPercent: -50,
            x: s.final.x,
            y: s.final.y,
            rotation: s.rest,
            scale: 1,
            opacity: s.depth.opacity,
            zIndex: s.depth.zIndex,
        });
    });
}

/* -------------------------------------------------------------------------- */
/* Titre lettre par lettre                                                     */
/* -------------------------------------------------------------------------- */

function splitTitle(el) {
    const original = el.innerHTML;
    const label = el.textContent.replace(/\s+/g, " ").trim();
    const chars = [];

    const walk = (node) => {
        [...node.childNodes].forEach((child) => {
            if (child.nodeType === Node.ELEMENT_NODE) {
                walk(child);
                return;
            }
            if (child.nodeType !== Node.TEXT_NODE) return;

            const frag = document.createDocumentFragment();
            child.textContent.split(/(\s+)/).forEach((part) => {
                if (!part) return;
                if (/^\s+$/.test(part)) {
                    frag.appendChild(document.createTextNode(" "));
                    return;
                }
                const word = document.createElement("span");
                word.className = "split-word";
                word.setAttribute("aria-hidden", "true");
                [...part].forEach((ch) => {
                    const c = document.createElement("span");
                    c.className = "split-char";
                    c.textContent = ch;
                    word.appendChild(c);
                    chars.push(c);
                });
                frag.appendChild(word);
            });
            child.replaceWith(frag);
        });
    };

    walk(el);
    el.setAttribute("aria-label", label);

    return {
        chars,
        restore() {
            el.innerHTML = original;
            el.removeAttribute("aria-label");
        },
    };
}

/* -------------------------------------------------------------------------- */
/* Intro                                                                       */
/* -------------------------------------------------------------------------- */

function runIntro(ui, shapes, mobile, onDone) {
    const root = document.documentElement;
    window.scrollTo(0, 0);

    const rect = ui.hero.getBoundingClientRect();
    // centre de l'écran, dans le repère du hero
    const C = { x: window.innerWidth / 2 - rect.left, y: window.innerHeight / 2 - rect.top };
    const n = shapes.length;

    const T = mobile
        ? { pulse: 0.1, split: 0.5, fly: 1.05, layerOut: 1.05, title: 1.4, titleSpan: 0.5 }
        : { pulse: 0.15, split: 0.6, assemble: 1.4, scan: 2.15, fly: 2.6, layerOut: 2.65, title: 3.2, titleSpan: 0.8 };

    // --- état initial --------------------------------------------------------
    const split = splitTitle(ui.title);
    const hl = ui.title.querySelector(".hl");
    gsap.set(split.chars, { yPercent: 110 });
    if (hl) gsap.set(hl, { backgroundSize: "0% 0.38em" });
    gsap.set(ui.eyebrow, { opacity: 0, y: 10 });
    gsap.set(ui.sub, { opacity: 0, filter: "blur(8px)" });
    gsap.set([ui.ctas, ui.points], { opacity: 0, scale: 0.9 });

    shapes.forEach((s) => {
        gsap.set(s.el, { xPercent: -50, yPercent: -50, x: C.x, y: C.y, scale: 0, opacity: 0, rotation: 0 });
        gsap.set(s.flash, { opacity: 0 });
        if (s.block) gsap.set(s.block, { width: s.w, height: s.h, borderRadius: s.radius, opacity: 0 });
    });
    if (ui.scan) gsap.set(ui.scan, { xPercent: -50, yPercent: -50, x: C.x, y: C.y, opacity: 0 });

    let finished = false;
    const finish = () => {
        if (finished) return;
        finished = true;

        split.restore();
        gsap.set([ui.eyebrow, ui.sub, ui.ctas, ui.points], { clearProps: "opacity,filter,transform" });
        shapes.forEach((s) => {
            gsap.set(s.geo, { clearProps: "opacity" });
            s.block?.remove();
            s.flash?.remove();
            gsap.set(s.el, { zIndex: s.depth.zIndex });
        });
        ui.scan?.remove();
        ui.layer?.remove();
        root.classList.remove("intro-pending", "intro-running");
        document.removeEventListener("keydown", onKey);
        onDone();
    };

    const tl = gsap.timeline({ defaults: { ease: "power2.out" }, onComplete: finish });

    const skip = () => tl.progress(1);
    const onKey = (e) => {
        if (e.key === "Escape") skip();
    };
    ui.skip?.addEventListener("click", skip);
    document.addEventListener("keydown", onKey);

    // --- 1. Le point ---------------------------------------------------------
    tl.fromTo(ui.dot, { scale: 0 }, { scale: 1, duration: 0.3, ease: "back.out(3)" }, 0);
    ui.pulses.forEach((pulse, i) => {
        const at = T.pulse + i * 0.2;
        tl.fromTo(pulse, { scale: 1, opacity: 0.7 }, { scale: 7, opacity: 0, duration: 0.5 }, at);
        tl.to(ui.dot, { scale: 1.3, duration: 0.1, yoyo: true, repeat: 1, ease: "power1.inOut" }, at);
    });

    // --- 2. La division ------------------------------------------------------
    tl.to(ui.dot, { scaleX: 1.6, scaleY: 0.6, duration: 0.08, ease: "power1.in" }, T.split - 0.08);
    tl.to(ui.dot, { scale: 0, opacity: 0, duration: 0.15 }, T.split);

    const spiralRadius = mobile ? Math.min(window.innerWidth, window.innerHeight) * 0.28 : 230;

    shapes.forEach((s, i) => {
        const theta = (i / n) * Math.PI * 2 - Math.PI / 2;
        const radius = spiralRadius * rand(0.85, 1.1);
        s.spiralEnd = { x: C.x + Math.cos(theta) * radius, y: C.y + Math.sin(theta) * radius };

        const at = T.split + i * 0.05;
        const setX = gsap.quickSetter(s.el, "x", "px");
        const setY = gsap.quickSetter(s.el, "y", "px");
        const p = { v: 0 };

        tl.to(s.el, { opacity: 1, scale: mobile ? 0.85 : 0.6, duration: 0.3, ease: "back.out(2)" }, at);
        tl.to(
            p,
            {
                v: 1,
                duration: 0.7,
                ease: "expo.out",
                onUpdate() {
                    const a = theta - Math.PI * 1.2 * (1 - p.v);
                    setX(C.x + Math.cos(a) * radius * p.v);
                    setY(C.y + Math.sin(a) * radius * p.v);
                },
            },
            at,
        );
        tl.fromTo(
            s.el,
            { rotation: 0 },
            { rotation: rand(200, 320) * (i % 2 ? 1 : -1), duration: 0.7, ease: "expo.out" },
            at,
        );
    });

    // --- 3. L'assemblage (desktop) -------------------------------------------
    if (!mobile) {
        tl.fromTo(ui.frame, { opacity: 0, scale: 0.96 }, { opacity: 1, scale: 1, duration: 0.5 }, T.assemble + 0.1);

        shapes.forEach((s, i) => {
            const w = s.def.wire;
            const at = T.assemble + i * 0.07;

            tl.to(
                s.el,
                {
                    x: C.x + w.x,
                    y: C.y + w.y,
                    rotation: 0,
                    scale: s.morph ? 1 : w.w / s.w,
                    duration: 0.6,
                    ease: "back.out(1.7)",
                },
                at,
            );

            if (s.morph) {
                tl.to(s.geo, { opacity: 0, duration: 0.25, ease: "none" }, at + 0.05);
                tl.to(s.block, { opacity: 1, duration: 0.2, ease: "none" }, at);
                tl.to(
                    s.block,
                    { width: w.w, height: w.h, borderRadius: w.r, duration: 0.6, ease: "back.out(1.7)" },
                    at,
                );
            }

            // micro-flash blanc à l'emboîtement
            tl.to(s.flash, { opacity: 0.9, duration: 0.08, yoyo: true, repeat: 1, ease: "none" }, at + 0.42);
        });

        // scan lumineux en diagonale
        if (ui.scan) {
            tl.set(ui.scan, { opacity: 1 }, T.scan);
            tl.fromTo(
                ui.scan,
                { maskPosition: "100% 100%" },
                { maskPosition: "0% 0%", duration: 0.45, ease: "power1.inOut" },
                T.scan,
            );
            tl.set(ui.scan, { opacity: 0 }, T.scan + 0.45);
        }

        tl.to(ui.frame, { opacity: 0, scale: 1.04, duration: 0.4 }, T.fly);
    }

    // --- 4. Le démontage : trajectoires courbes vers le hero -----------------
    shapes.forEach((s) => {
        const at = T.fly + rand(0, 0.15);
        const duration = rand(0.8, 1.05);
        const start = mobile ? s.spiralEnd : { x: C.x + s.def.wire.x, y: C.y + s.def.wire.y };
        const end = s.final;

        // point de contrôle : milieu du trajet, poussé vers l'extérieur (effet « éclat »)
        let ox = start.x - C.x;
        let oy = start.y - C.y;
        const len = Math.hypot(ox, oy) || 1;
        ox /= len;
        oy /= len;
        const ctrl = { x: (start.x + end.x) / 2 + ox * 140, y: (start.y + end.y) / 2 + oy * 140 };

        const setX = gsap.quickSetter(s.el, "x", "px");
        const setY = gsap.quickSetter(s.el, "y", "px");
        const p = { t: 0 };

        tl.to(
            p,
            {
                t: 1,
                duration,
                ease: "power2.inOut",
                onUpdate() {
                    const t = p.t;
                    const u = 1 - t;
                    setX(u * u * start.x + 2 * u * t * ctrl.x + t * t * end.x);
                    setY(u * u * start.y + 2 * u * t * ctrl.y + t * t * end.y);
                },
            },
            at,
        );
        tl.to(s.el, { rotation: s.rest, scale: 1, opacity: s.depth.opacity, duration, ease: "power2.inOut" }, at);

        if (s.morph && !mobile) {
            tl.to(
                s.block,
                { width: s.w, height: s.h, borderRadius: s.radius, opacity: 0, duration: 0.45, ease: "power2.inOut" },
                at,
            );
            tl.to(s.geo, { opacity: 1, duration: 0.35 }, at + 0.1);
        }
    });

    tl.to(ui.layer, { opacity: 0, duration: 0.6, ease: "power1.out" }, T.layerOut);
    if (ui.skip) tl.to(ui.skip, { opacity: 0, duration: 0.2 }, T.layerOut);

    // --- 5. Le titre ---------------------------------------------------------
    const each = Math.min(0.04, T.titleSpan / Math.max(split.chars.length, 1));
    const afterTitle = T.title + each * split.chars.length + 0.1;

    tl.to(ui.eyebrow, { opacity: 1, y: 0, duration: 0.5 }, T.title - 0.1);
    tl.to(split.chars, { yPercent: 0, duration: 0.7, ease: "power4.out", stagger: each }, T.title);
    if (hl) tl.to(hl, { backgroundSize: "100% 0.38em", duration: 0.6, ease: "power2.inOut" }, afterTitle - 0.2);
    tl.to(ui.sub, { opacity: 1, filter: "blur(0px)", duration: 0.6 }, afterTitle - 0.3);
    tl.to([ui.ctas, ui.points], { opacity: 1, scale: 1, duration: 0.45, ease: "back.out(1.6)", stagger: 0.08 }, afterTitle);
}

/* -------------------------------------------------------------------------- */
/* Hero au repos : flottement, souris, scroll                                  */
/* -------------------------------------------------------------------------- */

function startIdle(ui, shapes, { mobile, finePointer }) {
    let heroVisible = true;

    ScrollTrigger.create({
        trigger: ui.hero,
        start: "top bottom",
        end: "bottom top",
        onToggle: (self) => {
            heroVisible = self.isActive;
        },
    });

    // --- Flottement en « 8 » (lemniscate), désynchronisé ---------------------
    const floaters = shapes.map((s) => ({
        period: rand(...CONFIG.floatPeriod),
        phase: rand(0, Math.PI * 2),
        ax: rand(10, 22) * s.depth.float,
        ay: rand(10, 18) * s.depth.float,
        rot: rand(3, 8),
        setX: gsap.quickSetter(s.float, "x", "px"),
        setY: gsap.quickSetter(s.float, "y", "px"),
        setR: gsap.quickSetter(s.float, "rotation", "deg"),
    }));
    const amp = { v: 0 };
    gsap.to(amp, { v: 1, duration: 2, ease: "power1.inOut" });

    gsap.ticker.add((time) => {
        if (!heroVisible) return;
        floaters.forEach((f) => {
            const t = (time * Math.PI * 2) / f.period + f.phase;
            f.setX(Math.sin(t) * f.ax * amp.v);
            f.setY(Math.sin(t) * Math.cos(t) * f.ay * amp.v);
            f.setR(Math.sin(t) * f.rot * amp.v);
        });
    });

    // --- Souris : parallaxe par profondeur + répulsion -----------------------
    if (finePointer && !mobile) {
        const movers = shapes.map((s) => ({
            s,
            qx: gsap.quickTo(s.parallax, "x", { duration: 0.9, ease: "power3.out" }),
            qy: gsap.quickTo(s.parallax, "y", { duration: 0.9, ease: "power3.out" }),
        }));
        let mouse = null;
        let raf = 0;

        const update = () => {
            raf = 0;
            if (!heroVisible) return;

            movers.forEach(({ s, qx, qy }) => {
                if (!mouse) {
                    qx(0);
                    qy(0);
                    return;
                }
                const nx = (mouse.x / window.innerWidth) * 2 - 1;
                const ny = (mouse.y / window.innerHeight) * 2 - 1;
                let x = -nx * s.depth.parallax;
                let y = -ny * s.depth.parallax;

                // la couche scroll inclut position + scroll, mais pas parallaxe/flottement
                const r = s.scroll.getBoundingClientRect();
                const dx = r.left + r.width / 2 - mouse.x;
                const dy = r.top + r.height / 2 - mouse.y;
                const dist = Math.hypot(dx, dy) || 1;
                const reach = CONFIG.repelRadius + Math.max(r.width, r.height) / 2;

                if (dist < reach) {
                    const force = (1 - dist / reach) ** 2 * CONFIG.repelStrength;
                    x += (dx / dist) * force;
                    y += (dy / dist) * force;
                }
                qx(x);
                qy(y);
            });
        };

        window.addEventListener(
            "pointermove",
            (e) => {
                if (e.pointerType !== "mouse") return;
                mouse = { x: e.clientX, y: e.clientY };
                if (!raf) raf = requestAnimationFrame(update);
            },
            { passive: true },
        );
        document.documentElement.addEventListener("mouseleave", () => {
            mouse = null;
            update();
        });
    }

    // --- Scroll : les formes s'écartent, le titre monte et s'efface ----------
    const direction = (s) => {
        const dx = s.final.x - ui.hero.offsetWidth / 2;
        const dy = s.final.y - ui.hero.offsetHeight / 2;
        const len = Math.hypot(dx, dy) || 1;
        return { x: dx / len, y: dy / len };
    };
    const distance = (s) => s.depth.scroll * Math.max(window.innerWidth, window.innerHeight) * 0.7;

    const scrollTl = gsap.timeline({
        scrollTrigger: {
            trigger: ui.hero,
            start: 0,
            end: "bottom top",
            scrub: 0.6,
            invalidateOnRefresh: true,
        },
    });
    shapes.forEach((s) => {
        scrollTl.to(
            s.scroll,
            {
                x: () => direction(s).x * distance(s),
                y: () => direction(s).y * distance(s),
                ease: "none",
            },
            0,
        );
    });
    if (ui.inner) scrollTl.to(ui.inner, { y: -40, opacity: 0.35, ease: "none" }, 0);
}

function drawHighlight(ui) {
    const hl = ui.title?.querySelector(".hl");
    if (!hl) return;
    gsap.fromTo(
        hl,
        { backgroundSize: "0% 0.38em" },
        { backgroundSize: "100% 0.38em", duration: 0.9, delay: 0.3, ease: "power2.inOut" },
    );
}

/* -------------------------------------------------------------------------- */
/* Point d'entrée                                                              */
/* -------------------------------------------------------------------------- */

export function initHeroIntro() {
    const root = document.documentElement;
    const hero = document.querySelector("[data-hero]");
    const container = hero?.querySelector("[data-hero-shapes]");

    if (!hero || !container) {
        root.classList.remove("intro-pending");
        return;
    }

    const reduced = mq("(prefers-reduced-motion: reduce)");
    const mobile = mq(CONFIG.mobileQuery);
    const finePointer = mq("(hover: hover) and (pointer: fine)");

    const layer = hero.querySelector("[data-hero-intro]");
    const ui = {
        hero,
        layer,
        inner: hero.querySelector(".hero-inner"),
        title: hero.querySelector("[data-hero-title]"),
        eyebrow: hero.querySelector("[data-hero-eyebrow]"),
        sub: hero.querySelector("[data-hero-sub]"),
        ctas: hero.querySelector("[data-hero-ctas]"),
        points: hero.querySelector("[data-hero-points]"),
        scan: container.querySelector("[data-hero-scan]"),
        dot: layer?.querySelector("[data-intro-dot]"),
        pulses: layer ? [...layer.querySelectorAll("[data-intro-pulse]")] : [],
        frame: layer?.querySelector("[data-intro-frame]"),
        skip: layer?.querySelector("[data-intro-skip]"),
    };

    const defs = mobile ? SHAPES.filter((d) => d.mobile).slice(0, CONFIG.mobileMaxShapes) : SHAPES;
    const shapes = defs.map((def) => buildShape(def, mobile ? CONFIG.mobileSizeScale : 1));
    shapes.forEach((s) => container.appendChild(s.el));
    computeFinal(shapes, hero, mobile);

    // Repositionnement au redimensionnement (avant chaque refresh de ScrollTrigger)
    let introDone = false;
    ScrollTrigger.addEventListener("refreshInit", () => {
        computeFinal(shapes, hero, mobile);
        if (introDone) shapes.forEach((s) => gsap.set(s.el, { x: s.final.x, y: s.final.y }));
    });

    const idle = () => {
        introDone = true;
        container.classList.add("is-ready");
        if (!reduced) startIdle(ui, shapes, { mobile, finePointer });
    };

    const playIntro = !reduced && layer && ui.title && root.classList.contains("intro-pending");

    if (!playIntro) {
        root.classList.remove("intro-pending");
        layer?.remove();
        ui.scan?.remove();
        shapes.forEach((s) => {
            s.block?.remove();
            s.flash?.remove();
        });
        placeAtRest(shapes);
        if (!reduced) drawHighlight(ui);
        idle();
        return;
    }

    root.classList.add("intro-running");
    safeSession((s) => s.setItem(CONFIG.storageKey, "1"));
    pageReady.then(() => runIntro(ui, shapes, mobile, idle));
}
