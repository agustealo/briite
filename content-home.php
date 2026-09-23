<?php
/**
 * Template part for the blog posts index grid.
 *
 * @package kriate
 */

?>

<div class="work">
	<a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php echo esc_attr( the_title_attribute( array( 'echo' => false ) ) ); ?>">
		<?php
		$kriate_thumbnail_id = get_post_thumbnail_id();

		if ( $kriate_thumbnail_id ) {
			the_post_thumbnail(
				kriate_get_compatible_image_size( $kriate_thumbnail_id, 'kriate-grid-thumb', 'grid-thumb' ),
				array( 'class' => 'media' )
			);
		}
		?>
		<div class="caption">
			<div class="work_title">
				<?php the_title( '<h1>', '</h1>' ); ?>
			</div>
		</div>
	</a>
</div>
