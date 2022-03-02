<?php
$woodmart_loop = woodmart_loop_prop( 'woodmart_loop' );

$classes = array();

$blog_design = woodmart_loop_prop( 'blog_design' );
$classes[]   = 'blog-post-loop';
$classes[]   = 'post-slide';
$classes[]   = 'blog-design-' . $blog_design;

if ( 'meta-image' !== $blog_design ) {
	$classes[] = 'blog-style-' . woodmart_get_opt( 'blog_style' );
}

if ( ! get_the_title() ) {
	$classes[] = 'post-no-title';
}

$random = rand( 100, 999 );

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $classes ); ?>>
	<div class="article-inner">
		<header class="entry-header">
			<?php if ( has_post_thumbnail() && ! post_password_required() && ! is_attachment() && woodmart_loop_prop( 'parts_media' ) ) : ?>
				<figure class="entry-thumbnail">
					<div class="post-img-wrapp">
						<a href="<?php echo esc_url( get_permalink() ); ?>">
							<?php echo woodmart_get_post_thumbnail( 'large' ); ?>
						</a>
					</div>
					<div class="post-image-mask">
						<span></span>
					</div>
				</figure>
			<?php endif; ?>

			<?php
			woodmart_post_date(
				array(
					'style' => 'wd-style-with-bg',
				)
			);
			?>
		</header><!-- .entry-header -->

		<div class="article-body-container">
			<?php if ( woodmart_loop_prop( 'parts_meta' ) && get_the_category_list( ', ' ) ) : ?>
				<div class="meta-categories-wrapp"><div class="meta-post-categories wd-post-cat wd-style-with-bg"><?php echo get_the_category_list( ', ' ); ?></div></div>
			<?php endif ?>

			<?php if ( woodmart_loop_prop( 'parts_title' ) ) : ?>
				<h3 class="wd-entities-title title post-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php the_title(); ?></a>
				</h3>
			<?php endif; // is_single() ?>

			<?php if ( woodmart_loop_prop( 'parts_meta' ) ) : ?>
				<div class="entry-meta wd-entry-meta">
					<?php
					woodmart_post_meta(
						array(
							'author'        => 1,
							'author_avatar' => 1,
							'date'          => 0,
							'comments'      => 1,
							'author_label'  => 'long',
						)
					);
					?>
				</div><!-- .entry-meta -->
			<?php endif ?>
			<?php if ( woodmart_is_social_link_enable( 'share' ) ) : ?>
				<div class="hovered-social-icons wd-tltp wd-tltp-top">
					<div class="wd-tooltip-label">
						<?php
						if ( function_exists( 'woodmart_shortcode_social' ) ) {
							echo woodmart_shortcode_social(
								array(
									'size'  => 'small',
									'color' => 'light',
								)
							);}
						?>
					</div>
				</div>	
			<?php endif ?>

			<?php if ( woodmart_loop_prop( 'parts_text' ) ) : ?>
				<div class="entry-content wd-entry-content<?php echo woodmart_get_old_classes( ' woodmart-entry-content' ); ?>">
					<?php woodmart_get_content( woodmart_loop_prop( 'parts_btn' ) ); ?>
				</div><!-- .entry-content -->
			<?php endif; ?>
		</div>
	</div>
</article><!-- #post -->

<?php
// Increase loop count
woodmart_set_loop_prop( 'woodmart_loop', $woodmart_loop + 1 );
