<?php
/**
 * Sidmall för "Hitta hit".
 */

get_header();

$venue_query        = rawurlencode( sm_venue_name() . ', ' . sm_venue_street() . ', ' . sm_venue_zip() );
$map_embed_url      = 'https://maps.google.com/maps?q=' . $venue_query . '&t=&z=15&ie=UTF8&iwloc=&output=embed';
$map_external_url   = 'https://www.google.com/maps/search/?api=1&query=' . $venue_query;
$map_title          = 'Karta till ' . sm_venue_name() . ', ' . sm_venue_street();

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
			<div
				class="sm-embed-gated"
				data-src="<?php echo esc_url( $map_embed_url ); ?>"
				data-title="<?php echo esc_attr( $map_title ); ?>">
				<p><?php echo esc_html( sm_text( 'sm_cookie_map_fallback', 'Kartan visas via Google Maps som sätter tredjepartscookies. Acceptera cookies för att visa kartan här, eller öppna direkt hos Google.' ) ); ?></p>
				<div style="display: flex; gap: 12px; flex-wrap: wrap; justify-content: center;">
					<button type="button" class="sm-btn sm-btn--small" data-sm-load-embed><?php echo esc_html( sm_text( 'sm_cookie_map_button', 'Acceptera och visa karta' ) ); ?></button>
					<a href="<?php echo esc_url( $map_external_url ); ?>" target="_blank" rel="noopener" class="sm-btn sm-btn--small sm-btn--ghost"><?php echo esc_html( sm_text( 'sm_cookie_map_external', 'Öppna i Google Maps' ) ); ?> →</a>
				</div>
			</div>
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
