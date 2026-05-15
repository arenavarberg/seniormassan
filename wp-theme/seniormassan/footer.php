<?php
/**
 * Site footer — matchar site-shell.jsx SiteFooter.
 */
?>
</main>

<footer style="background: var(--sm-ink); color: rgba(255,255,255,0.82); margin-top: 0;">
	<div class="sm-container" style="padding: 64px 32px 32px; display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 48px;">
		<div>
			<div style="display: inline-flex; flex-direction: column; align-items: center; gap: 4px;">
				<img src="<?php echo esc_url( sm_image( 'senior-logo-2026-transparent.png' ) ); ?>" alt="Senior" style="height: 56px; width: auto; filter: brightness(0) invert(1); opacity: 0.95;">
				<div style="display: flex; align-items: center; justify-content: center; gap: 10px; font-family: var(--sm-font-body); font-size: 12px; font-weight: 500; color: #fff; opacity: 0.9;">
					<span style="width: 28px; height: 1px; background: currentColor; opacity: 0.55;"></span>
					<span style="letter-spacing: 0.65em; padding-left: 0.65em;">MÄSSAN</span>
					<span style="width: 28px; height: 1px; background: currentColor; opacity: 0.55;"></span>
				</div>
			</div>
			<div style="font-size: 13px; letter-spacing: 0.2em; text-transform: uppercase; margin-top: 16px; opacity: 0.6;">Sedan 2013</div>
			<p style="margin-top: 24px; opacity: 0.75; max-width: 360px;">
				En mötesplats för seniorer i Halland — arrangerad av Arena Varberg.
			</p>
			<div style="margin-top: 28px; display: flex; align-items: center; gap: 14px;">
				<div style="font-size: 11px; letter-spacing: 0.22em; text-transform: uppercase; opacity: 0.55;">Arrangör</div>
				<img src="<?php echo esc_url( sm_image( 'arena-varberg-logo-white.png' ) ); ?>" alt="Arena Varberg" style="height: 56px; width: auto;">
			</div>
		</div>

		<div>
			<div style="font-weight: 700; color: #fff; margin-bottom: 14px;">Mässan</div>
			<?php
			$mass_links = array(
				'program'   => 'Program',
				'hitta-hit' => 'Hitta hit',
				'hitta-hit' => 'Parkering',
				'hitta-hit' => 'Tillgänglighet',
			);
			foreach ( array(
				array( 'program',   'Program' ),
				array( 'hitta-hit', 'Hitta hit' ),
				array( 'hitta-hit', 'Parkering' ),
				array( 'hitta-hit', 'Tillgänglighet' ),
			) as $link ) :
				?>
				<div style="margin-bottom: 8px; opacity: 0.8; font-size: 16px;">
					<a href="<?php echo esc_url( home_url( '/' . $link[0] . '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php echo esc_html( $link[1] ); ?></a>
				</div>
			<?php endforeach; ?>
		</div>

		<div>
			<div style="font-weight: 700; color: #fff; margin-bottom: 14px;">Utställare</div>
			<?php foreach ( array(
				array( 'for-utstallare', 'Bli utställare' ),
				array( 'for-utstallare', 'Priser & paket' ),
				array( 'for-utstallare', 'Villkor' ),
			) as $link ) : ?>
				<div style="margin-bottom: 8px; opacity: 0.8; font-size: 16px;">
					<a href="<?php echo esc_url( home_url( '/' . $link[0] . '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php echo esc_html( $link[1] ); ?></a>
				</div>
			<?php endforeach; ?>
		</div>

		<div>
			<div style="font-weight: 700; color: #fff; margin-bottom: 14px;">Kontakt</div>
			<div style="opacity: 0.8; font-size: 16px; line-height: 1.8;">
				Arena Varberg<br>
				<a href="tel:0340690200" style="color: inherit; text-decoration: none;">0340-69 02 00</a><br>
				<a href="mailto:info@arenavarberg.se" style="color: inherit; text-decoration: none;">info@arenavarberg.se</a>
			</div>
		</div>
	</div>
	<div style="border-top: 1px solid rgba(255,255,255,0.12); padding: 20px 32px; font-size: 14px; opacity: 0.55; text-align: center;">
		© <?php echo esc_html( date( 'Y' ) ); ?> Arena Varberg AB
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
