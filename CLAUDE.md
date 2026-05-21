# CLAUDE.md

Vägledning för Claude Code (claude.ai/code) när den arbetar i detta repo.

## Vad detta repo är

Repot innehåller två saker:

1. **`design_handoff_seniormassan/`** — den ursprungliga HTML-prototypen med React + Babel inline. Designspec, tokens och referensbeteende. *Inte produktionskod.*
2. **`wp-theme/seniormassan/`** — det riktiga WordPress-temat som körs på www.seniormassanvarberg.se. Det här är vad användaren laddar upp till servern.

Sajten är för **Seniormässan på Arena Varberg**, onsdag 24 februari 2027. Målgrupp: seniorer 55+. Allt innehåll på **svenska**.

## Status (senast uppdaterad)

**Tema-version: 0.13.0** — driftsatt på `https://seniormassanvarberg.se` (HTTPS aktivt via Let's Encrypt; DNS pekar på Oderland).

### Klart
- **5 publika sidor**: För besökare, Program, Hitta hit, Kontakt, För utställare
- **Auto-populerande utställarlista** på förstasidan (hämtar från `sm_registration`-CPT)
- **Anmälningsformulär** — wizard-modal med 5 steg, stepper, sticky footer, sammanfattning, scenpassbokning, monter-variantval, matta som skalar med antal montrar
- **Admin**: Anmälningar i WP-admin med CPT, möjlighet att markera anmälningar som avbokade, ändra eller frigöra scenpass på en bokning, manuellt blockera enskilda montrar, ladda upp tillval-ikoner per produkt (+ per färgvariant), redigera monter- och tillvalspriser via Anmälningar → Priser
- **Editerbarhet v1** via Customizer (Utseende → Anpassa):
  - Färgpalett (13 paletter att välja mellan)
  - Hero-rubrik och ingress på förstasidan
  - Mässans datum, öppettider, dörröppning, entrépris, slogan
  - Plats & adress (namn, gatuadress, postnummer, mejl, växel)
  - Anmälan: hallplan-bild, sista anmälningsdag, notismejl
- **Editerbarhet v3 (klart)**: Customizer-fält per sida för **alla** rubriker, brödtexter, etiketter, video-URL och sektionsbilder. Grupperade i ~20 sektioner i Anpassa-panelen:
  - Förstasidan: statistik (4 par), video & intro, Praktiskt (+ bild), Områden-intro (+ bild), CTA-band, Höjdpunkter-intro
  - Program: hero-undertext, datum-text
  - Kontakt: hero-rubrik + undertext
  - Hitta hit: hero + parkering/tillgänglighet-rader
  - För utställare: hero, Nyhet 2027-callout, bredbild, Varför ställa ut (3 skäl), Utställarscen-ruta (+ bild), Monterpaket-intro, Tillägg-intro, Stämningen 2025, citat (text + författare + porträtt)
  - Implementerat i `inc/customizer-pages.php` med generiska hjälpare `sm_text( $id, $default )` och `sm_image_url( $id, $fallback_filename )`. Alla fält har defaults så sidan ser exakt likadan ut tills något ändras.
- **Cookie-banner** (`inc/cookie-banner.php`): diskret bottenfält tills besökaren klickar Acceptera/Avvisa. Valet sparas i cookie `sm_cookie_consent` i 365 dagar. Google Maps på `/hitta-hit/` gating med `.sm-embed-gated` — placeholder visar text + "Acceptera och visa karta"-knapp + extern länk till maps.google.com tills consent. Vimeo-videor körs med `dnt=1` (Do Not Track-läge, ingen consent krävs enligt Vimeo). Banner-texterna redigerbara via Utseende → Anpassa → Cookie-banner. "Läs mer"-länken pekar på WP:s privacy-page (Inställningar → Privacy) eller på en URL i Customizer.
- **Editerbarhet v2 (klart)**: Custom Post Types för redigerbara listor via WP-admin:
  - `sm_program_item` — programpunkter (tid + scen + namn + beskrivning). Sidan `/program/`.
  - `sm_contact` — kontaktpersoner (roll + namn + e-post + telefon). Sidan `/kontakt/`.
  - `sm_highlight` — höjdpunkter (etikett + rubrik + beskrivning + utvald bild). Förstasidan.
  - `sm_zone` — områden (namn + beskrivning, auto-numrerade 01/02/... från sortordning). Förstasidan.
  Alla läser från CPT med fallback till hårdkodad lista tills första posten är inlagd. Sortering via "Ordning" i Sidattribut. Hjälpfunktioner: `sm_program_items()`, `sm_contacts()`, `sm_highlights()`, `sm_zones()` + motsvarande `sm_has_*()`.

### Att göra (nästa iterationer)
- **Integritetspolicy + utställarvillkor** (sidor saknas — skapa en WP-sida och sätt slug till `integritet`, eller koppla via Inställningar → Privacy så används den automatiskt av cookie-bannerns "Läs mer"-länk)
- **Tillgänglighetspass** (prefers-reduced-motion på hero-karusell, aria-label på båskartan)

## Stack

### Produktion (faktisk)
- **WordPress 6.x** på Oderland-webbhotell (cPanel, PHP, MySQL)
- **Custom tema**: `wp-theme/seniormassan/` (inte block-tema, klassisk PHP-mallhierarki)
- Inga betalplugins — använder bara WP-kärnan
- Akismet-plugin borttagen
- Permalänkar: "Inläggsnamn" (kräver `.htaccess` med standard WP-rewrites)

### Prototyp (referens)
- React 18 (UMD från unpkg) + Babel standalone — bara för design_handoff_seniormassan/, ska inte byggas vidare
- Plain CSS med custom properties + 13 paletter via `data-palette`
- Google Fonts: Sofia Sans (display), Mulish (body), Caveat (dekorativ)

## Kommandon

### Visa prototypen lokalt

```bash
cd design_handoff_seniormassan
python3 -m http.server 8000
# → http://localhost:8000/Webbplats.html
```

### Bygga / testa / linta

Det finns ingen byggprocess för temat — det är ren PHP. För att se ändringar på servern: klona repot lokalt, paketera om `wp-theme/seniormassan/` som ZIP, ladda upp via WP-admin → Utseende → Teman → Lägg till nytt → Ladda upp. Bumpa versionen i `style.css` + `functions.php` för cache-busting.

## Mappstruktur

```
.
├── CLAUDE.md
├── design_handoff_seniormassan/        Originalreferens (orörd)
│   ├── README.md                       Auktoritativ designspec
│   ├── Webbplats.html
│   ├── styles.css
│   ├── site-shell.jsx
│   ├── site-pages.jsx
│   ├── booth-map.jsx
│   ├── registration.jsx
│   ├── exhibitor-list.jsx
│   ├── assets/                         Logotyper + hero-foton
│   └── images/                         Stockfoton + porträtt
└── wp-theme/
    └── seniormassan/                   WordPress-tema (produktion)
        ├── style.css                   Tema-header
        ├── functions.php               Bootstrap, enqueue, includes
        ├── header.php                  Site header med nav
        ├── footer.php                  Footer med kontakt
        ├── index.php                   Fallback
        ├── page.php                    Standardmall för sidor
        ├── front-page.php              För besökare (hero, stats, video, områden, höjdpunkter, utställarlista)
        ├── page-program.php            Programschema (Stora Scen + Utställarscenen)
        ├── page-hitta-hit.php          Karta + adress + tillgänglighet
        ├── page-kontakt.php            4 kontaktkort
        ├── page-for-utstallare.php     Paket, tillval, Nyhet 2027, citat
        ├── page-anmalan.php            Wizard-formulär (modal-stil)
        ├── inc/
        │   ├── booth-data.php          BOOTHS-array, priser, tillvalsdefinitioner
        │   ├── registration-cpt.php    CPT sm_registration + admin-kolumner + blockera-sida
        │   ├── registration-handler.php POST-validering, sparar CPT, skickar mejl
        │   ├── customizer.php          Alla Customizer-inställningar + hjälpfunktioner
        │   └── addon-admin.php         Admin-sida för tillval-ikoner (Media Library-picker)
        ├── template-parts/
        │   ├── wave-divider.php        SVG-våg mellan sektioner
        │   ├── page-hero.php           Färgad rubrik-banner
        │   ├── info-block.php          Eyebrow + key/value-rader
        │   ├── booth-map.php           SVG-karta (ersatt av uppladdad bild i v0.4+, kvar för fallback)
        │   └── exhibitor-list.php      Sök + bokstavsfilter + 3-kolumns kortgrid
        └── assets/
            ├── css/main.css            Designtokens, paletter, typografi (kopia av design_handoff/styles.css)
            └── images/                 Logotyper + foton (kopia)
```

## Konventioner

### Customizer som källa för redigerbara värden

Hjälpfunktioner i `inc/customizer.php` är källan till sanningen för alla globala värden. Använd dem i mallarna istället för att hårdkoda:

- `sm_palette()` — `'havsbla-korall'` etc.
- `sm_hero_h1_main()`, `sm_hero_h1_accent()`, `sm_hero_body()`
- `sm_event_date_long()`, `sm_event_hours()`, `sm_event_doors()`, `sm_event_entry()`, `sm_event_tagline()`
- `sm_venue_name()`, `sm_venue_street()`, `sm_venue_zip()`, `sm_venue_main_email()`, `sm_venue_main_phone()`
- `sm_booth_map_image_url()`, `sm_booking_email()`, `sm_last_registration_date()`
- `sm_addon_icon( $addon_id, $variant = null )`

När du lägger till ett nytt redigerbart fält: lägg till settingen i `sm_customizer_register()` och en hjälpfunktion längst ner i samma fil.

### Custom Post Type "sm_registration"

Varje utställaranmälan blir en post. Metafält (`_sm_*`):

- `_sm_company`, `_sm_orgnr`, `_sm_invoice_address`, `_sm_invoice_email`
- `_sm_contact_name`, `_sm_contact_email`, `_sm_contact_phone`
- `_sm_website`, `_sm_no_website`, `_sm_description`
- `_sm_booths` (array av booth-ID), `_sm_addons` (qty per addon-id), `_sm_addon_variants` (variant per addon-id)
- `_sm_stage_slot` (t.ex. `"12:00"`)
- `_sm_special_requests`, `_sm_is_forening`
- `_sm_total` (int, kr), `_sm_status` (`'pending'` | `'confirmed'` | `'cancelled'`)
- `_sm_submitted_at`

Booth-bokningsstatus härleds genom att samla alla `_sm_booths` från CPT-poster där `_sm_status != 'cancelled'`, plus options-listan `sm_blocked_booths` (manuellt blockerade via admin). Se `sm_booked_booth_ids()`.

Scenpass-bokningar härleds på liknande sätt — `sm_taken_stage_slots()`.

### Wizard-formulärets flöde

Formuläret på `/anmalan/` är en hybrid: HTML-formulär med JS-stepper.

- Alla 5 steg renderas i DOM:en samtidigt; JS togglar `is-active`-klass.
- `readState()` läser DOM, `computeTotal()` beräknar pris.
- `canNext(step, state)` validerar per steg. Inga `required`-attribut används — `novalidate` är satt på formuläret eftersom HTML5-validering kraschar på dolda fält.
- Submit POSTar till `admin-post.php` med `action=sm_register`. Handlern ligger i `inc/registration-handler.php`.

### Att lägga till ett nytt tillval

Redigera `sm_addons()` i `inc/booth-data.php`. Flaggor:

- `'variants' => [ 'Färg1', ... ]` — visar variant-knappar
- `'scales_with_booths' => true` — kvantitet räknas automatiskt från antal valda montrar (som montermatta), ingen quantity-input visas
- `'requires' => 'barbord'` — ej implementerat på UI än, men finns i specen

### Innehåll (verbatim-strängar i designen)

Programschema, kontakter, höjdpunkter och områden ligger nu i CPTs (se Editerbarhet v2 ovan). Sidmallarna har fortfarande fallback-arrayer med originalinnehållet som visas tills första CPT-posten är inlagd — säkerhetsnät under datamigreringen, kan tas bort när redaktören är klar.

Kvar som hårdkodat:

- Monterpaket-text på "För utställare" — hårdkodade i `page-for-utstallare.php`

### Routing (slugs)

WordPress-sidor som måste finnas:

- `/` (For besökare, satt som statisk startsida) — använder `front-page.php`
- `/program/` → `page-program.php`
- `/hitta-hit/` → `page-hitta-hit.php`
- `/kontakt/` → `page-kontakt.php`
- `/for-utstallare/` → `page-for-utstallare.php`
- `/anmalan/` → `page-anmalan.php`

## Server (Oderland)

- **Kontot**: arenavarberg på cPanel, primärdomän `arenavarberg.se`
- **Domänen**: `seniormassanvarberg.se` ligger som addon-domän
- **DNS**: pekar på Oderlands nameservers (ns1/ns2/ns3.oderland.com). Server-IP: `91.201.63.11`.
- **HTTPS**: aktivt via Let's Encrypt (AutoSSL). `wp-config.php` har `WP_HOME`/`WP_SITEURL` satt till `https://seniormassanvarberg.se`. `.htaccess` tvingar HTTP → HTTPS-redirect.
- **Permalänkar**: "Inläggsnamn" — `.htaccess` skapad manuellt i WP-roten med standard WP-rewrites.
- **AutoSSL-varningar**: cPanels auto-genererade service-subdomäner (`webdisk.`, `cpcalendars.`, `cpcontacts.`, `ipv6.`, `seniormassanvarberg.se.arenavarberg.se` osv.) saknar DNS-poster och triggar varningar. Exkluderade från AutoSSL via SSL/TLS Status → Exclude Domains.

## Git-konventioner

- Utvecklingsbranch: tidigare `claude/create-claude-md-1kiDx`, nu pushas direkt till `main` per användarens önskemål.
- Skapa nya commits hellre än att amenda.
- Bumpa `SM_THEME_VERSION` i `functions.php` *och* `Version:` i `style.css`-huvudet vid varje funktionsändring för cache-busting i WP.
