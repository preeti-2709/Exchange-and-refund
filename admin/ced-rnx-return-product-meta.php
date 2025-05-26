<?php
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Show Return Product detail on Order Page on admin Side

if ( ! is_int( $thepostid ) ) {
	$thepostid = $post->ID;
}
if ( ! is_object( $theorder ) ) {
	$theorder = wc_get_order( $thepostid );
}
$order = $theorder;
if ( WC()->version < '3.0.0' ) {
	$order_id = $order->id;
} else {
	$order_id = $order->get_id();
}
$return_datas = get_post_meta( $order_id, 'ced_rnx_return_product', true );
$line_items  = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
$save_ref_line_items = get_post_meta( $order_id, 'ced_rnx_save_ref_line_items', true );
if ( empty( $save_ref_line_items ) ) {
	update_post_meta( $order_id, 'mwb_rnx_new_refund_line_items', $line_items );
	update_post_meta( $order_id, 'ced_rnx_save_ref_line_items', 'saved' );
}
$line_items = get_post_meta( $order_id, 'mwb_rnx_new_refund_line_items', true );


if ( isset( $return_datas ) && ! empty( $return_datas ) ) {
	foreach ( $return_datas as $key => $return_data ) {
		$date = date_create( $key );
		$date_format = get_option( 'date_format' );
		$date = date_format( $date, $date_format );
		?>
		<p><?php _e( 'Following product warranty claim request made on', 'woocommerce-refund-and-exchange' ); ?> <b><?php echo $date; ?>.</b></p>
		<div>
		<div id="ced_rnx_return_wrapper">
			<table class="exchange-order-items">
				<thead>
					<tr>
						<th colspan="2"><?php _e( 'Product', 'woocommerce-refund-and-exchange' ); ?></th>
						<th><?php _e( 'Cost', 'woocommerce-refund-and-exchange' ); ?></th>
						<!-- <th><?php _e( 'Qty', 'woocommerce-refund-and-exchange' ); ?></th> -->
						<th><?php _e( 'Total', 'woocommerce-refund-and-exchange' ); ?></th>
						<th><?php _e( 'Request', 'woocommerce-refund-and-exchange' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				$total = 0;
				$return_products = $return_data['products'];
				$order_status = 'wc-' . $order->get_status();
				// echo "<pre>";
				// print_r($return_products); 
				foreach ( $line_items as $item_id => $item ) {
					foreach ( $return_products as $refundkey => $return_product ) {
						if ( $item_id == $return_product['item_id'] ) {
							if ( isset( $item['variation_id'] ) && $item['variation_id'] > 0 ) {
								$variation_id = $item['variation_id'];
								$product_id = $item['product_id'];
							} else {
								$product_id = $item['product_id'];
							}
							$refund_product_detail = $order->get_meta_data();
							foreach ( $refund_product_detail as $rpd_value ) {
								$refund_product_data = $rpd_value->get_data();
								if ( $refund_product_data['key'] == 'ced_rnx_return_product' ) {
									$refund_product_values = $refund_product_data['value'];
									foreach ( $refund_product_values as $rpv_value ) {
										$refund_product_values1 = $rpv_value['products'];
										foreach ( $refund_product_values1 as $rpv1_value ) {
											$refund_product_id = $rpv1_value['product_id'];
											$get_return_product = wc_get_product( $refund_product_id );
											$new_refund_image = wp_get_attachment_image_src( get_post_thumbnail_id( $refund_product_id ), 'single-post-thumbnail' );
											$refund_product_new[] = array(
												'name'  => $get_return_product->get_name(),
												'sku'   => $get_return_product->get_sku(),
												'image' => $new_refund_image[0],
											);
										}
									}
								}
							}
							$_product  = $item->get_product();
							$item_meta = wc_get_order_item_meta( $item_id, $key );
							$thumbnail     = $_product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $_product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
							?>
							<tr class="ced_rnx_warranty_column" data-productid="<?php echo $product_id; ?>" data-variationid="<?php echo $item['variation_id']; ?>" data-itemid="<?php echo $item_id; ?>" data-rowkey="<?php echo $refundkey; ?>" data-sku="<?php echo $_product->get_sku();?>">

							<td class="thumb">
								<?php
								if ( isset( $refund_product_new[ $refundkey ]['image'] ) && ! empty( $refund_product_new[ $refundkey ]['image'] ) ) {
									echo '<div class="wc-order-item-thumbnail"><img src ="' . $refund_product_new[ $refundkey ]['image'] . '"></div>';
								}
								?>
								</td>
								<td class="product-name">
								<?php
								
									// echo esc_html( $item['name'] );
								if ( isset( $refund_product_new[ $refundkey ]['name'] ) && ! empty( $refund_product_new[ $refundkey ]['name'] ) ) {
									echo esc_html( $refund_product_new[ $refundkey ]['name'] );
								}
								if ( isset( $refund_product_new[ $refundkey ]['sku'] ) && ! empty( $refund_product_new) ) {
									echo '<div class="wc-order-item-sku"><strong>' . __( 'SKU:', 'woocommerce-refund-and-exchange' ) . '</strong> ' . esc_html($_product->get_sku() ) . '</div>';
								}
								if ( ! empty( $item['variation_id'] ) ) {
									echo '<div class="wc-order-item-variation"><strong>' . __( 'Variation ID:', 'woocommerce-refund-and-exchange' ) . '</strong> ';
									if ( ! empty( $item['variation_id'] ) && 'product_variation' === get_post_type( $item['variation_id'] ) ) {
										echo esc_html( $item['variation_id'] );
									} elseif ( ! empty( $item['variation_id'] ) ) {
										echo esc_html( $item['variation_id'] ) . ' (' . __( 'No longer exists', 'woocommerce-refund-and-exchange' ) . ')';
									}
									echo '</div>';
								}
								$variation_product1 = wc_get_product( $item['variation_id'] );
								if(!empty($variation_product1)){
									$variation_attributes1 = $variation_product1->get_variation_attributes();
								}
								
								if ( WC()->version < '3.1.0' ) {
									$item_meta      = new WC_Order_Item_Meta( $item, $_product );
									// $item_meta->display();
								} else {
									$item_meta      = new WC_Order_Item_Product( $item, $_product );
									// wc_display_item_meta( $item_meta );
								}
								if ( isset( $variation_attributes1 ) && ! empty( $variation_attributes1 ) ) {
									echo wc_get_formatted_variation( $variation_attributes1 );
								}
								?>
								</td>
								<td><?php echo ced_rnx_format_price( $return_product['price'] ); ?></td>
								<!-- <td><?php echo $return_product['qty']; ?></td> -->
								<td><?php echo ced_rnx_format_price( $return_product['price'] * $return_product['qty'] ); ?></td>

								<td style="text-align: center;">
									<?php if(!isset($return_product['approve_status_key']) || ($order_status == 'wc-warranty-received' && $return_product['approve_status_key'] == '1')){
											?>
										<select name="approve_status" id="approve_status">
										  <option value="1" <?php if(isset($return_product['approve_status_key']) && $return_product['approve_status_key'] == '1'){echo "selected";}?> >Accept</option>
										  <option value="2" <?php if(isset($return_product['approve_status_key']) && $return_product['approve_status_key'] == '2'){echo "selected";}?>>Reject</option>
										</select>
									<?php } else if($return_product['approve_status_key'] == '1'){?>
										<p>Accepted</p>
									<?php } else if($return_product['approve_status_key'] == '2'){?>
										<p style="color:red;">Rejected</p>
									<?php }?>
									</td>
							</tr>
							<tr style="text-align: left;">
								<th colspan="2"><strong><?php _e( 'Subject', 'woocommerce-refund-and-exchange' ); ?> : </strong><i> <?php echo $return_product['subject']; ?></i></th>
								<th colspan="5"><strong><?php _e( 'Reason', 'woocommerce-refund-and-exchange' ); ?> : </strong><i><?php echo $return_product['reason']; ?></i></th>
							</tr>
							<tr style="text-align: left;">
								<th colspan="2">
								</th>
								<th colspan="3" style="padding: 10px;"><p>
								<b><?php _e( 'Attachment_', 'woocommerce-refund-and-exchange' ); echo esc_html( $_product->get_sku() ) ?> :</b>
								<?php 
								$count = 1;
								foreach ( $return_product['files'] as $attachment ) {
										if ( $attachment != $order_id . '-' ) {
											?>
											<a href="<?php echo home_url(); ?>/wp-content/uploads/Exchange_Images/<?php echo $attachment; ?>" target="_blank"><?php _e( 'Attachment', 'woocommerce-refund-and-exchange' ); ?>-<?php echo $count; ?></a>
											<?php
											$count++;
										}
									}
									?>
							</p></th>
							</tr>
							
							<?php
							$total += $return_product['price'] * $return_product['qty'];
						}
					}
				}
				?>
					<tr>
						<th colspan="2"><?php _e( 'Total Amount', 'woocommerce-refund-and-exchange' ); ?></th>
						<th colspan="3"><?php echo ced_rnx_format_price( $total ); ?></th>
					</tr>
				</tbody>
			</table>	
		</div>
		<div class="ced_rnx_extra_reason ced_rnx_extra_reason_for_refund">
		<?php

		$fee_enable = get_option( 'ced_rnx_return_shipcost_enable', false );
		if ( $fee_enable == 'yes' ) {
			?>
			<p><?php _e( 'Fees amount will be deducted from Refund amount', 'woocommerce-refund-and-exchange' ); ?></p>
			<?php
			$disable = '';
			if ( $return_data['status'] != 'pending' ) {
				$disable = 'readonly';
			} else {
				?>
			<div id="ced_rnx_add_fee">
				<?php
			}
			$added_fees = get_post_meta( $order_id, 'ced_rnx_return_added_fee', true );

			if ( isset( $added_fees ) && ! empty( $added_fees ) ) {
				if ( is_array( $added_fees ) ) {
					foreach ( $added_fees as $da => $added_fee ) {
						if ( $da == $key ) {
							if ( is_array( $added_fee ) ) {
								foreach ( $added_fee as $fee ) {
									$return_data['amount'] -= $fee['val'];
									if ( $return_data['status'] == 'pending' ) {
										?>
									<div class="ced_rnx_add_fee">

										<?php
									}
									?>

										<input type="text" placeholder="<?php _e( 'Fee Name', 'woocommerce-refund-and-exchange' ); ?>" <?php echo $disable; ?> value="<?php echo $fee['text']; ?>" name="ced_return_fee_txt[]" class="ced_return_fee_txt">
										<input type="text" name="" placeholder="0" <?php echo $disable; ?> value="<?php echo $fee['val']; ?>" class="ced_return_fee_value wc_input_price">
										<?php
										if ( $return_data['status'] == 'pending' ) {
											?>
										<input type="button" value="<?php _e( 'Remove', 'woocommerce-refund-and-exchange' ); ?>" class="button ced_rnx_remove-return-product-fee">
											<?php
										}
										if ( $return_data['status'] == 'pending' ) {
											?>
									</div>
											<?php
										}
								}
							}
							break;
						}
					}
				}
			}
			if ( $return_data['status'] == 'pending' ) {
				?>
				</div>
				<button class="button ced_rnx_add-return-product-fee" type="button"><?php _e( 'Add Fee', 'woocommerce-refund-and-exchange' ); ?></button>
				<button class="button button-primary ced_rnx_save-return-product-fee" type="button" data-orderid="<?php echo $order_id; ?>" data-date="<?php echo $key; ?>"><?php _e( 'Save', 'woocommerce-refund-and-exchange' ); ?></button>
				<?php
			}
		}

		if ( $return_data['status'] == 'pending' ) {
			?>
			<input type="hidden" value="<?php echo $return_data['amount']; ?>" id="ced_rnx_refund_amount">
			<input type="hidden" value="<?php echo $return_data['subject']; ?>" id="ced_rnx_refund_reason">
			<?php
		}
		?>
		<!-- <p><strong>
		
		<?php _e( 'Refund Amount', 'woocommerce-refund-and-exchange' ); ?> :</strong> 
		<?php echo ced_rnx_format_price( $return_data['amount'] ); ?> 
		<input type="hidden" name="ced_rnx_total_amount_for_refund" class="ced_rnx_total_amount_for_refund" value="<?php echo $return_data['amount']; ?>">
		</p> -->
		<div class="ced_rnx_reason">	
			<?php
			$order_status = 'wc-' . $order->get_status();
			if ( $return_data['status'] == 'pending' && $order_status == 'wc-warranty-request') {
				update_post_meta( $order_id, 'refundable_amount', $return_data['amount'] );
				?>
					<p id="ced_rnx_return_package">
					<input type="button" value="<?php _e( 'Accept Request', 'woocommerce-refund-and-exchange' ); ?>" class="button" id="ced_rnx_accept_warranty" data-orderid="<?php echo $order_id; ?>" data-date="<?php echo $key; ?>">

					<input type="button" value="<?php _e( 'Cancel Request', 'woocommerce-refund-and-exchange' ); ?>" class="button" id="ced_rnx_cancel_return" data-orderid="<?php echo $order_id; ?>" data-date="<?php echo $key; ?>">
					</p>
				<?php
			}else if($order_status == 'wc-warranty-received'){ ?>
					<p id="ced_rnx_return_package">

					<input type="button" value="<?php _e( 'Warranty Approve', 'woocommerce-refund-and-exchange' ); ?>" class="button" id="ced_rnx_accept_warranty_repair" data-orderid="<?php echo $order_id; ?>" data-date="<?php echo $key; ?>">

					<!-- <input type="button" value="<?php _e( 'Warranty Approve For Exchange', 'woocommerce-refund-and-exchange' ); ?>" class="button" id="ced_rnx_accept_warranty_repair" data-orderid="<?php echo $order_id; ?>" data-date="<?php echo $key; ?>"> -->

					<input type="button" value="<?php _e( 'Cancel Request', 'woocommerce-refund-and-exchange' ); ?>" class="button" id="ced_rnx_warranty_rejected" data-orderid="<?php echo $order_id; ?>" data-date="<?php echo $key; ?>">
					</p>
			<?php 
			}
			?>
		</div>
		<div class="ced_rnx_return_loader">
			<img src="<?php echo home_url(); ?>/wp-admin/images/spinner-2x.gif">
		</div>
		</div>	
		</div>
		<p>
		<?php

		if ( $return_data['status'] == 'complete' || $return_data['status'] == 'wc-warranty-approved') {
			?>
			<input type="hidden" value="<?php echo ced_rnx_currency_seprator( $return_data['amount'] ); ?>" id="ced_rnx_refund_amount">
			<input type="hidden" value="<?php echo $return_data['subject']; ?>" id="ced_rnx_refund_reason">
			<?php
			
			$approve_date = date_create( $return_data['approve_date'] );
			$date_format = get_option( 'date_format' );

			$approve_date = date_format( $approve_date, $date_format );
			$ced_refunded = get_post_meta( $order_id, 'ced_rnx_refund_approve_refunded', true );

			_e( 'Following product warranty request is approved on', 'woocommerce-refund-and-exchange' );
			?>
			<b><?php echo $approve_date; ?>.</b>
			<?php
			
			$ced_rnx_manage_stock_for_return = get_post_meta( $order_id, 'ced_rnx_manage_stock_for_return', true );
			if ( $ced_rnx_manage_stock_for_return == '' ) {
				$ced_rnx_manage_stock_for_return = 'yes';
			}
			$manage_stock = get_option( 'ced_rnx_return_request_manage_stock' );
			if ( $manage_stock == 'yes' && $ced_rnx_manage_stock_for_return == 'yes' ) {
				?>
				<div id="ced_rnx_stock_button_wrapper"><?php _e( 'When Product Back in stock then for stock management click on ', 'woocommerce-refund-and-exchange' ); ?> <input type="button" name="ced_rnx_stock_back" class="button button-primary" id="ced_rnx_stock_back" data-type="ced_rnx_return" data-orderid="<?php echo $order_id; ?>" Value="Manage Stock" ></div> 
				<?php
			}
		}
		if ( $return_data['status'] == 'cancel' ) {
			$approve_date = date_create( $return_data['cancel_date'] );
			$approve_date = date_format( $approve_date, 'F d, Y' );

			_e( 'Following product warranty request is cancelled on ', 'woocommerce-refund-and-exchange' );
			?>
			<b><?php echo $approve_date; ?>.</b>
			<?php
		}
		?>
		</p>
		<hr/>
		<?php
	}
} else {
	$ced_rnx_pages = get_option( 'ced_rnx_pages' );
	$page_id = $ced_rnx_pages['pages']['ced_return_from'];
	$return_url = get_permalink( $page_id );
	$order_id = $order->get_id();
	$ced_rnx_return_url = add_query_arg( 'order_id', $order_id, $return_url );
	?>
<p><?php _e( 'No request from customer', 'woocommerce-refund-and-exchange' ); ?></p>
<a target="_blank" href="<?php echo $ced_rnx_return_url; ?>" class="button-primary button"><b><?php _e( 'Initiate Warranty Request', 'woocommerce-refund-and-exchange' ); ?></b></a>
	<?php
}
?>
