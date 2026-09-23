<?php
/**
 * Template part used for displaying page content.
 *
 * @package kriate
 */

?>

<div class="wp-page">
	<div id="post-<?php the_ID(); ?>" <?php post_class( 'content' ); ?>>
		<article class="entry-content">
			<?php the_content(); ?>
			<?php
			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'briite' ),
					'after'  => '</div>',
				)
			);
			?>
		</article><!-- .entry-content -->

		<footer class="entry-footer">
			<?php edit_post_link( esc_html__( 'Edit', 'briite' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-footer -->

		<?php
		if ( comments_open() || 0 !== get_comments_number() ) {
			comments_template();
		}
		?>

		<?php get_sidebar(); ?>
	</div><!-- #post-## -->
</div>
