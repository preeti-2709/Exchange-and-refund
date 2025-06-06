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


$width = "<script>document.write(screen.width);</script>";
?>

<script>
	// Function to set cookie
	function setCookie(name, value, days) {
		var expires = "";
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			expires = "; expires=" + date.toUTCString();
		}
		document.cookie = name + "=" + (value || "") + expires + "; path=/";
	}

	// Function to detect and set screen width cookie
	function setScreenWidthCookie() {
		var width = window.innerWidth;
		setCookie("screen_width", width, 1); // Cookie expires in 1 day
	}

	// Call the function to set cookie on page load
	window.onload = function() {
		setScreenWidthCookie();
	};
</script>


<?php

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

			<?php if (woocommerce_product_loop()) :
			?>


				<?php
				$screen_width = isset($_COOKIE['screen_width']) ? $_COOKIE['screen_width'] : null;
				if (wp_is_mobile() &&  $screen_width < 992) { ?>
					<div class="before-loop-wrapper tabs-filters tablate_show">
						<div class="tabs_show_header">
							<button type="button" data-bs-toggle="offcanvas" data-bs-target="#fullPageSidebar"
								aria-controls="fullPageSidebar" class="filter-toggle-btn">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M0.625 5.21984H10.8013C11.082 6.35398 12.1079 7.19746 13.3276 7.19746C14.5472 7.19746 15.5732 6.35398 15.8539 5.21984H19.375C19.7202 5.21984 20 4.94 20 4.59484C20 4.24969 19.7202 3.96984 19.375 3.96984H15.8538C15.5732 2.8357 14.5472 1.99219 13.3275 1.99219C12.1079 1.99219 11.0819 2.8357 10.8012 3.96984H0.625C0.279844 3.96984 0 4.24969 0 4.59484C0 4.94 0.279844 5.21984 0.625 5.21984ZM13.3276 3.24219C14.0734 3.24219 14.6802 3.84898 14.6802 4.5948C14.6802 5.34066 14.0734 5.94746 13.3276 5.94746C12.5817 5.94746 11.9749 5.34066 11.9749 4.5948C11.9749 3.84898 12.5817 3.24219 13.3276 3.24219ZM0.625 10.6256H4.14617C4.42688 11.7598 5.45277 12.6032 6.67246 12.6032C7.89215 12.6032 8.91805 11.7598 9.19875 10.6256H19.375C19.7202 10.6256 20 10.3458 20 10.0006C20 9.65547 19.7202 9.37562 19.375 9.37562H9.19871C8.91801 8.24148 7.89211 7.39797 6.67242 7.39797C5.45273 7.39797 4.42684 8.24148 4.14613 9.37562H0.625C0.279844 9.37562 0 9.65547 0 10.0006C0 10.3458 0.279805 10.6256 0.625 10.6256ZM6.67242 8.64797C7.41828 8.64797 8.02508 9.25477 8.02508 10.0006C8.02508 10.7464 7.41828 11.3532 6.67242 11.3532C5.92656 11.3532 5.31977 10.7464 5.31977 10.0006C5.31977 9.25477 5.92656 8.64797 6.67242 8.64797ZM19.375 14.7814H15.8538C15.5731 13.6473 14.5472 12.8038 13.3275 12.8038C12.1079 12.8038 11.082 13.6473 10.8012 14.7814H0.625C0.279844 14.7814 0 15.0612 0 15.4064C0 15.7516 0.279844 16.0314 0.625 16.0314H10.8013C11.082 17.1655 12.1079 18.0091 13.3276 18.0091C14.5473 18.0091 15.5732 17.1655 15.8539 16.0314H19.375C19.7202 16.0314 20 15.7516 20 15.4064C20 15.0612 19.7202 14.7814 19.375 14.7814ZM13.3276 16.7591C12.5817 16.7591 11.9749 16.1523 11.9749 15.4064C11.9749 14.6605 12.5817 14.0538 13.3276 14.0538C14.0734 14.0538 14.6802 14.6605 14.6802 15.4064C14.6802 16.1523 14.0734 16.7591 13.3276 16.7591Z" fill="#010101" />
								</svg>
								FILTER
							</button>
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


					</div>
				<?php } else { ?>
					<div class="before-loop-wrapper filter_short_color tablate_hide">

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
				<?php } ?>
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
					/**
					 * Hook: woocommerce_after_shop_loop.
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

	<!-- Full Page Sidebar -->
	<div class="offcanvas offcanvas-start offcanvas-fullpage filter_sidebar_wrap" tabindex="-1" id="fullPageSidebar"
		aria-labelledby="fullPageSidebarLabel">
		<div class="offcanvas-header">
			<h5 class="offcanvas-title" id="fullPageSidebarLabel"> Filter</h5>
			<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
		</div>
		<div class="offcanvas-body">
			<!-- Content goes here -->
			<?php if ($page_column_class['left_sidebar']): ?>
				<?php if (is_active_sidebar($boxshop_theme_options['ts_prod_cat_left_sidebar'])): ?>
					<div class="filters-tabs-container filter-options filter_options_wrap">

						<div class="filer-data-tabs">
							<?php dynamic_sidebar($boxshop_theme_options['ts_prod_cat_left_sidebar']); ?>
						</div>
					</div>
				<?php endif; ?>

			<?php endif; ?>
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