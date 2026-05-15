<?php
/**
 * Site header — matchar site-shell.jsx SiteHeader.
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

<header style="position: sticky; top: 0; z-index: 30; background: rgba(250, 247, 242, 0.92); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border-bottom: 1px solid var(--sm-line-soft);">
	<div class="sm-container" style="display: flex; align-items: center; gap: 32px; padding: 16px 32px;">

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="background: none; border: none; padding: 0; display: flex; flex-direction: column; align-items: center; gap: 2px; text-decoration: none;">
			<img src="<?php echo esc_url( sm_image( 'senior-logo-2026-transparent.png' ) ); ?>" alt="Senior" style="height: 44px; width: auto; display: block;">
			<div style="display: flex; align-items: center; justify-content: center; gap: 10px; font-family: var(--sm-font-body); font-size: 11px; font-weight: 500; color: var(--sm-primary); margin-top: 2px;">
				<span style="width: 22px; height: 1px; background: currentColor; opacity: 0.55;"></span>
				<span style="letter-spacing: 0.65em; padding-left: 0.65em;">MÄSSAN</span>
				<span style="width: 22px; height: 1px; background: currentColor; opacity: 0.55;"></span>
			</div>
		</a>

		<nav style="display: flex; gap: 4px; margin-left: auto; flex-wrap: wrap;">
			<?php
			$current   = trim( $_SERVER['REQUEST_URI'] ?? '/', '/' );
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
				$style     = 'padding: 10px 16px; border-radius: 8px; text-decoration: none; font-size: 16px;';
				if ( $is_active ) {
					$style .= ' background: var(--sm-primary-soft); color: var(--sm-primary); font-weight: 700;';
				} else {
					$style .= ' background: transparent; color: var(--sm-ink); font-weight: 500;';
				}
				printf(
					'<a href="%s" style="%s">%s</a>',
					esc_url( $href ),
					esc_attr( $style ),
					esc_html( $label )
				);
			}
			?>
		</nav>

		<a href="<?php echo esc_url( home_url( '/anmalan/' ) ); ?>" class="sm-btn sm-btn--accent sm-btn--small">
			Boka monter →
		</a>
	</div>
</header>

<main id="main" class="sm-site-main">
