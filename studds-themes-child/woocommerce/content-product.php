<?php

/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility
if (empty($product) || ! $product->is_visible()) {
	return;
}

/* code for attribute filter */
$query_string = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : '';
parse_str($query_string, $query_params);

$converted_params = [];
foreach ($query_params as $key => $value) {
	if (strpos($key, 'filter_') === 0) {
		$new_key = 'attribute_pa_' . substr($key, 7);
		$converted_params[$new_key] = $value;
	} else {
		$converted_params[$key] = $value;
	}
}
$new_query_string = http_build_query($converted_params);
$product_link = get_permalink($product->get_id());
if ($new_query_string) {
	$product_link .= '?' . $new_query_string;
}
/* code for attribute filter */

?>
<section <?php wc_product_class('product', $product); ?>>
	<div class="product-wrapper">
		<?php do_action('woocommerce_before_shop_loop_item');
		?>

		<div class="thumbnail-wrapper">
			<a href="<?php echo esc_url($product_link); ?>">

				<?php
				/**
				 * woocommerce_before_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_show_product_loop_sale_flash - 10
				 * @hooked woocommerce_template_loop_product_thumbnail - 10
				 */
				do_action('woocommerce_before_shop_loop_item_title');
				?>

			</a>
			<?php
			/**
			 * woocommerce_shop_loop_item_title hook.
			 *
			 * @hooked woocommerce_template_loop_product_title - 10
			 */
			do_action('woocommerce_shop_loop_item_title');

			/**
			 * woocommerce_after_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_template_loop_rating - 5
			 * @hooked woocommerce_template_loop_price - 10
			 */
			do_action('woocommerce_after_shop_loop_item_title');
			?>

		</div>
		<div class="meta-wrapper">
			<?php do_action('woocommerce_after_shop_loop_item'); ?>
		</div>
	</div>
</section>