<?php
/**
 * Admin-sida för att redigera monterpriser och tillvalspriser.
 *
 * Sparas i två options:
 *   - 'sm_pricing'  — array med 'booth_2x2', 'booth_2x3', 'booth_3x3',
 *                     'forening', 'registration_fee' (heltal i kr exkl. moms)
 *   - 'sm_addon_prices' — array keyed by addon-id → pris i kr exkl. moms
 *
 * Funktionerna i inc/booth-data.php läser dessa options med fallback till
 * de hårdkodade default-värdena.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sm_pricing_menu() {
	add_submenu_page(
		'edit.php?post_type=sm_registration',
		'Priser',
		'Priser',
		'manage_options',
		'sm-pricing',
		'sm_pricing_page'
	);
}
add_action( 'admin_menu', 'sm_pricing_menu' );

function sm_pricing_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_POST['sm_pricing_save'] ) && check_admin_referer( 'sm_pricing' ) ) {
		$incoming = wp_unslash( $_POST );

		$pricing = array(
			'booth_2x2'        => max( 0, (int) ( $incoming['booth_2x2']        ?? 0 ) ),
			'booth_2x3'        => max( 0, (int) ( $incoming['booth_2x3']        ?? 0 ) ),
			'booth_3x3'        => max( 0, (int) ( $incoming['booth_3x3']        ?? 0 ) ),
			'forening'         => max( 0, (int) ( $incoming['forening']         ?? 0 ) ),
			'registration_fee' => max( 0, (int) ( $incoming['registration_fee'] ?? 0 ) ),
		);
		update_option( 'sm_pricing', $pricing );

		$addon_prices = array();
		$incoming_a   = isset( $incoming['addon_price'] ) && is_array( $incoming['addon_price'] ) ? $incoming['addon_price'] : array();
		foreach ( sm_addons_raw() as $a ) {
			if ( isset( $incoming_a[ $a['id'] ] ) ) {
				$addon_prices[ $a['id'] ] = max( 0, (int) $incoming_a[ $a['id'] ] );
			}
		}
		update_option( 'sm_addon_prices', $addon_prices );

		echo '<div class="notice notice-success"><p>Priser sparade.</p></div>';
	}

	$pricing = sm_get_pricing();
	?>
	<div class="wrap">
		<h1>Priser</h1>
		<p>Priserna nedan används både i utställarformuläret (<code>/anmalan/</code>) och på prislistan på <code>/for-utstallare/</code>. Alla priser anges <strong>exklusive moms</strong>. För föreningar läggs 25 % moms på automatiskt vid totalsumman.</p>

		<form method="post">
			<?php wp_nonce_field( 'sm_pricing' ); ?>

			<h2 style="margin-top:32px;">Monterpriser</h2>
			<table class="form-table" role="presentation" style="max-width:640px;">
				<tbody>
					<tr>
						<th scope="row"><label for="booth_2x2">Monter 2 × 2 m</label></th>
						<td>
							<input type="number" id="booth_2x2" name="booth_2x2" value="<?php echo (int) $pricing['booth_2x2']; ?>" min="0" step="1" style="width:120px;"> kr exkl. moms
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="booth_2x3">Monter 2 × 3 m</label></th>
						<td>
							<input type="number" id="booth_2x3" name="booth_2x3" value="<?php echo (int) $pricing['booth_2x3']; ?>" min="0" step="1" style="width:120px;"> kr exkl. moms
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="booth_3x3">Monter 3 × 3 m</label></th>
						<td>
							<input type="number" id="booth_3x3" name="booth_3x3" value="<?php echo (int) $pricing['booth_3x3']; ?>" min="0" step="1" style="width:120px;"> kr exkl. moms
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="forening">Förenings-monter (N1–N12)</label></th>
						<td>
							<input type="number" id="forening" name="forening" value="<?php echo (int) $pricing['forening']; ?>" min="0" step="1" style="width:120px;"> kr exkl. moms
							<p class="description">Visas som <strong><?php echo esc_html( number_format( (int) round( $pricing['forening'] * 1.25 ), 0, ',', "\u{00A0}" ) ); ?> kr inkl. moms</strong> på /for-utstallare/.</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="registration_fee">Registreringsavgift</label></th>
						<td>
							<input type="number" id="registration_fee" name="registration_fee" value="<?php echo (int) $pricing['registration_fee']; ?>" min="0" step="1" style="width:120px;"> kr exkl. moms
							<p class="description">Läggs på automatiskt när minst en monter är vald.</p>
						</td>
					</tr>
				</tbody>
			</table>

			<h2 style="margin-top:40px;">Tillvalspriser</h2>
			<p>Tillvalen visas i steg 3 av anmälningsformuläret. För <em>Montermatta</em> är priset per monter (skalar automatiskt med antal valda montrar); för övriga är priset per styck.</p>

			<?php
			$by_cat = array();
			foreach ( sm_addons_raw() as $a ) {
				$by_cat[ $a['cat'] ][] = $a;
			}
			foreach ( $by_cat as $cat => $list ) :
				?>
				<h3 style="margin-top:24px;border-bottom:1px solid #ccd0d4;padding-bottom:8px;"><?php echo esc_html( $cat ); ?></h3>
				<table class="form-table" role="presentation" style="max-width:640px;">
					<tbody>
						<?php foreach ( $list as $a ) :
							$current = sm_get_addon_price( $a['id'], $a['price'] );
							$label   = $a['price_label'] ?? 'kr/st';
							?>
							<tr>
								<th scope="row"><label for="addon_<?php echo esc_attr( $a['id'] ); ?>"><?php echo esc_html( $a['name'] ); ?></label></th>
								<td>
									<input type="number" id="addon_<?php echo esc_attr( $a['id'] ); ?>" name="addon_price[<?php echo esc_attr( $a['id'] ); ?>]" value="<?php echo (int) $current; ?>" min="0" step="1" style="width:100px;"> <?php echo esc_html( $label ); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endforeach; ?>

			<p style="margin-top:24px;"><input type="submit" name="sm_pricing_save" class="button button-primary" value="Spara priser"></p>
		</form>
	</div>
	<?php
}
