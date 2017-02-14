<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package hexo
 */

?>
<?php if ( is_single() ){ ?> <!--single page/post -->
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">

		<?php if (has_post_thumbnail()){ ?>

			<figure class="featured-image">
						<?php	the_post_thumbnail(); ?>
			</figure>

		<?php } ?>

		<?php
			the_title( '<h1 class="entry-title">', '</h1>' );

		if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php hexo_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php
		endif; ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
			the_content( sprintf(
				/* translators: %s: Name of current post. */
				wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'hexo' ), array( 'span' => array( 'class' => array() ) ) ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			) );

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'hexo' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php hexo_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

<?php } else { ?>
	<!--blog page-->
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">

			<date><?php hexo_posted_on(); ?></date>

			<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

					<?php if (has_post_thumbnail()){ ?>

						<figure class="featured-image">
									<a href="<?php echo esc_url(get_permalink()); ?>" rel="bookmark"><?php	the_post_thumbnail(); ?></a>
						</figure>

					<?php } ?>

			<?php if ( 'post' === get_post_type() ) : ?>
			<div class="entry-meta">
				<?php hexo_posted_by(); ?>
			</div><!-- .entry-meta -->
			<?php
			endif; ?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php
				the_excerpt();

				
			?>
		</div><!-- .entry-content -->

		<footer class="entry-footer">
			<span class="read-more">
				<a href="<?php echo esc_url(get_permalink()); ?>" rel="bookmark"><?php esc_html_e('Read More', 'hexo') ?></a>
			</span>
		</footer><!-- .entry-footer -->
	</article><!-- #post-## -->
	<?php } ?>
