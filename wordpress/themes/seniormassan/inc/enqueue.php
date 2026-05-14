<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'seniormassan-fonts',
        'https://fonts.googleapis.com/css2?family=Mulish:wght@400;500;600;700;800&family=Sofia+Sans:wght@600;700;800&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'seniormassan-main',
        SENIORMASSAN_THEME_URI . '/assets/css/main.css',
        [ 'seniormassan-fonts' ],
        SENIORMASSAN_VERSION
    );

    wp_enqueue_script(
        'seniormassan-main',
        SENIORMASSAN_THEME_URI . '/assets/js/main.js',
        [],
        SENIORMASSAN_VERSION,
        true
    );
} );

add_action( 'wp_head', function () {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 1 );
