<?php
/**
 * Sidmall för "Hitta hit".
 */

get_header();

$venue_query = rawurlencode( sm_venue_name() . ', ' . sm_venue_street() . ', ' . sm_venue_zip() );

get_template_part( 'template-parts/page-hero', null, array(
	'eyebrow' => 'Hitta hit',
	'title'   => sm_text( 'sm_hitta_hero_title', sm_venue_name() . ' — den stadsnära arenan.' ),
	'body'    => sm_text( 'sm_hitta_hero_body',  '15 minuters promenad från stationen. Fri parkering med över 200 platser.' ),
	'tone'    => 'primary',
) );
?>

<section class="sm-container" style="padding: 48px 32px 96px;">
	<div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 48px;">
		<div style="height: 440px; border-radius: var(--sm-radius-lg); overflow: hidden; border: 1px solid var(--sm-line);">
			<iframe
				src="https://maps.google.com/maps?q=<?php echo esc_attr( $venue_query ); ?>&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=&amp;output=embed"
				style="width: 100%; height: 100%; border: 0;"
				title="Karta till <?php echo esc_attr( sm_venue_name() . ', ' . sm_venue_street() ); ?>"
				loading="lazy"
				referrerpolicy="no-referrer-when-downgrade"></iframe>
		</div>
		<div>
			<?php
			get_template_part( 'template-parts/info-block', null, array(
				'title' => 'Adress',
				'items' => array(
					array( '', sm_venue_name() ),
					array( '', sm_venue_street() ),
					array( '', sm_venue_zip() ),
				),
			) );
			get_template_part( 'template-parts/info-block', null, array(
				'title' => 'Fri parkering',
				'items' => array(
					array( '', sm_text( 'sm_parking_info_1', 'Över 200 platser på området' ) ),
					array( '', sm_text( 'sm_parking_info_2', 'Kostnadsfritt under hela mässdagen' ) ),
				),
			) );
			get_template_part( 'template-parts/info-block', null, array(
				'title' => 'Tillgänglighet',
				'items' => array(
					array( '', sm_text( 'sm_access_info_1', 'Ramp till huvudentré' ) ),
					array( '', sm_text( 'sm_access_info_2', 'Tillgängliga toaletter på plan 1 & 2' ) ),
					array( '', sm_text( 'sm_access_info_3', 'Ljudslinga i scenområdena' ) ),
				),
			) );
			?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
