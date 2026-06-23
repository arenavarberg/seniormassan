<?php
/**
 * Båsdata — översättning av design_handoff_seniormassan/booth-map.jsx.
 *
 * BOOTHS-arrayen är källan till sanningen för:
 *   - vilka montrar som finns
 *   - deras storlek (2x2, 2x3, 3x3)
 *   - deras koordinater på SVG-kartan (viewBox 1000×780)
 *
 * När hallplanen ändras inför ett kommande år: redigera arrayen här.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SM_FORENING_PRICE = 1888; // 2360 inkl. 25 % moms — default; överstyrs av sm_pricing-option
const SM_BOOTH_PRICES = array(
	'2x2' => 3820,
	'2x3' => 5730,
	'3x3' => 8845,
);
const SM_BOOTH_COLORS = array(
	'2x2'      => '#F2DC6E',
	'2x3'      => '#7DC1D9',
	'3x3'      => '#A8D38E',
	'forening' => '#E8A6C4',
);
const SM_REGISTRATION_FEE = 800;

/**
 * Sammanslagna prisinställningar (admin-overrides + defaults).
 *
 * @return array{booth_2x2:int,booth_2x3:int,booth_3x3:int,forening:int,registration_fee:int}
 */
function sm_get_pricing() {
	$defaults = array(
		'booth_2x2'        => SM_BOOTH_PRICES['2x2'],
		'booth_2x3'        => SM_BOOTH_PRICES['2x3'],
		'booth_3x3'        => SM_BOOTH_PRICES['3x3'],
		'forening'         => SM_FORENING_PRICE,
		'registration_fee' => SM_REGISTRATION_FEE,
	);
	$saved = get_option( 'sm_pricing', array() );
	return is_array( $saved ) ? array_merge( $defaults, array_intersect_key( $saved, $defaults ) ) : $defaults;
}

function sm_get_booth_price_for_size( $size ) {
	$p = sm_get_pricing();
	return $p[ 'booth_' . $size ] ?? 0;
}

function sm_get_forening_price() {
	return sm_get_pricing()['forening'];
}

function sm_get_registration_fee() {
	return sm_get_pricing()['registration_fee'];
}

function sm_get_addon_price( $id, $fallback = 0 ) {
	$prices = get_option( 'sm_addon_prices', array() );
	if ( is_array( $prices ) && isset( $prices[ $id ] ) ) {
		return (int) $prices[ $id ];
	}
	return (int) $fallback;
}

function sm_booths() {
	return array(
		// N (entréhallen) — föreningsmontrar
		array( 'id' => 'N1',  'size' => '2x2', 'x' => 70,  'y' => 110, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'N2',  'size' => '2x2', 'x' => 70,  'y' => 152, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'N3',  'size' => '2x2', 'x' => 70,  'y' => 194, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'N4',  'size' => '2x2', 'x' => 70,  'y' => 236, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'N5',  'size' => '2x2', 'x' => 70,  'y' => 322, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'N6',  'size' => '2x2', 'x' => 70,  'y' => 364, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'N7',  'size' => '2x2', 'x' => 130, 'y' => 110, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'N8',  'size' => '2x2', 'x' => 130, 'y' => 152, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'N9',  'size' => '2x2', 'x' => 130, 'y' => 252, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'N10', 'size' => '2x2', 'x' => 130, 'y' => 294, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'N11', 'size' => '2x2', 'x' => 130, 'y' => 336, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'N12', 'size' => '2x2', 'x' => 130, 'y' => 378, 'w' => 38, 'h' => 38 ),

		// A
		array( 'id' => 'A1', 'size' => '2x2', 'x' => 320, 'y' => 178, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'A2', 'size' => '2x2', 'x' => 320, 'y' => 136, 'w' => 38, 'h' => 38 ),

		// B
		array( 'id' => 'B1', 'size' => '2x3', 'x' => 380, 'y' => 100, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'B2', 'size' => '2x3', 'x' => 438, 'y' => 100, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'B3', 'size' => '2x3', 'x' => 496, 'y' => 100, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'B4', 'size' => '2x3', 'x' => 554, 'y' => 100, 'w' => 56, 'h' => 38 ),

		// C
		array( 'id' => 'C1', 'size' => '3x3', 'x' => 640, 'y' => 100, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'C2', 'size' => '3x3', 'x' => 698, 'y' => 100, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'C3', 'size' => '3x3', 'x' => 756, 'y' => 100, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'C4', 'size' => '3x3', 'x' => 814, 'y' => 100, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'C5', 'size' => '3x3', 'x' => 872, 'y' => 100, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'C6', 'size' => '3x3', 'x' => 930, 'y' => 142, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'C7', 'size' => '3x3', 'x' => 930, 'y' => 200, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'C8', 'size' => '3x3', 'x' => 930, 'y' => 258, 'w' => 38, 'h' => 56 ),

		// D
		array( 'id' => 'D1', 'size' => '2x3', 'x' => 696, 'y' => 170, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'D2', 'size' => '2x3', 'x' => 754, 'y' => 170, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'D3', 'size' => '2x3', 'x' => 812, 'y' => 170, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'D4', 'size' => '2x3', 'x' => 870, 'y' => 170, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'D5', 'size' => '2x3', 'x' => 696, 'y' => 210, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'D6', 'size' => '2x3', 'x' => 754, 'y' => 210, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'D7', 'size' => '2x3', 'x' => 812, 'y' => 210, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'D8', 'size' => '2x3', 'x' => 870, 'y' => 210, 'w' => 56, 'h' => 38 ),

		// E
		array( 'id' => 'E1', 'size' => '2x3', 'x' => 380, 'y' => 170, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'E2', 'size' => '2x3', 'x' => 438, 'y' => 170, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'E3', 'size' => '2x3', 'x' => 496, 'y' => 170, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'E4', 'size' => '2x3', 'x' => 554, 'y' => 170, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'E5', 'size' => '2x3', 'x' => 380, 'y' => 210, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'E6', 'size' => '2x3', 'x' => 438, 'y' => 210, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'E7', 'size' => '2x3', 'x' => 496, 'y' => 210, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'E8', 'size' => '2x3', 'x' => 554, 'y' => 210, 'w' => 56, 'h' => 38 ),

		// F
		array( 'id' => 'F1', 'size' => '2x3', 'x' => 380, 'y' => 250, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'F2', 'size' => '2x3', 'x' => 438, 'y' => 250, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'F3', 'size' => '2x3', 'x' => 496, 'y' => 250, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'F4', 'size' => '2x3', 'x' => 554, 'y' => 250, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'F5', 'size' => '2x3', 'x' => 380, 'y' => 290, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'F6', 'size' => '2x3', 'x' => 438, 'y' => 290, 'w' => 56, 'h' => 38 ),

		// I
		array( 'id' => 'I1', 'size' => '2x3', 'x' => 320, 'y' => 282, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'I2', 'size' => '2x3', 'x' => 320, 'y' => 340, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'I3', 'size' => '2x3', 'x' => 320, 'y' => 398, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'I4', 'size' => '2x3', 'x' => 360, 'y' => 340, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'I5', 'size' => '2x3', 'x' => 360, 'y' => 398, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'I6', 'size' => '2x3', 'x' => 360, 'y' => 282, 'w' => 38, 'h' => 56 ),

		// G
		array( 'id' => 'G1', 'size' => '3x3', 'x' => 580, 'y' => 282, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'G2', 'size' => '3x3', 'x' => 638, 'y' => 282, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'G3', 'size' => '3x3', 'x' => 696, 'y' => 282, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'G4', 'size' => '3x3', 'x' => 754, 'y' => 282, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'G5', 'size' => '3x3', 'x' => 580, 'y' => 322, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'G6', 'size' => '3x3', 'x' => 638, 'y' => 322, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'G7', 'size' => '3x3', 'x' => 696, 'y' => 322, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'G8', 'size' => '3x3', 'x' => 754, 'y' => 322, 'w' => 56, 'h' => 38 ),

		// H
		array( 'id' => 'H1', 'size' => '3x3', 'x' => 470, 'y' => 380, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'H2', 'size' => '3x3', 'x' => 528, 'y' => 380, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'H3', 'size' => '3x3', 'x' => 586, 'y' => 380, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'H4', 'size' => '3x3', 'x' => 470, 'y' => 420, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'H5', 'size' => '3x3', 'x' => 528, 'y' => 420, 'w' => 56, 'h' => 38 ),
		array( 'id' => 'H6', 'size' => '3x3', 'x' => 586, 'y' => 420, 'w' => 56, 'h' => 38 ),

		// J
		array( 'id' => 'J1', 'size' => '2x3', 'x' => 410, 'y' => 488, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'J2', 'size' => '2x3', 'x' => 410, 'y' => 546, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'J3', 'size' => '2x3', 'x' => 410, 'y' => 604, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'J4', 'size' => '2x3', 'x' => 450, 'y' => 604, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'J5', 'size' => '2x3', 'x' => 450, 'y' => 546, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'J6', 'size' => '2x3', 'x' => 450, 'y' => 488, 'w' => 38, 'h' => 56 ),

		// K
		array( 'id' => 'K1', 'size' => '2x3', 'x' => 540, 'y' => 488, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'K2', 'size' => '2x3', 'x' => 540, 'y' => 546, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'K3', 'size' => '2x3', 'x' => 540, 'y' => 604, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'K4', 'size' => '2x3', 'x' => 580, 'y' => 604, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'K5', 'size' => '2x3', 'x' => 580, 'y' => 546, 'w' => 38, 'h' => 56 ),
		array( 'id' => 'K6', 'size' => '2x3', 'x' => 580, 'y' => 488, 'w' => 38, 'h' => 56 ),

		// L
		array( 'id' => 'L1', 'size' => '2x2', 'x' => 720, 'y' => 488, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'L2', 'size' => '2x2', 'x' => 720, 'y' => 530, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'L3', 'size' => '2x2', 'x' => 720, 'y' => 572, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'L4', 'size' => '2x2', 'x' => 720, 'y' => 614, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'L5', 'size' => '2x2', 'x' => 720, 'y' => 446, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'L6', 'size' => '2x2', 'x' => 720, 'y' => 404, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'L7', 'size' => '2x2', 'x' => 720, 'y' => 656, 'w' => 38, 'h' => 38 ),

		// M
		array( 'id' => 'M1',  'size' => '2x2', 'x' => 280, 'y' => 580, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'M2',  'size' => '2x2', 'x' => 280, 'y' => 622, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'M3',  'size' => '2x2', 'x' => 280, 'y' => 664, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'M4',  'size' => '2x2', 'x' => 280, 'y' => 706, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'M5',  'size' => '2x2', 'x' => 340, 'y' => 706, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'M6',  'size' => '2x2', 'x' => 382, 'y' => 706, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'M7',  'size' => '2x2', 'x' => 424, 'y' => 706, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'M8',  'size' => '2x2', 'x' => 466, 'y' => 706, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'M9',  'size' => '2x2', 'x' => 508, 'y' => 706, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'M10', 'size' => '2x2', 'x' => 550, 'y' => 706, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'M11', 'size' => '2x2', 'x' => 592, 'y' => 706, 'w' => 38, 'h' => 38 ),
		array( 'id' => 'M12', 'size' => '2x2', 'x' => 634, 'y' => 706, 'w' => 38, 'h' => 38 ),
	);
}

function sm_is_forening_booth( $id ) {
	return is_string( $id ) && strpos( $id, 'N' ) === 0;
}

/**
 * Yta i kvadratmeter för en monterstorlek (t.ex. '2x3' → 6).
 */
function sm_booth_sqm( $size ) {
	$map = array( '2x2' => 4, '2x3' => 6, '3x3' => 9 );
	return $map[ $size ] ?? 0;
}

/**
 * Summan av kvadratmeter för en lista monter-ID.
 */
function sm_booths_total_sqm( $ids ) {
	$sqm = 0;
	foreach ( (array) $ids as $id ) {
		$b = sm_booth( $id );
		if ( $b ) {
			$sqm += sm_booth_sqm( $b['size'] );
		}
	}
	return $sqm;
}

function sm_booth_price( $id ) {
	if ( sm_is_forening_booth( $id ) ) {
		return sm_get_forening_price();
	}
	foreach ( sm_booths() as $b ) {
		if ( $b['id'] === $id ) {
			return sm_get_booth_price_for_size( $b['size'] );
		}
	}
	return 0;
}

/**
 * Beräknar totalsumman för en bokning (montrar + tillval + reg.avgift, ev. moms).
 * Delad av formulärhandlern och admin-redigeringen så beräkningen är identisk.
 */
function sm_calculate_total( $booths, $addons, $is_forening ) {
	$total = 0;
	foreach ( (array) $booths as $bid ) {
		$total += sm_booth_price( $bid );
	}
	foreach ( (array) $addons as $id => $qty ) {
		$a = sm_addon( $id );
		if ( $a ) {
			$total += (int) $a['price'] * (int) $qty;
		}
	}
	if ( ! empty( $booths ) ) {
		$total += sm_get_registration_fee();
	}
	if ( $is_forening ) {
		$total = (int) round( $total * 1.25 );
	}
	return (int) $total;
}

function sm_booth( $id ) {
	foreach ( sm_booths() as $b ) {
		if ( $b['id'] === $id ) {
			return $b;
		}
	}
	return null;
}

/**
 * Hämtar alla montrar som är "bokade" — kombination av:
 *   - alla montrar i alla anmälningar (CPT) som inte är 'cancelled'
 *   - manuellt blockerade montrar via option 'sm_blocked_booths' (admin)
 */
function sm_booked_booth_ids( $exclude_post_id = 0 ) {
	$booked = array();

	$query = new WP_Query( array(
		'post_type'      => 'sm_registration',
		'post_status'    => array( 'publish', 'draft', 'pending' ),
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_query'     => array(
			array(
				'key'     => '_sm_status',
				'value'   => 'cancelled',
				'compare' => '!=',
			),
		),
	) );
	foreach ( $query->posts as $post_id ) {
		if ( $post_id === (int) $exclude_post_id ) {
			continue;
		}
		$ids = get_post_meta( $post_id, '_sm_booths', true );
		if ( is_array( $ids ) ) {
			$booked = array_merge( $booked, $ids );
		}
	}

	$blocked = get_option( 'sm_blocked_booths', array() );
	if ( is_array( $blocked ) ) {
		$booked = array_merge( $booked, $blocked );
	}

	return array_values( array_unique( $booked ) );
}

/**
 * Tillvalsprodukter — speglar designens addons.
 *
 * Registreringsavgift (800 kr) är INTE ett tillval — den läggs på automatiskt
 * när minst en monter är vald.
 *
 * 'variants' = lista av färgval/varianter att välja mellan.
 *
 * Priserna här är defaults; admin kan överstyra via Anmälningar → Priser
 * (sparas i option 'sm_addon_prices').
 */
function sm_addons_raw() {
	return array(
		array( 'id' => 'matta',     'name' => 'Montermatta',                'price' => 110, 'cat' => 'Mattor & golv', 'variants' => array( 'Blå', 'Röd', 'Grön', 'Grå', 'Svart', 'Orange' ), 'scales_with_booths' => true, 'scales_by' => 'sqm', 'price_label' => 'kr/m²' ),
		array( 'id' => 'stol',      'name' => 'Stol',                       'price' => 60,  'cat' => 'Möbler' ),
		array( 'id' => 'barstol',   'name' => 'Barstol',                    'price' => 130, 'cat' => 'Möbler' ),
		array( 'id' => 'bord180',   'name' => 'Bord 180×80 cm',             'price' => 130, 'cat' => 'Möbler' ),
		array( 'id' => 'bord120',   'name' => 'Bord 120×50 cm',             'price' => 110, 'cat' => 'Möbler' ),
		array( 'id' => 'rullbox',   'name' => 'Rullbox',                    'price' => 657, 'cat' => 'Möbler' ),
		array( 'id' => 'barbord',   'name' => 'Barbord',                    'price' => 130, 'cat' => 'Möbler' ),
		array( 'id' => 'strumpa',   'name' => 'Bordsstrumpa till barbord',  'price' => 100, 'cat' => 'Möbler', 'variants' => array( 'Vit', 'Svart' ), 'requires' => 'barbord' ),
		array( 'id' => 'belysning', 'name' => 'Belysning monter',           'price' => 110, 'cat' => 'El & belysning' ),
		array( 'id' => 'el16',      'name' => 'Anslutning 16 amp 3-fas',    'price' => 463, 'cat' => 'El & belysning' ),
		array( 'id' => 'grenkontakt','name' => 'Grenkontakt',                'price' => 110, 'cat' => 'El & belysning' ),
		array( 'id' => 'fika_fm',   'name' => 'Förmiddagsfika',             'price' => 95,  'cat' => 'Mat & fika' ),
		array( 'id' => 'lunch',     'name' => 'Lunch utställare',           'price' => 180, 'cat' => 'Mat & fika' ),
		array( 'id' => 'fika_em',   'name' => 'Eftermiddagsfika',           'price' => 95,  'cat' => 'Mat & fika' ),
	);
}

/**
 * Samma som sm_addons_raw(), men varje 'price' överstyrs av admin-värdet
 * från option 'sm_addon_prices' om sådant finns.
 */
function sm_addons() {
	$out = array();
	foreach ( sm_addons_raw() as $a ) {
		$a['price'] = sm_get_addon_price( $a['id'], $a['price'] );
		$out[]      = $a;
	}
	return $out;
}

function sm_addon( $id ) {
	foreach ( sm_addons() as $a ) {
		if ( $a['id'] === $id ) {
			return $a;
		}
	}
	return null;
}

/**
 * Hämtar ikon-URL för ett tillval (eller en specifik variant).
 * Sparade via admin-sidan "Tillval-ikoner".
 */
function sm_addon_icon( $addon_id, $variant = null ) {
	$icons = get_option( 'sm_addon_icons', array() );
	if ( $variant ) {
		return $icons[ $addon_id ]['variants'][ $variant ] ?? '';
	}
	return $icons[ $addon_id ]['main'] ?? '';
}

/**
 * Tider för utställarscenen (15-min slots).
 */
function sm_stage_slots() {
	return array(
		'11:00', '11:30', '12:00', '12:30',
		'13:00', '13:30', '14:00', '14:30',
		'15:00', '15:30', '16:00',
	);
}

/**
 * Tider som redan är upptagna — härleds från CPT-anmälningar.
 */
function sm_taken_stage_slots( $exclude_post_id = 0 ) {
	$query = new WP_Query( array(
		'post_type'      => 'sm_registration',
		'post_status'    => array( 'publish', 'draft', 'pending' ),
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_query'     => array(
			array(
				'key'     => '_sm_status',
				'value'   => 'cancelled',
				'compare' => '!=',
			),
		),
	) );
	$taken = array();
	foreach ( $query->posts as $post_id ) {
		if ( $post_id === (int) $exclude_post_id ) {
			continue;
		}
		$slot = get_post_meta( $post_id, '_sm_stage_slot', true );
		if ( $slot ) {
			$taken[] = $slot;
		}
	}
	return array_values( array_unique( $taken ) );
}
