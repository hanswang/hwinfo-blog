<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package hexo
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> id="totop">
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'hexo' ); ?></a>

	<?php if ( has_header_image() ) { ?>
	<?php echo '<header id="masthead" class="site-header" role="banner" style="background-image: url(' . esc_url(get_header_image()) . '">';

} else {?>
	<?php echo '<header id="masthead" class="site-header" role="banner">'; }?>

<div class="topm">
	<div class="container1">
		<div class="row collapse" id="collapsing-navbar">

			<div class="col-md-6 flex-1">
				<aside id="secondary" class="widget-area" role="complementary">
					<?php dynamic_sidebar( 'sidebar-2' ); ?>
				</aside>
			</div>

			<nav class="navbar navbar-light bg-faded col-md-6 flex-2">

				<?php
					 wp_nav_menu( array(
							 'theme_location'    => 'primary',
							 'depth'             => 4,
							 'container'         => '',
							 'container_class'   => 'nav navbar-nav',
							 'container_id'      => 'bs-example-navbar-collapse-1',
							 'menu_class'        => 'nav navbar-nav',
							 'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
							 'walker'            => new wp_bootstrap_navwalker())
					 );
					 ?>
			</nav>

		</div>
	</div>
</div>

			<button class="navbar-toggler hamburger hamburger--elastic" type="button" data-toggle="collapse" data-target="#collapsing-navbar">
				 <span class="hamburger-box">
					 <span class="hamburger-inner"></span>
				 </span>
			</button>

			<div class="container">
				<div class="row">
					<div class="site-branding">
						<?php
						if ( is_front_page() && is_home() ) : ?>
							<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						<?php else : ?>
							<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
						<?php
						endif;

						$description = get_bloginfo( 'description', 'display' );
						if ( $description || is_customize_preview() ) : ?>
							<p class="site-description"><?php echo $description; /* WPCS: xss ok. */ ?></p>
						<?php
						endif; ?>
					</div><!-- .site-branding -->
				</div>
			</div>


	</header><!-- #masthead -->

	<div id="content" class="site-content">
		<div class="container">
			<div class="row">
