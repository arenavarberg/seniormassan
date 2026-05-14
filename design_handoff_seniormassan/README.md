# Handoff: Seniormässan — Webbplats & Anmälningssystem

## Overview

A complete website for **Seniormässan** (Senior Fair) at Arena Varberg, scheduled for **Wednesday 24 February 2027**. The package includes:

- **Public marketing site** with 5 sections (Visitors, Program, Find Us, Contact, Exhibitors)
- **Exhibitor registration wizard** — multi-step form with interactive booth map (SVG), addon selection with stock management, optional stage time-slot booking
- **Admin panel** (demo) — view registrations, export CSV, manage product stock
- **Live exhibitor list** — auto-populates from registrations as exhibitors sign up
- **Tweaks panel** — runtime palette switching for design iteration

The site is targeted at **seniors (55+)** and Swedish speakers, so accessibility and clear hierarchy matter more than density. Copy is in **Swedish**.

---

## About the Design Files

The files in this bundle are **design references created in HTML with React via Babel inline**, served as prototypes to demonstrate the intended look and behavior. They are **not production code** to copy directly. They demonstrate:

- Layout, typography, spacing, color choices
- Component composition and interaction patterns
- Form flows and state shape
- Animations and transitions

The task is to **recreate these designs in your target codebase's environment** (e.g. React + Vite, Next.js, Astro, plain HTML/CSS — whichever the production stack uses). If no environment exists yet, **Next.js with the App Router** is a good default for this kind of marketing+forms site since it gives you both static rendering for SEO and server-side form handling.

The current prototype uses:
- React 18 (umd from unpkg)
- Babel standalone (inline JSX compilation — slow, only for prototype)
- Plain CSS with custom properties (no framework)
- `localStorage` for prototype data persistence (must be replaced with real backend)

---

## Fidelity

**High-fidelity (hifi).** The mockups use final colors, typography, spacing, and copy in Swedish. The developer should recreate these screens pixel-perfectly using the target codebase's existing libraries and patterns. Exact hex values, font weights, and spacing are documented below — use them.

The accent SVG illustrations (booth icons, addon product icons) are inline SVG and can be lifted as-is or replaced with proper assets.

---

## Brand Context

The fair is organized by **Arena Varberg AB**. Their logo (`arena-varberg-logo-white.png`) is featured in the footer as "Arrangör" (organizer). Their existing brand colors from their graphic profile include:
- Teal `#007765`
- Red `#C6371D`
- Yellow `#F1C400`
- Purple `#5B457F`
- Blue `#00719C`
- Pink `#C23669`
- Green `#008932`
- Dark grey `#333`

A separate **Seniormässan brand** has been established for this project with its own palette (see Design Tokens below). The "senior" wordmark (PNG at `assets/senior-logo-2026-transparent.png`) is used as the primary brand mark, paired with the word "MÄSSAN" in tracked-out sans-serif.

---

## Screens / Views

The site is a single-page app with 5 main sections, plus a registration modal wizard and admin panel.

Routing in the prototype is state-based (`page` variable in App). In production, use real URL routes:
- `/` → Visitor page
- `/program` → Program
- `/hitta-hit` → Find us
- `/kontakt` → Contact
- `/for-utstallare` → Exhibitor info

The registration wizard should be either a modal (current behavior) or a dedicated route `/anmalan`. The admin panel should be a separate authenticated route `/admin`.

### 1. Visitor page (home) — `For-besokare`

Order of sections, top to bottom:

1. **Hero with image carousel** — Full-bleed, 620px min-height, dark gradient overlay (15% top → 82% bottom). 7 photos cycling every 5.5 seconds with 1.2s opacity fade. Photos from `assets/hero-*.jpg`. Wave SVG at bottom transitions into next section.
   - **H1:** "Seniormässan **24 feb 2027**" — last 3 words in `--sm-gold` accent color. Font `--sm-font-display` (Sofia Sans), size `--sm-fs-xxl` (clamp 32–88px), text-shadow for legibility on photo.
   - **Body:** "En dag fylld av möten, upplevelser och inspiration — närmare 90 utställare, scenprogram, caféer och restaurang." Max-width 720px.

2. **Stats band** (4 columns, beige bg) — "~90 utställare", "~2000 besökare", "2 caféer + bar", "10+ år tradition". Numbers in Sofia Sans 56px 800w in `--sm-primary`. Padding `0 32px 64px` (no top padding since wave above provides spacing).

3. **Video embed** (white bg) — Vimeo player (id `1178774214`) in 16:9 aspect, rounded corners, shadow. Eyebrow "Så var det 2025" + h2 "En liten smakbit av förra årets mässa."

4. **Times & practical info** (beige bg, 2-column) — Left: eyebrow "Praktiskt" + h2 "Hela dagen i ett svep." + InfoBlock with "Onsdag 24 februari 2027 / 10.00–17.00 / Dörrarna öppnar 09.30" and "Entré 100 kr i förköp · 120 kr vid dörren (swish / kort), Fri parkering, Buss linje 1/2/4". Right: photo at 4:5 aspect.

5. **Areas overview** (white bg) — Image left (4:3) + heading right ("Sex världar att upptäcka"). Then 3×2 grid of category cards: Resor & Upplevelser, Hälsa & Välmående, Boende, Ekonomi & Juridik, Teknik, Kultur & Fritid. Each card has a number (top right), title, description on beige bg.

6. **CTA band** (primary blue) — "Ta med en vän — det blir roligare så." Body: "Dela gärna inbjudan. Köp biljett i förköp för 100 kr, eller 120 kr vid dörren."

7. **Höjdpunkter 2027** (beige bg) — Eyebrow + h2 + 3 cards in row. Each card: 16:10 image, then content padding 28px with kicker (uppercase 13px), h3 24px, body 17px. Images: `images/scenprogram.jpg`, `images/boule.jpg`, `images/resecentrum.jpg`. Topics: Scenprogram / Provspring / Resecentrum.

8. **Exhibitor list** (white bg) — Eyebrow "Utställare 2027" + h2 "Utställarlistan" + subtitle "Listan uppdateras löpande". Search input (pill-shaped, magnifying glass icon), letter filter (Alla + A/B/C/...) showing only letters that have entries. Counter "Visar X av Y utställare". 3-column grid of cards: name, booth ID with red pin icon, optional website link with external-link icon. "Visa alla X utställare →" button shows initial 24 then expands.

Wave dividers (SVG, height 70px) separate most sections, alternating curve direction with `flip` prop. The image after Nyhet 2027 (on Exhibitor page) is full-bleed with no waves — the photo itself is the divider.

### 2. Program page

- **PageHero** (primary blue, white text): eyebrow "Program", title "En dag, två scener, 18 programpunkter.", body "Allt ingår i entrén — kom och gå som du vill."
- **Eyebrow "Datum"** + h2 "Onsdag 24 februari" in primary color + disclaimer "Programmet uppdateras löpande inför mässan. Med reservation för ändringar."
- **Two stage columns** side by side (1:1 grid). Each column has a colored header bar (h3 22px 800w uppercase, white text, 18px padding) with one of two accent colors:
  - **Stora Scen** (left) — `--sm-primary` blue, 10 programpoints from 10:00–16:00
  - **Utställarscenen** (right) — `--sm-accent` coral, 8 programpoints from 11:00–14:30
- Each row in a stage column: 80px time column + content column with name (18px 700w) + description (15px ink-soft, 1.4 line-height). Separated by 1px `--sm-line-soft` hairline.

### 3. Find us — `Hitta-hit`

- **PageHero**: title "Arena Varberg — den stadsnära arenan.", body "15 minuters promenad från stationen. Fri parkering med över 200 platser."
- **2-column layout (1.4 / 1)**:
  - Left: Map placeholder (use Google Maps or OpenStreetMap embed in production)
  - Right: 3 InfoBlocks — Adress (Engelbrektsgatan 6, 432 41 Varberg), Fri parkering (över 200 platser, kostnadsfritt), Tillgänglighet (ramp, toaletter, hörselslinga)

### 4. Contact — `Kontakt`

- **PageHero**: title "Prata med oss.", body "Vi svarar inom ett arbetsdygn."
- **2×2 grid of contact cards** (white, border, padding 32px, soft shadow):
  - Top-left: **Besökare & program** — Gästservice — info@arenavarberg.se — 0340-690200
  - Top-right: **Projektledare** — Camilla Bourdette — camilla.bourdette@arenavarberg.se — 0340-690204
  - Bottom-left: **Försäljning / Utställare** — Vivi Strindö — vivi.strindo@arenavarberg.se — 0340-690213
  - Bottom-right: **Försäljning / Utställare** — Susanne Carlsson — susanne.carlsson@arenavarberg.se — 0340-690200

### 5. Exhibitor info — `For-utstallare`

1. **PageHero** (accent color bg): eyebrow "För utställare", title "Möt ~2000 engagerade seniorer.", body about long quality meetings + 10+ year tradition. CTA button "Gå till anmälan →" opens registration wizard.

2. **Nyhet 2027 callout** (white card on beige): Red circular badge ("Nyhet 2027" in 2 lines, rotated -6°) on left, then content right: h2 "5 digitala entrébiljetter ingår — bjud in dina kunder." + 18px body. Has decorative `--sm-gold` circle 220×220 with 18% opacity bleeding from top-right corner.

3. **Full-bleed image** (`images/senior-wide.jpg`, 480px height, no overlay, no wave divider).

4. **Varför ställa ut?** (beige) — eyebrow + h2 "Kvalificerade möten, inte flyktiga förbipasseranden." + 3-column grid with massive 64px accent numbers (01/02/03), 24px h3, body.

5. **Utställarscen-card** (white on beige) — 1.2:1 grid, left text + right image (4:3). Eyebrow "Utställarscen", h2 "Nå ut med ditt budskap på vår utställarscen.", body about free 15-min stage slots, "först till kvarn".

6. **Monterpaket** (white) — eyebrow + h2 "Tre storlekar. Alla inklusive det grundläggande." + 4-column grid of pricing cards. The 2×3m card is `featured` (primary blue bg, white text, has "POPULÄRAST" tag). Each card: size, ideal description, large 40px price, "exkl. moms" / "inkl. moms" label, separator, includes-list with checkmarks.
   - 2×2 m → 3 820 kr (exkl moms)
   - 2×3 m → 5 730 kr · POPULÄRAST
   - 3×3 m → 8 845 kr
   - Förening → 2 360 kr (inkl moms) — only for non-profits

7. **Tillägg** (sub-section in same panel) — h3 "Skräddarsy din monter." + body "När du gör din bokning kan du lägga till produkter i din monter — t.ex. golv i olika färger, bord, stolar, utställarlunch m.m." + 3-column grid of addon previews: Registreringsavgift 800, Montermatta 110, Wifi 450, Monterbord 350, Stol 120, Matbiljett 180.

8. **Sista anmälningsdag**: 15 augusti 2027.

9. **Quote section** (beige) — 260px circular portrait of Annett Wiktorsson (`images/annett-wiktorsson.webp`) on left + 30px Sofia Sans 600w italic-quoted blockquote on right: ""Många åker till vår butik i Åsa och handlar direkt efter att vi träffats på mässan." — Annett Wiktorsson, Modestugan (deltagit sedan 2015)"

### Registration Wizard (modal)

Opens when "Boka monter" or any registration CTA is clicked. Backdrop with blur, max-width 980px, rounded card.

**Header** (primary blue): "Utställaranmälan 2027" eyebrow + "Seniormässan · Arena Varberg" title + close × button.

**Stepper** (4-step but actually 5 steps internally): 1. Företag · 2. Monter · 3. Tillägg · 4. Scen · 5. Granska. Each step has number circle + label. Active step has accent underline.

**Step 1 — Företag (Company)**
2-column grid of fields:
- Företagsnamn (required)
- Organisationsnummer (required)
- Faktura-e-post
- Fakturaadress (full-width)
- Kontaktperson (required)
- Telefon (required)
- E-post kontaktperson (required, full-width)
- **Webbplats (required)** — used in the public exhibitor list
- Beskrivning av företaget (textarea, full-width, max 320 chars)

**Step 2 — Monter (Booth selection)**
- Heading "Välj monterplats" + body about clicking multiple booths
- Note: "Rosa montrar (N1–N12) är endast för ideella föreningar och kostar 2 360 kr inkl. moms."
- **Interactive SVG booth map** showing the hall layout (see Booth Map section below)
- Live summary box (primary blue): chips for each selected booth, total area, total price, "Rensa val" button
- Empty state: dashed border box

**Step 3 — Tillägg (Addons)**
14 products in grid (2 columns), grouped by category:
- **Möbler:** Matta (110 kr, 6 färgval), Stol (60), Barstol (130) [SLUTSÅLD by default], Bord 180×80 (130), Bord 120×50 (110), Rullbox (657), Barbord (130), Bordsstrumpa (100, 2 färgval, **locked until barbord selected**)
- **El & belysning:** Belysning monter (110), Anslutning 16 amp 3-fas (463), Grenkontakt (110)
- **Mat & fika:** Förmiddagsfika (95), Lunch (180), Eftermiddagsfika (95)

Each addon card: 120px tall illustration on beige bg, then product name, optional hint, price/st, optional color swatches (circular buttons), then qty controls (− [n] +) and live total.

"Slutsåld" products have a black badge overlay; quantity buttons disabled. Toggled via admin panel.

Bordsstrumpa shows a "🔒 LÅST" badge with reason "Kräver att du först lägger till ett barbord" if no barbord selected. Auto-removes when barbord qty hits 0.

Bottom: "Särskilda önskemål" textarea.

**Step 4 — Scen (Stage time, optional)**
- Heading "Utställarscen — boka tid (frivilligt)"
- Body about 15-min slots, free but binding
- 4-column grid of 11 time buttons: 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00
- Taken slots (read from localStorage) shown strikethrough + "UPPTAGEN" subtitle
- Selected slot shown in info box with "Ta bort" link
- Helpful "Inga tid vald — du kan hoppa över detta steg" hint

**Step 5 — Granska (Review)**
- Company block (2-col grid with Monter block)
- Monter block: list of selected booths with prices + registreringsavgift line
- Optional addon block: lists addons with variant in parens
- Optional stage block: time + duration
- Total summary bar (primary blue, large number)
- 2 checkboxes (required): utställarvillkor & GDPR

**Footer**:
- Total preview on left ("Total: X kr exkl. moms")
- Back / Next / Submit buttons

After submit: confirmation modal with success ✓ icon, reference ID, and "Admin-vy (demo)" button.

### Booth Map (SVG)

Hall plan in 1000×780 viewBox. Renders:

- **Fixed areas:** Café (black), Utställarscen (pink), Trappa in/ut (black), Café 2 (black, right side), Scen (pink), Trappa entré (orange), Garderob (purple), Toaletter (orange ×2), Gästservice/Restaurang strip, Publikplatser dot-grid
- **Hall labels:** "ENTRÉHALLEN", "SPARBANKSHALLEN", "HUVUDENTRÉ"
- **Booths** grouped by sections A, B, C, D, E, G, H, I, J, K, L, M, N
  - Section N (entréhallen, 12 booths) = pink `#E8A6C4` — föreningsmontrar
  - 2×2 m = yellow `#F2DC6E` (3 820 kr regular)
  - 2×3 m = light blue `#7DC1D9` (5 730 kr)
  - 3×3 m = lime green `#A8D38E` (8 845 kr)
  - Selected = `--sm-accent`
  - Booked (demo set: B2, B3, C2, E1, E2, G1, H5, K3, M5) = grey `#d6d2cb`

Click toggles selection. Disabled if booked. Coordinates for all booths are in `booth-map.jsx` `BOOTHS` array.

### Admin panel (demo)

Activated via small 🔒 "Admin (demo)" button bottom-left of viewport. In production: separate authenticated route.

**Header:** Dark bg, "Admin — demo" eyebrow, "Seniormässan 2027" title, action buttons: "↓ Exportera CSV", "🖨 Skriv ut lista", "Rensa", "Stäng".

**Tabs:** "Anmälningar" / "Tillval / lager"

**Anmälningar tab:**
- 4 stat cards: anmälningar count, summa kr, bokade montrar, kvarvarande lediga
- Table: Ref, Företag, Kontakt, Monter, Paket, Summa, Status (always "Ny" badge)

**Tillval / lager tab:**
- Table of all addons: Produkt, Kategori, Pris, Färgval, Status badge (green "I lager" or red "Slutsåld"), action button "Markera slutsåld" / "Markera i lager"
- Toggles persist to `localStorage` under `sm_addon_stock_v1`

---

## Interactions & Behavior

### Hero image carousel
- Auto-advance every 5500ms
- Opacity cross-fade 1.2s ease-in-out
- No dots (intentionally removed)
- Pause on user prefers-reduced-motion (not currently implemented — should be)

### Wave dividers between sections
- SVG with `preserveAspectRatio="none"` stretched to viewport width, 70px height
- Two paths (normal and flipped) cycled to create rhythmic flow
- 1px overlap (`L 1440,121` and `marginBottom: -1`) to avoid sub-pixel gap

### Booth map clicks
- onClick toggles `Set` of selected booth IDs
- Visual feedback: stroke 2px primary + accent fill on selected
- `cursor: not-allowed` and `pointer-events` disabled on booked

### Color tweaks panel (prototype-only)
- Activated via host iframe message (`__activate_edit_mode`)
- Allows switching palette via `data-palette` attribute on `<html>`
- 13 paletter defined — the production app should pick **one** and remove this panel
- Current production-intended palette: `havsbla-korall` (see Design Tokens)

### Form validation
- Each step has a `canNext` boolean: required fields must be non-empty / required selections made
- "Nästa" button is disabled (40% opacity) when invalid
- Final submit blocked until both T&C checkboxes ticked

### Animations
- Card hover: `translateY(-4px)` over 0.2s with shadow lift
- Button hover: `translateY(-1px)` with shadow
- All transitions use `ease` or `cubic-bezier`, 0.12–1.2s

### Responsive
- 900px breakpoint: 3/4-col grids → 2-col, h-stacks → v-stacks
- 640px breakpoint: most grids → 1 column, sections padding reduced, nav scrolls horizontally if needed
- Hero text scales via `clamp()` in font-size custom properties

---

## State Management

The prototype uses React state + `localStorage`. In production, all writes need a real backend.

### App-level state
- `page` — current visible screen (string, persisted to localStorage)
- `regOpen` — registration modal visibility
- `admin` — admin panel visibility
- `tweaks` — { palette, heroVariant, textSize } object
- `confirmed` — submission record (passed to confirmation modal)

### Registration form state (`data`)
```ts
{
  company: string,
  orgnr: string,
  invoiceAddress: string,
  invoiceEmail: string,
  contactName: string,
  contactEmail: string,
  contactPhone: string,
  website: string,         // required, used in public exhibitor list
  description: string,
  booths: string[],        // e.g. ["B3", "C12"]
  addons: {                // qty + optional variant
    [id]: { qty: number, variant?: string }
  },
  foodTickets: number,     // legacy, can drop
  stageSlot: string | null,  // e.g. "13:30"
  specialRequests: string,
  acceptTerms: boolean,
  acceptGdpr: boolean,
}
```

### Data persistence (current — replace in production)
- `localStorage["sm-registrations"]` — array of registration records (with id, total, submittedAt, ...data)
- `localStorage["sm_addon_stock_v1"]` — `{ [addonId]: boolean }` (false = sold out)
- `localStorage["sm-page"]` — current page

### Production backend requirements
- **Registrations table** — schema as above, plus invoice fields, accepted timestamps, payment status, admin notes
- **Booth occupancy** — derived from registrations (don't store duplicate state). Realtime updates would be nice but daily refresh is fine for v1.
- **Email** — confirmation to exhibitor + notification to admin on submit
- **CSV export** — endpoint that streams from DB
- **Stock management** — flat key/value table for addon availability
- **Admin authentication** — basic auth or proper login. Currently the panel is unprotected behind a debug button.

### Public exhibitor list source
Currently reads `localStorage["sm-registrations"]` directly. In production:
- Fetch from `/api/exhibitors` endpoint that returns only `{ company, booths, website }` (no PII)
- Cache for ~5 min on the client
- Should refresh when window regains focus

---

## Design Tokens

### Colors — Active palette: "Duvgrå · korall" / "Havsblå · korall"

The prototype lets users switch palettes. The intended production palette is **`havsbla-korall`** (deep navy + warm coral):

```css
--sm-primary: #003D5B;           /* Deep navy — buttons, page heros, primary surfaces */
--sm-primary-ink: #ffffff;
--sm-primary-soft: #d6e2ea;      /* Soft blue tint — cards, badges */

--sm-accent: #D96E5F;            /* Warm coral — CTAs, badges, links */
--sm-accent-soft: #f5d6d0;

--sm-gold: #F0A28D;              /* Hero text accent (light coral) */
--sm-success: #008932;

--sm-bg: #faf7f2;                /* Page bg — warm cream */
--sm-surface: #ffffff;           /* Cards, callouts */
--sm-ink: #2a2a2a;
--sm-ink-soft: #555555;
--sm-muted: #8a8a8a;
--sm-line: #efe9dd;
--sm-line-soft: rgba(90, 80, 60, 0.08);
```

### Booth-map specific colors
```css
2x2 booth: #F2DC6E (yellow)
2x3 booth: #7DC1D9 (light blue)
3x3 booth: #A8D38E (lime green)
N1-N12 (förening): #E8A6C4 (soft pink)
Booked: #d6d2cb (warm grey)
```

### Typography
- **Display:** "Sofia Sans" (Google Fonts) — headings, large numbers, eyebrows
- **Body:** "Mulish" (Google Fonts) — paragraphs, UI
- **Decorative (loggvarianter only):** "Caveat" — handwritten variants
- Both Sofia Sans and Mulish are the brand fonts of Arena Varberg

### Font sizes (responsive via clamp)
```css
--sm-fs-xxl: clamp(44px, 6vw, 88px);   /* Hero h1 */
--sm-fs-xl: clamp(32px, 4vw, 52px);    /* Section h2 */
--sm-fs-lg: clamp(24px, 2.4vw, 32px);  /* Body lead */
--sm-fs-md: 20px;                       /* Button text */
--sm-fs-body: 18px;                     /* Default */
--sm-fs-small: 16px;
```

Body line-height: 1.55. Headings line-height: 1.08, `text-wrap: balance`. Body: `text-wrap: pretty`.

### Spacing
The design uses ad-hoc spacing rather than a strict scale. Common values: 6, 8, 10, 12, 14, 16, 18, 20, 24, 28, 32, 40, 48, 56, 64, 80, 96px. The container is 1200px max-width with 32px horizontal padding (24px on tablet, 18px on mobile).

### Radii & shadows
```css
--sm-radius: 4px;       /* Small chips, badges */
--sm-radius-lg: 10px;   /* Cards, callouts */
--sm-shadow-sm: 0 1px 2px rgba(20,30,30,0.06), 0 2px 6px rgba(20,30,30,0.04);
--sm-shadow-md: 0 4px 14px rgba(20,30,30,0.08), 0 8px 32px rgba(20,30,30,0.06);
```

Pill buttons: `border-radius: 999px`. Booth cards: 6px. Booth rects: 2px.

---

## Assets

### Brand & logos
- `assets/senior-logo-2026-transparent.png` — primary "senior" wordmark (transparent PNG). Used in header + footer. The "i" letter is replaced by a stylized figure with raised arm.
- `assets/senior-logo-2026.png` — original with white background (don't use, use the transparent version)
- `assets/senior-logga-original.png` — older "Senior" handwritten mark from previous years (kept as reference; **not** currently used in the design)
- `assets/arena-varberg-logo-white.png` — Arena Varberg organizer logo (white version, used in dark footer)
- `assets/arena-varberg-logo.jpg` — Arena Varberg full-color version

### Photos (provided by client)
- `assets/hero-1.jpg` through `assets/hero-7.jpg` — 7 photos from previous fairs used in hero carousel
- `images/annett-wiktorsson.webp` — exhibitor portrait for testimonial quote
- `images/senior-wide.jpg` — wide image of happy visitor in booth (for exhibitor page)

### Stock photos (Adobe Stock, properly licensed by Arena Varberg)
- `images/scenprogram.jpg` — audience watching stage (Höjdpunkter: Scenprogram + Utställarscen-card)
- `images/boule.jpg` — outdoor activity (Höjdpunkter: Provspring)
- `images/resecentrum.jpg` — travel/bus image (Höjdpunkter: Resecentrum)

### Inline SVG illustrations
The 14 addon products use inline SVG icons (in `registration.jsx` → `AddonIcon`). They're simple, low-detail illustrations. Can be lifted as-is or replaced with proper product photos in production. Booth map elements (chairs, tables, lamps, electrical plugs, food) are similarly inline SVG.

### Vimeo embed
- Video ID: `1178774214` — promo video from 2025 fair. Embedded twice (visitor page + exhibitor page) with different headings.

---

## Files

The prototype is split across these files (open `Webbplats.html` to see them in action):

| File | Contents |
|---|---|
| `Webbplats.html` | Entry point. Includes React + Babel, mounts the App with state for routing + tweaks panel. Header includes editable defaults for palette/textsize. |
| `styles.css` | All design tokens, palette variants (data-palette attribute selectors), base typography, button styles, utility classes, responsive media queries (900px tablet, 640px mobile). |
| `site-shell.jsx` | NAV array, SiteHeader, Hero (legacy split variant), SiteFooter. The Header logo is the senior PNG + "MÄSSAN" text with hairlines on each side. |
| `site-pages.jsx` | VisitorPage, ExhibitorPage, ProgramPage, InfoPage, ContactPage, PageHero helper, InfoBlock, CTABand, WaveDivider, VimeoEmbed. Each page is a function. |
| `booth-map.jsx` | BOOTHS array (all booth coordinates and sizes), BOOTH_PRICES, BOOTH_COLORS, FORENING_PRICE, BoothMap (the interactive SVG), BoothLegend. DEMO_BOOKED Set defines which booths are pre-booked for demo. |
| `registration.jsx` | RegistrationWizard (modal), all 5 Step* components, AddonCard with AddonIcon (SVG library), ConfirmationModal, AdminPanel with tabs. Stock state read via `getStockStatus()`. |
| `exhibitor-list.jsx` | EXHIBITORS placeholder array (empty by default — fills from registrations), ExhibitorList component with search + letter filter + progressive disclosure. |

---

## Next steps for the developer

1. **Pick a framework** — Next.js 14 (App Router) is recommended for this site, or stick with whatever Arena Varberg currently uses.

2. **Recreate the design system** — set up the CSS custom properties and font imports first. The palette must be exactly the `havsbla-korall` values listed above (unless Arena Varberg requests a different one before launch).

3. **Build static pages first** — visitor, program, find-us, contact, exhibitor-info. These should be SSG / SSR for SEO.

4. **Build the registration flow** — multi-step form with a real backend, schema as documented. Email confirmations + admin notifications. Persist booth selections + addon stock to the database.

5. **Build the admin** — auth-gated route. Same UI as the prototype panel, plus CSV download. Should run server-side queries instead of localStorage.

6. **Wire the exhibitor list** — public read endpoint that pulls accepted registrations and exposes only name + booths + website (no PII).

7. **Accessibility audit before launch:**
   - Hero carousel should pause on `prefers-reduced-motion`
   - Add `aria-label` and proper roles to the booth map (currently SVG only). Maybe include a non-visual booth picker dropdown as alternative.
   - Verify all form fields have labels (currently use `<Field label>` wrappers; check that label-input association is proper).
   - The exhibitor list search input is currently `type="search"` with no label — fix.
   - Confirm color contrast on the coral CTAs against beige bg (WCAG AA).

8. **Replace placeholder content:**
   - Real photos in hero carousel (current set is 7 photos — confirm with Arena Varberg what they want)
   - Real Vimeo URL (current ID may need updating each year)
   - Real Google Maps embed for "Hitta hit"
   - Confirm all phone numbers, emails, and the 24 Feb 2027 date

9. **Forms-related decisions to confirm with Arena Varberg:**
   - How booth occupancy locks should work (race conditions when two exhibitors pick the same booth simultaneously)
   - Whether föreningspris should require manual verification or auto-trust the checkbox
   - Whether stage time-slot booking is binding before payment or pending review
   - Payment flow — invoice vs Swish/card at booking
   - Cancellation policy and refund handling

10. **Things explicitly NOT included in the prototype:**
    - Authentication for admin
    - Payment processing
    - Email sending
    - Sitemap / robots.txt / SEO meta tags
    - Cookie consent banner (required by Swedish law)
    - Privacy policy / terms of service pages
    - Server-side validation

---

## Quick reference — copy / strings

All UI text is in Swedish. Some key strings to use verbatim:

- Tagline: "Mötesplatsen för dig som vill leva hela livet"
- Hero h1: "Seniormässan **24 feb 2027**"
- Date: "Onsdag 24 februari 2027"
- Times: "10.00 – 17.00 (dörrarna öppnar 09.30)"
- Entry: "100 kr i förköp · 120 kr vid dörren (swish / kort)"
- CTA: "Boka monter →" (in header), "Anmäl din monter →" (on exhibitor page), "Gå till anmälan →" (PageHero CTA)
- Address: "Engelbrektsgatan 6, 432 41 Varberg"
- Phone (Arena Varberg switchboard): "0340-690200"
- Email: "info@arenavarberg.se"
