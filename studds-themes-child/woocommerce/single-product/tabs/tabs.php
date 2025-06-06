<?php
/**
 * Single Product tabs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/tabs.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

global $boxshop_theme_options;
$is_accordion = shortcode_exists('vc_tta_accordion') && isset($boxshop_theme_options['ts_prod_accordion_tabs']) && $boxshop_theme_options['ts_prod_accordion_tabs'];

if ( ! empty( $product_tabs ) ) : ?>

	<?php if( !$is_accordion ): 
	
$cat = get_the_terms( $product->ID, 'product_cat' );

foreach ($cat as $categoria) {
if($categoria->parent == 0){
   $categoria;
}
}
	?>
	
	<div class="woocommerce-tabs wc-tabs-wrapper <?php echo $categoria->name;?>">
		<ul class="tabs wc-tabs" role="tablist">
			<?php foreach ( $product_tabs as $key => $product_tab ) : ?>

				<li class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
					<a href="#tab-<?php echo esc_attr( $key ); ?>">
						<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
					</a>
				</li>

			<?php endforeach; ?>
		</ul>
		<?php foreach ( $product_tabs as $key => $product_tab ) : ?>

			<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
				
				
				<?php 
				if ( isset( $product_tab['callback'] ) ) {
					call_user_func( $product_tab['callback'], $key, $product_tab );
				}
				?>
			</div>

		<?php endforeach; ?>
		
		<?php do_action( 'woocommerce_product_after_tabs' ); ?>
	</div>
	
	<?php else: ?>
	
	<div class="woocommerce-tabs accordion-tabs">
	
		<?php
		$active_tab = 1;
		if( isset($boxshop_theme_options['ts_prod_tabs_position']) && $boxshop_theme_options['ts_prod_tabs_position'] == 'inside_summary' ){
			$active_tab = 0;
		}
		
		$shortcode_content = '[vc_tta_accordion no_fill="true" collapsible_all="true" active_section="'.$active_tab.'"]';
		
		foreach ( $product_tabs as $key => $product_tab ) :
			$shortcode_content .= '[vc_tta_section tab_id="ts-acc-'.rand().'" title="'.apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ).'"]';
			ob_start();
			call_user_func( $product_tab['callback'], $key, $product_tab );
			$shortcode_content .= ob_get_clean();
			$shortcode_content .= '[/vc_tta_section]';
		endforeach;
		
		$shortcode_content .= '[/vc_tta_accordion]';
		echo do_shortcode($shortcode_content);
		?>
		
	</div>
	
	<?php endif; ?>

<?php endif; ?>
