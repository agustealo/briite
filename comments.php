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
			$kriate_comments_number = get_comments_number();

			if ( 1 === $kriate_comments_number ) {
				/* translators: %s: post title. */
				$kriate_comments_format = __( 'One thought on &ldquo;%s&rdquo;', 'kriate' );
				$kriate_comments_title  = sprintf(
					$kriate_comments_format,
					'<span>' . esc_html( get_the_title() ) . '</span>'
				);
			} else {
				/* translators: 1: comment count, 2: post title. */
				$kriate_comments_format = _n( '%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $kriate_comments_number, 'kriate' );
				$kriate_comments_title  = sprintf(
					$kriate_comments_format,
					number_format_i18n( $kriate_comments_number ),
					'<span>' . esc_html( get_the_title() ) . '</span>'
				);
			}

			echo wp_kses_post( $kriate_comments_title );
			?>
		</h2>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		<nav id="comment-nav-above" class="comment-navigation" role="navigation">
			<h1 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'kriate' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( esc_html__( '← Older Comments', 'kriate' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments →', 'kriate' ) ); ?></div>
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
			<div class="nav-previous"><?php previous_comments_link( esc_html__( '← Older Comments', 'kriate' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments →', 'kriate' ) ); ?></div>
		</nav><!-- #comment-nav-below -->
		<?php endif; ?>

	<?php endif; ?>

	<?php if ( ! comments_open() && 0 !== get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'kriate' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</div><!-- #comments -->
