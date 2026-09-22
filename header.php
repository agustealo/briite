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
<a class="screen-reader-text skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'kriate' ); ?></a>
	<header id="masthead" class="site-header" role="banner">
		<div class="logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
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
		<button id="menu_icon" class="menu-toggle" type="button" aria-controls="primary-menu" aria-expanded="false">
			<span class="screen-reader-text"><?php esc_html_e( 'Toggle navigation', 'kriate' ); ?></span>
		</button>
		<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'kriate' ); ?>">
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
				<li><a href="#" class="fb" data-title="Facebook"><span class="screen-reader-text"><?php esc_html_e( 'Facebook', 'kriate' ); ?></span></a></li>
				<li><a href="#" class="google" data-title="Google +"><span class="screen-reader-text"><?php esc_html_e( 'Google Plus', 'kriate' ); ?></span></a></li>
				<li><a href="#" class="behance" data-title="Behance"><span class="screen-reader-text"><?php esc_html_e( 'Behance', 'kriate' ); ?></span></a></li>
				<li><a href="#" class="rss" data-title="RSS"><span class="screen-reader-text"><?php esc_html_e( 'RSS', 'kriate' ); ?></span></a></li>
			</ul><!-- end social -->
			<div class="rights">
				<p><?php esc_html_e( 'Proudly powered by', 'kriate' ); ?> <a href="https://wordpress.org/">WordPress</a></p>
				<p>
					<?php
					printf(
						/* translators: 1: theme name, 2: theme author link. */
						wp_kses_post( __( 'Theme: %1$s by %2$s.', 'kriate' ) ),
						'Briite',
						'<a href="https://agustealo.com/" rel="designer">Kriate Project</a>'
					);
					?>
				</p>
			</div><!-- end rights -->
		</div><!-- end footer -->
	</header><!-- #masthead -->
	<section id="content" class="main">
