<?php
/**
 * Sidmall för "Kontakt" — fyra kontaktkort.
 */

get_header();

if ( sm_has_contacts() ) {
	$contacts = sm_contacts();
} else {
	// Fallback tills redaktören har lagt in kontakter i WP-admin → Kontakter.
	$contacts = array(
		array( 'r' => 'Besökare & program',        'n' => 'Gästservice',         'e' => 'info@arenavarberg.se',           'p' => '0340-690200' ),
		array( 'r' => 'Projektledare',              'n' => 'Camilla Bourdette',   'e' => 'camilla.bourdette@arenavarberg.se', 'p' => '0340-690204' ),
		array( 'r' => 'Försäljning / Utställare',   'n' => 'Vivi Strindö',        'e' => 'vivi.strindo@arenavarberg.se',   'p' => '0340-690213' ),
		array( 'r' => 'Försäljning / Utställare',   'n' => 'Susanne Carlsson',    'e' => 'susanne.carlsson@arenavarberg.se','p' => '0340-690200' ),
	);
}

get_template_part( 'template-parts/page-hero', null, array(
	'eyebrow' => 'Kontakt',
	'title'   => sm_text( 'sm_kontakt_hero_title', 'Prata med oss.' ),
	'body'    => sm_text( 'sm_kontakt_hero_body',  'Vi svarar inom ett arbetsdygn.' ),
	'tone'    => 'accent',
) );
?>

<section class="sm-container" style="padding: 48px 32px 96px;">
	<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 32px;">
		<?php foreach ( $contacts as $c ) :
			$phone_tel = preg_replace( '/[^0-9+]/', '', $c['p'] );
			?>
			<div class="sm-card">
				<div style="font-size: 13px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; color: var(--sm-accent);"><?php echo esc_html( $c['r'] ); ?></div>
				<div style="font-family: var(--sm-font-display); font-size: 26px; font-weight: 700; margin-top: 10px;"><?php echo esc_html( $c['n'] ); ?></div>
				<div style="font-size: 17px; margin-top: 16px; color: var(--sm-ink-soft); line-height: 1.6;">
					<a href="mailto:<?php echo esc_attr( $c['e'] ); ?>" style="color: inherit;"><?php echo esc_html( $c['e'] ); ?></a><br>
					<a href="tel:<?php echo esc_attr( $phone_tel ); ?>" style="color: inherit;"><?php echo esc_html( $c['p'] ); ?></a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<?php get_footer(); ?>
