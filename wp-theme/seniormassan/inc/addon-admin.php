<?php
/**
 * Admin-sida för att ladda upp ikoner till tillval (och färgvarianter).
 *
 * Ikon-URL:er sparas i option 'sm_addon_icons' som en nästlad array:
 *   array(
 *     'matta' => array(
 *       'main' => 'https://.../matta.png',
 *       'variants' => array( 'Blå' => 'https://.../mata-bla.png', ... ),
 *     ),
 *     'stol' => array( 'main' => '...' ),
 *   )
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sm_addon_icons_menu() {
	add_submenu_page(
		'edit.php?post_type=sm_registration',
		'Tillval-ikoner',
		'Tillval-ikoner',
		'manage_options',
		'sm-addon-icons',
		'sm_addon_icons_page'
	);
}
add_action( 'admin_menu', 'sm_addon_icons_menu' );

function sm_addon_icons_enqueue( $hook ) {
	if ( strpos( $hook, 'sm-addon-icons' ) === false ) {
		return;
	}
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'sm_addon_icons_enqueue' );

function sm_addon_icons_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Spara
	if ( isset( $_POST['sm_addon_icons_save'] ) && check_admin_referer( 'sm_addon_icons' ) ) {
		$incoming = isset( $_POST['icons'] ) && is_array( $_POST['icons'] ) ? wp_unslash( $_POST['icons'] ) : array();
		$clean    = array();
		foreach ( sm_addons() as $a ) {
			$entry = array(
				'main' => isset( $incoming[ $a['id'] ]['main'] ) ? esc_url_raw( $incoming[ $a['id'] ]['main'] ) : '',
			);
			if ( ! empty( $a['variants'] ) ) {
				$entry['variants'] = array();
				foreach ( $a['variants'] as $v ) {
					$entry['variants'][ $v ] = isset( $incoming[ $a['id'] ]['variants'][ $v ] )
						? esc_url_raw( $incoming[ $a['id'] ]['variants'][ $v ] )
						: '';
				}
			}
			$clean[ $a['id'] ] = $entry;
		}
		update_option( 'sm_addon_icons', $clean );
		echo '<div class="notice notice-success"><p>Ikoner sparade.</p></div>';
	}

	$icons = get_option( 'sm_addon_icons', array() );
	?>
	<div class="wrap">
		<h1>Tillval-ikoner</h1>
		<p>Ladda upp en liten bild (helst kvadratisk, ca 200×200 px) för varje tillval. Bilden visas i anmälningsformulärets steg 3 "Tillval".</p>
		<form method="post">
			<?php wp_nonce_field( 'sm_addon_icons' ); ?>

			<?php foreach ( sm_addons() as $a ) :
				$main = $icons[ $a['id'] ]['main'] ?? '';
				?>
				<div style="background: #fff; border: 1px solid #ccd0d4; border-radius: 8px; padding: 20px; margin-bottom: 16px; max-width: 800px;">
					<h2 style="margin-top: 0;"><?php echo esc_html( $a['name'] ); ?> <span style="opacity: 0.5; font-weight: normal; font-size: 14px;">(<?php echo esc_html( $a['id'] ); ?>)</span></h2>

					<div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
						<div class="sm-icon-preview" data-target="icons[<?php echo esc_attr( $a['id'] ); ?>][main]" style="width: 80px; height: 80px; border: 1px dashed #ccd0d4; border-radius: 4px; display: flex; align-items: center; justify-content: center; background: #f9f9f9; overflow: hidden;">
							<?php if ( $main ) : ?>
								<img src="<?php echo esc_url( $main ); ?>" style="max-width: 100%; max-height: 100%;">
							<?php else : ?>
								<span style="color: #aaa; font-size: 12px;">Ingen bild</span>
							<?php endif; ?>
						</div>
						<div style="flex: 1;">
							<input type="text" name="icons[<?php echo esc_attr( $a['id'] ); ?>][main]" value="<?php echo esc_attr( $main ); ?>" style="width: 100%;" placeholder="https://...">
							<div style="margin-top: 6px;">
								<button type="button" class="button sm-icon-pick" data-target="icons[<?php echo esc_attr( $a['id'] ); ?>][main]">Välj bild</button>
								<?php if ( $main ) : ?>
									<button type="button" class="button sm-icon-clear" data-target="icons[<?php echo esc_attr( $a['id'] ); ?>][main]">Ta bort</button>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<?php if ( ! empty( $a['variants'] ) ) : ?>
						<div style="border-top: 1px solid #eee; padding-top: 14px;">
							<strong style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.08em; color: #666;">Färgvarianter</strong>
							<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; margin-top: 10px;">
								<?php foreach ( $a['variants'] as $v ) :
									$v_url = $icons[ $a['id'] ]['variants'][ $v ] ?? '';
									$field = 'icons[' . $a['id'] . '][variants][' . $v . ']';
									?>
									<div style="display: flex; align-items: center; gap: 10px; padding: 8px; background: #f6f7f7; border-radius: 4px;">
										<div class="sm-icon-preview" data-target="<?php echo esc_attr( $field ); ?>" style="width: 48px; height: 48px; border: 1px dashed #ccd0d4; border-radius: 4px; background: #fff; overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
											<?php if ( $v_url ) : ?>
												<img src="<?php echo esc_url( $v_url ); ?>" style="max-width: 100%; max-height: 100%;">
											<?php endif; ?>
										</div>
										<div style="flex: 1; min-width: 0;">
											<div style="font-weight: 600; margin-bottom: 4px;"><?php echo esc_html( $v ); ?></div>
											<input type="hidden" name="<?php echo esc_attr( $field ); ?>" value="<?php echo esc_attr( $v_url ); ?>">
											<button type="button" class="button button-small sm-icon-pick" data-target="<?php echo esc_attr( $field ); ?>"><?php echo $v_url ? 'Byt' : 'Välj bild'; ?></button>
											<?php if ( $v_url ) : ?>
												<button type="button" class="button button-small sm-icon-clear" data-target="<?php echo esc_attr( $field ); ?>">Ta bort</button>
											<?php endif; ?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>

			<p><input type="submit" name="sm_addon_icons_save" class="button button-primary" value="Spara ikoner"></p>
		</form>
	</div>

	<script>
	(function () {
		var frame;
		document.querySelectorAll('.sm-icon-pick').forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				var target = btn.dataset.target;
				var input  = document.querySelector('[name="' + target + '"]');
				var preview = btn.closest('div').parentElement.querySelector('.sm-icon-preview') ||
					document.querySelector('.sm-icon-preview[data-target="' + target + '"]');
				frame = wp.media({ title: 'Välj bild', button: { text: 'Använd bilden' }, multiple: false });
				frame.on('select', function () {
					var att = frame.state().get('selection').first().toJSON();
					if (input) input.value = att.url;
					if (preview) {
						preview.innerHTML = '<img src="' + att.url + '" style="max-width:100%;max-height:100%;">';
					}
				});
				frame.open();
			});
		});
		document.querySelectorAll('.sm-icon-clear').forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				var target = btn.dataset.target;
				var input  = document.querySelector('[name="' + target + '"]');
				var preview = btn.closest('div').parentElement.querySelector('.sm-icon-preview') ||
					document.querySelector('.sm-icon-preview[data-target="' + target + '"]');
				if (input) input.value = '';
				if (preview) preview.innerHTML = '<span style="color:#aaa;font-size:12px;">Ingen bild</span>';
			});
		});
	})();
	</script>
	<?php
}
