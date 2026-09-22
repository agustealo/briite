<?php
/**
 * The template for displaying all single posts.
 *
 * @package kriate
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
	<?php
	$background_color = sprintf(
		'%02x%02x%02x',
		mt_rand( 0, 75 ),
		mt_rand( 0, 75 ),
		mt_rand( 0, 75 )
	);
	$background_style = 'background-color: #' . $background_color . ';';
	$featured_image   = get_the_post_thumbnail_url( get_the_ID(), 'single-banner' );

	if ( $featured_image ) {
		$background_style .= " background-image: url('" . esc_url_raw( $featured_image ) . "');";
	}
	?>

	<section class="top" style="<?php echo esc_attr( $background_style ); ?>">
		<div class="wrapper content_header clearfix">
			<div class="work_nav">
				<ul class="btn clearfix">
					<li>
						<?php $next_post = get_next_post(); ?>
						<?php if ( ! empty( $next_post ) ) : ?>
							<a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" class="previous" data-title="<?php esc_attr_e( 'Previous', 'kriate' ); ?>">
								<span class="screen-reader-text"><?php esc_html_e( 'Previous post', 'kriate' ); ?></span>
							</a>
						<?php endif; ?>
					</li>
					<li>
						<?php
						$categories = get_the_category();
						foreach ( $categories as $category ) {
							if ( 0 === (int) $category->category_parent && 1 !== (int) $category->term_id ) {
								?>
								<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="grid" data-title="<?php esc_attr_e( 'Category', 'kriate' ); ?>">
									<span class="screen-reader-text"><?php echo esc_html( $category->name ); ?></span>
								</a>
								<?php
								break;
							}
						}
						?>
					</li>
					<li>
						<?php $previous_post = get_previous_post(); ?>
						<?php if ( ! empty( $previous_post ) ) : ?>
							<a href="<?php echo esc_url( get_permalink( $previous_post->ID ) ); ?>" class="next" data-title="<?php esc_attr_e( 'Next', 'kriate' ); ?>">
								<span class="screen-reader-text"><?php esc_html_e( 'Next post', 'kriate' ); ?></span>
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
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'kriate' ),
						'after'  => '</div>',
					)
				);
				?>
			</article><!-- .entry-content -->

			<footer class="entry-footer">
				<?php
				$category_list = get_the_category_list( esc_html__( ', ', 'kriate' ) );
				$tag_list      = get_the_tag_list( '', esc_html__( ', ', 'kriate' ) );

				if ( ! kriate_categorized_blog() ) {
					if ( '' !== $tag_list ) {
						$meta_text = __( 'This entry was tagged %2$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'kriate' );
					} else {
						$meta_text = __( 'Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'kriate' );
					}
				} elseif ( '' !== $tag_list ) {
					$meta_text = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'kriate' );
				} else {
					$meta_text = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'kriate' );
				}

				printf(
					wp_kses_post( $meta_text ),
					wp_kses_post( $category_list ),
					wp_kses_post( $tag_list ),
					esc_url( get_permalink() )
				);
				?>

				<?php edit_post_link( esc_html__( 'Edit', 'kriate' ), '<span class="edit-link">', '</span>' ); ?>
			</footer><!-- .entry-footer -->

			<?php kriate_post_nav(); ?>

			<?php
			if ( comments_open() || 0 < (int) get_comments_number() ) {
				comments_template();
			}
			?>

		<?php endwhile; ?>

		<?php get_sidebar(); ?>
		</div><!-- end content -->
	</section>

<?php get_footer(); ?>
