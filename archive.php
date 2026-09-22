<?php
/**
 * The template for displaying archive pages.
 *
 * @package kriate
 */

get_header(); ?>

	<?php if ( have_posts() ) : ?>

		<header class="page-header">
			<h1 class="page-title">
				<?php
				if ( is_category() ) :
					single_cat_title();
				elseif ( is_tag() ) :
					single_tag_title();
				elseif ( is_author() ) :
					printf( esc_html__( 'Author: %s', 'kriate' ), '<span class="vcard">' . esc_html( get_the_author() ) . '</span>' );
				elseif ( is_day() ) :
					printf( esc_html__( 'Day: %s', 'kriate' ), '<span>' . esc_html( get_the_date() ) . '</span>' );
				elseif ( is_month() ) :
					printf( esc_html__( 'Month: %s', 'kriate' ), '<span>' . esc_html( get_the_date( _x( 'F Y', 'monthly archives date format', 'kriate' ) ) ) . '</span>' );
				elseif ( is_year() ) :
					printf( esc_html__( 'Year: %s', 'kriate' ), '<span>' . esc_html( get_the_date( _x( 'Y', 'yearly archives date format', 'kriate' ) ) ) . '</span>' );
				elseif ( is_tax( 'post_format', 'post-format-aside' ) ) :
					esc_html_e( 'Asides', 'kriate' );
				elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) :
					esc_html_e( 'Galleries', 'kriate' );
				elseif ( is_tax( 'post_format', 'post-format-image' ) ) :
					esc_html_e( 'Images', 'kriate' );
				elseif ( is_tax( 'post_format', 'post-format-video' ) ) :
					esc_html_e( 'Videos', 'kriate' );
				elseif ( is_tax( 'post_format', 'post-format-quote' ) ) :
					esc_html_e( 'Quotes', 'kriate' );
				elseif ( is_tax( 'post_format', 'post-format-link' ) ) :
					esc_html_e( 'Links', 'kriate' );
				elseif ( is_tax( 'post_format', 'post-format-status' ) ) :
					esc_html_e( 'Statuses', 'kriate' );
				elseif ( is_tax( 'post_format', 'post-format-audio' ) ) :
					esc_html_e( 'Audios', 'kriate' );
				elseif ( is_tax( 'post_format', 'post-format-chat' ) ) :
					esc_html_e( 'Chats', 'kriate' );
				else :
					esc_html_e( 'Archives', 'kriate' );
				endif;
				?>
			</h1>
			<?php
			$term_description = term_description();
			if ( ! empty( $term_description ) ) :
				?>
				<div class="taxonomy-description"><?php echo wp_kses_post( $term_description ); ?></div>
			<?php endif; ?>
		</header><!-- .page-header -->

		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'content', get_post_format() ); ?>
		<?php endwhile; ?>

		<?php kriate_paging_nav(); ?>

	<?php else : ?>
		<?php get_template_part( 'content', 'none' ); ?>
	<?php endif; ?>

<?php get_footer(); ?>
