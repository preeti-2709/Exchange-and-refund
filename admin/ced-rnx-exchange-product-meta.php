<?php
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Show Exchange Product detail on Order Page on admin Side

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
//echo $order_id;
$exchange_details = get_post_meta( $order_id, 'ced_rnx_exchange_product', true );
// $exchange_warrantry_extrajson = get_post_meta( $order_id, 'ced_rnx_exchange_warrantry_extrajson',true);
// echo "<pre>";
// print_r($exchange_warrantry_extrajson);
$line_items  = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
$save_ex_line_items = get_post_meta( $order_id, 'ced_rnx_save_ex_line_items', true );
if ( empty( $save_ex_line_items ) ) {
	update_post_meta( $order_id, 'mwb_rnx_new_refund_line_items', $line_items );
	update_post_meta( $order_id, 'ced_rnx_save_ex_line_items', 'saved' );
}
$line_items = get_post_meta( $order_id, 'mwb_rnx_new_refund_line_items', true );
// Get Pending exchange request
// echo "<pre>";
// print_r($selected_product[$tok]['approve_status']);
if ( isset( $exchange_details ) && ! empty( $exchange_details ) ) {
	foreach ( $exchange_details as $date => $exchange_detail ) {
		// if ( isset( $exchange_details[ $date ]['subject'] ) && $exchange_details[ $date ]['reason'] ) {
				$approve_date = date_create( $date );
				$date_format = get_option( 'date_format' );
				$approve_date = date_format( $approve_date, $date_format );


				$pending_date = '';
			if ( $exchange_detail['status'] == 'pending' ) {
				$pending_date = $date;
			}
				$subject = $exchange_details[ $date ]['subject'];
				$reason = $exchange_details[ $date ]['reason'];
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
			if ( isset( $exchange_detail['fee'] ) ) {
				$exchange_fees = $exchange_detail['fee'];
			} else {
				$exchange_fees = array();
			}
			$order_status = 'wc-' . $order->get_status();
				$exchange_status = $exchange_detail['status'];
				$exchange_reason = $exchange_detail['reason'];
				$exchange_subject = $exchange_detail['subject'];

				_e( 'Following product exchange request is made on', 'woocommerce-refund-and-exchange' ); ?> <b><?php echo $approve_date; ?>.</b>
	
				<div>
				    
					<div id="ced_rnx_exchange_wrapper">
					<!-- <p><b><?php _e( 'Exchanged Product', 'woocommerce-refund-and-exchange' ); ?></b></p> -->
					<h3><b><?php _e( 'Exchanged Product', 'woocommerce-refund-and-exchange' ); ?></b></h3>
					<table class="exchange-order-items">
						<thead>
							<tr>
								<th colspan="2"><?php _e( 'Product', 'woocommerce-refund-and-exchange' ); ?></th>
								<th colspan="2"><?php _e( 'Requested Product', 'woocommerce-refund-and-exchange' ); ?></th>
								<th><?php _e( 'Cost', 'woocommerce-refund-and-exchange' ); ?></th>
								<!-- <th><?php _e( 'Qty', 'woocommerce-refund-and-exchange' ); ?></th> -->
								<th><?php _e( 'Request', 'woocommerce-refund-and-exchange' ); ?></th>
								<!-- <th><?php _e( 'Total', 'woocommerce-refund-and-exchange' ); ?></th> -->
							</tr>
						</thead>
						<tbody>
						<?php
						if ( isset( $exchange_products ) && ! empty( $exchange_products ) ) {
						    
							$selected_total_price = 0;
							$start_row = 0;
							$i = 0;
							foreach ( $line_items as $item_id => $item ) {
								foreach ( $exchange_products as $key => $exchanged_product ) {
								    //echo "<pre>";
								    //print_r($exchange_products);
									if ( $item_id == $exchanged_product['item_id'] ) {
										if ( isset( $item['variation_id'] ) && $item['variation_id'] > 0 ) {
											$variation_id = $item['variation_id'];
											$product_id = $item['product_id'];
										} else {
											$product_id = $item['product_id'];
										}
										if ( $item_id == $exchanged_product['item_id'] ) {

											$ex_product_data = $order->get_meta_data();
											foreach ( $ex_product_data as $value ) {
												$ex_product_details = $value->get_data();
												if ( $ex_product_details['key'] == 'ced_rnx_exchange_product' ) {
													$ex_product_details_values = $ex_product_details['value'];
													foreach ( $ex_product_details_values as $value ) {
														$ex_product_details_values1 = $value['from'];
														foreach ( $ex_product_details_values1 as $value ) {
															$ex_product_id = $value['product_id'];
															$get_ex_product = wc_get_product( $ex_product_id );
															$image = wp_get_attachment_image_src( get_post_thumbnail_id( $ex_product_id ), 'single-post-thumbnail' );

															$ex_product_new[] = array(
																'name' => $get_ex_product->get_name(),
																'sku'    => $get_ex_product->get_sku(),
																'image'  => $image[0],
															);
														}
													}
												}
											}
										}
										$_product  = $item->get_product();
										$item_meta = wc_get_order_item_meta( $item_id, $key );
										$thumbnail     = $_product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $_product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
										?>
										<tr class="ced_rnx_exchange_column" data-productid="<?php echo $product_id; ?>" data-variationid="<?php echo $item['variation_id']; ?>" data-itemid="<?php echo $item_id; ?>" data-rowkey="<?php echo $start_row; ?>" data-sku="<?php echo $ex_product_new[ $key ]['sku'];?>">

											<td class="thumb">
											<?php
											if ( isset( $ex_product_new[ $key ]['image'] ) && ! empty( $ex_product_new[ $key ]['image'] ) ) {
												echo '<div class="wc-order-item-thumbnail"><img src="' . $ex_product_new[ $key ]['image'] . '" class="exchange_img"></div>';
											}
											?>
											</td>
											<td class="product-name">
											<?php
											if ( isset( $ex_product_new[ $key ]['name'] ) && ! empty( $ex_product_new[ $key ]['name'] ) ) {
												echo esc_html( $ex_product_new[ $key ]['name'] );
											}
											if ( isset( $ex_product_new[ $key ]['sku'] ) && ! empty( $ex_product_new ) ) {
												echo '<div class="wc-order-item-sku"><strong>' . __( 'SKU:', 'woocommerce-refund-and-exchange' ) . '</strong> ' . esc_html( $_product->get_sku()) . '</div>';
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
											<?php 
											$exchange_to_product = '';
											$variation_attributes = array();

											// echo $i;
											// echo "<br/>";
											// echo $start_row;

											foreach ($exchange_to_products as $key_exchange => $value_exchange){
											    //echo "<pre>";
											    //print_r($exchange_to_products);
												// if($i == $start_row){
													if($value_exchange['product_id'] == $product_id && $value_exchange['product_id_key'] == $exchanged_product['product_id_key']){
														$exchange_to_product = $value_exchange;
														$key = $key_exchange;
														$total_price_exchange_tb = $total_price_exchange_tb + ($value_exchange['price'] - $mwb_actual_price);
													}
													// }else if($i == $start_row){
													// 	$exchange_to_product = $value_exchange;
													// 	$total_price_exchange_tb = $total_price_exchange_tb + ($value_exchange['price'] - $mwb_actual_price);
													// }

													// $i++;

												// }
												// break;
											}

								// 			echo "<pre>";
								// 			print_r($exchange_to_product); 
											if(!empty($exchange_to_product)){
												// Variable Product
												if ( isset( $exchange_to_product['variation_id'] ) ) {
													if ( $exchange_to_product['variation_id'] ) {
														$variation_product = wc_get_product( $exchange_to_product['variation_id'] );
														$variation_attributes = $variation_product->get_variation_attributes();
														$variation_labels = array();
														foreach ( $variation_attributes as $label => $value ) {
															if ( is_null( $value ) || $value == '' ) {
																$variation_labels[] = $label;
															}
														}

														if ( isset( $exchange_to_product['variations'] ) && ! empty( $exchange_to_product['variations'] ) ) {
															$variation_attributes = $exchange_to_product['variations'];
														}
														if ( $ced_woo_tax_enable_setting == 'yes' ) {
															$ced_rnx_tax_test = true;
															if ( isset( $exchange_to_product['price'] ) ) {
																$exchange_to_product_price = $exchange_to_product['price'];
															} else {
																$exchange_to_product_price = wc_get_price_including_tax( $variation_product );
															}
														} else {
															$exchange_to_product_price = $exchange_to_product['price'];
														}
													} $product = wc_get_product( $exchange_to_product['variation_id'] );
												} else {
													$product = wc_get_product( $exchange_to_product['id'] );

													if ( $ced_woo_tax_enable_setting == 'yes' ) {
														$ced_rnx_tax_test = true;
														if ( isset( $exchange_to_product['price'] ) ) {
															$exchange_to_product_price = $exchange_to_product['price'];
														} else {
															$exchange_to_product_price = wc_get_price_including_tax( $product );
														}
													} else {
														$exchange_to_product_price = $exchange_to_product['price'];
													}
												}
												// Grouped Product
												if ( isset( $exchange_to_product['p_id'] ) ) {
													if ( $exchange_to_product['p_id'] ) {
														$grouped_product = new WC_Product_Grouped( $exchange_to_product['p_id'] );
														$grouped_product_title = $grouped_product->get_title();
													}
												}
												$pro_price = $exchange_to_product['qty'] * $exchange_to_product_price;
												$total_price += $pro_price;
											}
											?>
											<td class="thumb">
												<?php
												if ( isset( $exchange_to_product['p_id'] ) ) {
													echo $grouped_product->get_image();
												} elseif ( isset( $variation_attributes ) && ! empty( $variation_attributes ) ) {
													echo $variation_product->get_image();
												} else {
													echo $product->get_image();
												}
												?>
											</td>
											<td class="product-name product-name-to" data-sku_id="<?php echo $product->get_sku();?>" data-item_id="<?php echo $exchange_to_product['id'];?>" data-key="<?php echo $key;?>">
												<?php
												if ( isset( $exchange_to_product['p_id'] ) ) {
													echo $grouped_product_title . ' -> ';
												}
													echo $product->get_title();
												if ( $product && $product->get_sku() ) {
														echo '<div class="wc-order-item-sku"><strong>' . __( 'SKU:', 'woocommerce-refund-and-exchange' ) . '</strong> ' . esc_html( $product->get_sku() ) . '</div>';
												}
												if ( isset( $variation_attributes ) && ! empty( $variation_attributes ) ) {

													echo wc_get_formatted_variation( $variation_attributes );
												}
												?>
											</td>
											
											<td><?php echo ced_rnx_format_price( $exchanged_product['price'] ); ?></td>
											<!-- <td><?php echo $exchanged_product['qty']; ?></td> -->
											<td>
											<?php 
											//print_r($order_status);
											if(!isset($exchange_to_product['approve_status_key']) || ($order_status == 'wc-exchange_received' && $exchange_to_product['approve_status_key'] == '1')){
											//if($order_status == 'wc-exchange_received' && $exchange_to_product['approve_status_key'] == '1'){
													?>
												<select name="approve_status" id="approve_status">
												  <option value="1" <?php if(isset($exchange_to_product['approve_status_key']) && $exchange_to_product['approve_status_key'] == '1'){echo "selected";}?> >Accept</option>
												  <option value="2" <?php if(isset($exchange_to_product['approve_status_key']) && $exchange_to_product['approve_status_key'] == '2'){echo "selected";}?>>Reject</option>
												</select>
											<?php } elseif($exchange_to_product['approve_status_key'] == '1' && $order_status !== 'wc-exchange-rejected'){?>
												<p>Accepted</p>
											<?php } elseif($exchange_to_product['approve_status_key'] == '2' && $order_status !== 'wc-exchange-rejected'){?>
												<p style="color:red;">Rejected</p>
											<?php } elseif($exchange_detail['status'] == 'cancel'){ ?>
											    <p style="color:red;">Exchange Cancelled</p>
											<?php } ?>
											</td>
										<!-- 	// <td><?php echo ced_rnx_format_price( $exchanged_product['price'] * $exchanged_product['qty'] ); ?></td> -->
										</tr>
										<?php
										$selected_total_price += $exchanged_product['price'] * $exchanged_product['qty'];
									
										?>	
										<tr style="text-align: left;">
											<th colspan="2"></th>
											<th colspan="5" style="padding: 10px"><strong><?php _e( 'Reason', 'woocommerce-refund-and-exchange' ); ?> : </strong><i><?php echo $exchange_to_product['reason']; ?></i></th>
										</tr>
										
									<?php 
									$start_row++;
									}

								}
								
							}
						}
						?>
							<tr>
								<th colspan="2"><?php _e( 'Net Amount', 'woocommerce-refund-and-exchange' ); ?></th>
								<th><?php echo ced_rnx_format_price( $selected_total_price - $total_price ); ?></th>
								<th colspan="2"><?php _e( 'Total', 'woocommerce-refund-and-exchange' ); ?></th>
								<th><?php echo ced_rnx_format_price( $selected_total_price ); ?></th>
							</tr>
                        <tr>
											<!--<th colspan="2"></th>-->
											<!--<th colspan="5" style="padding: 10px"><strong>-->
											
										<th style="padding: 10px;text-align: left!important;" colspan="6"><strong>
											<?php //_e( 'Images', 'woocommerce-refund-and-exchange' ); ?>  </strong>
											<style>
                                                /* The Modal (background) */
                                                .centerexchange{
                                                    display:block;
                                                    margin-left:auto;
                                                    margin-right:auto;
                                                    width:400px!important;
                                                    height:400px!important;
                                                }
                                                .modal {
                                                  display: none;
                                                  position: fixed;
                                                  z-index: 9999;
                                                  padding-top: 100px;
                                                  left: 0;
                                                  top: 0;
                                                  width: 100%;
                                                  height: 100%;
                                                  overflow: auto;
                                                  background-color: black;
                                                }
                                                
                                                /* Modal Content */
                                                .modal-content {
                                                  position: relative;
                                                  background-color: #000000;
                                                  margin: auto;
                                                  padding: 0;
                                                  width: 90%;
                                                  max-width: 1200px;
                                                }
                                                
                                                /* The Close Button */
                                                .close {
                                                  color: white;
                                                  position: absolute;
                                                  top: 10px;
                                                  right: 25px;
                                                  font-size: 35px;
                                                  font-weight: bold;
                                                }
                                                
                                                .close:hover,
                                                .close:focus {
                                                  color: #999;
                                                  text-decoration: none;
                                                  cursor: pointer;
                                                }
                                                
                                                .mySlides {
                                                  display: none;
                                                }
                                                
                                                .cursor {
                                                  cursor: pointer;
                                                }
                                                
                                                /* Next & previous buttons */
                                                .prev,
                                                .next {
                                                    background: #b50000;
                                                  cursor: pointer;
                                                  position: absolute;
                                                  top: 50%;
                                                  width: auto;
                                                  padding: 16px;
                                                  margin-top: -50px;
                                                  color: white;
                                                  font-weight: bold;
                                                  font-size: 20px;
                                                  transition: 0.6s ease;
                                                  border-radius: 0 3px 3px 0;
                                                  user-select: none;
                                                  -webkit-user-select: none;
                                                }
                                                
                                                /* Position the "next button" to the right */
                                                .next {
                                                  right: 0;
                                                  border-radius: 3px 0 0 3px;
                                                }
                                                
                                                /* On hover, add a black background color with a little bit see-through */
                                                .prev:hover,
                                                .next:hover {
                                                  background-color: rgba(0, 0, 0, 0.8);
                                                }
                                                
                                                /* Number text (1/3 etc) */
                                                .numbertext {
                                                  color: #f2f2f2;
                                                  font-size: 12px;
                                                  padding: 8px 12px;
                                                  position: absolute;
                                                  top: 0;
                                                }
                                                
                                                img {
                                                  margin-bottom: -4px;
                                                }
                                                
                                                .caption-container {
                                                  text-align: center;
                                                  background-color: black;
                                                  padding: 2px 16px;
                                                  color: white;
                                                }
                                                
                                                .demo {
                                                  opacity: 0.6;
                                                }
                                                
                                                .active,
                                                .demo:hover {
                                                  opacity: 1;
                                                }
                                                
                                                img.hover-shadow {
                                                  transition: 0.3s;
                                                }
                                                
                                                .hover-shadow:hover {
                                                  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
                                                }
                                            </style>
											<div class="row">
											<?php 
												$count = 1;
												foreach ( $exchange_to_product['files'] as $attachment ) {
													if ( $attachment != $order_id . '-' ) {
														?>

														<a href="<?php //echo home_url(); ?>/wp-content/uploads/Exchange_Images/<?php echo $attachment; ?>" target="_blank"><?php //_e( 'Attachment', 'woocommerce-refund-and-exchange' ); ?><?php //echo $count; ?></a>
														<!-- The Modal -->
                                                        
                                                            <div class="column">
                                                                <img src="<?php echo home_url(); ?>/wp-content/uploads/Exchange_Images/<?php echo $attachment; ?>" onclick="openModal();currentSlide(1)" class="hover-shadow cursor" style="float: left;width: 80px !important;height: 80px !important;"/>
                                                                <!--<img src="img_nature.jpg" style="width:100%" onclick="openModal();currentSlide(1)" class="hover-shadow cursor">-->
                                                            </div>
                                                          
                                                        
														
														<?php
														//$count++;
													}
												}
												?>
											</div>
											    <div id="myModal" class="modal">
                                                <span class="close cursor" onclick="closeModal()">&times;</span>
                                                    <div class="modal-content">
                                                    	<?php 
												            $count = 1;
												            foreach ( $exchange_to_product['files'] as $attachment ) {
													           if ( $attachment != $order_id . '-' ) {
														?>
                                                        <div class="mySlides">
                                                          <div class="numbertext"></div>
                                                          <img src="<?php echo home_url(); ?>/wp-content/uploads/Exchange_Images/<?php echo $attachment; ?>" class="centerexchange" style="width: 400px!important;height: 400px!important">
                                                        </div>

                                                        <?php 
													           }
												            }
												        ?>
    
                                                        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                                                        <a class="next" onclick="plusSlides(1)">&#10095;</a>

                                                        <div class="caption-container">
                                                          <p id="caption"></p>
                                                        </div>

                                                    </div>
                                                </div>
                                                
                                                <script>
function openModal() {
  document.getElementById("myModal").style.display = "block";
}

function closeModal() {
  document.getElementById("myModal").style.display = "none";
}

var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("demo");
  var captionText = document.getElementById("caption");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
  captionText.innerHTML = dots[slideIndex-1].alt;
}
</script>
										</th>
										</tr>
						</tbody>
					</table>	
				</div>
				<!-- <div id="ced_rnx_exchange_wrapper">
					<h3><b><?php _e( 'Requested Product', 'woocommerce-refund-and-exchange' ); ?></b></h3>
					<table class="admin_order_details">
						<thead>
							<tr>
								
								<th colspan="2"><?php _e( 'Product', 'woocommerce-refund-and-exchange' ); ?></th>
								<th><?php _e( 'Cost', 'woocommerce-refund-and-exchange' ); ?></th>
								<th><?php _e( 'Qty', 'woocommerce-refund-and-exchange' ); ?></th>
								<th><?php _e( 'Total', 'woocommerce-refund-and-exchange' ); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php
						$ced_woo_tax_enable_setting = get_option( 'woocommerce_calc_taxes' );
						$ced_woo_tax_display_shop_setting = get_option( 'woocommerce_tax_display_shop' );
						$ced_rnx_tax_test = false;


						if ( isset( $exchange_to_products ) && ! empty( $exchange_to_products ) ) {
							$total_price = 0;
							foreach ( $exchange_to_products as $key => $exchange_to_product ) {
								$variation_attributes = array();

								
								if ( isset( $exchange_to_product['variation_id'] ) ) {
									if ( $exchange_to_product['variation_id'] ) {
										$variation_product = wc_get_product( $exchange_to_product['variation_id'] );
										$variation_attributes = $variation_product->get_variation_attributes();
										$variation_labels = array();
										foreach ( $variation_attributes as $label => $value ) {
											if ( is_null( $value ) || $value == '' ) {
												$variation_labels[] = $label;
											}
										}

										if ( isset( $exchange_to_product['variations'] ) && ! empty( $exchange_to_product['variations'] ) ) {
											$variation_attributes = $exchange_to_product['variations'];
										}
										if ( $ced_woo_tax_enable_setting == 'yes' ) {
											$ced_rnx_tax_test = true;
											if ( isset( $exchange_to_product['price'] ) ) {
												$exchange_to_product_price = $exchange_to_product['price'];
											} else {
												$exchange_to_product_price = wc_get_price_including_tax( $variation_product );
											}
										} else {
											$exchange_to_product_price = $exchange_to_product['price'];
										}
									} $product = wc_get_product( $exchange_to_product['variation_id'] );
								} else {
									$product = wc_get_product( $exchange_to_product['id'] );

									if ( $ced_woo_tax_enable_setting == 'yes' ) {
										$ced_rnx_tax_test = true;
										if ( isset( $exchange_to_product['price'] ) ) {
											$exchange_to_product_price = $exchange_to_product['price'];
										} else {
											$exchange_to_product_price = wc_get_price_including_tax( $product );
										}
									} else {
										$exchange_to_product_price = $exchange_to_product['price'];
									}
								}
								// Grouped Product
								if ( isset( $exchange_to_product['p_id'] ) ) {
									if ( $exchange_to_product['p_id'] ) {
										$grouped_product = new WC_Product_Grouped( $exchange_to_product['p_id'] );
										$grouped_product_title = $grouped_product->get_title();
									}
								}

								$pro_price = $exchange_to_product['qty'] * $exchange_to_product_price;
								$total_price += $pro_price;
								?>
								<tr>
									<td class="thumb">
										<?php
										if ( isset( $exchange_to_product['p_id'] ) ) {
											echo $grouped_product->get_image();
										} elseif ( isset( $variation_attributes ) && ! empty( $variation_attributes ) ) {
											echo $variation_product->get_image();
										} else {
											echo $product->get_image();
										}
										?>
									</td>
									<td class="product-name">
										<?php
										if ( isset( $exchange_to_product['p_id'] ) ) {
											echo $grouped_product_title . ' -> ';
										}
											echo $product->get_title();
										if ( $product && $product->get_sku() ) {
												echo '<div class="wc-order-item-sku"><strong>' . __( 'SKU:', 'woocommerce-refund-and-exchange' ) . '</strong> ' . esc_html( $product->get_sku() ) . '</div>';
										}
										if ( isset( $variation_attributes ) && ! empty( $variation_attributes ) ) {

											echo wc_get_formatted_variation( $variation_attributes );
										}
										?>
									</td>
									<td><?php echo ced_rnx_format_price( $exchange_to_product_price ); ?></td>
									<td><?php echo $exchange_to_product['qty']; ?></td>
									<td><?php echo ced_rnx_format_price( $pro_price ); ?></td>
								</tr>
								<tr>
									<th colspan="2"><strong><?php _e( 'Subject', 'woocommerce-refund-and-exchange' ); ?> : </strong><i> <?php echo $exchange_to_product['subject']; ?></i></th>
									<th colspan="5"><strong><?php _e( 'Reason', 'woocommerce-refund-and-exchange' ); ?> : </strong><i><?php echo $exchange_to_product['reason']; ?></i></th>
								</tr>
								<p><b><?php _e( 'Attachment_', 'woocommerce-refund-and-exchange' ); echo esc_html( $product->get_sku() ) ?> :</b>
								<?php 
									$count = 1;
									foreach ( $exchange_to_product['files'] as $attachment ) {
										if ( $attachment != $order_id . '-' ) {
											?>
											<a href="<?php echo home_url(); ?>/wp-content/uploads/Exchange_Images/<?php echo $attachment; ?>" target="_blank"><?php _e( 'Attachment', 'woocommerce-refund-and-exchange' ); ?>-<?php echo $count; ?></a>
											<?php
											$count++;
										}
									}
									?></p>
									<?php
								
							}
						}	
						?>
							<tr>
								<th colspan="4"><?php _e( 'Total', 'woocommerce-refund-and-exchange' ); ?></th>
								<th><?php echo ced_rnx_format_price( $total_price ); ?></th>
							</tr>
						</tbody>
					</table>
					
				</div> -->

				<div class="ced_rnx_extra_reason ced_rnx_extra_reason_for_exchange">
				<?php
					$fee_enable = get_option( 'ced_rnx_exchange_shipcost_enable', false );
				
					$mwb_cpn_used = get_post_meta( $order_id, 'mwb_rnx_status_exchanged', true );
				if ( $mwb_cpn_used ) {
					$mwb_dis_tot = $mwb_cpn_used;
				} else {
					$mwb_cpn_dis = $order->get_discount_total();
					$mwb_cpn_tax = $order->get_discount_tax();
					$mwb_dis_tot = $mwb_cpn_dis + $mwb_cpn_tax;
				}
					$mwb_dis_tot = 0;
				if ( $total_price - ( $selected_total_price + $mwb_dis_tot ) > 0 ) {
					?>
						<!-- <p><strong><?php _e( 'Extra Amount Paid', 'woocommerce-refund-and-exchange' ); ?> : <?php echo ced_rnx_format_price( $total_price - ( $selected_total_price + $mwb_dis_tot ) ); ?></strong></p> -->
					<?php
				} else {
					if ( $mwb_dis_tot > $total_price ) {
						$total_price = 0;
					} else {
						$total_price = $total_price - $mwb_dis_tot;
					}
					?>
						<!-- <p><strong><i class="ced_rnx_left_amu"><?php _e( 'Left Amount After Exchange', 'woocommerce-refund-and-exchange' ); ?></i> : <?php echo ced_rnx_format_price( $selected_total_price - $total_price ); ?></strong> -->
						<!-- <input type="hidden" name="ced_rnx_left_amount_for_refund" class="ced_rnx_left_amount_for_refund" value="<?php echo( $selected_total_price - $total_price ); ?>"> -->
						<!-- </p> -->
						<?php
				}
				?>
					
					<?php
					$order_status = 'wc-' . $order->get_status();
					if ( $exchange_status == 'pending' && $order_status == 'wc-exchange-request') {
						?>
						<p>
							<input type="button" value="Exchange Reverse Approve" class="button" id="ced_rnx_accept_exchange_request" data-orderid="<?php echo $order_id; ?>" data-date="<?php echo $date; ?>">
							<input type="button" value="Cancel Request" class="button" id="ced_rnx_cancel_exchange" data-orderid="<?php echo $order_id; ?>" data-date="<?php echo $date; ?>">
						</p>
						<?php
					}
					if($order_status == 'wc-exchange_received'){ ?>
						<p>
							<input type="button" value="Exchange Approve" class="button" id="ced_rnx_accept_exchange" data-orderid="<?php echo $order_id; ?>" data-date="<?php echo $date; ?>">
							<input type="button" value="Exchange Reject" class="button" id="ced_rnx_reject_exchange" data-orderid="<?php echo $order_id; ?>" data-date="<?php echo $date; ?>">
						</p>

					<?php
						}

					?>
						
					</div>
					<div class="ced_rnx_exchange_loader">
						<img src="<?php echo home_url(); ?>/wp-admin/images/spinner-2x.gif">
					</div>
				</div>	
			</div>
			<p>
			<?php
			if ( $exchange_detail['status'] == 'complete' ) {
				$left_amount = get_post_meta( $order_id, 'ced_rnx_left_amount', true );
				$ced_refunded = get_post_meta( $order_id, 'ced_rnx_exchange_approve_refunded', true );
				if ( isset( $left_amount ) && $left_amount != null && $left_amount > 0 ) {

					?>
					<p class="exchange_msg"><strong>
					<?php
					_e( 'Refunddable Amount of this order is ', 'woocommerce-refund-and-exchange' );
						echo $left_amount . '. ';
					?>
					</strong><input type="button" name="ced_rnx_left_amount" class="button button-primary" id="ced_rnx_left_amount" data-orderid="<?php echo $order_id; ?>" Value="<?php _e( 'Refund Amount', 'woocommerce-refund-and-exchange' ); ?>" ></p>
					<input type="hidden" name="left_amount" id="left_amount" value="<?php echo $left_amount; ?>">
					<?php

				}
				$approve_date = date_create( $exchange_detail['approve'] );
				$date_format = get_option( 'date_format' );
				$approve_date = date_format( $approve_date, $date_format );

				if($order_status == 'wc-exchange-approved'){
					$approve_pickup = date_create( $exchange_detail['approve_pickup'] );
					$approve_date_pickup = date_format( $approve_pickup, $date_format );
				}
				
				_e( 'Above product exchange reverse pickup request is approved on', 'woocommerce-refund-and-exchange' );
				?>
				 <b><?php echo $approve_date; ?>.</b>
				<?php
				$exhanged_order_id = get_post_meta( $order_id, "date-$date", true );
				?>
				</p>
				<?php 
				if($order_status == 'wc-exchange-approved'){
					_e( 'Above product exchange request is approved on', 'woocommerce-refund-and-exchange' );
					}
				?> <b><?php if($order_status == 'wc-exchange-approved'){
					echo $approve_date_pickup; 
				}
			?>.</b>
				<!-- <p><?php _e( 'A new order is generated for your exchange request.', 'woocommerce-refund-and-exchange' ); ?> -->
				<!-- <a href="<?php echo home_url( "wp-admin/post.php?post=$exhanged_order_id&action=edit" ); ?>">Order #<?php echo $exhanged_order_id; ?></a> -->
				<?php
				$ced_rnx_manage_stock_for_exchange = get_post_meta( $order_id, 'ced_rnx_manage_stock_for_exchange', true );
				if ( $ced_rnx_manage_stock_for_exchange == '' ) {
					$ced_rnx_manage_stock_for_exchange = 'yes';
				}
				// $manage_stock = get_option( 'ced_rnx_exchange_request_manage_stock' );
				if ( $manage_stock == 'yes' && $ced_rnx_manage_stock_for_exchange == 'yes' ) {
					?>
					<div><?php _e( 'When Product Back in stock then for stock management click on ', 'woocommerce-refund-and-exchange' ); ?> <input type="button" name="ced_rnx_stock_back" class="button button-primary" id="ced_rnx_stock_back" data-type="ced_rnx_exchange" data-orderid="<?php echo $order_id; ?>" Value="Manage Stock" ></div> 
					<?php
				}
			}
			if ( $exchange_detail['status'] == 'cancel' ) {
				$approve_date = date_create( $exchange_detail['cancel_date'] );
				$approve_date = date_format( $approve_date, 'F d, Y' );
				?>
				</p><p>
				<?php
				_e( 'The above product exchange request is cancelled on  ', 'woocommerce-refund-and-exchange' );
				?>
				<b><?php echo $approve_date; ?>.</b>
				<?php
			}
			if ( $exchange_detail['status'] == 'reject' ) {
				$rejected_date = date_create( $exchange_detail['reject_date'] );
				$rejected_date = date_format( $rejected_date, 'F d, Y' );

				_e( 'Above product exchange request is Rejected on', 'woocommerce-refund-and-exchange' );
				?>
				 <b><?php echo $rejected_date; ?>.</b>
				 <?php
			}
			?>
			</p>
			<hr/>
			<?php
		// }
	}
} else {
	$ced_rnx_pages = get_option( 'ced_rnx_pages' );
	$page_id = $ced_rnx_pages['pages']['ced_exchange_from'];
	$exchange_url = get_permalink( $page_id );
	$order_id = $order->get_id();
	$ced_rnx_exchange_url = add_query_arg( 'order_id', $order_id, $exchange_url );
	?>
<p><?php _e( 'No request from customer', 'woocommerce-refund-and-exchange' ); ?></p>
<a target="_blank" href="<?php echo $ced_rnx_exchange_url; ?>" class="button-primary button"><b><?php _e( 'Initiate Exchange Request', 'woocommerce-refund-and-exchange' ); ?></b></a>
	<?php
}
?>
