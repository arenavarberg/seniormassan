<?php
/**
 * Förstasida — För besökare.
 *
 * Innehåll är just nu hårdkodat enligt designhandoffen. Flyttas till
 * redigerbara fält (ACF eller block-mönster) i senare iteration.
 */

get_header();

$hero_photos = array( 'hero-1.jpg', 'hero-2.jpg', 'hero-3.jpg', 'hero-4.jpg', 'hero-5.jpg', 'hero-6.jpg', 'hero-7.jpg' );
?>

<section class="sm-hero" style="position: relative; min-height: 620px; display: flex; align-items: center; overflow: hidden; background: #1a1a1a;">
	<?php foreach ( $hero_photos as $i => $photo ) : ?>
		<div class="sm-hero-slide" data-index="<?php echo (int) $i; ?>" style="position: absolute; inset: 0; background-image: url('<?php echo esc_url( sm_image( $photo ) ); ?>'); background-size: cover; background-position: center; opacity: <?php echo $i === 0 ? '1' : '0'; ?>; transition: opacity 1.2s ease-in-out;"></div>
	<?php endforeach; ?>

	<div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0.82) 100%);"></div>

	<div class="sm-container" style="position: relative; z-index: 2; color: #fff; padding: 96px 32px;">
		<h1 style="font-size: var(--sm-fs-xxl); font-weight: 800; max-width: 900px; text-shadow: 0 2px 16px rgba(0,0,0,0.4);">
			Seniormässan <span style="color: var(--sm-gold);">24 feb 2027</span>
		</h1>
		<p style="font-size: var(--sm-fs-lg); max-width: 720px; margin-top: 24px; text-shadow: 0 2px 12px rgba(0,0,0,0.4);">
			En dag fylld av möten, upplevelser och inspiration — närmare 90 utställare, scenprogram, caféer och restaurang.
		</p>
		<div style="margin-top: 36px; display: flex; gap: 14px; flex-wrap: wrap;">
			<a href="#hojdpunkter" class="sm-btn sm-btn--accent">Se höjdpunkterna →</a>
			<a href="<?php echo esc_url( home_url( '/program/' ) ); ?>" class="sm-btn sm-btn--ghost" style="color: #fff; border-color: #fff;">Program</a>
		</div>
	</div>

	<svg style="position: absolute; bottom: -1px; left: 0; right: 0; width: 100%; height: 70px; display: block;" viewBox="0 0 1440 120" preserveAspectRatio="none">
		<path d="M0,60 C240,120 480,0 720,60 C960,120 1200,0 1440,60 L1440,121 L0,121 Z" fill="var(--sm-bg)"></path>
	</svg>
</section>

<section style="background: var(--sm-bg); padding: 0 32px 64px;">
	<div class="sm-container" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 32px;">
		<?php
		$stats = array(
			array( '~90', 'utställare' ),
			array( '~2000', 'besökare' ),
			array( '2', 'caféer + bar' ),
			array( '10+', 'år tradition' ),
		);
		foreach ( $stats as $stat ) :
			?>
			<div style="text-align: center;">
				<div style="font-family: var(--sm-font-display); font-size: 56px; font-weight: 800; color: var(--sm-primary); line-height: 1;">
					<?php echo esc_html( $stat[0] ); ?>
				</div>
				<div style="margin-top: 12px; font-size: 18px; color: var(--sm-ink-soft);">
					<?php echo esc_html( $stat[1] ); ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<section style="background: var(--sm-surface); padding: 96px 0;">
	<div class="sm-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center;">
		<div>
			<div class="sm-eyebrow">Praktiskt</div>
			<h2 style="font-size: var(--sm-fs-xl); margin-bottom: 24px;">Hela dagen i ett svep.</h2>
			<div style="font-size: var(--sm-fs-md); line-height: 1.7;">
				<p><strong>Onsdag 24 februari 2027</strong><br>
				10.00 – 17.00 · Dörrarna öppnar 09.30</p>
				<p><strong>Entré</strong> 100 kr i förköp · 120 kr vid dörren (swish / kort)<br>
				Fri parkering · Buss linje 1/2/4</p>
			</div>
		</div>
		<div style="aspect-ratio: 4/5; background-image: url('<?php echo esc_url( sm_image( 'hero-3.jpg' ) ); ?>'); background-size: cover; background-position: center; border-radius: var(--sm-radius-lg);"></div>
	</div>
</section>

<section style="background: var(--sm-primary); color: var(--sm-primary-ink); padding: 96px 0;">
	<div class="sm-container" style="text-align: center; max-width: 760px;">
		<h2 style="font-size: var(--sm-fs-xl); margin-bottom: 16px;">Ta med en vän — det blir roligare så.</h2>
		<p style="font-size: var(--sm-fs-lg); opacity: 0.92;">Dela gärna inbjudan. Köp biljett i förköp för 100 kr, eller 120 kr vid dörren.</p>
	</div>
</section>

<script>
(function () {
	var slides = document.querySelectorAll('.sm-hero-slide');
	if (slides.length < 2) return;
	if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
	var i = 0;
	setInterval(function () {
		slides[i].style.opacity = '0';
		i = (i + 1) % slides.length;
		slides[i].style.opacity = '1';
	}, 5500);
})();
</script>

<?php get_footer(); ?>
