<?php
/**
 * The template part for displaying results in search pages.
 *
 * @package kriate
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content">
		<header class="entry-header">
			<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

			<?php if ( 'post' === get_post_type() ) : ?>
				<div class="entry-meta">
					<?php kriate_posted_on(); ?>
				</div><!-- .entry-meta -->
			<?php endif; ?>
		</header><!-- .entry-header -->

		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->

		<footer class="entry-footer">
			<?php if ( 'post' === get_post_type() ) : ?>
				<?php
				$categories_list = get_the_category_list( esc_html__( ', ', 'kriate' ) );
				if ( $categories_list && kriate_categorized_blog() ) :
					?>
					<span class="cat-links">
						<?php
						printf(
							/* translators: %s: post categories. */
							wp_kses_post( __( 'Posted in %1$s', 'kriate' ) ),
							wp_kses_post( $categories_list )
						);
						?>
					</span>
				<?php endif; ?>

				<?php
				$tags_list = get_the_tag_list( '', esc_html__( ', ', 'kriate' ) );
				if ( $tags_list ) :
					?>
					<span class="tags-links">
						<?php
						printf(
							/* translators: %s: post tags. */
							wp_kses_post( __( 'Tagged %1$s', 'kriate' ) ),
							wp_kses_post( $tags_list )
						);
						?>
					</span>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( ! post_password_required() && ( comments_open() || '0' !== get_comments_number() ) ) : ?>
				<span class="comments-link">
					<?php comments_popup_link( esc_html__( 'Leave a comment', 'kriate' ), esc_html__( '1 Comment', 'kriate' ), esc_html__( '% Comments', 'kriate' ) ); ?>
				</span>
			<?php endif; ?>

			<?php edit_post_link( esc_html__( 'Edit', 'kriate' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-footer -->
	</div><!-- .entry-content -->
</article><!-- #post-## -->
