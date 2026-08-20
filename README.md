# Zlaark Deals

Fifteen Elementor widgets plus a **Deals** manager in the WordPress sidebar. One set of deals, entered once, feeds every widget: the homepage scorecard, the deals index, the single-deal offer panel, ranked picks, trust stats and a logo marquee.

Version 4.1.0 · Requires PHP 7.4 · Elementor 3.5+ for the widgets (the Deals manager works without it).

**Elementor Pro is not required.** Every widget uses only free Elementor APIs.
Headers and footers come from *Header Footer Elementor* (free), and single deal
pages are handled by the plugin itself rather than Pro's Theme Builder.

---

## Before you upgrade

**Take a database backup, then export your deals.** Versions before 3.2.0 deleted every deal when the plugin was removed from the Plugins screen. That is now opt-in and off by default, but the safe order is: back up → upgrade → export.

---

## Install

1. Copy the plugin folder into `wp-content/plugins/`.
2. Activate **Zlaark Deals** in *Plugins*.
3. Two categories are created for you: **Discount Deals** and **Web Hosting**.
4. Go to **Zlaark Deals → Settings** and check the two options below.

### Settings

| Setting | Default | What it does |
|---|---|---|
| **Widget fonts** | on | Loads Bricolage Grotesque, Instrument Sans and IBM Plex Mono. All three are SIL Open Font License - free for commercial use. Turn off only if your theme already loads them. |
| **Delete all data on uninstall** | **off** | Leave this off. With it on, deleting the plugin permanently removes every deal and category, bypassing the Trash. Deactivating never deletes anything either way. |

**Export / Import** lives on the same screen. Export writes every deal, its meta and its categories to JSON - use it as a backup before upgrades, and to move a catalogue between staging and production. Import matches on slug, so re-importing updates rather than duplicating.

### If the fonts don't appear

The plugin enqueues them directly on `wp_enqueue_scripts` at priority 5, because Elementor resolves widget style dependencies after `wp_head` has printed on some themes. If the widgets still render in the system font, the cause is almost always a theme or add-on setting typography at higher specificity - check for Elementor global fonts and for other Elementor add-on plugins doing their own resets.

To self-host the fonts instead and drop the third-party connection:

```php
add_filter( 'zlaark_deals_fonts_url', function () {
    return get_stylesheet_directory_uri() . '/fonts/zlaark-fonts.css';
} );
```

---

## Adding a deal

**Zlaark Deals → Add Deal.** The fields are grouped into four blocks. You only need the first seven; the rest make the card carry more than any competitor's.

### Required

| Field | Notes |
|---|---|
| Title | The brand or offer name |
| Deal Image / Logo | Square lockup reads best |
| Deal Categories | How every widget filters |
| Offer Type | Coupon / Exclusive / Free trial / Free plan / Seasonal |
| Offer Headline | For offers that aren't a monthly price - "60-day free trial". Max 40 chars |
| Button URL | The affiliate link |
| Rating **or** Score Breakdown | See *Scores*, below |

### Recommended

Pricing, Original Price, **Renewal Price**, Term Length, Coupon Code, Tested On, Last Verified, Verdict, Score Breakdown, Highlights, Full Review URL.

The renewal price is the single most trust-building line on the card and the one every competitor hides. Fill it in.

### Optional

Expires On, Refund Window, Best For, Not For, Pros, Cons, Reviewer, Badge, Rank Label.

### Computed for you - never type these

Nine values are derived at render time, so they can't go stale or contradict each other. The editor screen shows a live **"Computed for you"** readout as you type.

| Value | Derived from |
|---|---|
| Discount % | Price + Original Price. **Rounded down** - 61.5% advertises as 61%, never 62% |
| Annual saving | (Original − Price) × 12 |
| First-term total | Price × Term Length |
| Overall score | Mean of the Score Breakdown, falling back to Rating |
| Score colour | The 0–10 ramp: ≥8.0 green, ≥6.5 amber, below that red |
| Days remaining | Expires On |
| "Ends in 6 days" | Expires On - only ever renders inside 14 days |
| "Verified 6 days ago" | Last Verified |
| Expired | Expires On - **removes the deal from every widget automatically** |

### Scores

Enter the breakdown as one `Label|9.4` per line:

```
Speed|9.6
Uptime|9.4
Support|8.8
Value|7.2
```

The headline score is the **mean of these**, so it can never disagree with the bars printed beneath it. A typed Rating is only used when there's no breakdown.

### Expiry

Leave **Expires On** empty for evergreen offers. Set it and the deal disappears from every widget the day after it passes - a deals site showing a lapsed coupon loses trust instantly, and this must not depend on anyone remembering.

---

## Building the pages

All widgets sit under **Zlaark Deals** in the Elementor panel. Every one has a **Content Max Width** control defaulting to 1240px, so stacking them down a page lines them all up.

### Header and footer

Neither lives on a page - both go in a **template** that every page inherits.
This site already has *Header Footer Elementor* installed, so:

**Templates → Header Footer Builder → Add New**, choose *Header* or *Footer*,
set the display rule to *Entire Site*, then build inside it.

| | Widget | Notes |
|---|---|---|
| Header | **Zlaark Navbar** | Logo, menu (repeater or a WP menu), sticky with shrink-on-scroll, one CTA |
| Footer | **Zlaark Footer** | Brand column with live figures, three link columns, the disclosure block, legal strip |

**Use one header only.** The live site currently renders the theme's Blocksy
header *and* a Zlaark Navbar stacked on top of each other - pick whichever you
prefer and delete the other. Two headers is the single most visible bug on the
site today.

The footer's first figure and its "Last catalogue sweep" date both read from the
catalogue, so neither can go stale. The disclosure block is a titled section
rather than a sentence buried in an about-us paragraph - that is a legal
requirement for affiliate links, and it reads as confidence rather than
small print.

### Homepage - one widget

Drop **Zlaark Homepage** on a blank page and the whole architecture renders at once: hero, scorecard, the dark methodology band, live deals, editor's picks, the logo marquee and a closing call to action.

Each section has its own panel in the Elementor sidebar with a **Show This Section** toggle and its own copy. One query feeds every section, so no two blocks can disagree about a price or a score.

| Panel | What it renders |
|---|---|
| 01 · Hero | Headline with a highlight phrase, sub-line, CTA, and a **savings scoreboard** built from your biggest current discounts |
| 02 · Scorecard | Top deals by score, capped with each deal's **Rank Label**, bars from the Score Breakdown |
| 03 · Methodology band | The one dark section. Receipts repeater; the first figure can auto-fill from the live deal count |
| 04 · Live deals | Cards sorted by biggest saving, with coupon, renewal price and verification |
| 05 · Editor's picks | Three ranked cards |
| 06 · Browse by category | Tiles with live per-category counts - doubles as navigation |
| 07 · Expiring this week | Only renders when something is genuinely expiring, so a countdown is never fake |
| 08 · How we test | Four numbered steps. Numbered because it is a real sequence |
| 09 · Trusted by | Logo marquee, built from deals that have an image |
| 10 · About us | The people who do the testing - photo, name, role, one line each |
| 11 · Questions | Expandable Q&A, and it emits **FAQPage** markup for search results |
| 12 · Closing CTA | Heading, body, and any signup form shortcode |

Every section has a **Show This Section** toggle, and each one hides itself when
it has nothing to say - no empty "Ending soon" with no deadlines, no category
tiles with one category.

Sections bleed to the viewport edge automatically, so the dark band and the marquee run full width without needing separate Elementor rows.

**Prefer to build it by hand?** Every individual widget is still there - Hero Fresh, Comparison, Stats, About, Deals Grid, Top Picks and Logo Marquee - and can be stacked in the same order.

### The card

Every deal card - scorecard column, ranked pick, and grid card - is built from
the same slots:

**brand row** (logo, name, offer type and tested date, score) → **price** (with
the struck original and the savings kept beside it) → **body slot** → **terms**
(renewal, refund, "verified N days ago") → **action**.

The body slot has a fallback chain: **score bars**, else the **highlights**
checklist, else the **tagline**. Something always fills it, which is what stops
a thinly-filled deal rendering as a hairline floating above an empty gap. The
slot also absorbs the slack when cards in a row stretch to match heights, so a
sparse card is *compact* rather than tall and empty.

Deals are deduplicated by title, because with no scores every deal ties and the
same brand can otherwise win several places in one row.

### Deals page - `/deals`

Drop **Zlaark Deals Index** on a blank page and set *Number of Deals* high enough to cover the catalogue (it filters client-side, so one query serves every filter).

- **Show At A Time** - 24 is a good default; a "Show more" button appears beyond it.
- **Comparison Page URL** - where the compare tray sends people. Selected IDs arrive as `?deals=12,34`.
- Filters, sort and search write to the query string, so a filtered view is linkable and indexable.

### Comparison page

The Deals Index compare tray sends the visitor to a page with `?deals=12,34` on
the URL. To make that page work:

1. Create a page (`/compare/`) and drop in **Zlaark Comparison**, columns mode.
2. Under **Comparison Page**, turn on **Accept Deals From The Compare Tray**.
3. Point the index widget's *Comparison Page URL* at it.

With the toggle on and IDs on the URL, those deals are shown in the order they
were ticked and the category filter is ignored. Without IDs the widget falls
back to its normal category behaviour, so the page is never empty.

### Single deal page - no Elementor Pro needed

Building a single template for a custom post type needs Pro's Theme Builder, so
the plugin does it for you instead. **Zlaark Deals → Settings → Single deal
pages** offers three options:

| Option | Result |
|---|---|
| **Beside the content** (default) | Panel in a right-hand column, sticky on scroll, above the content on mobile |
| **Above the content** | Panel first, then the article |
| **Off** | Nothing injected - place the **Zlaark Deal Panel** widget yourself |

The injected panel is the same markup the widget renders, from one shared file,
so the two cannot drift apart. It stands down automatically when a deal has been
built with Elementor, so a hand-built page is never doubled up.

Choose **Off** only if you have Elementor Pro and would rather lay the page out
by hand.

### Hosting Finder

Use **Zlaark Comparison** in columns mode, not a grid of raw content. Cards must render fields, never post content - that is what made the old Finder render one column thousands of pixels tall.

---

## Theme

The design system is **Verified Green**: a light site with exactly one dark band.

| Token | Value | Use |
|---|---|---|
| `--zd-accent` | `#0b7a4f` | Primary buttons, prices, links |
| `--zd-accent-2` | `#065f42` | Hover and pressed |
| `--zd-ember` | `#c2410c` | Countdowns only - under 5% of any screen |
| `--zd-ink` | `#0a1310` | The dark band, display type |
| `--zd-body` | `#4a5a52` | Body copy |
| `--zd-score-good/fair/weak` | `#0b7a4f` / `#a16207` / `#b91c1c` | The rating ramp |

Every pairing clears WCAG AA. Three radii only - 8px, 16px, 28px - with 999px reserved for the nav CTA.

Override any of them per-widget from the Elementor Style tab, or globally in your child theme:

```css
.zd-deals, .zd-index, .zd-panel, .zd-compare {
    --zd-accent: #0b7a4f;
}
```

**Motion.** Scroll reveal and hover lift are on. 3D cursor tilt, hover shine and the rotating border glow default to **off** - ambient motion on a comparison page costs trust. Count-ups and score bars stay, because they animate data. Everything respects `prefers-reduced-motion`.

---

## Search results

Structured data is emitted automatically from the fields above - no extra work:

- **Product + Offer + AggregateRating + Review** on single deal pages
- **ItemList** on any page rendering two or more deals

`Offer.priceValidUntil` comes from Expires On, so Google knows the deal is live. Validate with Google's Rich Results Test after your first few deals are filled in.

---

## Structure

```
zlaark-deals-pro/
├── zlaark-deals-pro.php                    bootstrap, fonts, assets
├── uninstall.php                           gated behind an opt-in setting
├── includes/
│   ├── class-zlaark-deals-settings.php     settings, JSON export/import
│   ├── class-zlaark-deals-post-type.php    CPT, taxonomy, admin columns
│   ├── class-zlaark-deals-computed.php     the nine derived values
│   ├── class-zlaark-deals-meta.php         Deal Details meta box
│   ├── class-zlaark-deals-schema.php       JSON-LD
│   └── class-zlaark-deals-elementor.php    widget registration
├── widgets/                                15 widgets + shared base
└── assets/                                 frontend + admin CSS/JS
```

---

## Tests

No WordPress install needed - these run against stubs.

```
php tests/register-widgets.php   # controls build + render with defaults
php tests/render-widgets.php     # render against a populated catalogue
php tests/schema.php             # JSON-LD output
php tests/markup.php             # ?deals= parsing + accessibility of the markup
```

`render-widgets.php` runs three fixture deals through every widget - one fully
filled, one carrying only the required fields, and one expired - so both sides
of every `if` branch and every computed value get exercised. It also checks the
markup comes back tag-balanced.

Loads every widget and runs `register_controls()` the way the Elementor editor
bootstrap does, then calls `render()` with each control's default. Any fatal,
warning or notice is a failure.

This matters more than it looks: Elementor calls `_doing_it_wrong()` when a
control is added while no controls section is open, and that notice is printed
into the editor's JSON config. The editor then can't parse its own config and
**hangs on the loading screen forever** - with no error shown. This harness
catches that, plus duplicate control IDs, unclosed sections and repeaters with
no fields.

It also reports which widgets hit the database during control registration,
since that runs on every editor load.

`markup.php` covers the two things the others miss: `?deals=` is parsed from a
query string, so it is fed SQL injection, script tags, negatives and overlong
lists; and every widget's emitted markup is checked for missing alt text,
skipped heading levels, unnamed links and buttons, and unlabelled inputs.

## Accessibility

Full `prefers-reduced-motion` support. Touch targets meet 44px on coarse pointers. Every colour pairing is measured against WCAG AA. CSS and JS load only on pages that use a widget; reveals and counters share one `IntersectionObserver`.
