<?php
/**
 * Seniormässan theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'SENIORMASSAN_VERSION', '0.1.0' );
define( 'SENIORMASSAN_THEME_DIR', get_template_directory() );
define( 'SENIORMASSAN_THEME_URI', get_template_directory_uri() );

require_once SENIORMASSAN_THEME_DIR . '/inc/setup.php';
require_once SENIORMASSAN_THEME_DIR . '/inc/enqueue.php';
require_once SENIORMASSAN_THEME_DIR . '/inc/menus.php';
