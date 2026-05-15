<?php
/**
 * Sidmall för "Program" — två scener med tider.
 */

get_header();

$stora_scen = array(
	array( 't' => '10:00', 'n' => 'Peter Börjesson',           'd' => 'Välkomnar från scenen' ),
	array( 't' => '10:15', 'n' => 'Modevisning',               'd' => 'Rydholms Modehus & Dressmann' ),
	array( 't' => '11:00', 'n' => 'Lotta Engberg',             'd' => 'Livemusik med pianist' ),
	array( 't' => '11:45', 'n' => 'Elisabeth Wahlin',          'd' => '100% livslust' ),
	array( 't' => '13:15', 'n' => 'Linedance',                 'd' => 'Prova på med Tobias Herbertzon' ),
	array( 't' => '14:00', 'n' => 'Modevisning',               'd' => 'Modestugan visar höstens mode' ),
	array( 't' => '14:30', 'n' => 'Lotta Engberg',             'd' => 'Livemusik med pianist' ),
	array( 't' => '15:00', 'n' => 'Föreläsning om svenskt vin','d' => 'Branschorganisationen svenskt vin' ),
	array( 't' => '15:40', 'n' => 'Peter Börjesson',           'd' => 'Föreläsning om 60-talets Varberg' ),
	array( 't' => '16:00', 'n' => 'Elisabeth Wahlin',          'd' => '100% livslust' ),
);

$utstallarscenen = array(
	array( 't' => '11:00', 'n' => 'Glaukomföreningen',         'd' => 'Den lömska smygande ögonsjukdomen Glaukom' ),
	array( 't' => '11:30', 'n' => 'Hantverksmagasinet',        'd' => 'Kan hantverk förändra världen?' ),
	array( 't' => '12:00', 'n' => 'Teater Halland',            'd' => 'En plats för möten, samtal och starka teaterupplevelser' ),
	array( 't' => '12:30', 'n' => 'Jourhavande medmänniska',   'd' => 'Informerar om sin verksamhet' ),
	array( 't' => '13:00', 'n' => 'Varbergs kommun',           'd' => 'Välkommen på en digital promenad i framtidens Västerport!' ),
	array( 't' => '13:30', 'n' => 'Varberg Energi',            'd' => 'Få bättre koll på din energi — på ett enkelt sätt!' ),
	array( 't' => '14:00', 'n' => 'Fot & Fin',                 'd' => 'Har vi slutat lita på våra fötter?' ),
	array( 't' => '14:30', 'n' => 'Samtalsbyrån Varberg',      'd' => 'Sorg genom livet — och vägarna som hjälper oss vidare' ),
);

get_template_part( 'template-parts/page-hero', null, array(
	'eyebrow' => 'Program',
	'title'   => 'En dag, två scener, 18 programpunkter.',
	'body'    => 'Allt ingår i entrén — kom och gå som du vill.',
	'tone'    => 'primary',
) );
?>

<section class="sm-container" style="padding: 64px 32px 32px;">
	<div style="display: flex; align-items: baseline; gap: 16px; margin-bottom: 8px; flex-wrap: wrap;">
		<div class="sm-eyebrow" style="margin: 0;">Datum</div>
	</div>
	<h2 style="font-size: 36px; margin: 0 0 8px; color: var(--sm-primary);">Onsdag 24 februari</h2>
	<p style="font-size: 17px; color: var(--sm-ink-soft); margin: 0 0 40px;">
		Programmet uppdateras löpande inför mässan. Med reservation för ändringar.
	</p>

	<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
		<?php
		$columns = array(
			array( 'title' => 'Stora Scen',      'rows' => $stora_scen,      'accent' => 'var(--sm-primary)' ),
			array( 'title' => 'Utställarscenen', 'rows' => $utstallarscenen, 'accent' => 'var(--sm-accent)' ),
		);
		foreach ( $columns as $col ) :
			?>
			<div>
				<div style="background: <?php echo esc_attr( $col['accent'] ); ?>; color: #fff; padding: 18px 24px; border-radius: var(--sm-radius-lg) var(--sm-radius-lg) 0 0; font-family: var(--sm-font-display); font-size: 22px; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase;">
					<?php echo esc_html( $col['title'] ); ?>
				</div>
				<div style="background: var(--sm-surface); border-radius: 0 0 var(--sm-radius-lg) var(--sm-radius-lg); border: 1px solid var(--sm-line); border-top: none; overflow: hidden;">
					<?php foreach ( $col['rows'] as $i => $r ) : ?>
						<div style="display: grid; grid-template-columns: 80px 1fr; padding: 18px 24px; <?php echo $i === 0 ? '' : 'border-top: 1px solid var(--sm-line-soft);'; ?> align-items: baseline; gap: 16px;">
							<div style="font-family: var(--sm-font-display); font-size: 20px; font-weight: 700; color: <?php echo esc_attr( $col['accent'] ); ?>;"><?php echo esc_html( $r['t'] ); ?></div>
							<div>
								<div style="font-size: 18px; font-weight: 700; color: var(--sm-ink); line-height: 1.25;"><?php echo esc_html( $r['n'] ); ?></div>
								<div style="font-size: 15px; color: var(--sm-ink-soft); margin-top: 4px; line-height: 1.4;"><?php echo esc_html( $r['d'] ); ?></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<section style="padding-bottom: 80px;"></section>

<?php get_footer(); ?>
