<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package kriate
 */
?>

<div class="wp-page">
	
	<div id="post-<?php the_ID(); ?>" <?php post_class("content"); ?>>
	
		<article class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'kriate' ),
				'after'  => '</div>',
			) );
		?>
		</article><!-- .entry-content -->
		
		<footer class="entry-footer">
		<?php edit_post_link( __( 'Edit', 'kriate' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-footer -->
		
		

		<?php
			// If comments are open or we have at least one comment, load up the comment template
			if ( comments_open() || '0' != get_comments_number() ) :
				comments_template();
			endif;
		?>

<?php get_sidebar(); ?>

	</div><!-- #post-## -->
</div>
