<?php
/**
 * WP Customizer-inställningar.
 *
 * Inställningar som administratören kan ändra själv via
 * Utseende → Anpassa.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tillgängliga paletter — matchar de som finns i assets/css/main.css.
 * Värdet blir <html data-palette="...">.
 */
function sm_available_palettes() {
	return array(
		'havsbla-korall' => 'Havsblå · korall (standard)',
		'havsbla'        => 'Havsblå · gul',
		'havsbla-rost'   => 'Havsblå · rost',
		'havsbla-rosa'   => 'Havsblå · rosa',
		'havsbla-salvia' => 'Havsblå · salvia',
		'havsbla-koppar' => 'Havsblå · koppar',
		'havsbla-creme'  => 'Havsblå · creme',
		'terrakotta'     => 'Terrakotta',
		'plommon'        => 'Plommon',
		'skog'           => 'Skog',
		'kust'           => 'Kust',
		'varmgra'        => 'Varmgrå',
		'duvgra-korall'  => 'Duvgrå · korall',
	);
}

function sm_customizer_register( $wp_customize ) {

	// ─── FÄRGER ───────────────────────────────────────────
	$wp_customize->add_section( 'sm_colors', array(
		'title'    => 'Färger',
		'priority' => 20,
	) );

	$wp_customize->add_setting( 'sm_palette', array(
		'default'           => 'havsbla-korall',
		'sanitize_callback' => 'sm_sanitize_palette',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'sm_palette', array(
		'label'       => 'Färgpalett',
		'description' => 'Påverkar primärfärg (mörkblå), accent (korall) och dekorativa toner i hela sajten. Förhandsgranska genom att välja och spara.',
		'section'     => 'sm_colors',
		'type'        => 'select',
		'choices'     => sm_available_palettes(),
	) );


	// ─── HERO / FÖRSTASIDAN ────────────────────────────────
	$wp_customize->add_section( 'sm_hero', array(
		'title'    => 'Förstasidan — hero',
		'priority' => 25,
	) );

	$wp_customize->add_setting( 'sm_hero_h1_main', array(
		'default'           => 'Seniormässan',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sm_hero_h1_main', array(
		'label'   => 'Hero-rubrik (huvuddel)',
		'section' => 'sm_hero',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'sm_hero_h1_accent', array(
		'default'           => '10 mars 2027',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sm_hero_h1_accent', array(
		'label'       => 'Hero-rubrik (accent)',
		'description' => 'Visas i guld/korall efter huvuddelen.',
		'section'     => 'sm_hero',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'sm_hero_body', array(
		'default'           => 'En dag fylld av möten, upplevelser och inspiration — närmare 90 utställare, scenprogram, caféer och restaurang.',
		'sanitize_callback' => 'sanitize_textarea_field',
	) );
	$wp_customize->add_control( 'sm_hero_body', array(
		'label'   => 'Hero-ingress',
		'section' => 'sm_hero',
		'type'    => 'textarea',
	) );


	// ─── MÄSSANS INFORMATION ──────────────────────────────
	$wp_customize->add_section( 'sm_event', array(
		'title'    => 'Mässans information',
		'priority' => 26,
	) );

	$wp_customize->add_setting( 'sm_event_date_long', array(
		'default'           => 'Onsdag 10 mars 2027',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sm_event_date_long', array(
		'label'   => 'Datum (skrivet)',
		'section' => 'sm_event',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'sm_event_hours', array(
		'default'           => '10.00 – 17.00',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sm_event_hours', array(
		'label'   => 'Öppettider',
		'section' => 'sm_event',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'sm_event_doors', array(
		'default'           => 'Dörrarna öppnar 09.30',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sm_event_doors', array(
		'label'   => 'Dörröppning (text)',
		'section' => 'sm_event',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'sm_event_entry', array(
		'default'           => '100 kr i förköp · 120 kr vid dörren (swish / kort)',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sm_event_entry', array(
		'label'   => 'Entrépris (text)',
		'section' => 'sm_event',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'sm_event_tagline', array(
		'default'           => 'Mötesplatsen för dig som vill leva hela livet',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sm_event_tagline', array(
		'label'       => 'Slogan',
		'description' => 'Visas i footer och som meta-beskrivning.',
		'section'     => 'sm_event',
		'type'        => 'text',
	) );


	// ─── ADRESS & PLATS ──────────────────────────────────
	$wp_customize->add_section( 'sm_venue', array(
		'title'    => 'Plats & adress',
		'priority' => 27,
	) );

	$wp_customize->add_setting( 'sm_venue_name', array(
		'default'           => 'Arena Varberg',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sm_venue_name', array(
		'label'   => 'Platsens namn',
		'section' => 'sm_venue',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'sm_venue_street', array(
		'default'           => 'Kattegattsvägen 26',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sm_venue_street', array(
		'label'   => 'Gatuadress',
		'section' => 'sm_venue',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'sm_venue_zip', array(
		'default'           => '432 50 Varberg',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sm_venue_zip', array(
		'label'   => 'Postnummer + ort',
		'section' => 'sm_venue',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'sm_venue_main_email', array(
		'default'           => 'info@arenavarberg.se',
		'sanitize_callback' => 'sanitize_email',
	) );
	$wp_customize->add_control( 'sm_venue_main_email', array(
		'label'   => 'Allmän e-postadress',
		'section' => 'sm_venue',
		'type'    => 'email',
	) );

	$wp_customize->add_setting( 'sm_venue_main_phone', array(
		'default'           => '0340-690200',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sm_venue_main_phone', array(
		'label'   => 'Växel/telefonnummer',
		'section' => 'sm_venue',
		'type'    => 'text',
	) );


	// ─── ANMÄLAN ─────────────────────────────────────────
	$wp_customize->add_section( 'sm_registration', array(
		'title'    => 'Anmälan',
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'sm_booth_map_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'sm_booth_map_image', array(
		'label'       => 'Hallplan (bild)',
		'description' => 'Visas överst i anmälningsformuläret. Rekommenderad bredd: 2000 px.',
		'section'     => 'sm_registration',
	) ) );

	$wp_customize->add_setting( 'sm_last_registration_date', array(
		'default'           => '15 augusti 2027',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sm_last_registration_date', array(
		'label'   => 'Sista anmälningsdag (text)',
		'section' => 'sm_registration',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'sm_booking_email', array(
		'default'           => 'bokning@arenavarberg.se',
		'sanitize_callback' => 'sanitize_email',
	) );
	$wp_customize->add_control( 'sm_booking_email', array(
		'label'       => 'Mejladress för anmälningsnotiser',
		'description' => 'Notiser om nya anmälningar skickas hit.',
		'section'     => 'sm_registration',
		'type'        => 'email',
	) );


	// ─── FÖRKÖP / BILJETTER ──────────────────────────────
	$wp_customize->add_section( 'sm_tickets', array(
		'title'    => 'Förköp / biljetter',
		'priority' => 28,
	) );

	$wp_customize->add_setting( 'sm_ticket_url', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'sm_ticket_url', array(
		'label'       => 'Länk till biljettförköp',
		'description' => 'Klistra in URL:en till biljettshoppen. Den flytande "Förköp entré"-knappen visas på de publika sidorna först när en länk är ifylld här. Lämna tom för att dölja knappen.',
		'section'     => 'sm_tickets',
		'type'        => 'url',
	) );

	$wp_customize->add_setting( 'sm_ticket_label', array(
		'default'           => 'Förköp entré',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sm_ticket_label', array(
		'label'   => 'Knapptext',
		'section' => 'sm_tickets',
		'type'    => 'text',
	) );
}
add_action( 'customize_register', 'sm_customizer_register' );

function sm_sanitize_palette( $value ) {
	return array_key_exists( $value, sm_available_palettes() ) ? $value : 'havsbla-korall';
}

// ─── Hjälpfunktioner som mallarna använder ────────────────────────────────
function sm_palette()                  { return get_theme_mod( 'sm_palette', 'havsbla-korall' ); }
function sm_booth_map_image_url()      { return get_theme_mod( 'sm_booth_map_image', '' ); }
function sm_booking_email()            { return get_theme_mod( 'sm_booking_email', 'bokning@arenavarberg.se' ); }
function sm_last_registration_date()   { return get_theme_mod( 'sm_last_registration_date', '15 augusti 2027' ); }
function sm_ticket_url()               { return get_theme_mod( 'sm_ticket_url', '' ); }
function sm_ticket_label()             { return get_theme_mod( 'sm_ticket_label', 'Förköp entré' ); }

function sm_hero_h1_main()             { return get_theme_mod( 'sm_hero_h1_main', 'Seniormässan' ); }
function sm_hero_h1_accent()           { return get_theme_mod( 'sm_hero_h1_accent', '10 mars 2027' ); }
function sm_hero_body()                { return get_theme_mod( 'sm_hero_body', 'En dag fylld av möten, upplevelser och inspiration — närmare 90 utställare, scenprogram, caféer och restaurang.' ); }

function sm_event_date_long()          { return get_theme_mod( 'sm_event_date_long', 'Onsdag 10 mars 2027' ); }
function sm_event_hours()              { return get_theme_mod( 'sm_event_hours', '10.00 – 17.00' ); }
function sm_event_doors()              { return get_theme_mod( 'sm_event_doors', 'Dörrarna öppnar 09.30' ); }
function sm_event_entry()              { return get_theme_mod( 'sm_event_entry', '100 kr i förköp · 120 kr vid dörren (swish / kort)' ); }
function sm_event_tagline()            { return get_theme_mod( 'sm_event_tagline', 'Mötesplatsen för dig som vill leva hela livet' ); }

function sm_venue_name()               { return get_theme_mod( 'sm_venue_name', 'Arena Varberg' ); }
function sm_venue_street()             { return get_theme_mod( 'sm_venue_street', 'Kattegattsvägen 26' ); }
function sm_venue_zip()                { return get_theme_mod( 'sm_venue_zip', '432 50 Varberg' ); }
function sm_venue_main_email()         { return get_theme_mod( 'sm_venue_main_email', 'info@arenavarberg.se' ); }
function sm_venue_main_phone()         { return get_theme_mod( 'sm_venue_main_phone', '0340-690200' ); }
