<?php
/**
 * Förstasida — För besökare.
 *
 * Sektioner enligt design_handoff_seniormassan/site-pages.jsx → VisitorPage:
 *   1. Hero med fotokarusell
 *   2. Statistikband (4 siffror)
 *   3. Vimeo-video "Så var det 2025"
 *   4. Praktiskt info (datum, tider, entré, parkering, buss)
 *   5. Områden — "Sex världar att upptäcka"
 *   6. CTA-band "Ta med en vän"
 *   7. Höjdpunkter 2027 (3 kort)
 *   8. Utställarlista (kommer i nästa iteration)
 */

get_header();

$hero_photos = array( 'hero-5.jpg', 'hero-1.jpg', 'hero-7.jpg', 'hero-2.jpg', 'hero-6.jpg', 'hero-3.jpg', 'hero-4.jpg' );

$stats = array(
	array( '~90', 'utställare' ),
	array( '~2000', 'besökare' ),
	array( '2', 'caféer + bar' ),
	array( '10+ år', 'tradition' ),
);

$zones = array(
	array( 'n' => '01', 't' => 'Resor & Upplevelser',  'd' => 'Från bussresor i Europa till kryssningar och kulturresor.' ),
	array( 'n' => '02', 't' => 'Hälsa & Välmående',    'd' => 'Hörsel, syn, tandvård, fysioterapi och mental hälsa.' ),
	array( 'n' => '03', 't' => 'Boende',                'd' => 'Seniorboenden, hemtjänst, tillgänglighetsanpassning, flyttfirmor.' ),
	array( 'n' => '04', 't' => 'Ekonomi & Juridik',    'd' => 'Pension, arv och testamente, bankfrågor och försäkring.' ),
	array( 'n' => '05', 't' => 'Teknik',                'd' => 'Smartphones, larm, hörselslingor — personlig hjälp i lugn takt.' ),
	array( 'n' => '06', 't' => 'Kultur & Fritid',       'd' => 'Studieförbund, körer, bokklubbar, golf, boule, dans.' ),
);

$highlights = array(
	array( 'img' => 'scenprogram.jpg',  'k' => 'Scenprogram', 't' => 'Författarsamtal & musik',  'd' => 'Scenprogram som berör. Från livemusik till föreläsningar om det goda livet efter 70.' ),
	array( 'img' => 'boule.jpg',        'k' => 'Provspring',  't' => 'Testa nya aktiviteter',     'd' => 'Prova curling, boule, linedance, innebandy och mycket mer — helt gratis.' ),
	array( 'img' => 'resecentrum.jpg',  'k' => 'Resecentrum', 't' => 'Drömresor & bussutflykter', 'd' => 'Plocka hem vårens bästa reseidéer. Mässpriser hos 20+ researrangörer.' ),
);
?>

<section class="sm-hero" style="position: relative; background: var(--sm-ink); overflow: hidden;">
	<?php foreach ( $hero_photos as $i => $photo ) : ?>
		<div class="sm-hero-slide" data-index="<?php echo (int) $i; ?>" aria-hidden="<?php echo $i === 0 ? 'false' : 'true'; ?>" style="position: absolute; inset: 0; background-image: url('<?php echo esc_url( sm_image( $photo ) ); ?>'); background-size: cover; background-position: center 35%; opacity: <?php echo $i === 0 ? '1' : '0'; ?>; transition: opacity 1.2s ease-in-out;"></div>
	<?php endforeach; ?>

	<div style="position: relative; background: linear-gradient(180deg, rgba(15,23,30,0.15) 0%, rgba(15,23,30,0.55) 60%, rgba(15,23,30,0.82) 100%); min-height: 620px; display: flex; align-items: flex-end;">
		<div class="sm-container" style="padding: 80px 32px 88px; color: #fff; position: relative; z-index: 2;">
			<h1 style="font-size: var(--sm-fs-xxl); color: #fff; max-width: 920px; text-shadow: 0 2px 20px rgba(0,0,0,0.45);">
				<?php echo esc_html( sm_hero_h1_main() ); ?> <span style="color: var(--sm-gold);"><?php echo esc_html( sm_hero_h1_accent() ); ?></span>
			</h1>
			<p style="font-size: var(--sm-fs-lg); max-width: 720px; margin-top: 20px; color: rgba(255,255,255,0.95); text-shadow: 0 1px 10px rgba(0,0,0,0.5);">
				<?php echo esc_html( sm_hero_body() ); ?>
			</p>
		</div>
	</div>

	<svg viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true" style="display: block; position: absolute; left: 0; right: 0; bottom: -1px; width: 100%; height: 90px; z-index: 2; pointer-events: none;">
		<path d="M0,60 C240,10 480,110 720,70 C960,30 1200,90 1440,50 L1440,120 L0,120 Z" fill="var(--sm-bg)"></path>
	</svg>
</section>

<?php get_template_part( 'template-parts/wave-divider', null, array( 'from' => 'transparent', 'to' => 'var(--sm-bg)' ) ); ?>

<section style="background: var(--sm-bg);">
	<div class="sm-container" style="display: grid; grid-template-columns: repeat(4, 1fr); padding: 0 32px 64px; gap: 32px;">
		<?php foreach ( $stats as $stat ) : ?>
			<div style="text-align: center;">
				<div style="font-family: var(--sm-font-display); font-size: 56px; font-weight: 800; color: var(--sm-primary); letter-spacing: -0.03em; line-height: 1;"><?php echo esc_html( $stat[0] ); ?></div>
				<div style="font-size: 16px; color: var(--sm-ink-soft); margin-top: 8px; letter-spacing: 0.04em;"><?php echo esc_html( $stat[1] ); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<?php get_template_part( 'template-parts/wave-divider', null, array( 'from' => 'var(--sm-bg)', 'to' => 'var(--sm-surface)', 'flip' => true ) ); ?>

<section style="background: var(--sm-surface);">
	<div class="sm-container" style="padding: 72px 32px;">
		<div style="text-align: center; margin-bottom: 32px;">
			<div class="sm-eyebrow">Så var det 2025</div>
			<h2 style="font-size: var(--sm-fs-xl); max-width: 720px; margin: 0 auto;">En liten smakbit av förra årets mässa.</h2>
		</div>
		<div style="max-width: 960px; margin: 0 auto;">
			<div style="position: relative; padding-top: 56.25%; border-radius: var(--sm-radius-lg); overflow: hidden; box-shadow: var(--sm-shadow-md); background: #000;">
				<iframe src="https://player.vimeo.com/video/1178774214?title=0&byline=0&portrait=0" title="Seniormässan 2025" style="position: absolute; inset: 0; width: 100%; height: 100%; border: 0;" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
			</div>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/wave-divider', null, array( 'from' => 'var(--sm-surface)', 'to' => 'var(--sm-bg)', 'flip' => true, 'height' => 60 ) ); ?>

<section style="background: var(--sm-bg);">
	<div class="sm-container" style="padding: 80px 32px;">
		<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: center;">
			<div>
				<div class="sm-eyebrow">Praktiskt</div>
				<h2 style="font-size: var(--sm-fs-xl);">Hela dagen i ett svep.</h2>
				<p style="font-size: var(--sm-fs-lg); color: var(--sm-ink-soft); margin-top: 16px;">
					En dag som rymmer allt. Kom när dörrarna öppnar — eller bara en stund när det passar.
				</p>
				<div style="margin-top: 32px;">
					<?php
					get_template_part( 'template-parts/info-block', null, array(
						'title' => 'När?',
						'items' => array(
							array( sm_event_date_long(), sm_event_hours() ),
							array( '', sm_event_doors() ),
						),
					) );
					get_template_part( 'template-parts/info-block', null, array(
						'title' => 'Hur?',
						'items' => array(
							array( 'Entré', sm_event_entry() ),
							array( 'Fri parkering', 'över 200 platser på området' ),
							array( 'Buss', 'Linje 1, 2 och 4 till ' . sm_venue_name() ),
						),
					) );
					?>
				</div>
			</div>
			<div style="aspect-ratio: 4/5; border-radius: var(--sm-radius-lg); overflow: hidden; background-image: url('<?php echo esc_url( sm_image( 'hero-1.jpg' ) ); ?>'); background-size: cover; background-position: center;"></div>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/wave-divider', null, array( 'from' => 'var(--sm-bg)', 'to' => 'var(--sm-surface)' ) ); ?>

<section style="background: var(--sm-surface);">
	<div class="sm-container" style="padding: 96px 32px;">
		<div style="display: grid; grid-template-columns: 1fr 1.3fr; gap: 56px; align-items: center; margin-bottom: 56px;">
			<div style="aspect-ratio: 4/3; border-radius: var(--sm-radius-lg); overflow: hidden; background-image: url('<?php echo esc_url( sm_image( 'hero-2.jpg' ) ); ?>'); background-size: cover; background-position: center;"></div>
			<div>
				<div class="sm-eyebrow">Områden</div>
				<h2 style="font-size: var(--sm-fs-xl);">Sex världar att upptäcka.</h2>
				<p style="font-size: var(--sm-fs-lg); color: var(--sm-ink-soft); margin-top: 16px;">
					Utställare inom det som berör vardagen — från drömresor till digital hjälp i lugn takt.
				</p>
			</div>
		</div>
		<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
			<?php foreach ( $zones as $z ) : ?>
				<div style="padding: 28px 24px; background: var(--sm-bg); border-radius: var(--sm-radius-lg);">
					<div style="display: flex; justify-content: space-between; align-items: baseline;">
						<div style="font-family: var(--sm-font-display); font-size: 22px; font-weight: 800;"><?php echo esc_html( $z['t'] ); ?></div>
						<div style="font-family: var(--sm-font-display); font-size: 18px; color: var(--sm-accent); font-weight: 700;"><?php echo esc_html( $z['n'] ); ?></div>
					</div>
					<p style="color: var(--sm-ink-soft); margin-top: 10px; font-size: 17px; margin-bottom: 0;"><?php echo esc_html( $z['d'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/wave-divider', null, array( 'from' => 'var(--sm-surface)', 'to' => 'var(--sm-primary)', 'flip' => true ) ); ?>

<section style="background: var(--sm-primary); color: #fff;">
	<div class="sm-container" style="padding: 64px 32px; text-align: center;">
		<h2 style="font-size: var(--sm-fs-xl); color: #fff;">Ta med en vän — det blir roligare så.</h2>
		<p style="font-size: var(--sm-fs-lg); opacity: 0.9; margin-top: 16px; font-weight: 300;">Dela gärna inbjudan. Köp biljett i förköp för 100 kr, eller 120 kr vid dörren.</p>
	</div>
</section>

<?php get_template_part( 'template-parts/wave-divider', null, array( 'from' => 'var(--sm-primary)', 'to' => 'var(--sm-bg)', 'flip' => true ) ); ?>

<section style="background: var(--sm-bg);">
	<div class="sm-container" style="padding: 96px 32px;">
		<div class="sm-eyebrow">Höjdpunkter 2027</div>
		<h2 style="font-size: var(--sm-fs-xl); max-width: 700px;">Det du inte vill missa.</h2>
		<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-top: 48px;">
			<?php foreach ( $highlights as $h ) : ?>
				<article style="background: var(--sm-surface); border-radius: var(--sm-radius-lg); overflow: hidden; box-shadow: var(--sm-shadow-sm);">
					<div style="aspect-ratio: 16/10; background-image: url('<?php echo esc_url( sm_image( $h['img'] ) ); ?>'); background-size: cover; background-position: center;"></div>
					<div style="padding: 28px;">
						<div style="font-size: 13px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; color: var(--sm-accent);"><?php echo esc_html( $h['k'] ); ?></div>
						<h3 style="font-size: 24px; margin-top: 10px; margin-bottom: 12px;"><?php echo esc_html( $h['t'] ); ?></h3>
						<p style="color: var(--sm-ink-soft); font-size: 17px; margin: 0;"><?php echo esc_html( $h['d'] ); ?></p>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/wave-divider', null, array( 'from' => 'var(--sm-bg)', 'to' => 'var(--sm-surface)' ) ); ?>

<?php get_template_part( 'template-parts/exhibitor-list' ); ?>

<script>
(function () {
	var slides = document.querySelectorAll('.sm-hero-slide');
	if (slides.length < 2) return;
	if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
	var i = 0;
	setInterval(function () {
		slides[i].style.opacity = '0';
		slides[i].setAttribute('aria-hidden', 'true');
		i = (i + 1) % slides.length;
		slides[i].style.opacity = '1';
		slides[i].setAttribute('aria-hidden', 'false');
	}, 5500);
})();
</script>

<?php get_footer(); ?>
