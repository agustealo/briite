<?php /* @package kriate */

get_header(); ?>
		<?php if ( have_posts() ) : ?>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'grid' ); ?>

			<?php endwhile; ?>

		<?php endif; ?>
<?php get_footer(); ?>
