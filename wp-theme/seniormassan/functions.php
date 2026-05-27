<?php
/**
 * Seniormässan – theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SM_THEME_VERSION', '0.15.3' );

require_once get_theme_file_path( 'inc/customizer.php' );
require_once get_theme_file_path( 'inc/customizer-pages.php' );
require_once get_theme_file_path( 'inc/booth-data.php' );
require_once get_theme_file_path( 'inc/registration-cpt.php' );
require_once get_theme_file_path( 'inc/registration-handler.php' );
require_once get_theme_file_path( 'inc/addon-admin.php' );
require_once get_theme_file_path( 'inc/pricing-admin.php' );
require_once get_theme_file_path( 'inc/program-cpt.php' );
require_once get_theme_file_path( 'inc/content-cpts.php' );
require_once get_theme_file_path( 'inc/cookie-banner.php' );

function sm_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus( array(
		'primary' => __( 'Huvudnavigering', 'seniormassan' ),
	) );
}
add_action( 'after_setup_theme', 'sm_theme_setup' );

function sm_enqueue_assets() {
	wp_enqueue_style(
		'sm-google-fonts',
		'https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800&family=Sofia+Sans:wght@400;500;600;700;800;900&family=Caveat:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'sm-main',
		get_theme_file_uri( 'assets/css/main.css' ),
		array( 'sm-google-fonts' ),
		SM_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'sm_enqueue_assets' );

/**
 * Sätt data-palette på <html> så designtokens slår igenom.
 */
function sm_html_palette( $output ) {
	return $output . ' data-palette="havsbla-korall" lang="sv"';
}
add_filter( 'language_attributes', function ( $output ) {
	$palette = function_exists( 'sm_palette' ) ? sm_palette() : 'havsbla-korall';
	return 'data-palette="' . esc_attr( $palette ) . '" lang="sv"';
} );

/**
 * Hjälpare – path till bild i temat.
 */
function sm_image( $filename ) {
	return get_theme_file_uri( 'assets/images/' . $filename );
}
