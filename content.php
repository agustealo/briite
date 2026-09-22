<?php
/**
 * Template part for displaying posts in archive and index views.
 *
 * @package kriate
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

		<?php if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php kriate_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content( wp_kses_post( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'kriate' ) ) ); ?>
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
		<?php if ( 'post' === get_post_type() ) : ?>
			<?php
			/* translators: Used between category list items. */
			$kriate_categories_list = get_the_category_list( __( ', ', 'kriate' ) );
			if ( $kriate_categories_list && kriate_categorized_blog() ) :
				?>
			<span class="cat-links">
				<?php
				/* translators: %1$s: list of post categories. */
				echo wp_kses_post( sprintf( __( 'Posted in %1$s', 'kriate' ), $kriate_categories_list ) );
				?>
			</span>
			<?php endif; ?>

			<?php
			/* translators: Used between tag list items. */
			$kriate_tags_list = get_the_tag_list( '', __( ', ', 'kriate' ) );
			if ( $kriate_tags_list ) :
				?>
			<span class="tags-links">
				<?php
				/* translators: %1$s: list of post tags. */
				echo wp_kses_post( sprintf( __( 'Tagged %1$s', 'kriate' ), $kriate_tags_list ) );
				?>
			</span>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( ! post_password_required() && ( comments_open() || 0 !== get_comments_number() ) ) : ?>
		<span class="comments-link"><?php comments_popup_link( esc_html__( 'Leave a comment', 'kriate' ), esc_html__( '1 Comment', 'kriate' ), esc_html__( '% Comments', 'kriate' ) ); ?></span>
		<?php endif; ?>

		<?php edit_post_link( esc_html__( 'Edit', 'kriate' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
