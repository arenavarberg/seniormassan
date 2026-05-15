<?php
/**
 * Sidmall för "Anmälan" — utställaranmälningsformulär.
 *
 * Innehåll:
 *   1. SVG-karta med samtliga montrar
 *   2. Båslista grupperad per sektion med kryssrutor (multi-select)
 *   3. Företagsuppgifter
 *   4. Tillval
 *   5. Önskemål + GDPR + skicka
 *
 * Vid lyckad inskickning visas en bekräftelsesida (?ref=ID).
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
					Frågor? Hör av dig till <a href="mailto:bokning@arenavarberg.se" style="color: var(--sm-gold);">bokning@arenavarberg.se</a>.
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
$errors = get_transient( 'sm_register_errors_' . sm_session_id() );
$input  = get_transient( 'sm_register_input_' . sm_session_id() ) ?: array();

$booked      = sm_booked_booth_ids();
$booked_set  = array_flip( $booked );

// Gruppera montrar per sektion
$by_section = array();
foreach ( sm_booths() as $b ) {
	$section = substr( $b['id'], 0, 1 );
	$by_section[ $section ][] = $b;
}
ksort( $by_section );

// Sektionsbeskrivningar
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

$size_labels = array(
	'2x2' => '2 × 2 m',
	'2x3' => '2 × 3 m',
	'3x3' => '3 × 3 m',
);

get_template_part( 'template-parts/page-hero', null, array(
	'eyebrow' => 'Utställaranmälan',
	'title'   => 'Anmäl din monter till Seniormässan 2027.',
	'body'    => 'Fyll i uppgifterna nedan, välj monter och eventuella tillval. Sista anmälningsdag: 15 augusti 2027.',
	'tone'    => 'accent',
) );

$saved_booths   = is_array( $input['sm_booths'] ?? null ) ? $input['sm_booths'] : array();
$saved_addons   = is_array( $input['sm_addons'] ?? null ) ? $input['sm_addons'] : array();
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

		<form method="post" action="<?php echo esc_url( get_permalink() ); ?>" style="display: grid; gap: 48px;">
			<?php wp_nonce_field( 'sm_register', 'sm_register_nonce' ); ?>

			<!-- Steg 1: Företagsuppgifter -->
			<div class="sm-card" style="padding: 40px;">
				<div class="sm-eyebrow">Steg 1</div>
				<h2 style="font-size: 28px; margin-bottom: 24px;">Företagsuppgifter</h2>
				<p style="color: var(--sm-ink-soft); margin-bottom: 24px;">Webbplatsen används i den publika utställarlistan.</p>

				<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
					<?php
					$fields = array(
						array( 'sm_company',       'Företagsnamn *',       'text', true ),
						array( 'sm_orgnr',         'Organisationsnummer *','text', true ),
						array( 'sm_contact_name',  'Kontaktperson *',      'text', true ),
						array( 'sm_contact_phone', 'Telefon *',            'tel',  true ),
						array( 'sm_contact_email', 'E-post kontaktperson *', 'email', true ),
						array( 'sm_website',       'Webbplats *',          'url',  true ),
						array( 'sm_invoice_email', 'Faktura-e-post',       'email', false ),
					);
					foreach ( $fields as $f ) :
						$value = $input[ $f[0] ] ?? '';
						?>
						<label style="display: block;">
							<span style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px;"><?php echo esc_html( $f[1] ); ?></span>
							<input type="<?php echo esc_attr( $f[2] ); ?>" name="<?php echo esc_attr( $f[0] ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php echo $f[3] ? 'required' : ''; ?> style="width: 100%; padding: 12px 14px; border: 1px solid var(--sm-line); border-radius: 6px; font-size: 16px; font-family: inherit;">
						</label>
					<?php endforeach; ?>

					<label style="display: block; grid-column: 1 / -1;">
						<span style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px;">Fakturaadress</span>
						<textarea name="sm_invoice_address" rows="2" style="width: 100%; padding: 12px 14px; border: 1px solid var(--sm-line); border-radius: 6px; font-size: 16px; font-family: inherit;"><?php echo esc_textarea( $input['sm_invoice_address'] ?? '' ); ?></textarea>
					</label>

					<label style="display: block; grid-column: 1 / -1;">
						<span style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px;">Kort beskrivning av företaget (frivilligt, max 320 tecken)</span>
						<textarea name="sm_description" rows="3" maxlength="320" style="width: 100%; padding: 12px 14px; border: 1px solid var(--sm-line); border-radius: 6px; font-size: 16px; font-family: inherit;"><?php echo esc_textarea( $input['sm_description'] ?? '' ); ?></textarea>
					</label>

					<label style="display: flex; gap: 10px; align-items: flex-start; grid-column: 1 / -1; padding: 12px; background: var(--sm-bg); border-radius: 6px;">
						<input type="checkbox" name="sm_is_forening" value="1" <?php checked( ! empty( $input['sm_is_forening'] ) ); ?> style="margin-top: 4px;">
						<span>Anmälan görs av <strong>ideell förening</strong> (välj då en N-monter i steget nedan — föreningspris 2 360 kr inkl. moms)</span>
					</label>
				</div>
			</div>

			<!-- Steg 2: Båskarta -->
			<div class="sm-card" style="padding: 40px;">
				<div class="sm-eyebrow">Steg 2</div>
				<h2 style="font-size: 28px; margin-bottom: 12px;">Välj monter</h2>
				<p style="color: var(--sm-ink-soft); margin-bottom: 24px;">
					På kartan ser ni montrarnas placering. Bocka i en eller flera montrar i listan nedan.<br>
					Samtliga priser är exkl. moms. För ideella föreningar tillkommer 25 % moms.
				</p>

				<?php get_template_part( 'template-parts/booth-map', null, array(
					'booked_ids'   => $booked,
					'selected_ids' => $saved_booths,
				) ); ?>

				<div style="margin-top: 32px; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 24px;">
					<?php foreach ( $by_section as $section => $booths_in_section ) :
						$first      = $booths_in_section[0];
						$is_forening = ( $section === 'N' );
						$price      = $is_forening ? SM_FORENING_PRICE : SM_BOOTH_PRICES[ $first['size'] ];
						$label      = $section_labels[ $section ] ?? '';
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
										<input type="checkbox" name="sm_booths[]" value="<?php echo esc_attr( $b['id'] ); ?>" <?php echo $is_booked ? 'disabled' : ''; ?> <?php checked( $is_selected ); ?>>
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

			<!-- Steg 3: Tillval -->
			<div class="sm-card" style="padding: 40px;">
				<div class="sm-eyebrow">Steg 3</div>
				<h2 style="font-size: 28px; margin-bottom: 12px;">Tillval</h2>
				<p style="color: var(--sm-ink-soft); margin-bottom: 24px;">Lägg till valfria produkter i din monter. Registreringsavgiften (800 kr) är obligatorisk och redan ikryssad.</p>

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
									<input type="number" name="sm_addons[<?php echo esc_attr( $a['id'] ); ?>]" min="<?php echo ! empty( $a['required'] ) ? '1' : '0'; ?>" max="20" value="<?php echo (int) $saved_qty; ?>" style="width: 60px; padding: 6px 8px; border: 1px solid var(--sm-line); border-radius: 4px; font-size: 14px;">
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

			<!-- Steg 4: Villkor + skicka -->
			<div class="sm-card" style="padding: 40px;">
				<div class="sm-eyebrow">Steg 4</div>
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

<?php
// Rensa fel direkt efter visning
delete_transient( 'sm_register_errors_' . sm_session_id() );
?>

<?php get_footer(); ?>
