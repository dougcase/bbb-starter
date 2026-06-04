# The Big Beautiful Build
## Risingline New Site Build Process
**Version:** 2.0 — March 2026

---

## What This Document Is

This is the technical build process for new Risingline client sites — starting from the point where the sitemap and content direction are already decided. It governs everything from base setup through site launch.

It is not a project management document. Client intake, content strategy, and design discovery happen before this process begins.

**Governing principle:** Every decision in this process serves one goal — a fast, accessible, secure, maintainable site that moves qualified visitors toward a conversion action.

---

## Build Quality Standards

These are not post-launch audit targets — they are the standards every phase builds toward from day one. If you build to these throughout, the site is ready to launch when Phase 6 is complete.

| Standard | Target |
|----------|--------|
| Accessibility (pa11y + axe) | Zero errors in site code |
| HTML validation | Zero errors |
| Lighthouse Accessibility | 100 |
| Lighthouse SEO | 100 |
| Lighthouse Performance | 70+ (90+ preferred) |
| Lighthouse Best Practices | 90+ |
| Security headers | All required headers present |
| Schema.org validation | Zero errors |
| Mobile responsiveness | No overflow, touch targets pass |
| Forms | End-to-end delivery confirmed |

---

## How This Document Works

Copy this file to the project root at build kickoff. Work through phases in sequence. Each phase has a trigger condition — don't start a phase until its trigger is met.

Project-specific decisions, content status, and session context go in `build/client-notes.md` — not in this file.

---

## Build Phases Overview

| Phase | Focus | Lead |
|-------|-------|------|
| 1. Site Architecture | Sitemap, conversion paths, nav structure | Doug |
| 2. Base Setup | File structure, BS5, .htaccess, includes, config | cc |
| 3. Brand Implementation | Color tokens, fonts, logo, accessibility baseline | Doug |
| 4. Component Build | Nav, footer, pages, CTAs, forms | Doug + cc |
| 5. Content Integration | Copy, images, meta, NAP, analytics | Doug |
| 6. Pre-Launch | Final checklist + launch | Doug + cc |

---

## Phase 1 — Site Architecture

**Trigger:** Sitemap and content direction confirmed

### 1A. Sitemap Confirmation

All pages defined before any build work begins. Standard pages for professional services sites:

- Home
- About / Our Team
- Services (overview + individual service pages as needed)
- Contact
- Accessibility Statement
- Privacy Policy
- 404

Optional: Blog, Resources/FAQ, Testimonials

Document final approved sitemap in `build/client-notes.md`.

### 1B. Conversion Path Mapping

For each primary visitor type, map the intended path:

```
Entry → Trust-building → CTA → Conversion
Example: Home → Practice Areas → Contact → Phone call
```

Every page must have a defined next step. Pages with no clear path to conversion need a documented rationale. Record paths in `build/client-notes.md`.

### 1C. Navigation Structure

- Maximum two levels (primary nav + one dropdown tier)
- Mobile nav: accordion pattern (see components library)
- Primary CTA visible in header at all breakpoints
- No orphan pages — every page reachable from nav or contextual links

---

## Phase 2 — Base Setup

**Lead:** cc (with Doug oversight)
**Trigger:** Phase 1 complete

### 2A. File & Directory Structure

```
/
├── index.php
├── [page].php
├── css/
│   ├── bootstrap.min.css
│   ├── bootstrap-icons.min.css
│   └── styles.css
├── fonts/
│   └── bootstrap-icons.*   ← woff/woff2 icon font files
├── js/
│   ├── bootstrap.bundle.min.js
│   └── scripts.js
├── img/
├── includes/
│   ├── head.php
│   ├── nav.php
│   ├── footer.php
│   ├── schema.php
│   └── business-config.php
├── build/
│   └── client-notes.md       ← secured, not public
├── .htaccess
├── robots.txt
├── sitemap.xml
└── BUILD.md
```

`build/` is secured in `.htaccess` — contains client notes, not for public access.

### 2B. Bootstrap 5 Base

- Bootstrap 5.x current stable — hosted locally, not CDN
- Bootstrap Icons 1.11+
- No jQuery unless genuinely required
- No build tools (Gulp, npm, SASS)

### 2C. .htaccess Baseline

Built in at Phase 2 — not caught at launch:

**HTTPS and routing:**
- Cloudflare-safe HTTPS redirect: `RewriteCond %{HTTP:X-Forwarded-Proto} !https`
- www canonicalization (consistent www vs non-www)
- Custom error pages: `ErrorDocument 404`, `ErrorDocument 500`

**Security:**
- `Options -Indexes` (directory listing disabled)
- Server signature hidden
- Sensitive files protected (.git, config files)
- Security headers — every site needs these, build them in now:
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: SAMEORIGIN`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Permissions-Policy`
  - `Strict-Transport-Security` (HSTS)
  - `Content-Security-Policy` (CSP — configure per site)

**Performance:**
- Browser caching rules for static assets (CSS, JS, images)
- Gzip compression

**Development settings (changed at launch):**
- HTML caching: `access 0 seconds` during development — Cloudflare serves stale HTML otherwise; restore to short TTL at launch
- `robots.txt`: `Disallow: /` during development — opened at launch

### 2D. PHP Includes

Create as shells, ready for content:

- `includes/head.php` — doctype through closing `</head>`
- `includes/nav.php` — full navigation (desktop + mobile)
- `includes/footer.php` — footer content + scripts
- `includes/schema.php` — Schema.org JSON-LD output

### 2E. Business Config

Create `includes/business-config.php` as the single source of truth for all NAP and Schema data on the site. Footer, contact page, and Schema.org JSON-LD all pull from this one file — nothing is hardcoded separately.

```php
<?php
$business = [
  'name'          => '[Business Legal Name]',
  'address'       => '[Street Address]',
  'city'          => '[City]',
  'state'         => '[State]',
  'zip'           => '[ZIP]',
  'phone'         => '[+1-XXX-XXX-XXXX]',
  'phone_display' => '[(XXX) XXX-XXXX]',
  'email'         => '[contact@domain.com]',
  'url'           => 'https://[domain.com]',
  'schema_type'   => '[LegalService / AccountingService / InsuranceAgency]',
];
?>
```

NAP consistency check during content integration (Phase 5) verifies these values match the footer, contact page, and Schema output — all of which pull from this file. No external source checking.

**Output:** Deployable base with no content. All infrastructure confirmed working before Phase 3 begins.

---

## Phase 3 — Brand Implementation

**Lead:** Doug
**Trigger:** Phase 2 base confirmed working

### 3A. Color System

Define all color tokens as CSS custom properties in `styles.css` before any component work begins. Once tokens are set, nothing in the codebase gets a hardcoded color value — every color reference uses a token. This makes global color changes a single-file edit.

```css
:root {
  /* Brand */
  --color-primary:    [hex];   /* Main brand color — nav, headings */
  --color-secondary:  [hex];   /* Accent / CTA color — buttons, links */
  --color-dark:       [hex];   /* Dark backgrounds, footer */
  --color-light:      [hex];   /* Light backgrounds, panels */

  /* Text */
  --color-text:       [hex];   /* Body copy */
  --color-text-light: [hex];   /* Muted / secondary text */
  --color-white:      #ffffff;

  /* Interactive */
  --color-cta:        [hex];   /* Primary button background */
  --color-cta-hover:  [hex];   /* Primary button hover state */
  --color-focus:      #ffdd00; /* Focus indicator — do not change */

  /* Borders */
  --color-border:     [hex];
}
```

**Before moving to Phase 4, verify all WCAG AA contrast ratios:**
- Body text on background: 4.5:1 minimum
- Large text / headings: 3:1 minimum
- CTA buttons: 4.5:1 minimum in all states (default, hover, focus)
- Any text on colored backgrounds: verify each combination

**To change the site's color scheme during development:** update the token values in `:root` only. Every component inherits the change automatically. Test contrast ratios after any token change.

### 3B. Typography

- Heading font and body font confirmed
- Base size: 16px minimum
- Line height: 1.5 minimum for body copy
- Google Fonts loaded via `<link rel="preconnect">` in head — never `@import`
- Type scale defined in CSS tokens:

```css
:root {
  --font-heading: '[Font Name]', sans-serif;
  --font-body:    '[Font Name]', sans-serif;
  --text-base:    1rem;      /* 16px */
  --text-lg:      1.125rem;
  --text-xl:      1.25rem;
  --text-2xl:     1.5rem;
  --text-3xl:     1.875rem;
  --text-4xl:     2.25rem;
}
```

### 3C. Logo

- SVG version (primary) — scales without quality loss
- PNG fallback available if needed
- Alt text defined: "[Business Name]" or more descriptive if needed
- Sizing appropriate in header context — not oversized, not undersized
- Focus behavior handled in accessibility CSS (see 3D)

### 3D. Accessibility Baseline CSS

**Built in at Phase 3 — never retrofitted.** Add to `styles.css`:

```css
/* Focus indicator — applies to all keyboard-navigable elements */
:focus-visible {
  outline: 3px solid var(--color-focus);
  outline-offset: 2px;
}

/* Skip link — custom class, NOT Bootstrap's sr-only-focusable
   (Bootstrap's version causes layout shift on focus) */
.skip-link {
  position: absolute;
  top: -100%;
  left: 0;
  z-index: 9999;
  padding: 8px 16px;
  background: var(--color-dark);
  color: var(--color-white);
  font-weight: 600;
  text-decoration: none;
}
.skip-link:focus-visible {
  top: 0;
}

/* Logo: focus on the img child, not the link
   (focusing the link breaks header layout) */
.navbar-brand a:focus-visible {
  outline: none;
}
.navbar-brand a:focus-visible img {
  outline: 3px solid var(--color-focus);
  outline-offset: 2px;
}

/* Image links — exclude logo (handled above) */
a:has(img):not(.navbar-brand a):focus-visible {
  outline: 3px solid var(--color-focus);
  outline-offset: 4px;
}

/* Content links — underline in all content contexts */
p a, li a, blockquote a, td a {
  text-decoration: underline;
}

/* Button focus states */
.btn:focus-visible {
  outline: 3px solid var(--color-focus);
  outline-offset: 2px;
  box-shadow: none;
}
```

**External link and PDF link icons** — add to `scripts.js`. Uses Bootstrap Icons (already in the stack) to visually indicate external links and PDF downloads via CSS pseudo-elements. JS auto-detects and applies classes; CSS renders the icons.

```javascript
// Auto-detect external links — adds icon class + target/rel attributes
// Skips: same-domain links, image links, icon-only links, buttons
document.addEventListener('DOMContentLoaded', function() {
  var ownHost = location.hostname.replace('www.', '');
  document.querySelectorAll('a[href^="http"]').forEach(function(a) {
    try {
      var linkHost = new URL(a.href).hostname.replace('www.', '');
      if (linkHost === ownHost) return;
      if (a.querySelector('img')) return;
      if (a.querySelector('i, svg') && !a.textContent.trim().length) return;
      if (a.classList.contains('btn')) return;
      a.classList.add('external-link');
      if (!a.getAttribute('target')) a.setAttribute('target', '_blank');
      if (!a.getAttribute('rel')) a.setAttribute('rel', 'noopener noreferrer');
    } catch(e) {}
  });

  // Auto-detect PDF links — adds icon class + target="_blank"
  document.querySelectorAll('a[href$=".pdf"]').forEach(function(a) {
    if (a.querySelector('img')) return;
    a.classList.add('pdf-link');
    if (!a.getAttribute('target')) a.setAttribute('target', '_blank');
  });
});
```

Pair with CSS in `styles.css`:

```css
/* External link icon — Bootstrap Icons: box-arrow-up-right */
.external-link::after {
  content: "\f1c5";
  font-family: "bootstrap-icons";
  margin-left: 4px;
  font-size: 0.75em;
  text-decoration: none !important;
  display: inline-block;
}

/* PDF link icon — Bootstrap Icons: file-earmark-pdf */
.pdf-link::after {
  content: "\f32b";
  font-family: "bootstrap-icons";
  margin-left: 4px;
  font-size: 0.85em;
  color: #ea4335;
  text-decoration: none !important;
  display: inline-block;
}
```

**Output:** Styled base page with all tokens set and accessibility baseline in place. Brand system documented in `build/client-notes.md`.

---

## Phase 4 — Component Build

**Lead:** Doug (HTML/CSS), cc (JS/PHP as needed)
**Trigger:** Brand system approved — tokens set, contrast verified, accessibility baseline in place

Build in this order. Each component complete and tested before moving to the next.

### 4A. Head Include (`includes/head.php`)

- [ ] Doctype HTML5, `lang="en"`
- [ ] Charset UTF-8
- [ ] Viewport meta tag
- [ ] `<title>` — page-specific variable, not hardcoded
- [ ] Meta description — page-specific variable
- [ ] Canonical URL
- [ ] Favicon and web manifest
- [ ] CSS links with `?v=YYYYMMDD` cache busting (Bootstrap CSS, Bootstrap Icons CSS, styles.css)
- [ ] Preconnect hints for Google Fonts if used
- [ ] Open Graph tags: og:title, og:description, og:image, og:url, og:type
- [ ] GA4 or GTM snippet — async, not render-blocking

### 4B. Navigation (`includes/nav.php`)

- [ ] Skip link: `<a href="#main-content" class="skip-link">Skip to main content</a>`
- [ ] `<nav aria-label="Main navigation">`
- [ ] Desktop dropdowns: CSS `:hover`/`:focus-within` — no `data-toggle="dropdown"`
- [ ] All dropdown parent links have real `href` values — never `javascript:void(0)`
- [ ] Click on parent navigates to parent page; hover/focus shows dropdown
- [ ] Keyboard: Tab triggers `:focus-within` (dropdown opens), Enter navigates
- [ ] Mobile: BS5 accordion pattern (see components library)
- [ ] Primary CTA (phone and/or button) visible at all breakpoints
- [ ] `aria-current="page"` on active item
- [ ] Logo links to homepage with descriptive alt text

### 4C. Footer (`includes/footer.php`)

- [ ] Business name, address, phone — pulled from `business-config.php`
- [ ] Phone as `<a href="tel:[number]">` clickable link
- [ ] Copyright: `&copy; <?php echo date('Y'); ?> [Business Name]`
- [ ] Site version stamp: `<p class="site-version">v1.0.0</p>` in the copyright band, lower-left (inherits the muted footer color — re-verify AA against `--color-dark`). Start at `v1.0.0`; bump on each CSS/JS ship alongside the `?v=` cache-buster.
- [ ] Navigation links (abbreviated)
- [ ] Accessibility Statement link
- [ ] Privacy Policy link
- [ ] Enzuzo cookie consent script (if client approved)
- [ ] Bootstrap JS and custom scripts with `?v=YYYYMMDD` cache busting

### 4D. Schema.org JSON-LD (`includes/schema.php`)

Pulled from `business-config.php`. Values are set once in the config — the schema file formats and outputs them.

- [ ] `@context`, `@type` correct
- [ ] Schema type appropriate to industry
- [ ] Name, address, phone, URL match `business-config.php` exactly
- [ ] Validated via Schema.org validator before launch

### 4E. Homepage (`index.php`)

The homepage has the hardest job: establish trust and move a first-time visitor toward contact within one page view.

**Required structure:**
- [ ] **Hero** — value proposition headline (not the firm's name), primary CTA above the fold on all devices
- [ ] **Trust signals** — years in practice, credentials, recognizable client types, or social proof
- [ ] **Services overview** — brief visitor-benefit-focused descriptions, links to detail pages
- [ ] **About teaser** — human face on the firm, link to full About page
- [ ] **Secondary CTA** — mid-page or bottom reinforcement of primary CTA
- [ ] **Contact teaser** — direct path to contact at page bottom

### 4F. Contact Page

The conversion destination — this page must be flawless.

- [ ] MachForm embed — placeholder div must use `id="mf_placeholder"` exactly (mf.js requires this specific ID)
- [ ] One MachForm JS embed per page maximum — additional forms on same page use iframes
- [ ] Modal forms: iframes with `data-src` lazy-loading (loads on modal open, avoids third-party cookies on every page load)
- [ ] Phone number prominent and clickable (`tel:` link)
- [ ] Address with Google Maps link (opens in new tab)
- [ ] Business hours if applicable
- [ ] No unnecessary form fields — ask only what's needed

### 4G. Remaining Pages

For each page in the approved sitemap:

- [ ] One H1, sequential heading hierarchy (H2, H3 — no skipped levels)
- [ ] Page-specific `<title>` and meta description
- [ ] Canonical URL
- [ ] Clear next step or CTA at or near page bottom
- [ ] Images: WebP, correct dimensions, descriptive alt text
- [ ] Internal links to related pages where natural
- [ ] `id="main-content"` on the main content area (skip link target)

### 4H. Accessibility Statement

From components library (`accessibility-statement.html`). Replace all bracketed placeholders:

- [ ] `[Company Name]`
- [ ] `[CONTACT_PAGE_URL]`
- [ ] `[AUDIT_DATE]`
- [ ] `[VERSION]`

### 4I. 404 Page

- [ ] On-brand message, navigation intact
- [ ] Link back to homepage
- [ ] Returns actual 404 HTTP status (not 200)
- [ ] `ErrorDocument 404 /404.php` in `.htaccess`

**Output:** All pages built, linked, and functional. Ready for content.

---

## Phase 5 — Content Integration

**Lead:** Doug
**Trigger:** All components built and confirmed working

**Content source — confirm before starting:**

- [ ] **Client-provided:** Review against copy checklist (5A) before integrating. Flag issues before they're in the site.
- [ ] **Migrated from existing site:** Review and update against copy checklist. Don't migrate problems.
- [ ] **Risingline-created:** Write to copy checklist standards from the start.

**Conversion path revisit:** Before integrating content, review Phase 1B conversion paths against what's actually been written. Real content often changes the optimal page structure or CTA placement. Update `build/client-notes.md` if paths change.

### 5A. Copy Review Checklist

Confirm all copy:

- [ ] Leads with visitor problems and goals, not firm credentials
- [ ] Value proposition clear in first two sentences of each page
- [ ] CTAs are action-specific: "Call us today" not "Learn more"
- [ ] Tone: professional but not cold, accessible but not casual
- [ ] Proofread — typos undermine credibility immediately

### 5B. Image Optimization

- [ ] All images converted to WebP
- [ ] JPGs (photos): `cwebp -q 80` (lossy)
- [ ] PNGs (logos, graphics, transparency): `cwebp -lossless` — never lossy on PNGs
- [ ] Dimensions match display size — no 2000px images displayed at 400px
- [ ] `width` and `height` attributes on all `<img>` tags (prevents CLS)
- [ ] `loading="lazy"` on all images below the fold
- [ ] Hero/above-fold image: `<link rel="preload">` in head, no lazy loading
- [ ] Descriptive alt text on meaningful images; `alt=""` on decorative

### 5C. NAP Consistency Check

`business-config.php` is the source — everything else pulls from it. Verify the output matches in all three places:

- [ ] Footer (rendered from config)
- [ ] Contact page (rendered from config or hardcoded — must match exactly)
- [ ] Schema.org JSON-LD (rendered from config)

Same format, same spelling, same phone number format across all three.

### 5D. Meta Tags — All Pages

Audit every page:

| Page | Title (50-60 chr) | Meta Desc (120-158 chr) | Canonical | OG tags |
|------|-------------------|-------------------------|-----------|---------|
| Home | | | | |
| About | | | | |
| Services | | | | |
| Contact | | | | |
| Accessibility | | | | |
| Privacy | | | | |
| 404 | | | | |

### 5E. Analytics

- [ ] GA4 property created under client ownership (not Risingline)
- [ ] Tracking snippet in `head.php` — async, not render-blocking
- [ ] Verified firing in GA4 DebugView
- [ ] No duplicate tracking codes

### 5F. Sitemap & Robots.txt

- [ ] `sitemap.xml` accurate, includes all indexable pages
- [ ] `sitemap.xml` referenced in `robots.txt`
- [ ] `robots.txt` blocks `/build/` and `/audit/`
- [ ] `robots.txt` still set to `Disallow: /` until Phase 6 pre-launch

**Output:** Site fully populated with real content. Ready for pre-launch.

---

## Phase 6 — Pre-Launch & Launch

**Lead:** Doug + cc
**Trigger:** Content integration complete

### 6A. Pre-Launch Checklist

**Development settings — restore for production:**
- [ ] `.htaccess`: Change HTML caching from `access 0 seconds` to short TTL (1 hour)
- [ ] `robots.txt`: Remove `Disallow: /` — open to indexing. Keep `/build/` and `/audit/` blocked.

**Infrastructure:**
- [ ] Cloudflare SSL confirmed: Full (Strict) — never Flexible
- [ ] HTTPS enforced, no mixed content warnings
- [ ] Cache busting applied to all CSS/JS references (`?v=YYYYMMDD`)

**Functionality:**
- [ ] All pages load without console errors
- [ ] All internal links resolve
- [ ] Mobile nav tested on actual device or Puppeteer screenshots
- [ ] Form tested end-to-end: submission → confirmation → delivery to client inbox
- [ ] Third-party integrations tested (ecommerce links, portals, etc.)

**Content:**
- [ ] Accessibility statement live with correct date and version
- [ ] Privacy policy (Enzuzo) live and configured (if client approved)
- [ ] All placeholder content replaced

**Build quality standards (verify against targets at top of document):**
- [ ] Site meets all targets listed in Build Quality Standards

### 6B. At Launch

1. Pre-launch checklist complete → deploy to production
2. `BUILD.md` remains in project root for reference
3. `build/client-notes.md` remains for session history

---

## Components Library

The components library lives in the master repo. It is not a framework — it is a personal parts bin of tested, documented, reusable patterns built from real project work.

**How to use a component:**
- Use as-is if it fits the project
- Modify for project-specific requirements
- Use as a reference/starting point for something similar

**How to add a component:**
When a pattern is built on a project and proves reusable, promote it to the library. Each component file is self-documenting — header comments explain the problem solved, implementation steps, and any known gotchas.

### Current Components

| Component | Status | Notes |
|-----------|--------|-------|
| `accessibility-statement.html` | Ready | Replace 4 bracketed placeholders per project |
| `mobile-nav-accordion.php` | BS3 only | BS5 version needed — generate from first real project |
| `business-config.php` | Ready | Full field set with inline comments (doc shows minimum, template has all fields) |
| `client-notes.md` | Ready | Starter with section headings mirroring build phases |

### Adding to the Library

When a pattern graduates from a project to the library:

1. Strip all client-specific content — replace with bracketed placeholders
2. Add a header comment block explaining: problem solved, implementation steps, dependencies, known issues
3. Confirm it's tested and working before promoting
4. Update `README.md` with the new component entry

---

## Build Notes

See `build/client-notes.md` for:
- Sitemap and conversion paths (Phase 1)
- Brand decisions — colors, fonts, logo context
- Third-party integration details (MachForm IDs, Enzuzo script ID, analytics property)
- Content source and status
- Deferred items and known constraints
- Session history and decisions made

---

*At launch: BUILD.md remains for reference.*
