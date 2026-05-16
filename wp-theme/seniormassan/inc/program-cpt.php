<?php
/**
 * Custom Post Type "sm_program_item" — programpunkter på de två scenerna.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sm_program_stages() {
	return array(
		'stora_scen'      => 'Stora Scen',
		'utstallarscenen' => 'Utställarscenen',
	);
}

function sm_register_program_cpt() {
	register_post_type( 'sm_program_item', array(
		'labels' => array(
			'name'               => 'Program',
			'singular_name'      => 'Programpunkt',
			'menu_name'          => 'Program',
			'add_new'            => 'Lägg till',
			'add_new_item'       => 'Lägg till programpunkt',
			'edit_item'          => 'Redigera programpunkt',
			'new_item'           => 'Ny programpunkt',
			'view_item'          => 'Visa programpunkt',
			'search_items'       => 'Sök programpunkter',
			'not_found'          => 'Inga programpunkter hittades.',
			'not_found_in_trash' => 'Inga programpunkter i papperskorgen.',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 27,
		'menu_icon'           => 'dashicons-calendar-alt',
		'capability_type'     => 'post',
		'supports'            => array( 'title', 'page-attributes' ),
		'has_archive'         => false,
		'exclude_from_search' => true,
	) );
}
add_action( 'init', 'sm_register_program_cpt' );

/**
 * Meta box med tid, scen och beskrivning.
 */
function sm_program_meta_boxes() {
	add_meta_box( 'sm_program_details', 'Programpunkt', 'sm_program_meta_box_html', 'sm_program_item', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'sm_program_meta_boxes' );

function sm_program_meta_box_html( $post ) {
	wp_nonce_field( 'sm_save_program', 'sm_program_nonce' );

	$time        = get_post_meta( $post->ID, '_sm_time', true );
	$stage       = get_post_meta( $post->ID, '_sm_stage', true ) ?: 'stora_scen';
	$description = get_post_meta( $post->ID, '_sm_description', true );

	echo '<style>.sm-program-grid{display:grid;grid-template-columns:160px 1fr;gap:12px 24px;align-items:start;}.sm-program-grid label{font-weight:600;padding-top:6px;}.sm-program-grid input[type=text],.sm-program-grid textarea,.sm-program-grid select{width:100%;padding:6px 10px;}.sm-program-grid .sm-hint{color:#646970;font-size:12px;margin-top:4px;}</style>';

	echo '<div class="sm-program-grid">';

	echo '<label for="sm_time">Tid</label>';
	echo '<div><input type="text" id="sm_time" name="sm_time" value="' . esc_attr( $time ) . '" placeholder="t.ex. 10:00" pattern="[0-9]{1,2}:[0-9]{2}" style="max-width:140px;"><div class="sm-hint">Format: HH:MM (24-timmar). Används för sortering.</div></div>';

	echo '<label for="sm_stage">Scen</label>';
	echo '<select id="sm_stage" name="sm_stage" style="max-width:280px;">';
	foreach ( sm_program_stages() as $key => $label ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $key ), selected( $stage, $key, false ), esc_html( $label ) );
	}
	echo '</select>';

	echo '<label for="sm_description">Beskrivning</label>';
	echo '<textarea id="sm_description" name="sm_description" rows="3" placeholder="Kort beskrivning som visas under namnet">' . esc_textarea( $description ) . '</textarea>';

	echo '</div>';
	echo '<p class="sm-hint" style="margin-top:16px;color:#646970;">Titeln på inlägget är programpunktens namn (t.ex. "Peter Börjesson" eller "Modevisning").</p>';
}

function sm_save_program_meta( $post_id ) {
	if ( ! isset( $_POST['sm_program_nonce'] ) || ! wp_verify_nonce( $_POST['sm_program_nonce'], 'sm_save_program' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['sm_time'] ) ) {
		$time = sanitize_text_field( wp_unslash( $_POST['sm_time'] ) );
		// Normalisera "9:00" → "09:00" för korrekt sortering.
		if ( preg_match( '/^(\d{1,2}):(\d{2})$/', $time, $m ) ) {
			$time = sprintf( '%02d:%02d', (int) $m[1], (int) $m[2] );
		}
		update_post_meta( $post_id, '_sm_time', $time );
	}

	if ( isset( $_POST['sm_stage'] ) ) {
		$stage = sanitize_text_field( wp_unslash( $_POST['sm_stage'] ) );
		if ( ! array_key_exists( $stage, sm_program_stages() ) ) {
			$stage = 'stora_scen';
		}
		update_post_meta( $post_id, '_sm_stage', $stage );
	}

	if ( isset( $_POST['sm_description'] ) ) {
		update_post_meta( $post_id, '_sm_description', sanitize_textarea_field( wp_unslash( $_POST['sm_description'] ) ) );
	}
}
add_action( 'save_post_sm_program_item', 'sm_save_program_meta' );

/**
 * Anpassade kolumner i listvyn.
 */
function sm_program_columns( $columns ) {
	return array(
		'cb'         => $columns['cb'],
		'sm_time'    => 'Tid',
		'title'      => 'Namn',
		'sm_stage'   => 'Scen',
		'sm_desc'    => 'Beskrivning',
		'date'       => 'Skapad',
	);
}
add_filter( 'manage_sm_program_item_posts_columns', 'sm_program_columns' );

function sm_program_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'sm_time':
			$time = get_post_meta( $post_id, '_sm_time', true );
			echo $time ? '<strong>' . esc_html( $time ) . '</strong>' : '—';
			break;

		case 'sm_stage':
			$stage  = get_post_meta( $post_id, '_sm_stage', true );
			$stages = sm_program_stages();
			echo esc_html( $stages[ $stage ] ?? $stage );
			break;

		case 'sm_desc':
			echo esc_html( get_post_meta( $post_id, '_sm_description', true ) );
			break;
	}
}
add_action( 'manage_sm_program_item_posts_custom_column', 'sm_program_column_content', 10, 2 );

/**
 * Sortera listvyn på tid stigande, scen.
 */
function sm_program_admin_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || $screen->post_type !== 'sm_program_item' ) {
		return;
	}
	if ( ! $query->get( 'orderby' ) ) {
		$query->set( 'meta_key', '_sm_time' );
		$query->set( 'orderby', 'meta_value' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'sm_program_admin_order' );

/**
 * Hämta programpunkter för en viss scen, sorterade på tid.
 *
 * @param string $stage 'stora_scen' eller 'utstallarscenen'.
 * @return array{t:string,n:string,d:string}[]
 */
function sm_program_items( $stage ) {
	$posts = get_posts( array(
		'post_type'      => 'sm_program_item',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_key'       => '_sm_time',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => array(
			array(
				'key'   => '_sm_stage',
				'value' => $stage,
			),
		),
	) );

	$items = array();
	foreach ( $posts as $p ) {
		$items[] = array(
			't' => (string) get_post_meta( $p->ID, '_sm_time', true ),
			'n' => $p->post_title,
			'd' => (string) get_post_meta( $p->ID, '_sm_description', true ),
		);
	}
	return $items;
}

/**
 * Har redaktören lagt in några programpunkter alls?
 */
function sm_has_program_items() {
	$q = new WP_Query( array(
		'post_type'      => 'sm_program_item',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	) );
	return $q->have_posts();
}
