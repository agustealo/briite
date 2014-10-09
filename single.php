<?php
/**
 * The template for displaying all single posts.
 *
 * @package kriate
 */

get_header(); ?>

		<?php while ( have_posts() ) : the_post(); ?>

<?php
/**
 * @package kriate
 */
?>

<?php if (has_post_thumbnail( $post->ID ) ): ?>
<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-featured' ); ?>

		<section class="top" style="background-image: url('<?php echo $image[0]; ?>')">
		<?php endif; ?>
			<div class="wrapper content_header clearfix">
				<div class="work_nav">
							
					<ul class="btn clearfix">
						<li>
                    <?php
$next_post = get_next_post();
if (!empty( $next_post )): ?>
  <a href="<?php echo get_permalink( $next_post->ID ); ?>" class="previous" data-title="Previous"></a></li>
<?php endif; ?>
                                                
                        <li><?php $cats=get_the_category();
								foreach($cats as $cat){
							/*check for category having parent or not except category id=1 which is wordpress default category (Uncategorized)*/
									if($cat->category_parent == 0 && $cat->term_id != 1){
										echo '<a href="'.get_category_link($cat->term_id ).'" class="grid" data-title="Category"></a>';
									}
									break;
								} ?></li>
						<li>
<?php
$prev_post = get_previous_post();
if (!empty( $prev_post )): ?>
  <a href="<?php echo get_permalink( $prev_post->ID ); ?>" class="next" data-title="Next"></a>
<?php endif; ?></li>
					</ul>							
					
                    


                    
                    
                    
				</div><!-- end work_nav -->
		<?php the_title( '<h1 class="title">', '</h1>' ); ?>
			</div>		
		</section><!-- end top -->

	<section class="wrapper">
	<div class="content">
		<div class="entry-content">
			<?php the_content(); ?>
			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . __( 'Pages:', 'kriate' ),
					'after'  => '</div>',
				) );
			?>
		</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php
			/* translators: used between list items, there is a space after the comma */
			$category_list = get_the_category_list( __( ', ', 'kriate' ) );

			/* translators: used between list items, there is a space after the comma */
			$tag_list = get_the_tag_list( '', __( ', ', 'kriate' ) );

			if ( ! kriate_categorized_blog() ) {
				// This blog only has 1 category so we just need to worry about tags in the meta text
				if ( '' != $tag_list ) {
					$meta_text = __( 'This entry was tagged %2$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'kriate' );
				} else {
					$meta_text = __( 'Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'kriate' );
				}

			} else {
				// But this blog has loads of categories so we should probably display them here
				if ( '' != $tag_list ) {
					$meta_text = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'kriate' );
				} else {
					$meta_text = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'kriate' );
				}

			} // end check for categories on this blog

			printf(
				$meta_text,
				$category_list,
				$tag_list,
				get_permalink()
			);
		?>

		<?php edit_post_link( __( 'Edit', 'kriate' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-footer -->

			<?php kriate_post_nav(); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() ) :
					comments_template();
				endif;
			?>

		<?php endwhile; // end of the loop. ?>

<?php get_sidebar(); ?>

</div><!-- end content -->

</section>

<?php get_footer(); ?>