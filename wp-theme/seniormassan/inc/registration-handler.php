<?php
/**
 * Formulärhantering — tar emot POST från anmälningsformuläret,
 * validerar, skapar CPT-post, skickar mejl.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sm_handle_registration() {
	if ( ! isset( $_POST['sm_register_nonce'] ) || ! wp_verify_nonce( $_POST['sm_register_nonce'], 'sm_register' ) ) {
		wp_die( 'Säkerhetskontrollen misslyckades. Gå tillbaka och försök igen.' );
	}

	$errors = array();
	$post   = wp_unslash( $_POST );

	// Redigeringsläge: giltig boknings-ID + token → uppdatera befintlig post.
	$edit_id    = isset( $post['sm_booking_id'] ) ? (int) $post['sm_booking_id'] : 0;
	$edit_token = isset( $post['sm_edit_token'] ) ? sanitize_text_field( $post['sm_edit_token'] ) : '';
	$editing    = sm_valid_edit_request( $edit_id, $edit_token );
	$self_id    = $editing ? $edit_id : 0;

	// Företagsfält
	$company       = sanitize_text_field( $post['sm_company'] ?? '' );
	$orgnr         = sanitize_text_field( $post['sm_orgnr'] ?? '' );
	$invoice_addr  = sanitize_textarea_field( $post['sm_invoice_address'] ?? '' );
	$invoice_email = sanitize_email( $post['sm_invoice_email'] ?? '' );
	$contact_name  = sanitize_text_field( $post['sm_contact_name'] ?? '' );
	$contact_email = sanitize_email( $post['sm_contact_email'] ?? '' );
	$contact_phone = sanitize_text_field( $post['sm_contact_phone'] ?? '' );
	$no_website    = ! empty( $post['sm_no_website'] );
	$website       = $no_website ? '' : esc_url_raw( $post['sm_website'] ?? '' );
	$description   = sanitize_textarea_field( $post['sm_description'] ?? '' );
	$booths        = isset( $post['sm_booths'] ) && is_array( $post['sm_booths'] )
		? array_map( 'sanitize_text_field', $post['sm_booths'] )
		: array();
	$stage_slot    = sanitize_text_field( $post['sm_stage_slot'] ?? '' );
	$reqs          = sanitize_textarea_field( $post['sm_special_requests'] ?? '' );
	$is_forening   = ! empty( $post['sm_is_forening'] );
	$accept_terms  = ! empty( $post['sm_accept_terms'] );
	$accept_gdpr   = ! empty( $post['sm_accept_gdpr'] );

	$addon_input    = isset( $post['sm_addons'] ) && is_array( $post['sm_addons'] ) ? $post['sm_addons'] : array();
	$variants_input = isset( $post['sm_addon_variants'] ) && is_array( $post['sm_addon_variants'] ) ? $post['sm_addon_variants'] : array();
	$addons         = array();
	$variants       = array();
	foreach ( sm_addons() as $a ) {
		$variant = '';
		if ( ! empty( $a['variants'] ) && isset( $variants_input[ $a['id'] ] ) ) {
			$chosen = sanitize_text_field( $variants_input[ $a['id'] ] );
			if ( in_array( $chosen, $a['variants'], true ) ) {
				$variant = $chosen;
			}
		}

		if ( ! empty( $a['scales_with_booths'] ) ) {
			// Skalar med yta (m²) — kvantitet sätts av servern, inte av användaren
			$qty = $variant ? sm_booths_total_sqm( $booths ) : 0;
		} else {
			$qty = isset( $addon_input[ $a['id'] ] ) ? max( 0, (int) $addon_input[ $a['id'] ] ) : 0;
		}

		if ( $qty > 0 ) {
			$addons[ $a['id'] ] = $qty;
			if ( $variant ) {
				$variants[ $a['id'] ] = $variant;
			}
		}
	}

	// Validering
	if ( ! $company ) {
		$errors[] = 'Företagsnamn krävs.';
	}
	if ( ! $orgnr ) {
		$errors[] = 'Organisationsnummer krävs.';
	}
	if ( ! $contact_name ) {
		$errors[] = 'Kontaktperson krävs.';
	}
	if ( ! $contact_email || ! is_email( $contact_email ) ) {
		$errors[] = 'Giltig e-postadress till kontaktperson krävs.';
	}
	if ( ! $contact_phone ) {
		$errors[] = 'Telefonnummer krävs.';
	}
	if ( ! $no_website && ! $website ) {
		$errors[] = 'Ange webbplats eller bocka i "Vi har ingen webbplats".';
	}
	if ( empty( $booths ) ) {
		$errors[] = 'Välj minst en monter.';
	}
	// Verifiera att scenpasset inte redan är taget
	if ( $stage_slot ) {
		$taken_slots = sm_taken_stage_slots( $self_id );
		if ( in_array( $stage_slot, $taken_slots, true ) ) {
			$errors[] = "Scenpasset $stage_slot är redan upptaget. Välj en annan tid eller hoppa över scenen.";
		}
		if ( ! in_array( $stage_slot, sm_stage_slots(), true ) ) {
			$errors[] = 'Ogiltigt scenpass.';
		}
	}
	if ( ! $accept_terms || ! $accept_gdpr ) {
		$errors[] = 'Du måste godkänna utställarvillkor och GDPR.';
	}

	// Kolla att inga av de valda montrarna redan är upptagna
	$already_taken = array_intersect( $booths, sm_booked_booth_ids( $self_id ) );
	if ( $already_taken ) {
		$errors[] = 'Följande montrar är redan upptagna: ' . implode( ', ', $already_taken );
	}

	// Föreningar får endast välja N-montrar; vanliga får inte välja N
	foreach ( $booths as $bid ) {
		if ( sm_is_forening_booth( $bid ) && ! $is_forening ) {
			$errors[] = 'Monter ' . $bid . ' är reserverad för föreningar. Svara Ja på föreningsfrågan i monter-steget om det stämmer.';
		}
		if ( ! sm_is_forening_booth( $bid ) && $is_forening ) {
			$errors[] = 'Som förening kan du endast välja N-montrar (N1–N12).';
		}
	}

	if ( $errors ) {
		set_transient( 'sm_register_errors_' . sm_session_id(), $errors, 60 * 10 );
		set_transient( 'sm_register_input_' . sm_session_id(), $post, 60 * 10 );
		$referer = wp_get_referer() ?: home_url( '/anmalan/' );
		wp_safe_redirect( add_query_arg( 'fel', '1', $referer ) );
		exit;
	}

	// Beräkna totalsumma (registreringsavgift + ev. moms hanteras i hjälparen)
	$total = sm_calculate_total( $booths, $addons, $is_forening );

	// Skapa eller uppdatera CPT-post
	if ( $editing ) {
		$post_id = $edit_id;
		wp_update_post( array( 'ID' => $post_id, 'post_title' => $company ) );
	} else {
		$post_id = wp_insert_post( array(
			'post_type'   => 'sm_registration',
			'post_status' => 'publish',
			'post_title'  => $company,
		), true );

		if ( is_wp_error( $post_id ) ) {
			wp_die( 'Kunde inte spara anmälan. Försök igen eller kontakta bokning@arenavarberg.se.' );
		}
	}

	update_post_meta( $post_id, '_sm_company',         $company );
	update_post_meta( $post_id, '_sm_orgnr',           $orgnr );
	update_post_meta( $post_id, '_sm_invoice_address', $invoice_addr );
	update_post_meta( $post_id, '_sm_invoice_email',   $invoice_email );
	update_post_meta( $post_id, '_sm_contact_name',    $contact_name );
	update_post_meta( $post_id, '_sm_contact_email',   $contact_email );
	update_post_meta( $post_id, '_sm_contact_phone',   $contact_phone );
	update_post_meta( $post_id, '_sm_website',         $website );
	update_post_meta( $post_id, '_sm_description',     $description );
	update_post_meta( $post_id, '_sm_booths',          $booths );
	update_post_meta( $post_id, '_sm_addons',          $addons );
	update_post_meta( $post_id, '_sm_addon_variants',  $variants );
	update_post_meta( $post_id, '_sm_stage_slot',      $stage_slot );
	update_post_meta( $post_id, '_sm_no_website',      $no_website ? '1' : '' );
	update_post_meta( $post_id, '_sm_special_requests',$reqs );
	update_post_meta( $post_id, '_sm_is_forening',     $is_forening ? '1' : '' );
	update_post_meta( $post_id, '_sm_total',           $total );

	if ( $editing ) {
		update_post_meta( $post_id, '_sm_updated_at', current_time( 'mysql' ) );
	} else {
		update_post_meta( $post_id, '_sm_status',       'pending' );
		update_post_meta( $post_id, '_sm_submitted_at', current_time( 'mysql' ) );
	}

	// Säkerställ att en redigerings-token finns (för "ändra din bokning"-länken)
	if ( ! get_post_meta( $post_id, '_sm_edit_token', true ) ) {
		update_post_meta( $post_id, '_sm_edit_token', wp_generate_password( 24, false ) );
	}

	// Skicka mejl
	sm_send_registration_emails( $post_id, $editing );

	// Rensa transient
	delete_transient( 'sm_register_errors_' . sm_session_id() );
	delete_transient( 'sm_register_input_' . sm_session_id() );

	// Redirect till bekräftelsesida
	$args = array( 'ref' => $post_id );
	if ( $editing ) {
		$args['updated'] = '1';
	}
	wp_safe_redirect( add_query_arg( $args, home_url( '/anmalan/' ) ) );
	exit;
}
add_action( 'admin_post_sm_register',        'sm_handle_registration' );
add_action( 'admin_post_nopriv_sm_register', 'sm_handle_registration' );

/**
 * Kontrollerar att en boknings-ID + token matchar en redigerbar anmälan.
 */
function sm_valid_edit_request( $booking_id, $token ) {
	$booking_id = (int) $booking_id;
	$token      = (string) $token;
	if ( ! $booking_id || $token === '' ) {
		return false;
	}
	$p = get_post( $booking_id );
	if ( ! $p || $p->post_type !== 'sm_registration' ) {
		return false;
	}
	if ( get_post_meta( $booking_id, '_sm_status', true ) === 'cancelled' ) {
		return false;
	}
	$stored = (string) get_post_meta( $booking_id, '_sm_edit_token', true );
	return $stored !== '' && hash_equals( $stored, $token );
}

/**
 * URL där en utställare kan ändra sin egen bokning.
 */
function sm_booking_edit_url( $post_id ) {
	$token = get_post_meta( $post_id, '_sm_edit_token', true );
	if ( ! $token ) {
		$token = wp_generate_password( 24, false );
		update_post_meta( $post_id, '_sm_edit_token', $token );
	}
	return add_query_arg(
		array( 'booking' => (int) $post_id, 'token' => $token ),
		home_url( '/anmalan/' )
	);
}

/**
 * En slags session-ID för icke-inloggade användare (cookie-baserat),
 * så att fel- och inputtransienter knyts till rätt besökare.
 */
function sm_session_id() {
	if ( empty( $_COOKIE['sm_session'] ) ) {
		$id = wp_generate_password( 20, false );
		setcookie( 'sm_session', $id, time() + 3600, COOKIEPATH, COOKIE_DOMAIN );
		$_COOKIE['sm_session'] = $id;
		return $id;
	}
	return sanitize_text_field( $_COOKIE['sm_session'] );
}

/**
 * Skickar mejl till booking och bekräftelse till utställaren.
 */
function sm_send_registration_emails( $post_id, $editing = false ) {
	$company       = get_post_meta( $post_id, '_sm_company', true );
	$contact_name  = get_post_meta( $post_id, '_sm_contact_name', true );
	$contact_email = get_post_meta( $post_id, '_sm_contact_email', true );
	$contact_phone = get_post_meta( $post_id, '_sm_contact_phone', true );
	$booths        = get_post_meta( $post_id, '_sm_booths', true ) ?: array();
	$addons        = get_post_meta( $post_id, '_sm_addons', true ) ?: array();
	$variants      = get_post_meta( $post_id, '_sm_addon_variants', true ) ?: array();
	$total         = (int) get_post_meta( $post_id, '_sm_total', true );
	$is_for        = get_post_meta( $post_id, '_sm_is_forening', true );
	$reqs          = get_post_meta( $post_id, '_sm_special_requests', true );
	$stage_slot    = get_post_meta( $post_id, '_sm_stage_slot', true );

	$booths_str = implode( ', ', $booths );

	$addons_str = '';
	foreach ( $addons as $id => $qty ) {
		$a = sm_addon( $id );
		if ( ! $a || (int) $qty <= 0 ) {
			continue;
		}
		$unit    = $a['price_label'] ?? 'kr/st';
		$variant = ! empty( $variants[ $id ] ) ? ' (' . $variants[ $id ] . ')' : '';
		$addons_str .= "\n  • {$a['name']}{$variant} × {$qty} ({$a['price']} {$unit})";
	}

	$moms_label = $is_for ? 'inkl. moms' : 'exkl. moms';
	$admin_link = admin_url( 'post.php?post=' . $post_id . '&action=edit' );
	$edit_link  = sm_booking_edit_url( $post_id );

	$headers = array(
		'From: Seniormässan – Arena Varberg <' . sm_booking_email() . '>',
		'Reply-To: ' . sm_booking_email(),
	);

	// Notis till bokning@arenavarberg.se
	$admin_subject = ( $editing ? 'Ändrad utställaranmälan: ' : 'Ny utställaranmälan: ' ) . $company;
	$admin_body    = ( $editing ? "En utställare har ändrat sin anmälan." : "Ny anmälan inkommen till Seniormässan 2027." ) . "\n\n"
		. "Företag: $company\n"
		. "Kontakt: $contact_name\n"
		. "E-post: $contact_email\n"
		. "Telefon: $contact_phone\n"
		. "Montrar: $booths_str\n"
		. ( $stage_slot ? "Scenpass: $stage_slot\n" : '' )
		. ( $reqs ? "\nÖnskemål:\n$reqs\n" : '' )
		. ( $addons_str ? "\nTillval:$addons_str\n" : '' )
		. "\nTotalsumma: " . number_format( $total, 0, ',', "\u{00A0}" ) . " kr ($moms_label)\n\n"
		. "Hantera i admin: $admin_link\n";

	wp_mail( sm_booking_email(), $admin_subject, $admin_body, $headers );

	// Bekräftelse till utställaren
	if ( $contact_email ) {
		$user_subject = $editing ? 'Din anmälan till Seniormässan 2027 är uppdaterad' : 'Tack för din anmälan till Seniormässan 2027';
		$user_body    = "Hej $contact_name,\n\n"
			. ( $editing
				? 'Din bokning till Seniormässan på ' . sm_venue_name() . ', ' . mb_strtolower( sm_event_date_long() ) . " är nu uppdaterad.\n\n"
				: 'Tack för din anmälan till Seniormässan på ' . sm_venue_name() . ', ' . mb_strtolower( sm_event_date_long() ) . ".\n\n" )
			. "Sammanfattning av din bokning:\n"
			. "  • Montrar: $booths_str\n"
			. ( $stage_slot ? "  • Utställarscen: $stage_slot (15 min)\n" : '' )
			. ( $addons_str ? "\nTillval:$addons_str\n" : '' )
			. "\nTotalsumma: " . number_format( $total, 0, ',', "\u{00A0}" ) . " kr ($moms_label).\n\n"
			. "Behöver du ändra något i din bokning? Använd din personliga länk:\n$edit_link\n\n"
			. 'Vi återkommer inom kort med bekräftelse och faktura. Har du frågor — svara på det här mejlet eller hör av dig till ' . sm_booking_email() . ".\n\n"
			. "Vänliga hälsningar,\nSeniormässan / Arena Varberg";

		wp_mail( $contact_email, $user_subject, $user_body, $headers );
	}
}
