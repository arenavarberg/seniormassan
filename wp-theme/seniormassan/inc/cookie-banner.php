<?php
/**
 * Cookie-banner och gating av tredjepartsinbäddningar.
 *
 * Funktionalitet:
 *   - Banner längst ner på sidan tills besökaren klickar Acceptera/Avvisa.
 *   - Valet sparas i cookie 'sm_cookie_consent' i 365 dagar.
 *   - Element med klassen .sm-embed-gated renderas som platshållare tills
 *     användaren godkänner — då byter JS ut platshållaren mot en riktig iframe.
 *   - Vimeo-videor körs med dnt=1 (Do Not Track-läge) och behöver inget consent.
 *
 * Banner-texterna är redigerbara via Utseende → Anpassa → Cookie-banner.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =====================================================================
 * Hjälpare
 * ===================================================================== */

function sm_privacy_url() {
	$override = get_theme_mod( 'sm_cookie_link_url', '' );
	if ( $override ) {
		return $override;
	}
	$wp_url = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';
	return $wp_url ?: home_url( '/integritet/' );
}

/* =====================================================================
 * Customizer
 * ===================================================================== */

function sm_cookie_customizer( $wp_customize ) {
	$wp_customize->add_section( 'sm_cookie', array(
		'title'       => 'Cookie-banner',
		'priority'    => 70,
		'description' => 'Texterna i bannern som visas för förstagångsbesökare. Bannern gömmer även Google Maps-kartan på "Hitta hit" tills besökaren godkänner.',
	) );

	$add = function ( $id, $label, $default, $type = 'text' ) use ( $wp_customize ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $default,
			'sanitize_callback' => $type === 'textarea' ? 'sanitize_textarea_field' : 'sanitize_text_field',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $label,
			'section' => 'sm_cookie',
			'type'    => $type,
		) );
	};

	$add( 'sm_cookie_text',
		'Banner-text',
		'Vi använder nödvändiga cookies för att sajten ska fungera. För att visa kartan på "Hitta hit" behöver vi även sätta cookies från Google. Inga statistik- eller marknadsföringscookies används.',
		'textarea'
	);
	$add( 'sm_cookie_accept',  'Knapp: Acceptera', 'Acceptera' );
	$add( 'sm_cookie_decline', 'Knapp: Avvisa',    'Avvisa' );
	$add( 'sm_cookie_link_label', 'Länktext: "Läs mer"', 'Läs mer' );

	$wp_customize->add_setting( 'sm_cookie_link_url', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'sm_cookie_link_url', array(
		'label'       => '"Läs mer" — länk',
		'description' => 'Lämna tomt för att använda WordPress integritetspolicy-sida (Inställningar → Privacy). Annars en absolut URL.',
		'section'     => 'sm_cookie',
		'type'        => 'url',
	) );

	$add( 'sm_cookie_map_fallback',
		'Karta — text när kartan inte laddats',
		'Kartan visas via Google Maps som sätter tredjepartscookies. Acceptera cookies för att visa kartan här, eller öppna direkt hos Google.',
		'textarea'
	);
	$add( 'sm_cookie_map_button', 'Karta — knapp', 'Acceptera och visa karta' );
	$add( 'sm_cookie_map_external', 'Karta — länk till Google Maps', 'Öppna i Google Maps' );
}
add_action( 'customize_register', 'sm_cookie_customizer' );

/* =====================================================================
 * Banner HTML + JS i footer
 * ===================================================================== */

function sm_cookie_banner_render() {
	// Om besökaren redan har valt: bannern är gömd från start (men markup finns i DOM
	// för konsistens; CSS styr synligheten via :not(.is-visible)).
	$accept   = sm_text( 'sm_cookie_accept',  'Acceptera' );
	$decline  = sm_text( 'sm_cookie_decline', 'Avvisa' );
	$body     = sm_text( 'sm_cookie_text',
		'Vi använder nödvändiga cookies för att sajten ska fungera. För att visa kartan på "Hitta hit" behöver vi även sätta cookies från Google. Inga statistik- eller marknadsföringscookies används.' );
	$link_lbl = sm_text( 'sm_cookie_link_label', 'Läs mer' );
	$link_url = sm_privacy_url();
	?>
	<style>
		#sm-cookie-banner {
			position: fixed; left: 16px; right: 16px; bottom: 16px;
			background: var(--sm-ink, #1a2330); color: #fff;
			border-radius: var(--sm-radius-lg, 14px);
			box-shadow: 0 20px 60px rgba(0,0,0,0.35);
			padding: 20px 24px;
			z-index: 9999;
			display: none;
			max-width: 880px; margin: 0 auto;
			max-height: 80vh; overflow-y: auto;
			font-family: var(--sm-font-body, system-ui, sans-serif);
		}
		#sm-cookie-banner.is-visible { display: block; }
		.sm-cookie-grid { display: grid; grid-template-columns: 1fr auto; gap: 24px; align-items: center; }
		.sm-cookie-text { font-size: 15px; line-height: 1.5; margin: 0; opacity: 0.9; }
		.sm-cookie-text a { color: var(--sm-gold, #f3c057); text-decoration: underline; }
		.sm-cookie-buttons { display: flex; gap: 10px; flex-shrink: 0; }
		.sm-cookie-btn {
			padding: 10px 18px; border-radius: 8px; font: inherit; font-weight: 600;
			cursor: pointer; border: 1px solid transparent; white-space: nowrap;
		}
		.sm-cookie-btn--accept { background: var(--sm-accent, #c6371d); color: #fff; }
		.sm-cookie-btn--accept:hover { filter: brightness(1.08); }
		.sm-cookie-btn--decline { background: transparent; color: #fff; border-color: rgba(255,255,255,0.35); }
		.sm-cookie-btn--decline:hover { background: rgba(255,255,255,0.08); }
		@media (max-width: 640px) {
			.sm-cookie-grid { grid-template-columns: 1fr; }
			.sm-cookie-buttons { justify-content: flex-end; }
		}

		/* Gated embed placeholder */
		.sm-embed-gated {
			width: 100%; height: 100%;
			display: flex; flex-direction: column; align-items: center; justify-content: center;
			gap: 14px; padding: 32px; text-align: center;
			background: var(--sm-surface, #f5efe6); color: var(--sm-ink, #1a2330);
			border-radius: var(--sm-radius-lg, 14px);
		}
		.sm-embed-gated p { margin: 0; max-width: 480px; font-size: 16px; line-height: 1.55; }
	</style>

	<div id="sm-cookie-banner" role="dialog" aria-label="Information om cookies" aria-live="polite">
		<div class="sm-cookie-grid">
			<p class="sm-cookie-text">
				<?php echo esc_html( $body ); ?>
				<?php if ( $link_url ) : ?>
					<a href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $link_lbl ); ?></a>
				<?php endif; ?>
			</p>
			<div class="sm-cookie-buttons">
				<button type="button" class="sm-cookie-btn sm-cookie-btn--decline" data-sm-cookie="decline"><?php echo esc_html( $decline ); ?></button>
				<button type="button" class="sm-cookie-btn sm-cookie-btn--accept"  data-sm-cookie="accept"><?php echo esc_html( $accept ); ?></button>
			</div>
		</div>
	</div>

	<script>
	(function () {
		function getCookie(name) {
			var parts = document.cookie.split('; ');
			for (var i = 0; i < parts.length; i++) {
				if (parts[i].indexOf(name + '=') === 0) return parts[i].substring(name.length + 1);
			}
			return '';
		}
		function setCookie(name, value, days) {
			var d = new Date();
			d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
			document.cookie = name + '=' + value + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
		}

		var banner = document.getElementById('sm-cookie-banner');
		var consent = getCookie('sm_cookie_consent');

		function loadGatedEmbeds() {
			document.querySelectorAll('.sm-embed-gated[data-src]').forEach(function (el) {
				var iframe = document.createElement('iframe');
				iframe.src = el.dataset.src;
				if (el.dataset.title)   iframe.title = el.dataset.title;
				if (el.dataset.allow)   iframe.allow = el.dataset.allow;
				iframe.loading = 'lazy';
				iframe.referrerPolicy = 'no-referrer-when-downgrade';
				iframe.style.cssText = 'width:100%;height:100%;border:0;display:block;';
				el.replaceWith(iframe);
			});
		}

		function accept() {
			setCookie('sm_cookie_consent', 'accepted', 365);
			if (banner) banner.classList.remove('is-visible');
			loadGatedEmbeds();
		}
		function decline() {
			setCookie('sm_cookie_consent', 'declined', 365);
			if (banner) banner.classList.remove('is-visible');
		}

		if (!consent && banner) {
			banner.classList.add('is-visible');
		}
		if (consent === 'accepted') {
			loadGatedEmbeds();
		}

		document.querySelectorAll('[data-sm-cookie="accept"]').forEach(function (b) { b.addEventListener('click', accept); });
		document.querySelectorAll('[data-sm-cookie="decline"]').forEach(function (b) { b.addEventListener('click', decline); });
		// Knappar inuti embed-placeholders ("Visa karta") → samma effekt som global accept.
		document.querySelectorAll('[data-sm-load-embed]').forEach(function (b) { b.addEventListener('click', accept); });

		// Exponera ett enkelt API om annan kod vill kolla läget.
		window.smCookieConsent = function () { return getCookie('sm_cookie_consent'); };
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'sm_cookie_banner_render' );
