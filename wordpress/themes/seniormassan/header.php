<?php
/**
 * Header template.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="sr-only" href="#sm-main">Hoppa till innehåll</a>

<header class="sm-header">
    <div class="sm-container sm-header__inner">
        <a class="sm-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Seniormässan – startsidan">
            <img
                class="sm-header__logo"
                src="<?php echo esc_url( SENIORMASSAN_THEME_URI . '/assets/images/senior-logo-2026-transparent.png' ); ?>"
                alt="Senior"
                width="160" height="44">
            <span class="sm-header__wordmark" aria-hidden="true">
                <span class="sm-header__rule"></span>
                <span class="sm-header__wordmark-text">MÄSSAN</span>
                <span class="sm-header__rule"></span>
            </span>
        </a>

        <nav class="sm-nav" aria-label="Huvudnavigering">
            <?php seniormassan_render_primary_nav(); ?>
        </nav>

        <a class="sm-btn sm-btn--accent sm-btn--small sm-header__cta" href="<?php echo esc_url( home_url( '/anmalan/' ) ); ?>">
            Boka monter →
        </a>
    </div>
</header>

<main id="sm-main" class="sm-main">
