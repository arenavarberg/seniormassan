<?php
/**
 * Sidmall för "Anmälan" — utställaranmälningsformulär i wizard-format.
 *
 * Matchar registration.jsx RegistrationWizard (modal-stil, stepper, sticky footer).
 */

get_header();

$ref_id  = isset( $_GET['ref'] ) ? (int) $_GET['ref'] : 0;
$updated = isset( $_GET['updated'] );

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
				<h1 style="font-size: var(--sm-fs-xl); color: #fff;"><?php echo $updated ? 'Din bokning är uppdaterad!' : 'Tack för din anmälan!'; ?></h1>
				<p style="font-size: var(--sm-fs-lg); opacity: 0.9; margin-top: 16px;">
					<?php if ( $updated ) : ?>
						Vi har sparat ändringarna för <strong><?php echo esc_html( $company ); ?></strong>. En uppdaterad bekräftelse är skickad till den angivna e-postadressen.
					<?php else : ?>
						Vi har tagit emot anmälan för <strong><?php echo esc_html( $company ); ?></strong>. En bekräftelse är skickad till den angivna e-postadressen.
					<?php endif; ?>
				</p>
				<div style="background: rgba(255,255,255,0.1); border-radius: var(--sm-radius-lg); padding: 24px; margin-top: 32px; text-align: left;">
					<div style="font-size: 16px;">
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

// Redigeringsläge: giltig boknings-ID + token (från länk eller bevarat över felomladdning).
$edit_id    = isset( $_GET['booking'] ) ? (int) $_GET['booking'] : (int) ( $input['sm_booking_id'] ?? 0 );
$edit_token = isset( $_GET['token'] ) ? sanitize_text_field( wp_unslash( $_GET['token'] ) ) : (string) ( $input['sm_edit_token'] ?? '' );
$editing    = sm_valid_edit_request( $edit_id, $edit_token );
if ( ! $editing ) {
	$edit_id    = 0;
	$edit_token = '';
}
$self_id = $editing ? $edit_id : 0;

// Förifyll formuläret från den befintliga bokningen vid färsk redigeringsladdning.
if ( $editing && empty( $input ) ) {
	$input = array(
		'sm_company'          => get_post_meta( $edit_id, '_sm_company', true ),
		'sm_orgnr'            => get_post_meta( $edit_id, '_sm_orgnr', true ),
		'sm_contact_name'     => get_post_meta( $edit_id, '_sm_contact_name', true ),
		'sm_contact_phone'    => get_post_meta( $edit_id, '_sm_contact_phone', true ),
		'sm_contact_email'    => get_post_meta( $edit_id, '_sm_contact_email', true ),
		'sm_invoice_email'    => get_post_meta( $edit_id, '_sm_invoice_email', true ),
		'sm_invoice_address'  => get_post_meta( $edit_id, '_sm_invoice_address', true ),
		'sm_website'          => get_post_meta( $edit_id, '_sm_website', true ),
		'sm_no_website'       => get_post_meta( $edit_id, '_sm_no_website', true ),
		'sm_special_requests' => get_post_meta( $edit_id, '_sm_special_requests', true ),
		'sm_booths'           => get_post_meta( $edit_id, '_sm_booths', true ) ?: array(),
		'sm_addons'           => get_post_meta( $edit_id, '_sm_addons', true ) ?: array(),
		'sm_addon_variants'   => get_post_meta( $edit_id, '_sm_addon_variants', true ) ?: array(),
		'sm_stage_slot'       => get_post_meta( $edit_id, '_sm_stage_slot', true ),
		'sm_is_forening'      => get_post_meta( $edit_id, '_sm_is_forening', true ),
	);
}

$booked      = sm_booked_booth_ids( $self_id );
$booked_set  = array_flip( $booked );
$taken_slots = sm_taken_stage_slots( $self_id );
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
	'registration_fee' => sm_get_registration_fee(),
	'forening_price'   => sm_get_forening_price(),
);
foreach ( sm_booths() as $b ) {
	$prices_json['booths'][ $b['id'] ] = array( 'size' => $b['size'], 'price' => sm_booth_price( $b['id'] ) );
}
foreach ( sm_addons() as $a ) {
	$prices_json['addons'][ $a['id'] ] = array(
		'name'              => $a['name'],
		'price'             => $a['price'],
		'variants'          => $a['variants'] ?? array(),
		'scales_with_booths' => ! empty( $a['scales_with_booths'] ),
	);
}

$addons_by_cat = array();
foreach ( sm_addons() as $a ) {
	$addons_by_cat[ $a['cat'] ][] = $a;
}

$saved_booths     = is_array( $input['sm_booths'] ?? null ) ? $input['sm_booths'] : array();
$saved_addons     = is_array( $input['sm_addons'] ?? null ) ? $input['sm_addons'] : array();
$saved_variants   = is_array( $input['sm_addon_variants'] ?? null ) ? $input['sm_addon_variants'] : array();
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
	.sm-wiz-input.is-invalid { border-color: #dc2626; background: #fef2f2; }
	.sm-wiz-input.is-invalid:focus { border-color: #dc2626; }
	.sm-wiz-stepper-btn:disabled { cursor: default; }
	.sm-wiz-field-label { font-size: 14px; font-weight: 700; margin-bottom: 8px; color: var(--sm-ink); display: block; }
	.sm-wiz-field-required { color: var(--sm-accent); }
	.sm-wiz-step { display: none; }
	.sm-wiz-step.is-active { display: block; }
	.sm-wiz-stepper-btn { flex: 1; padding: 18px 12px; border: none; background: transparent; border-bottom: 3px solid transparent; color: var(--sm-muted); font-weight: 500; font-size: 15px; cursor: default; display: flex; align-items: center; justify-content: center; gap: 10px; font-family: inherit; }
	.sm-wiz-stepper-btn.is-active { color: var(--sm-ink); font-weight: 700; border-bottom-color: var(--sm-accent); cursor: pointer; }
	.sm-wiz-stepper-btn.is-done { color: var(--sm-ink); cursor: pointer; }
	.sm-wiz-stepper-circle { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 13px; background: var(--sm-line); color: var(--sm-muted); font-size: 13px; font-weight: 700; }
	.sm-wiz-stepper-btn.is-active .sm-wiz-stepper-circle,
	.sm-wiz-stepper-btn.is-done .sm-wiz-stepper-circle { background: var(--sm-primary); color: #fff; }
	@media (max-width: 760px) {
		.sm-wiz-stepper-btn { padding: 12px 6px; font-size: 12px; }
		.sm-wiz-stepper-btn span:not(.sm-wiz-stepper-circle) { display: none; }
	}
	@media (max-width: 600px) {
		.sm-wiz-header { padding: 18px 18px !important; }
		.sm-wiz-header-title { font-size: 19px !important; }
		.sm-wiz-body { padding: 28px 18px !important; min-height: 0 !important; }
		.sm-wiz-grid-2 { grid-template-columns: 1fr !important; }
		.sm-review-grid { grid-template-columns: 1fr !important; }
		.sm-stage-grid { grid-template-columns: repeat(2, 1fr) !important; }
		#sm-booth-summary { grid-template-columns: 1fr !important; gap: 16px !important; padding: 20px !important; }
		#sm-booth-summary > div:last-child { text-align: left !important; }
		.sm-wiz-footer { padding: 16px 18px !important; gap: 10px !important; }
		.sm-wiz-total { flex: 1 1 100%; }
		.sm-wiz-spacer { display: none; }
		.sm-wiz-footer .sm-btn { flex: 1 1 auto; }
		.sm-wiz-footer #sm-wiz-back { flex: 0 0 auto; }
	}

	/* Collapsible monter-sektioner */
	.sm-section-toggle {
		display: flex; justify-content: space-between; align-items: center;
		width: 100%; padding: 14px 18px; background: #fff;
		border: 1px solid var(--sm-line); border-radius: 8px; cursor: pointer;
		font-family: inherit; text-align: left;
	}
	.sm-section-toggle:hover { background: #fafafa; }
	.sm-section-toggle[aria-expanded="true"] { border-bottom-left-radius: 0; border-bottom-right-radius: 0; }
	.sm-section-toggle .sm-chevron { transition: transform 0.2s; }
	.sm-section-toggle[aria-expanded="true"] .sm-chevron { transform: rotate(90deg); }
	.sm-section-body {
		display: none;
		background: #fff; border: 1px solid var(--sm-line); border-top: none;
		border-radius: 0 0 8px 8px; padding: 16px 18px;
	}
	.sm-section-body.is-open { display: block; }

	/* Addon-kort med ikon */
	.sm-addon-card {
		display: flex; gap: 14px; padding: 14px; background: #fff;
		border: 1px solid var(--sm-line); border-radius: 8px; align-items: center;
	}
	.sm-addon-icon {
		width: 56px; height: 56px; border-radius: 6px; background: var(--sm-bg);
		flex-shrink: 0; display: flex; align-items: center; justify-content: center;
		overflow: hidden;
	}
	.sm-addon-icon img { max-width: 100%; max-height: 100%; }
	.sm-addon-icon-placeholder { font-size: 24px; color: var(--sm-muted); }
	.sm-variant-grid {
		display: grid; grid-template-columns: repeat(auto-fit, minmax(64px, 1fr));
		gap: 6px; margin-top: 8px;
	}
	.sm-variant-btn {
		display: flex; flex-direction: column; align-items: center; gap: 4px;
		padding: 6px 4px; border: 2px solid var(--sm-line); background: #fff;
		border-radius: 6px; cursor: pointer; font-family: inherit; font-size: 12px;
	}
	.sm-variant-btn.is-selected { border-color: var(--sm-accent); background: var(--sm-accent-soft); }
	.sm-variant-btn img { width: 28px; height: 28px; border-radius: 4px; }
	.sm-variant-btn.is-selected { font-weight: 700; }

	/* Stage time buttons */
	.sm-stage-btn {
		padding: 16px 8px; border-radius: 8px; font-size: 18px; font-weight: 700;
		border: 1px solid var(--sm-line); background: #fff; cursor: pointer;
		transition: all 0.15s; font-family: inherit;
	}
	.sm-stage-btn:hover:not(:disabled) { border-color: var(--sm-primary); }
	.sm-stage-btn.is-selected { border: 2px solid var(--sm-accent); background: var(--sm-accent); color: #fff; }
	.sm-stage-btn:disabled { background: #f3f3f3; color: var(--sm-muted); text-decoration: line-through; cursor: not-allowed; opacity: 0.6; }
</style>

<section style="background: var(--sm-bg); padding: 40px 20px; min-height: 80vh;">
	<div style="background: var(--sm-bg); width: 100%; max-width: 980px; margin: 0 auto; border-radius: var(--sm-radius-lg); box-shadow: 0 40px 80px rgba(0,0,0,0.15); overflow: hidden;">

		<!-- Header -->
		<div class="sm-wiz-header" style="background: var(--sm-primary); color: #fff; padding: 24px 32px; display: flex; justify-content: space-between; align-items: center; gap: 12px;">
			<div>
				<div style="font-size: 12px; letter-spacing: 0.2em; text-transform: uppercase; opacity: 0.85;">Utställaranmälan 2027</div>
				<div class="sm-wiz-header-title" style="font-family: var(--sm-font-display); font-size: 26px; font-weight: 800; margin-top: 4px;">Seniormässan · Arena Varberg</div>
			</div>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Stäng" style="background: rgba(255,255,255,0.15); color: #fff; border: none; width: 40px; height: 40px; border-radius: 20px; font-size: 20px; text-decoration: none; display: flex; align-items: center; justify-content: center;">×</a>
		</div>

		<!-- Stepper -->
		<div style="display: flex; background: #fff; border-bottom: 1px solid var(--sm-line);" id="sm-wiz-stepper">
			<?php
			$steps = array( 'Monter', 'Tillägg', 'Scen', 'Företag', 'Granska' );
			foreach ( $steps as $i => $label ) :
				?>
				<button type="button" class="sm-wiz-stepper-btn<?php echo $i === 0 ? ' is-active' : ''; ?>" data-step="<?php echo (int) $i; ?>">
					<span class="sm-wiz-stepper-circle"><?php echo (int) ( $i + 1 ); ?></span>
					<span><?php echo esc_html( $label ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="sm-reg-form" novalidate>
			<input type="hidden" name="action" value="sm_register">
			<input type="hidden" name="sm_is_forening" id="sm-is-forening" value="<?php echo $saved_forening ? '1' : ''; ?>">
			<?php if ( $editing ) : ?>
				<input type="hidden" name="sm_booking_id" value="<?php echo (int) $edit_id; ?>">
				<input type="hidden" name="sm_edit_token" value="<?php echo esc_attr( $edit_token ); ?>">
			<?php endif; ?>
			<?php wp_nonce_field( 'sm_register', 'sm_register_nonce' ); ?>

			<!-- Body -->
			<div class="sm-wiz-body" style="padding: 40px 48px; min-height: 480px; background: var(--sm-bg);">

				<?php if ( $editing ) : ?>
					<div style="background: var(--sm-primary-soft); border: 1px solid var(--sm-primary); border-radius: var(--sm-radius-lg); padding: 16px 20px; margin-bottom: 24px; color: var(--sm-primary);">
						<strong>Du redigerar en befintlig bokning.</strong> Ändra det du vill och gå vidare till sista steget för att spara. Dina nuvarande montrar och scenpass är fortfarande reserverade för dig.
					</div>
				<?php endif; ?>

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

				<div id="sm-step-error" style="display: none; padding: 12px 16px; background: #fee2e2; border: 1px solid #fecaca; border-radius: var(--sm-radius-lg); color: #991b1b; font-size: 15px; margin-bottom: 24px;"></div>

				<!-- STEG 4: FÖRETAG -->
				<div class="sm-wiz-step" data-step="3">
					<h2 style="font-size: 28px; margin-bottom: 8px;">Företagsuppgifter</h2>
					<p style="color: var(--sm-ink-soft); margin-bottom: 28px;">Webbplatsen visas i den publika utställarlistan. Saknar du webbplats — bocka i rutan så hoppar fältet förbi.</p>

					<div class="sm-wiz-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
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

						<label style="grid-column: 1 / -1; display: flex; gap: 10px; align-items: center; padding: 10px 14px; background: #fff; border: 1px solid var(--sm-line); border-radius: 8px; font-size: 14px; cursor: pointer;">
							<input type="checkbox" name="sm_no_website" value="1" id="sm-no-website" <?php checked( $saved_no_website ); ?>>
							<span>Vi har ingen webbplats</span>
						</label>

						<label style="grid-column: 1 / -1;">
							<span class="sm-wiz-field-label">
								Webbplats <span class="sm-wiz-field-required" id="sm-website-required">*</span>
							</span>
							<input type="text" name="sm_website" id="sm-website-input" value="<?php echo esc_attr( $input['sm_website'] ?? '' ); ?>" placeholder="t.ex. www.exempel.se" class="sm-wiz-input" data-required="1" <?php echo $saved_no_website ? 'disabled' : ''; ?> data-step1-input>
						</label>

						<label style="grid-column: 1 / -1;">
							<span class="sm-wiz-field-label">Fakturaadress</span>
							<textarea name="sm_invoice_address" rows="2" class="sm-wiz-input"><?php echo esc_textarea( $input['sm_invoice_address'] ?? '' ); ?></textarea>
						</label>

					</div>
				</div>

				<!-- STEG 1: MONTER -->
				<div class="sm-wiz-step is-active" data-step="0">
					<h2 style="font-size: 28px; margin-bottom: 8px;">Välj monter</h2>
					<p style="color: var(--sm-ink-soft); margin-bottom: 20px;">
						På kartan ser du montrarnas placering. Klicka på en sektion nedan för att visa lediga montrar och bocka i en eller flera.
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

					<div style="display: grid; gap: 10px;">
						<?php foreach ( $by_section as $section => $booths_in_section ) :
							$first       = $booths_in_section[0];
							$is_forening = ( $section === 'N' );
							$price       = $is_forening ? sm_get_forening_price() : sm_get_booth_price_for_size( $first['size'] );
							$label       = $section_labels[ $section ] ?? '';
							$available   = 0;
							foreach ( $booths_in_section as $b ) {
								if ( ! isset( $booked_set[ $b['id'] ] ) ) {
									$available++;
								}
							}
							$total_in_section = count( $booths_in_section );
							?>
							<div>
								<button type="button" class="sm-section-toggle" aria-expanded="false" data-section="<?php echo esc_attr( $section ); ?>">
									<div>
										<div style="font-family: var(--sm-font-display); font-weight: 800; font-size: 17px;">
											Sektion <?php echo esc_html( $section ); ?> — <?php echo esc_html( $size_labels[ $first['size'] ] ); ?>
											<span style="font-weight: 400; font-size: 13px; color: var(--sm-ink-soft); margin-left: 6px;"><?php echo esc_html( $label ); ?></span>
										</div>
										<div style="font-size: 13px; color: var(--sm-ink-soft); margin-top: 2px;">
											<strong style="color: var(--sm-primary);"><?php echo esc_html( number_format( $price, 0, ',', "\u{00A0}" ) ); ?> kr</strong><?php echo $is_forening ? ' inkl. moms' : ' / st exkl. moms'; ?>
											· <span style="color: var(--sm-success); font-weight: 700;"><?php echo (int) $available; ?> lediga</span> av <?php echo (int) $total_in_section; ?>
										</div>
									</div>
									<span class="sm-chevron" style="font-size: 18px; color: var(--sm-muted);">›</span>
								</button>
								<div class="sm-section-body" data-section="<?php echo esc_attr( $section ); ?>">
									<?php if ( $is_forening ) : ?>
											<div class="sm-forening-gate" style="margin-bottom: 12px;">
												<div class="sm-forening-q" style="<?php echo $saved_forening ? 'display:none;' : ''; ?> background: var(--sm-accent-soft); border: 1px solid var(--sm-accent); border-radius: 8px; padding: 14px 16px;">
													<div style="font-weight: 700; margin-bottom: 4px;">Endast för föreningar</div>
													<p style="margin: 0 0 12px; font-size: 14px; color: var(--sm-ink-soft);">Montrarna i entréhallen (N) är reserverade för föreningar till föreningspris. Är ni en förening?</p>
													<div style="display: flex; gap: 8px; flex-wrap: wrap;">
														<button type="button" class="sm-btn sm-btn--small" data-forening="yes">Ja, vi är en förening</button>
														<button type="button" class="sm-btn sm-btn--ghost sm-btn--small" data-forening="no">Nej</button>
													</div>
												</div>
												<div class="sm-forening-yes" style="<?php echo $saved_forening ? '' : 'display:none;'; ?> background: #e6f4ea; border: 1px solid var(--sm-success); border-radius: 8px; padding: 10px 14px; font-size: 14px;">
													✓ Föreningspris tillämpas. Välj en N-monter nedan.
													<button type="button" data-forening="reset" style="background:none;border:none;color:var(--sm-primary);text-decoration:underline;cursor:pointer;font-size:14px;padding:0;margin-left:6px;">Ångra</button>
												</div>
												<div class="sm-forening-no" style="display:none; background: #f4f4f6; border: 1px solid #e0e0e6; border-radius: 8px; padding: 10px 14px; font-size: 14px; color: var(--sm-ink-soft);">
													Montrarna i entréhallen (N) är endast för föreningar. Välj en monter i ett annat område, eller
													<button type="button" data-forening="reset" style="background:none;border:none;color:var(--sm-primary);text-decoration:underline;cursor:pointer;font-size:14px;padding:0;margin-left:2px;">ändra svar</button>.
												</div>
											</div>
										<?php endif; ?>
										<div class="sm-booth-grid" style="display: <?php echo ( $is_forening && ! $saved_forening ) ? 'none' : 'grid'; ?>; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: 6px;">
										<?php foreach ( $booths_in_section as $b ) :
											$is_booked   = isset( $booked_set[ $b['id'] ] );
											$is_selected = in_array( $b['id'], $saved_booths, true );
											?>
											<label style="display: flex; align-items: center; gap: 8px; padding: 6px 10px; background: <?php echo $is_booked ? '#f5f5f5' : 'var(--sm-bg)'; ?>; border: 1px solid var(--sm-line); border-radius: 4px; cursor: <?php echo $is_booked ? 'not-allowed' : 'pointer'; ?>; opacity: <?php echo $is_booked ? '0.55' : '1'; ?>; font-size: 14px;">
												<input type="checkbox" name="sm_booths[]" value="<?php echo esc_attr( $b['id'] ); ?>" <?php echo $is_booked ? 'disabled' : ''; ?> <?php checked( $is_selected ); ?> data-sm-booth="<?php echo esc_attr( $b['id'] ); ?>" class="sm-booth-input">
												<span style="<?php echo $is_booked ? 'text-decoration: line-through;' : ''; ?> font-weight: 600;"><?php echo esc_html( $b['id'] ); ?></span>
												<span style="margin-left: auto; font-size: 11px; color: <?php echo $is_booked ? '#999' : 'var(--sm-success)'; ?>; font-weight: 700; letter-spacing: 0.04em;">
													<?php echo $is_booked ? 'BOKAD' : 'LEDIG'; ?>
												</span>
											</label>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>

					<!-- Live-summering längst ner (matchar designens StepBooth) -->
					<div id="sm-booth-summary" style="margin-top: 24px; background: var(--sm-primary); color: #fff; border-radius: 12px; padding: 24px 28px; display: none; grid-template-columns: 1fr auto; align-items: center; gap: 24px;">
						<div>
							<div style="font-size: 12px; letter-spacing: 0.1em; text-transform: uppercase; opacity: 0.8;">Dina valda montrar</div>
							<div id="sm-booth-chips" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 6px;"></div>
							<button type="button" id="sm-booth-clear" style="margin-top: 12px; background: transparent; border: 1px solid rgba(255,255,255,0.4); color: #fff; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 13px;">Rensa val</button>
						</div>
						<div style="text-align: right;">
							<div style="font-size: 12px; opacity: 0.8; letter-spacing: 0.1em; text-transform: uppercase;">Montrar</div>
							<div style="font-family: var(--sm-font-display); font-size: 36px; font-weight: 800; line-height: 1; margin-top: 4px;">
								<span id="sm-booth-summary-total">0</span> <span style="font-size: 16px; font-weight: 500;">kr</span>
							</div>
							<div style="font-size: 12px; opacity: 0.75; margin-top: 4px;">+ <?php echo (int) sm_get_registration_fee(); ?> kr reg.avg · <span id="sm-booth-summary-moms">exkl. moms</span></div>
						</div>
					</div>
					<div id="sm-booth-summary-empty" style="margin-top: 24px; border: 2px dashed var(--sm-line); border-radius: 12px; padding: 32px 24px; text-align: center; color: var(--sm-ink-soft); font-size: 16px;">
						Inga montrar valda än. Bocka i minst en monter ovan för att komma vidare.
					</div>
				</div>

				<!-- STEG 2: TILLÄGG -->
				<div class="sm-wiz-step" data-step="1">
					<h2 style="font-size: 28px; margin-bottom: 8px;">Tillval</h2>
					<p style="color: var(--sm-ink-soft); margin-bottom: 24px;">
						Skräddarsy din monter. Registreringsavgift (<?php echo (int) sm_get_registration_fee(); ?> kr) ingår automatiskt och visas i sammanfattningen.
					</p>

					<?php foreach ( $addons_by_cat as $cat => $list ) : ?>
						<div style="margin-bottom: 24px;">
							<div style="font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; font-size: 12px; color: var(--sm-ink-soft); margin-bottom: 10px;"><?php echo esc_html( $cat ); ?></div>
							<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 10px;">
								<?php foreach ( $list as $a ) :
									$saved_qty     = isset( $saved_addons[ $a['id'] ] ) ? (int) $saved_addons[ $a['id'] ] : 0;
									$saved_variant = $saved_variants[ $a['id'] ] ?? '';
									$icon_url      = sm_addon_icon( $a['id'] );
									$has_variants  = ! empty( $a['variants'] );
									$scales        = ! empty( $a['scales_with_booths'] );
									$price_label   = $a['price_label'] ?? 'kr/st';
									?>
									<div class="sm-addon-card" data-addon-id="<?php echo esc_attr( $a['id'] ); ?>"<?php echo $scales ? ' data-scales="1"' : ''; ?>>
										<div class="sm-addon-icon">
											<?php if ( $icon_url ) : ?>
												<img src="<?php echo esc_url( $icon_url ); ?>" alt="">
											<?php else : ?>
												<span class="sm-addon-icon-placeholder">·</span>
											<?php endif; ?>
										</div>
										<div style="flex: 1; min-width: 0;">
											<div style="font-weight: 700;"><?php echo esc_html( $a['name'] ); ?></div>
											<div style="font-size: 12px; color: var(--sm-ink-soft);"><?php echo esc_html( $a['price'] ); ?> <?php echo esc_html( $price_label ); ?></div>

											<?php if ( $has_variants ) : ?>
												<div class="sm-variant-grid" data-variant-for="<?php echo esc_attr( $a['id'] ); ?>">
													<?php if ( $scales ) : ?>
														<button type="button" class="sm-variant-btn<?php echo ! $saved_variant ? ' is-selected' : ''; ?>" data-variant="">
															<span style="font-size: 18px;">✕</span>
															<span>Ingen</span>
														</button>
													<?php endif; ?>
													<?php foreach ( $a['variants'] as $v ) :
														$v_icon = sm_addon_icon( $a['id'], $v );
														$is_v_sel = ( $saved_variant === $v );
														?>
														<button type="button" class="sm-variant-btn<?php echo $is_v_sel ? ' is-selected' : ''; ?>" data-variant="<?php echo esc_attr( $v ); ?>">
															<?php if ( $v_icon ) : ?>
																<img src="<?php echo esc_url( $v_icon ); ?>" alt="">
															<?php endif; ?>
															<span><?php echo esc_html( $v ); ?></span>
														</button>
													<?php endforeach; ?>
													<input type="hidden" name="sm_addon_variants[<?php echo esc_attr( $a['id'] ); ?>]" value="<?php echo esc_attr( $saved_variant ); ?>" class="sm-variant-value">
												</div>
											<?php endif; ?>

											<?php if ( $scales ) : ?>
												<div style="font-size: 12px; color: var(--sm-muted); margin-top: 8px;">
													Antal m² räknas automatiskt från dina valda montrar (4 m² per 2×2, 6 m² per 2×3, 9 m² per 3×3).
												</div>
											<?php endif; ?>
										</div>

										<?php if ( ! $scales ) : ?>
											<div style="display: flex; align-items: center; gap: 4px; flex-shrink: 0;">
												<button type="button" class="button sm-qty-btn" data-action="dec" aria-label="Minska" style="width: 30px; height: 30px; border: 1px solid var(--sm-line); background: #fff; border-radius: 4px; cursor: pointer; font-size: 16px;">−</button>
												<input type="number" name="sm_addons[<?php echo esc_attr( $a['id'] ); ?>]" min="0" max="20" value="<?php echo (int) $saved_qty; ?>" data-sm-addon="<?php echo esc_attr( $a['id'] ); ?>" class="sm-addon-input" style="width: 48px; padding: 4px; border: 1px solid var(--sm-line); border-radius: 4px; font-size: 14px; text-align: center;">
												<button type="button" class="button sm-qty-btn" data-action="inc" aria-label="Öka" style="width: 30px; height: 30px; border: 1px solid var(--sm-line); background: #fff; border-radius: 4px; cursor: pointer; font-size: 16px;">+</button>
											</div>
										<?php else : ?>
											<input type="hidden" name="sm_addons[<?php echo esc_attr( $a['id'] ); ?>]" value="<?php echo (int) $saved_qty; ?>" data-sm-addon="<?php echo esc_attr( $a['id'] ); ?>" class="sm-addon-input sm-addon-input-auto">
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>

					<label style="display: block; margin-top: 16px;">
						<span class="sm-wiz-field-label">Särskilda önskemål</span>
						<textarea name="sm_special_requests" rows="3" class="sm-wiz-input" placeholder="Frivilligt — t.ex. placering nära fönster, plats för rullstol etc."><?php echo esc_textarea( $input['sm_special_requests'] ?? '' ); ?></textarea>
					</label>
				</div>

				<!-- STEG 3: SCEN (matchar designens StepStage) -->
				<div class="sm-wiz-step" data-step="2">
					<h2 style="font-size: 28px; margin-bottom: 8px;">Utställarscen — boka tid (frivilligt)</h2>
					<p style="color: var(--sm-ink-soft); margin-bottom: 24px; max-width: 720px; line-height: 1.55;">
						Ta chansen och nå ut med ditt budskap i 15 min på utställarscenen. Skärm för presentation och ljud finns på plats. Utställarscenens program syns i vår marknadsföring.
						<br><br>
						<strong>Kostnadsfri bindande anmälan</strong> — vid avbokning kan kostnader tillkomma.
					</p>

					<div style="background: #fff; border: 1px solid var(--sm-line); border-radius: 10px; padding: 24px;">
						<div style="font-size: 13px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; color: var(--sm-muted); margin-bottom: 16px;">
							Lediga tider · <?php echo esc_html( sm_event_date_long() ); ?>
						</div>
						<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;" class="sm-stage-grid">
							<?php foreach ( sm_stage_slots() as $slot ) :
								$is_taken    = in_array( $slot, $taken_slots, true );
								$is_selected = ( $slot === $saved_stage );
								?>
								<button type="button" class="sm-stage-btn<?php echo $is_selected ? ' is-selected' : ''; ?>" data-slot="<?php echo esc_attr( $slot ); ?>" <?php echo $is_taken ? 'disabled' : ''; ?>>
									<?php echo esc_html( $slot ); ?>
									<?php if ( $is_taken ) : ?>
										<div style="font-size: 11px; font-weight: 500; margin-top: 4px; letter-spacing: 0.04em;">UPPTAGEN</div>
									<?php endif; ?>
								</button>
							<?php endforeach; ?>
						</div>
						<input type="hidden" name="sm_stage_slot" id="sm-stage-slot-value" value="<?php echo esc_attr( $saved_stage ); ?>">

						<div id="sm-stage-selected" style="margin-top: 20px; padding: 14px; background: var(--sm-bg); border-radius: 8px; font-size: 15px; display: <?php echo $saved_stage ? 'flex' : 'none'; ?>; justify-content: space-between; align-items: center;">
							<span>Vald tid: <strong id="sm-stage-selected-time"><?php echo esc_html( $saved_stage ); ?></strong> · 15 min</span>
							<button type="button" id="sm-stage-clear" style="background: transparent; border: none; color: var(--sm-ink-soft); cursor: pointer; font-size: 14px; text-decoration: underline;">Ta bort</button>
						</div>
						<div id="sm-stage-empty" style="margin-top: 20px; font-size: 14px; color: var(--sm-muted); display: <?php echo $saved_stage ? 'none' : 'block'; ?>;">
							Ingen tid vald — du kan hoppa över detta steg om du inte vill boka scen.
						</div>
					</div>
				</div>

				<!-- STEG 5: GRANSKA (matchar designens StepReview) -->
				<div class="sm-wiz-step" data-step="4">
					<h2 style="font-size: 28px; margin-bottom: 8px;">Granska och skicka</h2>
					<p style="color: var(--sm-ink-soft); margin-bottom: 24px;">Du får en bekräftelse på e-post direkt.</p>

					<div id="sm-review-empty" style="display: none; padding: 32px; background: #fff; border: 1px dashed var(--sm-line); border-radius: 8px; text-align: center; color: var(--sm-ink-soft);">
						Gå tillbaka och fyll i företagsuppgifter samt välj minst en monter.
					</div>

					<div id="sm-review-content">
						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;" class="sm-review-grid">
							<div style="background: #fff; border: 1px solid var(--sm-line); border-radius: 10px; padding: 20px;">
								<div style="font-size: 13px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; color: var(--sm-muted); margin-bottom: 12px;">Företag</div>
								<div id="sm-review-company"></div>
							</div>
							<div style="background: #fff; border: 1px solid var(--sm-line); border-radius: 10px; padding: 20px;">
								<div style="font-size: 13px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; color: var(--sm-muted); margin-bottom: 12px;" id="sm-review-booths-title">Montrar</div>
								<div id="sm-review-booths"></div>
							</div>
						</div>

						<div id="sm-review-addons-wrap" style="margin-top: 24px; padding: 20px; background: #fff; border: 1px solid var(--sm-line); border-radius: 10px; display: none;">
							<div style="font-size: 13px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; color: var(--sm-muted); margin-bottom: 12px;">Tillägg</div>
							<div id="sm-review-addons"></div>
						</div>

						<div id="sm-review-stage-wrap" style="margin-top: 24px; padding: 20px; background: #fff; border: 1px solid var(--sm-line); border-radius: 10px; display: none;">
							<div style="font-size: 13px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; color: var(--sm-muted); margin-bottom: 8px;">Utställarscen</div>
							<div id="sm-review-stage" style="font-size: 17px;"></div>
							<div style="font-size: 13px; color: var(--sm-ink-soft); margin-top: 4px;">Kostnadsfritt · bindande anmälan</div>
						</div>

						<div id="sm-review-tickets-wrap" style="margin-top: 24px; padding: 16px 20px; background: var(--sm-primary-soft); border: 1px solid var(--sm-primary); border-radius: 10px;">
							<div style="display: flex; justify-content: space-between; align-items: baseline;">
								<span style="font-size: 16px;"><strong>5 digitala entrébiljetter</strong> — bjud in dina kunder</span>
								<span style="color: var(--sm-primary); font-weight: 700;">0 kr</span>
							</div>
						</div>

						<div style="margin-top: 24px; padding: 20px 24px; background: var(--sm-primary); color: #fff; border-radius: 10px; display: flex; justify-content: space-between; align-items: center;">
							<span style="font-size: 18px;">Totalsumma <span id="sm-review-moms">exkl. moms</span></span>
							<span style="font-family: var(--sm-font-display); font-size: 32px; font-weight: 800;"><span id="sm-review-total">0</span> kr</span>
						</div>

						<div style="margin-top: 28px; display: flex; flex-direction: column; gap: 12px;">
							<label style="display: flex; gap: 12px; cursor: pointer; align-items: flex-start; padding: 8px 0;">
								<input type="checkbox" name="sm_accept_terms" value="1" id="sm-accept-terms" style="width: 22px; height: 22px; margin-top: 2px; accent-color: var(--sm-primary);">
								<span style="font-size: 16px;">Jag godkänner <a href="<?php echo esc_url( home_url( '/villkor/' ) ); ?>" target="_blank" rel="noopener" style="color: var(--sm-primary); text-decoration: underline;">utställarvillkoren</a> och faktureringsvillkoren (30 dagar netto). <span style="opacity:0.65; font-size:13px;">(länken öppnas i en ny flik)</span></span>
							</label>
							<label style="display: flex; gap: 12px; cursor: pointer; align-items: flex-start; padding: 8px 0;">
								<input type="checkbox" name="sm_accept_gdpr" value="1" id="sm-accept-gdpr" style="width: 22px; height: 22px; margin-top: 2px; accent-color: var(--sm-primary);">
								<span style="font-size: 16px;">Jag samtycker till att uppgifterna hanteras enligt vår <a href="<?php echo esc_url( home_url( '/integritet/' ) ); ?>" target="_blank" rel="noopener" style="color: var(--sm-primary); text-decoration: underline;">integritetspolicy</a> och visas i utställarlistan på webben.</span>
							</label>
							<div id="sm-submit-error" style="display: none; padding: 12px 16px; background: #fee2e2; border: 1px solid #fecaca; border-radius: 8px; color: #991b1b; font-size: 14px; margin-top: 8px;">
								Bocka i båda rutorna ovan för att skicka anmälan.
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Footer -->
			<div class="sm-wiz-footer" style="padding: 20px 48px; background: #fff; border-top: 1px solid var(--sm-line); display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
				<div class="sm-wiz-total" style="font-size: 14px; color: var(--sm-muted);">
					Total: <strong style="color: var(--sm-primary); font-size: 20px;"><span id="sm-footer-total">0</span> kr</strong> <span id="sm-footer-moms">exkl. moms</span>
				</div>
				<div class="sm-wiz-spacer" style="flex: 1;"></div>
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
	step = 4;
	<?php endif; ?>

	function $(id) { return document.getElementById(id); }
	function nf(n) { return n.toLocaleString('sv-SE'); }
	function smSqm(size) { var p = String(size).split('x'); return (parseInt(p[0], 10) || 0) * (parseInt(p[1], 10) || 0); }

	function readState() {
		var booths = [];
		document.querySelectorAll('.sm-booth-input:checked').forEach(function (i) { booths.push(i.value); });
		var variants = {};
		document.querySelectorAll('.sm-variant-value').forEach(function (i) {
			var name = i.name.match(/\[([^\]]+)\]/);
			if (name && i.value) variants[name[1]] = i.value;
		});

		// Uppdatera auto-skalande tillval (matta) innan vi läser kvantiteter
		Object.keys(data.addons).forEach(function (id) {
			var info = data.addons[id];
			if (!info.scales_with_booths) return;
			var auto = document.querySelector('.sm-addon-input-auto[data-sm-addon="' + id + '"]');
			if (!auto) return;
			auto.value = variants[id] ? booths.reduce(function (sum, bid) { var b = data.booths[bid]; return sum + (b ? smSqm(b.size) : 0); }, 0) : 0;
		});

		var addons = {};
		document.querySelectorAll('.sm-addon-input').forEach(function (i) {
			var qty = parseInt(i.value, 10) || 0;
			if (qty > 0) addons[i.dataset.smAddon] = qty;
		});

		var stage = $('sm-stage-slot-value') ? $('sm-stage-slot-value').value : '';
		var forening = $('sm-is-forening') ? ($('sm-is-forening').value === '1') : false;
		return { booths: booths, addons: addons, variants: variants, stage: stage, forening: forening };
	}

	function computeTotal(s) {
		var t = 0;
		s.booths.forEach(function (id) {
			var b = data.booths[id];
			if (!b) return;
			t += (s.forening && id.charAt(0) === 'N') ? data.forening_price : b.price;
		});
		if (s.booths.length > 0) t += data.registration_fee;
		Object.keys(s.addons).forEach(function (id) {
			var a = data.addons[id];
			if (a) t += a.price * s.addons[id];
		});
		if (s.forening) t = Math.round(t * 1.25);
		return t;
	}

	function canNext(currentStep, s) {
		if (currentStep === 0) return s.booths.length > 0; // Monter
		if (currentStep === 1) return true;                // Tillägg
		if (currentStep === 2) return true;                // Scen (frivilligt)
		if (currentStep === 3) {                           // Företag
			var required = document.querySelectorAll('[data-step1-input][data-required="1"]:not([disabled])');
			for (var i = 0; i < required.length; i++) {
				if (!required[i].value.trim()) return false;
			}
			return true;
		}
		if (currentStep === 4) return $('sm-accept-terms').checked && $('sm-accept-gdpr').checked;
		return true;
	}

	function render() {
		document.querySelectorAll('.sm-wiz-step').forEach(function (el) {
			el.classList.toggle('is-active', parseInt(el.dataset.step, 10) === step);
		});
		document.querySelectorAll('#sm-wiz-stepper .sm-wiz-stepper-btn').forEach(function (btn) {
			var i = parseInt(btn.dataset.step, 10);
			btn.classList.toggle('is-active', i === step);
			btn.classList.toggle('is-done', i < step || completed[i]);
			btn.disabled = (i > step && !completed[i]);
		});

		$('sm-wiz-back').style.display = step > 0 ? 'inline-flex' : 'none';
		$('sm-wiz-next').style.display = step < TOTAL_STEPS - 1 ? 'inline-flex' : 'none';
		$('sm-wiz-submit').style.display = step === TOTAL_STEPS - 1 ? 'inline-flex' : 'none';

		var s = readState();
		var canProceed = canNext(step, s);
		// Både Nästa och Skicka är alltid klickbara; klick-handlern visar
		// tydligt vilka fält som saknas (rödmarkering + meddelande).
		$('sm-wiz-next').style.opacity = canProceed ? '1' : '0.55';
		$('sm-wiz-submit').style.opacity = canProceed ? '1' : '0.55';

		var total = computeTotal(s);
		$('sm-footer-total').textContent = nf(total);
		$('sm-footer-moms').textContent = s.forening ? 'inkl. moms' : 'exkl. moms';

		// Bottom-summering i monter-steget (matchar designens StepBooth)
		var summaryEl = $('sm-booth-summary');
		var emptyEl   = $('sm-booth-summary-empty');
		if (summaryEl && emptyEl) {
			if (s.booths.length > 0) {
				summaryEl.style.display = 'grid';
				emptyEl.style.display   = 'none';
				var chips = s.booths.slice().sort().map(function (id) {
					var b = data.booths[id];
					var price = (s.forening && id.charAt(0) === 'N') ? data.forening_price : (b ? b.price : 0);
					var sizeLabel = b ? b.size.replace('x', '×') + ' m' : '';
					return '<span style="background:rgba(255,255,255,0.15);padding:6px 12px;border-radius:6px;font-family:var(--sm-font-display);font-weight:700;font-size:14px;">' + id + ' <span style="opacity:0.7;font-weight:400;font-size:12px;">· ' + sizeLabel + ' · ' + nf(price) + ' kr</span></span>';
				});
				$('sm-booth-chips').innerHTML = chips.join('');

				// Bara monter-priser (utan reg.avg + tillval)
				var boothOnly = 0;
				s.booths.forEach(function (id) {
					var b = data.booths[id];
					if (!b) return;
					boothOnly += (s.forening && id.charAt(0) === 'N') ? data.forening_price : b.price;
				});
				if (s.forening) boothOnly = Math.round(boothOnly * 1.25);
				$('sm-booth-summary-total').textContent = nf(boothOnly);
				$('sm-booth-summary-moms').textContent  = s.forening ? 'inkl. moms' : 'exkl. moms';
			} else {
				summaryEl.style.display = 'none';
				emptyEl.style.display   = 'block';
			}
		}

		if (step === 4) renderReview(s, total);
	}

	function renderReview(s, total) {
		// Företag som rader (key/value)
		var fields = [
			['Namn', 'sm_company'],
			['Org.nr', 'sm_orgnr'],
			['Kontakt', 'sm_contact_name'],
			['E-post', 'sm_contact_email'],
			['Telefon', 'sm_contact_phone'],
			['Webbplats', 'sm_website'],
		];
		var rows = fields.map(function (f) {
			var el = document.querySelector('[name=' + f[1] + ']');
			var val = el ? (el.value || '—') : '—';
			if (f[1] === 'sm_website' && $('sm-no-website').checked) val = '— Ingen webbplats';
			return '<div style="display:flex;justify-content:space-between;padding:6px 0;font-size:16px;border-bottom:1px solid var(--sm-line);"><span style="color:var(--sm-ink-soft);">' + f[0] + '</span><span style="font-weight:600;text-align:right;">' + val + '</span></div>';
		});
		$('sm-review-company').innerHTML = rows.join('');

		// Montrar med priser, sen registreringsavgift
		var boothLines = [];
		s.booths.forEach(function (id) {
			var b = data.booths[id];
			if (!b) return;
			var price = (s.forening && id.charAt(0) === 'N') ? data.forening_price : b.price;
			var label = id.charAt(0) === 'N' ? id + ' (förenings-monter)' : id + ' (' + b.size.replace('x', '×') + ' m)';
			boothLines.push('<div style="display:flex;justify-content:space-between;padding:6px 0;font-size:16px;border-bottom:1px solid var(--sm-line);"><span style="color:var(--sm-ink-soft);">' + label + '</span><span style="font-weight:600;">' + nf(price) + ' kr</span></div>');
		});
		if (s.booths.length > 0) {
			boothLines.push('<div style="display:flex;justify-content:space-between;padding:6px 0;font-size:16px;border-bottom:1px solid var(--sm-line);"><span style="color:var(--sm-ink-soft);">Registreringsavgift</span><span style="font-weight:600;">' + nf(data.registration_fee) + ' kr</span></div>');
		}
		$('sm-review-booths').innerHTML = boothLines.join('') || '<div style="color:var(--sm-muted);">— Inga montrar valda</div>';
		$('sm-review-booths-title').textContent = 'Montrar (' + s.booths.length + ')';

		// Tillval
		var addonLines = [];
		Object.keys(s.addons).forEach(function (id) {
			var a = data.addons[id];
			if (!a) return;
			var variant = s.variants[id] ? ' (' + s.variants[id] + ')' : '';
			addonLines.push('<div style="display:flex;justify-content:space-between;padding:6px 0;font-size:17px;"><span>' + a.name + variant + ' × ' + s.addons[id] + '</span><span style="font-weight:700;">' + nf(a.price * s.addons[id]) + ' kr</span></div>');
		});
		$('sm-review-addons').innerHTML = addonLines.join('');
		$('sm-review-addons-wrap').style.display = addonLines.length ? 'block' : 'none';

		// Scenpass
		if (s.stage) {
			$('sm-review-stage-wrap').style.display = 'block';
			$('sm-review-stage').innerHTML = '<strong>' + s.stage + '</strong> — 15 min · <?php echo esc_js( sm_event_date_long() ); ?>';
		} else {
			$('sm-review-stage-wrap').style.display = 'none';
		}

		// 5 entrébiljetter — endast icke-föreningar med minst en monter
		$('sm-review-tickets-wrap').style.display = ( ! s.forening && s.booths.length > 0 ) ? 'block' : 'none';

		$('sm-review-total').textContent = nf(total);
		$('sm-review-moms').textContent = s.forening ? 'inkl. moms' : 'exkl. moms';

		// Tomt-state
		var anyData = s.booths.length > 0 || (document.querySelector('[name=sm_company]') && document.querySelector('[name=sm_company]').value);
		$('sm-review-empty').style.display = anyData ? 'none' : 'block';
		$('sm-review-content').style.display = anyData ? 'block' : 'none';
	}

	function clearStepErrors() {
		var box = $('sm-step-error');
		if (box) box.style.display = 'none';
		document.querySelectorAll('.sm-wiz-input.is-invalid').forEach(function (i) { i.classList.remove('is-invalid'); });
	}

	function showStepErrors(currentStep) {
		var box = $('sm-step-error');
		document.querySelectorAll('.sm-wiz-input.is-invalid').forEach(function (i) { i.classList.remove('is-invalid'); });
		var msg = '';
		if (currentStep === 0) {
			msg = 'Välj minst en monter för att gå vidare.';
		} else if (currentStep === 3) {
			var required = document.querySelectorAll('[data-step1-input][data-required="1"]:not([disabled])');
			var missing = 0;
			required.forEach(function (el) { if (!el.value.trim()) { el.classList.add('is-invalid'); missing++; } });
			msg = missing > 0 ? 'Fyll i de rödmarkerade fälten för att gå vidare.' : 'Kontrollera uppgifterna och försök igen.';
		}
		if (box && msg) {
			box.textContent = msg;
			box.style.display = 'block';
			box.scrollIntoView({ behavior: 'smooth', block: 'center' });
		}
	}

	function goto(newStep) {
		if (newStep < 0 || newStep >= TOTAL_STEPS) return;
		var s = readState();
		if (newStep > step) {
			if (!canNext(step, s)) { showStepErrors(step); return; }
			completed[step] = true;
		}
		clearStepErrors();
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

	// Collapsible monter-sektioner
	document.querySelectorAll('.sm-section-toggle').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var expanded = btn.getAttribute('aria-expanded') === 'true';
			btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
			var body = document.querySelector('.sm-section-body[data-section="' + btn.dataset.section + '"]');
			if (body) body.classList.toggle('is-open', !expanded);
		});
	});

	// Förenings-gate i N-sektionen
	document.querySelectorAll('[data-forening]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var mode = btn.dataset.forening; // 'yes' | 'no' | 'reset'
			var gate = btn.closest('.sm-forening-gate');
			var body = btn.closest('.sm-section-body');
			var grid = body ? body.querySelector('.sm-booth-grid') : null;
			$('sm-is-forening').value = (mode === 'yes') ? '1' : '';
			if (gate) {
				var q = gate.querySelector('.sm-forening-q');
				var yesBox = gate.querySelector('.sm-forening-yes');
				var noBox = gate.querySelector('.sm-forening-no');
				if (q) q.style.display = (mode === 'reset') ? '' : 'none';
				if (yesBox) yesBox.style.display = (mode === 'yes') ? '' : 'none';
				if (noBox) noBox.style.display = (mode === 'no') ? '' : 'none';
			}
			if (grid) {
				grid.style.display = (mode === 'yes') ? 'grid' : 'none';
				if (mode !== 'yes') grid.querySelectorAll('.sm-booth-input:checked').forEach(function (i) { i.checked = false; });
			}
			render();
		});
	});

	// Stage-knappar
	document.querySelectorAll('.sm-stage-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			if (btn.disabled) return;
			var slot = btn.dataset.slot;
			var current = $('sm-stage-slot-value').value;
			var newValue = (current === slot) ? '' : slot;
			$('sm-stage-slot-value').value = newValue;
			document.querySelectorAll('.sm-stage-btn').forEach(function (b) { b.classList.remove('is-selected'); });
			if (newValue) {
				btn.classList.add('is-selected');
				$('sm-stage-selected').style.display = 'flex';
				$('sm-stage-empty').style.display = 'none';
				$('sm-stage-selected-time').textContent = newValue;
			} else {
				$('sm-stage-selected').style.display = 'none';
				$('sm-stage-empty').style.display = 'block';
			}
			render();
		});
	});
	$('sm-stage-clear').addEventListener('click', function () {
		$('sm-stage-slot-value').value = '';
		document.querySelectorAll('.sm-stage-btn').forEach(function (b) { b.classList.remove('is-selected'); });
		$('sm-stage-selected').style.display = 'none';
		$('sm-stage-empty').style.display = 'block';
		render();
	});

	// Quantity +/- knappar
	document.querySelectorAll('.sm-qty-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var card = btn.closest('.sm-addon-card');
			var input = card.querySelector('.sm-addon-input');
			var cur = parseInt(input.value, 10) || 0;
			input.value = btn.dataset.action === 'inc' ? Math.min(cur + 1, 20) : Math.max(cur - 1, 0);
			input.dispatchEvent(new Event('change', { bubbles: true }));
		});
	});

	// Variant-knappar
	document.querySelectorAll('.sm-variant-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var grid = btn.closest('.sm-variant-grid');
			grid.querySelectorAll('.sm-variant-btn').forEach(function (b) { b.classList.remove('is-selected'); });
			btn.classList.add('is-selected');
			grid.querySelector('.sm-variant-value').value = btn.dataset.variant;
			render();
		});
	});

	// Rensa val-knapp i monter-summeringen
	var clearBtn = $('sm-booth-clear');
	if (clearBtn) {
		clearBtn.addEventListener('click', function () {
			document.querySelectorAll('.sm-booth-input:checked').forEach(function (i) { i.checked = false; });
			render();
		});
	}

	$('sm-wiz-back').addEventListener('click', function () { goto(step - 1); });
	$('sm-wiz-next').addEventListener('click', function () { goto(step + 1); });
	// Submit-validering: visa fel om GDPR/villkor saknas
	$('sm-reg-form').addEventListener('submit', function (e) {
		if (step !== TOTAL_STEPS - 1) {
			// Skickas av misstag (t.ex. Enter i ett textfält i tidigare steg)
			e.preventDefault();
			return;
		}
		if (!canNext(4, readState())) {
			e.preventDefault();
			$('sm-submit-error').style.display = 'block';
			$('sm-submit-error').scrollIntoView({ behavior: 'smooth', block: 'center' });
		}
	});
	document.querySelectorAll('#sm-wiz-stepper .sm-wiz-stepper-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var i = parseInt(btn.dataset.step, 10);
			if (i <= step || completed[i]) goto(i);
		});
	});
	document.addEventListener('change', function (e) {
		if (e.target.matches('.sm-booth-input, .sm-addon-input, #sm-is-forening, #sm-accept-terms, #sm-accept-gdpr')) {
			render();
			if (e.target.matches('.sm-booth-input') && document.querySelector('.sm-booth-input:checked')) {
				var sErr = $('sm-step-error');
				if (sErr) sErr.style.display = 'none';
			}
			// Göm submit-felmeddelandet när användaren bockar i en ruta
			if (e.target.id === 'sm-accept-terms' || e.target.id === 'sm-accept-gdpr') {
				var err = $('sm-submit-error');
				if (err && canNext(4, readState())) err.style.display = 'none';
			}
		}
		if (e.target.id === 'sm-no-website') {
			toggleWebsite();
			render();
		}
	});
	document.addEventListener('input', function (e) {
		if (e.target.matches('[data-step1-input], .sm-addon-input')) {
			e.target.classList.remove('is-invalid');
			if (!document.querySelector('.sm-wiz-input.is-invalid')) {
				var box = $('sm-step-error');
				if (box) box.style.display = 'none';
			}
			render();
		}
	});
	toggleWebsite();
	render();
})();
</script>

<?php
delete_transient( 'sm_register_errors_' . sm_session_id() );
?>

<?php get_footer(); ?>
