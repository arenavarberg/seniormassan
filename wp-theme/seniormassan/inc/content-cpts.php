<?php
/**
 * CPT:s för redigerbart innehåll:
 *   - sm_contact    Kontaktpersoner (sidan /kontakt/)
 *   - sm_highlight  Höjdpunkter (förstasidan)
 *   - sm_zone       Områden (förstasidan)
 *
 * Alla tre stödjer "page-attributes" så redaktören kan dra-och-släppa eller
 * sätta "Ordning" för att styra visningen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =====================================================================
 * Registrering
 * ===================================================================== */

function sm_register_content_cpts() {
	register_post_type( 'sm_contact', array(
		'labels' => array(
			'name'          => 'Kontakter',
			'singular_name' => 'Kontakt',
			'menu_name'     => 'Kontakter',
			'add_new_item'  => 'Lägg till kontakt',
			'edit_item'     => 'Redigera kontakt',
			'new_item'      => 'Ny kontakt',
			'search_items'  => 'Sök kontakter',
			'not_found'     => 'Inga kontakter hittades.',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 28,
		'menu_icon'           => 'dashicons-businessperson',
		'supports'            => array( 'title', 'page-attributes' ),
		'has_archive'         => false,
		'exclude_from_search' => true,
	) );

	register_post_type( 'sm_highlight', array(
		'labels' => array(
			'name'          => 'Höjdpunkter',
			'singular_name' => 'Höjdpunkt',
			'menu_name'     => 'Höjdpunkter',
			'add_new_item'  => 'Lägg till höjdpunkt',
			'edit_item'     => 'Redigera höjdpunkt',
			'new_item'      => 'Ny höjdpunkt',
			'search_items'  => 'Sök höjdpunkter',
			'not_found'     => 'Inga höjdpunkter hittades.',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 29,
		'menu_icon'           => 'dashicons-star-filled',
		'supports'            => array( 'title', 'thumbnail', 'page-attributes' ),
		'has_archive'         => false,
		'exclude_from_search' => true,
	) );

	register_post_type( 'sm_zone', array(
		'labels' => array(
			'name'          => 'Områden',
			'singular_name' => 'Område',
			'menu_name'     => 'Områden',
			'add_new_item'  => 'Lägg till område',
			'edit_item'     => 'Redigera område',
			'new_item'      => 'Nytt område',
			'search_items'  => 'Sök områden',
			'not_found'     => 'Inga områden hittades.',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 30,
		'menu_icon'           => 'dashicons-grid-view',
		'supports'            => array( 'title', 'page-attributes' ),
		'has_archive'         => false,
		'exclude_from_search' => true,
	) );
}
add_action( 'init', 'sm_register_content_cpts' );

/* =====================================================================
 * Meta boxes
 * ===================================================================== */

function sm_content_meta_boxes() {
	add_meta_box( 'sm_contact_details',   'Kontaktuppgifter', 'sm_contact_meta_box_html',   'sm_contact',   'normal', 'high' );
	add_meta_box( 'sm_highlight_details', 'Höjdpunkt',        'sm_highlight_meta_box_html', 'sm_highlight', 'normal', 'high' );
	add_meta_box( 'sm_zone_details',      'Område',           'sm_zone_meta_box_html',      'sm_zone',      'normal', 'high' );
}
add_action( 'add_meta_boxes', 'sm_content_meta_boxes' );

function sm_meta_grid_style() {
	echo '<style>.sm-cpt-grid{display:grid;grid-template-columns:160px 1fr;gap:12px 24px;align-items:start;}.sm-cpt-grid label{font-weight:600;padding-top:6px;}.sm-cpt-grid input[type=text],.sm-cpt-grid input[type=email],.sm-cpt-grid textarea{width:100%;padding:6px 10px;}.sm-cpt-grid .sm-hint{color:#646970;font-size:12px;margin-top:4px;}</style>';
}

function sm_contact_meta_box_html( $post ) {
	wp_nonce_field( 'sm_save_contact', 'sm_contact_nonce' );
	sm_meta_grid_style();

	$role  = get_post_meta( $post->ID, '_sm_role', true );
	$email = get_post_meta( $post->ID, '_sm_email', true );
	$phone = get_post_meta( $post->ID, '_sm_phone', true );

	echo '<div class="sm-cpt-grid">';
	echo '<label for="sm_role">Roll</label>';
	echo '<div><input type="text" id="sm_role" name="sm_role" value="' . esc_attr( $role ) . '" placeholder="t.ex. Projektledare"><div class="sm-hint">Visas som liten etikett ovanför namnet.</div></div>';

	echo '<label for="sm_email">E-post</label>';
	echo '<input type="email" id="sm_email" name="sm_email" value="' . esc_attr( $email ) . '">';

	echo '<label for="sm_phone">Telefon</label>';
	echo '<input type="text" id="sm_phone" name="sm_phone" value="' . esc_attr( $phone ) . '" placeholder="t.ex. 0340-690200">';
	echo '</div>';

	echo '<p class="sm-hint" style="margin-top:16px;color:#646970;">Titel = personens namn. Sortering styrs av "Ordning" i Sidattribut-rutan.</p>';
}

function sm_highlight_meta_box_html( $post ) {
	wp_nonce_field( 'sm_save_highlight', 'sm_highlight_nonce' );
	sm_meta_grid_style();

	$kicker      = get_post_meta( $post->ID, '_sm_kicker', true );
	$description = get_post_meta( $post->ID, '_sm_description', true );

	echo '<div class="sm-cpt-grid">';
	echo '<label for="sm_kicker">Etikett</label>';
	echo '<div><input type="text" id="sm_kicker" name="sm_kicker" value="' . esc_attr( $kicker ) . '" placeholder="t.ex. Scenprogram"><div class="sm-hint">Liten versal etikett ovanför rubriken.</div></div>';

	echo '<label for="sm_description">Beskrivning</label>';
	echo '<textarea id="sm_description" name="sm_description" rows="3">' . esc_textarea( $description ) . '</textarea>';
	echo '</div>';

	echo '<p class="sm-hint" style="margin-top:16px;color:#646970;">Titel = rubriken på kortet. Bilden sätts via "Utvald bild" till höger.</p>';
}

function sm_zone_meta_box_html( $post ) {
	wp_nonce_field( 'sm_save_zone', 'sm_zone_nonce' );
	sm_meta_grid_style();

	$description = get_post_meta( $post->ID, '_sm_description', true );

	echo '<div class="sm-cpt-grid">';
	echo '<label for="sm_description">Beskrivning</label>';
	echo '<textarea id="sm_description" name="sm_description" rows="3">' . esc_textarea( $description ) . '</textarea>';
	echo '</div>';

	echo '<p class="sm-hint" style="margin-top:16px;color:#646970;">Titel = områdets namn (t.ex. "Resor & Upplevelser"). Numreringen (01, 02, …) räknas automatiskt från sorteringen — ändra "Ordning" i Sidattribut för att flytta området.</p>';
}

/* =====================================================================
 * Save
 * ===================================================================== */

function sm_save_contact_meta( $post_id ) {
	if ( ! isset( $_POST['sm_contact_nonce'] ) || ! wp_verify_nonce( $_POST['sm_contact_nonce'], 'sm_save_contact' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( array( 'role', 'email', 'phone' ) as $k ) {
		if ( isset( $_POST[ 'sm_' . $k ] ) ) {
			update_post_meta( $post_id, '_sm_' . $k, sanitize_text_field( wp_unslash( $_POST[ 'sm_' . $k ] ) ) );
		}
	}
}
add_action( 'save_post_sm_contact', 'sm_save_contact_meta' );

function sm_save_highlight_meta( $post_id ) {
	if ( ! isset( $_POST['sm_highlight_nonce'] ) || ! wp_verify_nonce( $_POST['sm_highlight_nonce'], 'sm_save_highlight' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['sm_kicker'] ) ) {
		update_post_meta( $post_id, '_sm_kicker', sanitize_text_field( wp_unslash( $_POST['sm_kicker'] ) ) );
	}
	if ( isset( $_POST['sm_description'] ) ) {
		update_post_meta( $post_id, '_sm_description', sanitize_textarea_field( wp_unslash( $_POST['sm_description'] ) ) );
	}
}
add_action( 'save_post_sm_highlight', 'sm_save_highlight_meta' );

function sm_save_zone_meta( $post_id ) {
	if ( ! isset( $_POST['sm_zone_nonce'] ) || ! wp_verify_nonce( $_POST['sm_zone_nonce'], 'sm_save_zone' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['sm_description'] ) ) {
		update_post_meta( $post_id, '_sm_description', sanitize_textarea_field( wp_unslash( $_POST['sm_description'] ) ) );
	}
}
add_action( 'save_post_sm_zone', 'sm_save_zone_meta' );

/* =====================================================================
 * Admin-listkolumner
 * ===================================================================== */

function sm_contact_columns( $columns ) {
	return array(
		'cb'        => $columns['cb'],
		'title'     => 'Namn',
		'sm_role'   => 'Roll',
		'sm_email'  => 'E-post',
		'sm_phone'  => 'Telefon',
		'menu_order'=> 'Ordning',
	);
}
add_filter( 'manage_sm_contact_posts_columns', 'sm_contact_columns' );

function sm_contact_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'sm_role':
			echo esc_html( get_post_meta( $post_id, '_sm_role', true ) );
			break;
		case 'sm_email':
			echo esc_html( get_post_meta( $post_id, '_sm_email', true ) );
			break;
		case 'sm_phone':
			echo esc_html( get_post_meta( $post_id, '_sm_phone', true ) );
			break;
		case 'menu_order':
			$p = get_post( $post_id );
			echo (int) $p->menu_order;
			break;
	}
}
add_action( 'manage_sm_contact_posts_custom_column', 'sm_contact_column_content', 10, 2 );

function sm_highlight_columns( $columns ) {
	return array(
		'cb'         => $columns['cb'],
		'title'      => 'Rubrik',
		'sm_kicker'  => 'Etikett',
		'sm_thumb'   => 'Bild',
		'menu_order' => 'Ordning',
	);
}
add_filter( 'manage_sm_highlight_posts_columns', 'sm_highlight_columns' );

function sm_highlight_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'sm_kicker':
			echo esc_html( get_post_meta( $post_id, '_sm_kicker', true ) );
			break;
		case 'sm_thumb':
			if ( has_post_thumbnail( $post_id ) ) {
				echo get_the_post_thumbnail( $post_id, array( 60, 40 ) );
			} else {
				echo '<span style="color:#a00;">Saknas</span>';
			}
			break;
		case 'menu_order':
			$p = get_post( $post_id );
			echo (int) $p->menu_order;
			break;
	}
}
add_action( 'manage_sm_highlight_posts_custom_column', 'sm_highlight_column_content', 10, 2 );

function sm_zone_columns( $columns ) {
	return array(
		'cb'         => $columns['cb'],
		'title'      => 'Namn',
		'sm_desc'    => 'Beskrivning',
		'menu_order' => 'Ordning',
	);
}
add_filter( 'manage_sm_zone_posts_columns', 'sm_zone_columns' );

function sm_zone_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'sm_desc':
			echo esc_html( wp_trim_words( get_post_meta( $post_id, '_sm_description', true ), 14 ) );
			break;
		case 'menu_order':
			$p = get_post( $post_id );
			echo (int) $p->menu_order;
			break;
	}
}
add_action( 'manage_sm_zone_posts_custom_column', 'sm_zone_column_content', 10, 2 );

/**
 * Sortera adminlistorna på menu_order ASC som standard.
 */
function sm_content_admin_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen ) {
		return;
	}
	if ( in_array( $screen->post_type, array( 'sm_contact', 'sm_highlight', 'sm_zone' ), true ) ) {
		if ( ! $query->get( 'orderby' ) ) {
			$query->set( 'orderby', 'menu_order title' );
			$query->set( 'order', 'ASC' );
		}
	}
}
add_action( 'pre_get_posts', 'sm_content_admin_order' );

/* =====================================================================
 * Hjälpfunktioner — hämta poster + fallback-checks
 * ===================================================================== */

function sm_fetch_ordered( $post_type ) {
	return get_posts( array(
		'post_type'      => $post_type,
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	) );
}

function sm_has_posts( $post_type ) {
	$q = new WP_Query( array(
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	) );
	return $q->have_posts();
}

/**
 * @return array{r:string,n:string,e:string,p:string}[]
 */
function sm_contacts() {
	$items = array();
	foreach ( sm_fetch_ordered( 'sm_contact' ) as $p ) {
		$items[] = array(
			'r' => (string) get_post_meta( $p->ID, '_sm_role', true ),
			'n' => $p->post_title,
			'e' => (string) get_post_meta( $p->ID, '_sm_email', true ),
			'p' => (string) get_post_meta( $p->ID, '_sm_phone', true ),
		);
	}
	return $items;
}

function sm_has_contacts() {
	return sm_has_posts( 'sm_contact' );
}

/**
 * @return array{img_url:string,k:string,t:string,d:string}[]
 */
function sm_highlights() {
	$items = array();
	foreach ( sm_fetch_ordered( 'sm_highlight' ) as $p ) {
		$img_url = has_post_thumbnail( $p->ID )
			? (string) get_the_post_thumbnail_url( $p->ID, 'large' )
			: '';
		$items[] = array(
			'img_url' => $img_url,
			'k'       => (string) get_post_meta( $p->ID, '_sm_kicker', true ),
			't'       => $p->post_title,
			'd'       => (string) get_post_meta( $p->ID, '_sm_description', true ),
		);
	}
	return $items;
}

function sm_has_highlights() {
	return sm_has_posts( 'sm_highlight' );
}

/**
 * @return array{n:string,t:string,d:string}[]  Med 'n' = "01", "02", ... räknat från sortordning.
 */
function sm_zones() {
	$items = array();
	$i = 1;
	foreach ( sm_fetch_ordered( 'sm_zone' ) as $p ) {
		$items[] = array(
			'n' => sprintf( '%02d', $i ),
			't' => $p->post_title,
			'd' => (string) get_post_meta( $p->ID, '_sm_description', true ),
		);
		$i++;
	}
	return $items;
}

function sm_has_zones() {
	return sm_has_posts( 'sm_zone' );
}
