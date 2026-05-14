<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Standardnavigering om ingen meny tilldelats i WP-admin.
 */
function seniormassan_default_nav_items() : array {
    return [
        [ 'label' => 'För besökare',   'url' => home_url( '/' ),                'slug' => '' ],
        [ 'label' => 'Program',        'url' => home_url( '/program/' ),        'slug' => 'program' ],
        [ 'label' => 'Hitta hit',      'url' => home_url( '/hitta-hit/' ),      'slug' => 'hitta-hit' ],
        [ 'label' => 'Kontakt',        'url' => home_url( '/kontakt/' ),        'slug' => 'kontakt' ],
        [ 'label' => 'För utställare', 'url' => home_url( '/for-utstallare/' ), 'slug' => 'for-utstallare' ],
    ];
}

function seniormassan_render_primary_nav() : void {
    if ( has_nav_menu( 'primary' ) ) {
        wp_nav_menu( [
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'sm-nav__list',
            'depth'          => 1,
        ] );
        return;
    }

    $current = trim( $_SERVER['REQUEST_URI'] ?? '/', '/' );
    echo '<ul class="sm-nav__list">';
    foreach ( seniormassan_default_nav_items() as $item ) {
        $is_active = ( $item['slug'] === '' && $current === '' ) || ( $item['slug'] !== '' && str_starts_with( $current, $item['slug'] ) );
        $class     = $is_active ? 'sm-nav__item sm-nav__item--active' : 'sm-nav__item';
        printf(
            '<li class="%s"><a href="%s">%s</a></li>',
            esc_attr( $class ),
            esc_url( $item['url'] ),
            esc_html( $item['label'] )
        );
    }
    echo '</ul>';
}
