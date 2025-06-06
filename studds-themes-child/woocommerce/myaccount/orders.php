<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<?php if ( $has_orders ) : ?>

	<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
		<thead>
			<tr>
				<?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
				<?php endforeach; ?>
			</tr>
		</thead>

		<tbody>
			<?php

			foreach ( $customer_orders->orders as $customer_order ) {
				$order      = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				// echo "<pre>";
				// print_r($order); die;
				$item_count = $order->get_item_count() - $order->get_item_count_refunded();

				$statusproducts = esc_html( wc_get_order_status_name( $order->get_status() ) );

				$cancel_start=1;
				$processing_start=1;
				$cancel_item = 0;
				$processing_item = 0;
				$cancel_html = '';
				$exchange_item = 0;
				$exchange_html = '';
				if($order->get_status() == 'partial-cancel' || $order->get_status() == 'cancelled'){

					$cancel_datas = get_post_meta( $order->get_order_number(), 'partial_cancel_details',true);
					$cancel_item_new = '';
					if(!empty($cancel_datas) && $order->get_status() == 'partial-cancel'){
						foreach ( $cancel_datas['partial_cancel_product'] as  $item_id_cancel => $item_data_cancel ){
								$new_one = $item_data_cancel['prev_qty'] - $item_data_cancel['qty'];
								$cancel_item_new = (int)$new_one+(int)$cancel_item_new;
								//echo "<br>";
						}
						foreach ( $order->get_items() as  $item_id => $item_data ) {
							  $item_data = $item_data->get_data();
					          $quantity = $item_data['quantity'];
					          if($quantity == '0'){
					          	$cancel_item = (int)$cancel_start+(int)$cancel_item;
					          }else{
					          	$processing_item = (int)$quantity+(int)$processing_item;
					          }
						}
						$count_cancel = count($cancel_datas['partial_cancel_product']);
						if($count_cancel>1){
							$text_items = ' items are';
						}else{
							$text_items = ' item is';
						}
						if($processing_item>1){
							$text_items_1 = ' items are';
						}else{
							$text_items_1 = ' item is';
						}
						$cancel_html = "<strong>".$count_cancel."<strong>".$text_items." cancelled & <br> ".$processing_item. $text_items_1 ." under processing ";

					    //echo $order->get_subtotal();
					}
					if($order->get_status() == 'cancelled'){
						$total_amount = '';

						if (isset($cancel_datas['partial_cancel_product']) &&
							is_array($cancel_datas['partial_cancel_product']))
							{
								foreach($cancel_datas['partial_cancel_product'] as $cancel_items){
									$total_amount = (int)$cancel_items['price'] + (int)$total_amount;
								}
								$item_count = count($cancel_datas['partial_cancel_product']);
							}
					}
				}
				$exchange_html = '';
				$exchange_item = '';
				if($order->get_status() == 'exchange-approved'){
					$exchange_order = get_post_meta( $order->get_order_number(), 'ced_rnx_exchange_product', true );
				// 	echo "<pre>";
				// 	print_r($exchange_order);
					if ( isset( $exchange_order ) && ! empty( $exchange_order ) ) {
						foreach ( $exchange_order as $key => $return_data ) {
						  //  echo "<pre>";
						  //  print_r($return_data[to]);
							$count_exchange = count($return_data['from']);
						}

					//Exchange Approved Product Count
						$count1=0;
						foreach ( $return_data[to] as $keyto1 => $return_datato1 ) {
						  //echo "<pre>";
						  //  print_r($return_datato);
						  if($return_datato1[approve_status_key] == 1){
                                $count1++;
                            }
						}
						//echo '2 comes '.$count1.' time.';
					//Exchange Rejected Product Count
						$count3=0;
						foreach ( $return_data[to] as $keyto3 => $return_datato3 ) {
						  //echo "<pre>";
						  //  print_r($return_datato);
						  if($return_datato3[approve_status_key] == 2){
                                $count3++;
                            }
						}
						//echo '1 comes '.$count2.' time.';


						foreach ( $order->get_items() as  $item_id => $item_data ) {
							  $item_data = $item_data->get_data();
					          $quantity_exchange = $item_data['quantity'];
					          if($quantity_exchange != '0'){
					          	$exchange_item = (int)$quantity_exchange+(int)$exchange_item;
					          }
						}
						$final_complete = $exchange_item - $count_exchange;
					}
					//Condition for Approved Count
					if($count1>1){
						$text_items_0 = ' items is';
					}else{
						$text_items_0 = ' item is';
					}
					//Condition for Rejected Count
					if($count3>2){
						$text_items_3 = ' items is';
					}else{
						$text_items_3 = ' item is';
					}
					//Condition for Final Count
					if($final_complete>1){
						$text_items_2 = ' items is';
					}else{
						$text_items_2 = ' item is';
					}
					if($final_complete){
						//$exchange_html .= "<strong>".$final_complete .$text_items_2." Complete & </strong><br>";
					}
					//Display of Approved Count
					if($count1 > 0){
					$exchange_html .= "<p style='line-height: 16px;margin-bottom: 0px;'>Your exchange request for <b><span style='color:green;'>" .$count1. $text_items_0 ." approved</span></b> </p>";
					}
					//Display of Rejected Count
					if($count3 > 0){
					  $exchange_html .= "<p style='line-height: 16px;margin-bottom: 0px;'>and <b><span style='color:red;'>" .$count3. $text_items_3 ." rejected </span></b></p>";
					}
				// 	if($final_complete){
				// 		$exchange_html .= "<strong>".$final_complete .$text_items_2." Complete & </strong><br>";
				// 	}
				// 	$exchange_html .= "<strong>" .$count_exchange. $text_items_0 ." Under Exchange </strong>";
				}
				?>
				<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $order->get_status() ); ?> order">
					<?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
							<?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
								<?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>

							<?php elseif ( 'order-number' === $column_id ) : ?>
								<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
									<?php echo esc_html( _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number() ); ?>
								</a>
                                <?php //foreach ( $order->get_items() as $item_id => $item ) {

                                  //echo $product_name = $item->get_name();
                                  //echo "<br>";

                              // } ?>
							<?php elseif ( 'order-date' === $column_id ) : ?>
								<time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>

							<?php elseif ( 'order-status' === $column_id ) : ?>
								<span class="<?php echo $statusproducts; ?>"><?php echo $statusproducts; ?></span>
								<br/>
								<?php if($cancel_html != ''){?>
									<span class="cancel_html" style="font-size: 11px;"><?php echo $cancel_html; ?></span>
								<?php }
								if($exchange_html != ''){?>
									<span class="cancel_html" style="font-size: 11px;"><?php echo $exchange_html; ?></span>
								<?php }?>

							<?php elseif ( 'order-total' === $column_id ) : ?>
								<?php
								if($order->get_status() == 'partial-cancel' || $order->get_status() == 'cancelled'){
                                    $cancel_datas = get_post_meta( $order->get_order_number(), 'partial_cancel_details',true);
                					$cancel_item_new = '';
                					if(!empty($cancel_datas) && $order->get_status() == 'partial-cancel'){
                					//echo $count_cancel;
    								//echo "<br>";
    								//echo $processing_item;
    								//echo "<br>";
    								$totalproductcount = (int) $count_cancel + (int) $processing_item;

    							    //echo "<br>";
    							    $totalshipping_amount = $order->get_shipping_total(); // Total Shipping Price

                                    $totalshippingwamount =  $totalshipping_amount / $totalproductcount;
                                    $final_shippingtotal = $totalshippingwamount * $processing_item;
                                    $finalamountd = $order->get_subtotal() + $final_shippingtotal;
                					}
                				// 	if($order->get_status() == 'cancelled'){
                				// 		$total_amount = '';
                				// 		foreach($cancel_datas['partial_cancel_product'] as $cancel_items){
                		  //                  $total_amount = (int)$cancel_items['price'] + (int)$total_amount;
                		  //              }
                				// 		$item_count = count($cancel_datas['partial_cancel_product']);
                				// 	}
                				}




								/* translators: 1: formatted order total 2: total order items */

								if($order->get_status() == 'partial-cancel'){
								    echo '<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">₹</span>'.$finalamountd.'</span> for '.$processing_item.' Items';
								}

								elseif($cancel_datas != '' && $order->get_status() == 'cancelled'){
									echo wp_kses_post( sprintf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ), wc_price($total_amount), $item_count ) );
								}
								elseif($cancel_datas == '' && $order->get_status() == 'cancelled'){
								   echo wp_kses_post( sprintf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ) );
								}
								// else($order->get_status() != 'partial-cancel' && $order->get_status() != 'cancelled'){
								else{
									echo wp_kses_post( sprintf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ) );
								}
								?>

							<?php elseif ( 'order-actions' === $column_id ) : ?>
								<?php
								$actions = wc_get_account_orders_actions( $order );

								if ( ! empty( $actions ) ) {
									foreach ( $actions as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
									//print_r($key);
										echo '<a href="' . esc_url( $action['url'] ) . '" class="woocommerce-button button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
									}
								}
								?>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>

	<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
			<?php endif; ?>

			<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>
	<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
		<a class="woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"><?php esc_html_e( 'Browse products', 'woocommerce' ); ?></a>
		<?php esc_html_e( 'No order has been made yet.', 'woocommerce' ); ?>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
