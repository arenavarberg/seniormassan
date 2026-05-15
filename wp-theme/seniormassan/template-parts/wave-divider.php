<?php
/**
 * Återanvändbar SVG-vågdelare mellan sektioner.
 *
 * Args:
 *   from:   string  — färgen ovanför (default var(--sm-bg))
 *   to:     string  — färgen nedanför (default var(--sm-surface))
 *   flip:   bool    — vänd vågen åt andra hållet
 *   height: int     — höjd i px
 */

$args = isset( $args ) && is_array( $args ) ? $args : array();
$from   = $args['from']   ?? 'var(--sm-bg)';
$to     = $args['to']     ?? 'var(--sm-surface)';
$flip   = ! empty( $args['flip'] );
$height = $args['height'] ?? 70;

$path = $flip
	? 'M0,60 C240,0 480,100 720,60 C960,20 1200,80 1440,40 L1440,121 L0,121 Z'
	: 'M0,40 C240,90 480,10 720,60 C960,100 1200,30 1440,70 L1440,121 L0,121 Z';
?>
<div style="background: <?php echo esc_attr( $from ); ?>; line-height: 0; margin-bottom: -1px; position: relative; z-index: 1;">
	<svg viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true" style="display: block; width: 100%; height: <?php echo (int) $height; ?>px; margin-bottom: -1px;">
		<path d="<?php echo esc_attr( $path ); ?>" fill="<?php echo esc_attr( $to ); ?>"></path>
	</svg>
</div>
