<?php
/**
 * Site footer.
 */
?>
</main>

<footer class="sm-site-footer" style="background: #1a1a1a; color: #f5f5f5; padding: 64px 0 32px;">
	<div class="sm-container" style="display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 48px; align-items: start;">
		<div>
			<div style="display: flex; align-items: center; gap: 14px; margin-bottom: 20px;">
				<img src="<?php echo esc_url( sm_image( 'senior-logo-2026-transparent.png' ) ); ?>" alt="Seniormässan" style="height: 48px; width: auto; filter: brightness(0) invert(1);">
				<span style="font-family: var(--sm-font-display); font-weight: 800; letter-spacing: 0.18em; font-size: 18px;">MÄSSAN</span>
			</div>
			<p style="opacity: 0.7; max-width: 360px;">Mötesplatsen för dig som vill leva hela livet. Onsdag 24 februari 2027 på Arena Varberg.</p>
			<div style="margin-top: 24px; font-size: 14px; opacity: 0.6;">
				<div style="text-transform: uppercase; letter-spacing: 0.14em; margin-bottom: 8px;">Arrangör</div>
				<img src="<?php echo esc_url( sm_image( 'arena-varberg-logo-white.png' ) ); ?>" alt="Arena Varberg" style="height: 38px; width: auto;">
			</div>
		</div>

		<div>
			<div style="text-transform: uppercase; letter-spacing: 0.14em; font-size: 13px; margin-bottom: 14px; opacity: 0.6;">Praktiskt</div>
			<div style="line-height: 1.7;">
				Engelbrektsgatan 6<br>
				432 41 Varberg<br>
				<br>
				10.00 – 17.00<br>
				Dörrarna öppnar 09.30
			</div>
		</div>

		<div>
			<div style="text-transform: uppercase; letter-spacing: 0.14em; font-size: 13px; margin-bottom: 14px; opacity: 0.6;">Kontakt</div>
			<div style="line-height: 1.7;">
				<a href="mailto:info@arenavarberg.se" style="color: inherit;">info@arenavarberg.se</a><br>
				<a href="tel:0340690200" style="color: inherit;">0340-690200</a>
			</div>
		</div>
	</div>

	<div class="sm-container" style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 48px; padding-top: 24px; display: flex; justify-content: space-between; opacity: 0.5; font-size: 14px;">
		<div>© <?php echo esc_html( date( 'Y' ) ); ?> Arena Varberg AB</div>
		<div><a href="/integritet/" style="color: inherit;">Integritetspolicy</a></div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
