<?php
/**
 * Standard sidmall — används för alla statiska sidor som inte har egen mall.
 */

get_header();
?>

<section style="background: var(--sm-primary); color: var(--sm-primary-ink); padding: 96px 0;">
	<div class="sm-container">
		<h1 style="font-size: var(--sm-fs-xl);"><?php the_title(); ?></h1>
	</div>
</section>

<section style="padding: 80px 0; background: var(--sm-bg);">
	<div class="sm-container" style="max-width: 800px;">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
		endif;
		?>
	</div>
</section>

<?php get_footer(); ?>
