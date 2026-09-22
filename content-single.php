<?php
/**
 * Template part for displaying a single post.
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
		/* translators: Used between category list items. */
		$kriate_category_list = get_the_category_list( __( ', ', 'kriate' ) );

		/* translators: Used between tag list items. */
		$kriate_tag_list = get_the_tag_list( '', __( ', ', 'kriate' ) );

		if ( ! kriate_categorized_blog() ) {
			if ( '' !== $kriate_tag_list ) {
				/* translators: 1: categories, 2: tags, 3: post permalink. */
				$kriate_meta_text = __( 'This entry was tagged %2$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'kriate' );
			} else {
				/* translators: 1: categories, 2: tags, 3: post permalink. */
				$kriate_meta_text = __( 'Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'kriate' );
			}
		} elseif ( '' !== $kriate_tag_list ) {
			/* translators: 1: categories, 2: tags, 3: post permalink. */
			$kriate_meta_text = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'kriate' );
		} else {
			/* translators: 1: categories, 2: tags, 3: post permalink. */
			$kriate_meta_text = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'kriate' );
		}

		echo wp_kses_post(
			sprintf(
				$kriate_meta_text,
				$kriate_category_list,
				$kriate_tag_list,
				esc_url( get_permalink() )
			)
		);
		?>

		<?php edit_post_link( esc_html__( 'Edit', 'kriate' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
