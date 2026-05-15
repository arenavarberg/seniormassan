# Seniormässan – WordPress-tema

WordPress-tema för www.seniormassanvarberg.se. Översätter designhandoffen i `design_handoff_seniormassan/` till en riktig WordPress-sajt.

## Status

**0.1.0 – skelett.** Förstasidan ("För besökare") finns i grunden, övriga sidor använder standard-mallen tills de byggs ut.

Klart:
- Tema-bootstrap, font-/CSS-enqueue
- Header med navigation
- Footer med kontakt + arrangör
- `front-page.php`: hero med carousel, statistik, praktiskt-info, CTA-band
- `page.php`: enkel sidmall

Att göra (kommande commits):
- `page-program.php` – programschema, två scener
- `page-hitta-hit.php` – karta, parkering, tillgänglighet
- `page-kontakt.php` – fyra kontaktkort
- `page-for-utstallare.php` – paketpriser, tillägg, Nyhet 2027
- Anmälningsformulär (Fas 1: vanligt formulär; Fas 2: båskarta som plugin)
- Höjdpunkter + utställarlista på förstasidan

## Installera

1. Ladda upp hela mappen `wp-theme/seniormassan/` till `/wp-content/themes/seniormassan/` på servern (via cPanel File Manager eller FTP).
2. WP-admin → Utseende → Teman → aktivera "Seniormässan".
3. Skapa sidor (Sidor → Skapa ny) med slugs `program`, `hitta-hit`, `kontakt`, `for-utstallare`.
4. Inställningar → Läsning → "Din startsida visar" = **Statisk sida** och välj en sida med slug `for-besokare` (eller skapa en tom sida och välj den som startsida).

## Struktur

```
wp-theme/seniormassan/
├── style.css                  WP-temahuvud (laddar inga regler själv)
├── functions.php              Enqueue, navmeny, palette-attribut
├── header.php                 Site header + nav
├── footer.php                 Site footer
├── index.php                  Fallback
├── page.php                   Standard sidmall
├── front-page.php             För besökare (startsida)
└── assets/
    ├── css/main.css           Designtokens + utilities (kopia av design_handoff_seniormassan/styles.css)
    └── images/                Logotyper + foton
```

## Konventioner

- Palett: `havsbla-korall` – sätts på `<html data-palette="">` via filter i `functions.php`.
- Bildhjälpare: `sm_image( 'hero-1.jpg' )` ger URL till `assets/images/hero-1.jpg`.
- Inline-styles används för layout som följer specen tätt; flytta till klasser när mönster återkommer.
- Allt innehåll på svenska.
