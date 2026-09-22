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
					<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'kriate' ) ); ?></div>
				<?php endif; ?>

				<?php if ( get_previous_posts_link() ) : ?>
					<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'kriate' ) ); ?></div>
				<?php endif; ?>
			</div><!-- .nav-links -->
		</nav><!-- .navigation -->
		<?php
	}
endif;

if ( ! function_exists( 'kriate_post_nav' ) ) :
	/**
	 * Display navigation to adjacent posts when applicable.
	 *
	 * @return void
	 */
	function kriate_post_nav() {
		$previous = is_attachment() ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous ) {
			return;
		}
		?>
		<nav class="navigation post-navigation" role="navigation">
			<h1 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'kriate' ); ?></h1>
			<div class="nav-links">
				<?php
				/* translators: %title: title of the previous post. */
				$previous_link_text = _x( '<span class="meta-nav">&larr;</span>&nbsp;%title', 'Previous post link', 'kriate' );
				/* translators: %title: title of the next post. */
				$next_link_text = _x( '%title&nbsp;<span class="meta-nav">&rarr;</span>', 'Next post link', 'kriate' );

				previous_post_link( '<div class="nav-previous">%link</div>', $previous_link_text );
				next_post_link( '<div class="nav-next">%link</div>', $next_link_text );
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
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date link. */
			_x( 'Posted on %s', 'post date', 'kriate' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		$byline = sprintf(
			/* translators: %s: post author link. */
			_x( 'by %s', 'post author', 'kriate' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="posted-on">' . wp_kses_post( $posted_on ) . '</span><span class="byline"> ' . wp_kses_post( $byline ) . '</span>';
	}
endif;

/**
 * Determine whether the blog has more than one populated category.
 *
 * @return bool
 */
function kriate_categorized_blog() {
	$category_count = get_transient( 'kriate_categories' );

	if ( false === $category_count ) {
		$category_ids = get_categories(
			array(
				'fields'     => 'ids',
				'hide_empty' => 1,
				'number'     => 2,
			)
		);
		$category_count = count( $category_ids );
		set_transient( 'kriate_categories', $category_count );
	}

	return $category_count > 1;
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
