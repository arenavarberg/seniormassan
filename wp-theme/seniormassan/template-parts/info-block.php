<?php
/**
 * Återanvändbar InfoBlock (titel + rader med key/value).
 *
 * Args:
 *   title: string
 *   items: array of [key, value] pairs
 */

$args  = isset( $args ) && is_array( $args ) ? $args : array();
$title = $args['title'] ?? '';
$items = $args['items'] ?? array();
?>
<div style="margin-bottom: 40px;">
	<div style="font-size: 13px; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; color: var(--sm-accent); margin-bottom: 12px;"><?php echo esc_html( $title ); ?></div>
	<?php foreach ( $items as $item ) :
		$k = $item[0] ?? '';
		$v = $item[1] ?? '';
		?>
		<div style="display: flex; justify-content: space-between; gap: 24px; font-size: 19px; border-bottom: 1px solid var(--sm-line-soft); padding: 12px 0;">
			<span style="font-weight: 600;"><?php echo esc_html( $k ); ?></span>
			<span style="color: var(--sm-ink-soft); text-align: right;"><?php echo esc_html( $v ); ?></span>
		</div>
	<?php endforeach; ?>
</div>
