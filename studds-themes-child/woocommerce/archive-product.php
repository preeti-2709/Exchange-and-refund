<?php

/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined('ABSPATH') || exit;

global $boxshop_theme_options;

get_header();

$extra_class = "";
$page_column_class = boxshop_page_layout_columns_class($boxshop_theme_options['ts_prod_cat_layout']);

$term = get_queried_object();
$taxonomy = $term->taxonomy;
$term_id = $term->term_id;

$post_id = $taxonomy . '_' . $term_id;
$term_title = $term->name;

$banner_image_url = get_field('category_banner_image', $post_id) ?: get_stylesheet_directory_uri() . '/assets/img/hero_banner_slider.jpg';

$show_top_content_widget_area = is_active_sidebar('product-category-top-content') && $boxshop_theme_options['ts_prod_cat_top_content'];
$session_one = WC()->session->get('exchange_session_start');
if ($session_one == '1') {
	$expire_time = WC()->session->get('expire_time');
	$session_time = time() - $expire_time;
} else {
	$session_time = '';
	$session_one = '';
}
wp_enqueue_script('jquery');
wp_enqueue_script('exchangepagejs', get_template_directory_uri() . '/js/exchange.js', array(), rand(1, 100), true);
?>
<input type="hidden" name="session_one" id="session_exchange" value="<?php echo $session_one ?>">
<input type="hidden" name="session_time" id="session_time" value="<?php echo $expire_time ?>">

<!-- <div class="hero_banner_slider">
	<div class="hero_banner_content">
		<h2 class="hero_title">FULL FACE HELMET</h2>
		<div class="breadcrumb">
			<a href="#">HOME</a> / <a href="#">HELMETS</a> / <span>FULL FACE HELMET</span>
		</div>
	</div>
</div> -->

<?php if (!empty($banner_image_url)): ?>
	<div class="hero_banner_slider" style="background-image: url('<?php echo esc_url($banner_image_url); ?>');">
		<div class="hero_banner_content">
			<?php if (!empty($term_title)): ?>
				<h2 class="hero_title"><?php echo $term_title; ?></h2>
			<?php endif; ?>
			<div>
				<?php boxshop_breadcrumbs_title(true); ?>
			</div>
		</div>
	</div>
<?php endif; ?>

<div class="container <?php echo esc_attr($extra_class) ?>">

	<?php
	function display_subcategories_section($categories = [])
	{
		if (empty($categories) || !is_array($categories)) {
			return;
		}
	?>
		<div class="sub_categories">
			<div class="subcategories-section subcategories_swipe">
				<div class="swiper-button-prev"></div>
				<div class="swiper subcategories_categories">
					<div class="swiper-wrapper">
						<?php foreach ($categories as $cat):
							$thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
							$image_url = wp_get_attachment_url($thumbnail_id);
							$default_image_url = get_stylesheet_directory_uri() . '/assets/img/default-helmet-category-image.png';
							$category_link = get_term_link($cat);

							$category_slider_media = get_field('category_slider_video', 'product_cat_' . $cat->term_id);
							$final_media_url = $category_slider_media ?: ($image_url ?: $default_image_url);
							$media_is_video = preg_match('/\.(mp4|webm|ogg)$/i', $final_media_url);

						?>
							<div class="swiper-slide subcategory-card">
								<a href="<?php echo esc_url($category_link); ?>">
									<div class="subcategory-info">
										<?php if ($media_is_video): ?>
											<video class="subcategory-thumbnail" autoplay muted loop playsinline>
												<source src="<?php echo esc_url($final_media_url); ?>" type="video/mp4">
												Your browser does not support the video tag.
											</video>
										<?php else: ?>
											<img src="<?php echo esc_url($final_media_url); ?>" alt="<?php echo esc_attr($cat->name); ?>" class="subcategory-thumbnail" />
										<?php endif; ?>
										<div class="subcategory_card_dls">
											<h3><?php echo esc_html($cat->name); ?></h3>
											<p>
												Shop Now
												<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M3.33301 8L12.6663 8" stroke="#010101" stroke-linecap="round" stroke-linejoin="round" />
													<path d="M8.66699 12L12.667 8" stroke="#010101" stroke-linecap="round" stroke-linejoin="round" />
													<path d="M8.66699 4L12.667 8" stroke="#010101" stroke-linecap="round" stroke-linejoin="round" />
												</svg>
											</p>
										</div>
									</div>
								</a>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="swiper-button-next"></div>
			</div>
		</div>
	<?php
	}
	?>
	<?php
	// Dynamically get current product category object
	$current_cat = get_queried_object();

	if (is_shop()) {
		// Get the ID of the 'uncategorised' term (if exists)
		$uncategorised = get_term_by('slug', 'uncategorised', 'product_cat');
		$exclude_ids = $uncategorised ? [$uncategorised->term_id] : [];

		// Shop page: get top-level categories excluding 'uncategorised'
		$top_level_cats = get_terms([
			'taxonomy' => 'product_cat',
			'parent' => 0,
			'exclude' => $exclude_ids,
			'hide_empty' => false
		]);

		if (!empty($top_level_cats)) {
			display_subcategories_section($top_level_cats);
		}
	} elseif ($current_cat instanceof WP_Term && $current_cat->taxonomy === 'product_cat') {
		// Category page: get subcategories of current category
		$child_cats = get_terms([
			'taxonomy' => 'product_cat',
			'parent' => $current_cat->term_id,
			'hide_empty' => false
		]);
		if (!empty($child_cats)) {
			display_subcategories_section($child_cats);
		}
	}
	?>
	<?php
	/**
	 * woocommerce_before_main_content hook
	 *
	 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
	 * @hooked woocommerce_breadcrumb - 20
	 */
	do_action('woocommerce_before_main_content');
	?>
	<div id="main-content" class="shop_content_wrap">
		<div id="primary" class="site-content">
			<?php do_action('woocommerce_archive_description'); ?>

			<?php if (woocommerce_product_loop()) : ?>

				<div class="before-loop-wrapper filter_short_color">

					<!-- Left Sidebar -->
					<?php if ($page_column_class['left_sidebar']): ?>
						<!-- <aside id="left-sidebar" class="ts-sidebar <?php echo esc_attr($page_column_class['left_sidebar_class']); ?>"> -->
						<?php if (is_active_sidebar($boxshop_theme_options['ts_prod_cat_left_sidebar'])): ?>
							<div class="filters-container filter-options filter_options_wrap d-none">
								<strong>FILTER :</strong>
								<?php dynamic_sidebar($boxshop_theme_options['ts_prod_cat_left_sidebar']); ?>
							</div>
						<?php endif; ?>
						<!-- </aside> -->
					<?php endif; ?>

					<?php
					/**
					 * woocommerce_before_shop_loop hook
					 *
					 * @hooked woocommerce_result_count - 20
					 * @hooked woocommerce_catalog_ordering - 30
					 */
					// do_action('woocommerce_before_shop_loop');
					woocommerce_catalog_ordering();

					?>
				</div>
				<div class="selected_filter_roles">
					<ul class="selected-filters">

					</ul>
					<div class="clear-all-filters" style="display: none;">
						<a href="javascript:void(0)" class="clear-filer">CLEAR ALL</a>
					</div>
				</div>

				<!-- Top Content -->
				<?php if ($show_top_content_widget_area): ?>
					<aside class="ts-sidebar product-category-top-content" style="display: none">
						<?php dynamic_sidebar('product-category-top-content'); ?>
					</aside>
				<?php endif; ?>

				<?php
				global $woocommerce_loop;
				if (absint($boxshop_theme_options['ts_prod_cat_columns']) > 0) {
					$woocommerce_loop['columns'] = absint($boxshop_theme_options['ts_prod_cat_columns']);
				}
				?>
				<?php //echo do_shortcode('[products limit="3" columns="3" best_selling="true" ]"); 
				?>
				<div id="studds_product_filter_results" class="woocommerce columns-<?php echo esc_attr($woocommerce_loop['columns']); ?>">
					<?php
					woocommerce_product_loop_start();

					if (wc_get_loop_prop('total')) {
						while (have_posts()) {
							the_post();

							// do_action( 'woocommerce_shop_loop' );

							wc_get_template_part('content', 'product');
						}
					}

					woocommerce_product_loop_end();
					?>
				</div>

				<div class="after-loop-wrapper">
					<?php
					/**
					 * woocommerce_after_shop_loop hook
					 *
					 * @hooked woocommerce_pagination - 10
					 */
					do_action('woocommerce_after_shop_loop');
					?>
				</div>

			<?php else: ?>

				<?php do_action('woocommerce_no_products_found'); ?>

			<?php endif; ?>

			<?php
			/**
			 * woocommerce_after_main_content hook
			 *
			 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
			 */
			do_action('woocommerce_after_main_content');
			?>
		</div>
	</div>
	<!-- Right Sidebar -->
	<?php if ($page_column_class['right_sidebar']): ?>
		<aside id="right-sidebar" class="ts-sidebar <?php echo esc_attr($page_column_class['right_sidebar_class']); ?>">
			<?php if (is_active_sidebar($boxshop_theme_options['ts_prod_cat_right_sidebar'])): ?>
				<?php dynamic_sidebar($boxshop_theme_options['ts_prod_cat_right_sidebar']); ?>
			<?php endif; ?>
		</aside>
	<?php endif; ?>

</div>
<?php get_footer('shop'); ?>