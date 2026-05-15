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

function sm_customizer_register( $wp_customize ) {
	$wp_customize->add_section( 'sm_registration', array(
		'title'    => 'Anmälan',
		'priority' => 30,
	) );

	// Hallplan-bild
	$wp_customize->add_setting( 'sm_booth_map_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'sm_booth_map_image', array(
		'label'       => 'Hallplan (bild)',
		'description' => 'Bilden visas överst i anmälningsformuläret som referens. Rekommenderad bredd: 2000 px.',
		'section'     => 'sm_registration',
	) ) );

	// Sista anmälningsdag (visas i flera texter)
	$wp_customize->add_setting( 'sm_last_registration_date', array(
		'default'           => '15 augusti 2027',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sm_last_registration_date', array(
		'label'   => 'Sista anmälningsdag (text)',
		'section' => 'sm_registration',
		'type'    => 'text',
	) );

	// Mejladress för anmälningsnotiser
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
}
add_action( 'customize_register', 'sm_customizer_register' );

function sm_booth_map_image_url() {
	return get_theme_mod( 'sm_booth_map_image', '' );
}

function sm_booking_email() {
	return get_theme_mod( 'sm_booking_email', 'bokning@arenavarberg.se' );
}

function sm_last_registration_date() {
	return get_theme_mod( 'sm_last_registration_date', '15 augusti 2027' );
}
