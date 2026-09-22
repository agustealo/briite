<?php
/**
 * The template for displaying comments.
 *
 * The area of the page that contains both current comments and the comment form.
 *
 * @package kriate
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$kriate_comment_count = (int) get_comments_number();
			$kriate_post_title    = get_the_title();

			if ( 1 === $kriate_comment_count ) {
				printf(
					/* translators: %s: post title. */
					wp_kses_post( __( 'One thought on &ldquo;%s&rdquo;', 'kriate' ) ),
					'<span>' . esc_html( $kriate_post_title ) . '</span>'
				);
			} else {
				printf(
					/* translators: 1: number of comments, 2: post title. */
					wp_kses_post( __( '%1$s thoughts on &ldquo;%2$s&rdquo;', 'kriate' ) ),
					esc_html( number_format_i18n( $kriate_comment_count ) ),
					'<span>' . esc_html( $kriate_post_title ) . '</span>'
				);
			}
			?>
		</h2>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			<nav id="comment-nav-above" class="comment-navigation" role="navigation">
				<h1 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'kriate' ); ?></h1>
				<div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'kriate' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'kriate' ) ); ?></div>
			</nav><!-- #comment-nav-above -->
		<?php endif; ?>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
				)
			);
			?>
		</ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			<nav id="comment-nav-below" class="comment-navigation" role="navigation">
				<h1 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'kriate' ); ?></h1>
				<div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'kriate' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'kriate' ) ); ?></div>
			</nav><!-- #comment-nav-below -->
		<?php endif; ?>

	<?php endif; ?>

	<?php if ( ! comments_open() && 0 < (int) get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'kriate' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</div><!-- #comments -->
