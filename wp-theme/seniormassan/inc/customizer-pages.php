<?php
/**
 * Customizer-fält för redigerbart per-sida-innehåll.
 *
 * Alla rubriker, brödtexter och sektionsbilder som tidigare var hårdkodade
 * i mallarna har här fått egna fält i Utseende → Anpassa, grupperade per sida.
 *
 * Globala värden (färgpalett, mässans datum, plats, anmälan) ligger kvar i
 * inc/customizer.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =====================================================================
 * Generiska hjälpare som mallarna använder.
 * ===================================================================== */

/**
 * Returnera ett textmod med fallback.
 */
function sm_text( $id, $default = '' ) {
	$value = get_theme_mod( $id, null );
	return ( $value === null || $value === '' ) ? $default : $value;
}

/**
 * Returnera ett bild-URL från ett customizer-mod med fallback till en
 * fil i temats assets/images/ om inget är uppladdat.
 */
function sm_image_url( $id, $fallback_filename = '' ) {
	$url = get_theme_mod( $id, '' );
	if ( $url ) {
		return $url;
	}
	return $fallback_filename ? sm_image( $fallback_filename ) : '';
}

/* =====================================================================
 * Registrering
 * ===================================================================== */

function sm_register_page_customizer( $wp_customize ) {

	// Helper: text/textarea-fält
	$add_text = function ( $section, $id, $label, $default, $type = 'text', $description = '' ) use ( $wp_customize ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $default,
			'sanitize_callback' => $type === 'textarea' ? 'sanitize_textarea_field' : 'sanitize_text_field',
		) );
		$wp_customize->add_control( $id, array(
			'label'       => $label,
			'description' => $description,
			'section'     => $section,
			'type'        => $type,
		) );
	};

	// Helper: bild-fält
	$add_image = function ( $section, $id, $label, $description = '' ) use ( $wp_customize ) {
		$wp_customize->add_setting( $id, array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $id, array(
			'label'       => $label,
			'description' => $description,
			'section'     => $section,
		) ) );
	};


	/* ─── FÖRSTASIDAN — HERO-BILDER ─────────────────────────── */
	$wp_customize->add_section( 'sm_front_hero_images', array(
		'title'    => 'Förstasidan — Hero-bilder',
		'priority' => 39,
	) );
	$hero_default_files = array( 'hero-5.jpg', 'hero-1.jpg', 'hero-7.jpg', 'hero-2.jpg', 'hero-6.jpg', 'hero-3.jpg', 'hero-4.jpg' );
	foreach ( $hero_default_files as $n => $file ) {
		$num  = $n + 1;
		$desc = $num === 1
			? 'Bildspelet högst upp på startsidan. Liggande bild, gärna bred (t.ex. 1920×1080). Bild 1 visas först. Lämna tomt för temats standardbild.'
			: 'Lämna tomt för temats standardbild.';
		$add_image( 'sm_front_hero_images', 'sm_hero_image_' . $num, 'Hero-bild ' . $num, $desc );
	}


	/* ─── FÖRSTASIDAN — STATISTIK ────────────────────────────── */
	$wp_customize->add_section( 'sm_front_stats', array(
		'title'    => 'Förstasidan — statistik',
		'priority' => 40,
	) );
	$add_text( 'sm_front_stats', 'sm_stat_1_value', 'Siffra 1', '~90' );
	$add_text( 'sm_front_stats', 'sm_stat_1_label', 'Etikett 1', 'utställare' );
	$add_text( 'sm_front_stats', 'sm_stat_2_value', 'Siffra 2', '~2000' );
	$add_text( 'sm_front_stats', 'sm_stat_2_label', 'Etikett 2', 'besökare' );
	$add_text( 'sm_front_stats', 'sm_stat_3_value', 'Siffra 3', '2' );
	$add_text( 'sm_front_stats', 'sm_stat_3_label', 'Etikett 3', 'caféer + bar' );
	$add_text( 'sm_front_stats', 'sm_stat_4_value', 'Siffra 4', '10+ år' );
	$add_text( 'sm_front_stats', 'sm_stat_4_label', 'Etikett 4', 'tradition' );


	/* ─── FÖRSTASIDAN — VIDEO + PRAKTISKT ───────────────────── */
	$wp_customize->add_section( 'sm_front_video', array(
		'title'    => 'Förstasidan — video & intro',
		'priority' => 41,
	) );
	$add_text( 'sm_front_video', 'sm_video_eyebrow_front', 'Video — etikett', 'Så var det 2025' );
	$add_text( 'sm_front_video', 'sm_video_title_front',   'Video — rubrik',  'En liten smakbit av förra årets mässa.' );
	$add_text( 'sm_front_video', 'sm_video_url', 'Video — Vimeo embed-URL', 'https://player.vimeo.com/video/1178774214', 'text',
		'Klistra in Vimeo embed-URL:en (https://player.vimeo.com/video/XXXXXXX). Används både här och på "För utställare".' );


	/* ─── FÖRSTASIDAN — PRAKTISKT ───────────────────────────── */
	$wp_customize->add_section( 'sm_front_practical', array(
		'title'    => 'Förstasidan — Praktiskt',
		'priority' => 42,
	) );
	$add_text( 'sm_front_practical', 'sm_practical_eyebrow', 'Etikett', 'Praktiskt' );
	$add_text( 'sm_front_practical', 'sm_practical_title',   'Rubrik',  'Hela dagen i ett svep.' );
	$add_text( 'sm_front_practical', 'sm_practical_body',    'Brödtext', 'En dag som rymmer allt. Kom när dörrarna öppnar — eller bara en stund när det passar.', 'textarea' );
	$add_text( 'sm_front_practical', 'sm_practical_parking', 'Parkering — text', 'över 200 platser på området' );
	$add_text( 'sm_front_practical', 'sm_practical_bus',     'Buss — text',      'Linje 1, 2 och 4 till {plats}', 'text',
		'{plats} ersätts automatiskt med platsens namn från "Plats & adress".' );
	$add_image( 'sm_front_practical', 'sm_practical_image', 'Bild bredvid', 'Visas till höger om "Praktiskt"-blocket. Hög stående bild (4:5). Standardbild används om inget laddas upp.' );


	/* ─── FÖRSTASIDAN — OMRÅDEN-INTRO ───────────────────────── */
	$wp_customize->add_section( 'sm_front_zones', array(
		'title'    => 'Förstasidan — Områden-intro',
		'priority' => 43,
	) );
	$add_text( 'sm_front_zones', 'sm_zones_eyebrow', 'Etikett', 'Områden' );
	$add_text( 'sm_front_zones', 'sm_zones_title',   'Rubrik',  'Sex världar att upptäcka.' );
	$add_text( 'sm_front_zones', 'sm_zones_body',    'Brödtext', 'Utställare inom det som berör vardagen — från drömresor till digital hjälp i lugn takt.', 'textarea' );
	$add_image( 'sm_front_zones', 'sm_zones_image',  'Bild bredvid', 'Visas till vänster om områden-intron. Liggande bild (4:3).' );


	/* ─── FÖRSTASIDAN — CTA-BAND ────────────────────────────── */
	$wp_customize->add_section( 'sm_front_cta', array(
		'title'    => 'Förstasidan — CTA-band',
		'priority' => 44,
	) );
	$add_text( 'sm_front_cta', 'sm_cta_title', 'Rubrik', 'Ta med en vän — det blir roligare så.' );
	$add_text( 'sm_front_cta', 'sm_cta_body',  'Brödtext', 'Dela gärna inbjudan. Köp biljett i förköp för 100 kr, eller 120 kr vid dörren.', 'textarea' );


	/* ─── FÖRSTASIDAN — LUNCH I ROTUNDAN ────────────────────── */
	$wp_customize->add_section( 'sm_front_lunch', array(
		'title'    => 'Förstasidan — Lunch i Rotundan',
		'priority' => 44,
	) );
	$add_text( 'sm_front_lunch', 'sm_lunch_eyebrow', 'Etikett', 'Mat & paus' );
	$add_text( 'sm_front_lunch', 'sm_lunch_title',   'Rubrik',  'Lunch i Rotundan' );
	$add_text( 'sm_front_lunch', 'sm_lunch_body',    'Brödtext', 'Under mässdagen kan du inta din lunch i Rotundan. I samband med ditt förköp av entrébiljett kan du även förköpa din lunch — så slipper du köa och har platsen klar när hungern kommer.', 'textarea' );
	$add_image( 'sm_front_lunch', 'sm_lunch_image',  'Bild', 'Visas bredvid texten. Liggande bild (4:3). Standardbild används om inget laddas upp.' );


	/* ─── FÖRSTASIDAN — HÖJDPUNKTER-INTRO ───────────────────── */
	$wp_customize->add_section( 'sm_front_highlights', array(
		'title'    => 'Förstasidan — Höjdpunkter-intro',
		'priority' => 45,
	) );
	$add_text( 'sm_front_highlights', 'sm_highlights_eyebrow', 'Etikett', 'Höjdpunkter 2027' );
	$add_text( 'sm_front_highlights', 'sm_highlights_title',   'Rubrik', 'Det du inte vill missa.' );


	/* ─── PROGRAM ───────────────────────────────────────────── */
	$wp_customize->add_section( 'sm_page_program', array(
		'title'    => 'Sidan: Program',
		'priority' => 50,
	) );
	$add_text( 'sm_page_program', 'sm_program_hero_body', 'Hero — undertext', 'Allt ingår i entrén — kom och gå som du vill.', 'textarea' );
	$add_text( 'sm_page_program', 'sm_program_intro',     'Datum-text under rubriken', 'Programmet uppdateras löpande inför mässan. Med reservation för ändringar.', 'textarea' );
	$add_text( 'sm_page_program', 'sm_program_stages_eyebrow', 'Scen-intro — etikett', 'Två scener' );
	$add_text( 'sm_page_program', 'sm_program_stages_title',   'Scen-intro — rubrik',  'Mycket händer på våra scener' );
	$add_text( 'sm_page_program', 'sm_program_stages_body',    'Scen-intro — brödtext', 'Under hela mässdagen pågår program på två scener. På Stora Scenen bjuds det på livemusik, modevisningar och föreläsningar. På Utställarscenen delar våra utställare med sig av kunskap, nyheter och inspiration i kortare pass. Allt ingår i entrén — slå dig ner en stund eller följ med hela dagen.', 'textarea' );


	/* ─── KONTAKT ───────────────────────────────────────────── */
	$wp_customize->add_section( 'sm_page_kontakt', array(
		'title'    => 'Sidan: Kontakt',
		'priority' => 51,
	) );
	$add_text( 'sm_page_kontakt', 'sm_kontakt_hero_title', 'Hero — rubrik',    'Prata med oss.' );
	$add_text( 'sm_page_kontakt', 'sm_kontakt_hero_body',  'Hero — undertext', 'Vi svarar inom ett arbetsdygn.', 'textarea' );


	/* ─── HITTA HIT ─────────────────────────────────────────── */
	$wp_customize->add_section( 'sm_page_hitta', array(
		'title'    => 'Sidan: Hitta hit',
		'priority' => 52,
	) );
	$add_text( 'sm_page_hitta', 'sm_hitta_hero_title', 'Hero — rubrik',    'Arena Varberg — den stadsnära arenan.' );
	$add_text( 'sm_page_hitta', 'sm_hitta_hero_body',  'Hero — undertext', '15 minuters promenad från stationen. Fri parkering med över 200 platser.', 'textarea' );

	$add_text( 'sm_page_hitta', 'sm_parking_info_1', 'Parkering — rad 1', 'Över 200 platser på området' );
	$add_text( 'sm_page_hitta', 'sm_parking_info_2', 'Parkering — rad 2', 'Kostnadsfritt under hela mässdagen' );

	$add_text( 'sm_page_hitta', 'sm_access_info_1', 'Tillgänglighet — rad 1', 'Ramp till huvudentré' );
	$add_text( 'sm_page_hitta', 'sm_access_info_2', 'Tillgänglighet — rad 2', 'Tillgängliga toaletter på plan 1 & 2' );
	$add_text( 'sm_page_hitta', 'sm_access_info_3', 'Tillgänglighet — rad 3', 'Ljudslinga i scenområdena' );


	/* ─── FÖR UTSTÄLLARE — HERO ─────────────────────────────── */
	$wp_customize->add_section( 'sm_exhib_hero', array(
		'title'    => 'För utställare — hero',
		'priority' => 60,
	) );
	$add_text( 'sm_exhib_hero', 'sm_exhib_hero_title', 'Rubrik',   'Möt ~2000 engagerade seniorer.' );
	$add_text( 'sm_exhib_hero', 'sm_exhib_hero_body',  'Brödtext', 'Besökare tillbringar många timmar här och har god tid att besöka alla utställare — utan stress. Seniormässan är en etablerad mötesplats i Halland sedan 10+ år.', 'textarea' );
	$add_text( 'sm_exhib_hero', 'sm_exhib_hero_cta',   'Knapptext', 'Gå till anmälan →' );


	/* ─── FÖR UTSTÄLLARE — NYHET 2027 ───────────────────────── */
	$wp_customize->add_section( 'sm_exhib_novelty', array(
		'title'    => 'För utställare — Nyhet 2027',
		'priority' => 61,
	) );
	$add_text( 'sm_exhib_novelty', 'sm_novelty_badge_1', 'Badge — rad 1', 'Nyhet' );
	$add_text( 'sm_exhib_novelty', 'sm_novelty_badge_2', 'Badge — rad 2', '2027' );
	$add_text( 'sm_exhib_novelty', 'sm_novelty_eyebrow', 'Etikett', 'Nyhet 2027' );
	$add_text( 'sm_exhib_novelty', 'sm_novelty_title',   'Rubrik',  '5 digitala entrébiljetter ingår — bjud in dina kunder.' );
	$add_text( 'sm_exhib_novelty', 'sm_novelty_body',    'Brödtext', 'I varje bokad monter ingår 5 stycken kostnadsfria digitala entrébiljetter. Dessa digitala entrébiljetter kan du som utställare dela ut bland kunder, medlemmar eller någon som du önskar bjuda in till mässan.', 'textarea' );


	/* ─── FÖR UTSTÄLLARE — STORT BILDBAND ───────────────────── */
	$wp_customize->add_section( 'sm_exhib_wide', array(
		'title'    => 'För utställare — bredbild',
		'priority' => 62,
	) );
	$add_image( 'sm_exhib_wide', 'sm_exhib_wide_image', 'Bred bild', 'Heltäckande bild mellan Nyhet-rutan och "Varför ställa ut?".' );


	/* ─── FÖR UTSTÄLLARE — VARFÖR STÄLLA UT ─────────────────── */
	$wp_customize->add_section( 'sm_exhib_reasons', array(
		'title'    => 'För utställare — Varför ställa ut',
		'priority' => 63,
	) );
	$add_text( 'sm_exhib_reasons', 'sm_why_eyebrow', 'Etikett', 'Varför ställa ut?' );
	$add_text( 'sm_exhib_reasons', 'sm_why_title',   'Rubrik',  'Kvalificerade möten, inte flyktiga förbipasseranden.' );

	$add_text( 'sm_exhib_reasons', 'sm_why_1_title', 'Skäl 1 — rubrik',  'Målgrupp med tid & lust' );
	$add_text( 'sm_exhib_reasons', 'sm_why_1_body',  'Skäl 1 — brödtext', 'Seniorer är en målgrupp som växer varje år — med både tid, nyfikenhet och köpkraft.', 'textarea' );

	$add_text( 'sm_exhib_reasons', 'sm_why_2_title', 'Skäl 2 — rubrik',  'Långa kvalitetsmöten' );
	$add_text( 'sm_exhib_reasons', 'sm_why_2_body',  'Skäl 2 — brödtext', 'Besökare stannar många timmar tack vare scen, caféer, bar och lunchservering. Tid för riktiga samtal.', 'textarea' );

	$add_text( 'sm_exhib_reasons', 'sm_why_3_title', 'Skäl 3 — rubrik',  'Vi marknadsför åt dig' );
	$add_text( 'sm_exhib_reasons', 'sm_why_3_body',  'Skäl 3 — brödtext', 'Annonser i dagspress, digitala kanaler, vägpratare och via organisationer. 2027 även mot Sjuhärad.', 'textarea' );


	/* ─── FÖR UTSTÄLLARE — UTSTÄLLARSCEN-CALLOUT ────────────── */
	$wp_customize->add_section( 'sm_exhib_stage', array(
		'title'    => 'För utställare — Utställarscen-ruta',
		'priority' => 64,
	) );
	$add_text( 'sm_exhib_stage', 'sm_stage_callout_eyebrow', 'Etikett', 'Utställarscen' );
	$add_text( 'sm_exhib_stage', 'sm_stage_callout_title',   'Rubrik',  'Nå ut med ditt budskap på vår utställarscen.' );
	$add_text( 'sm_exhib_stage', 'sm_stage_callout_body',    'Brödtext', 'Samtidigt som vi släpper upp monterbokningen släpper vi också upp tidsbokningen till vår utställarscen. Det är kostnadsfritt och först till kvarn som gäller — så passa på och knip dessa eftertraktade platserna.', 'textarea' );
	$add_image( 'sm_exhib_stage', 'sm_stage_callout_image',  'Bild', 'Liggande bild (4:3) till höger om texten.' );


	/* ─── FÖR UTSTÄLLARE — MONTERPAKET-INTRO ────────────────── */
	$wp_customize->add_section( 'sm_exhib_packages', array(
		'title'    => 'För utställare — Monterpaket-intro',
		'priority' => 65,
	) );
	$add_text( 'sm_exhib_packages', 'sm_packages_eyebrow', 'Etikett',   'Monterpaket' );
	$add_text( 'sm_exhib_packages', 'sm_packages_title',   'Rubrik',    'Tre storlekar. Alla inklusive det grundläggande.' );
	$add_text( 'sm_exhib_packages', 'sm_addons_title',     'Tillägg — rubrik', 'Skräddarsy din monter.' );
	$add_text( 'sm_exhib_packages', 'sm_addons_intro',     'Tillägg — brödtext', 'Vill du ha större utrymme kan du välja flera montrar. Du kan också lägga till produkter i din monter — t.ex. golv i olika färger, bord, stolar, utställarlunch m.m.', 'textarea' );
	$add_text( 'sm_exhib_packages', 'sm_packages_cta',     'Knapptext längst ner', 'Anmäl din monter →' );


	/* ─── FÖR UTSTÄLLARE — STÄMNINGEN 2025 ──────────────────── */
	$wp_customize->add_section( 'sm_exhib_atmosphere', array(
		'title'    => 'För utställare — Stämningen 2025',
		'priority' => 66,
	) );
	$add_text( 'sm_exhib_atmosphere', 'sm_atmosphere_eyebrow', 'Etikett', 'Stämningen 2025' );
	$add_text( 'sm_exhib_atmosphere', 'sm_atmosphere_title',   'Rubrik',  'Så här ser det ut att ställa ut hos oss.' );


	/* ─── FÖR UTSTÄLLARE — CITAT ────────────────────────────── */
	$wp_customize->add_section( 'sm_exhib_quote', array(
		'title'    => 'För utställare — Citat',
		'priority' => 67,
	) );
	$add_text( 'sm_exhib_quote', 'sm_quote_text',   'Citat',     '”Många åker till vår butik i Åsa och handlar direkt efter att vi träffats på mässan.”', 'textarea' );
	$add_text( 'sm_exhib_quote', 'sm_quote_author', 'Författare', 'Annett Wiktorsson, Modestugan' );
	$add_text( 'sm_exhib_quote', 'sm_quote_meta',   'Tillägg',   '(deltagit sedan 2015)' );
	$add_image( 'sm_exhib_quote', 'sm_quote_image', 'Porträtt',  'Rund bild bredvid citatet. Kvadratiskt format rekommenderas.' );

}
add_action( 'customize_register', 'sm_register_page_customizer' );
