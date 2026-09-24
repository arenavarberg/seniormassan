<?php
/**
 * Admin-verktyg: hitta anmälningar vars sparade totalsumma avviker från en
 * omräkning med nuvarande prislogik (t.ex. föreningsbokningar gjorda innan
 * momsfixen i v0.15.14), och räkna om dem.
 *
 * Sidan ligger under Anmälningar → Kontrollera totaler.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sm_recompute_menu() {
	add_submenu_page(
		'edit.php?post_type=sm_registration',
		'Kontrollera totaler',
		'Kontrollera totaler',
		'manage_options',
		'sm-recompute',
		'sm_recompute_page'
	);
}
add_action( 'admin_menu', 'sm_recompute_menu' );

/**
 * Alla anmälningar vars sparade _sm_total avviker från sm_calculate_total().
 *
 * @return array<int,array{id:int,company:string,is_for:bool,stored:int,correct:int,status:string}>
 */
function sm_find_mismatched_registrations() {
	$posts = get_posts( array(
		'post_type'   => 'sm_registration',
		'post_status' => 'any',
		'numberposts' => -1,
		'orderby'     => 'date',
		'order'       => 'DESC',
	) );

	$rows = array();
	foreach ( $posts as $p ) {
		$booths  = get_post_meta( $p->ID, '_sm_booths', true ) ?: array();
		$addons  = get_post_meta( $p->ID, '_sm_addons', true ) ?: array();
		$is_for  = ! empty( get_post_meta( $p->ID, '_sm_is_forening', true ) );
		$stored  = (int) get_post_meta( $p->ID, '_sm_total', true );
		$correct = (int) sm_calculate_total( (array) $booths, (array) $addons, $is_for );

		if ( $stored !== $correct ) {
			$rows[] = array(
				'id'      => (int) $p->ID,
				'company' => (string) get_post_meta( $p->ID, '_sm_company', true ),
				'is_for'  => $is_for,
				'stored'  => $stored,
				'correct' => $correct,
				'status'  => (string) ( get_post_meta( $p->ID, '_sm_status', true ) ?: 'pending' ),
			);
		}
	}
	return $rows;
}

function sm_recompute_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$did = 0;
	if ( isset( $_POST['sm_recompute_all'] ) && check_admin_referer( 'sm_recompute' ) ) {
		foreach ( sm_find_mismatched_registrations() as $r ) {
			update_post_meta( $r['id'], '_sm_total', $r['correct'] );
			$did++;
		}
	}

	$rows = sm_find_mismatched_registrations();
	$kr   = function ( $n ) {
		return number_format( (int) $n, 0, ',', "\u{00A0}" ) . ' kr';
	};
	?>
	<div class="wrap">
		<h1>Kontrollera totaler</h1>

		<?php if ( $did ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo (int) $did; ?> bokning(ar) omräknade och sparade.</p></div>
		<?php endif; ?>

		<p style="max-width:720px;">
			Här listas anmälningar vars <strong>sparade totalsumma</strong> skiljer sig från en omräkning
			med nuvarande prislogik. Framför allt gäller det <strong>föreningsbokningar gjorda innan
			momsfixen</strong> (då lades 25&nbsp;% moms felaktigt på hela ordern, inklusive montern).
			Ingen data ändras förrän du klickar på knappen — och inga mejl skickas.
		</p>

		<?php if ( empty( $rows ) ) : ?>
			<p style="font-size:15px;"><strong>Inga avvikelser — alla totaler stämmer. ✓</strong></p>
		<?php else : ?>
			<table class="widefat striped" style="max-width:900px; margin-top:16px;">
				<thead>
					<tr>
						<th>Företag</th>
						<th>Typ</th>
						<th>Status</th>
						<th style="text-align:right;">Sparad (fel)</th>
						<th style="text-align:right;">Korrekt</th>
						<th style="text-align:right;">Diff</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rows as $r ) :
						$diff = $r['correct'] - $r['stored'];
						$edit = get_edit_post_link( $r['id'] );
						?>
						<tr>
							<td><a href="<?php echo esc_url( $edit ); ?>"><?php echo esc_html( $r['company'] !== '' ? $r['company'] : ( '#' . $r['id'] ) ); ?></a></td>
							<td><?php echo $r['is_for'] ? 'Förening' : 'Företag'; ?></td>
							<td><?php echo esc_html( $r['status'] ); ?></td>
							<td style="text-align:right;"><?php echo esc_html( $kr( $r['stored'] ) ); ?></td>
							<td style="text-align:right;"><strong><?php echo esc_html( $kr( $r['correct'] ) ); ?></strong></td>
							<td style="text-align:right; color:<?php echo $diff < 0 ? '#116611' : '#a00'; ?>;">
								<?php echo ( $diff > 0 ? '+' : ( $diff < 0 ? '−' : '' ) ) . esc_html( number_format( abs( $diff ), 0, ',', "\u{00A0}" ) ); ?> kr
							</td>
							<td><a class="button button-small" href="<?php echo esc_url( $edit ); ?>">Öppna</a></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<form method="post" style="margin-top:20px;">
				<?php wp_nonce_field( 'sm_recompute' ); ?>
				<button type="submit" name="sm_recompute_all" value="1" class="button button-primary"
					onclick="return confirm('Räkna om och spara korrekt totalsumma för alla <?php echo count( $rows ); ?> bokningar i listan?');">
					Räkna om alla (<?php echo count( $rows ); ?>)
				</button>
				<p class="description" style="max-width:720px;">
					Uppdaterar den sparade totalsumman till den korrekta. Du kan också öppna en enskild
					bokning och klicka <em>Uppdatera</em> — det räknar om just den. Inga mejl skickas.
				</p>
			</form>
		<?php endif; ?>
	</div>
	<?php
}
