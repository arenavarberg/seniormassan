<?php
/**
 * Sidmall för "Anmälan" — utställaranmälningsformulär.
 *
 * Stegordning:
 *   1. Välj monter (bild + sektionsindelade bockrutor)
 *   2. Tillval
 *   3. Utställarscen (valfritt)
 *   4. Sammanfattning (live-uppdaterad totalsumma)
 *   5. Företagsuppgifter
 *   6. GDPR + skicka
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

// Hämta input/fel om de finns
$errors      = get_transient( 'sm_register_errors_' . sm_session_id() );
$input       = get_transient( 'sm_register_input_' . sm_session_id() ) ?: array();
$booked      = sm_booked_booth_ids();
$booked_set  = array_flip( $booked );
$taken_slots = sm_taken_stage_slots();
$map_image   = sm_booth_map_image_url();

// Gruppera montrar per sektion
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

// Pris- och addon-data som JSON för JS
$prices_json = array(
	'booths'           => array(),
	'addons'           => array(),
	'registration_fee' => SM_REGISTRATION_FEE,
	'forening_price'   => SM_FORENING_PRICE,
);
foreach ( sm_booths() as $b ) {
	$prices_json['booths'][ $b['id'] ] = array(
		'size'  => $b['size'],
		'price' => sm_booth_price( $b['id'] ),
	);
}
foreach ( sm_addons() as $a ) {
	$prices_json['addons'][ $a['id'] ] = array( 'name' => $a['name'], 'price' => $a['price'] );
}

get_template_part( 'template-parts/page-hero', null, array(
	'eyebrow' => 'Utställaranmälan',
	'title'   => 'Anmäl din monter till Seniormässan 2027.',
	'body'    => 'Välj monter, eventuella tillval och scenpass. Sista anmälningsdag: ' . sm_last_registration_date() . '.',
	'tone'    => 'accent',
) );

$saved_booths     = is_array( $input['sm_booths'] ?? null ) ? $input['sm_booths'] : array();
$saved_addons     = is_array( $input['sm_addons'] ?? null ) ? $input['sm_addons'] : array();
$saved_stage      = $input['sm_stage_slot'] ?? '';
$saved_no_website = ! empty( $input['sm_no_website'] );
?>

<section style="background: var(--sm-bg);">
	<div class="sm-container" style="padding: 64px 32px;">

		<?php if ( $errors && is_array( $errors ) ) : ?>
			<div style="background: #fee2e2; border: 1px solid #fecaca; border-radius: var(--sm-radius-lg); padding: 20px 24px; margin-bottom: 32px; color: #991b1b;">
				<strong>Anmälan kunde inte sparas. Korrigera följande:</strong>
				<ul style="margin: 12px 0 0; padding-left: 20px;">
					<?php foreach ( $errors as $err ) : ?>
						<li><?php echo esc_html( $err ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( get_permalink() ); ?>" style="display: grid; gap: 48px;" id="sm-reg-form">
			<?php wp_nonce_field( 'sm_register', 'sm_register_nonce' ); ?>

			<!-- Steg 1: Välj monter -->
			<div class="sm-card" style="padding: 40px;">
				<div class="sm-eyebrow">Steg 1</div>
				<h2 style="font-size: 28px; margin-bottom: 12px;">Välj monter</h2>
				<p style="color: var(--sm-ink-soft); margin-bottom: 24px;">
					På kartan ser ni montrarnas placering. Bocka i en eller flera montrar i listan nedan.<br>
					Samtliga priser är exkl. moms. För ideella föreningar tillkommer 25 % moms.
				</p>

				<?php if ( $map_image ) : ?>
					<div style="background: #fff; border: 1px solid var(--sm-line-soft); border-radius: var(--sm-radius-lg); padding: 16px; box-shadow: var(--sm-shadow-sm); margin-bottom: 24px;">
						<a href="<?php echo esc_url( $map_image ); ?>" target="_blank" rel="noopener">
							<img src="<?php echo esc_url( $map_image ); ?>" alt="Hallplan — Seniormässan på Arena Varberg" style="width: 100%; height: auto; display: block; border-radius: 4px;">
						</a>
						<div style="margin-top: 8px; font-size: 13px; color: var(--sm-muted); text-align: center;">Klicka för större bild</div>
					</div>
				<?php else : ?>
					<div style="background: var(--sm-bg); border: 1px dashed var(--sm-line); border-radius: var(--sm-radius-lg); padding: 48px; text-align: center; margin-bottom: 24px; color: var(--sm-ink-soft);">
						<strong>Hallplan saknas.</strong><br>
						<span style="font-size: 14px;">Ladda upp en bild i WP-admin → Utseende → Anpassa → Anmälan → "Hallplan (bild)".</span>
					</div>
				<?php endif; ?>

				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px;">
					<?php foreach ( $by_section as $section => $booths_in_section ) :
						$first       = $booths_in_section[0];
						$is_forening = ( $section === 'N' );
						$price       = $is_forening ? SM_FORENING_PRICE : SM_BOOTH_PRICES[ $first['size'] ];
						$label       = $section_labels[ $section ] ?? '';
						?>
						<div>
							<div style="font-family: var(--sm-font-display); font-size: 18px; font-weight: 800; margin-bottom: 4px;">
								Sektion <?php echo esc_html( $section ); ?> — <?php echo esc_html( $size_labels[ $first['size'] ] ); ?>
							</div>
							<div style="font-size: 13px; color: var(--sm-ink-soft); margin-bottom: 12px;">
								<?php echo esc_html( $label ); ?><br>
								<strong style="color: var(--sm-primary);"><?php echo esc_html( number_format( $price, 0, ',', "\u{00A0}" ) ); ?> kr</strong>
								<?php echo $is_forening ? ' exkl. moms' : ' exkl. moms / st'; ?>
							</div>
							<div style="display: flex; flex-direction: column; gap: 6px;">
								<?php foreach ( $booths_in_section as $b ) :
									$is_booked   = isset( $booked_set[ $b['id'] ] );
									$is_selected = in_array( $b['id'], $saved_booths, true );
									?>
									<label style="display: flex; align-items: center; gap: 8px; padding: 6px 10px; background: <?php echo $is_booked ? '#f5f5f5' : '#fff'; ?>; border: 1px solid var(--sm-line); border-radius: 4px; cursor: <?php echo $is_booked ? 'not-allowed' : 'pointer'; ?>; opacity: <?php echo $is_booked ? '0.6' : '1'; ?>;">
										<input type="checkbox" name="sm_booths[]" value="<?php echo esc_attr( $b['id'] ); ?>" <?php echo $is_booked ? 'disabled' : ''; ?> <?php checked( $is_selected ); ?> data-sm-booth="<?php echo esc_attr( $b['id'] ); ?>" class="sm-booth-input">
										<span style="<?php echo $is_booked ? 'text-decoration: line-through;' : ''; ?> font-weight: 600;"><?php echo esc_html( $b['id'] ); ?></span>
										<span style="margin-left: auto; font-size: 12px; color: <?php echo $is_booked ? '#999' : 'var(--sm-success)'; ?>; font-weight: 700; letter-spacing: 0.04em;">
											<?php echo $is_booked ? 'BOKAD' : 'LEDIG'; ?>
										</span>
									</label>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Steg 2: Tillval -->
			<div class="sm-card" style="padding: 40px;">
				<div class="sm-eyebrow">Steg 2</div>
				<h2 style="font-size: 28px; margin-bottom: 12px;">Tillval</h2>
				<p style="color: var(--sm-ink-soft); margin-bottom: 24px;">
					Lägg till valfria produkter i din monter. Registreringsavgiften (800 kr) är obligatorisk och redan ikryssad.
				</p>

				<?php
				$addons_by_cat = array();
				foreach ( sm_addons() as $a ) {
					$addons_by_cat[ $a['cat'] ][] = $a;
				}
				?>
				<?php foreach ( $addons_by_cat as $cat => $list ) : ?>
					<div style="margin-bottom: 28px;">
						<div style="font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; font-size: 13px; color: var(--sm-ink-soft); margin-bottom: 12px;"><?php echo esc_html( $cat ); ?></div>
						<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px;">
							<?php foreach ( $list as $a ) :
								$saved_qty = isset( $saved_addons[ $a['id'] ] ) ? (int) $saved_addons[ $a['id'] ] : ( ! empty( $a['required'] ) ? 1 : 0 );
								?>
								<label style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; background: var(--sm-bg); border: 1px solid var(--sm-line); border-radius: 6px;">
									<input type="number" name="sm_addons[<?php echo esc_attr( $a['id'] ); ?>]" min="<?php echo ! empty( $a['required'] ) ? '1' : '0'; ?>" max="20" value="<?php echo (int) $saved_qty; ?>" data-sm-addon="<?php echo esc_attr( $a['id'] ); ?>" class="sm-addon-input" style="width: 60px; padding: 6px 8px; border: 1px solid var(--sm-line); border-radius: 4px; font-size: 14px;">
									<span style="flex: 1;">
										<strong><?php echo esc_html( $a['name'] ); ?></strong><br>
										<span style="font-size: 13px; color: var(--sm-ink-soft);"><?php echo esc_html( $a['price'] ); ?> kr/st</span>
									</span>
								</label>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>

				<label style="display: block; margin-top: 16px;">
					<span style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px;">Särskilda önskemål</span>
					<textarea name="sm_special_requests" rows="3" style="width: 100%; padding: 12px 14px; border: 1px solid var(--sm-line); border-radius: 6px; font-size: 16px; font-family: inherit;"><?php echo esc_textarea( $input['sm_special_requests'] ?? '' ); ?></textarea>
				</label>
			</div>

			<!-- Steg 3: Utställarscen -->
			<div class="sm-card" style="padding: 40px;">
				<div class="sm-eyebrow">Steg 3</div>
				<h2 style="font-size: 28px; margin-bottom: 12px;">Utställarscen — boka tid (frivilligt)</h2>
				<p style="color: var(--sm-ink-soft); margin-bottom: 24px;">
					15 minuters scenpass. Kostnadsfritt men bindande. Först till kvarn — välj en tid eller hoppa över steget.
				</p>

				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: 8px;">
					<label style="display: flex; align-items: center; justify-content: center; padding: 14px; background: <?php echo ! $saved_stage ? 'var(--sm-primary-soft)' : 'var(--sm-bg)'; ?>; border: 2px solid <?php echo ! $saved_stage ? 'var(--sm-primary)' : 'var(--sm-line)'; ?>; border-radius: 6px; cursor: pointer; font-weight: 600;">
						<input type="radio" name="sm_stage_slot" value="" <?php checked( ! $saved_stage ); ?> style="margin-right: 8px;">
						Hoppa över
					</label>
					<?php foreach ( sm_stage_slots() as $slot ) :
						$is_taken    = in_array( $slot, $taken_slots, true );
						$is_selected = ( $slot === $saved_stage );
						?>
						<label style="display: flex; flex-direction: column; align-items: center; padding: 14px; background: <?php echo $is_taken ? '#f5f5f5' : ( $is_selected ? 'var(--sm-accent)' : 'var(--sm-bg)' ); ?>; color: <?php echo $is_selected ? '#fff' : 'inherit'; ?>; border: 2px solid <?php echo $is_taken ? '#ccc' : ( $is_selected ? 'var(--sm-accent)' : 'var(--sm-line)' ); ?>; border-radius: 6px; cursor: <?php echo $is_taken ? 'not-allowed' : 'pointer'; ?>; opacity: <?php echo $is_taken ? '0.6' : '1'; ?>; font-weight: 700;">
							<input type="radio" name="sm_stage_slot" value="<?php echo esc_attr( $slot ); ?>" <?php echo $is_taken ? 'disabled' : ''; ?> <?php checked( $is_selected ); ?> style="display: none;">
							<span style="<?php echo $is_taken ? 'text-decoration: line-through;' : ''; ?>"><?php echo esc_html( $slot ); ?></span>
							<?php if ( $is_taken ) : ?>
								<span style="font-size: 10px; opacity: 0.7; letter-spacing: 0.08em;">UPPTAGEN</span>
							<?php endif; ?>
						</label>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Steg 4: Sammanfattning -->
			<div class="sm-card" style="padding: 40px; background: var(--sm-primary); color: #fff; border: none;">
				<div class="sm-eyebrow" style="color: var(--sm-gold);">Steg 4</div>
				<h2 style="font-size: 28px; margin-bottom: 24px; color: #fff;">Sammanfattning</h2>

				<div id="sm-summary-empty" style="display: none; text-align: center; padding: 32px; opacity: 0.7;">
					Välj minst en monter ovan så fylls sammanfattningen i automatiskt.
				</div>

				<div id="sm-summary-content">
					<div style="margin-bottom: 24px;">
						<div style="text-transform: uppercase; letter-spacing: 0.14em; font-size: 12px; opacity: 0.7; margin-bottom: 8px;">Montrar</div>
						<div id="sm-summary-booths" style="font-size: 16px; line-height: 1.8;"></div>
					</div>

					<div id="sm-summary-addons-wrap" style="margin-bottom: 24px;">
						<div style="text-transform: uppercase; letter-spacing: 0.14em; font-size: 12px; opacity: 0.7; margin-bottom: 8px;">Tillval</div>
						<div id="sm-summary-addons" style="font-size: 16px; line-height: 1.8;"></div>
					</div>

					<div id="sm-summary-stage-wrap" style="margin-bottom: 24px; display: none;">
						<div style="text-transform: uppercase; letter-spacing: 0.14em; font-size: 12px; opacity: 0.7; margin-bottom: 8px;">Scenpass</div>
						<div id="sm-summary-stage" style="font-size: 16px;"></div>
					</div>

					<div id="sm-summary-tickets-wrap" style="margin-bottom: 24px;">
						<div style="text-transform: uppercase; letter-spacing: 0.14em; font-size: 12px; opacity: 0.7; margin-bottom: 8px;">Ingår</div>
						<div style="font-size: 16px; display: flex; justify-content: space-between;">
							<span id="sm-summary-tickets-text">5 digitala entrébiljetter</span>
							<span style="opacity: 0.7;">0 kr</span>
						</div>
					</div>

					<div style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 24px; margin-top: 24px;">
						<div style="display: flex; justify-content: space-between; align-items: baseline;">
							<span style="font-size: 18px; opacity: 0.8;">Totalsumma <span id="sm-summary-moms-label">exkl. moms</span></span>
							<span style="font-family: var(--sm-font-display); font-size: 36px; font-weight: 800;"><span id="sm-summary-total">0</span> kr</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Steg 5: Företagsuppgifter -->
			<div class="sm-card" style="padding: 40px;">
				<div class="sm-eyebrow">Steg 5</div>
				<h2 style="font-size: 28px; margin-bottom: 12px;">Företagsuppgifter</h2>
				<p style="color: var(--sm-ink-soft); margin-bottom: 24px;">Webbplatsen visas i den publika utställarlistan på sajten.</p>

				<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
					<?php
					$fields = array(
						array( 'sm_company',       'Företagsnamn *',         'text',  true ),
						array( 'sm_orgnr',         'Organisationsnummer *',  'text',  true ),
						array( 'sm_contact_name',  'Kontaktperson *',        'text',  true ),
						array( 'sm_contact_phone', 'Telefon *',              'tel',   true ),
						array( 'sm_contact_email', 'E-post kontaktperson *', 'email', true ),
						array( 'sm_invoice_email', 'Faktura-e-post',         'email', false ),
					);
					foreach ( $fields as $f ) :
						$value = $input[ $f[0] ] ?? '';
						?>
						<label style="display: block;">
							<span style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px;"><?php echo esc_html( $f[1] ); ?></span>
							<input type="<?php echo esc_attr( $f[2] ); ?>" name="<?php echo esc_attr( $f[0] ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php echo $f[3] ? 'required' : ''; ?> style="width: 100%; padding: 12px 14px; border: 1px solid var(--sm-line); border-radius: 6px; font-size: 16px; font-family: inherit;">
						</label>
					<?php endforeach; ?>

					<!-- Webbplats med "ingen webbplats"-toggle -->
					<label style="display: block; grid-column: 1 / -1;">
						<span style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px;">Webbplats <span id="sm-website-required" style="color: var(--sm-accent);">*</span></span>
						<input type="url" name="sm_website" id="sm-website-input" value="<?php echo esc_attr( $input['sm_website'] ?? '' ); ?>" placeholder="https://" style="width: 100%; padding: 12px 14px; border: 1px solid var(--sm-line); border-radius: 6px; font-size: 16px; font-family: inherit;" <?php echo $saved_no_website ? 'disabled' : ''; ?>>
					</label>
					<label style="display: flex; gap: 10px; align-items: center; grid-column: 1 / -1; padding: 10px 14px; background: var(--sm-bg); border-radius: 6px; font-size: 14px;">
						<input type="checkbox" name="sm_no_website" value="1" id="sm-no-website" <?php checked( $saved_no_website ); ?>>
						<span>Vi har ingen webbplats</span>
					</label>

					<label style="display: block; grid-column: 1 / -1;">
						<span style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px;">Fakturaadress</span>
						<textarea name="sm_invoice_address" rows="2" style="width: 100%; padding: 12px 14px; border: 1px solid var(--sm-line); border-radius: 6px; font-size: 16px; font-family: inherit;"><?php echo esc_textarea( $input['sm_invoice_address'] ?? '' ); ?></textarea>
					</label>

					<label style="display: block; grid-column: 1 / -1;">
						<span style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px;">Kort beskrivning av företaget (frivilligt, max 320 tecken)</span>
						<textarea name="sm_description" rows="3" maxlength="320" style="width: 100%; padding: 12px 14px; border: 1px solid var(--sm-line); border-radius: 6px; font-size: 16px; font-family: inherit;"><?php echo esc_textarea( $input['sm_description'] ?? '' ); ?></textarea>
					</label>

					<label style="display: flex; gap: 10px; align-items: flex-start; grid-column: 1 / -1; padding: 12px; background: var(--sm-bg); border-radius: 6px;">
						<input type="checkbox" name="sm_is_forening" value="1" id="sm-is-forening" <?php checked( ! empty( $input['sm_is_forening'] ) ); ?> style="margin-top: 4px;">
						<span>Anmälan görs av <strong>ideell förening</strong> (välj då en N-monter — föreningspris 2 360 kr inkl. moms)</span>
					</label>
				</div>
			</div>

			<!-- Steg 6: Skicka -->
			<div class="sm-card" style="padding: 40px;">
				<div class="sm-eyebrow">Steg 6</div>
				<h2 style="font-size: 28px; margin-bottom: 24px;">Granska och skicka</h2>

				<label style="display: flex; gap: 12px; padding: 14px; background: var(--sm-bg); border-radius: 6px; margin-bottom: 12px;">
					<input type="checkbox" name="sm_accept_terms" value="1" required>
					<span>Jag har läst och godkänner utställarvillkoren.</span>
				</label>

				<label style="display: flex; gap: 12px; padding: 14px; background: var(--sm-bg); border-radius: 6px; margin-bottom: 28px;">
					<input type="checkbox" name="sm_accept_gdpr" value="1" required>
					<span>Jag samtycker till att Arena Varberg behandlar mina uppgifter enligt GDPR för att hantera anmälan.</span>
				</label>

				<button type="submit" name="sm_register_submit" value="1" class="sm-btn sm-btn--accent" style="width: 100%; padding: 20px;">
					Skicka anmälan →
				</button>
				<p style="text-align: center; margin-top: 16px; font-size: 14px; color: var(--sm-muted);">
					Vi återkommer inom kort med bekräftelse och faktura.
				</p>
			</div>
		</form>
	</div>
</section>

<script>
(function () {
	var data = <?php echo wp_json_encode( $prices_json ); ?>;

	function nf(n) { return n.toLocaleString('sv-SE'); }

	function update() {
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

		var isForening = document.getElementById('sm-is-forening');
		var forening = isForening && isForening.checked;

		// Båspriser
		var boothTotal = 0;
		var boothLines = [];
		booths.forEach(function (id) {
			var b = data.booths[id];
			if (!b) return;
			var price = forening && id.charAt(0) === 'N' ? data.forening_price : b.price;
			boothTotal += price;
			boothLines.push('<div style="display:flex;justify-content:space-between;"><span>' + id + ' (' + b.size.replace('x','×') + ' m)</span><span>' + nf(price) + ' kr</span></div>');
		});

		// Tillval
		var addonTotal = 0;
		var addonLines = [];
		Object.keys(addons).forEach(function (id) {
			var a = data.addons[id];
			if (!a) return;
			var sub = a.price * addons[id];
			addonTotal += sub;
			addonLines.push('<div style="display:flex;justify-content:space-between;"><span>' + a.name + ' × ' + addons[id] + '</span><span>' + nf(sub) + ' kr</span></div>');
		});

		var total = boothTotal + addonTotal;
		if (forening) total = Math.round(total * 1.25);

		// Visa/dölj sammanfattning
		var emptyEl   = document.getElementById('sm-summary-empty');
		var contentEl = document.getElementById('sm-summary-content');
		if (booths.length === 0) {
			emptyEl.style.display = 'block';
			contentEl.style.display = 'none';
		} else {
			emptyEl.style.display = 'none';
			contentEl.style.display = 'block';
		}

		document.getElementById('sm-summary-booths').innerHTML = boothLines.join('') || '—';
		document.getElementById('sm-summary-addons').innerHTML = addonLines.join('') || '—';
		document.getElementById('sm-summary-addons-wrap').style.display = addonLines.length ? 'block' : 'none';

		var stageWrap = document.getElementById('sm-summary-stage-wrap');
		if (stage) {
			stageWrap.style.display = 'block';
			document.getElementById('sm-summary-stage').textContent = stage + ' (15 min, kostnadsfritt)';
		} else {
			stageWrap.style.display = 'none';
		}

		// Föreningar får inte 5 entrébiljetter (de är för betald monter)
		var ticketsWrap = document.getElementById('sm-summary-tickets-wrap');
		ticketsWrap.style.display = forening ? 'none' : 'block';

		document.getElementById('sm-summary-total').textContent = nf(total);
		document.getElementById('sm-summary-moms-label').textContent = forening ? 'inkl. moms' : 'exkl. moms';
	}

	// Webbplats-toggle
	function toggleWebsite() {
		var cb       = document.getElementById('sm-no-website');
		var input    = document.getElementById('sm-website-input');
		var required = document.getElementById('sm-website-required');
		if (cb.checked) {
			input.disabled = true;
			input.value = '';
			required.style.display = 'none';
		} else {
			input.disabled = false;
			required.style.display = 'inline';
		}
	}

	document.addEventListener('change', function (e) {
		if (e.target.matches('.sm-booth-input, .sm-addon-input, input[name=sm_stage_slot], #sm-is-forening')) {
			update();
		}
		if (e.target.id === 'sm-no-website') {
			toggleWebsite();
		}
	});
	document.addEventListener('input', function (e) {
		if (e.target.matches('.sm-addon-input')) update();
	});
	update();
	toggleWebsite();
})();
</script>

<?php
delete_transient( 'sm_register_errors_' . sm_session_id() );
?>

<?php get_footer(); ?>
