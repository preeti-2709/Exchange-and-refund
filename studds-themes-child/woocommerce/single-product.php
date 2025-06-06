<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $boxshop_theme_options;

get_header();

$extra_class = "";
$page_column_class = boxshop_page_layout_columns_class($boxshop_theme_options['ts_prod_layout']);

$show_page_title = $boxshop_theme_options['ts_prod_title'];
$show_breadcrumb = !isset($boxshop_theme_options['ts_prod_breadcrumb']) || ( isset($boxshop_theme_options['ts_prod_breadcrumb']) && $boxshop_theme_options['ts_prod_breadcrumb'] );
if( ($show_breadcrumb || $show_page_title) && isset($boxshop_theme_options['ts_breadcrumb_layout']) ){
	$extra_class = 'show_breadcrumb_'.$boxshop_theme_options['ts_breadcrumb_layout'];
}

boxshop_breadcrumbs_title($show_breadcrumb, $show_page_title, get_the_title());
wp_enqueue_script('jquery');
wp_enqueue_script( 'exchangepagejs', get_template_directory_uri() . '/js/exchange.js', array(), rand(1, 100), true);
?>

<div class="breadcrumbs_cutom">
	<div class="single_shop_container">
			<?php echo custom_breadcrumbs(); ?>
	</div>
</div>

<div class="shop-page page-container single_shop <?php echo esc_attr($extra_class) ?>" >
	<div class="single_shop_container">
	 
		<!-- Left Sidebar -->
		<?php if( $page_column_class['left_sidebar'] ): ?>
			<aside id="left-sidebar" class="ts-sidebar <?php echo esc_attr($page_column_class['left_sidebar_class']); ?>">
			<?php if( is_active_sidebar($boxshop_theme_options['ts_prod_left_sidebar']) ): ?>
				<?php dynamic_sidebar( $boxshop_theme_options['ts_prod_left_sidebar'] ); ?>
			<?php endif; ?>
			</aside>
		<?php endif; ?>

		<div id="main-content test" class="shop_details_wrapper">
			<div id="primary" class="site-content">
				<?php
					/**
					 * woocommerce_before_main_content hook
					 *
					 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
					 * @hooked woocommerce_breadcrumb - 20
					 */
					do_action( 'woocommerce_before_main_content' );
				?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php wc_get_template_part( 'content', 'single-product' ); ?>
				
				<?php endwhile; // end of the loop. ?>
					
				<?php
					/**
					 * woocommerce_after_main_content hook
					 *
					 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
					 */
					do_action( 'woocommerce_after_main_content' );
				?>
			</div>
		</div>
		
		<!-- Right Sidebar -->
		<?php if( $page_column_class['right_sidebar'] ): ?>
			<aside id="right-sidebar" class="ts-sidebar <?php echo esc_attr($page_column_class['right_sidebar_class']); ?>">
				<?php if( is_active_sidebar($boxshop_theme_options['ts_prod_right_sidebar']) ): ?>
					<?php dynamic_sidebar( $boxshop_theme_options['ts_prod_right_sidebar'] ); ?>
				<?php endif; ?>
			</aside>
		<?php endif; ?>

	</div>
</div>

	<div class="single_shop_container">
		<!-- Manually display WooCommerce tabs here -->
			<div class="custom-tab-wrapper">
			<?php wc_get_template( 'single-product/tabs/tabs.php' ); ?>
			</div>
	</div>

	<div class="single_shop_container">
		<?php
				// Display Customer Reviews Section
				if ( comments_open() ) : ?>
					<div id="customer-reviews" class="woocommerce-customer-reviews">
						<!-- Section Header -->
						<div>
							Customer Reviews
							<?php
								global $product;
								echo wc_get_rating_html( $product->get_average_rating(), $product->get_review_count() );
							?>
							<span>
								<?php echo $product->get_review_count(); ?> REVIEWS
							</span>
						</div>

						<!-- List of Reviews -->
						<div class="review-list">
							<?php comments_template(); ?>
						</div>

					</div>
				<?php endif; ?>
				<?php
					/**
					 * woocommerce_after_single_product_summary hook
					 * @hooked woocommerce_output_related_products - 20
					 */
					woocommerce_output_related_products();
				?>
	</div>

<?php get_footer( 'shop' ); ?>