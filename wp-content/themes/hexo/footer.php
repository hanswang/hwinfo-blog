<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package hexo
 */

?>
</div><!--row-->
</div><!--container-->
	</div><!-- #content -->

	<div class="bottomm">
				<div class="container1">
					<div class="row">
				<aside id="secondary" class="widget-area" role="complementary">
					<?php dynamic_sidebar( 'sidebar-1' ); ?>
				</aside><!-- #secondary -->
				</div><!--row-->
				</div><!--container1-->
	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-info">

			<div><a href="#totop" class="to-top">&#8593;</a></div>
			
			<a href="<?php echo esc_url( __( 'http://imrohan.com/', 'hexo' ) ); ?>"><?php printf( esc_html__( 'Proudly powered by %s', 'hexo' ), 'imrohan' ); ?></a>
			
		</div><!-- .site-info -->
	</footer><!-- #colophon -->

</div><!--bottomm-->

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
