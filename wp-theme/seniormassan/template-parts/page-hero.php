<?php
/**
 * Återanvändbar sidhero för undersidor (Program, Hitta hit, Kontakt, För utställare).
 *
 * Args:
 *   eyebrow: string
 *   title:   string
 *   body:    string
 *   tone:    'primary' | 'accent'  (default primary)
 *   cta:     string (knapptext, valfritt)
 *   cta_url: string (knapp-URL, valfritt)
 */

$args = isset( $args ) && is_array( $args ) ? $args : array();
$eyebrow = $args['eyebrow'] ?? '';
$title   = $args['title']   ?? '';
$body    = $args['body']    ?? '';
$tone    = $args['tone']    ?? 'primary';
$cta     = $args['cta']     ?? '';
$cta_url = $args['cta_url'] ?? '';

$is_accent = ( $tone === 'accent' );
$bg        = $is_accent ? 'var(--sm-accent)' : 'var(--sm-primary)';
$btn_color = $is_accent ? 'var(--sm-accent)' : 'var(--sm-primary)';
?>
<section style="background: <?php echo esc_attr( $bg ); ?>; color: #fff;">
	<div class="sm-container" style="padding: 80px 32px; max-width: 900px;">
		<?php if ( $eyebrow ) : ?>
			<div style="font-size: 13px; font-weight: 700; letter-spacing: 0.22em; text-transform: uppercase; opacity: 0.85;"><?php echo esc_html( $eyebrow ); ?></div>
		<?php endif; ?>
		<h1 style="font-size: var(--sm-fs-xl); margin-top: 16px; color: #fff;"><?php echo esc_html( $title ); ?></h1>
		<?php if ( $body ) : ?>
			<p style="font-size: var(--sm-fs-lg); opacity: 0.9; margin-top: 16px; font-weight: 300;"><?php echo esc_html( $body ); ?></p>
		<?php endif; ?>
		<?php if ( $cta && $cta_url ) : ?>
			<a href="<?php echo esc_url( $cta_url ); ?>" style="display: inline-block; margin-top: 32px; background: #fff; color: <?php echo esc_attr( $btn_color ); ?>; padding: 16px 28px; border-radius: 999px; font-weight: 700; font-size: 18px; text-decoration: none;"><?php echo esc_html( $cta ); ?></a>
		<?php endif; ?>
	</div>
</section>
