<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'after_setup_theme', function () {
    load_theme_textdomain( 'seniormassan', SENIORMASSAN_THEME_DIR . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'editor-styles' );

    register_nav_menus( [
        'primary' => __( 'Huvudmeny', 'seniormassan' ),
        'footer'  => __( 'Sidfot', 'seniormassan' ),
    ] );
} );

add_filter( 'document_title_separator', fn() => '·' );
