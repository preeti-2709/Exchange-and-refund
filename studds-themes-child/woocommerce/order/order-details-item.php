<?php
/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}
?>
<?php 
//echo $order->get_id();
$cancel_datas = get_post_meta( $order->get_id(), 'partial_cancel_details',true);
$exchange_warrantry_extrajson = get_post_meta( $order->get_id(), 'ced_rnx_exchange_warrantry_extrajson',true);
// echo "<pre>";
// print_r($exchange_warrantry_extrajson);
$cancel_products = '';
if(!empty($cancel_datas)){
	$cancel_products = $cancel_datas['partial_cancel_product'];
} 
// $thumbnail = wp_get_attachment_image( $product->get_image_id(), 'thumbnail' );
if ( $product && is_a( $product, 'WC_Product' ) ) {
    $thumbnail = wp_get_attachment_image( $product->get_image_id(), 'thumbnail' );
} else {
    $thumbnail = ''; // or a placeholder image
}

for ($i=1; $i <= $item->get_quantity(); $i++) { ?>	
<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order ) ); ?>">
	<td class="thumbnail_views"> <?php 
		if ( isset( $thumbnail ) && ! empty( $thumbnail ) ) {
			echo '<div class="ced_rnx_prod_img_view">' . wp_kses_post( $thumbnail ) . '</div>';
		} else {
		?>
		<div class="ced_rnx_prod_img_view"><img alt="Placeholder" width="150" height="150" class="attachment-thumbnail size-thumbnail wp-post-image" src="<?php echo home_url(); ?>/wp-content/plugins/woocommerce/assets/images/placeholder.png"></div>
		<?php } ?>
	</td>
	<td class="woocommerce-table__product-name product-name">
		<?php
		$is_visible        = $product && $product->is_visible();
		$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item );

		echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s" style="font-weight:500;">%s</a>', $product_permalink, $item->get_name() ) : $item->get_name(), $item, $is_visible ) );

		$qty          = $item->get_quantity();
		$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

		if ( $refunded_qty ) {
			$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
		} else {
			// $qty_display = esc_html( $qty );
			$qty_display = esc_html( 1 );
		}
		

		echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity" style="color:black;">' . sprintf( '&times;&nbsp;%s', $qty_display ) . '</strong>', $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

		wc_display_item_meta( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
    if(!empty($exchange_warrantry_extrajson)){
        //print_r($exchange_warrantry_extrajson['from']);
		foreach($exchange_warrantry_extrajson['from'] as $key => $values){
		  //  echo '<p style="color:red">'.$values['material_code'].'</p>';
		  //  echo '<p style="color:red">'.$product->get_sku().'</p>';
		  //echo '<p style="color:red">'.$exchange_warrantry_extrajson['status'].'</p>';
		  //echo '<p style="color:red">'.$values['approve_status_key'].'</p>';
		   
			if($values['material_code'] == $product->get_sku()){
				if($exchange_warrantry_extrajson['status'] == 'warranty-accepted'){
					if($values['approve_status_key'] == '1'){
						echo '<br/><span style="color:#ff6161;line-height: 14px;">Warranty reject same product has been return.</span>';
					    //echo '<p style="color:red">'.$values['approve_status'].'</p>';
					    //echo '<p style="color:red">'.'Material Code'.$values['material_code'].' - Main product'.$product->get_sku().'</p>';
					}elseif($values['approve_status_key'] == '2'){
						echo '<br/><span style="color:#ff6161;line-height: 14px;">Warranty approved replace product has been provided.</span>';
					    //echo '<p style="color:red">'.$values['approve_status'].'</p>';
					    //echo '<p style="color:red">'.'Material Code'.$values['material_code'].' - Main product'.$product->get_sku().'</p>';
					}
				}elseif($exchange_warrantry_extrajson['status'] == 'warranty-rejected'){
					echo '<br/><span style="color:#ff6161;line-height: 14px;">Warranty reject same product has been return.</span>';
				    //echo '<p style="color:red">'.$values['approve_status'].'</p>';
				    //echo '<p style="color:red">'.'Material Code'.$values['material_code'].' - Main product'.$product->get_sku().'</p>';
				}	
				if($exchange_warrantry_extrajson['status'] == 'exchange-accepted'){
					if($values['approve_status_key'] == '1'){
						echo '<br/><span style="color:#ff6161;line-height: 14px;">Your exchange request for this product has been approved.</span>';
					    //echo '<p style="color:red">'.$values['approve_status'].'</p>';
					    //echo '<p style="color:red">'.'Material Code'.$values['material_code'].' - Main product'.$product->get_sku().'</p>';
					}elseif($values['approve_status_key'] == '2'){
						echo '<br/><span style="color:#ff6161;line-height: 14px;">Your exchange request for this product has been rejected.</span>';
					    //echo '<p style="color:red">'.$values['approve_status'].'</p>';
					    //echo '<p style="color:red">'.'Material Code'.$values['material_code'].' - Main product'.$product->get_sku().'</p>';
					}
				}elseif($exchange_warrantry_extrajson['status'] == 'exchange-rejected'){
					echo '<br/><span style="color:#ff6161;line-height: 14px;">Exchange reject same product has been return.</span>';
				}
				
			}
// 			elseif($values['material_code'] != $product->get_sku()){
// 			    echo '<span style="color:#ff6161;line-height: 14px;">Exchange reject same product has been return.</span>';
// 		    }
		}
    }
		?>
		
	</td>

	<td class="woocommerce-table__product-total product-total">
		<?php //echo wc_price( wc_get_price_to_display( $product )) ?>
		<?php if ( $product && is_a( $product, 'WC_Product' ) ) : ?>
			<?php echo wc_price( wc_get_price_to_display( $product ) ); ?>
		<?php else : ?>
			<span class="price-unavailable">Price not available</span>
		<?php endif; ?>

	</td>

</tr>
<?php
}
if(!empty($cancel_products)){
foreach($cancel_products as $cancel_items){ 
	if($cancel_items['item_id'] == $item_id){
?>

<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order ) ); ?>">
	<td class="thumbnail_views"> <?php 
		if ( isset( $thumbnail ) && ! empty( $thumbnail ) ) {
			echo '<div class="ced_rnx_prod_img_view">' . wp_kses_post( $thumbnail ) . '</div>';
		} else {
		?>
		<div class="ced_rnx_prod_img_view"><img alt="Placeholder" width="150" height="150" class="attachment-thumbnail size-thumbnail wp-post-image" src="<?php echo home_url(); ?>/wp-content/plugins/woocommerce/assets/images/placeholder.png"></div>
		<?php } ?>
	</td>
	<td class="woocommerce-table__product-name product-name">
		<?php
		$is_visible        = $product && $product->is_visible();
		$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

		echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s" style="font-weight:500;">%s</a>', $product_permalink, $item->get_name() ) : $item->get_name(), $item, $is_visible ) );

		$qty          = $item->get_quantity();
		$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

		if ( $refunded_qty ) {
			$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
		} else {
			// $qty_display = esc_html( $qty );
			$qty_display = esc_html( 1 );
		}

		echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', $qty_display ) . '</strong>', $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

		wc_display_item_meta( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
		?>
		<span style="color:#ff6161;"><strong>Cancelled on </strong><?php $date = date_create($cancel_items['date']);
		echo date_format($date,"d-M-Y");?></span>
	</td>
	<td class="woocommerce-table__product-total product-total"><del aria-hidden="true">
		<?php //echo wc_price( wc_get_price_to_display( $product ));?>
		<?php if ( $product && is_a( $product, 'WC_Product' ) ) : ?>
			<?php echo wc_price( wc_get_price_to_display( $product ) ); ?>
		<?php else : ?>
			<span class="price-unavailable">Price not available</span>
		<?php endif; ?>

	</del>
	</td>
</tr>
<?php	
} 
}
}
?>


<?php if ( $show_purchase_note && $purchase_note ) : ?>

<tr class="woocommerce-table__product-purchase-note product-purchase-note">

	<td colspan="2"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>

</tr>

<?php endif; ?>
