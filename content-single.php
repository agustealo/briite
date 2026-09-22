<?php
/**
 * The single-post content template part.
 *
 * @package kriate
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'work' ); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<div class="entry-meta">
			<?php kriate_posted_on(); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
		<?php
		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'kriate' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php
		$kriate_category_list = get_the_category_list( esc_html__( ', ', 'kriate' ) );
		$kriate_tag_list      = get_the_tag_list( '', esc_html__( ', ', 'kriate' ) );

		if ( ! kriate_categorized_blog() ) {
			if ( '' !== $kriate_tag_list ) {
				/* translators: 2: post tags, 3: post permalink. */
				$kriate_meta_text = __( 'This entry was tagged %2$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'kriate' );
			} else {
				/* translators: 3: post permalink. */
				$kriate_meta_text = __( 'Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'kriate' );
			}
		} elseif ( '' !== $kriate_tag_list ) {
			/* translators: 1: post categories, 2: post tags, 3: post permalink. */
			$kriate_meta_text = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'kriate' );
		} else {
			/* translators: 1: post categories, 3: post permalink. */
			$kriate_meta_text = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'kriate' );
		}

		printf(
			wp_kses_post( $kriate_meta_text ),
			wp_kses_post( $kriate_category_list ),
			wp_kses_post( $kriate_tag_list ),
			esc_url( get_permalink() )
		);
		?>

		<?php edit_post_link( esc_html__( 'Edit', 'kriate' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
