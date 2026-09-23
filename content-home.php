<?php
/**
 * Template part for the blog posts index grid.
 *
 * @package kriate
 */

?>

<div id="post-<?php the_ID(); ?>" <?php post_class( 'work' ); ?>>
	<a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php echo esc_attr( the_title_attribute( array( 'echo' => false ) ) ); ?>">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'briite-grid-thumb', array( 'class' => 'media' ) );
		}
		?>
		<div class="caption">
			<div class="work_title">
				<?php the_title( '<h1>', '</h1>' ); ?>
			</div>
		</div>
	</a>
</div>
