<?php
/**
 * Template part for displaying results in search pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package hexo
 */

?>

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

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'hexo' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<span class="read-more">
			<a href="<?php echo esc_url(get_permalink()); ?>" rel="bookmark"><?php esc_html_e('Read More', 'hexo') ?></a>
		</span>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
