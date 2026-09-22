<?php
/**
 * Custom template tags for this theme.
 *
 * @package kriate
 */

if ( ! function_exists( 'kriate_paging_nav' ) ) :
	/**
	 * Display navigation to the next or previous set of posts when applicable.
	 *
	 * @return void
	 */
	function kriate_paging_nav() {
		global $wp_query;

		if ( $wp_query->max_num_pages < 2 ) {
			return;
		}
		?>
		<nav class="navigation paging-navigation" role="navigation">
			<h1 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'kriate' ); ?></h1>
			<div class="nav-links">
				<?php if ( get_next_posts_link() ) : ?>
				<div class="nav-previous"><?php next_posts_link( wp_kses_post( __( '<span class="meta-nav">&larr;</span> Older posts', 'kriate' ) ) ); ?></div>
				<?php endif; ?>

				<?php if ( get_previous_posts_link() ) : ?>
				<div class="nav-next"><?php previous_posts_link( wp_kses_post( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'kriate' ) ) ); ?></div>
				<?php endif; ?>
			</div><!-- .nav-links -->
		</nav><!-- .navigation -->
		<?php
	}
endif;

if ( ! function_exists( 'kriate_post_nav' ) ) :
	/**
	 * Display navigation to the next or previous post when applicable.
	 *
	 * @return void
	 */
	function kriate_post_nav() {
		$kriate_current_post = get_post();
		$kriate_previous     = is_attachment() && $kriate_current_post ? get_post( $kriate_current_post->post_parent ) : get_adjacent_post( false, '', true );
		$kriate_next         = get_adjacent_post( false, '', false );

		if ( ! $kriate_next && ! $kriate_previous ) {
			return;
		}
		?>
		<nav class="navigation post-navigation" role="navigation">
			<h1 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'kriate' ); ?></h1>
			<div class="nav-links">
				<?php
				/* translators: %title: previous post title. */
				previous_post_link(
					'<div class="nav-previous">%link</div>',
					wp_kses_post( _x( '<span class="meta-nav">&larr;</span>&nbsp;%title', 'Previous post link', 'kriate' ) )
				);
				/* translators: %title: next post title. */
				next_post_link(
					'<div class="nav-next">%link</div>',
					wp_kses_post( _x( '%title&nbsp;<span class="meta-nav">&rarr;</span>', 'Next post link', 'kriate' ) )
				);
				?>
			</div><!-- .nav-links -->
		</nav><!-- .navigation -->
		<?php
	}
endif;

if ( ! function_exists( 'kriate_posted_on' ) ) :
	/**
	 * Print HTML with meta information for the current post date and author.
	 *
	 * @return void
	 */
	function kriate_posted_on() {
		$kriate_time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$kriate_time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';
		}

		$kriate_time_string = sprintf(
			$kriate_time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		/* translators: %s: linked post date. */
		$kriate_posted_on = sprintf(
			_x( 'Posted on %s', 'post date', 'kriate' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $kriate_time_string . '</a>'
		);

		/* translators: %s: linked post author. */
		$kriate_byline = sprintf(
			_x( 'by %s', 'post author', 'kriate' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo wp_kses_post( '<span class="posted-on">' . $kriate_posted_on . '</span><span class="byline"> ' . $kriate_byline . '</span>' );
	}
endif;

/**
 * Return whether the blog has more than one populated category.
 *
 * @return bool
 */
function kriate_categorized_blog() {
	$kriate_category_count = get_transient( 'kriate_categories' );

	if ( false === $kriate_category_count ) {
		$kriate_category_ids = get_categories(
			array(
				'fields'     => 'ids',
				'hide_empty' => 1,
				'number'     => 2,
			)
		);

		$kriate_category_count = count( $kriate_category_ids );
		set_transient( 'kriate_categories', $kriate_category_count );
	}

	return $kriate_category_count > 1;
}

/**
 * Flush the transient used by kriate_categorized_blog().
 *
 * @return void
 */
function kriate_category_transient_flusher() {
	delete_transient( 'kriate_categories' );
}
add_action( 'edit_category', 'kriate_category_transient_flusher' );
add_action( 'save_post', 'kriate_category_transient_flusher' );
