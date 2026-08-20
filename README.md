# Zlaark Deals

Six animated Elementor widgets plus a **Deals** manager in the WordPress sidebar. Built as a more polished answer to the ecommerce-platforms.com homepage pattern: ranked top picks, a scorecard comparison, a trust-stat strip and a logo marquee, all driven by one set of deals you enter once.

## Install

1. Copy the `zlaark-deals-pro` folder into `wp-content/plugins/`.
2. Activate **Zlaark Deals** in *Plugins*.
3. Two categories are created for you: **Discount Deals** and **Web Hosting**.

Elementor 3.5+ is required for the widgets. The Deals manager works without it.

Every widget except the Logo Marquee (which is meant to run edge-to-edge) has a **Content Max Width** control defaulting to **1480px**, so stacking Hero, About, Deals Grid, Top Picks, Comparison, Stats and the Navbar down a page lines every one of them up to the same column width automatically.

## Adding deals

**Zlaark Deals → Add Deal** in the sidebar. One deal feeds every widget:

| Field | Used by |
|---|---|
| Title | all widgets |
| Deal Image / Logo | Deals Grid, Top Picks, Comparison, Marquee |
| Tagline | Deals Grid, Top Picks, Comparison (rows) |
| Pricing / Original Price | Deals Grid, Top Picks, Comparison |
| Badge | Deals Grid (animated corner ribbon) |
| Rank Label | Top Picks (e.g. *Editor's Choice*) |
| Rating (0–10) | the animated rating ring |
| Highlights | Top Picks — one per line, staggered checklist |
| Score Breakdown | Comparison — `Label\|9.4` per line, drives the bars |
| Button Text / URL / new tab | all card widgets |
| Deal Categories | how every widget filters |

Categories live at **Zlaark Deals → Categories**. Use *Page Attributes → Order* for manual sorting.

## The widgets

All widgets sit under the **Zlaark Deals** category in the Elementor panel, and each one has a **Motion** section in the Style tab: scroll-reveal effect (rise / fade / zoom / slide / 3D flip), stagger delay, 3D cursor tilt, hover shine sweep and a rotating conic border glow.

### Zlaark Hero
Aurora background with drifting colour blooms, a panning grid, five floating orbs, a word-by-word masked title reveal, a gradient highlight phrase with a self-drawing underline, a pulsing status pill, magnetic buttons, counting stat strip, and a counter-rotating double ring behind a parallaxing, bobbing image. Four layouts; every colour, size and motion toggle is exposed.

### Zlaark Hero Classic
The original text-and-image hero, elevated: eyebrow pill with icon, word-by-word title with a slim gradient underline on the highlight phrase, a feature checklist rendered as frosted pill chips that lift on hover, dual buttons, and a social-proof row with an overlapping avatar stack and star rating. The media sits in a glass panel (with a soft glossy sheen), browser-window chrome or plain frame, on top of an optional blurred accent glow, with a floating stat card and a live badge overlapping its edges, over a drifting gradient mesh and dot grid.

### Zlaark Hero Fresh
A stripped-down, flat hero: headline (with an optional accent-coloured highlight phrase), a description with an optional highlighted lead-in phrase, and a single image on either side — nothing else by default. No eyebrow, no badges, no gradients, no background motion unless you turn them on. Ships with a **Colour Theme** switch (Monochrome — black & orange, or Fresh — green & yellow; no purple/pink anywhere) plus optional accent-colour overrides. Buttons are entirely optional (one switch turns both off for a pure text + image hero) and, when no image is set, the media side reserves a clean dashed placeholder frame instead of collapsing.

### Zlaark Navbar
Logo (text or image), a centred capsule menu whose blue indicator pill glides to whichever item the pointer is over and settles back on the active one, plus a text link and a solid CTA. Items come from a repeater or any WordPress menu, with optional auto-detection of the current page. Sticky with a shrink-on-scroll state, a magnetic CTA, a staggered drop-in on load, and a hamburger panel below a width you choose.

### Zlaark Hero Bento
A collage of floating cards wired together with elbow connectors on one side, oversized display type on the other. Four image cards (portrait, outlined panel with a centre icon and dashed guide, dark tile with a stat pill, wide tile), three colour-coded icon squares, nine outline placeholder tiles, and animated connector wires that draw themselves on scroll. Cards float out of phase, shift by depth under the cursor and pop on hover. Collage can sit left or right.

### Zlaark Deals Grid
Your deal cards, filtered by category. Optional front-end **category tabs** with a sliding pill indicator. Cards tilt toward the cursor, lift, sweep a shine across, and light a rotating gradient border. Logos push forward in 3D on hover.

Six **Card Layout** options, so the same deals can be shown however suits the row width:

| Layout | Best for | Description |
|---|---|---|
| Row | 1–2 columns | Logo beside a stacked title/price/button |
| Stack | 2–3 columns | Logo above everything |
| Panel | 3–4 columns | Tall card, hairline divider, full-width button pinned to the base |
| Split | 1–2 wide rows | Details left, price and CTA right behind a divider — reads like a table row |
| Compact | 1–2 columns | Dense single-line rows — small logo, everything inline, tiny button |
| Spotlight | 3–4 columns | Full-bleed image card with a dark scrim and white overlay text |

**Columns** goes up to 6, or turn on **Auto-fit Columns** to skip a fixed count entirely and let the row pack in as many cards as fit a chosen **Minimum Card Width**. **Equal Card Heights** (default on) stretches every card in a row to the tallest so buttons line up, and **Accent Bar on Card** (default on) grows a gradient rule across the top on hover.

### Zlaark About
A "who we are" section: an image on one side, with an eyebrow, a word-by-word animated title with a gradient highlight, description, a **Mission Points** repeater (icon + title + text, cascading in on scroll), a button and an inline stat row on the other. A floating stat card overlaps the image corner, and a dot-grid backdrop sits behind everything.

Five **Media Style** options:

| Style | Description |
|---|---|
| Collage | Two overlapping framed photos |
| Single | One framed image |
| Stacked | Two layered photos, offset and gently counter-tilted |
| Shape | One image over a blurred colour blob, ringed by a rotated dashed frame |
| Grid | Four images in a 2×2 grid, each with its own hover lift |

**Image Height** (responsive) locks every frame in the chosen style to a fixed height and crops the photo to fill it — leave it empty to size by the image's natural proportion instead. Media side, column width and gap are all controls, and the frames bob gently and parallax with the cursor in every style.

### Zlaark Top Picks
Ranked cards with a counter-rotating gradient medal, an animated rating ring that draws its arc and counts its number up, a staggered highlights checklist, and an optional gradient-bordered spotlight on the #1 pick.

### Zlaark Comparison
Scorecards or compact rows built from the Score Breakdown field. Bars fill on scroll with a travelling sheen, numbers count up, rows lift and logos rotate on hover.

### Zlaark Stats
Trust numbers that roll up from zero when scrolled into view, with an orbiting accent ring and optional animated gradient fill on the digits.

### Zlaark Logo Marquee
Seamless infinite scroll in either direction, sourced from a deal category or a manual list. Pauses on hover, greyscale until hover, faded edges.

## Differentiating by category

Every widget has a multi-select **Category** control. Drop the Deals Grid twice on a page, pick *Discount Deals* in one and *Web Hosting* in the other, and you get the two-column layout from your reference — leave the Heading empty and each block titles itself from its category.

## Accessibility & performance

- Full `prefers-reduced-motion` support: all ambient loops, reveals, tilts and parallax switch off while the layout stays intact.
- CSS and JS are registered but only enqueued on pages that actually use a widget.
- Reveals and counters use a single shared `IntersectionObserver`; pointer effects are `requestAnimationFrame`-throttled.

## Structure

```
zlaark-deals-pro/
├── zlaark-deals-pro.php                        bootstrap, constants, assets
├── uninstall.php
├── includes/
│   ├── class-zlaark-deals-post-type.php    CPT, taxonomy, default terms, columns
│   ├── class-zlaark-deals-meta.php         Deal Details meta box, save, parsers
│   └── class-zlaark-deals-elementor.php    widget category + registration
├── widgets/
│   ├── class-zlaark-widget-base.php        shared motion + query controls
│   ├── class-zlaark-hero-widget.php
│   ├── class-zlaark-deals-widget.php
│   ├── class-zlaark-top-picks-widget.php
│   ├── class-zlaark-compare-widget.php
│   ├── class-zlaark-stats-widget.php
│   └── class-zlaark-marquee-widget.php
└── assets/
    ├── css/frontend.css                    layout + the whole motion system
    ├── css/admin.css
    ├── js/frontend.js                      reveal/count/tilt/magnetic/tabs runtime
    └── js/admin.js
```
