<?php
/**
 * Sidmall för "För utställare".
 *
 * Sektioner enligt site-pages.jsx → ExhibitorPage:
 *   1. PageHero (accent)
 *   2. Nyhet 2027 — callout med rotation-badge
 *   3. Full-bleed bild
 *   4. Varför ställa ut? (3 kolumner)
 *   5. Utställarscen — kort
 *   6. Monterpaket (4 kort) + tillägg
 *   7. Vimeo "Stämningen 2025"
 *   8. Citat — Annett Wiktorsson
 */

get_header();

$packages = array(
	array( 'size' => '2 × 2 m',  'price' => 3820, 'ideal' => 'Standardmonter för enskild utställare', 'includes' => array( 'El (230 V)', 'Vita mässväggar', 'Omnämning i program', '5 digitala entrébiljetter' ) ),
	array( 'size' => '2 × 3 m',  'price' => 5730, 'ideal' => 'Mest populära paketet',                 'includes' => array( 'El (230 V)', 'Vita mässväggar', 'Omnämning i program', '5 digitala entrébiljetter' ), 'featured' => true ),
	array( 'size' => '3 × 3 m',  'price' => 8845, 'ideal' => 'Stor monter med extra synlighet',      'includes' => array( 'El (230 V)', 'Vita mässväggar', 'Omnämning i program', '5 digitala entrébiljetter' ) ),
	array( 'size' => 'Förening', 'price' => 2360, 'price_label' => 'inkl. moms', 'ideal' => '2 × 2 m · Föreningspris (ideella föreningar)', 'includes' => array( 'El (230 V)', 'Vita mässväggar', 'Omnämning i program' ) ),
);

$addons = array(
	array( 'Registreringsavgift (obligatorisk)', 800 ),
	array( 'Montermatta', 110 ),
	array( 'Wifi (premium)', 450 ),
	array( 'Monterbord', 350 ),
	array( 'Stol', 120 ),
	array( 'Matbiljett utställare', 180 ),
);

$reasons = array(
	array( 'n' => '01', 't' => 'Målgrupp med tid & lust',   'd' => 'Seniorer är en målgrupp som växer varje år — med både tid, nyfikenhet och köpkraft.' ),
	array( 'n' => '02', 't' => 'Långa kvalitetsmöten',      'd' => 'Besökare stannar många timmar tack vare scen, caféer, bar och lunchservering. Tid för riktiga samtal.' ),
	array( 'n' => '03', 't' => 'Vi marknadsför åt dig',     'd' => 'Annonser i dagspress, digitala kanaler, vägpratare och via organisationer. 2027 även mot Sjuhärad.' ),
);

get_template_part( 'template-parts/page-hero', null, array(
	'eyebrow' => 'För utställare',
	'title'   => 'Möt ~2000 engagerade seniorer.',
	'body'    => 'Besökare tillbringar många timmar här och har god tid att besöka alla utställare — utan stress. Seniormässan är en etablerad mötesplats i Halland sedan 10+ år.',
	'tone'    => 'accent',
	'cta'     => 'Gå till anmälan →',
	'cta_url' => home_url( '/anmalan/' ),
) );

get_template_part( 'template-parts/wave-divider', null, array( 'from' => 'var(--sm-accent)', 'to' => 'var(--sm-bg)' ) );
?>

<section style="background: var(--sm-bg);">
	<div class="sm-container" style="padding: 88px 32px;">
		<div style="background: var(--sm-surface); border-radius: var(--sm-radius-lg); padding: 56px; display: grid; grid-template-columns: auto 1fr; gap: 48px; align-items: center; box-shadow: var(--sm-shadow-sm); position: relative; overflow: hidden;">
			<div aria-hidden="true" style="position: absolute; top: -40px; right: -40px; width: 220px; height: 220px; border-radius: 50%; background: var(--sm-gold); opacity: 0.18;"></div>
			<div style="width: 120px; height: 120px; border-radius: 50%; background: var(--sm-accent); color: #fff; display: flex; align-items: center; justify-content: center; font-family: var(--sm-font-display); font-weight: 800; font-size: 22px; line-height: 1.05; text-align: center; flex-shrink: 0; transform: rotate(-6deg); box-shadow: 0 6px 18px rgba(198, 55, 29, 0.35);">
				Nyhet<br>2027
			</div>
			<div style="position: relative; z-index: 1;">
				<div class="sm-eyebrow" style="margin-bottom: 12px;">Nyhet 2027</div>
				<h2 style="font-size: var(--sm-fs-xl); margin-bottom: 20px; max-width: 720px;">
					5 digitala entrébiljetter ingår — bjud in dina kunder.
				</h2>
				<p style="font-size: var(--sm-fs-lg); color: var(--sm-ink-soft); max-width: 720px; margin-bottom: 0;">
					I varje bokad monter ingår 5 stycken kostnadsfria digitala entrébiljetter. Dessa digitala entrébiljetter kan du som utställare dela ut bland kunder, medlemmar eller någon som du önskar bjuda in till mässan.
				</p>
			</div>
		</div>
	</div>
</section>

<section style="position: relative; height: 480px; background-image: url('<?php echo esc_url( sm_image( 'senior-wide.jpg' ) ); ?>'); background-size: cover; background-position: center 35%;"></section>

<section style="background: var(--sm-bg);">
	<div class="sm-container" style="padding: 80px 32px 40px;">
		<div class="sm-eyebrow">Varför ställa ut?</div>
		<h2 style="font-size: var(--sm-fs-xl); max-width: 800px;">Kvalificerade möten, inte flyktiga förbipasseranden.</h2>
		<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; margin-top: 56px;">
			<?php foreach ( $reasons as $b ) : ?>
				<div>
					<div style="font-family: var(--sm-font-display); font-size: 64px; font-weight: 800; color: var(--sm-accent); letter-spacing: -0.04em; line-height: 1;"><?php echo esc_html( $b['n'] ); ?></div>
					<h3 style="font-size: 24px; margin-top: 16px; margin-bottom: 10px;"><?php echo esc_html( $b['t'] ); ?></h3>
					<p style="color: var(--sm-ink-soft); margin: 0;"><?php echo esc_html( $b['d'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section style="background: var(--sm-bg);">
	<div class="sm-container" style="padding: 40px 32px 80px;">
		<div style="background: var(--sm-surface); border-radius: var(--sm-radius-lg); padding: 56px; border: 1px solid var(--sm-line-soft); display: grid; grid-template-columns: 1.2fr 1fr; gap: 48px; align-items: center;">
			<div>
				<div class="sm-eyebrow" style="margin-bottom: 12px;">Utställarscen</div>
				<h2 style="font-size: var(--sm-fs-xl); margin-bottom: 20px; max-width: 560px;">
					Nå ut med ditt budskap på vår utställarscen.
				</h2>
				<p style="font-size: 18px; color: var(--sm-ink-soft); margin: 0; max-width: 560px;">
					Samtidigt som vi släpper upp monterbokningen släpper vi också upp tidsbokningen till vår utställarscen. Det är kostnadsfritt och först till kvarn som gäller — så passa på och knip dessa eftertraktade platserna.
				</p>
			</div>
			<div style="aspect-ratio: 4/3; border-radius: var(--sm-radius-lg); background-image: url('<?php echo esc_url( sm_image( 'scenprogram.jpg' ) ); ?>'); background-size: cover; background-position: center;"></div>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/wave-divider', null, array( 'from' => 'var(--sm-bg)', 'to' => 'var(--sm-surface)', 'flip' => true ) ); ?>

<section style="background: var(--sm-surface);">
	<div class="sm-container" style="padding: 80px 32px;">
		<div class="sm-eyebrow">Monterpaket</div>
		<h2 style="font-size: var(--sm-fs-xl);">Tre storlekar. Alla inklusive det grundläggande.</h2>
		<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-top: 48px;">
			<?php foreach ( $packages as $p ) :
				$featured = ! empty( $p['featured'] );
				$bg       = $featured ? 'var(--sm-primary)' : 'var(--sm-bg)';
				$color    = $featured ? '#fff' : 'var(--sm-ink)';
				$border   = $featured ? 'none' : '1px solid var(--sm-line)';
				$check    = $featured ? 'var(--sm-accent)' : 'var(--sm-primary)';
				$line     = $featured ? 'rgba(255,255,255,0.2)' : 'var(--sm-line)';
				$label    = $p['price_label'] ?? 'exkl. moms';
				?>
				<div style="background: <?php echo esc_attr( $bg ); ?>; color: <?php echo esc_attr( $color ); ?>; border-radius: var(--sm-radius-lg); padding: 28px; position: relative; border: <?php echo esc_attr( $border ); ?>;">
					<?php if ( $featured ) : ?>
						<div style="position: absolute; top: 14px; right: 14px; background: var(--sm-accent); color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 700; letter-spacing: 0.08em;">POPULÄRAST</div>
					<?php endif; ?>
					<div style="font-family: var(--sm-font-display); font-size: 28px; font-weight: 800; letter-spacing: -0.02em;"><?php echo esc_html( $p['size'] ); ?></div>
					<div style="font-size: 14px; opacity: 0.75; margin-top: 4px; min-height: 40px;"><?php echo esc_html( $p['ideal'] ); ?></div>
					<div style="font-family: var(--sm-font-display); font-size: 40px; font-weight: 800; margin-top: 16px;">
						<?php echo esc_html( number_format( $p['price'], 0, ',', "\u{00A0}" ) ); ?> <span style="font-size: 16px; opacity: 0.7; font-weight: 500;">kr</span>
					</div>
					<div style="font-size: 13px; opacity: 0.7; margin-bottom: 18px;"><?php echo esc_html( $label ); ?></div>
					<ul style="padding-left: 0; list-style: none; margin: 0; border-top: 1px solid <?php echo esc_attr( $line ); ?>; padding-top: 14px;">
						<?php foreach ( $p['includes'] as $inc ) : ?>
							<li style="font-size: 15px; padding: 6px 0; display: flex; gap: 8px; align-items: flex-start;">
								<span style="color: <?php echo esc_attr( $check ); ?>; font-weight: 700;">✓</span><?php echo esc_html( $inc ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>

		<div style="margin-top: 64px;">
			<div class="sm-eyebrow">Tillägg</div>
			<h3 style="font-size: 28px; margin-bottom: 8px;">Skräddarsy din monter.</h3>
			<p style="font-size: 17px; color: var(--sm-ink-soft); margin-bottom: 24px; max-width: 720px; line-height: 1.5;">
				Vill du ha större utrymme kan du välja flera montrar. Du kan också lägga till produkter i din monter — t.ex. golv i olika färger, bord, stolar, utställarlunch m.m.
			</p>
			<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0; border: 1px solid var(--sm-line); border-radius: var(--sm-radius-lg); overflow: hidden; background: var(--sm-bg);">
				<?php
				$total = count( $addons );
				foreach ( $addons as $i => $a ) :
					$show_bottom = $i < $total - 3;
					$show_right  = ( $i + 1 ) % 3 !== 0;
					?>
					<div style="padding: 20px 24px; display: flex; justify-content: space-between; <?php echo $show_bottom ? 'border-bottom: 1px solid var(--sm-line);' : ''; ?> <?php echo $show_right ? 'border-right: 1px solid var(--sm-line);' : ''; ?>">
						<span style="font-size: 17px;"><?php echo esc_html( $a[0] ); ?></span>
						<span style="font-weight: 700; color: var(--sm-primary);"><?php echo esc_html( $a[1] ); ?> kr</span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div style="text-align: center; margin-top: 56px;">
			<a href="<?php echo esc_url( home_url( '/anmalan/' ) ); ?>" class="sm-btn sm-btn--accent">Anmäl din monter →</a>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/wave-divider', null, array( 'from' => 'var(--sm-surface)', 'to' => 'var(--sm-bg)' ) ); ?>

<section style="background: var(--sm-bg);">
	<div class="sm-container" style="padding: 80px 32px;">
		<div style="text-align: center; margin-bottom: 32px;">
			<div class="sm-eyebrow">Stämningen 2025</div>
			<h2 style="font-size: var(--sm-fs-xl); max-width: 760px; margin: 0 auto;">Så här ser det ut att ställa ut hos oss.</h2>
		</div>
		<div style="max-width: 960px; margin: 0 auto;">
			<div style="position: relative; padding-top: 56.25%; border-radius: var(--sm-radius-lg); overflow: hidden; box-shadow: var(--sm-shadow-md); background: #000;">
				<iframe src="https://player.vimeo.com/video/1178774214?title=0&byline=0&portrait=0" title="Seniormässan 2025 — för utställare" style="position: absolute; inset: 0; width: 100%; height: 100%; border: 0;" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
			</div>
		</div>
	</div>
</section>

<section style="background: var(--sm-bg);">
	<div class="sm-container" style="padding: 80px 32px;">
		<div style="display: grid; grid-template-columns: 260px 1fr; gap: 48px; align-items: center;">
			<img src="<?php echo esc_url( sm_image( 'annett-wiktorsson.webp' ) ); ?>" alt="Annett Wiktorsson, Modestugan" style="width: 100%; aspect-ratio: 1/1; object-fit: cover; border-radius: 50%; box-shadow: var(--sm-shadow-md);">
			<blockquote style="margin: 0; max-width: 820px; font-size: 30px; font-family: var(--sm-font-display); font-weight: 600; line-height: 1.3; letter-spacing: -0.01em;">
				”Många åker till vår butik i Åsa och handlar direkt efter att vi träffats på mässan.”
				<footer style="font-size: 16px; font-family: var(--sm-font-body); font-weight: 500; margin-top: 20px; color: var(--sm-ink-soft);">
					— Annett Wiktorsson, Modestugan <span style="opacity: 0.7;">(deltagit sedan 2015)</span>
				</footer>
			</blockquote>
		</div>
	</div>
</section>

<?php get_footer(); ?>
