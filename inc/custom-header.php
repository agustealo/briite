<?php
/**
 * Custom Header feature integration.
 *
 * @package kriate
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @return void
 */
function kriate_custom_header_setup() {
	add_theme_support(
		'custom-header',
		apply_filters(
			'kriate_custom_header_args',
			array(
				'random-default'     => false,
				'width'              => 162,
				'height'             => 21,
				'flex-height'        => false,
				'flex-width'         => false,
				'default-image'      => get_template_directory_uri() . '/images/logo.png',
				'default-text-color' => '000',
				'header-text'        => true,
				'uploads'            => true,
			)
		)
	);
}
add_action( 'after_setup_theme', 'kriate_custom_header_setup' );

if ( ! function_exists( 'kriate_header_style' ) ) :
	/**
	 * Output custom header text styles.
	 *
	 * @return void
	 */
	function kriate_header_style() {
		$header_text_color = get_header_textcolor();
		$custom_header     = get_theme_support( 'custom-header' );
		$default_color     = '000';

		if ( isset( $custom_header[0]['default-text-color'] ) ) {
			$default_color = (string) $custom_header[0]['default-text-color'];
		}

		if ( $default_color === $header_text_color ) {
			return;
		}
		?>
		<style type="text/css">
		<?php if ( 'blank' === $header_text_color ) : ?>
			.site-title,
			.site-description {
				position: absolute;
				clip: rect(1px, 1px, 1px, 1px);
			}
		<?php else : ?>
			.site-title a,
			.site-description {
				color: #<?php echo esc_attr( $header_text_color ); ?>;
			}
		<?php endif; ?>
		</style>
		<?php
	}
endif;

if ( ! function_exists( 'kriate_admin_header_style' ) ) :
	/**
	 * Output styles for the legacy Appearance > Header preview.
	 *
	 * @return void
	 */
	function kriate_admin_header_style() {
		?>
		<style type="text/css">
			.appearance_page_custom-header #headimg {
				border: none;
			}
		</style>
		<?php
	}
endif;

if ( ! function_exists( 'kriate_admin_header_image' ) ) :
	/**
	 * Render the legacy Appearance > Header preview markup.
	 *
	 * @return void
	 */
	function kriate_admin_header_image() {
		$header_text_color = get_header_textcolor();
		?>
		<div id="headimg">
			<h1 class="displaying-header-text">
				<a id="name" style="color:#<?php echo esc_attr( $header_text_color ); ?>;" onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php echo esc_html( get_bloginfo( 'name', 'display' ) ); ?>
				</a>
			</h1>
			<div class="displaying-header-text" id="desc" style="color:#<?php echo esc_attr( $header_text_color ); ?>;">
				<?php echo esc_html( get_bloginfo( 'description', 'display' ) ); ?>
			</div>
			<?php if ( get_header_image() ) : ?>
				<img src="<?php echo esc_url( get_header_image() ); ?>" alt="">
			<?php endif; ?>
		</div>
		<?php
	}
endif;
