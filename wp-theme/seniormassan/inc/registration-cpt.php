<?php
/**
 * Custom Post Type "sm_registration" — utställaranmälningar.
 *
 * Spara, lista och redigera anmälningar via standard WP-admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sm_register_cpt() {
	register_post_type( 'sm_registration', array(
		'labels' => array(
			'name'               => 'Anmälningar',
			'singular_name'      => 'Anmälan',
			'menu_name'          => 'Anmälningar',
			'add_new'            => 'Lägg till',
			'add_new_item'       => 'Lägg till anmälan',
			'edit_item'          => 'Redigera anmälan',
			'view_item'          => 'Visa anmälan',
			'search_items'       => 'Sök anmälningar',
			'not_found'          => 'Inga anmälningar hittades.',
			'not_found_in_trash' => 'Inga anmälningar i papperskorgen.',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 26,
		'menu_icon'           => 'dashicons-clipboard',
		'capability_type'     => 'post',
		'supports'            => array( 'title' ),
		'has_archive'         => false,
		'exclude_from_search' => true,
	) );
}
add_action( 'init', 'sm_register_cpt' );

/**
 * Anpassade kolumner i listvyn.
 */
function sm_registration_columns( $columns ) {
	return array(
		'cb'           => $columns['cb'],
		'title'        => 'Företag',
		'sm_contact'   => 'Kontakt',
		'sm_booths'    => 'Montrar',
		'sm_total'     => 'Summa',
		'sm_status'    => 'Status',
		'date'         => 'Inkom',
	);
}
add_filter( 'manage_sm_registration_posts_columns', 'sm_registration_columns' );

function sm_registration_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'sm_contact':
			$name  = get_post_meta( $post_id, '_sm_contact_name', true );
			$email = get_post_meta( $post_id, '_sm_contact_email', true );
			$phone = get_post_meta( $post_id, '_sm_contact_phone', true );
			echo esc_html( $name );
			if ( $email ) {
				echo '<br><a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
			}
			if ( $phone ) {
				echo '<br>' . esc_html( $phone );
			}
			break;

		case 'sm_booths':
			$booths = get_post_meta( $post_id, '_sm_booths', true );
			if ( is_array( $booths ) && $booths ) {
				echo esc_html( implode( ', ', $booths ) );
			} else {
				echo '—';
			}
			break;

		case 'sm_total':
			$total = (int) get_post_meta( $post_id, '_sm_total', true );
			echo esc_html( number_format( $total, 0, ',', "\u{00A0}" ) ) . ' kr';
			break;

		case 'sm_status':
			$status = get_post_meta( $post_id, '_sm_status', true ) ?: 'pending';
			$labels = array(
				'pending'   => array( 'Ny',         '#fef3c7', '#92400e' ),
				'confirmed' => array( 'Bekräftad',  '#dcfce7', '#166534' ),
				'cancelled' => array( 'Avbokad',    '#fee2e2', '#991b1b' ),
			);
			$info = $labels[ $status ] ?? array( $status, '#eee', '#333' );
			printf(
				'<span style="background:%s;color:%s;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:600;">%s</span>',
				esc_attr( $info[1] ),
				esc_attr( $info[2] ),
				esc_html( $info[0] )
			);
			break;
	}
}
add_action( 'manage_sm_registration_posts_custom_column', 'sm_registration_column_content', 10, 2 );

/**
 * Meta box med alla anmälningsdata.
 */
function sm_registration_meta_boxes() {
	add_meta_box( 'sm_registration_details', 'Anmälningsdetaljer', 'sm_registration_meta_box_html', 'sm_registration', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'sm_registration_meta_boxes' );

function sm_registration_meta_box_html( $post ) {
	wp_nonce_field( 'sm_save_registration', 'sm_registration_nonce' );

	$fields = array(
		'_sm_company'         => 'Företagsnamn',
		'_sm_orgnr'           => 'Organisationsnummer',
		'_sm_invoice_address' => 'Fakturaadress',
		'_sm_invoice_email'   => 'Faktura-e-post',
		'_sm_contact_name'    => 'Kontaktperson',
		'_sm_contact_email'   => 'E-post kontaktperson',
		'_sm_contact_phone'   => 'Telefon',
		'_sm_website'         => 'Webbplats',
		'_sm_description'     => 'Beskrivning',
	);
	$status   = get_post_meta( $post->ID, '_sm_status', true ) ?: 'pending';
	$booths   = get_post_meta( $post->ID, '_sm_booths', true ) ?: array();
	$addons   = get_post_meta( $post->ID, '_sm_addons', true ) ?: array();
	$variants = get_post_meta( $post->ID, '_sm_addon_variants', true ) ?: array();
	$reqs     = get_post_meta( $post->ID, '_sm_special_requests', true );
	$total    = (int) get_post_meta( $post->ID, '_sm_total', true );
	$is_for   = get_post_meta( $post->ID, '_sm_is_forening', true );

	echo '<style>.sm-meta-grid{display:grid;grid-template-columns:200px 1fr;gap:12px 24px;align-items:start;margin-bottom:16px;}.sm-meta-grid label{font-weight:600;padding-top:6px;}.sm-meta-grid input,.sm-meta-grid textarea,.sm-meta-grid select{width:100%;padding:6px 10px;}</style>';

	echo '<div class="sm-meta-grid">';
	echo '<label>Status</label><select name="sm_status">';
	foreach ( array( 'pending' => 'Ny', 'confirmed' => 'Bekräftad', 'cancelled' => 'Avbokad' ) as $k => $v ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $k ), selected( $status, $k, false ), esc_html( $v ) );
	}
	echo '</select>';

	foreach ( $fields as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		$name  = 'sm_' . substr( $key, 4 );
		echo '<label>' . esc_html( $label ) . '</label>';
		if ( in_array( $key, array( '_sm_invoice_address', '_sm_description' ), true ) ) {
			echo '<textarea name="' . esc_attr( $name ) . '" rows="3">' . esc_textarea( $value ) . '</textarea>';
		} else {
			echo '<input type="text" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '">';
		}
	}

	echo '<label>Förening</label><label><input type="checkbox" name="sm_is_forening" value="1" ' . checked( $is_for, '1', false ) . '> Anmälan från ideell förening (N-monter / inkl. moms)</label>';

	$booked_other     = sm_booked_booth_ids( $post->ID );
	$booked_other_set = array_flip( $booked_other );
	$booths_by_sec    = array();
	foreach ( sm_booths() as $b ) {
		$booths_by_sec[ substr( $b['id'], 0, 1 ) ][] = $b;
	}
	ksort( $booths_by_sec );
	echo '<label>Montrar</label><div>';
	echo '<div style="max-height:240px;overflow:auto;border:1px solid #dcdcde;border-radius:4px;padding:10px;">';
	foreach ( $booths_by_sec as $sec => $list ) {
		echo '<div style="font-weight:600;margin:6px 0 4px;">Sektion ' . esc_html( $sec ) . ( $sec === 'N' ? ' (förening)' : '' ) . '</div>';
		echo '<div style="display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:6px;">';
		foreach ( $list as $b ) {
			$checked    = in_array( $b['id'], (array) $booths, true ) ? 'checked' : '';
			$taken_else = isset( $booked_other_set[ $b['id'] ] );
			printf(
				'<label style="font-size:12px;%s"%s><input type="checkbox" name="sm_admin_booths[]" value="%s" %s> %s</label>',
				$taken_else ? 'color:#b32d2e;' : '',
				$taken_else ? ' title="Bokad av en annan anmälan"' : '',
				esc_attr( $b['id'] ),
				$checked,
				esc_html( $b['id'] )
			);
		}
		echo '</div>';
	}
	echo '</div>';
	echo '<p class="description">Bocka i/ur montrar. Röd = redan bokad av en annan anmälan. Totalsumman räknas om när du sparar.</p>';
	echo '</div>';

	$stage_slot  = get_post_meta( $post->ID, '_sm_stage_slot', true );
	$taken_other = sm_taken_stage_slots( $post->ID ); // exkludera den här posten själv
	echo '<label for="sm_stage_slot">Scenpass</label>';
	echo '<div><select id="sm_stage_slot" name="sm_stage_slot" style="max-width:240px;">';
	echo '<option value="">— Inget scenpass bokat</option>';
	foreach ( sm_stage_slots() as $slot ) {
		$is_taken_by_other = in_array( $slot, $taken_other, true );
		$disabled          = ( $is_taken_by_other && $slot !== $stage_slot ) ? ' disabled' : '';
		$label             = $slot . ( $is_taken_by_other && $slot !== $stage_slot ? ' (upptagen av annan anmälan)' : '' );
		printf(
			'<option value="%s"%s%s>%s</option>',
			esc_attr( $slot ),
			selected( $stage_slot, $slot, false ),
			$disabled,
			esc_html( $label )
		);
	}
	echo '</select>';
	echo '<p class="description" style="margin-top:6px;">Välj "— Inget scenpass bokat" för att frigöra ett tidigare bokat pass.</p>';
	echo '</div>';

	echo '<label>Tillval</label><div>';
	echo '<table style="width:100%;border-collapse:collapse;max-width:560px;">';
	foreach ( sm_addons() as $a ) {
		$id     = $a['id'];
		$qty    = isset( $addons[ $id ] ) ? (int) $addons[ $id ] : 0;
		$scales = ! empty( $a['scales_with_booths'] );
		$unit   = $a['price_label'] ?? 'kr/st';
		echo '<tr style="border-bottom:1px solid #f0f0f1;">';
		printf(
			'<td style="padding:4px 8px 4px 0;">%s <span style="color:#646970;">(%s %s)</span></td>',
			esc_html( $a['name'] ),
			esc_html( $a['price'] ),
			esc_html( $unit )
		);
		if ( ! empty( $a['variants'] ) ) {
			$cur_v = $variants[ $id ] ?? '';
			echo '<td style="padding:4px 8px;"><select name="sm_admin_addon_variants[' . esc_attr( $id ) . ']" style="min-width:110px;"><option value="">— ingen —</option>';
			foreach ( $a['variants'] as $v ) {
				printf( '<option value="%s"%s>%s</option>', esc_attr( $v ), selected( $cur_v, $v, false ), esc_html( $v ) );
			}
			echo '</select></td>';
		} else {
			echo '<td></td>';
		}
		if ( $scales ) {
			printf( '<td style="padding:4px 8px;color:#646970;font-size:12px;">%s m² — räknas från montrarna</td>', (int) $qty );
		} else {
			printf( '<td style="padding:4px 8px;"><input type="number" min="0" max="99" name="sm_admin_addons[%s]" value="%d" style="width:64px;"></td>', esc_attr( $id ), $qty );
		}
		echo '</tr>';
	}
	echo '</table>';
	echo '<p class="description">Ändra antal/variant. Montermattan räknas automatiskt per m² från valda montrar när en färg är vald.</p>';
	echo '</div>';

	echo '<label>Önskemål</label><div style="white-space:pre-wrap;">' . esc_html( $reqs ?: '—' ) . '</div>';
	echo '<label>Totalsumma</label><div><strong>' . esc_html( number_format( $total, 0, ',', "\u{00A0}" ) ) . ' kr</strong> ' . ( $is_for ? 'inkl. moms' : 'exkl. moms' ) . ' <span class="description">(räknas om vid sparning)</span></div>';

	if ( function_exists( 'sm_booking_edit_url' ) ) {
		$edit_link = sm_booking_edit_url( $post->ID );
		echo '<label>Ändra-länk</label><div><input type="text" readonly value="' . esc_attr( $edit_link ) . '" style="width:100%;" onclick="this.select();"><p class="description">Personlig länk där utställaren själv kan ändra sin bokning (skickas i bekräftelsemejlet).</p></div>';
	}
	echo '</div>';
}

function sm_save_registration_meta( $post_id ) {
	if ( ! isset( $_POST['sm_registration_nonce'] ) || ! wp_verify_nonce( $_POST['sm_registration_nonce'], 'sm_save_registration' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$text_keys = array( 'company', 'orgnr', 'invoice_address', 'invoice_email', 'contact_name', 'contact_email', 'contact_phone', 'website', 'description', 'status' );
	foreach ( $text_keys as $k ) {
		if ( isset( $_POST[ 'sm_' . $k ] ) ) {
			update_post_meta( $post_id, '_sm_' . $k, sanitize_textarea_field( wp_unslash( $_POST[ 'sm_' . $k ] ) ) );
		}
	}
	$is_for = isset( $_POST['sm_is_forening'] );
	update_post_meta( $post_id, '_sm_is_forening', $is_for ? '1' : '' );

	// Montrar
	$new_booths = ( isset( $_POST['sm_admin_booths'] ) && is_array( $_POST['sm_admin_booths'] ) )
		? array_values( array_map( 'sanitize_text_field', wp_unslash( $_POST['sm_admin_booths'] ) ) )
		: array();
	update_post_meta( $post_id, '_sm_booths', $new_booths );

	// Tillval + varianter (montermatta räknas per m² från montrarna)
	$new_addons   = array();
	$new_variants = array();
	foreach ( sm_addons() as $a ) {
		$id      = $a['id'];
		$variant = '';
		if ( ! empty( $a['variants'] ) && isset( $_POST['sm_admin_addon_variants'][ $id ] ) ) {
			$chosen = sanitize_text_field( wp_unslash( $_POST['sm_admin_addon_variants'][ $id ] ) );
			if ( in_array( $chosen, $a['variants'], true ) ) {
				$variant = $chosen;
			}
		}
		if ( ! empty( $a['scales_with_booths'] ) ) {
			$qty = $variant ? sm_booths_total_sqm( $new_booths ) : 0;
		} else {
			$qty = isset( $_POST['sm_admin_addons'][ $id ] ) ? max( 0, (int) $_POST['sm_admin_addons'][ $id ] ) : 0;
		}
		if ( $qty > 0 ) {
			$new_addons[ $id ] = $qty;
			if ( $variant ) {
				$new_variants[ $id ] = $variant;
			}
		}
	}
	update_post_meta( $post_id, '_sm_addons', $new_addons );
	update_post_meta( $post_id, '_sm_addon_variants', $new_variants );

	// Räkna om totalsumman
	update_post_meta( $post_id, '_sm_total', sm_calculate_total( $new_booths, $new_addons, $is_for ) );

	// Scenpass: tomt värde frigör en tidigare bokad tid; annars måste tiden vara
	// giltig och inte upptagen av en annan anmälan.
	if ( isset( $_POST['sm_stage_slot'] ) ) {
		$incoming = sanitize_text_field( wp_unslash( $_POST['sm_stage_slot'] ) );
		if ( $incoming === '' ) {
			update_post_meta( $post_id, '_sm_stage_slot', '' );
		} elseif ( in_array( $incoming, sm_stage_slots(), true ) ) {
			$taken_other = sm_taken_stage_slots( $post_id );
			if ( ! in_array( $incoming, $taken_other, true ) ) {
				update_post_meta( $post_id, '_sm_stage_slot', $incoming );
			}
		}
	}
}
add_action( 'save_post_sm_registration', 'sm_save_registration_meta' );

/**
 * Admin-sida för att manuellt blockera/avblockera enskilda montrar.
 */
function sm_register_blocked_booths_page() {
	add_submenu_page(
		'edit.php?post_type=sm_registration',
		'Blockera montrar',
		'Blockera montrar',
		'manage_options',
		'sm-blocked-booths',
		'sm_blocked_booths_page'
	);
}
add_action( 'admin_menu', 'sm_register_blocked_booths_page' );

function sm_blocked_booths_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['sm_blocked_booths_save'] ) && check_admin_referer( 'sm_blocked_booths' ) ) {
		$ids = isset( $_POST['sm_blocked'] ) && is_array( $_POST['sm_blocked'] )
			? array_map( 'sanitize_text_field', wp_unslash( $_POST['sm_blocked'] ) )
			: array();
		update_option( 'sm_blocked_booths', $ids );
		echo '<div class="notice notice-success"><p>Sparat.</p></div>';
	}

	$blocked = (array) get_option( 'sm_blocked_booths', array() );
	$by_section = array();
	foreach ( sm_booths() as $b ) {
		$section = substr( $b['id'], 0, 1 );
		$by_section[ $section ][] = $b;
	}
	ksort( $by_section );

	echo '<div class="wrap"><h1>Blockera montrar</h1>';
	echo '<p>Bocka i de montrar som ska markeras som upptagna oavsett anmälningar (t.ex. internt bokade eller ej tillgängliga). Dessa visas som "BOKAD" i anmälningsformuläret.</p>';
	echo '<form method="post">';
	wp_nonce_field( 'sm_blocked_booths' );
	foreach ( $by_section as $section => $booths ) {
		echo '<h2>' . esc_html( $section ) . '</h2><div style="display:grid;grid-template-columns:repeat(6,1fr);gap:8px;max-width:720px;">';
		foreach ( $booths as $b ) {
			$checked = in_array( $b['id'], $blocked, true ) ? 'checked' : '';
			printf(
				'<label style="display:flex;align-items:center;gap:6px;"><input type="checkbox" name="sm_blocked[]" value="%s" %s> %s <span style="opacity:0.6;font-size:12px;">(%s)</span></label>',
				esc_attr( $b['id'] ),
				$checked,
				esc_html( $b['id'] ),
				esc_html( $b['size'] )
			);
		}
		echo '</div>';
	}
	echo '<p style="margin-top:24px;"><input type="submit" name="sm_blocked_booths_save" class="button button-primary" value="Spara"></p>';
	echo '</form></div>';
}
