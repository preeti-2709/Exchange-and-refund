<?php
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $woocommerce;
$allowed = true;

// Product Exchange request form

$current_user_id = get_current_user_id();   // check user is logged in or not

if ( $allowed ) {
	$subject = '';
	$reason = '';
	if ( isset( $_POST['order_id'] ) ) {
		$order_id = $_POST['order_id'];

	} elseif ( isset( $_GET['order_id'] ) ) {
		$order_id = $_GET['order_id'];
	} else {

		$url = strtok( $_SERVER['REQUEST_URI'], '?' );
		$link_array = explode( '/', $url );
		if ( empty( $link_array[ count( $link_array ) - 1 ] ) ) {
			$order_id = $link_array[ count( $link_array ) - 2 ];
		} else {
			$order_id = $link_array[ count( $link_array ) - 1 ];
		}
	}

	// check order id is valid

	if ( ! is_numeric( $order_id ) ) {
		if ( get_current_user_id() > 0 ) {
			$myaccount_page = get_option( 'woocommerce_myaccount_page_id' );
			$myaccount_page_url = get_permalink( $myaccount_page );
		} else {
			$ced_rnx_pages = get_option( 'ced_rnx_pages' );
			$page_id = $ced_rnx_pages['pages']['ced_request_from'];
			$myaccount_page_url = get_permalink( $page_id );
		}
		$allowed = false;
		$reason = __( 'Please choose an Order.', 'woocommerce-refund-and-exchange' ) . '<a href="' . $myaccount_page_url . '">' . __( 'Click Here', 'woocommerce-refund-and-exchange' ) . '</a>';
		$reason = apply_filters( 'ced_rnx_exchange_choose_order', $reason );
	} else {
		$order_customer_id = get_post_meta( $order_id, '_customer_user', true );
		if ( $current_user_id > 0 ) {
			if ( ! ( current_user_can( 'administrator' ) ) ) {
				if ( $order_customer_id != $current_user_id ) {
					$myaccount_page = get_option( 'woocommerce_myaccount_page_id' );
					$myaccount_page_url = get_permalink( $myaccount_page );
					$allowed = false;
					$reason = __( "This order #$order_id is not associated to your account. <a href='$myaccount_page_url'>Click Here</a>", 'woocommerce-refund-and-exchange' );
					$reason = apply_filters( 'ced_rnx_exchange_choose_order', $reason );
				}
			}
		} else // check order associated to customer account or not for guest user
		{
			if ( null != WC()->session->get( 'ced_rnx_email' ) ) {
				$user_email = WC()->session->get( 'ced_rnx_email' );
				$order_email = get_post_meta( $order_id, '_billing_email', true );
				if ( ! ( current_user_can( 'administrator' ) ) ) {
					if ( $user_email != $order_email ) {
						$allowed = false;
						$ced_rnx_pages = get_option( 'ced_rnx_pages' );
						$page_id = $ced_rnx_pages['pages']['ced_request_from'];
						$myaccount_page_url = get_permalink( $page_id );
						$reason = __( "This order #$order_id is not associated to your account. <a href='$myaccount_page_url'>Click Here</a>", 'woocommerce-refund-and-exchange' );
						$reason = apply_filters( 'ced_rnx_exchange_choose_order', $reason );
					}
				}
			} else {
				$allowed = false;
			}
		}
	}

	if ( $allowed ) {
		$ced_rnx_next_return = true;
		$ced_rnx_enable = get_option( 'ced_rnx_return_exchange_enable', false );
		if ( $ced_rnx_enable == 'yes' ) {
			$ced_rnx_made = get_post_meta( $order_id, 'ced_rnx_request_made', true );
			if ( isset( $ced_rnx_made ) && ! empty( $ced_rnx_made ) ) {
				$ced_rnx_next_return = false;
			}
		}
		if ( $ced_rnx_next_return ) {
			$allowed = true;
		} else {
			$allowed = false;
		}

		if ( $allowed ) {
			$order = wc_get_order( $order_id );

			// Check enable exchange
			$exchange_enable = get_option( 'ced_rnx_exchange_enable', false );
			if ( isset( $exchange_enable ) && ! empty( $exchange_enable ) ) {
				if ( $exchange_enable == 'yes' ) {
					$allowed = true;
				} else {
					$allowed = false;
					$reason = __( 'Exchange request is disabled.', 'woocommerce-refund-and-exchange' );
					$reason = apply_filters( 'ced_rnx_exchange_order_amount', $reason );
				}
			} else {
				$allowed = false;
				$reason = __( 'Exchange request is disabled.', 'woocommerce-refund-and-exchange' );
				$reason = apply_filters( 'ced_rnx_exchange_order_amount', $reason );
			}



			if ( $allowed ) {
				if ( null != WC()->session->get( 'exchange_requset' ) ) {
					$exchange_details = WC()->session->get( 'exchange_requset' );
				} else {
					$exchange_details = get_post_meta( $order_id, 'ced_rnx_exchange_product', true );
				}
				// WC()->session->__unset( 'exchange_requset' );
				// echo "<pre>";
				// // die;
				// echo "<pre>";
				// print_r(WC()->session->get( 'exchange_requset' )); die;
				// WC()->session->set( 'exchange_requset', $exchange_details );

				// Get pending exchange request

				if ( isset( $exchange_details ) && ! empty( $exchange_details ) ) {
					foreach ( $exchange_details as $date => $exchange_detail ) {
						if ( $exchange_detail['status'] == 'pending' ) {
							if ( isset( $exchange_detail['subject'] ) ) {
								$subject = $exchange_details[ $date ]['subject'];
							}
							if ( isset( $exchange_detail['reason'] ) ) {
								$reason = $exchange_details[ $date ]['reason'];
							}
							if ( isset( $exchange_detail['from'] ) ) {
								$exchange_products = $exchange_detail['from'];
							} else {
								$exchange_products = array();
							}
							if ( isset( $exchange_detail['to'] ) ) {
								$exchange_to_products = $exchange_detail['to'];
							} else {
								$exchange_to_products = array();
							}
						}
					}
				}
				$order = new WC_Order( $order );
				$items = $order->get_items();
				$ced_rnx_catalog = get_option( 'catalog', array() );
				if ( is_array( $ced_rnx_catalog ) && ! empty( $ced_rnx_catalog ) ) {
					$ced_rnx_catalog_exchange = array();
					foreach ( $items as $item ) {
						$product_id = $item['product_id'];
						if ( is_array( $ced_rnx_catalog ) && ! empty( $ced_rnx_catalog ) ) {
							foreach ( $ced_rnx_catalog as $key => $value ) {
								if ( is_array( $value['products'] ) ) {
									if ( in_array( $product_id, $value['products'] ) ) {
										$ced_rnx_catalog_exchange[] = $value['exchange'];
									}
								}
							}
						}
					}
					if ( is_array( $ced_rnx_catalog_exchange ) && ! empty( $ced_rnx_catalog_exchange ) ) {
						$ced_rnx_catalog_exchange_days = max( $ced_rnx_catalog_exchange );
					}
				}
				if ( WC()->version < '3.0.0' ) {
					$order_date = date_i18n( 'F j, Y', strtotime( $order->order_date ) );
				} else {
				    // Change Order Date created to Order Completed date - So that Exchange form date count after the order completeion 
					$order_date = date_i18n( 'F j, Y', strtotime( $order->get_date_completed() ) );
				}
				$today_date = date_i18n( 'F j, Y' );
				$order_date = strtotime( $order_date );
				$today_date = strtotime( $today_date );
				$days = $today_date - $order_date;
				$day_diff = floor( $days / ( 60 * 60 * 24 ) );
				$day_allowed = get_option( 'ced_rnx_exchange_days', true ); // Check allowed days
				if ( isset( $ced_rnx_catalog_exchange_days ) && $ced_rnx_catalog_exchange_days != 0 ) {
					if ( $ced_rnx_catalog_exchange_days >= $day_diff ) {
						$allowed = true;
					} else {
						$allowed = false;
						$reason = __( 'Days exceed.', 'woocommerce-refund-and-exchange' );
						$reason = apply_filters( 'ced_rnx_exchange_day_exceed', $reason );
					}
				} else {
					if ( $day_allowed >= $day_diff && $day_allowed != 0 ) {
						$allowed = true;
					} else {
						$allowed = false;
						$reason = __( 'Days exceed.', 'woocommerce-refund-and-exchange' );
						$reason = apply_filters( 'ced_rnx_exchange_day_exceed', $reason );
					}
				}
				if ( $allowed ) {
					$order = wc_get_order( $order_id );
					$order_total = $order->get_total();
					$exchange_min_amount = get_option( 'ced_rnx_exchange_minimum_amount', false );

					// Check minimum amount

					if ( isset( $exchange_min_amount ) && ! empty( $exchange_min_amount ) ) {
						if ( $exchange_min_amount <= $order_total ) {
							$allowed = true;
						} else {
							$allowed = false;
							$reason = __( 'For Exchange request Order amount must be greater or equal to ', 'woocommerce-refund-and-exchange' ) . $exchange_min_amount . '.';
							$reason = apply_filters( 'ced_rnx_exchange_order_amount', $reason );
						}
					}
					if ( $allowed ) {
						$statuses = get_option( 'ced_rnx_exchange_order_status', array() );
						$order_status = 'wc-' . $order->get_status();
						if ( ! in_array( $order_status, $statuses ) ) {
							$allowed = false;
							$reason = __( 'Exchange request is disabled.', 'woocommerce-refund-and-exchange' );
							$reason = apply_filters( 'ced_rnx_return_order_amount', $reason );
						}
					}
				}
			}
		}
	}
}
$ced_rnx_show_sidebar_on_form = get_option( 'ced_rnx_show_sidebar_on_form', 'no' );
get_header( 'shop' );

/**
 * woocommerce_before_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 */
if ( $ced_rnx_show_sidebar_on_form == 'yes' ) {
	do_action( 'woocommerce_before_main_content' );
}
if ( $allowed ) {
	$total_price = 0;
	$total_price_exchange = 0;
	$total_price_exchange_tb = 0;
	$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
	$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();

	$ced_main_wrapper_class = get_option( 'ced_rnx_return_exchange_class' );
	$ced_child_wrapper_class = get_option( 'ced_rnx_return_exchange_child_class' );
	$ced_exchange_css = get_option( 'ced_rnx_exchange_custom_css' );
		
	$predefined_exchange_reason = get_option( 'ced_rnx_exchange_predefined_reason', false );
	
	?>
	 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet"/>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
	<style>	<?php echo $ced_exchange_css; ?>	</style>
		

	<div class="woocommerce woocommerce-account <?php echo $ced_main_wrapper_class; ?>">
		<div class="<?php echo $ced_child_wrapper_class; ?>" id="ced_rnx_exchange_request_form_wrapper">
			<div id="ced_rnx_exchange_request_container">
				<h2>
				<?php
					$exchange_product_form = __( 'Product Exchange Request Form', 'woocommerce-refund-and-exchange' );
					echo apply_filters( 'ced_rnx_exchange_product_form', $exchange_product_form );
				?>
				</h2>
				<!-- <p>
				<?php
					$select_product_text = __( 'Select Product to Exchange', 'woocommerce-refund-and-exchange' );
					echo apply_filters( 'ced_rnx_select_exchange_text', $select_product_text );
				?>
				</p> -->
			</div>
			<ul class="woocommerce-error" id="ced-exchange-alert">
			</ul>
			<input type="hidden" id="ced_rnx_exchange_request_order" value="<?php echo esc_html( $order_id ); ?>">
			<div class="ced_rnx_product_table_wrapper ced_rnx_product_table_exchange">
				<table class="shop_table order_details order_details_exchange ced_rnx_product_table">
					<thead>
						<tr>
							<th class="product-check">
								<input type="hidden" name="ced_rnx_exchange_product_all" class="ced_rnx_exchange_product_all"> 
								<?php _e( 'Select items', 'woocommerce-refund-and-exchange' ); ?></th>
							<th class="product-name"><?php _e( 'Product', 'woocommerce-refund-and-exchange' ); ?></th>
							<th class="product-name"><?php _e( 'Exchange', 'woocommerce-refund-and-exchange' ); ?></th>
							<th class="product-qty"><?php _e( 'Reason', 'woocommerce-refund-and-exchange' ); ?></th>
							<!-- <th class="product-total"><?php _e( 'Total', 'woocommerce-refund-and-exchange' ); ?></th> -->
						</tr>
					</thead>
					<tbody>
						<?php
							$ced_rnx_sale = get_option( 'ced_rnx_exchange_sale_enable', false );
							$ced_rnx_ex_cats = get_option( 'ced_rnx_exchange_ex_cats', array() );

							$sale_enable = false;
						if ( $ced_rnx_sale == 'yes' ) {
							$sale_enable = true;
						}

							$ced_rnx_in_tax = get_option( 'ced_rnx_exchange_tax_enable', false );
							$in_tax = false;
						if ( $ced_rnx_in_tax == 'yes' ) {
							$in_tax = true;
						}
						$i_qty_one = 1;
						$start_row = 0;
						foreach ( $order->get_items() as $item_id => $item ) {
							if ( $item['qty'] > 0 ) {
							for ($i_qty=1; $i_qty <= $item['qty']; $i_qty++) {
								if ( isset( $item['variation_id'] ) && $item['variation_id'] > 0 ) {
									$variation_id = $item['variation_id'];
									$product_id = $item['product_id'];
								} else {
									$product_id = $item['product_id'];
								}
								$ced_rnx_catalog_detail = get_option( 'catalog', array() );
								$day_allowed = get_option( 'ced_rnx_exchange_days', true );
								if ( isset( $ced_rnx_catalog_detail ) && ! empty( $ced_rnx_catalog_detail ) ) {
									$ced_rnx_catalog_exchange = array();
									foreach ( $ced_rnx_catalog_detail as $key => $value ) {
										if ( is_array( $ced_rnx_catalog_detail[ $key ]['products'] ) && ! empty( $ced_rnx_catalog_detail[ $key ]['products'] ) ) {
											if ( in_array( $product_id, $ced_rnx_catalog_detail[ $key ]['products'] ) ) {
												$ced_rnx_pro = $product_id;
												$ced_rnx_catalog_exchange[] = $ced_rnx_catalog_detail[ $key ]['exchange'];
												if ( WC()->version < '3.0.0' ) {
													$order_date = date_i18n( 'F j, Y', strtotime( $order->order_date ) );
												} else {

													$order_date = date_i18n( 'F j, Y', strtotime( $order->get_date_created() ) );
												}
												$today_date = date_i18n( 'F j, Y' );
												$order_date = strtotime( $order_date );
												$today_date = strtotime( $today_date );
												$days = $today_date - $order_date;
												$day_diff = floor( $days / ( 60 * 60 * 24 ) );
											}
										}
									}
									if ( is_array( $ced_rnx_catalog_exchange ) && ! empty( $ced_rnx_catalog_exchange ) ) {
										$ced_rnx_catalog_exchange_day = min( $ced_rnx_catalog_exchange );
									}
								}
								if ( isset( $product_id ) && isset( $ced_rnx_pro ) && $product_id == $ced_rnx_pro ) {
									if ( $ced_rnx_catalog_exchange_day >= $day_diff && $ced_rnx_catalog_exchange_day != 0 ) {
										$show = true;
									} else {
										$show = false;
									}
								} else {
									if ( $day_allowed >= $day_diff && $day_allowed != 0 ) {
										$show = true;
									} else {
										$show = false;
									}
								}
								$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );

								$thumbnail     = wp_get_attachment_image( $product->get_image_id(), 'thumbnail' );

								$pro_categories = get_the_terms( $product_id, 'product_cat' );
								$productdata = wc_get_product( $product_id );


								$ced_coupon_deduct = get_option( 'ced_rnx_exchange_deduct_coupon_amount_enable', false );

								$disable_product = get_post_meta( $product_id, 'ced_rnx_disable_exchange', true );
								if ( isset( $disable_product ) && ! empty( $disable_product ) ) {
									if ( $disable_product == 'open' ) {
										$show = false;
									}
								}

								if ( isset( $pro_categories ) && ! empty( $pro_categories ) ) {
									foreach ( $pro_categories as $k => $cat ) {
										$cat = (array) $cat;

										if ( in_array( $cat['term_id'], $ced_rnx_ex_cats ) ) {
											$show = false;
										}
									}
								}

								if ( $show ) {
									if ( $sale_enable ) {
										$show = true;
									} else {
										if ( $productdata->is_on_sale() ) {
											$show = false;
										}
									}
								}

								$ced_product_total = $order->get_line_subtotal( $item, $in_tax );
								$ced_product_qty = $item['qty'];
								if ( $ced_product_qty > 0 ) {
									$ced_per_product_price = $ced_product_total / $ced_product_qty;
								}
								$purchase_note = get_post_meta( $product_id, '_purchase_note', true );

								$checked = '';
								$qty = 1;
								if ( isset( $exchange_products ) && ! empty( $exchange_products ) ) {
									foreach ( $exchange_products as $key_exchange_1 => $exchange_product ) {
										if ( $item['product_id'] == $exchange_product['product_id'] && $item['variation_id'] == $exchange_product['variation_id']) {
											foreach ($exchange_to_products as $key_exchange => $value_exchange){
												if($key_exchange == $start_row){
														$checked = 'checked="checked"';
														$qty = $exchange_product['qty'];
														break;
												}
											}
										}
									}
								}


								?>
									<tr class="ced_rnx_exchange_column" data-productid="<?php echo $product_id; ?>" data-variationid="<?php echo $item['variation_id']; ?>" data-itemid="<?php echo $item_id; ?>" data-rowkey="<?php echo $start_row; ?>" data-sku="<?php echo $product->get_sku();?>">
										<td class="product-select" style="width:12%;">
									<?php
									if ( $show ) {
										if ( WC()->version < '3.7.0' ) {
											$mwb_ord_cpn = $order->get_used_coupons();
										} else {
											$mwb_ord_cpn = $order->get_coupon_codes();
										}
										$mwb_wlt_status = false;
										if ( ! empty( $mwb_ord_cpn ) ) {
											foreach ( $mwb_ord_cpn as $k_cpn => $v_cpn ) {
												$mwb_cpn_obj = new WC_Coupon( $v_cpn );
												$mwb_cpn_id = $mwb_cpn_obj->get_id();
												$mwb_wlt = get_post_meta( $mwb_cpn_id, 'rnxwallet', true );
												if ( $mwb_wlt ) {
													$mwb_wlt_status = true;
												}
											}
										}
										if ( $mwb_wlt_status ) {
											$mwb_actual_price = $ced_per_product_price;
										} else {
											if ( 'yes' == $ced_coupon_deduct ) {
												$mwb_actual_price = $order->get_item_total( $item, $in_tax );
											} else {
												$mwb_actual_price = $order->get_item_subtotal( $item, $in_tax );
											}
										}
										?>
										
												<input type="checkbox" <?php echo $checked; ?> class="ced_rnx_exchange_product" value="<?php echo $mwb_actual_price; ?>">
										        <label class="order-exchange-select">Select Item</label>
											<?php
									} else {
										?>
												<!-- <img src="<?php echo CED_REFUND_N_EXCHANGE_URL; ?>/assets/images/exchange-disable.png" width="20px"> -->
												<input type="checkbox" <?php echo $checked; ?> class="ced_rnx_exchange_product" value="<?php echo $mwb_actual_price; ?>">
                                                <label class="order-exchange-select">Select Item</label>
											<?php
											$mwb_actual_price = $order->get_item_total( $item, $in_tax );
									}
									?>
										</td>
										<td class="product-name product-name-exchange" style="width: 32%;">
									<?php echo sprintf( '<input type="hidden" max="%s" min="1" value="%s" class="ced_rnx_exchange_product_qty form-control" name="ced_rnx_exchange_product_qty">', $i_qty_one, $i_qty_one ); 

									$is_visible        = $product && $product->is_visible();
									$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

									echo '<div class="ced_rnx_prod_img">' . wp_kses_post( $thumbnail ) . '</div>';
									?>
											<div class="ced_rnx_product_title">
									<?php
									echo apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $item['name'] ) : $item['name'], $item, $is_visible );

									echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', $i_qty_one ) . '</strong>', $item );

									?>		<input type="hidden" class="product_permalink" value="<?php echo $product_permalink; ?>"> 
											<input type="hidden" class="quanty" value="<?php echo $item['qty']; ?>"> 
										<?php
								//Removed, as i don't want to display the EAN and Meta Tags on the product. 
								// 		do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );

								// 		if ( WC()->version < '3.0.0' ) {
								// 			$order->display_item_meta( $item );
								// 			$order->display_item_downloads( $item );
								// 		} else {
								// 			wc_display_item_meta( $item );
								// 			wc_display_item_downloads( $item );
								// 		}

								// 		do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );
										// echo 'SKU : ' . $product->get_sku();
										?>
										<p>
											<b><?php _e( 'Price', 'woocommerce-refund-and-exchange' ); ?> : </b>
													<?php
														echo ced_rnx_format_price( $mwb_actual_price );
														$total_price_exchange = $total_price_exchange + $mwb_actual_price;
													?>
											<?php
											if ( $in_tax == true ) {
												?>
													<small class="tax_label"><?php _e( '(incl. tax)', 'woocommerce-refund-and-exchange' ); ?></small>
												<?php
											}
											?>
										</p>
										</div>
										</td>
										<td class="product-name product-name-exchange product-exchange" style="width:32%;">
											<?php
											$exchange_to_product = '';
											foreach ($exchange_to_products as $key_exchange => $value_exchange){
												if($value_exchange['product_id'] == $product_id && $key_exchange == $start_row){
													$exchange_to_product = $value_exchange;
													$total_price_exchange_tb = $total_price_exchange_tb + ($value_exchange['price'] - $mwb_actual_price);
												}else if($key_exchange == $start_row){
													$exchange_to_product = $value_exchange;
													$total_price_exchange_tb = $total_price_exchange_tb + ($value_exchange['price'] - $mwb_actual_price);
												}
											}
											
											if(!empty($exchange_to_product)){
														$props = array();
													$variation_attributes = array();
													$ced_woo_tax_enable_setting = get_option( 'woocommerce_calc_taxes' );
													$ced_woo_tax_display_shop_setting = get_option( 'woocommerce_tax_display_shop' );
													$ced_rnx_tax_test = false;
													if ( isset( $exchange_to_product['variation_id'] ) ) {
														if ( $exchange_to_product['variation_id'] ) {
															$variation_product = wc_get_product( $exchange_to_product['variation_id'] );
															$variation_attributes = $variation_product->get_variation_attributes();
															$variation_attributes = isset( $exchange_to_product['variations'] ) ? $exchange_to_product['variations'] : $variation_product->get_variation_attributes();
															$variation_labels = array();
															foreach ( $variation_attributes as $label => $value ) {
																if ( is_null( $value ) || $value == '' ) {
																	$variation_labels[] = $label;
																}
															}
															if ( count( $variation_labels ) ) {
																$all_line_items = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
																$var_attr_info = array();
																foreach ( $all_line_items as $ear_item ) {
																	$variationID = isset( $ear_item['item_meta']['_variation_id'] ) ? $ear_item['item_meta']['_variation_id'][0] : 0;

																	if ( $variationID && $variationID == $exchange_to_product['variation_id'] ) {
																		$itemMeta = isset( $ear_item['item_meta'] ) ? $ear_item['item_meta'] : array();

																		foreach ( $itemMeta as $metaKey => $metaInfo ) {
																			$metaName = 'attribute_' . sanitize_title( $metaKey );
																			if ( in_array( $metaName, $variation_labels ) ) {
																				$variation_attributes[ $metaName ] = isset( $term->name ) ? $term->name : $metaInfo[0];
																			}
																		}
																	}
																}
															}
															$ced_rnx_thumbnail     = wp_get_attachment_image_src( $variation_product->get_image_id(), 'thumbnail' );
															$ced_rnx_thumbnail = $ced_rnx_thumbnail[0];

															if ( $ced_woo_tax_enable_setting == 'yes' ) {
																$ced_rnx_tax_test = true;
																$ced_rnx_exchange_to_product_price = wc_get_price_including_tax( $variation_product );
															} else {
																$ced_rnx_exchange_to_product_price = $exchange_to_product['price'];
															}
															$product = wc_get_product( $exchange_to_product['variation_id'] );

														}
													} else {
														$product = wc_get_product( $exchange_to_product['id'] );
														if ( $ced_woo_tax_enable_setting == 'yes' ) {
															$ced_rnx_tax_test = true;
															$ced_rnx_exchange_to_product_price = wc_get_price_including_tax( $product );
														} else {
															$ced_rnx_exchange_to_product_price = $exchange_to_product['price'];
														}

														$ced_rnx_thumbnail = wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' );
														$ced_rnx_thumbnail = $ced_rnx_thumbnail[0];

													}


													if ( isset( $exchange_to_product['p_id'] ) ) {
														if ( $exchange_to_product['p_id'] ) {
															$grouped_product = new WC_Product_Grouped( $exchange_to_product['p_id'] );
															$grouped_product_title = $grouped_product->get_title();
															$props = wc_get_product_attachment_props( get_post_thumbnail_id( $exchange_to_product['p_id'] ), $grouped_product );
														}
													}


													$pro_price = $exchange_to_product['qty'] * $ced_rnx_exchange_to_product_price;
													$total_price += $pro_price;

													?>
													
														<?php
														if ( isset( $ced_rnx_thumbnail ) && ! is_null( $ced_rnx_thumbnail ) ) {
															?>
																<div class="ced_rnx_prod_img"><img width="100" height="100" alt="" class="attachment-thumbnail size-thumbnail wp-post-image" src="<?php echo $ced_rnx_thumbnail; ?>"></div>
															<?php
														} else {
															?>
																<div class="ced_rnx_prod_img"><img width="100" height="100" alt="" class="attachment-thumbnail size-thumbnail wp-post-image" src="<?php echo home_url( '/wp-content/plugins/woocommerce/assets/images/placeholder.png' ); ?>"></div>
															<?php
														}

														?>
													<div class="ced_rnx_product_title">
														<?php
														if ( isset( $exchange_to_product['p_id'] ) ) {
															echo $grouped_product_title . ' -> ';
														}
														$is_visible=$product && $product->is_visible();
														$product_permalinks = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );
														echo apply_filters( 'woocommerce_order_item_name', $product_permalinks ? sprintf( '<a href="%s">%s</a>', $product_permalinks, $product->get_title() ) : $product->get_title(), $item, $is_visible );

														// echo $product->get_title();
														if ( isset( $variation_attributes ) && ! empty( $variation_attributes ) ) {
															//echo wc_get_formatted_variation( $variation_attributes );
														}
														?>
														<p>
															<b><?php _e( 'Price', 'woocommerce-refund-and-exchange' ); ?> : </b>
																	<?php
																		echo ced_rnx_format_price( $pro_price );
																		// $total_price_exchange_tb = $total_price_exchange_tb + $pro_price
																	?>
															
														</p>
														<p style="color: red !important;" data-key="<?php echo $start_row; ?>" class="exchnaged_product_remove">Remove<a class="remove" href="javascript:void(0)">×</a></p>
													</div>

												<?php }
												?>
													<p class="form-row form-row form-row-wide show_choose_button" <?php if(!empty($exchange_to_product)){?> style="display:none;"<?php } ?>>
														<?php
														$choose_product_button = '<input type="button" class="button btn" name="ced_rnx_exhange_shop"  id="ced_rnx_exhange_shop" value="' . __( 'CHOOSE PRODUCTS', 'woocommerce-refund-and-exchange' ) . '" class="input-text">';
														?>
														<a class="ced_rnx_exhange_shop" data-price="<?php echo $mwb_actual_price;?>" data-key="<?php echo $start_row;?>" href="javascript:void(0);" id="<?php echo $product_id;?>">
															<?php echo apply_filters( 'ced_rnx_exchange_choose_product_button', $choose_product_button ); ?>
															<span class="ced_rnx_exchange_notification_choose_product "><img src="<?php echo CED_REFUND_N_EXCHANGE_URL; ?>/assets/images/loading.gif" width="20px"></span>
														</a>
													</p>
												
											<input type="text" value="<?php echo $exchange_to_product['price']; ?>" id="ced_rnx_exchanged_total" class="ced_rnx_exchanged_total" style="display:none;">
										</td>
										<td class="reason_tab" style="width:30%;">
											<form action="ced_rnx_submit_exchange_request" method="post" id="ced_rnx_exchange_request_form" data-orderid="<?php echo esc_html( $order_id ); ?>" enctype="multipart/form-data">
											<div class="ced_rnx_subject_dropdown">
												<select name="ced_rnx_exchange_request_subject" data-sort="<?php echo $start_row;?>" id="ced_rnx_exchange_request_subject" class="ced_rnx_exchange_request_subject jsselect<?php echo $start_row;?>" style="max-width: 100%;">
													<option value="0" selected data-keyid="0"><?php _e( 'Select Reason', 'woocommerce-refund-and-exchange' ); ?></option>
													<?php
													if ( isset( $predefined_exchange_reason ) && ! empty( $predefined_exchange_reason ) ) {
														$ii=1;
														foreach ( $predefined_exchange_reason as $predefine_reason ) {
															if ( isset( $predefine_reason ) && ! empty( $predefine_reason ) ) {
																?>
																<option value="<?php echo $predefine_reason; ?>" data-sort="<?php echo $ii;?>" data-keyid="<?php echo $ii;?>"><?php echo $predefine_reason; ?></option>
															<?php
															}
															$ii++;
														}
													}
													?>
													<!-- <option value="" data-keyid="0"><?php _e( 'Other', 'woocommerce-refund-and-exchange' ); ?></option> -->
												</select>
												<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
												<script>
												$(function() {
                                                    $('.jsselect<?php echo $start_row;?>').change(function() {
                                                        localStorage.setItem('todoData<?php echo $start_row;?>', this.value);
                                                    });
                                                    if(localStorage.getItem('todoData<?php echo $start_row;?>')){
                                                        $('.jsselect<?php echo $start_row;?>').val(localStorage.getItem('todoData<?php echo $start_row;?>'));
                                                    }
                                                });
                       
												</script>
												
										
											</div>
											<!-- <label><b><?php _e( 'Attach Files', 'woocommerce-refund-and-exchange' ); ?></b></label> -->
											
											
											</form>
										</td>
									</tr>
									<?php
							$start_row++;
							}
							}
						}
						?>
                                                <?php //_e( '(incl. tax)', 'woocommerce-refund-and-exchange' ); ?> 
						<!--	</td>-->
						<!--	<th scope="row" class="exchange-th"><b>Extra Amount Need to Pay<b></th>-->
						<!--	<td class="ced_rnx_total_amount_wrap">-->
						<!--		<span id="ced_rnx_total_exchange_amount">-->
						<!--		<?php //echo ced_rnx_format_price( $total_price_exchange_tb ); ?></span> -->
							
						<!--	</td>-->
							
						<!--</tr>-->
					</tbody>
				</table>
				<div class="ced_rnx_return_notification_checkbox"><img src="<?php echo CED_REFUND_N_EXCHANGE_URL; ?>/assets/images/loading.gif" width="40px"></div>
			<h3 style="margin-top: 15px;margin-bottom: 0px;font-size: 15px;">Upload the product exchange images</h3>
			<p class="form-row form-row form-row-wide images_return_form image_error_st" style="margin-bottom: 0px;text-align:left;margin-top: 10px;display: block;">
				<span>
					<input type="hidden" name="action" value="<?php _e( 'ced_rnx_refund_upload_files', 'woocommerce-refund-and-exchange' ); ?>">
					<input type="file" name="ced_rnx_return_request_files[]" id="ced_rnx_return_request_files" class="input-text ced_rnx_return_request_files" style="background: #ce2c30;color: white;" onchange="GetFileSize()" required multiple>
                    <p id="fp"></p>	
                    <script>
                        function GetFileSize() {
                            var fi = document.getElementById('ced_rnx_return_request_files'); // GET THE FILE INPUT.
                            var total_size = fi.files.length;
                            //console.log(total_size);
                            // VALIDATE OR CHECK IF ANY FILE IS SELECTED.
                            if (fi.files.length > 0) {
                                // RUN A LOOP TO CHECK EACH SELECTED FILE.
                                var total = 0;
                                for (var i = 0; i <= fi.files.length - 1; i++) {
                                    var fsize = fi.files.item(i).size;      // THE SIZE OF THE FILE.
                                    var file = Math.round((fsize / 1024) * total_size);
                                    // The size of the file.
                                    if (file >= 25000) {
                                        
                                        alert("Total image size should be less than 25MB");
                                        $("#ced_rnx_return_request_files").val('');
                                        break;
                                    }  
                                    // else {
                                    //     document.getElementById('fp').innerHTML = '<b>'
                                    //     + file + '</b> KB';
                                    // }
                                }
                            }
                            var filePath = fi.value;
                             // Allowing file type
                            var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
                            if (!allowedExtensions.exec(filePath)) {
                                alert('Upload only .jpg | .jpeg | .png extension images.');
                                $("#ced_rnx_return_request_files").val('');
                                return false;
                            }
                        }
                    </script>
				</span>
                <!--<span style="line-height: 38px!important;">Minimum 1 image is required!</span>-->
				<!-- <input type="button" value="<?php _e( 'Add More', 'woocommerce-refund-and-exchange' ); ?>" class="btn button ced_rnx_return_request_morefiles" data-count="1" data-max="10"> -->
				<!-- <i><?php _e( 'Only .png, .jpeg extension file is approved.', 'woocommerce-refund-and-exchange' ); ?></i> -->
			</p>
            <p class="form-row form-row form-row-wide request_subject_text" style="display:none;margin-top: 10px;">	<input type="text" name="ced_rnx_exchange_request_subject" maxlength="50" id="ced_rnx_exchange_request_subject_text" class="input-text ced_rnx_exchange_request_subject" placeholder="<?php _e( 'Write your reason subject', 'woocommerce-refund-and-exchange' ); ?>"></p>
			</div>
			<p class="form-row form-row form-row-wide ced_rnx_exchange_note">
				<?php do_action( 'ced_rnx_exchange_after_order_item_table' ); ?>
			</p>
			
			<p class="form-row form-row form-row-wide cancel_exchange_button">
				<!--<input type="submit" class="button btn ced_rnx_exchange_request_submit" name="ced_rnx_exchange_request_submit" value="<?php //_e( 'Submit Request', 'woocommerce-refund-and-exchange' ); ?>" class="input-text">-->
				<?php 
                    $exchange_details = get_post_meta( $order_id, 'ced_rnx_exchange_product', true );
                    if($exchange_details == ''){   
                ?>
				<button type="button" class="button btn btn-success ced_confirm_exchange" id="openModal" data-target="#confirm-exchange" data-toggle="modal">Submit Request</button>
				<?php 
				    } else{ ?>
				<script>
				    alert('It seems, your exchange request is already submitted, kindly check your order details page.');
				    window.location.href = "https://shop.studds.com/my-account/";
				    
				</script>
				<?php } ?>
				<div class="ced_rnx_exchange_notification"><img src="<?php echo CED_REFUND_N_EXCHANGE_URL; ?>/assets/images/loading.gif" width="40px"></div>
			</p>
			<div class="ced_rnx_note_tag_wrapper lower_wrapper mwb_rma_flex">
				<div class="mwb_rma__column left_lower_wrapper">
					<!-- <input type="text" value="<?php echo $total_price; ?>" id="ced_rnx_exchanged_total" style="display:none;"> -->
					<p class="form-row form-row form-row-wide" id="ced_rnx_exchange_extra_amount" style="display: none;">
						<label>
							<b>
								<?php
									$reason_exchange_amount = __( '<i>Extra Amount Need to Pay</i>', 'woocommerce-refund-and-exchange' );
								;
									echo apply_filters( 'ced_rnx_exchange_extra_amount', $reason_exchange_amount );
								?>
								: 
								<span class="ced_rnx_exchange_extra_amount">
									<?php echo ced_rnx_format_price( 0 ); ?>
								</span>
							</b>
						</label>
					</p>
					
				</div>
				<?php
				$rule_enable = get_option( 'ced_rnx_exchange_rule' );
				$rules       = get_option( 'ced_rnx_exchange_rule_editor' );
				if ( 'yes' === $rule_enable && ! empty( $rules ) ) {
					?>
					<div class="mwb_rma__column right_lower_wrapper mwb_rma_flex" style="display: none;">
						<div>
						<?php
						echo wp_kses_post( $rules );
						?>
						</div>
					</div>
					<?php
				}
				?>
				<br/>
				<p class="form-row form-row form-row-wide">
					<?php do_action( 'ced_rnx_exchange_after_submit_button' ); ?>
				</p>

				<div class="ced-rnx_customer_detail">
					<?php wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) ); ?>
				</div>
			</div>
	</div>
	<div class="modal fade" id="confirm-exchange" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
		    <div class="modal-dialog" style="top: 30%;">
		        <div class="modal-content">
		            <div class="modal-header" style="color:black; background-color:white;">
		               <h3 class="modal-title">Confirm Exchange Request</h3>
        				<button type="button" class="close" data-dismiss="modal">&times;</button>
		            </div>
		            <div class="modal-body">
		               <div style="padding-left:10px;">
		               	<h5>Are you sure you want to exchange this order?</h5>

		               	<p class="form-row validate-required cancel_policy_tab">
							<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
								<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox fc-terms-checkbox" name="terms" id="terms" required>
								<span class="woocommerce-terms-and-conditions-checkbox-text">I have read the <a href="https://shop.studds.com/our-policies/#exchange-policy" class="woocommerce-terms-and-conditions-link" target="_blank">Exchange policy</a> and agree to the same.</span>
							</label>
						</p>
						<div style="display:none; color:red" id="agree_chk_error">
							  Can't proceed as you didn't agree to the terms!
						</div>
		               </div>
		            </div>
		            <div class="modal-footer" style="background-color:white;">
		                <input type="submit" class="btn-success ced_rnx_exchange_request_submit myButton" name="ced_rnx_exchange_request_submit"  style="background: green;border-radius: 4px;border: 0px;font-size: 12px!important;padding-top: 6px;padding-bottom: 6px;" value="<?php _e( 'Confirm Request', 'woocommerce-refund-and-exchange' ); ?>" class="input-text">
		            	<!--<a class="btn btn-danger remove_items">Cancel Order</a>-->
		                <button type="button" id="cancel-exchange" class="btn btn-default" data-dismiss="modal">Back</button>
		            </div>
		        </div>
		    </div>
		</div>	
	<?php
} else {
	 $exchange_request_not_send = __( 'Exchange Request can\'t be sent. ', 'woocommerce-refund-and-exchange' );
	 echo apply_filters( 'ced_rnx_exchange_request_not_send', $exchange_request_not_send );
	 echo $reason;
}
/**
 * woocommerce_after_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
if ( $ced_rnx_show_sidebar_on_form == 'yes' ) {
	do_action( 'woocommerce_after_main_content' );
}



get_footer( 'shop' );
?>
