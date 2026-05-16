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

		<?php
		$current   = trim( $_SERVER['REQUEST_URI'] ?? '/', '/' );
		$nav_items = array(
			''               => 'För besökare',
			'program'        => 'Program',
			'hitta-hit'      => 'Hitta hit',
			'kontakt'        => 'Kontakt',
			'for-utstallare' => 'För utställare',
		);
		?>

		<nav class="sm-desktop-nav" style="display: flex; gap: 4px; margin-left: auto; flex-wrap: wrap;">
			<?php
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
					'<a href="%s" style="%s"%s>%s</a>',
					esc_url( $href ),
					esc_attr( $style ),
					$is_active ? ' aria-current="page"' : '',
					esc_html( $label )
				);
			}
			?>
		</nav>

		<a href="<?php echo esc_url( home_url( '/anmalan/' ) ); ?>" class="sm-btn sm-btn--accent sm-btn--small sm-header-cta">
			Boka monter →
		</a>

		<button type="button" class="sm-mobile-menu-toggle" aria-label="Öppna meny" aria-expanded="false" aria-controls="sm-mobile-drawer">
			<span class="sm-burger-icon" aria-hidden="true">
				<span></span><span></span><span></span>
			</span>
		</button>
	</div>
</header>

<div class="sm-mobile-overlay" hidden></div>

<aside id="sm-mobile-drawer" class="sm-mobile-drawer" aria-hidden="true" aria-label="Huvudmeny">
	<div class="sm-mobile-drawer-header">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="text-decoration: none; display: inline-flex; align-items: center; gap: 10px;">
			<img src="<?php echo esc_url( sm_image( 'senior-logo-2026-transparent.png' ) ); ?>" alt="Senior" style="height: 36px; width: auto;">
		</a>
		<button type="button" class="sm-mobile-menu-close" aria-label="Stäng meny">×</button>
	</div>
	<nav class="sm-mobile-drawer-nav" aria-label="Mobilmeny">
		<?php
		foreach ( $nav_items as $slug => $label ) {
			$is_active = ( $slug === $current );
			$href      = $slug === '' ? home_url( '/' ) : home_url( '/' . $slug . '/' );
			printf(
				'<a href="%s"%s class="%s">%s</a>',
				esc_url( $href ),
				$is_active ? ' aria-current="page"' : '',
				$is_active ? 'is-active' : '',
				esc_html( $label )
			);
		}
		?>
	</nav>
	<div class="sm-mobile-drawer-footer">
		<a href="<?php echo esc_url( home_url( '/anmalan/' ) ); ?>" class="sm-btn sm-btn--accent" style="width: 100%; text-align: center;">Boka monter →</a>
	</div>
</aside>

<script>
(function () {
	var toggle  = document.querySelector('.sm-mobile-menu-toggle');
	var drawer  = document.getElementById('sm-mobile-drawer');
	var overlay = document.querySelector('.sm-mobile-overlay');
	var close   = document.querySelector('.sm-mobile-menu-close');
	if (!toggle || !drawer || !overlay) return;

	function openMenu() {
		drawer.classList.add('is-active');
		overlay.hidden = false;
		// Trigga reflow innan vi sätter is-active så övergången körs
		void overlay.offsetWidth;
		overlay.classList.add('is-active');
		toggle.setAttribute('aria-expanded', 'true');
		drawer.setAttribute('aria-hidden', 'false');
		document.body.classList.add('sm-menu-open');
	}
	function closeMenu() {
		drawer.classList.remove('is-active');
		overlay.classList.remove('is-active');
		toggle.setAttribute('aria-expanded', 'false');
		drawer.setAttribute('aria-hidden', 'true');
		document.body.classList.remove('sm-menu-open');
		setTimeout(function () {
			if (!overlay.classList.contains('is-active')) overlay.hidden = true;
		}, 300);
		toggle.focus();
	}
	toggle.addEventListener('click', function () {
		(toggle.getAttribute('aria-expanded') === 'true') ? closeMenu() : openMenu();
	});
	overlay.addEventListener('click', closeMenu);
	if (close) close.addEventListener('click', closeMenu);
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && drawer.classList.contains('is-active')) closeMenu();
	});
	// Stäng menyn när man klickar på en länk i drawer:n
	drawer.querySelectorAll('a').forEach(function (a) {
		a.addEventListener('click', closeMenu);
	});
})();
</script>

<main id="main" class="sm-site-main">
