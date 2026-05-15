<?php
/**
 * Site header + öppnande av <body>.
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="sm-site-header" style="background: var(--sm-surface); border-bottom: 1px solid var(--sm-line); position: sticky; top: 0; z-index: 50;">
	<div class="sm-container" style="display: flex; align-items: center; justify-content: space-between; height: 80px; gap: 24px;">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="display: flex; align-items: center; gap: 14px; text-decoration: none; color: var(--sm-ink);">
			<img src="<?php echo esc_url( sm_image( 'senior-logo-2026-transparent.png' ) ); ?>" alt="Seniormässan" style="height: 48px; width: auto;">
			<span style="font-family: var(--sm-font-display); font-weight: 800; letter-spacing: 0.18em; font-size: 18px;">MÄSSAN</span>
		</a>

		<nav style="display: flex; align-items: center; gap: 4px; flex-wrap: wrap;">
			<?php
			$current = trim( $_SERVER['REQUEST_URI'] ?? '/', '/' );
			$nav_items = array(
				''               => 'För besökare',
				'program'        => 'Program',
				'hitta-hit'      => 'Hitta hit',
				'kontakt'        => 'Kontakt',
				'for-utstallare' => 'För utställare',
			);
			foreach ( $nav_items as $slug => $label ) {
				$is_active = ( $slug === $current );
				$href      = $slug === '' ? home_url( '/' ) : home_url( '/' . $slug . '/' );
				$style     = 'padding: 10px 14px; border-radius: 999px; text-decoration: none; font-weight: 600; font-size: 16px; color: var(--sm-ink); transition: background 0.15s;';
				if ( $is_active ) {
					$style .= ' background: var(--sm-primary); color: var(--sm-primary-ink);';
				}
				printf(
					'<a href="%s" style="%s">%s</a>',
					esc_url( $href ),
					esc_attr( $style ),
					esc_html( $label )
				);
			}
			?>
			<a href="<?php echo esc_url( home_url( '/anmalan/' ) ); ?>" class="sm-btn sm-btn--accent sm-btn--small" style="margin-left: 12px;">
				Boka monter →
			</a>
		</nav>
	</div>
</header>

<main id="main" class="sm-site-main">
