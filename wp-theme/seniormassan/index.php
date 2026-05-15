<?php
/**
 * Fallback-mall — används när ingen mer specifik mall matchar.
 */

get_header();
?>

<section style="padding: 96px 0;">
	<div class="sm-container">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article style="margin-bottom: 48px;">
					<h1 style="font-size: var(--sm-fs-xl); margin-bottom: 16px;"><?php the_title(); ?></h1>
					<div><?php the_content(); ?></div>
				</article>
			<?php endwhile; ?>
		<?php else : ?>
			<h1 style="font-size: var(--sm-fs-xl);">Inget innehåll här ännu.</h1>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
