<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up to the main content.
 *
 * @package kriate
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'briite' ); ?></a>
	<header id="masthead" class="site-header" role="banner">
		<div class="logo">
			<?php $kriate_home_label = get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : __( 'Home', 'briite' ); ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( $kriate_home_label ); ?>">
				<?php if ( get_header_image() ) : ?>
					<img
						src="<?php echo esc_url( get_header_image() ); ?>"
						height="<?php echo esc_attr( get_custom_header()->height ); ?>"
						width="<?php echo esc_attr( get_custom_header()->width ); ?>"
						alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
					>
				<?php endif; ?>
			</a>
		</div><!-- end logo -->
		<button id="menu_icon" class="menu-toggle" type="button" aria-controls="site-navigation" aria-expanded="false">
			<span class="screen-reader-text"><?php esc_html_e( 'Toggle navigation', 'briite' ); ?></span>
		</button>
		<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'briite' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_id'        => 'primary-menu',
				)
			);
			?>
		</nav><!-- #site-navigation -->
		<div class="footer clearfix">
			<ul class="social clearfix">
				<li><span class="social-icon fb" aria-hidden="true"></span></li>
				<li><span class="social-icon google" aria-hidden="true"></span></li>
				<li><span class="social-icon behance" aria-hidden="true"></span></li>
				<li><a href="<?php echo esc_url( get_feed_link() ); ?>" class="rss" data-title="RSS"><span class="screen-reader-text"><?php esc_html_e( 'RSS', 'briite' ); ?></span></a></li>
			</ul><!-- end social -->
			<div class="rights">
				<p><?php esc_html_e( 'Proudly powered by', 'briite' ); ?> <a href="https://wordpress.org/">WordPress</a></p>
				<p>
					<?php
					printf(
						/* translators: 1: theme name, 2: theme author link. */
						wp_kses_post( __( 'Theme: %1$s by %2$s.', 'briite' ) ),
						'Briite',
						'<a href="https://agustealo.com/" rel="designer">Kriate Project</a>'
					);
					?>
				</p>
			</div><!-- end rights -->
		</div><!-- end footer -->
	</header><!-- #masthead -->
	<section id="content" class="main" tabindex="-1">
