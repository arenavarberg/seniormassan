# CLAUDE.md

Vägledning för Claude Code (claude.ai/code) när den arbetar i detta repo.

## Vad detta repo är

Repot innehåller två saker:

1. **`design_handoff_seniormassan/`** — den ursprungliga HTML-prototypen med React + Babel inline. Designspec, tokens och referensbeteende. *Inte produktionskod.*
2. **`wp-theme/seniormassan/`** — det riktiga WordPress-temat som körs på www.seniormassanvarberg.se. Det här är vad användaren laddar upp till servern.

Sajten är för **Seniormässan på Arena Varberg**, onsdag 24 februari 2027. Målgrupp: seniorer 55+. Allt innehåll på **svenska**.

## Status (senast uppdaterad)

**Tema-version: 0.8.0** — driftsatt på `http://seniormassanvarberg.se` (HTTP fortfarande; SSL kommer när Loopia-fakturan är betald och DNS pekas om till Oderland).

### Klart
- **5 publika sidor**: För besökare, Program, Hitta hit, Kontakt, För utställare
- **Auto-populerande utställarlista** på förstasidan (hämtar från `sm_registration`-CPT)
- **Anmälningsformulär** — wizard-modal med 5 steg, stepper, sticky footer, sammanfattning, scenpassbokning, monter-variantval, matta som skalar med antal montrar
- **Admin**: Anmälningar i WP-admin med CPT, möjlighet att markera anmälningar som avbokade, manuellt blockera enskilda montrar, ladda upp tillval-ikoner per produkt (+ per färgvariant)
- **Editerbarhet v1** via Customizer (Utseende → Anpassa):
  - Färgpalett (13 paletter att välja mellan)
  - Hero-rubrik och ingress på förstasidan
  - Mässans datum, öppettider, dörröppning, entrépris, slogan
  - Plats & adress (namn, gatuadress, postnummer, mejl, växel)
  - Anmälan: hallplan-bild, sista anmälningsdag, notismejl

### Att göra (nästa iterationer)
- **Editerbarhet v2** — Custom Post Types för listor: program, kontakter, höjdpunkter, områden
- **Tillval-priser** redigerbara via admin (just nu hårdkodade i `inc/booth-data.php`)
- **SSL** via Let's Encrypt (kräver att DNS pekar rätt först)
- **Cookie-banner** (krävs enligt svensk lag)
- **Integritetspolicy + utställarvillkor** (sidor saknas)
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

Dessa är fortfarande hårdkodade i temat på vissa ställen — flytta gärna till Customizer eller CPT när möjligt:

- Områden ("Sex världar att upptäcka") — `front-page.php`, hårdkodade i array
- Höjdpunkter 2027 — `front-page.php`, hårdkodade i array
- Programschema — `page-program.php`, hårdkodade i array
- Kontaktpersoner — `page-kontakt.php`, hårdkodade i array
- Monterpaket-text på "För utställare" — hårdkodade

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
- **DNS**: pekar fortfarande på Loopia. När fakturan är betald: byt nameservers till Oderlands ns1/ns2/ns3.oderland.com.
- **Tillfällig åtkomst** under tiden: hosts-fil på utvecklarens Mac mappar `seniormassanvarberg.se` → `91.201.63.11`.
- **HTTP/HTTPS**: bara HTTP fungerar tills DNS pekar rätt och Let's Encrypt kan utfärda cert. `wp-config.php` har manuellt satt `WP_HOME`/`WP_SITEURL` till `http://...`.
- **Permalänkar**: "Inläggsnamn" — `.htaccess` skapad manuellt i WP-roten med standard WP-rewrites.

## Git-konventioner

- Utvecklingsbranch: tidigare `claude/create-claude-md-1kiDx`, nu pushas direkt till `main` per användarens önskemål.
- Skapa nya commits hellre än att amenda.
- Bumpa `SM_THEME_VERSION` i `functions.php` *och* `Version:` i `style.css`-huvudet vid varje funktionsändring för cache-busting i WP.
