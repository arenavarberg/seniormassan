<?php
/**
 * SVG-karta över mässgolvet.
 *
 * Args:
 *   booked_ids: array av monter-ID som ska visas som bokade
 *   selected_ids: array av monter-ID som ska visas som valda
 */

$args        = isset( $args ) && is_array( $args ) ? $args : array();
$booked_ids   = $args['booked_ids'] ?? array();
$selected_ids = $args['selected_ids'] ?? array();

$booked_set   = array_flip( (array) $booked_ids );
$selected_set = array_flip( (array) $selected_ids );

$colors_bg = array(
	'cafe'      => '#1a1a1a',
	'cafe2'     => '#1a1a1a',
	'stage'     => '#E8407C',
	'stage2'    => '#E8407C',
	'trappa'    => '#1a1a1a',
	'trappa2'   => '#E6741E',
	'garderob'  => '#B97FB0',
	'toilet'    => '#E6741E',
	'toilet2'   => '#E6741E',
	'guests'    => '#fff',
	'stolar'    => '#f6f3ee',
);

$fixed_areas = array(
	array( 'kind' => 'cafe',     'x' => 70,  'y' => 60,  'w' => 100, 'h' => 40,  'label' => 'CAFÉ' ),
	array( 'kind' => 'stage',    'x' => 235, 'y' => 100, 'w' => 60,  'h' => 110, 'label' => 'UTSTÄLLAR­SCEN' ),
	array( 'kind' => 'trappa',   'x' => 310, 'y' => 60,  'w' => 110, 'h' => 70,  'label' => 'TRAPPA IN & UT' ),
	array( 'kind' => 'cafe2',    'x' => 750, 'y' => 320, 'w' => 160, 'h' => 100, 'label' => 'CAFÉ' ),
	array( 'kind' => 'stage2',   'x' => 800, 'y' => 440, 'w' => 100, 'h' => 100, 'label' => 'SCEN' ),
	array( 'kind' => 'trappa2',  'x' => 175, 'y' => 425, 'w' => 90,  'h' => 50,  'label' => 'TRAPPA ENTRÉ' ),
	array( 'kind' => 'garderob', 'x' => 270, 'y' => 320, 'w' => 36,  'h' => 110, 'label' => 'GARDEROB' ),
	array( 'kind' => 'toilet',   'x' => 230, 'y' => 240, 'w' => 36,  'h' => 60,  'label' => 'WC' ),
	array( 'kind' => 'toilet2',  'x' => 230, 'y' => 480, 'w' => 36,  'h' => 60,  'label' => 'WC' ),
	array( 'kind' => 'stolar',   'x' => 750, 'y' => 580, 'w' => 180, 'h' => 110, 'label' => 'Publikplatser' ),
);
?>
<div style="background: #fff; border: 1px solid var(--sm-line-soft); border-radius: var(--sm-radius-lg); padding: 24px; box-shadow: var(--sm-shadow-sm);">
	<div style="display: flex; gap: 28px; flex-wrap: wrap; margin-bottom: 20px; font-size: 14px;">
		<?php
		$legend = array(
			array( '#F2DC6E', '2 × 2 m  ·  3 820 kr' ),
			array( '#7DC1D9', '2 × 3 m  ·  5 730 kr' ),
			array( '#A8D38E', '3 × 3 m  ·  8 845 kr' ),
			array( '#E8A6C4', 'N1–N12  ·  endast föreningar  ·  2 360 kr inkl. moms' ),
			array( 'var(--sm-accent)', 'Vald' ),
			array( '#d6d2cb', 'Bokad' ),
		);
		foreach ( $legend as $l ) :
			?>
			<div style="display: flex; align-items: center; gap: 10px;">
				<span style="width: 22px; height: 22px; background: <?php echo esc_attr( $l[0] ); ?>; border-radius: 3px; border: 1px solid rgba(0,0,0,0.15);"></span>
				<span style="color: var(--sm-ink-soft);"><?php echo esc_html( $l[1] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>

	<svg viewBox="0 0 1000 780" xmlns="http://www.w3.org/2000/svg" style="width: 100%; height: auto; background: var(--sm-bg); border-radius: 6px;" aria-label="Hallkarta — Seniormässan på Arena Varberg">
		<rect x="0" y="0" width="1000" height="780" fill="var(--sm-bg)"/>

		<text x="500" y="36" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="800" font-size="20" fill="#333">SENIORMÄSSAN 2027</text>
		<text x="240" y="80" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="700" font-size="13" fill="#666">ENTRÉHALLEN</text>
		<text x="700" y="80" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="700" font-size="13" fill="#666">SPARBANKSHALLEN</text>
		<text x="220" y="755" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="700" font-size="13" fill="#666">HUVUDENTRÉ</text>

		<?php foreach ( $fixed_areas as $fa ) :
			$bg = $colors_bg[ $fa['kind'] ] ?? '#ddd';
			$fg = in_array( $fa['kind'], array( 'cafe', 'cafe2', 'trappa' ), true ) ? '#E6741E' : '#fff';
			if ( $fa['kind'] === 'stolar' ) {
				$fg = '#888';
			}
			?>
			<g>
				<rect x="<?php echo (int) $fa['x']; ?>" y="<?php echo (int) $fa['y']; ?>" width="<?php echo (int) $fa['w']; ?>" height="<?php echo (int) $fa['h']; ?>" fill="<?php echo esc_attr( $bg ); ?>" rx="3"/>
				<text x="<?php echo (int) ( $fa['x'] + $fa['w'] / 2 ); ?>" y="<?php echo (int) ( $fa['y'] + $fa['h'] / 2 + 4 ); ?>" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="700" font-size="11" fill="<?php echo esc_attr( $fg ); ?>"><?php echo esc_html( $fa['label'] ); ?></text>
			</g>
		<?php endforeach; ?>

		<?php foreach ( sm_booths() as $b ) :
			$is_booked   = isset( $booked_set[ $b['id'] ] );
			$is_selected = isset( $selected_set[ $b['id'] ] );
			$is_forening = sm_is_forening_booth( $b['id'] );

			if ( $is_booked ) {
				$fill = '#d6d2cb';
			} elseif ( $is_selected ) {
				$fill = 'var(--sm-accent)';
			} elseif ( $is_forening ) {
				$fill = SM_BOOTH_COLORS['forening'];
			} else {
				$fill = SM_BOOTH_COLORS[ $b['size'] ];
			}
			$text_color = $is_selected ? '#fff' : '#222';
			?>
			<g>
				<rect x="<?php echo (int) $b['x']; ?>" y="<?php echo (int) $b['y']; ?>" width="<?php echo (int) $b['w']; ?>" height="<?php echo (int) $b['h']; ?>" rx="2" fill="<?php echo esc_attr( $fill ); ?>" stroke="rgba(0,0,0,0.2)" stroke-width="0.5"/>
				<text x="<?php echo (float) ( $b['x'] + $b['w'] / 2 ); ?>" y="<?php echo (float) ( $b['y'] + $b['h'] / 2 + 3.5 ); ?>" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="700" font-size="10" fill="<?php echo esc_attr( $text_color ); ?>"><?php echo esc_html( $b['id'] ); ?></text>
			</g>
		<?php endforeach; ?>
	</svg>
</div>
