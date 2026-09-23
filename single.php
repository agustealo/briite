<?php
/**
 * The template for displaying all single posts.
 *
 * @package kriate
 */

get_header();

while ( have_posts() ) :
	the_post();
	$kriate_background_color = sprintf(
		'%02x%02x%02x',
		wp_rand( 0, 75 ),
		wp_rand( 0, 75 ),
		wp_rand( 0, 75 )
	);
	$kriate_background_style = 'background-color: #' . $kriate_background_color . ';';
	$kriate_featured_image   = get_the_post_thumbnail_url( get_the_ID(), 'kriate-single-banner' );

	if ( $kriate_featured_image ) {
		$kriate_background_style .= " background-image: url('" . esc_url_raw( $kriate_featured_image ) . "');";
	}
	?>

	<section class="top" style="<?php echo esc_attr( $kriate_background_style ); ?>">
		<div class="wrapper content_header clearfix">
			<div class="work_nav">
				<ul class="btn clearfix">
					<li>
						<?php $kriate_next_post = get_next_post(); ?>
						<?php if ( ! empty( $kriate_next_post ) ) : ?>
							<a href="<?php echo esc_url( get_permalink( $kriate_next_post->ID ) ); ?>" class="previous" data-title="<?php esc_attr_e( 'Previous', 'briite' ); ?>">
								<span class="screen-reader-text"><?php esc_html_e( 'Previous post', 'briite' ); ?></span>
							</a>
						<?php endif; ?>
					</li>
					<li>
						<?php
						$kriate_categories = get_the_category();
						foreach ( $kriate_categories as $kriate_category ) {
							if ( 0 === (int) $kriate_category->category_parent && 1 !== (int) $kriate_category->term_id ) {
								?>
								<a href="<?php echo esc_url( get_category_link( $kriate_category->term_id ) ); ?>" class="grid" data-title="<?php esc_attr_e( 'Category', 'briite' ); ?>">
									<span class="screen-reader-text"><?php echo esc_html( $kriate_category->name ); ?></span>
								</a>
								<?php
								break;
							}
						}
						?>
					</li>
					<li>
						<?php $kriate_previous_post = get_previous_post(); ?>
						<?php if ( ! empty( $kriate_previous_post ) ) : ?>
							<a href="<?php echo esc_url( get_permalink( $kriate_previous_post->ID ) ); ?>" class="next" data-title="<?php esc_attr_e( 'Next', 'briite' ); ?>">
								<span class="screen-reader-text"><?php esc_html_e( 'Next post', 'briite' ); ?></span>
							</a>
						<?php endif; ?>
					</li>
				</ul>
			</div><!-- end work_nav -->
			<?php the_title( '<h1 class="title">', '</h1>' ); ?>
		</div>
	</section><!-- end top -->

	<section class="wrapper">
		<div class="content">
			<article class="entry-content">
				<?php the_content(); ?>
				<?php
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'briite' ),
						'after'  => '</div>',
					)
				);
				?>
			</article><!-- .entry-content -->

			<footer class="entry-footer">
				<?php
				$kriate_category_list = get_the_category_list( esc_html__( ', ', 'briite' ) );
				$kriate_tag_list      = get_the_tag_list( '', esc_html__( ', ', 'briite' ) );

				if ( ! kriate_categorized_blog() ) {
					if ( '' !== $kriate_tag_list ) {
						/* translators: 2: post tags, 3: post permalink. */
						$kriate_meta_text = __( 'This entry was tagged %2$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'briite' );
					} else {
						/* translators: 3: post permalink. */
						$kriate_meta_text = __( 'Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'briite' );
					}
				} elseif ( '' !== $kriate_tag_list ) {
					/* translators: 1: post categories, 2: post tags, 3: post permalink. */
					$kriate_meta_text = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'briite' );
				} else {
					/* translators: 1: post categories, 3: post permalink. */
					$kriate_meta_text = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'briite' );
				}

				printf(
					wp_kses_post( $kriate_meta_text ),
					wp_kses_post( $kriate_category_list ),
					wp_kses_post( $kriate_tag_list ),
					esc_url( get_permalink() )
				);
				?>

				<?php edit_post_link( esc_html__( 'Edit', 'briite' ), '<span class="edit-link">', '</span>' ); ?>
			</footer><!-- .entry-footer -->

			<?php kriate_post_nav(); ?>

			<?php
			if ( comments_open() || 0 < (int) get_comments_number() ) {
				comments_template();
			}
			?>

		<?php
endwhile;
?>

		<?php get_sidebar(); ?>
		</div><!-- end content -->
	</section>

<?php get_footer(); ?>
