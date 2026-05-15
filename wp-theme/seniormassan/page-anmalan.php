<?php
/**
 * Sidmall för "Anmälan" — utställaranmälningsformulär i wizard-format.
 *
 * Layouten är en kopia av registration.jsx RegistrationWizard:
 *   - Mörkblå header med titel + stäng-knapp
 *   - Stepper med 5 numrerade flikar (Företag, Monter, Tillägg, Scen, Granska)
 *   - Ett steg synligt åt gången, switchas via JS
 *   - Sticky footer med total + Tillbaka/Nästa/Skicka
 *
 * Formuläret POSTar till samma sida; serverhanteringen ligger i
 * inc/registration-handler.php.
 */

get_header();

$ref_id = isset( $_GET['ref'] ) ? (int) $_GET['ref'] : 0;

// Bekräftelsesida
if ( $ref_id ) {
	$post = get_post( $ref_id );
	if ( $post && $post->post_type === 'sm_registration' ) {
		$company = get_post_meta( $ref_id, '_sm_company', true );
		$booths  = get_post_meta( $ref_id, '_sm_booths', true ) ?: array();
		$total   = (int) get_post_meta( $ref_id, '_sm_total', true );
		$is_for  = get_post_meta( $ref_id, '_sm_is_forening', true );
		?>
		<section style="background: var(--sm-primary); color: #fff;">
			<div class="sm-container" style="padding: 80px 32px; max-width: 720px; text-align: center;">
				<div style="font-size: 64px; margin-bottom: 16px;">✓</div>
				<h1 style="font-size: var(--sm-fs-xl); color: #fff;">Tack för din anmälan!</h1>
				<p style="font-size: var(--sm-fs-lg); opacity: 0.9; margin-top: 16px;">
					Vi har tagit emot anmälan för <strong><?php echo esc_html( $company ); ?></strong>. En bekräftelse är skickad till den angivna e-postadressen.
				</p>
				<div style="background: rgba(255,255,255,0.1); border-radius: var(--sm-radius-lg); padding: 24px; margin-top: 32px; text-align: left;">
					<div style="font-size: 13px; letter-spacing: 0.14em; text-transform: uppercase; opacity: 0.7; margin-bottom: 8px;">Referensnummer</div>
					<div style="font-family: var(--sm-font-display); font-size: 28px; font-weight: 700;">#<?php echo (int) $ref_id; ?></div>
					<div style="margin-top: 16px; font-size: 16px;">
						Valda montrar: <strong><?php echo esc_html( implode( ', ', $booths ) ); ?></strong><br>
						Preliminär totalsumma: <strong><?php echo esc_html( number_format( $total, 0, ',', "\u{00A0}" ) ); ?> kr</strong> <?php echo $is_for ? 'inkl. moms' : 'exkl. moms'; ?>
					</div>
				</div>
				<p style="margin-top: 32px; opacity: 0.8;">
					Frågor? Hör av dig till <a href="mailto:<?php echo esc_attr( sm_booking_email() ); ?>" style="color: var(--sm-gold);"><?php echo esc_html( sm_booking_email() ); ?></a>.
				</p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="sm-btn" style="background: #fff; color: var(--sm-primary); margin-top: 32px;">Tillbaka till startsidan →</a>
			</div>
		</section>
		<?php
		get_footer();
		return;
	}
}

$errors      = get_transient( 'sm_register_errors_' . sm_session_id() );
$input       = get_transient( 'sm_register_input_' . sm_session_id() ) ?: array();
$booked      = sm_booked_booth_ids();
$booked_set  = array_flip( $booked );
$taken_slots = sm_taken_stage_slots();
$map_image   = sm_booth_map_image_url();

$by_section = array();
foreach ( sm_booths() as $b ) {
	$section = substr( $b['id'], 0, 1 );
	$by_section[ $section ][] = $b;
}
ksort( $by_section );

$section_labels = array(
	'A' => 'Vid trappan',
	'B' => 'Sparbankshallen norr',
	'C' => 'Sparbankshallen östra',
	'D' => 'Mittenraden höger',
	'E' => 'Mittenraden vänster',
	'G' => 'Mitten höger',
	'H' => 'Södra mitten',
	'I' => 'Vänstra raden',
	'J' => 'Södra raden vänster',
	'K' => 'Södra raden höger',
	'L' => 'Höger om scen',
	'M' => 'Längst söderut',
	'N' => 'Entréhallen · endast föreningar',
);
$size_labels = array( '2x2' => '2 × 2 m', '2x3' => '2 × 3 m', '3x3' => '3 × 3 m' );

$prices_json = array(
	'booths'           => array(),
	'addons'           => array(),
	'registration_fee' => SM_REGISTRATION_FEE,
	'forening_price'   => SM_FORENING_PRICE,
);
foreach ( sm_booths() as $b ) {
	$prices_json['booths'][ $b['id'] ] = array( 'size' => $b['size'], 'price' => sm_booth_price( $b['id'] ) );
}
foreach ( sm_addons() as $a ) {
	$prices_json['addons'][ $a['id'] ] = array( 'name' => $a['name'], 'price' => $a['price'] );
}

$addons_by_cat = array();
foreach ( sm_addons() as $a ) {
	$addons_by_cat[ $a['cat'] ][] = $a;
}

$saved_booths     = is_array( $input['sm_booths'] ?? null ) ? $input['sm_booths'] : array();
$saved_addons     = is_array( $input['sm_addons'] ?? null ) ? $input['sm_addons'] : array();
$saved_stage      = $input['sm_stage_slot'] ?? '';
$saved_no_website = ! empty( $input['sm_no_website'] );
$saved_forening   = ! empty( $input['sm_is_forening'] );
?>

<style>
	.sm-wiz-input {
		width: 100%; padding: 14px 16px; font-size: 17px; font-family: var(--sm-font-body);
		border: 1.5px solid var(--sm-line); border-radius: 8px; background: #fff; color: var(--sm-ink);
		transition: border-color 0.15s;
	}
	.sm-wiz-input:focus { outline: none; border-color: var(--sm-primary); }
	.sm-wiz-input:disabled { background: #f5f5f5; opacity: 0.6; }
	.sm-wiz-field-label { font-size: 14px; font-weight: 700; margin-bottom: 8px; color: var(--sm-ink); display: block; }
	.sm-wiz-field-required { color: var(--sm-accent); }
	.sm-wiz-step { display: none; }
	.sm-wiz-step.is-active { display: block; }
	.sm-wiz-stepper-btn { flex: 1; padding: 18px 12px; border: none; background: transparent; border-bottom: 3px solid transparent; color: var(--sm-muted); font-weight: 500; font-size: 15px; cursor: not-allowed; display: flex; align-items: center; justify-content: center; gap: 10px; font-family: inherit; }
	.sm-wiz-stepper-btn.is-active { color: var(--sm-ink); font-weight: 700; border-bottom-color: var(--sm-accent); cursor: pointer; }
	.sm-wiz-stepper-btn.is-done { color: var(--sm-ink); cursor: pointer; }
	.sm-wiz-stepper-circle { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 13px; background: var(--sm-line); color: var(--sm-muted); font-size: 13px; font-weight: 700; }
	.sm-wiz-stepper-btn.is-active .sm-wiz-stepper-circle,
	.sm-wiz-stepper-btn.is-done .sm-wiz-stepper-circle { background: var(--sm-primary); color: #fff; }
	@media (max-width: 760px) {
		.sm-wiz-stepper-btn { padding: 12px 6px; font-size: 12px; }
		.sm-wiz-stepper-btn span:not(.sm-wiz-stepper-circle) { display: none; }
	}
</style>

<section style="background: var(--sm-bg); padding: 40px 20px; min-height: 80vh;">
	<div style="background: var(--sm-bg); width: 100%; max-width: 980px; margin: 0 auto; border-radius: var(--sm-radius-lg); box-shadow: 0 40px 80px rgba(0,0,0,0.15); overflow: hidden;">

		<!-- Header -->
		<div style="background: var(--sm-primary); color: #fff; padding: 24px 32px; display: flex; justify-content: space-between; align-items: center;">
			<div>
				<div style="font-size: 12px; letter-spacing: 0.2em; text-transform: uppercase; opacity: 0.85;">Utställaranmälan 2027</div>
				<div style="font-family: var(--sm-font-display); font-size: 26px; font-weight: 800; margin-top: 4px;">Seniormässan · Arena Varberg</div>
			</div>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Stäng" style="background: rgba(255,255,255,0.15); color: #fff; border: none; width: 40px; height: 40px; border-radius: 20px; font-size: 20px; text-decoration: none; display: flex; align-items: center; justify-content: center;">×</a>
		</div>

		<!-- Stepper -->
		<div style="display: flex; background: #fff; border-bottom: 1px solid var(--sm-line);" id="sm-wiz-stepper">
			<?php
			$steps = array( 'Företag', 'Monter', 'Tillägg', 'Scen', 'Granska' );
			foreach ( $steps as $i => $label ) :
				?>
				<button type="button" class="sm-wiz-stepper-btn<?php echo $i === 0 ? ' is-active' : ''; ?>" data-step="<?php echo (int) $i; ?>">
					<span class="sm-wiz-stepper-circle"><?php echo (int) ( $i + 1 ); ?></span>
					<span><?php echo esc_html( $label ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>

		<form method="post" action="<?php echo esc_url( get_permalink() ); ?>" id="sm-reg-form">
			<?php wp_nonce_field( 'sm_register', 'sm_register_nonce' ); ?>

			<!-- Body -->
			<div style="padding: 40px 48px; min-height: 480px; background: var(--sm-bg);">

				<?php if ( $errors && is_array( $errors ) ) : ?>
					<div style="background: #fee2e2; border: 1px solid #fecaca; border-radius: var(--sm-radius-lg); padding: 16px 20px; margin-bottom: 24px; color: #991b1b;">
						<strong>Något blev fel — korrigera och försök igen:</strong>
						<ul style="margin: 8px 0 0; padding-left: 20px;">
							<?php foreach ( $errors as $err ) : ?>
								<li><?php echo esc_html( $err ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<!-- STEG 1: FÖRETAG -->
				<div class="sm-wiz-step is-active" data-step="0">
					<h2 style="font-size: 28px; margin-bottom: 8px;">Företagsuppgifter</h2>
					<p style="color: var(--sm-ink-soft); margin-bottom: 28px;">Webbplatsen visas i den publika utställarlistan. Saknar du webbplats — bocka i rutan så hoppar fältet förbi.</p>

					<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
						<?php
						$fields_step1 = array(
							array( 'sm_company',       'Företagsnamn',          'text',  true ),
							array( 'sm_orgnr',         'Organisationsnummer',   'text',  true ),
							array( 'sm_contact_name',  'Kontaktperson',         'text',  true ),
							array( 'sm_contact_phone', 'Telefon',               'tel',   true ),
							array( 'sm_contact_email', 'E-post kontaktperson',  'email', true ),
							array( 'sm_invoice_email', 'Faktura-e-post',        'email', false ),
						);
						foreach ( $fields_step1 as $f ) :
							$value = $input[ $f[0] ] ?? '';
							?>
							<label>
								<span class="sm-wiz-field-label">
									<?php echo esc_html( $f[1] ); ?>
									<?php if ( $f[3] ) : ?><span class="sm-wiz-field-required">*</span><?php endif; ?>
								</span>
								<input type="<?php echo esc_attr( $f[2] ); ?>" name="<?php echo esc_attr( $f[0] ); ?>" value="<?php echo esc_attr( $value ); ?>" class="sm-wiz-input" <?php echo $f[3] ? 'data-required="1"' : ''; ?> data-step1-input>
							</label>
						<?php endforeach; ?>

						<label style="grid-column: 1 / -1;">
							<span class="sm-wiz-field-label">
								Webbplats <span class="sm-wiz-field-required" id="sm-website-required">*</span>
							</span>
							<input type="url" name="sm_website" id="sm-website-input" value="<?php echo esc_attr( $input['sm_website'] ?? '' ); ?>" placeholder="https://" class="sm-wiz-input" data-required="1" <?php echo $saved_no_website ? 'disabled' : ''; ?> data-step1-input>
						</label>

						<label style="grid-column: 1 / -1; display: flex; gap: 10px; align-items: center; padding: 10px 14px; background: #fff; border: 1px solid var(--sm-line); border-radius: 8px; font-size: 14px; cursor: pointer;">
							<input type="checkbox" name="sm_no_website" value="1" id="sm-no-website" <?php checked( $saved_no_website ); ?>>
							<span>Vi har ingen webbplats</span>
						</label>

						<label style="grid-column: 1 / -1;">
							<span class="sm-wiz-field-label">Fakturaadress</span>
							<textarea name="sm_invoice_address" rows="2" class="sm-wiz-input"><?php echo esc_textarea( $input['sm_invoice_address'] ?? '' ); ?></textarea>
						</label>

						<label style="grid-column: 1 / -1;">
							<span class="sm-wiz-field-label">Kort beskrivning av företaget</span>
							<textarea name="sm_description" rows="3" maxlength="320" class="sm-wiz-input" placeholder="Beskriv kort vad ni gör — max 320 tecken (frivilligt)"><?php echo esc_textarea( $input['sm_description'] ?? '' ); ?></textarea>
						</label>

						<label style="grid-column: 1 / -1; display: flex; gap: 10px; align-items: flex-start; padding: 14px; background: #fff; border: 1px solid var(--sm-line); border-radius: 8px; cursor: pointer;">
							<input type="checkbox" name="sm_is_forening" value="1" id="sm-is-forening" <?php checked( $saved_forening ); ?> style="margin-top: 4px;">
							<span>Anmälan görs av <strong>ideell förening</strong> (välj då en N-monter i nästa steg — föreningspris 2 360 kr inkl. moms)</span>
						</label>
					</div>
				</div>

				<!-- STEG 2: MONTER -->
				<div class="sm-wiz-step" data-step="1">
					<h2 style="font-size: 28px; margin-bottom: 8px;">Välj monter</h2>
					<p style="color: var(--sm-ink-soft); margin-bottom: 24px;">
						På kartan ser du montrarnas placering. Bocka i en eller flera montrar i listan nedan.
					</p>

					<?php if ( $map_image ) : ?>
						<div style="background: #fff; border: 1px solid var(--sm-line-soft); border-radius: var(--sm-radius-lg); padding: 12px; margin-bottom: 24px;">
							<a href="<?php echo esc_url( $map_image ); ?>" target="_blank" rel="noopener">
								<img src="<?php echo esc_url( $map_image ); ?>" alt="Hallplan — Seniormässan på Arena Varberg" style="width: 100%; height: auto; display: block; border-radius: 4px;">
							</a>
							<div style="margin-top: 8px; font-size: 13px; color: var(--sm-muted); text-align: center;">Klicka för större bild</div>
						</div>
					<?php else : ?>
						<div style="background: #fff; border: 1px dashed var(--sm-line); border-radius: var(--sm-radius-lg); padding: 32px; text-align: center; margin-bottom: 24px; color: var(--sm-ink-soft);">
							<strong>Hallplan saknas.</strong><br>
							<span style="font-size: 14px;">Ladda upp en bild i Utseende → Anpassa → Anmälan.</span>
						</div>
					<?php endif; ?>

					<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 20px;">
						<?php foreach ( $by_section as $section => $booths_in_section ) :
							$first       = $booths_in_section[0];
							$is_forening = ( $section === 'N' );
							$price       = $is_forening ? SM_FORENING_PRICE : SM_BOOTH_PRICES[ $first['size'] ];
							$label       = $section_labels[ $section ] ?? '';
							?>
							<div>
								<div style="font-family: var(--sm-font-display); font-size: 17px; font-weight: 800; margin-bottom: 4px;">
									Sektion <?php echo esc_html( $section ); ?> — <?php echo esc_html( $size_labels[ $first['size'] ] ); ?>
								</div>
								<div style="font-size: 12px; color: var(--sm-ink-soft); margin-bottom: 10px;">
									<?php echo esc_html( $label ); ?><br>
									<strong style="color: var(--sm-primary);"><?php echo esc_html( number_format( $price, 0, ',', "\u{00A0}" ) ); ?> kr</strong>
									<?php echo $is_forening ? ' exkl. moms' : ' / st'; ?>
								</div>
								<div style="display: flex; flex-direction: column; gap: 5px;">
									<?php foreach ( $booths_in_section as $b ) :
										$is_booked   = isset( $booked_set[ $b['id'] ] );
										$is_selected = in_array( $b['id'], $saved_booths, true );
										?>
										<label style="display: flex; align-items: center; gap: 8px; padding: 6px 10px; background: <?php echo $is_booked ? '#f5f5f5' : '#fff'; ?>; border: 1px solid var(--sm-line); border-radius: 4px; cursor: <?php echo $is_booked ? 'not-allowed' : 'pointer'; ?>; opacity: <?php echo $is_booked ? '0.55' : '1'; ?>; font-size: 14px;">
											<input type="checkbox" name="sm_booths[]" value="<?php echo esc_attr( $b['id'] ); ?>" <?php echo $is_booked ? 'disabled' : ''; ?> <?php checked( $is_selected ); ?> data-sm-booth="<?php echo esc_attr( $b['id'] ); ?>" class="sm-booth-input">
											<span style="<?php echo $is_booked ? 'text-decoration: line-through;' : ''; ?> font-weight: 600;"><?php echo esc_html( $b['id'] ); ?></span>
											<span style="margin-left: auto; font-size: 11px; color: <?php echo $is_booked ? '#999' : 'var(--sm-success)'; ?>; font-weight: 700; letter-spacing: 0.04em;">
												<?php echo $is_booked ? 'BOKAD' : 'LEDIG'; ?>
											</span>
										</label>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<!-- STEG 3: TILLÄGG -->
				<div class="sm-wiz-step" data-step="2">
					<h2 style="font-size: 28px; margin-bottom: 8px;">Tillval</h2>
					<p style="color: var(--sm-ink-soft); margin-bottom: 24px;">
						Skräddarsy din monter. Registreringsavgiften (800 kr) är obligatorisk och redan ikryssad.
					</p>

					<?php foreach ( $addons_by_cat as $cat => $list ) : ?>
						<div style="margin-bottom: 24px;">
							<div style="font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; font-size: 12px; color: var(--sm-ink-soft); margin-bottom: 10px;"><?php echo esc_html( $cat ); ?></div>
							<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 10px;">
								<?php foreach ( $list as $a ) :
									$saved_qty = isset( $saved_addons[ $a['id'] ] ) ? (int) $saved_addons[ $a['id'] ] : ( ! empty( $a['required'] ) ? 1 : 0 );
									?>
									<label style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; background: #fff; border: 1px solid var(--sm-line); border-radius: 6px;">
										<input type="number" name="sm_addons[<?php echo esc_attr( $a['id'] ); ?>]" min="<?php echo ! empty( $a['required'] ) ? '1' : '0'; ?>" max="20" value="<?php echo (int) $saved_qty; ?>" data-sm-addon="<?php echo esc_attr( $a['id'] ); ?>" class="sm-addon-input" style="width: 58px; padding: 6px 8px; border: 1px solid var(--sm-line); border-radius: 4px; font-size: 14px;">
										<span style="flex: 1;">
											<strong><?php echo esc_html( $a['name'] ); ?></strong><br>
											<span style="font-size: 12px; color: var(--sm-ink-soft);"><?php echo esc_html( $a['price'] ); ?> kr/st</span>
										</span>
									</label>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>

					<label style="display: block; margin-top: 16px;">
						<span class="sm-wiz-field-label">Särskilda önskemål</span>
						<textarea name="sm_special_requests" rows="3" class="sm-wiz-input" placeholder="Frivilligt — t.ex. placering nära fönster, plats för rullstol etc."><?php echo esc_textarea( $input['sm_special_requests'] ?? '' ); ?></textarea>
					</label>
				</div>

				<!-- STEG 4: SCEN -->
				<div class="sm-wiz-step" data-step="3">
					<h2 style="font-size: 28px; margin-bottom: 8px;">Utställarscen — boka tid</h2>
					<p style="color: var(--sm-ink-soft); margin-bottom: 24px;">
						15 minuters scenpass. Kostnadsfritt men bindande — först till kvarn. Du kan hoppa över detta steg.
					</p>

					<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: 8px;">
						<label style="display: flex; align-items: center; justify-content: center; padding: 14px; background: <?php echo ! $saved_stage ? 'var(--sm-primary-soft)' : '#fff'; ?>; border: 2px solid <?php echo ! $saved_stage ? 'var(--sm-primary)' : 'var(--sm-line)'; ?>; border-radius: 6px; cursor: pointer; font-weight: 600;">
							<input type="radio" name="sm_stage_slot" value="" <?php checked( ! $saved_stage ); ?> style="margin-right: 8px;">
							Hoppa över
						</label>
						<?php foreach ( sm_stage_slots() as $slot ) :
							$is_taken    = in_array( $slot, $taken_slots, true );
							$is_selected = ( $slot === $saved_stage );
							?>
							<label style="display: flex; flex-direction: column; align-items: center; padding: 14px; background: <?php echo $is_taken ? '#f5f5f5' : ( $is_selected ? 'var(--sm-accent)' : '#fff' ); ?>; color: <?php echo $is_selected ? '#fff' : 'inherit'; ?>; border: 2px solid <?php echo $is_taken ? '#ccc' : ( $is_selected ? 'var(--sm-accent)' : 'var(--sm-line)' ); ?>; border-radius: 6px; cursor: <?php echo $is_taken ? 'not-allowed' : 'pointer'; ?>; opacity: <?php echo $is_taken ? '0.6' : '1'; ?>; font-weight: 700;">
								<input type="radio" name="sm_stage_slot" value="<?php echo esc_attr( $slot ); ?>" <?php echo $is_taken ? 'disabled' : ''; ?> <?php checked( $is_selected ); ?> style="display: none;">
								<span style="<?php echo $is_taken ? 'text-decoration: line-through;' : ''; ?>"><?php echo esc_html( $slot ); ?></span>
								<?php if ( $is_taken ) : ?>
									<span style="font-size: 10px; opacity: 0.7; letter-spacing: 0.08em;">UPPTAGEN</span>
								<?php endif; ?>
							</label>
						<?php endforeach; ?>
					</div>
				</div>

				<!-- STEG 5: GRANSKA -->
				<div class="sm-wiz-step" data-step="4">
					<h2 style="font-size: 28px; margin-bottom: 8px;">Granska och skicka</h2>
					<p style="color: var(--sm-ink-soft); margin-bottom: 24px;">
						Kontrollera nedan att allt stämmer. Tryck sen "Skicka anmälan" — vi mejlar bekräftelse direkt.
					</p>

					<div id="sm-review-empty" style="display: none; padding: 32px; background: #fff; border: 1px dashed var(--sm-line); border-radius: 8px; text-align: center; color: var(--sm-ink-soft);">
						Gå tillbaka och fyll i företagsuppgifter samt välj minst en monter.
					</div>

					<div id="sm-review-content" style="display: grid; gap: 20px;">
						<div style="background: #fff; border: 1px solid var(--sm-line); border-radius: 8px; padding: 20px 24px;">
							<div style="text-transform: uppercase; letter-spacing: 0.14em; font-size: 12px; color: var(--sm-ink-soft); margin-bottom: 12px;">Företag</div>
							<div id="sm-review-company" style="font-size: 16px; line-height: 1.7;"></div>
						</div>

						<div style="background: #fff; border: 1px solid var(--sm-line); border-radius: 8px; padding: 20px 24px;">
							<div style="text-transform: uppercase; letter-spacing: 0.14em; font-size: 12px; color: var(--sm-ink-soft); margin-bottom: 12px;">Montrar</div>
							<div id="sm-review-booths" style="font-size: 16px; line-height: 1.8;"></div>
						</div>

						<div id="sm-review-addons-wrap" style="background: #fff; border: 1px solid var(--sm-line); border-radius: 8px; padding: 20px 24px;">
							<div style="text-transform: uppercase; letter-spacing: 0.14em; font-size: 12px; color: var(--sm-ink-soft); margin-bottom: 12px;">Tillval</div>
							<div id="sm-review-addons" style="font-size: 16px; line-height: 1.8;"></div>
						</div>

						<div id="sm-review-stage-wrap" style="background: #fff; border: 1px solid var(--sm-line); border-radius: 8px; padding: 20px 24px; display: none;">
							<div style="text-transform: uppercase; letter-spacing: 0.14em; font-size: 12px; color: var(--sm-ink-soft); margin-bottom: 12px;">Scenpass</div>
							<div id="sm-review-stage" style="font-size: 16px;"></div>
						</div>

						<div id="sm-review-tickets-wrap" style="background: var(--sm-primary-soft); border: 1px solid var(--sm-primary); border-radius: 8px; padding: 20px 24px;">
							<div style="text-transform: uppercase; letter-spacing: 0.14em; font-size: 12px; color: var(--sm-primary); margin-bottom: 8px;">Ingår</div>
							<div style="display: flex; justify-content: space-between; align-items: baseline;">
								<span style="font-size: 16px; font-weight: 600;">5 digitala entrébiljetter</span>
								<span style="color: var(--sm-primary); font-weight: 700;">0 kr</span>
							</div>
						</div>

						<div style="background: var(--sm-primary); color: #fff; border-radius: 8px; padding: 24px;">
							<div style="display: flex; justify-content: space-between; align-items: baseline;">
								<span style="font-size: 18px; opacity: 0.9;">Totalsumma <span id="sm-review-moms">exkl. moms</span></span>
								<span style="font-family: var(--sm-font-display); font-size: 36px; font-weight: 800;"><span id="sm-review-total">0</span> kr</span>
							</div>
						</div>
					</div>

					<div style="margin-top: 28px;">
						<label style="display: flex; gap: 12px; padding: 14px; background: #fff; border: 1px solid var(--sm-line); border-radius: 8px; margin-bottom: 10px; cursor: pointer;">
							<input type="checkbox" name="sm_accept_terms" value="1" required id="sm-accept-terms">
							<span>Jag har läst och godkänner utställarvillkoren.</span>
						</label>
						<label style="display: flex; gap: 12px; padding: 14px; background: #fff; border: 1px solid var(--sm-line); border-radius: 8px; cursor: pointer;">
							<input type="checkbox" name="sm_accept_gdpr" value="1" required id="sm-accept-gdpr">
							<span>Jag samtycker till att Arena Varberg behandlar mina uppgifter enligt GDPR för att hantera anmälan.</span>
						</label>
					</div>
				</div>
			</div>

			<!-- Footer -->
			<div style="padding: 20px 48px; background: #fff; border-top: 1px solid var(--sm-line); display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
				<div style="font-size: 14px; color: var(--sm-muted);">
					Total: <strong style="color: var(--sm-primary); font-size: 20px;"><span id="sm-footer-total">0</span> kr</strong> <span id="sm-footer-moms">exkl. moms</span>
				</div>
				<div style="flex: 1;"></div>
				<button type="button" class="sm-btn sm-btn--ghost sm-btn--small" id="sm-wiz-back" style="display: none;">← Tillbaka</button>
				<button type="button" class="sm-btn sm-btn--small" id="sm-wiz-next">Nästa →</button>
				<button type="submit" name="sm_register_submit" value="1" class="sm-btn sm-btn--accent sm-btn--small" id="sm-wiz-submit" style="display: none;">Skicka anmälan ✓</button>
			</div>
		</form>
	</div>
</section>

<script>
(function () {
	var data = <?php echo wp_json_encode( $prices_json ); ?>;
	var step = 0;
	var TOTAL_STEPS = 5;
	var completed = [false, false, false, false, false];
	<?php if ( $errors ) : ?>
	step = 4; // Servern returnerade fel — visa granska-steget igen
	<?php endif; ?>

	function $(id) { return document.getElementById(id); }
	function nf(n) { return n.toLocaleString('sv-SE'); }

	function readState() {
		var booths = [];
		document.querySelectorAll('.sm-booth-input:checked').forEach(function (i) { booths.push(i.value); });
		var addons = {};
		document.querySelectorAll('.sm-addon-input').forEach(function (i) {
			var qty = parseInt(i.value, 10) || 0;
			if (qty > 0) addons[i.dataset.smAddon] = qty;
		});
		var stage = '';
		var sel = document.querySelector('input[name=sm_stage_slot]:checked');
		if (sel) stage = sel.value;
		var forening = $('sm-is-forening') ? $('sm-is-forening').checked : false;
		return { booths: booths, addons: addons, stage: stage, forening: forening };
	}

	function computeTotal(s) {
		var t = 0;
		s.booths.forEach(function (id) {
			var b = data.booths[id];
			if (!b) return;
			t += (s.forening && id.charAt(0) === 'N') ? data.forening_price : b.price;
		});
		if (t > 0) t += data.registration_fee;
		Object.keys(s.addons).forEach(function (id) {
			var a = data.addons[id];
			if (a) t += a.price * s.addons[id];
		});
		if (s.forening) t = Math.round(t * 1.25);
		return t;
	}

	function canNext(currentStep, s) {
		if (currentStep === 0) {
			var required = document.querySelectorAll('[data-step1-input][data-required="1"]:not([disabled])');
			for (var i = 0; i < required.length; i++) {
				if (!required[i].value.trim()) return false;
			}
			return true;
		}
		if (currentStep === 1) return s.booths.length > 0;
		if (currentStep === 2) return true;
		if (currentStep === 3) return true;
		if (currentStep === 4) return $('sm-accept-terms').checked && $('sm-accept-gdpr').checked;
		return true;
	}

	function render() {
		// Steps
		document.querySelectorAll('.sm-wiz-step').forEach(function (el) {
			el.classList.toggle('is-active', parseInt(el.dataset.step, 10) === step);
		});
		// Stepper
		document.querySelectorAll('#sm-wiz-stepper .sm-wiz-stepper-btn').forEach(function (btn) {
			var i = parseInt(btn.dataset.step, 10);
			btn.classList.toggle('is-active', i === step);
			btn.classList.toggle('is-done', i < step || completed[i]);
			btn.disabled = (i > step && !completed[i]);
		});
		// Knappar
		$('sm-wiz-back').style.display = step > 0 ? 'inline-flex' : 'none';
		$('sm-wiz-next').style.display = step < TOTAL_STEPS - 1 ? 'inline-flex' : 'none';
		$('sm-wiz-submit').style.display = step === TOTAL_STEPS - 1 ? 'inline-flex' : 'none';

		var s = readState();
		var canProceed = canNext(step, s);
		$('sm-wiz-next').style.opacity = canProceed ? '1' : '0.4';
		$('sm-wiz-submit').style.opacity = canProceed ? '1' : '0.4';

		// Total i footer
		var total = computeTotal(s);
		$('sm-footer-total').textContent = nf(total);
		$('sm-footer-moms').textContent = s.forening ? 'inkl. moms' : 'exkl. moms';

		// Granska-stegets innehåll
		if (step === 4) renderReview(s, total);
	}

	function renderReview(s, total) {
		// Företag
		var companyEl = $('sm-review-company');
		var inputs = ['sm_company', 'sm_orgnr', 'sm_contact_name', 'sm_contact_phone', 'sm_contact_email', 'sm_website'];
		var lines = [];
		inputs.forEach(function (n) {
			var el = document.querySelector('[name=' + n + ']');
			if (!el || !el.value) return;
			lines.push('<div>' + el.value + '</div>');
		});
		if ($('sm-no-website').checked) lines.push('<div style="color:var(--sm-muted);">— Ingen webbplats</div>');
		companyEl.innerHTML = lines.join('') || '— Saknar uppgifter';

		// Båspriser
		var boothLines = [];
		s.booths.forEach(function (id) {
			var b = data.booths[id];
			if (!b) return;
			var price = (s.forening && id.charAt(0) === 'N') ? data.forening_price : b.price;
			boothLines.push('<div style="display:flex;justify-content:space-between;"><span>' + id + ' (' + b.size.replace('x', '×') + ' m)</span><span>' + nf(price) + ' kr</span></div>');
		});
		if (s.booths.length > 0) {
			boothLines.push('<div style="display:flex;justify-content:space-between;border-top:1px solid var(--sm-line);padding-top:8px;margin-top:8px;"><span>Registreringsavgift</span><span>' + nf(data.registration_fee) + ' kr</span></div>');
		}
		$('sm-review-booths').innerHTML = boothLines.join('') || '— Inga montrar valda';

		// Tillval
		var addonLines = [];
		Object.keys(s.addons).forEach(function (id) {
			var a = data.addons[id];
			if (!a) return;
			addonLines.push('<div style="display:flex;justify-content:space-between;"><span>' + a.name + ' × ' + s.addons[id] + '</span><span>' + nf(a.price * s.addons[id]) + ' kr</span></div>');
		});
		$('sm-review-addons').innerHTML = addonLines.join('') || '— Inga tillval';
		$('sm-review-addons-wrap').style.display = addonLines.length ? 'block' : 'none';

		// Scen
		var stageWrap = $('sm-review-stage-wrap');
		if (s.stage) {
			stageWrap.style.display = 'block';
			$('sm-review-stage').textContent = s.stage + ' (15 min, kostnadsfritt)';
		} else {
			stageWrap.style.display = 'none';
		}

		// Entrébiljetter — bara för icke-föreningar med minst en monter
		$('sm-review-tickets-wrap').style.display = ( ! s.forening && s.booths.length > 0 ) ? 'block' : 'none';

		// Total
		$('sm-review-total').textContent = nf(total);
		$('sm-review-moms').textContent = s.forening ? 'inkl. moms' : 'exkl. moms';

		// Visa "tomt"-meddelande om inget är ifyllt
		var anyData = s.booths.length > 0 || lines.length > 0;
		$('sm-review-empty').style.display = anyData ? 'none' : 'block';
		$('sm-review-content').style.display = anyData ? 'grid' : 'none';
	}

	function goto(newStep) {
		if (newStep < 0 || newStep >= TOTAL_STEPS) return;
		var s = readState();
		// Bara framåt om aktuellt steg är validerat
		if (newStep > step && !canNext(step, s)) return;
		if (newStep > step) completed[step] = true;
		step = newStep;
		render();
		window.scrollTo({ top: 0, behavior: 'smooth' });
	}

	// Webbplats-toggle
	function toggleWebsite() {
		var cb = $('sm-no-website');
		var input = $('sm-website-input');
		var required = $('sm-website-required');
		if (cb.checked) {
			input.disabled = true;
			input.value = '';
			input.removeAttribute('data-required');
			required.style.display = 'none';
		} else {
			input.disabled = false;
			input.setAttribute('data-required', '1');
			required.style.display = 'inline';
		}
	}

	$('sm-wiz-back').addEventListener('click', function () { goto(step - 1); });
	$('sm-wiz-next').addEventListener('click', function () { goto(step + 1); });
	document.querySelectorAll('#sm-wiz-stepper .sm-wiz-stepper-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var i = parseInt(btn.dataset.step, 10);
			if (i <= step || completed[i]) goto(i);
		});
	});
	document.addEventListener('change', function (e) {
		if (e.target.matches('.sm-booth-input, .sm-addon-input, input[name=sm_stage_slot], #sm-is-forening, #sm-accept-terms, #sm-accept-gdpr')) {
			render();
		}
		if (e.target.id === 'sm-no-website') {
			toggleWebsite();
			render();
		}
	});
	document.addEventListener('input', function (e) {
		if (e.target.matches('[data-step1-input], .sm-addon-input')) render();
	});
	toggleWebsite();
	render();
})();
</script>

<?php
delete_transient( 'sm_register_errors_' . sm_session_id() );
?>

<?php get_footer(); ?>
