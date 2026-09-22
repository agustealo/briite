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
					/* translators: %s: author name. */
					printf( esc_html__( 'Author: %s', 'kriate' ), '<span class="vcard">' . esc_html( get_the_author() ) . '</span>' );
				elseif ( is_day() ) :
					/* translators: %s: archive date. */
					printf( esc_html__( 'Day: %s', 'kriate' ), '<span>' . esc_html( get_the_date() ) . '</span>' );
				elseif ( is_month() ) :
					/* translators: %s: archive month. */
					printf( esc_html__( 'Month: %s', 'kriate' ), '<span>' . esc_html( get_the_date( _x( 'F Y', 'monthly archives date format', 'kriate' ) ) ) . '</span>' );
				elseif ( is_year() ) :
					/* translators: %s: archive year. */
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
			$kriate_term_description = term_description();
			if ( ! empty( $kriate_term_description ) ) :
				?>
				<div class="taxonomy-description"><?php echo wp_kses_post( $kriate_term_description ); ?></div>
			<?php endif; ?>
		</header><!-- .page-header -->

		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'content', get_post_format() );
		endwhile;
		?>

		<?php kriate_paging_nav(); ?>

	<?php else : ?>
		<?php get_template_part( 'content', 'none' ); ?>
	<?php endif; ?>

<?php get_footer(); ?>
