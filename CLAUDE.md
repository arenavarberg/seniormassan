# CLAUDE.md

Vägledning för Claude Code (claude.ai/code) när den arbetar i detta repo.

## Vad detta repo är

Repot innehåller **designunderlag** för webbplatsen till **Seniormässan på Arena Varberg** (onsdag 24 februari 2027). Det är inte produktionskod — `design_handoff_seniormassan/` är en HTML-prototyp med React + Babel inline som demonstrerar tänkt utseende och beteende. Produktionssidan ska byggas i en riktig stack och deployas separat.

Webbplatsen omfattar:

- Publik marknadsföringssajt (5 sektioner: För besökare, Program, Hitta hit, Kontakt, För utställare)
- Utställaranmälan (5-stegs wizard med interaktiv SVG-båskarta, tillval med lagerhantering, scenpassbokning)
- Adminpanel (visa anmälningar, exportera CSV, hantera lager)
- Levande utställarlista (fylls på från anmälningar)

Domän: `https://www.seniormassanvarberg.se`. Målgrupp: seniorer 55+. Allt innehåll är på **svenska**.

## Stack

### Prototyp (det som finns i repot idag)

- React 18 (UMD från unpkg)
- Babel standalone — JSX kompileras inline i webbläsaren (långsamt, endast för prototyp)
- Plain CSS med custom properties (ingen ramverk)
- `localStorage` för all dataflöde (måste ersättas med riktig backend)
- Google Fonts: Sofia Sans (display), Mulish (body), Caveat (dekorativ)

### Produktion (planerat)

- **WordPress** på Oderland-webbhotell (PHP + MySQL via cPanel/WP Toolkit)
- Custom tema som matchar designen pixel-perfekt
- Anmälningswizarden + båskartan byggs som custom plugin
- Anmälningsnotis till `bokning@arenavarberg.se`
- Adminpanel = WP-admin
- Ingen betalningshantering — utställare faktureras separat

Alternativ stack som diskuterats (men inte vald): Next.js (App Router) + headless CMS.

## Kommandon

### Visa prototypen lokalt

Det finns ingen byggprocess. Öppna HTML-filen direkt:

```bash
# macOS
open design_handoff_seniormassan/Webbplats.html

# Linux
xdg-open design_handoff_seniormassan/Webbplats.html
```

Eller servera mappen med valfri statisk server (rekommenderas för att undvika `file://`-restriktioner i vissa webbläsare):

```bash
cd design_handoff_seniormassan
python3 -m http.server 8000
# → http://localhost:8000/Webbplats.html
```

### Bygg / test / lint

Inget byggsteg, inga tester, ingen linter konfigurerad i repot. När produktionsstacken är på plats ska kommandon dokumenteras här.

## Mappstruktur

```
.
├── CLAUDE.md                          # Den här filen
└── design_handoff_seniormassan/
    ├── README.md                      # Komplett designspec — läs denna först
    ├── Webbplats.html                 # Prototypens entry point
    ├── styles.css                     # Designtokens, paletter, typografi, responsivitet
    ├── site-shell.jsx                 # NAV, SiteHeader, SiteFooter
    ├── site-pages.jsx                 # Visitor/Exhibitor/Program/Info/Contact-sidor + helpers
    ├── booth-map.jsx                  # SVG-båskarta, BOOTHS, BOOTH_PRICES, DEMO_BOOKED
    ├── registration.jsx               # 5-stegs wizard, AddonCard, ConfirmationModal, AdminPanel
    ├── exhibitor-list.jsx             # Utställarlista med sök + bokstavsfilter
    ├── assets/                        # Logotyper + hero-foton (hero-1.jpg … hero-7.jpg)
    ├── images/                        # Stockfoton + porträtt
    └── screenshots/                   # Referensskärmdumpar
```

`README.md` i `design_handoff_seniormassan/` är den auktoritativa specen — den dokumenterar varje sektion, alla designtokens, formstate-shape, animationer, ansvarsfördelning och kvarvarande beslut. Konsultera den innan du implementerar något.

## Konventioner

### Design

- **Aktiv palett:** `havsbla-korall` — djup marinblå (`#003D5B`) + varm korall (`#D96E5F`) på varm cremeaktig bakgrund (`#faf7f2`). Prototypen exponerar 13 paletter via `data-palette`-attribut på `<html>`; produktionen ska låsa in en och ta bort växlaren.
- **Typografi:** Sofia Sans för rubriker/eyebrows/siffror, Mulish för brödtext, Caveat endast för dekorativa logovarianter.
- **Fontstorlekar** är responsiva via `clamp()` i CSS-variabler (`--sm-fs-xxl` etc).
- **Spacing** är ad hoc — vanliga värden: 6, 8, 10, 12, 14, 16, 18, 20, 24, 28, 32, 40, 48, 56, 64, 80, 96 px. Container max-width 1200 px med 32 px horisontal padding (24 på tablet, 18 på mobil).
- **Radii:** `--sm-radius` 4 px (chips), `--sm-radius-lg` 10 px (kort). Pill-knappar `999px`. Båskartans rektanglar `2px`.
- **Brytpunkter:** 900 px (tablet), 640 px (mobil).
- **Vågdivider** mellan sektioner: SVG, 70 px hög, alternerar riktning via `flip`-prop.

### Båskarta

- viewBox `1000×780`. Booth-koordinater i `BOOTHS`-arrayen i `booth-map.jsx`.
- Färgkodning per storlek: 2×2 m gul, 2×3 m ljusblå, 3×3 m limegrön, N1–N12 (föreningsmontrar) rosa, bokade grå.
- `DEMO_BOOKED` Set är bara för prototypen. I produktion härleds beläggning från anmälningstabellen — duplicera inte state.

### Anmälningsformulär

- Steg 1 Företag → 2 Monter → 3 Tillägg → 4 Scen → 5 Granska
- Obligatoriska fält i Steg 1: företagsnamn, orgnr, kontaktperson, telefon, e-post kontaktperson, webbplats. Webbplats används i den publika utställarlistan.
- Tilläggslogik: Bordsstrumpa låst tills ett barbord lagts till; auto-tas bort när barbord når 0.
- Slutsåld-status togglas via adminpanelen → `localStorage["sm_addon_stock_v1"]` (måste flyttas till backend).
- Final submit blockerad tills båda checkboxarna (utställarvillkor + GDPR) är ikryssade.

### State (prototyp)

- `localStorage["sm-registrations"]` — array av anmälningar
- `localStorage["sm_addon_stock_v1"]` — `{ [addonId]: boolean }`
- `localStorage["sm-page"]` — aktuell sida
- All `localStorage`-användning **måste** ersättas med riktiga API-anrop i produktion.

### Routing

Prototypen routar via state. I produktion:

- `/` → För besökare
- `/program` → Program
- `/hitta-hit` → Hitta hit
- `/kontakt` → Kontakt
- `/for-utstallare` → För utställare
- `/anmalan` → Anmälningswizard (eller modal)
- `/admin` → Adminpanel (auth-skyddad)

### Innehåll (verbatim-strängar)

- Tagline: "Mötesplatsen för dig som vill leva hela livet"
- Datum: "Onsdag 24 februari 2027"
- Tider: "10.00 – 17.00 (dörrarna öppnar 09.30)"
- Entré: "100 kr i förköp · 120 kr vid dörren (swish / kort)"
- Adress: "Engelbrektsgatan 6, 432 41 Varberg"
- Sista anmälningsdag: 15 augusti 2027

### Kontakter (publika kontaktsidan)

- Besökare & program: Gästservice — info@arenavarberg.se — 0340-690200
- Projektledare: Camilla Bourdette — camilla.bourdette@arenavarberg.se — 0340-690204
- Försäljning/Utställare: Vivi Strindö — vivi.strindo@arenavarberg.se — 0340-690213
- Försäljning/Utställare: Susanne Carlsson — susanne.carlsson@arenavarberg.se — 0340-690200

### Tillgänglighet (att uppfylla i produktion)

- Hero-karusell ska pausa vid `prefers-reduced-motion`
- Båskartan behöver `aria-label`/roller och en icke-visuell alternativ väljare
- Verifiera label/input-koppling i alla formulärfält
- Verifiera kontrast på korall-CTA mot beige bakgrund (WCAG AA)
- Cookie-banner krävs enligt svensk lag
- Sidor för integritetspolicy + utställarvillkor saknas och måste skapas

## Git-konventioner

- Utvecklingsbranch: `claude/create-claude-md-1kiDx` (per session-direktiv)
- Skapa nya commits hellre än att amenda
- Använd `git push -u origin <branch>` vid första pushen
