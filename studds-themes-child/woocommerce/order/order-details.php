<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.6.0
 */

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( ! $order ) {
	return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}
?>
<section class="woocommerce-order-details">
	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

	<h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'woocommerce' ); ?></h2>

	<table class="woocommerce-table woocommerce-table--order-details shop_table order_details shippingflat">

		<thead>
			<tr>
				<th class="woocommerce-table__product-table"></th>
				<th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php
			do_action( 'woocommerce_order_details_before_order_table_items', $order );

			foreach ( $order_items as $item_id => $item ) {
				$product = $item->get_product();

				wc_get_template(
					'order/order-details-item.php',
					array(
						'order'              => $order,
						'item_id'            => $item_id,
						'item'               => $item,
						'show_purchase_note' => $show_purchase_note,
						'purchase_note'      => $product ? $product->get_purchase_note() : '',
						'product'            => $product,
					)
				);
			}

			do_action( 'woocommerce_order_details_after_order_table_items', $order );
			?>
		</tbody>

		<tfoot>
		    <?php 
		    $order->get_id();
		    $cancel_datas = get_post_meta( $order->get_id(), 'partial_cancel_details',true); 
		  //  echo "<pre>";
		  //  print_r($cancel_datas);
		    
    
		    ?>
		    <?php 
			     
			    
			    
			   
			    foreach ( $order->get_items() as $item_id => $item ) {
                  
                   $quantity = $item->get_quantity();
                   
                }
                $cancel_products = '';
                if(!empty($cancel_datas)){
                	$cancel_products = $cancel_datas['partial_cancel_product'];
                }
                if(!empty($cancel_products)){
                $totalquantity = $cancel_products[0]['prev_qty']; // Total Qty
                
                $total_shipping_amount = $order->get_shipping_total(); // Total Shipping Price
                
                $totalshippingqamount =  $total_shipping_amount / $totalquantity; 
                
                $final_shipping = $totalshippingqamount * $quantity;
                }
			    //echo $order->get_item_total();
			 //   foreach ( $order->get_items() as $item_id => $item ) {
    //               foreach ( $item_ids as $item_detail ) {
				// 	if ( $item_id == $item_detail[0] ) {
				// 		$product_id = $item['product_id'];
				// 		$product_variation_id = $item['variation_id'];
				// 		if ( $product_variation_id > 0 ) {
				// 			$product = wc_get_product( $product_variation_id );
				// 		} else {
				// 			$product = wc_get_product( $product_id );
				// 		}
				// 		$refund_amount_total = $refund_amount_total + wc_get_price_to_display( $product );
				// 		$refundtotal_item_qty = count($item_ids);
				// 	}
				// }
    //             }
			 //   $shipping_amount = $order->get_shipping_total();
    // 			$email = $order->get_billing_email();
    // 			$phone = $order->get_billing_phone();
    // 			$refund_cancel['total_item_qty1'] =  $total_qty_order;
    // 			$refund_cancel['item_qty1'] = $refundtotal_item_qty;
    // 			$refund_cancel['amount'] = $total_amount;
    // 			$refund_cancel['refund_shippingamount'] = $shipping_amount;
    // 			echo $refund_cancel['refund_amount'] = $refund_amount_total;
    // 			$refund_cancel['phone'] = $phone;
    // 			$refund_cancel['email'] = $email;
    // 			$refund_cancel['customer_id'] = $customer_id;
			    
			if($final_shipping != ''){ ?>
		    <tr>
				<th></th>
			    <th>Subtotal:</th>
				<td><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">₹</span><?php echo $order->get_subtotal(); ?></span></td>
			</tr>
			<tr>
				<th></th>
			    <th>Shipping:</th>
				<td><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">₹</span><?php echo $final_shipping ?></span></td>
			</tr>
			<tr>
				<th></th>
			    <th>Payment method:</th>
				<td style="text-transform: uppercase!important;"><?php echo $order->get_payment_method(); ?></td>
			</tr>
			<?php if($order->get_transaction_id() != '' ){ ?>
			<tr>
				<th></th>
			    <th>Transection Id:</th>
				<td><?php echo $order->get_transaction_id(); ?></td>
			</tr>
		    <?php } ?>
			<tr>
				<th></th>
			    <th>Total:</th>
				<td><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">₹</span><?php echo $order->get_subtotal()+$final_shipping//$order->get_total(); ?></span>&nbsp;</td>
			</tr>
			<?php 
			    } else {
			        foreach ( $order->get_order_item_totals() as $key => $total ) {         
			        //print_r($order->get_order_item_totals());
			?>
					<tr>
						<th></th>
						<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
						<?php 
						    if($order->get_status() == 'cancelled' ){ 
							    $cancel_datas = get_post_meta( $order->get_id(), 'partial_cancel_details',true);
						            if(!empty($cancel_datas)){
				                        $cancel_products = $cancel_datas['partial_cancel_product'];
				                        $total_amount = '';
				                            foreach($cancel_products as $cancel_items){ 
				                                $total_amount = $cancel_items['price'] + $total_amount;
				                            }
				                        $total_amount = wc_price($total_amount);
				                    
				                if('cart_subtotal' === $key || 'order_total' === $key){
				        ?>
							        <td><?php echo wp_kses_post( $total_amount );?></td>
						<?php 
							    }else{ 
						?>
                                    <td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] );?></td>
						<?php   } 
						                
						            } else{ ?>
						               <td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] );?></td>
						         <?php   }
						
						
						
						?>

						<?php }else{ ?>
							<td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
						<?php } ?>
					</tr>
					<?php
			        }   
			    }
			?>
			<?php if ( $order->get_customer_note() ) : ?>
				<tr>
					<th><?php //esc_html_e( 'Note:', 'woocommerce' ); ?></th>
					<td><?php //echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
			<?php endif; ?>
		</tfoot>
	</table>

	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
</section>

<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action( 'woocommerce_after_order_details', $order );

if ( $show_customer_details ) {
	wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
}
