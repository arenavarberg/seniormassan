<?php
/**
 * SVG-karta över mässgolvet (v2 — med väggar och proportionsenligt utrymme).
 *
 * Layouten är baserad på den officiella hallplanen för 2026 men koordinater
 * härstammar från design_handoff_seniormassan/booth-map.jsx. Justera enskilda
 * koordinater i inc/booth-data.php när 2027-planen släpps.
 *
 * Args:
 *   booked_ids:   array av monter-ID som ska visas som bokade
 *   selected_ids: array av monter-ID som ska visas som valda
 */

$args         = isset( $args ) && is_array( $args ) ? $args : array();
$booked_ids   = $args['booked_ids']   ?? array();
$selected_ids = $args['selected_ids'] ?? array();

$booked_set   = array_flip( (array) $booked_ids );
$selected_set = array_flip( (array) $selected_ids );

// Hjälpfunktion för fyllning
$booth_fill = function ( $b ) use ( $booked_set, $selected_set ) {
	if ( isset( $booked_set[ $b['id'] ] ) )   return '#d6d2cb';
	if ( isset( $selected_set[ $b['id'] ] ) ) return 'var(--sm-accent)';
	if ( sm_is_forening_booth( $b['id'] ) )   return SM_BOOTH_COLORS['forening'];
	return SM_BOOTH_COLORS[ $b['size'] ];
};
?>
<div style="background: #fff; border: 1px solid var(--sm-line-soft); border-radius: var(--sm-radius-lg); padding: 24px; box-shadow: var(--sm-shadow-sm);">

	<!-- Förklaring -->
	<div style="display: flex; gap: 24px; flex-wrap: wrap; margin-bottom: 20px; font-size: 13px;">
		<?php
		$legend = array(
			array( '#F2DC6E',         '2 × 2 m  ·  3 820 kr' ),
			array( '#7DC1D9',         '2 × 3 m  ·  5 730 kr' ),
			array( '#A8D38E',         '3 × 3 m  ·  8 845 kr' ),
			array( '#E8A6C4',         'N1–N12  ·  förening  ·  2 360 kr inkl. moms' ),
			array( 'var(--sm-accent)','Vald' ),
			array( '#d6d2cb',         'Bokad' ),
		);
		foreach ( $legend as $l ) :
			?>
			<div style="display: flex; align-items: center; gap: 8px;">
				<span style="width: 18px; height: 18px; background: <?php echo esc_attr( $l[0] ); ?>; border-radius: 2px; border: 1px solid rgba(0,0,0,0.15);"></span>
				<span style="color: var(--sm-ink-soft);"><?php echo esc_html( $l[1] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>

	<svg viewBox="0 0 1000 780" xmlns="http://www.w3.org/2000/svg" style="width: 100%; height: auto; background: #ffffff; display: block;" aria-label="Hallkarta — Seniormässan på Arena Varberg">
		<defs>
			<pattern id="sm-seats" x="0" y="0" width="8" height="8" patternUnits="userSpaceOnUse">
				<circle cx="2" cy="2" r="1" fill="#444"/>
			</pattern>
		</defs>

		<!-- Vit bakgrund -->
		<rect x="0" y="0" width="1000" height="780" fill="#ffffff"/>

		<!-- Rubrik -->
		<text x="500" y="32" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="800" font-size="18" fill="#1a1a1a">SENIORMÄSSAN 2027</text>

		<!-- ENTRÉHALLEN — vänster hall (yttervägg) -->
		<g>
			<rect x="40" y="60" width="230" height="440" fill="none" stroke="#111" stroke-width="2.5"/>
			<text x="155" y="78" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="800" font-size="13" fill="#1a1a1a" letter-spacing="0.06em">ENTRÉHALLEN</text>
		</g>

		<!-- SPARBANKSHALLEN — höger hall (yttervägg) -->
		<g>
			<rect x="270" y="60" width="710" height="690" fill="none" stroke="#111" stroke-width="2.5"/>
			<text x="625" y="78" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="800" font-size="13" fill="#1a1a1a" letter-spacing="0.06em">SPARBANKSHALLEN</text>
		</g>


		<!-- HUVUDENTRÉ -markering nedanför södra väggen -->
		<text x="640" y="772" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="700" font-size="11" fill="#666" letter-spacing="0.18em">HUVUDENTRÉ</text>

		<!-- NÖDUTGÅNGAR (gula markeringar i väggen) -->
		<?php
		$exits = array(
			array( 'x' => 350, 'y' => 58,  'w' => 24, 'h' => 4 ),
			array( 'x' => 440, 'y' => 58,  'w' => 24, 'h' => 4 ),
			array( 'x' => 600, 'y' => 58,  'w' => 24, 'h' => 4 ),
			array( 'x' => 750, 'y' => 58,  'w' => 24, 'h' => 4 ),
			array( 'x' => 38,  'y' => 280, 'w' => 4,  'h' => 24 ),
			array( 'x' => 978, 'y' => 170, 'w' => 4,  'h' => 24 ),
			array( 'x' => 978, 'y' => 360, 'w' => 4,  'h' => 24 ),
			array( 'x' => 978, 'y' => 530, 'w' => 4,  'h' => 24 ),
			array( 'x' => 350, 'y' => 748, 'w' => 24, 'h' => 4 ),
			array( 'x' => 480, 'y' => 748, 'w' => 24, 'h' => 4 ),
		);
		foreach ( $exits as $e ) :
			printf(
				'<rect x="%d" y="%d" width="%d" height="%d" fill="#F0D429"/>',
				(int) $e['x'], (int) $e['y'], (int) $e['w'], (int) $e['h']
			);
		endforeach;
		?>

		<!-- Café (top-left i entréhallen) -->
		<rect x="70" y="80" width="80" height="32" fill="#1a1a1a" rx="2"/>
		<text x="110" y="100" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="800" font-size="12" fill="#E6741E" letter-spacing="0.08em">CAFÉ</text>

		<!-- Utställarscen (rosa, höger i entréhallen) -->
		<rect x="218" y="100" width="34" height="100" fill="#E8407C" rx="2"/>
		<text x="235" y="155" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="800" font-size="10" fill="#fff" letter-spacing="0.08em" transform="rotate(-90, 235, 155)">UTSTÄLLARSCEN</text>

		<!-- Trappa in & ut mässgolv (svart, mellan hallarna upptill) -->
		<rect x="265" y="100" width="32" height="80" fill="#1a1a1a" rx="2"/>
		<text x="281" y="138" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="800" font-size="9" fill="#E6741E" transform="rotate(-90, 281, 138)">TRAPPA IN &amp; UT</text>

		<!-- Toaletter övre (orange, rotated) -->
		<rect x="265" y="200" width="32" height="50" fill="#E6741E" rx="2"/>
		<text x="281" y="227" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="800" font-size="9" fill="#fff" letter-spacing="0.06em" transform="rotate(-90, 281, 227)">TOALETTER</text>

		<!-- Garderob (lila) -->
		<rect x="265" y="260" width="32" height="100" fill="#B97FB0" rx="2"/>
		<text x="281" y="312" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="800" font-size="10" fill="#fff" letter-spacing="0.08em" transform="rotate(-90, 281, 312)">GARDEROB</text>

		<!-- Trappa entré & utgång (svart) + Hiss (orange) -->
		<rect x="180" y="395" width="60" height="38" fill="#1a1a1a" rx="2"/>
		<text x="210" y="411" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="800" font-size="8" fill="#fff">TRAPPA</text>
		<text x="210" y="421" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="800" font-size="8" fill="#fff">ENTRÉ &amp; UTGÅNG</text>
		<rect x="240" y="395" width="20" height="38" fill="#E6741E" rx="2"/>
		<text x="250" y="418" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="800" font-size="9" fill="#fff" transform="rotate(-90, 250, 418)">HISS</text>

		<!-- Toaletter nedre -->
		<rect x="265" y="380" width="32" height="50" fill="#E6741E" rx="2"/>
		<text x="281" y="408" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="800" font-size="9" fill="#fff" letter-spacing="0.06em" transform="rotate(-90, 281, 408)">TOALETTER</text>

		<!-- Gästservice / Restaurang etikett (entréhallen, längst ner) -->
		<text x="150" y="460" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="700" font-size="10" fill="#666">← GÄSTSERVICE</text>
		<text x="150" y="475" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="700" font-size="10" fill="#666">RESTAURANG FÖR UTSTÄLLARE</text>

		<!-- Café 2 (höger hall, mittenhöger) -->
		<rect x="800" y="340" width="160" height="100" fill="#1a1a1a" rx="2"/>
		<text x="880" y="395" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="800" font-size="18" fill="#E6741E" letter-spacing="0.1em">CAFÉ</text>

		<!-- Scen 2 (rosa T-form, nedre höger) -->
		<rect x="810" y="450" width="100" height="80" fill="#E8407C" rx="2"/>
		<rect x="840" y="530" width="40" height="40" fill="#E8407C"/>
		<text x="860" y="495" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="800" font-size="18" fill="#fff" letter-spacing="0.1em">SCEN</text>

		<!-- Publikplatser (stolar) -->
		<rect x="750" y="580" width="220" height="140" fill="url(#sm-seats)" stroke="#888" stroke-width="0.5"/>

		<!-- Måttangivelser (5, 10, 15... längs östra och södra väggen) -->
		<?php
		$marks_x = array( 5 => 305, 10 => 350, 15 => 395, 20 => 440, 25 => 485, 30 => 530, 35 => 575, 40 => 620, 45 => 665 );
		foreach ( $marks_x as $label => $px ) :
			printf( '<text x="%d" y="98" text-anchor="middle" font-family="Mulish, sans-serif" font-size="9" fill="#888">%d</text>', $px, $label );
			printf( '<text x="%d" y="744" text-anchor="middle" font-family="Mulish, sans-serif" font-size="9" fill="#888">%d</text>', $px, $label );
		endforeach;
		$marks_y = array( 5 => 130, 10 => 175, 15 => 220, 20 => 265, 25 => 310, 30 => 355, 35 => 400, 40 => 445 );
		foreach ( $marks_y as $label => $py ) :
			printf( '<text x="990" y="%d" text-anchor="middle" font-family="Mulish, sans-serif" font-size="9" fill="#888">%d</text>', $py, $label );
		endforeach;
		?>

		<!-- BÅS -->
		<?php foreach ( sm_booths() as $b ) :
			$is_booked   = isset( $booked_set[ $b['id'] ] );
			$is_selected = isset( $selected_set[ $b['id'] ] );
			$fill        = $booth_fill( $b );
			$text_color  = $is_selected ? '#fff' : '#222';
			$opacity     = $is_booked ? '0.65' : '1';
			?>
			<g>
				<rect x="<?php echo (int) $b['x']; ?>" y="<?php echo (int) $b['y']; ?>" width="<?php echo (int) $b['w']; ?>" height="<?php echo (int) $b['h']; ?>" rx="2" fill="<?php echo esc_attr( $fill ); ?>" fill-opacity="<?php echo esc_attr( $opacity ); ?>" stroke="rgba(0,0,0,0.3)" stroke-width="0.7"/>
				<text x="<?php echo (float) ( $b['x'] + $b['w'] / 2 ); ?>" y="<?php echo (float) ( $b['y'] + $b['h'] / 2 + 3.5 ); ?>" text-anchor="middle" font-family="Sofia Sans, sans-serif" font-weight="700" font-size="10" fill="<?php echo esc_attr( $text_color ); ?>" opacity="<?php echo esc_attr( $opacity ); ?>"><?php echo esc_html( $b['id'] ); ?></text>
			</g>
		<?php endforeach; ?>

		<!-- MONTERSTORLEK-förklaring (nedre vänster, utanför entréhallen) -->
		<g transform="translate(40, 510)">
			<text x="0" y="0" font-family="Sofia Sans, sans-serif" font-weight="800" font-size="11" fill="#1a1a1a" letter-spacing="0.06em">MONTERSTORLEK:</text>
			<rect x="0" y="18" width="22" height="22" fill="<?php echo esc_attr( SM_BOOTH_COLORS['2x2'] ); ?>" rx="2" stroke="rgba(0,0,0,0.2)" stroke-width="0.5"/>
			<text x="32" y="35" font-family="Mulish, sans-serif" font-size="12" fill="#333">2×2 meter</text>
			<rect x="0" y="50" width="22" height="22" fill="<?php echo esc_attr( SM_BOOTH_COLORS['2x3'] ); ?>" rx="2" stroke="rgba(0,0,0,0.2)" stroke-width="0.5"/>
			<text x="32" y="67" font-family="Mulish, sans-serif" font-size="12" fill="#333">2×3 meter</text>
			<rect x="0" y="82" width="22" height="22" fill="<?php echo esc_attr( SM_BOOTH_COLORS['3x3'] ); ?>" rx="2" stroke="rgba(0,0,0,0.2)" stroke-width="0.5"/>
			<text x="32" y="99" font-family="Mulish, sans-serif" font-size="12" fill="#333">3×3 meter</text>
		</g>
	</svg>
</div>
