<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after.
 *
 * @package accessiblezen
 * @since accessiblezen 1.0
 */
?>

	<?php get_sidebar(); ?>
	</div><!-- #main -->
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- HWinfo -->
        <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-9281883025637377" data-ad-slot="7142062241" data-ad-format="auto"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
	<footer id="colophon" class="site-footer cf" role="contentinfo">
		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<nav role="navigation" class="main-navigation cf">
				<h1 class="screen-reader-text"><?php _e( 'Main Menu', 'accessiblezen' ); ?></h1>

				<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class'      => 'nav', 'depth' => 1 ) ); ?>
			</nav><!-- .main-navigation -->
		<?php endif; ?>
		
		<?php if ( has_nav_menu( 'secondary' ) ) : ?>	
			<nav role="navigation" class="secondary-navigation cf">
				<h1 class="screen-reader-text"><?php _e( 'Secondary Menu', 'accessiblezen' ); ?></h1>

				<?php wp_nav_menu( array( 'theme_location' => 'secondary', 'menu_class'      => 'nav', 'depth' => 1 ) ); ?>
			</nav><!-- .secondary-navigation -->
		<?php endif; ?>

	</footer><!-- #colophon .site-footer -->
	<div class="skip-container cf">
		<a class="skip-link" href="#page"><?php _e( '&uarr; Back to the Top', 'accessiblezen' ); ?></a>
	</div><!-- .skip-container -->
</div><!-- #page .hfeed .site -->

<?php wp_footer(); ?>

</body>
</html>