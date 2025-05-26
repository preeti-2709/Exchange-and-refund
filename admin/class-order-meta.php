<?php
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ced_refund_and_exchange_order_meta' ) ) {

	/**
	 * This class for managing admin interfaces for woocommerce order.
	 *
	 * @name    Ced_refund_and_exchange_order_meta
	 * @category Class
	 * @author wpswings<webmaster@wpswings.com>
	 */

	class Ced_refund_and_exchange_order_meta {

		/**
		 * This function is construct of class
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function __construct() {

			add_filter( 'admin_enqueue_scripts', array( $this, 'ced_rnx_admin_scripts' ) );
			add_action( 'wp_ajax_ced_rnx_register_license', array( $this, 'ced_rnx_register_license' ) );

			$ced_rnx_license_hash = get_option( 'ced_rnx_license_hash' );
			$ced_rnx_license_key = get_option( 'ced_rnx_license_key' );
			$ced_rnx_license_plugin = get_option( 'ced_rnx_plugin_name' );
			$ced_rnx_hash = md5( $_SERVER['HTTP_HOST'] . $ced_rnx_license_plugin . $ced_rnx_license_key );
			$ced_rnx_activation_date = get_option( 'ced_rnx_activation_date', false );
			$ced_rnx_after_month = strtotime( '+1200 days', $ced_rnx_activation_date );
			$ced_rnx_currenttime = current_time( 'timestamp' );
			$ced_rnx_time_difference = $ced_rnx_after_month - $ced_rnx_currenttime;
			$ced_rnx_days_left = floor( $ced_rnx_time_difference / ( 60 * 60 * 24 ) );
			if (  1200 >= 0 ) {
				add_action( 'admin_menu', array( $this, 'ced_rnx_product_return_meta_box' ) );

				// Return Request Hooks and filter
				add_action( 'wp_ajax_ced_return_fee_add', array( $this, 'ced_rnx_return_fee_add_callback' ) );
				add_action( 'wp_ajax_nopriv_ced_return_fee_add', array( $this, 'ced_rnx_return_fee_add_callback' ) );
				add_action( 'wp_ajax_ced_return_req_approve', array( $this, 'ced_rnx_return_req_approve_callback' ) );
				add_action( 'wp_ajax_nopriv_ced_return_req_approve', array( $this, 'ced_rnx_return_req_approve_callback' ) );
				add_action( 'wp_ajax_ced_return_req_cancel', array( $this, 'ced_rnx_return_req_cancel_callback' ) );
				add_action( 'wp_ajax_nopriv_ced_return_req_cancel', array( $this, 'ced_rnx_return_req_cancel_callback' ) );

				// Exchange Request Hooks and filter
				add_action( 'wp_ajax_ced_exchange_fee_add', array( $this, 'ced_rnx_exchange_fee_add_callback' ) );
				add_action( 'wp_ajax_nopriv_ced_exchange_fee_add', array( $this, 'ced_rnx_exchange_fee_add_callback' ) );
				add_action( 'woocommerce_refund_created', array( $this, 'ced_rnx_action_woocommerce_order_refunded' ), 10, 2 );
				add_action( 'wp_ajax_ced_exchange_req_approve_refund', array( $this, 'ced_exchange_req_approve_refund' ) );
				add_action( 'wp_ajax_ced_exchange_req_approve', array( $this, 'ced_exchange_req_approve_callback' ) );
				add_action( 'wp_ajax_ced_exchange_req_approve_first_level', array( $this, 'ced_exchange_req_approve_first_level' ) ); //18-11-22 new button details add
				add_action( 'wp_ajax_nopriv_exchange_req_approve_first_level', array( $this, 'ced_exchange_req_approve_first_level' ) ); //18-11-22 new button details add

				add_action( 'wp_ajax_nopriv_ced_exchange_req_approve', array( $this, 'ced_exchange_req_approve_callback' ) );
				add_action( 'wp_ajax_ced_exchange_req_cancel', array( $this, 'ced_rnx_exchange_req_cancel_callback' ) );
				add_action( 'wp_ajax_nopriv_ced_exchange_req_cancel', array( $this, 'ced_rnx_exchange_req_cancel_callback' ) );
				// new code add exchange rejected - 21-11-22-aarti
				add_action( 'wp_ajax_ced_exchange_req_rejected', array( $this, 'ced_rnx_exchange_req_reject_callback' ) );
				add_action( 'wp_ajax_nopriv_ced_exchange_req_rejected', array( $this, 'ced_rnx_exchange_req_reject_callback' ) );

				add_action( 'woocommerce_admin_order_items_after_fees', array( $this, 'ced_rnx_show_order_exchange_product' ) );
				add_filter( 'woocommerce_order_number', array( $this, 'ced_rnx_update_order_number_callback' ) );
				add_filter( 'woocommerce_valid_order_statuses_for_payment', array( $this, 'ced_rnx_order_need_payment' ) );
				add_filter( 'woocommerce_valid_order_statuses_for_cancel', array( $this, 'ced_rnx_order_can_cancel' ) );

				add_action( 'woocommerce_order_status_changed', array( $this, 'ced_rnx_woocommerce_order_status_changed' ), 10, 3 );
				add_action( 'wp_ajax_ced_rnx_coupon_regenertor', array( $this, 'ced_rnx_coupon_regenertor' ), 10 );
				add_action( 'wp_ajax_nopriv_ced_rnx_coupon_regenertor', array( $this, 'ced_rnx_coupon_regenertor' ), 10 );
				add_action( 'wp_ajax_ced_rnx_generate_user_wallet_code', array( $this, 'ced_rnx_generate_user_wallet_code' ), 10 );
				add_action( 'wp_ajax_nopriv_ced_rnx_generate_user_wallet_code', array( $this, 'ced_rnx_generate_user_wallet_code' ), 10 );
				add_action( 'wp_ajax_ced_rnx_change_customer_wallet_amount', array( $this, 'ced_rnx_change_customer_wallet_amount' ), 10 );
				add_action( 'wp_ajax_nopriv_ced_rnx_change_customer_wallet_amount', array( $this, 'ced_rnx_change_customer_wallet_amount' ), 10 );
				add_action( 'wp_ajax_ced_rnx_catalog_count', array( $this, 'ced_rnx_catalog_count' ), 10 );
				add_action( 'wp_ajax_ced_rnx_catalog_delete', array( $this, 'ced_rnx_catalog_delete' ), 10 );
				add_action( 'wp_ajax_ced_rnx_cancel_customer_order', array( $this, 'ced_rnx_cancel_customer_order' ), 10 );
				add_action( 'wp_ajax_nopriv_ced_rnx_cancel_customer_order', array( $this, 'ced_rnx_cancel_customer_order' ), 10 );
				add_action( 'wp_ajax_ced_rnx_cancel_customer_order_products', array( $this, 'ced_rnx_cancel_customer_order_products' ), 10 );
				add_action( 'wp_ajax_nopriv_ced_rnx_cancel_customer_order_products', array( $this, 'ced_rnx_cancel_customer_order_products' ), 10 );
				add_action( 'wp_ajax_ced_rnx_manage_stock', array( $this, 'ced_rnx_manage_stock' ) );
				add_action( 'wp_ajax_ced_rnx_refund_price', array( $this, 'ced_rnx_refund_price' ) );
				add_action( 'wp_ajax_ced_rnx_order_messages_save', array( $this, 'ced_rnx_order_messages_save' ) );
				add_action( 'wp_ajax_nopriv_ced_rnx_order_messages_save', array( $this, 'ced_rnx_order_messages_save' ) );

				add_action( 'wp_ajax_ced_warranty_req_approve_first_level', array( $this, 'ced_warranty_req_approve_first_level' ) ); //18-11-22 new button details add
			    add_action( 'wp_ajax_nopriv_ced_warranty_req_approve_first_level', array( $this, 'ced_warranty_req_approve_first_level' ) ); //18-11-22 new button details add

			    add_action( 'wp_ajax_ced_warranty_req_approve', array( $this, 'ced_rnx_warranty_req_approve_callback' ) );
			    add_action( 'wp_ajax_nopriv_ced_warranty_req_approve', array( $this, 'ced_rnx_warranty_req_approve_callback' ) ); //30-11-22 new button details add
			    add_action( 'wp_ajax_ced_warranty_rejected', array( $this, 'ced_rnx_warranty_rejected' ) );
			    add_action( 'wp_ajax_nopriv_ced_warranty_rejected', array( $this, 'ced_rnx_warranty_rejected' ) ); //30-11-22 warranty reject

			    add_action( 'wp_ajax_ced_rnx_session_expire_exchange', array( $this, 'ced_rnx_session_expire_exchange' ) );
			    add_action( 'wp_ajax_nopriv_ced_rnx_session_expire_exchange', array( $this, 'ced_rnx_session_expire_exchange' ) ); //30-11-22 warranty reject

			}
		}
		// exchange product session expire
		public function ced_rnx_session_expire_exchange(){
			$inactive = 3600; 
			$expire_time = WC()->session->get( 'expire_time' );
			$session_life = time() - $expire_time;
			// if (isset($expire_time) && ($session_life > $inactive)) {
				WC()->session->__unset( 'expire_time' );
				WC()->session->__unset( 'exchange_session_start' );
				return '1';
			// }else{
			// 	return '0';
			// }
		}
		//create token in shiprocket rest api
		public function Authorization_ShiprockAPI(){
			global $wpdb;
			$table = 'shiprock_restapi_token';
		    $query = $wpdb->get_results("SELECT * FROM $table WHERE status = '1' order by id desc limit 1");
			if(!empty($query)){
				$resultat = $query['0'];
				$today = date("Y-m-d",strtotime($resultat->created_date)); //Today
				$date = date("Y-m-d"); //Date

				$diff = strtotime($today) - strtotime($date);
				$difference =  abs(round($diff / 86400));

				if ($difference == 9) {
				    $POSTFIELDS = array();
					$POSTFIELDS['email'] = 'rishabh.gupta@studds.com';
					$POSTFIELDS['password'] = 'Studds@123';
					$POSTFIELDS = json_encode($POSTFIELDS);

				 	$ch = curl_init();
				    curl_setopt($ch, CURLOPT_URL, "https://apiv2.shiprocket.in/v1/external/auth/login");
				    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				    curl_setopt($ch, CURLOPT_ENCODING , '');
				    curl_setopt($ch, CURLOPT_MAXREDIRS , 10);
				    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
				    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
				    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				    curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);	    		    
				    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				   		'Content-Type: application/json'
				   	));
				   	$contents = curl_exec($ch);
				    curl_close($ch);
					$token_response = json_decode($contents,true);
					if(!empty($token_response['token']) && !empty($query)){
						$token = $token_response['token'];
						$datesent = date('Y-m-d H:i:s'); //string value use: %s
						$id = $resultat->id; 
						$sql = $wpdb->prepare("UPDATE $table SET token = %s, modify_date = %s where id = %s", $token,$datesent,$id);
						$wpdb->query($sql);
					}
				}
				return $resultat->token;
			}else{
				$POSTFIELDS = array();
				$POSTFIELDS['email'] = 'rishabh.gupta@studds.com';
				$POSTFIELDS['password'] = 'Studds@123';
				$POSTFIELDS = json_encode($POSTFIELDS);

				$ch = curl_init();
			    curl_setopt($ch, CURLOPT_URL, "https://apiv2.shiprocket.in/v1/external/auth/login");
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($ch, CURLOPT_ENCODING , '');
			    curl_setopt($ch, CURLOPT_MAXREDIRS , 10);
			    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
			    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			    curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);	    		    
			    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			   		'Content-Type: application/json'
			   	));
			   	$contents = curl_exec($ch);
			    curl_close($ch);
				$token_response = json_decode($contents,true);
				if(!empty($token_response['token']) && empty($query)){
					$status = '1';
					$company_id = $token_response['company_id'];
					$email = $token_response['email'];
					$token = $token_response['token'];
					$created_at = date('Y-m-d H:i:s');
					$datesent = date('Y-m-d H:i:s'); //string value use: %s

					$sql = $wpdb->prepare("INSERT INTO `$table` (`token`, `status`, `company_id`, `email`, `created_date`, `modify_date`) values (%s, %s, %d, %s, %s, %s)", $token, $status, $company_id, $email, $created_at, $datesent);
					$wpdb->query($sql);
				}
				return $token_response['token'];
			}
		}
		
		function return_shiprock_api($orderid,$exchanged_products){
			$token = $this->Authorization_ShiprockAPI();

			$dataraw = array();
			//pickup details
			$order_date = date('Y-m-d H:i:s');
			$channel_id  = '2933586';
			$dataraw['order_id'] = $orderid;
			$dataraw['order_date'] = $order_date;
			$dataraw['channel_id'] = $channel_id;

			$shipping_first_name = get_post_meta( $orderid, '_shipping_first_name', true );
			$shipping_last_name = get_post_meta( $orderid, '_shipping_last_name', true );
			$shipping_company = get_post_meta( $orderid, '_shipping_company', true );
			$shipping_address_1 = get_post_meta( $orderid, '_shipping_address_1', true );
			$shipping_address_2 = get_post_meta( $orderid, '_shipping_address_2', true );
			$shipping_city = get_post_meta( $orderid, '_shipping_city', true );
			$shipping_state = get_post_meta( $orderid, '_shipping_state', true );
			$shipping_phone = get_post_meta( $orderid, '_shipping_phone', true );
			$shipping_email = get_post_meta( $orderid, '_shipping_email', true );
			$shipping_country = get_post_meta( $orderid, '_shipping_country', true );
			$shipping_postcode = get_post_meta( $orderid, '_shipping_postcode', true );
			$payment_method_tittle = get_post_meta( $orderid, '_payment_method_title', true );

			$dataraw['pickup_customer_name'] = $shipping_first_name;
			$dataraw['pickup_last_name'] = $shipping_last_name;
			$dataraw['company_name'] = $shipping_company;
			$dataraw['pickup_address'] = $shipping_address_1;
			$dataraw['pickup_address_2'] = $shipping_address_2;
			$dataraw['pickup_city'] = $shipping_city;
			$dataraw['pickup_state'] = $shipping_state;
			$dataraw['pickup_country'] = $shipping_country;
			$dataraw['pickup_pincode'] = $shipping_postcode;
			$dataraw['pickup_email'] = $shipping_email;
			$dataraw['pickup_phone'] = $shipping_phone;
			$dataraw['pickup_isd_code'] = '';

			//shipping details
			$phone = '9971845558';
			$email = 'deepak.grover@studds.com';
			$address = 'Studds Accessories Limited, Plot no 918,Sector 68, IMT,India,121004,Haryana';

			$dataraw['shipping_customer_name'] = 'Studds';
			$dataraw['shipping_last_name'] = 'Accessories Limited';
			$dataraw['shipping_address'] = 'Studds Accessories Limited, Plot no 918';
			$dataraw['shipping_address_2'] = 'Sector 68, IMT';
			$dataraw['shipping_city'] = 'Faridabad';
			$dataraw['shipping_country'] = 'India';
			$dataraw['shipping_pincode'] = '121004';
			$dataraw['shipping_state'] = 'Haryana';
			$dataraw['shipping_email'] = $email;
			$dataraw['shipping_isd_code'] = '';
			$dataraw['shipping_phone'] = $phone;

			//payment details
			$dataraw['payment_method'] = $payment_method_tittle;
			$i = 0;
			if ( isset( $exchanged_products ) && ! empty( $exchanged_products ) ) {
			    foreach ( $exchanged_products as $exchanged_product ) {
			        if ( isset( $exchanged_product['variation_id'] ) ) {
			            $product = wc_get_product( $exchanged_product['variation_id'] );
			            $price_exchange = $product->get_price();
			        } elseif ( isset( $exchanged_product['id'] ) ) {
			            $product = wc_get_product( $exchanged_product['id'] );
			            $price_exchange = $product->get_price();
			        }
			        $subtotal  = $price_exchange * $exchanged_product['qty'];
			        $total  = $price_exchange * $exchanged_product['qty'];

			        $sku = $product->get_sku();
			        $dataraw['order_items'][$i]['sku'] = $sku;
			        $dataraw['order_items'][$i]['name'] = $product->get_name();
			        $dataraw['order_items'][$i]['units'] = $exchanged_product['qty'];
			        $dataraw['order_items'][$i]['selling_price'] = $product->get_price();
			        $dataraw['order_items'][$i]['discount'] = 0;
			        $dataraw['order_items'][$i]['qc_enable'] = false;
			        $dataraw['order_items'][$i]['hsn'] = '';
			        $dataraw['order_items'][$i]['brand'] = '';

			        $attributes = $product->get_attributes();
			        $dataraw['order_items'][$i]['qc_size'] = $attributes['pa_size'];

			        $dataraw['total_discount'] = $subtotal;
			        $dataraw['sub_total'] = $total;
			        $dataraw['length'] = $product->get_length();
			        $dataraw['breadth'] = '';
			        $dataraw['height'] = $product->get_height();
			        $dataraw['weight'] = $product->get_width();
			        $i++;
			        // $dataraw['return_reason'] = 'Quality not as expected';
			    }
			}	
			$newdata = json_encode($dataraw);
			$ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, "https://apiv2.shiprocket.in/v1/external/orders/create/return");
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch, CURLOPT_ENCODING , '');
		    curl_setopt($ch, CURLOPT_MAXREDIRS , 10);
		    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $newdata);	    		    
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		   		'Content-Type: application/json',
		   		'Authorization: Bearer ' . $token
		   	));
		    $contents = curl_exec($ch);
		    curl_close($ch);
		    // $return_response = '{"order_id":288251485,"shipment_id":287643450,"status":"RETURN PENDING","status_code":21,"company_name":"Studds Accessories Limited"}';
		    $return_response = json_decode($contents,true);
		    $shiprocket_order_id = $return_response['order_id'];
		    $shipment_id = $return_response['shipment_id'];
		    $status = $return_response['status'];
		    $status_code = $return_response['status_code'];
		    $company_name = $return_response['company_name'];
		    $message = $return_response['message'];

		    global $wpdb;
		    $sql = $wpdb->prepare("INSERT INTO `shiprocket_return_api` (`order_id`, `order_date`, `channel_id`, `customer_name`,`customer_email`,`customer_phone`,`shipping_address`,`shipping_email`,`shipping_phone`,`payment_method`,`sku`,`shiprocket_order_id`,`shipment_id`,`status`,`status_code`,`company_name`,`message`,`created_date`, `modify_date`) values (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)", $orderid, $order_date, $channel_id, $shipping_first_name,$shipping_email,$shipping_phone,$address,$email,$phone,$payment_method_tittle,$sku,$shiprocket_order_id,$shipment_id,$status,$status_code,$company_name,$message,$order_date,$order_date);
		    $wpdb->query($sql);
		   	return $contents;
		}
		public function ced_rnx_refund_price() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				// Wallet for customer.
				$order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_id'] ) ) : 0;
				$order = wc_get_order( $order_id );
				$refund_amount = isset( $_POST['refund_amount'] ) ? sanitize_text_field( wp_unslash( $_POST['refund_amount'] ) ) : 0;
				$wallet_enable = get_option( 'ced_rnx_return_wallet_enable', 'no' );
				$ced_rnx_select_refund_method_enable = get_option( 'ced_rnx_select_refund_method_enable', 'no' );
				$ced_rnx_refund_method = '';
				$ced_rnx_refund_method = get_post_meta( $order_id, 'ced_rnx_refund_method', true );
				$response['refund_method'] = '';
				if ( $ced_rnx_refund_method != '' ) {
					$response['refund_method'] = $ced_rnx_refund_method;
				}
				if ( $wallet_enable == 'yes' && $ced_rnx_select_refund_method_enable == 'yes' && $ced_rnx_refund_method == 'manual_method' ) {
					echo json_encode( $response );
					wp_die();
				} elseif ( $wallet_enable == 'yes' ) {

					$customer_id = ( $value = get_post_meta( $order_id, '_customer_user', true ) ) ? absint( $value ) : '';
					if ( $customer_id > 0 ) {
						$walletcoupon = get_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', true );
						if ( empty( $walletcoupon ) ) {
							$coupon_code = ced_rnx_coupon_generator( 5 ); // Code
							$amount = $refund_amount; // Amount

							$discount_type = 'fixed_cart';
							$coupon_description = "REFUND ACCEPTED - ORDER #$order_id";

							$coupon = array(
								'post_title' => $coupon_code,
								'post_content' => $coupon_description,
								'post_excerpt' => $coupon_description,
								'post_status' => 'publish',
								'post_author' => get_current_user_id(),
								'post_type'     => 'shop_coupon',
							);

							$new_coupon_id = wp_insert_post( $coupon );
							$discount_type = 'fixed_cart';
							update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
							update_post_meta( $new_coupon_id, 'rnxwallet', true );
							update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $coupon_code );
							update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
						} else {
							$the_coupon = new WC_Coupon( $walletcoupon );
							$coupon_id = $the_coupon->get_id();
							if ( isset( $coupon_id ) ) {
								$amount = get_post_meta( $coupon_id, 'coupon_amount', true );
								$remaining_amount = $amount + $refund_amount;
								update_post_meta( $coupon_id, 'coupon_amount', $remaining_amount );
								update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $walletcoupon );
								update_post_meta( $coupon_id, 'rnxwallet', true );
							}
						}
					}
					// Add the note.
					$order           = wc_get_order( $order_id );
					$today           = date( 'F j, Y' );
					$timezone_format = 'h:i a';
					$time            = date_i18n( $timezone_format );
					$mess            = esc_html( '#' . $order_id . ' Refund  - ' . get_woocommerce_currency_symbol() . $refund_amount . ' ' . $today . ', ' . $time . ' by admin in wallet' );
					$refund = wc_create_refund(
						array(
							'amount' => $refund_amount,
							'reason' => $mess,
							'order_id' => $order_id,
							'refund_payment' => false,
						)
					);
					$order->save();
					$order->calculate_totals();
					update_post_meta( $order_id, 'refundable_amount', '0' );
				}
				update_post_meta( $order_id, 'ced_rnx_refund_approve_refunded', 'yes' );
				echo json_encode( $response );
				wp_die();
			}
		}

		/**
		 * Manage stock when product is actually back in stock.
		 *
		 * @name ced_rnx_manage_stock
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		public function ced_rnx_manage_stock() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_id'] ) ) : '';
				;
				if ( $order_id > 0 ) {
					$ced_rnx_type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
					;

					if ( $ced_rnx_type != '' ) {
						if ( $ced_rnx_type == 'ced_rnx_return' ) {
							$manage_stock = get_option( 'ced_rnx_return_request_manage_stock' );
							if ( $manage_stock == 'yes' ) {
								$ced_rnx_return_data = get_post_meta( $order_id, 'ced_rnx_return_product', true );
								if ( is_array( $ced_rnx_return_data ) && ! empty( $ced_rnx_return_data ) ) {
									foreach ( $ced_rnx_return_data as $date => $requested_data ) {
										$ced_rnx_returned_products = $requested_data['products'];
										if ( is_array( $ced_rnx_returned_products ) && ! empty( $ced_rnx_returned_products ) ) {
											foreach ( $ced_rnx_returned_products as $key => $product_data ) {
												if ( $product_data['variation_id'] > 0 ) {
													$product = wc_get_product( $product_data['variation_id'] );
												} else {
													$product = wc_get_product( $product_data['product_id'] );
												}
												if ( $product->managing_stock() ) {
													$avaliable_qty = $product_data['qty'];
													if ( WC()->version < '3.0.0' ) {
														$product->set_stock( $avaliable_qty, 'add' );
													} else {
														if ( $product_data['variation_id'] > 0 ) {
															$total_stock = get_post_meta( $product_data['variation_id'], '_stock', true );
															$total_stock = $total_stock + $avaliable_qty;
															wc_update_product_stock( $product_data['variation_id'], $total_stock, 'set' );
														} else {
															$total_stock = get_post_meta( $product_data['product_id'], '_stock', true );
															$total_stock = $total_stock + $avaliable_qty;
															wc_update_product_stock( $product_data['product_id'], $total_stock, 'set' );
														}
													}
													update_post_meta( $order_id, 'ced_rnx_manage_stock_for_return', 'no' );
													$response['result'] = 'success';
													$response['msg'] = __( 'Product Stock is updated Successfully.', 'woocommerce-refund-and-exchange' );
												} else {
													$response['result'] = false;
													$response['msg'] = __( 'Product Stock is not updated as manage stock setting of product is disable.', 'woocommerce-refund-and-exchange' );
												}
											}
										}
									}
								}
							}
						} else {
							$manage_stock = get_option( 'ced_rnx_exchange_request_manage_stock' );
							if ( $manage_stock == 'yes' ) {
								$ced_rnx_exchange_deta = get_post_meta( $order_id, 'ced_rnx_exchange_product', true );
								if ( is_array( $ced_rnx_exchange_deta ) && ! empty( $ced_rnx_exchange_deta ) ) {
									foreach ( $ced_rnx_exchange_deta as $date => $requested_data ) {
										$ced_rnx_exchanged_products = $requested_data['from'];
										if ( is_array( $ced_rnx_exchanged_products ) && ! empty( $ced_rnx_exchanged_products ) ) {
											foreach ( $ced_rnx_exchanged_products as $key => $product_data ) {
												if ( $product_data['variation_id'] > 0 ) {
													$product = wc_get_product( $product_data['variation_id'] );
												} else {
													$product = wc_get_product( $product_data['product_id'] );
												}
												if ( $product->managing_stock() ) {
													$avaliable_qty = $product_data['qty'];
													if ( WC()->version < '3.0.0' ) {
														$product->set_stock( $avaliable_qty, 'add' );
													} else {
														if ( $product_data['variation_id'] > 0 ) {
															$total_stock = get_post_meta( $product_data['variation_id'], '_stock', true );
															$total_stock = $total_stock + $avaliable_qty;
															wc_update_product_stock( $product_data['variation_id'], $total_stock, 'set' );
														} else {
															$total_stock = get_post_meta( $product_data['product_id'], '_stock', true );
															$total_stock = $total_stock + $avaliable_qty;
															wc_update_product_stock( $product_data['product_id'], $total_stock, 'set' );
														}
													}
													update_post_meta( $order_id, 'ced_rnx_manage_stock_for_exchange', 'no' );
													$response['result'] = true;
													$response['msg'] = __( 'Product Stock is updated Successfully.', 'woocommerce-refund-and-exchange' );
												} else {
													$response['result'] = false;
													$response['msg'] = __( 'Product Stock is not updated as manage stock setting of product is disable.', 'woocommerce-refund-and-exchange' );
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
			echo json_encode( $response );
			wp_die();
		}

		/**
		 * update left amount becuse amount is refunded.
		 *
		 * @name ced_rnx_action_woocommerce_order_refunded
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		public function ced_rnx_action_woocommerce_order_refunded( $order_get_id, $refund_get_id ) {
			update_post_meta( $refund_get_id['order_id'], 'ced_rnx_left_amount', '0' );
			update_post_meta( $refund_get_id['order_id'], 'refundable_amount', '0' );
			
		}

		/**
		 * Cancel order and manage stock of cancelled product.
		 *
		 * @name ced_rnx_cancel_customer_order
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function ced_rnx_cancel_customer_order() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$order_id = $_POST['order_id'];

				$the_order = wc_get_order( $order_id );

				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
				$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );
				$subject = '#' . $order_id . __( ' order cancelled by customer', 'woocommerce-refund-and-exchange' );

				$message = __( 'Order is canceled by customer and current order status goes in canceled.', 'woocommerce-refund-and-exchange' );
				$html_content = '<html>
										<head>
											<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
											<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
										</head>
										<body>
											<table cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td style="text-align: center; margin-top: 30px; margin-bottom: 10px; color: #99B1D8; font-size: 12px;">
														' . $mail_header . '
													</td>
												</tr>
												<tr>
													<td>
														<table align="center" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-family:Open Sans; max-width: 600px; width: 100%;">
															<tr>
																<td style="padding: 36px 48px; width: 100%; background-color:#557DA1;color: #fff; font-size: 30px; font-weight: 300; font-family:helvetica;">' . $subject . '</td>
															</tr>
															<tr>
																<td style="width:100%; padding: 36px 48px 10px; background-color:#fdfdfd; font-size: 14px; color: #737373;">' . $message . '</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td style="text-align: center; margin-top: 30px; color: #99B1D8; font-size: 12px;">
														' . $mail_footer . '
													</td>
												</tr>
											</table>
										</body>
									</html>';
				$headers = array();
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$to = get_option( 'ced_rnx_notification_from_mail' );
				wc_mail( $to, $subject, $html_content, $headers );

				$endpoints = get_option( 'woocommerce_myaccount_orders_endpoint', 'orders' );

				$url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
				$url .= "$endpoints";
				$success    = __( 'Your order is cancelled', 'woocommerce-refund-and-exchange' );
				$the_order->cancel_order( __( 'Order canceled by customer.', 'woocommerce-refund-and-exchange' ) );

				$notice     = wc_add_notice( $success );
				echo $url;
				wp_die();
			}
		}
		/* easebuzz refund api logs capture */
			public function Refund_Request_Api($refund_cancel){
				if ( isset( $refund_cancel ) ){
					$key = 'RWN6LH487F';
					$salt = 'YTYT4L3QWX';
				    $txnid = $refund_cancel['txid'];
				    $refund_cancel['total_item_qty1']; //Total Quantity - 10
				    //echo "<br>";
				    $refund_cancel['item_qty1']; //Item Quantity - 2 
				    //echo "<br>";
				    $refund_cancel['refund_shippingamount']; //Shipping Amount - 10
				    //echo "<br>";
				    $item_shipping = $refund_cancel['refund_shippingamount'] / $refund_cancel['total_item_qty1']; //Shipping Amount - 10 / Total Quantity - 10
				    //echo "<br>";
                    $refund_item_shipping = $item_shipping * $refund_cancel['item_qty1']; //Individual Item Shipping - 1 * //Item Quantity - 2
                    //echo "<br>";
				    $amount1 = $refund_cancel['amount']; //Total Amount - 10 + Shipping Amount 10
				    //echo "<br>";
				    //$amount1 = 2;
                    $amount = number_format($amount1, 1, '.', '');
                    $refund_amount1 = $refund_cancel['refund_amount']+$refund_item_shipping; //
				    //$refund_amount1 = 1;
				    //echo "<br>";
				    $refund_amount = number_format($refund_amount1, 1, '.', '');
				    $email = $refund_cancel['email'];
				    $phone = $refund_cancel['phone'];
				    // $country_code = '+91';
        //                 $phone_no = '+918433478971';
        //             $phone = preg_replace('/^\+?91|\|91|\D/', '', ($phone_no));
				    
				    // print_r($refund_cancel);
				    // echo "<br>";

				    $checkhash = hash('sha512', "$key|$txnid|$amount|$refund_amount|$email|$phone|$salt");
				    
					$data_send = array(
				        'key' => $key,
				        'txnid' => $txnid,
				        'refund_amount' => $refund_amount,
				        'phone' => $phone,
				        'email' => $email,
				        'amount' => $amount,
				        'hash' => $checkhash
				    );		
				    global $wpdb;
			        $tablename = 'easebuzz_refund_logs';
				    $wpdb->insert( $tablename, array(
				            'order_id' => $refund_cancel['order_id'], 
				            'initiative_way' => $refund_cancel['status'],
				            'txid' => $refund_cancel['txid'], 
				            'customer_id' => $refund_cancel['customer_id'],
				            'request_json' => json_encode($data_send),
				            'resonse_json' => '', 
				            'created_date' => date("Y-m-d H:i:s"), 
				            'modified_date' => ''
				        ),
				            array( '%s', '%s', '%s', '%s', '%s', '%s', '%s') 
				        );
				        
				        // echo "<pre>";
				        // print_r($checkhash); 
				        // die;
				    
				    $id = $wpdb->insert_id;
				    $ch = curl_init();
				    curl_setopt($ch, CURLOPT_URL, "https://dashboard.easebuzz.in/transaction/v1/refund");
				    curl_setopt($ch, CURLOPT_HEADER, 0);
				    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				    curl_setopt($ch, CURLOPT_POST, 1);
				    
				    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_send);
				    $contents = curl_exec($ch);
				    curl_close($ch);

				    $resonse_json = $contents;
				    $modified_date = date("Y-m-d H:i:s");
				    $wpdb->query( $wpdb->prepare("UPDATE $tablename 
				                SET resonse_json = %s , modified_date = %s
				             WHERE id = %s",$resonse_json, $modified_date, $id)
				    );

				    
				return json_decode($resonse_json,true);
				}
			}

		/**
		 * Cancel order's profucts and manage stock of cancelled product.
		 *
		 * @name ced_rnx_cancel_customer_order
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function ced_rnx_cancel_customer_order_products() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			$item_ids = $_POST['item_ids'];
			$refund_cancel = array();
			$order_id = $_POST['order_id'];
			$refund_cancel['order_id'] = $_POST['order_id'];
			if($_POST['cancel_all'] == 'cancelled'){
				$refund_cancel['status'] = 'cancelled';
			}else{
				$refund_cancel['status'] = 'partial-cancel';
			}
			//$refund_cancel['txid'] = get_post_meta( $order_id,'transaction_id',true);

			$the_order = wc_get_order( $_POST['order_id'] );			
			$items = $the_order->get_items();
			$total_qty_order = $the_order->get_item_count();
			$refund_amount_total = '';
			$sumVA = 0;
			//My Code
			foreach ( $items as $item_id1 => $item1 ) {
			   //$lineid = $item1->get_id();
			   
               	foreach ( $item_ids as $item_detail1 ) {
               	    
					if($item_id1 == $item_detail1[0]){
					   	$product_variation_subtotal = $item1['subtotal'];
					   	$product_variation_total = $item1['total'];
					   	$product_quantity = $item1['quantity'];
					   	$product_variation_finaltotal = $product_variation_subtotal - $product_variation_total;
					   	$finaldiscount = $product_variation_finaltotal / $product_quantity;
					   	$sumVA=$sumVA+$finaldiscount;
					}
               	}
               	
            }
			foreach ( $items as $item_id => $item ) {
			 //   $total_item_qty = count($items);
				foreach ( $item_ids as $item_detail ) {
					if ( $item_id == $item_detail[0] ) {
						$product_id = $item['product_id'];
						$product_variation_id = $item['variation_id'];
						if ( $product_variation_id > 0 ) {
							$product = wc_get_product( $product_variation_id );
						} else {
							$product = wc_get_product( $product_id );
						}
						$refund_amount_total = $refund_amount_total + wc_get_price_to_display( $product );
						$refundtotal_item_qty = count($item_ids);
					}
				}	
			}
			$customer_id = ( $value = get_post_meta( $order_id, '_customer_user', true ) ) ? absint( $value ) : '';
			$total_amount = $the_order->get_total();
			$shipping_amount = $the_order->get_shipping_total();
			$email = $the_order->get_billing_email();
			$phone = $the_order->get_billing_phone();
			$refund_cancel['total_item_qty1'] =  $total_qty_order;
			$refund_cancel['item_qty1'] = $refundtotal_item_qty;
			$quanity_shpping_price = $refund_cancel['item_qty1'] * 99;
			$refund_cancel['txid'] = $the_order->get_transaction_id();
			$refund_cancel['amount'] = $total_amount;
			$refund_cancel['refund_shippingamount'] = $shipping_amount;
			$refund_cancel['refund_discount'] = $sumVA;
			$refund_cancel['refund_amount'] = $refund_amount_total - $refund_cancel['refund_discount'];
			$total_finalamount = $refund_cancel['refund_amount'] + $quanity_shpping_price;
			$refund_cancel['phone'] = $phone;
			$refund_cancel['email'] = $email;
			$refund_cancel['customer_id'] = $customer_id;
		    
            //print_r($refund_cancel);
            
			$Refund_Request = $this->Refund_Request_Api($refund_cancel);
			
			// if ($check_ajax & $Refund_Request['status'] != '') {
			if ($check_ajax) {
				$order_id = intval( $_POST['order_id'] );
				$the_order = wc_get_order( $order_id );
				$items = $the_order->get_items();				
				if ( ! is_array( $item_ids ) || empty( $item_ids ) ) {
					$endpoints = get_option( 'woocommerce_myaccount_orders_endpoint', 'orders' );
					$url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
					$url .= "$endpoints";
					$success    = __( 'Please select order\'s product to cancel.', 'woocommerce-refund-and-exchange' );
					$notice     = wc_add_notice( $success, 'error' );
					echo $url;
					wp_die();
				}
					// <h4 style="text-align:center">Order #' . $order_id . '</h4>
				$message = '';
				$message .= '<div style="font-family:Arial,Helvetica Neue,Helvetica,sans-serif;text-align:center" align="center">
                                <table style="width:100%; border: 1px solid black; border-collapse: collapse; font-family: Arial" cm-sr-title="Email Id: rishabh.gupta@studds.com" cm-sr-comment="true">
                                    <tbody>
                                        <tr>
											<th style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . __( 'Product', 'woocommerce-refund-and-exchange' ) . '</th>
											<th style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . __( 'Quantity', 'woocommerce-refund-and-exchange' ) . '</th>
											<th style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . __( 'Price', 'woocommerce-refund-and-exchange' ) . '</th>
										</tr>';
				$total_amount = 0;
				//changes - 02-010-22
				$cancel_datas = get_post_meta( $order_id, 'partial_cancel_details',true);
				$count = count($cancel_datas['partial_cancel_product']);
				$count_final = $count;
				$i = 0;
				$insert_data['partial_cancel_product'] = array();
				foreach ( $items as $item_id => $item ) {
					foreach ( $item_ids as $item_detail ) {
						if ( $item_id == $item_detail[0] ) {
							$product_name = $item['name'];
							$product_id = $item['product_id'];
							$product_variation_id = $item['variation_id'];
							if ( $product_variation_id > 0 ) {
								$product = wc_get_product( $product_variation_id );
							} else {
								$product = wc_get_product( $product_id );
							}
							if ( WC()->version < '3.1.0' ) {
								$item_meta      = new WC_Order_Item_Meta( $item, $_product );
								$item_meta_html = $item_meta->display( true, true );
							} else {
								$item_meta      = new WC_Order_Item_Product( $item, $_product );
								$item_meta_html = wc_display_item_meta( $item_meta, array( 'echo' => false ) );
							}
							$total_amount += $item_detail[1] * wc_get_price_to_display( $product );
							$message .= '<tr>
											<td style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;"><b>' . $item['name'] . '</b></td>
										    <td style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . $item_detail[1] . '</td>
											<td style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . wc_price( $item_detail[1] * wc_get_price_to_display( $product ) ) . '</td>
										</tr>';
							if ( WC()->version < '3.0.0' ) {
								$product_qty_left = $item['qty'] - $item_detail[1];
								$product_quantity = $item_detail[1];
							} else {
								$product_qty_left = $item['qty'] - $item_detail[1];
								$product_quantity = $item_detail[1];
							}
							if ( $product_qty_left < 0 ) {
								$endpoints = get_option( 'woocommerce_myaccount_orders_endpoint', 'orders' );

								$endpoints = get_option( 'woocommerce_myaccount_orders_endpoint', 'orders' );
								$url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
								$url .= "$endpoints";
								$success    = __( 'Please select correct quantity of order\'s product.', 'woocommerce-refund-and-exchange' );
								$notice     = wc_add_notice( $success, 'error' );
								echo $url;
								wp_die();
							} else if ( $product_qty_left >= 0 ) {
								$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );

								$item['qty'] = $item['qty'] - $item_detail[1];
								$args['qty'] = $item['qty'];
								if ( WC()->version < '3.0.0' ) {
									$the_order->update_product( $item_id, $product, $args );
								} else {
									wc_update_order_item_meta( $item_id, '_qty', $item['qty'] );

									$product = wc_get_product( $product->get_id() );

									if ( $product->backorders_require_notification() && $product->is_on_backorder( $args['qty'] ) ) {
										$item->add_meta_data( apply_filters( 'woocommerce_backordered_item_meta_name', __( 'Backordered', 'woocommerce-refund-and-exchange' ) ), $args['qty'] - max( 0, $product->get_stock_quantity() ), true );
									}

									$item_data = $item->get_data();

									$price_excluded_tax = wc_get_price_excluding_tax( $product, array( 'qty' => 1 ) );
									$price_tax_excluded = $item_data['total'] / $item_data['quantity'];

									$args['subtotal'] = $price_excluded_tax * $args['qty'];
									$args['total']  = $price_tax_excluded * $args['qty'];

									if ( isset( $cancel_datas ) && ! empty( $cancel_datas ) ) {
										$cancel_datas1['product_id'] = $item_data['product_id'];
										$cancel_datas1['variation_id'] = $item_data['variation_id'];
										$cancel_datas1['item_id'] = $item_id;
										$cancel_datas1['total'] = $item_data['subtotal'];
										// $total_amount = $item_data['total'];
										$cancel_datas1['price'] = wc_get_price_to_display( $product );
										$cancel_datas1['qty'] = $item_detail[1];
										$cancel_datas1['prev_qty'] = $item_data['quantity'];
										// $cancel_datas1['subject'] = $_POST['subject'];
										$cancel_datas1['reason'] = $item_detail[2];
										$date_time = date( 'Y-m-d H:i:s e' );
										$cancel_datas1['date'] = $date_time;
										$newArray = array_push($cancel_datas['partial_cancel_product'], $cancel_datas1);
										$count_final++;
									}else{
										$cancel_array['product_id'] = $item_data['product_id'];

										$cancel_array['variation_id'] = $item_data['variation_id']; 

										$cancel_array['item_id'] = $item_id;

										$cancel_array['total'] = $item_data['subtotal']; 

										$cancel_array['price'] = wc_get_price_to_display( $product );

										$cancel_array['qty'] = $item_detail[1];

										$cancel_array['prev_qty'] = $item_data['quantity'];
										$total_amount = $item_data['total'];

										// $cancel_array['subject'] = $_POST['subject'];

										$cancel_array['reason'] = $item_detail[2];
										$date_time = date( 'Y-m-d H:i:s e' );
										$cancel_array['date'] = $date_time;
										array_push($insert_data['partial_cancel_product'], $cancel_array);;
										$cancel_array['reason'] = $_POST['reason'];									}
									$item->set_order_id( $order_id );
									$item->set_props( $args );
									$item->save();
								}
							}
							$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
							if ( $product->managing_stock() ) {
								if ( WC()->version < '3.0.0' ) {
									$product->set_stock( $product_quantity, 'add' );

								} else {
									if ( $product_variation_id > 0 ) {
										$total_stock = get_post_meta( $product_variation_id, '_stock', true );
										$total_stock = $total_stock + $product_quantity;
										wc_update_product_stock( $product_variation_id, $total_stock, 'set' );
									} else {
										$total_stock = get_post_meta( $product_id, '_stock', true );
										$total_stock = $total_stock + $product_quantity;
										wc_update_product_stock( $product_id, $total_stock, 'set' );
									}
								}
							}
							$i++;
						}
					}
				}
				// echo "<br>";
				// print_r($insert_data);
				// die;
				if(!empty($insert_data['partial_cancel_product'] && count($insert_data['partial_cancel_product']) >= 1)){
					update_post_meta( $order_id, 'partial_cancel_details', $insert_data);
				}else if(!empty($cancel_datas)){
					update_post_meta( $order_id, 'partial_cancel_details', $cancel_datas);
				}				
				$message .= '<br><tr>
				                <td colspan="2" style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;"><b>Total Shipping Amount</b></td>
								<td style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;"><span class="woocommerce-Price-currencySymbol">₹</span>' . 	$quanity_shpping_price . '</td>
							</tr>
							<tr>
				                <td colspan="2" style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;"><b>Total Refund Amount</b></td>
								<td style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;"><span class="woocommerce-Price-currencySymbol">₹</span>' . $total_finalamount . '</td>
							</tr>
							</tbody></table><p>Once your order has been cancelled, the refund amount will be credited back to the source account within 5-7 working days.</p></div>';
				$the_order->calculate_totals();
				$wallet_flag = true;
				if ( 'processing' === $the_order->get_status() && 'cod' === $the_order->get_payment_method() ) {
					$wallet_flag = false;
				}
				if ( ced_rnx_wallet_feature_enable() ) {
					$cancelstatusenable = get_option( 'ced_rnx_return_wallet_cancelled', 'no' );
					if ( $cancelstatusenable == 'yes' && $wallet_flag ) {
						$customer_id = ( $value = get_post_meta( $order_id, '_customer_user', true ) ) ? absint( $value ) : '';
						if ( $customer_id > 0 ) {
							$walletcoupon = get_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', true );
							if ( empty( $walletcoupon ) ) {
								$coupon_code = ced_rnx_coupon_generator( 5 ); // Code
								$discount_type = 'fixed_cart';
								$order_id = $the_order->get_id();
								$coupon_description = "CANCELLED - ORDER #$order_id";

								$coupon = array(
									'post_title' => $coupon_code,
									'post_content' => $coupon_description,
									'post_excerpt' => $coupon_description,
									'post_status' => 'publish',
									'post_author' => get_current_user_id(),
									'post_type'     => 'shop_coupon',
								);

								$new_coupon_id = wp_insert_post( $coupon );
								$discount_type = 'fixed_cart';
								update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
								update_post_meta( $new_coupon_id, 'rnxwallet', true );
								update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $coupon_code );
								update_post_meta( $new_coupon_id, 'coupon_amount', $total_amount );
							} else {
								$the_coupon = new WC_Coupon( $walletcoupon );
								$coupon_id = $the_coupon->get_id();
								if ( isset( $coupon_id ) ) {

									$amount = get_post_meta( $coupon_id, 'coupon_amount', true );
									$remaining_amount = $amount + $total_amount;
									update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $walletcoupon );
									update_post_meta( $coupon_id, 'rnxwallet', true );
									update_post_meta( $coupon_id, 'coupon_amount', $remaining_amount );
								}
							}
						}
					} else {
						wc_add_notice( 'Your order is unpaid' );
					}
				}
				if($_POST['cancel_all'] == 'cancelled'){
				    
				//ShipRcoket Order fetch
				
				$token = Authorization_ShiprockAPI_Cron();
                if ($token) {
                    $curl2 = curl_init();
            
                    curl_setopt_array($curl2, array(
                        CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json',
                            'Authorization: Bearer ' . $token
                        ),
                    ));
            
                    $response2 = curl_exec($curl2);
                    $response_array2 = json_decode($response2, true);
                    //print_r($response_array2);
                   
                    // Iterate through the array to find the entry with the matching channel_order_id
                    foreach ($response_array2['data'] as $entry) {
                        //print_r($response_array);
                        if ($entry['channel_order_id'] == $order_id) { // Replace "634666" with the desired channel_order_id
                            $id = $entry['id']; // Retrieve the id associated with the matching channel_order_id
                            
                            // Call the function to cancel the order
                            //$cancel_response = cancelShiprocketOrder($id, $token);
                            //return $cancel_response;
                            //break; // Exit the loop once the match is found
                        }
                    }
                    
                    curl_close($curl2);
                }
    
            //ShipRocket Order fetch Code End
            //ShipRocket Cancel API Start

                $curl1 = curl_init();
            
                curl_setopt_array($curl1, array(
                  CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders/cancel',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => json_encode(array("ids" => [$id])),
                  CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                  ),
                ));
                $response3 = curl_exec($curl1);
                curl_close($curl1);
            //ShipRocket Cancel API End
			// $the_order->update_status( 'wc-cancelled', __( 'The user has cancelled some products of order.', 'woocommerce-refund-and-exchange' ) );
					
					$the_order->update_status( 'cancelled', __( 'Order cancelled by customer.', 'woocommerce' ) );
				}else{
					$the_order->update_status( 'wc-partial-cancel', __( 'The user has cancelled some products of order.', 'woocommerce-refund-and-exchange' ) );
				}
				
				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
				$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );
				if($_POST['cancel_all'] == 'cancelled'){
				$subject = __( 'Just to let you know - Your order has been cancelled against order id #', 'woocommerce-refund-and-exchange' ) . $order_id;
			    }else{
				$subject = __( 'Just to let you know - Your order has been partially cancelled against order id #', 'woocommerce-refund-and-exchange' ) . $order_id;
			    }
				//$subject = __( 'Product(s) cancelled by a customer of order #', 'woocommerce-refund-and-exchange' ) . $order_id;
				$html_content = '<!DOCTYPE html>
                                    <html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">
                                        <head>
                                            <title></title>
                                                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                                                <meta name="viewport" content="width=device-width,initial-scale=1">
                                                <!--[if mso]>
                                				<xml>
                                					<o:OfficeDocumentSettings>
                                						<o:PixelsPerInch>96</o:PixelsPerInch>
                                						<o:AllowPNG/>
                                					</o:OfficeDocumentSettings>
                                				</xml>
                                				<![endif]-->
                                                <style>
                                                  * {
                                                    box-sizing: border-box
                                                  }
                                            
                                                  body {
                                                    margin: 0;
                                                    padding: 0
                                                  }
                                            
                                                  a[x-apple-data-detectors] {
                                                    color: inherit !important;
                                                    text-decoration: inherit !important
                                                  }
                                            
                                                  #MessageViewBody a {
                                                    color: inherit;
                                                    text-decoration: none
                                                  }
                                            
                                                  p {
                                                    line-height: inherit
                                                  }
                                            
                                                  .desktop_hide,
                                                  .desktop_hide table {
                                                    mso-hide: all;
                                                    display: none;
                                                    max-height: 0;
                                                    overflow: hidden
                                                  }
                                            
                                                  .image_block img+div {
                                                    display: none
                                                  }
                                            
                                                  @media (max-width:620px) {
                                                    .row-content {
                                                      width: 100% !important
                                                    }
                                            
                                                    .mobile_hide {
                                                      display: none
                                                    }
                                            
                                                    .stack .column {
                                                      width: 100%;
                                                      display: block
                                                    }
                                            
                                                    .mobile_hide {
                                                      min-height: 0;
                                                      max-height: 0;
                                                      max-width: 0;
                                                      overflow: hidden;
                                                      font-size: 0
                                                    }
                                            
                                                    .desktop_hide,
                                                    .desktop_hide table {
                                                      display: table !important;
                                                      max-height: none !important
                                                    }
                                            
                                                    .row-2 .column-1 {
                                                      padding: 5px 10px !important
                                                    }
                                                  }
                                                </style>
								            </head>
								        <body style="background-color:#fff;margin:0;padding:0;-webkit-text-size-adjust:none;text-size-adjust:none">
								            <table class="nl-container" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;background-color:#fff">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <table class="row row-1" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                                                              <tbody>
                                                                <tr>
                                                                  <td>
                                                                    <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;background-color:#ed3237;border-radius:0;color:#000;width:600px" width="600">
                                                                      <tbody>
                                                                        <tr>
                                                                          <td class="column column-1" width="100%" style="mso-table-lspace:0;mso-table-rspace:0;font-weight:400;text-align:left;padding-bottom:5px;padding-right:15px;padding-top:5px;vertical-align:top;border-top:0;border-right:0;border-bottom:0;border-left:0">
                                                                            <table class="image_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                                                                              <tr>
                                                                                <td class="pad" style="width:100%;padding-right:0;padding-left:0">
                                                                                  <div class="alignment" align="center" style="line-height:10px">
                                                                                    <img alt="" width="100" src="https://shop.studds.com/wp-content/uploads/2023/01/STUDDS-White-250-X-250-LOGO.png" max-width="100%" style="border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; background-color: transparent; max-width: 100%; vertical-align: middle; width: 100px;" border="0" bgcolor="transparent">
                                                                                  </div>
                                                                                </td>
                                                                              </tr>
                                                                            </table>
                                                                          </td>
                                                                        </tr>
                                                                      </tbody>
                                                                    </table>
                                                                  </td>
                                                                </tr>
                                                              </tbody>
                                                            </table>
                                                            <table class="row row-2" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <table class="row-content" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;background-color:#f9f9f9;border-bottom:0 solid #bfbfbf;border-left:0 solid #bfbfbf;border-right:0 solid #bfbfbf;border-top:0 solid #bfbfbf;color:#000;width:600px" width="600">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="column column-1" width="100%" style="mso-table-lspace:0;mso-table-rspace:0;font-weight:400;text-align:left;border-left:1px solid #3c434a;border-right:1px solid #3c434a;padding-bottom:5px;padding-left:30px;padding-right:30px;padding-top:5px;vertical-align:top;border-top:0;border-bottom:0">
                                                                                            <table class="heading_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                                                                                              <tr>
                                                                                                <td class="pad" style="padding-bottom:15px;padding-top:25px;text-align:center;width:100%">
                                                                                                  <h1 style="margin:0;color:#555;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:23px;font-weight:700;letter-spacing:normal;line-height:120%;text-align:left;margin-top:0;margin-bottom:0">
                                                                                                    <span class="tinyMce-placeholder">Hi '. $the_order->get_billing_first_name().'</span>
                                                                                                  </h1>
                                                                                                </td>
                                                                                              </tr>
                                                                                            </table>
                                                                                            <table class="paragraph_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;word-break:break-word">
                                                                                              <tr>
                                                                                                <td class="pad" style="padding-bottom:40px">
                                                                                                  <div style="color:#000;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:14px;font-weight:400;letter-spacing:0;line-height:120%;text-align:left;mso-line-height-alt:16.8px">
                                                                                                    <p style="margin:0">' . $subject . '</p>
                                                                                                  </div>
                                                                                                </td>
                                                                                              </tr>
                                                                                            </table>
                                                                                            <table class="html_block block-3" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                                                                                                <tr>
                                                                                                    <td class="pad">' . $message . '</td>
                                                                                                </tr>
                                                                                            </table>';
                                                                            $html_content .='<table class="heading_block block-4" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                                                                                                <tr>
                                                                                                    <td class="pad" style="padding-bottom:15px;padding-top:50px;text-align:center;width:100%">
                                                                                                        <h3 style="margin:0;color:#ed3237;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:20px;font-weight:700;letter-spacing:normal;line-height:120%;text-align:left;margin-top:0;margin-bottom:0">
                                                                                                            <span class="tinyMce-placeholder">' . __( 'Customer details', 'woocommerce-refund-and-exchange' ) . '</span>
                                                                                                        </h3>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </table>
                                                                                            <table class="paragraph_block block-5" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;word-break:break-word">
                                                                                                <tr>
                                                                                                    <td class="pad" style="padding-bottom:10px;padding-top:10px">
                                                                                                        <div style="color:#000;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:14px;font-weight:400;letter-spacing:0;line-height:120%;text-align:left;mso-line-height-alt:16.8px">
                                                                                                            <p style="margin:0;margin-bottom:16px">' . __( 'Email', 'woocommerce-refund-and-exchange' ) . ': ' . get_post_meta( $order_id, '_billing_email', true ) . '</p>
                                                                                                            <p style="margin:0">' . __( 'Mobile', 'woocommerce-refund-and-exchange' ) . ': ' . get_post_meta( $order_id, '_billing_phone', true ) . '</p>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </table>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row row-3" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;background-color:#f9f9f9;border-radius:0;color:#000;width:600px" width="600">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="column column-1" width="50%" style="mso-table-lspace:0;mso-table-rspace:0;font-weight:400;text-align:left;border-left:1px solid #3c434a;padding-bottom:5px;padding-left:30px;padding-right:25px;padding-top:5px;vertical-align:top;border-top:0;border-right:0;border-bottom:0">
                                                                                            <table class="heading_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                                                                                                <tr>
                                                                                                    <td class="pad" style="padding-bottom:15px;padding-top:20px;text-align:center;width:100%">
                                                                                                        <h3 style="margin:0;color:#ed3237;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:20px;font-weight:700;letter-spacing:normal;line-height:120%;text-align:left;margin-top:0;margin-bottom:0">
                                                                                                            <span class="tinyMce-placeholder">' . __( 'Shipping Address', 'woocommerce-refund-and-exchange' ) . '</span>
                                                                                                        </h3>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </table>
                                                                                            <table class="paragraph_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;word-break:break-word">
                                                                                                <tr>
                                                                                                    <td class="pad" style="padding-bottom:10px;padding-top:10px">
                                                                                                        <div style="color:#000;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:14px;font-weight:400;letter-spacing:0;line-height:120%;text-align:left;mso-line-height-alt:16.8px">
                                                                                                            <p style="margin:0">' . $the_order->get_formatted_shipping_address() . '</p>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </table>
                                                                                        </td>
                                                                                        <td class="column column-2" width="50%" style="mso-table-lspace:0;mso-table-rspace:0;font-weight:400;text-align:left;border-right:1px solid #3c434a;padding-bottom:5px;padding-left:15px;padding-top:5px;vertical-align:top;border-top:0;border-bottom:0;border-left:0">
                                                                                            <table class="heading_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                                                                                                <tr>
                                                                                                    <td class="pad" style="padding-bottom:15px;padding-top:20px;text-align:center;width:100%">
                                                                                                        <h3 style="margin:0;color:#ed3237;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:20px;font-weight:700;letter-spacing:normal;line-height:120%;text-align:left;margin-top:0;margin-bottom:0">
                                                                                                            <span class="tinyMce-placeholder">' . __( 'Billing Address', 'woocommerce-refund-and-exchange' ) . '</span>
                                                                                                        </h3>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </table>
                                                                                            <table class="paragraph_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;word-break:break-word">
                                                                                                <tr>
                                                                                                    <td class="pad" style="padding-bottom:10px;padding-top:10px">
                                                                                                        <div style="color:#000;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:14px;font-weight:400;letter-spacing:0;line-height:120%;text-align:left;mso-line-height-alt:16.8px">
                                                                                                            <p style="margin:0">	' . $the_order->get_formatted_billing_address() . '</p>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </table>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                            							    <table class="row row-4" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                                                                <tbody>
                                                                    <tr>
                                                                      <td>
                                                                        <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;background-color:#ed3237;border-radius:0;color:#000;width:600px" width="600">
                                                                          <tbody>
                                                                            <tr>
                                                                              <td class="column column-1" width="100%" style="mso-table-lspace:0;mso-table-rspace:0;font-weight:400;text-align:left;border-bottom:1px solid #ffffff;padding-bottom:5px;padding-top:5px;vertical-align:top;border-top:0;border-right:0;border-left:0">
                                                                                <table class="heading_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                                                                                  <tr>
                                                                                    <td class="pad" style="padding-bottom:25px;padding-top:25px;text-align:center;width:100%">
                                                                                      <h1 style="margin:0;color:#ffffff;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:18px;font-weight:500;letter-spacing:normal;line-height:120%;text-align:center;margin-top:0;margin-bottom:0">
                                                                                        <span style="color: #ffffff;">
                                                                                          <u>
                                                                                            <span class="tinyMce-placeholder">STAY CONNECTED</span>
                                                                                          </u>
                                                                                        </span>
                                                                                      </h1>
                                                                                    </td>
                                                                                  </tr>
                                                                                </table>
                                                                                <table class="icons_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                                                                                  <tr>
                                                                                    <td class="pad" style="vertical-align:middle;color:#000;font-family:inherit;font-size:14px;font-weight:400;text-align:center">
                                                                                      <table class="alignment" cellpadding="0" cellspacing="0" role="presentation" align="center" style="mso-table-lspace:0;mso-table-rspace:0">
                                                                                        <tr>
                                                                                          <td style="vertical-align:middle;text-align:center;padding-top:5px;padding-bottom:5px;padding-left:5px;padding-right:5px">
                                                                                            <a href="https://www.facebook.com/StuddsAccessoriesLtd/" style="text-decoration: none; word-break: break-word;">
                                                                                                <img src="https://shop.studds.com/wp-content/plugins/email-template-customizer-for-woo/assets/img/fb-blue-white.png" width="32" style="border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; vertical-align: middle; background-color: transparent; max-width: 100%;" border="0" bgcolor="transparent">
                                                                                            </a>
                                                                                          </td>
                                                                                          <td style="vertical-align:middle;text-align:center;padding-top:5px;padding-bottom:5px;padding-left:5px;padding-right:5px">
                                                                                            <a href="https://twitter.com/StuddsHelmet" style="text-decoration: none; word-break: break-word;">
                                                                                                <img src="https://shop.studds.com/wp-content/plugins/email-template-customizer-for-woo/assets/img/twi-cyan-white.png" width="32" style="border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; vertical-align: middle; background-color: transparent; max-width: 100%;" border="0" bgcolor="transparent">
                                                                                            </a>
                                                                                          </td>
                                                                                          <td style="vertical-align:middle;text-align:center;padding-top:5px;padding-bottom:5px;padding-left:5px;padding-right:5px">
                                                                                            <a href="https://www.instagram.com/studdshelmets/" style="text-decoration: none; word-break: break-word;">
                                                                                                <img src="https://shop.studds.com/wp-content/plugins/email-template-customizer-for-woo/assets/img/ins-white-color.png" width="32" style="border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; vertical-align: middle; background-color: transparent; max-width: 100%;" border="0" bgcolor="transparent">
                                                                                            </a>
                                                                                          </td>
                                                                                        </tr>
                                                                                      </table>
                                                                                    </td>
                                                                                  </tr>
                                                                                </table>
                                                                                <table class="paragraph_block block-3" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;word-break:break-word">
                                                                                  <tr>
                                                                                    <td class="pad">
                                                                                      <div style="color:#fff;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:14px;font-weight:400;letter-spacing:0;line-height:120%;text-align:center;mso-line-height-alt:16.8px">
                                                                                        <p style="margin:0" style="color:white;"><a href="https://shop.studds.com/my-account/contact-us/?utm_source=Notification+Mails&utm_medium=email&utm_campaign=Order+Cancelled&utm_content=Need+Help" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Need Help?</a> | customercare@studds.com | 0129-4296500</p>
                                                                                      </div>
                                                                                    </td>
                                                                                  </tr>
                                                                                </table>
                                                                              </td>
                                                                            </tr>
                                                                          </tbody>
                                                                        </table>
                                                                      </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table class="row row-5" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                                                                <tbody>
                                                                    <tr>
                                                                      <td>
                                                                        <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;background-color:#ed3237;border-radius:0;color:#000;width:600px" width="600">
                                                                          <tbody>
                                                                            <tr>
                                                                              <td class="column column-1" width="100%" style="mso-table-lspace:0;mso-table-rspace:0;font-weight:400;text-align:left;border-bottom:1px solid #000;padding-bottom:5px;padding-top:5px;vertical-align:top;border-top:0;border-right:0;border-left:0">
                                                                                <table class="paragraph_block block-1" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;word-break:break-word">
                                                                                  <tr>
                                                                                    <td class="pad">
                                                                                      <div style="color:#fff;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:14px;font-weight:400;letter-spacing:0;line-height:120%;text-align:center;mso-line-height-alt:16.8px">
                                                                                        <p style="margin:0"><a href="https://shop.studds.com/our-policies/#warranty-policy?utm_source=Notification+Mails&utm_medium=email&utm_campaign=Order+Cancelled&utm_content=Warranty+Policy" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Warrenty Policy</a> | <a href="https://shop.studds.com/our-policies/#order-cancellation-policy?utm_source=Notification+Mails&utm_medium=email&utm_campaign=Order+Cancelled&utm_content=Exchange+Policy" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Exchange Policy</a> | <a href="https://shop.studds.com/our-policies/#order-cancellation-policy" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Cancellation Policy</a></p>
                                                                                      </div>
                                                                                    </td>
                                                                                  </tr>
                                                                                </table>
                                                                                <table class="paragraph_block block-2" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;word-break:break-word">
                                                                                  <tr>
                                                                                    <td class="pad">
                                                                                      <div style="color:#fff;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:14px;font-weight:400;letter-spacing:0;line-height:120%;text-align:center;mso-line-height-alt:16.8px">
                                                                                        <p style="margin:0"><a href="https://shop.studds.com/shopping-shipping-delivery-policy/?utm_source=Notification+Mails&utm_medium=email&utm_campaign=Order+Cancelled&utm_content=Shopping%2C+Shipping+%26+Delivery+Policy" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Shopping, Shipping & Delivery Policy</a> &nbsp;| &nbsp;<a href="https://shop.studds.com/terms-of-use/?utm_source=Notification+Mails&utm_medium=email&utm_campaign=Order+Cancelled&utm_content=Terms+Of+Use" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Terms Of Use</a> | &nbsp;<a href="https://shop.studds.com/privacy-policy/?utm_source=Notification+Mails&utm_medium=email&utm_campaign=Order+Cancelled&utm_content=Privacy+Policy" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; font-family: Roboto;color: #ffffff;"" >Privacy Policy</a></p>
                                                                                      </div>
                                                                                    </td>
                                                                                  </tr>
                                                                                </table>
                                                                              </td>
                                                                            </tr>
                                                                          </tbody>
                                                                        </table>
                                                                      </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <!-- End -->
                                        </body>
                                    </html>';
    
				$headers = array();
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				// $to = get_option( 'ced_rnx_notification_from_mail' );
				$to = $email;
				wc_mail( $to, $subject, $html_content, $headers );

				$endpoints = get_option( 'woocommerce_myaccount_orders_endpoint', 'orders' );

				$url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
				$url .= "$endpoints";
				$success    = __( 'Your selected product(s) removed from order Id #', 'woocommerce-refund-and-exchange' ) . $order_id;;
				$notice     = wc_add_notice( $success );
				echo $url;
				wp_die();
			}else{
				$url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
				$url .= "$endpoints";
				$errors    = __( 'something went wrong please contact your administrator - ERROR!' );
				$notice     = wc_add_notice( $errors );
				echo $url;
				wp_die();
			}
		}

		/**
		 * Change coupon amount for customers from user listing panel.
		 *
		 * @name ced_rnx_change_customer_wallet_amount
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		public function ced_rnx_change_customer_wallet_amount() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$coupon_code = $_POST['coupon_code'];
				$amount = $_POST['amount'];
				if ( ! isset( $amount ) || $amount == '' ) {
					$amount = 0;
				}
				$the_coupon = new WC_Coupon( $coupon_code );
				$customer_coupon_id = $the_coupon->get_id();
				if ( isset( $the_coupon ) && $the_coupon != '' ) {
					update_post_meta( $customer_coupon_id, 'coupon_amount', $amount );
				}
			}
			wp_die();
		}

		/**
		 * Generate User Wallet Coupon Code with no wallet.
		 *
		 * @name ced_rnx_generate_user_wallet_code
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		public function ced_rnx_generate_user_wallet_code() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$coupon_code = ced_rnx_coupon_generator( 5 );
				$coupon = array(
					'post_title' => $coupon_code,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'shop_coupon',
				);
				$new_coupon_id = wp_insert_post( $coupon );
				$discount_type = 'fixed_cart';
				update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
				update_post_meta( $new_coupon_id, 'coupon_amount', 0 );
				update_post_meta( $new_coupon_id, 'rnxwallet', true );
				update_post_meta( $_POST['id'], 'ced_rnx_refund_wallet_coupon', $coupon_code );
				echo esc_html( $coupon_code );
				wp_die();
			}
		}

		/**
		 * Regenerate Customer Wallet Coupon Code.
		 *
		 * @name ced_rnx_coupon_regenertor
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		public function ced_rnx_coupon_regenertor() {

			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$coupon_code = ced_rnx_coupon_generator( 5 );
				$coupon = array(
					'ID' => $_POST['id'],
					'post_title' => $coupon_code,
				);
				$customer_id = get_current_user_id();
				wp_update_post( $coupon );
				update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $coupon_code );
				$coupon_price = get_post_meta( $_POST['id'], 'coupon_amount', true );
				$response = array(
					'coupon_code' => $coupon_code,
					'currency_symbol' => get_woocommerce_currency_symbol(),
					'coupon_price' => ced_rnx_currency_seprator( $coupon_price ),
					'coupon_code_text' => __( 'Coupon Code', 'woocommerce-refund-and-exchange' ),
					'wallet_amount_text' => __( 'Wallet Amount', 'woocommerce-refund-and-exchange' ),
				);
				echo json_encode( $response );
				wp_die();
			}
		}

		/**
		 * Manage Customer Wallet on Order cancelled
		 *
		 * @name ced_rnx_woocommerce_order_status_changed
		 * @param $order_id
		 * @param $old_status
		 * @param $new_status
		 */
		function ced_rnx_woocommerce_order_status_changed( $order_id, $old_status, $new_status ) {
			$order = wc_get_order( $order_id );
			if ( ced_rnx_wallet_feature_enable() ) {
				$cancelstatusenable = get_option( 'ced_rnx_return_wallet_cancelled', 'no' );

				if ( $cancelstatusenable == 'yes' ) {
					$customer_id = ( $value = get_post_meta( $order_id, '_customer_user', true ) ) ? absint( $value ) : '';
					if ( $customer_id > 0 ) {
						$statuses = array( 'processing', 'completed' );
						$wallet_flag = true;
						if ( 'processing' === $old_status && 'cod' === $order->get_payment_method() ) {
							$wallet_flag = false;
						}
						if ( in_array( $old_status, $statuses ) && $wallet_flag ) {
							if ( $new_status == 'cancelled' ) {
								$order_total = $order->get_total();
								$order_discount = $order->get_total_discount();
								$total_amount = $order_total + $order_discount;

								$walletcoupon = get_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', true );
								if ( empty( $walletcoupon ) ) {
									$coupon_code = ced_rnx_coupon_generator( 5 ); // Code
									$discount_type = 'fixed_cart';
									$order_id = $order->get_id();
									$coupon_description = "CANCELLED - ORDER #$order_id";

									$coupon = array(
										'post_title' => $coupon_code,
										'post_content' => $coupon_description,
										'post_excerpt' => $coupon_description,
										'post_status' => 'publish',
										'post_author' => get_current_user_id(),
										'post_type'     => 'shop_coupon',
									);

									$new_coupon_id = wp_insert_post( $coupon );
									$discount_type = 'fixed_cart';
									update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
									update_post_meta( $new_coupon_id, 'coupon_amount', $total_amount );
									update_post_meta( $new_coupon_id, 'rnxwallet', true );
									update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $coupon_code );
								} else {
									$the_coupon = new WC_Coupon( $walletcoupon );
									$coupon_id = $the_coupon->get_id();
									if ( isset( $coupon_id ) ) {

										$amount = get_post_meta( $coupon_id, 'coupon_amount', true );
										$remaining_amount = $amount + $total_amount;
										update_post_meta( $coupon_id, 'coupon_amount', $remaining_amount );
										update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $walletcoupon );
										update_post_meta( $coupon_id, 'rnxwallet', true );
									}
								}
							}
						}
					}
				}
			}
		}
		/**
		 * Exchange Rejected callback.
		 */
		function ced_rnx_exchange_req_reject_callback() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
		if ( $check_ajax ) {
				$orderid = $_POST['orderid'];
				$checkdate = $_POST['date'];
                $selected_product = $_POST['selected_product'];
				$exchange_details = get_post_meta( $orderid, 'ced_rnx_exchange_product', true );
                $exchange_warranty_display =  array();
				$exchange_warranty_display['status'] = 'exchange-accepted';
				$exchange_warranty = array();
				if ( isset( $exchange_details ) && ! empty( $exchange_details ) ) {
					foreach ( $exchange_details as $date => $exchange_detail ) {
					   // echo "<pre>";
					   // print_r($exchange_details);
					   // echo "<br>";
						if ($exchange_detail['status_pickup'] == 'reverse_pickup_approved') {

							$exchanged_products = $exchange_detail['to'];
				// 			print_r($exchanged_products);
				//     echo "<br>";
							$exchanged_from_products = $exchange_detail['from'];
							
							$exchange_details[ $date ]['status'] = 'cancel';
							$exchange_details[ $date ]['approve'] = date( 'd-m-Y' );
							break;
						}
					}
				}
				foreach($exchanged_products as $keys => $kvalues){
				    // print_r($exchanged_products);
				    // echo "<br>";
				    foreach ($selected_product as $key_exchange => $value_exchange){
				        if($value_exchange['approve_status'] == '3'){
        				    if($kvalues['product_id'] == $value_exchange['product_id']){
        				        unset($exchanged_products[$keys]);
        				    }
				        }
				    }
				}
				foreach($exchanged_from_products as $keys_1 => $kvalues_1){
				    foreach ($selected_product as $key_exchange => $value_exchange){
				        if($value_exchange['approve_status'] == '3'){
        				    if($kvalues_1['product_id'] == $value_exchange['product_id']){
        				        unset($exchanged_from_products[$keys_1]);
        				    }
				        }
				    }
				}
				$exchange_warranty['status'] = 'exchange-rejected';

				$order_detail = wc_get_order( $orderid );
				$includeTax = isset( $order_detail->prices_include_tax ) ? $order_detail->prices_include_tax : false;
				$user_id = $order_detail->user_id;
				$order_data = array(
					'post_name'     => 'order-' . date( 'M-d-Y-hi-a' ),
					'post_type'     => 'shop_order',
					'post_title'    => 'Order &ndash; ' . date( 'F d, Y @ h:i A' ), 
					'post_status'   => 'wc-exchange-rejected',
					'ping_status'   => 'closed',
					'post_excerpt'  => 'requested',
					'post_author'   => $user_id,
					'post_password' => uniqid( 'order_' ),
					'post_date'     => date( 'Y-m-d H:i:s e' ),
					'comment_status' => 'open',
				);

				$order_id = wp_insert_post( $order_data, true );
				//product approved and rejected comment display in view order page 
				foreach ($selected_product as $key_exchange => $value_exchange){
					$exchange_warranty_display['from'][$key_exchange]['approve_status_key'] = $value_exchange['approve_status'];
					$exchange_warranty_display['from'][$key_exchange]['approve_status'] = 'Reject';
					$exchange_warranty_display['from'][$key_exchange]['order_id'] = (int)$order_id;
					$exchange_warranty_display['from'][$key_exchange]['material_code'] = $value_exchange['sku'];							
				}

				$approve = get_option( 'ced_rnx_notification_exchange_approve' );
				$fname = get_post_meta( $orderid, '_billing_first_name', true );
				$lname = get_post_meta( $orderid, '_billing_last_name', true );

				$fullname = $fname . ' ' . $lname;

				$approve = str_replace( '[username]', $fullname, $approve );
				$approve = str_replace( '[order]', '#' . $orderid, $approve );
				$approve = str_replace( '[siteurl]', home_url(), $approve );
				$message = stripslashes( get_option( 'ced_rnx_notification_exchange_approve', false ) );
				$message = apply_filters( 'mwb_rnx_meta_content', $message );

				$fname = get_post_meta( $orderid, '_billing_first_name', true );
				$lname = get_post_meta( $orderid, '_billing_last_name', true );
				$billing_company = get_post_meta( $orderid, '_billing_company', true );
				$billing_email = get_post_meta( $orderid, '_billing_email', true );
				$billing_phone = get_post_meta( $orderid, '_billing_phone', true );
				$billing_country = get_post_meta( $orderid, '_billing_country', true );
				$billing_address_1 = get_post_meta( $orderid, '_billing_address_1', true );
				$billing_address_2 = get_post_meta( $orderid, '_billing_address_2', true );
				$billing_state = get_post_meta( $orderid, '_billing_state', true );
				$billing_postcode = get_post_meta( $orderid, '_billing_postcode', true );
				$shipping_first_name = get_post_meta( $orderid, '_shipping_first_name', true );
				$shipping_last_name = get_post_meta( $orderid, '_shipping_last_name', true );
				$shipping_company = get_post_meta( $orderid, '_shipping_company', true );
				$shipping_country = get_post_meta( $orderid, '_shipping_country', true );
				$shipping_address_1 = get_post_meta( $orderid, '_shipping_address_1', true );
				$shipping_address_2 = get_post_meta( $orderid, '_shipping_address_2', true );
				$shipping_city = get_post_meta( $orderid, '_shipping_city', true );
				$shipping_state = get_post_meta( $orderid, '_shipping_state', true );
				$shipping_postcode = get_post_meta( $orderid, '_shipping_postcode', true );
				$payment_method_tittle = get_post_meta( $orderid, '_payment_method_title', true );
				$order_shipping = get_post_meta( $orderid, '_order_shipping', true );
				$order_total = get_post_meta( $orderid, '_order_total', true );
				$refundable_amount = get_post_meta( $orderid, 'refundable_amount', true );

				// get title data
				$_billing_salutation = get_post_meta( $orderid, '_billing_salutation', true );
				$_shipping_salutation = get_post_meta( $orderid, '_shipping_salutation', true );

				// get other data 
				$payment_method = get_post_meta( $orderid, '_payment_method', true);
				$payment_method_title = get_post_meta( $orderid, '_payment_method_title', true);
				$date_completed = get_post_meta( $orderid, '_date_completed', true);
				$date_paid = get_post_meta( $orderid, '_date_paid', true);
				$completed_date = get_post_meta( $orderid, '_completed_date', true);
				$cart_discount = get_post_meta( $orderid, '_cart_discount', true);
				$order_shipping = get_post_meta( $orderid, '_order_shipping', true);
				$transaction_id = get_post_meta( $orderid, '_transaction_id', true);

				$fullname = $fname . ' ' . $lname;

				$message = str_replace( '[username]', $fullname, $message );
				$message = str_replace( '[order]', '#' . $orderid, $message );
				$message = str_replace( '[siteurl]', home_url(), $message );
				$message = str_replace( '[_billing_company]', $billing_company, $message );
				$message = str_replace( '[_billing_email]', $billing_email, $message );
				$message = str_replace( '[_billing_phone]', $billing_phone, $message );
				$message = str_replace( '[_billing_country]', $billing_country, $message );
				$message = str_replace( '[_billing_address_1]', $billing_address_1, $message );
				$message = str_replace( '[_billing_address_2]', $billing_address_2, $message );
				$message = str_replace( '[_billing_state]', $billing_state, $message );
				$message = str_replace( '[_billing_postcode]', $billing_postcode, $message );
				$message = str_replace( '[_shipping_first_name]', $shipping_first_name, $message );
				$message = str_replace( '[_shipping_last_name]', $shipping_last_name, $message );
				$message = str_replace( '[_shipping_company]', $shipping_company, $message );
				$message = str_replace( '[_shipping_country]', $shipping_country, $message );
				$message = str_replace( '[_shipping_address_1]', $shipping_address_1, $message );
				$message = str_replace( '[_shipping_address_2]', $shipping_address_2, $message );
				$message = str_replace( '[_shipping_city]', $shipping_city, $message );
				$message = str_replace( '[_shipping_state]', $shipping_state, $message );
				$message = str_replace( '[_shipping_postcode]', $shipping_postcode, $message );
				$message = str_replace( '[_payment_method_tittle]', $payment_method_tittle, $message );
				$message = str_replace( '[_order_shipping]', $order_shipping, $message );
				$message = str_replace( '[_order_total]', $order_total, $message );
				$message = str_replace( '[_refundable_amount]', $refundable_amount, $message );
				$ced_rnx_odr = wc_get_order( $orderid );
				$message = str_replace( '[formatted_shipping_address]', $ced_rnx_odr->get_formatted_shipping_address(), $message );
				$message = str_replace( '[formatted_billing_address]', $ced_rnx_odr->get_formatted_billing_address(), $message );

				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
				$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );
				$ced_rnx_notification_exchange_approve_template = get_option( 'ced_rnx_notification_exchange_approve_template', 'no' );
				$mwb_dis_tot = 0;
				$ced_flag = false;

				$fname = get_option( 'ced_rnx_notification_from_name' );
				$fmail = get_option( 'ced_rnx_notification_from_mail' );

				$ced_rnx_enable_return_ship_label = get_option( 'ced_rnx_enable_return_ship_label', 'no' );
				if ( $ced_rnx_enable_return_ship_label == 'on' ) {
					$headers = array();
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					$to = get_post_meta( $orderid, '_billing_email', true );
					$subject = get_option( 'ced_rnx_return_slip_mail_subject' );
					$subject = str_replace( '[order]', '#' . $orderid, $subject );
					$ced_rnx_order_for_label = wc_get_order( $orderid );
					$ced_rnx_shiping_address = $ced_rnx_order_for_label->get_formatted_shipping_address();
					if ( $ced_rnx_shiping_address == '' ) {
						$ced_rnx_shiping_address = $ced_rnx_order_for_label->get_formatted_billing_address();
					}
					$message1 = get_option( 'ced_rnx_return_ship_template' );
					$message1 = str_replace( '[username]', $fullname, $message1 );
					$message1 = str_replace( '[order]', '#' . $orderid, $message1 );
					$message1 = str_replace( '[siteurl]', home_url(), $message1 );
					$message1 = str_replace( '[Tracking_Id]', 'ID#' . $orderid, $message1 );
					$message1 = str_replace( '[Order_shipping_address]', $ced_rnx_shiping_address, $message1 );
					$message1 = str_replace( '[formatted_shipping_address]', $ced_rnx_order_for_label->get_formatted_shipping_address(), $message1 );
					$message1 = str_replace( '[formatted_billing_address]', $ced_rnx_order_for_label->get_formatted_billing_address(), $message1 );

					$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
					$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
					$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
					$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );

					$subject = str_replace( '[username]', $fullname, $subject );
					$subject = str_replace( '[order]', '#' . $orderid, $subject );
					$subject = str_replace( '[siteurl]', home_url(), $subject );
					$ced_rnx_restrict_mails = get_option( 'ced_rnx_exchange_restrict_customer_mails', true );
					if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
						wc_mail( $to, $subject, $message1, $headers );
					}
				}

				update_post_meta( $order_id, 'ced_rnx_exchange_order', $orderid );
				update_post_meta( $orderid, "date-$date", $order_id );
				update_post_meta( $orderid, 'mwb_rnx_status_exchanged', $mwb_dis_tot );

				$ex_fr = '';
			
				foreach ( $order_detail->get_items() as $item_id => $item ) {
				    	$fromk = 0;
					if ( isset( $exchanged_from_products ) && ! empty( $exchanged_from_products ) ) {
						foreach ( $exchanged_from_products as $k => $product_data ) {
							if ( $item['product_id'] == $product_data['product_id'] && $item['variation_id'] == $product_data['variation_id'] ) {
								$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
								if($product_data['qty']>1){
									$item['qty'] = $item['qty'] - $product_data['qty'];
								}else{

								}
								$args['qty'] = $item['qty'];
								$ex_fr = $ex_fr . $item['name'] . '(SKU : ' . $product->get_sku() . ') x ' . $product_data['qty'] . ' | ';
								if ( WC()->version < '3.0.0' ) {
									$order->update_product( $item_id, $product, $args );
								} else {
									wc_update_order_item_meta( $item_id, '_qty', $item['qty'] );

									if ( $product->backorders_require_notification() && $product->is_on_backorder( $args['qty'] ) ) {
										$item->add_meta_data( apply_filters( 'woocommerce_backordered_item_meta_name', __( 'Backordered', 'woocommerce' ) ), $args['qty'] - max( 0, $product->get_stock_quantity() ), true );
									}
									$item_data = $item->get_data();

									$price_excluded_tax = wc_get_price_excluding_tax( $product, array( 'qty' => 1 ) );
									$price_tax_excluded = $item_data['total'] / $item_data['quantity'];

									$args['subtotal'] = $price_excluded_tax * $args['qty'];
									$args['total']  = $price_tax_excluded * $args['qty'];

									$item->set_order_id( $orderid );
									$item->set_props( $args );
									$item->save();
								}
								$exchange_warranty['from'][$fromk]['order_id'] = (int)$orderid;
								$exchange_warranty['from'][$fromk]['material_code'] = $product->get_sku();

								//break;
							}
							$fromk++;
						}
					}
				}
				$order_detail->calculate_totals();
				// $order_detail->update_status( 'wc-completed' );
				$order_detail->update_status( 'wc-exchange-rejected' ); // old order status to be exchange approved - 19-11-22
				$order = (object) $order_detail->get_address( 'shipping' );

				// other info
				update_post_meta( $order_id, '_payment_method', $payment_method );
				update_post_meta( $order_id, '_payment_method_title', $payment_method_title );
				update_post_meta( $order_id, '_date_completed', $date_completed );
				update_post_meta( $order_id, '_date_paid', $date_paid );
				update_post_meta( $order_id, '_completed_date', $completed_date );
				update_post_meta( $order_id, '_cart_discount', $cart_discount );
				update_post_meta( $order_id, '_order_shipping', $order_shipping );
				update_post_meta( $order_id, '_transaction_id', $transaction_id );

				// Shipping info

				update_post_meta( $order_id, '_customer_user', $user_id );
				update_post_meta( $order_id, '_shipping_address_1', $order->address_1 );
				update_post_meta( $order_id, '_shipping_address_2', $order->address_2 );
				update_post_meta( $order_id, '_shipping_city', $order->city );
				update_post_meta( $order_id, '_shipping_state', $order->state );
				update_post_meta( $order_id, '_shipping_postcode', $order->postcode );
				update_post_meta( $order_id, '_shipping_country', $order->country );
				update_post_meta( $order_id, '_shipping_company', $order->company );
				update_post_meta( $order_id, '_shipping_first_name', $order->first_name );
				update_post_meta( $order_id, '_shipping_last_name', $order->last_name );
				update_post_meta( $order_id, '_shipping_email', $order->email );
				update_post_meta( $order_id, '_shipping_phone', $order->phone, true );
				update_post_meta( $order_id, '_shipping_salutation', $_shipping_salutation, true );

				// billing info

				$order_detail = wc_get_order( $orderid );
				$order_detail->calculate_totals();
				$order = (object) $order_detail->get_address( 'billing' );

				add_post_meta( $order_id, '_billing_first_name', $order->first_name, true );
				add_post_meta( $order_id, '_billing_last_name', $order->last_name, true );
				add_post_meta( $order_id, '_billing_company', $order->company, true );
				add_post_meta( $order_id, '_billing_address_1', $order->address_1, true );
				add_post_meta( $order_id, '_billing_address_2', $order->address_2, true );
				add_post_meta( $order_id, '_billing_city', $order->city, true );
				add_post_meta( $order_id, '_billing_state', $order->state, true );
				add_post_meta( $order_id, '_billing_postcode', $order->postcode, true );
				add_post_meta( $order_id, '_billing_country', $order->country, true );
				add_post_meta( $order_id, '_billing_email', $order->email, true );
				add_post_meta( $order_id, '_billing_phone', $order->phone, true );
				add_post_meta( $order_id, '_billing_salutation', $_billing_salutation, true );

				// $exchanged_products

				$order = wc_get_order( $order_id );
				if ( WC()->version >= '3.0.0' ) {
					if ( ! $order->get_order_key() ) {
						update_post_meta( $order_id, '_order_key', 'wc-' . uniqid( 'order_' ) );
					}
				}
				$orders = wc_get_order( $order_id );
				$new_url = $orders->get_checkout_order_received_url();
				$message = str_replace( '[new_order_id_created]', '#' . $order_id, $message );
				$message = str_replace( '[new_order_typ_url]', $new_url, $message );

				if ( isset( $ced_rnx_notification_exchange_approve_template ) && $ced_rnx_notification_exchange_approve_template == 'on' ) {
					$html_content = $message;
				} else {
					$ced_flag = true;

				}
				if ( $ced_flag ) {
					$html_content = $this->create_exchange_approve_mail_html( $mail_header, $message, $orderid, $order_id, $exchange_details, $mail_footer );
				}
				$left_amount = get_post_meta( $orderid, 'ced_rnx_left_amount', true );
				if ( $left_amount > 0 ) {

					$new_fee  = new WC_Order_Item_Fee();
					$new_fee->set_name( esc_attr( 'Refundable Amount' ) );
					$new_fee->set_total( $left_amount );
					$new_fee->set_tax_class( '' );
					$new_fee->set_tax_status( 'none' );
					$new_fee->save();
					$item_id = $order_detail->add_item( $new_fee );
					$order_detail->calculate_totals();
				}
				$headers = array();
				$headers[] = "From: $fname <$fmail>";
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$subject = get_option( 'ced_rnx_notification_exchange_approve_subject' );

				$to = get_post_meta( $orderid, '_billing_email', true );

				$subject = str_replace( '[username]', $fullname, $subject );
				$subject = str_replace( '[order]', '#' . $orderid, $subject );
				$subject = str_replace( '[siteurl]', home_url(), $subject );

				$ced_rnx_restrict_mails = get_option( 'ced_rnx_exchange_restrict_customer_mails', true );
				if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
					wc_mail( $to, $subject, $html_content, $headers );
				}
				$ex_to = '';
				$tok = 0;
				if ( isset( $exchanged_products ) && ! empty( $exchanged_products ) ) {
					foreach ( $exchanged_products as $exchanged_product ) {
					    
						if ( isset( $exchanged_product['variation_id'] ) ) {
							$product = wc_get_product( $exchanged_product['variation_id'] );
							
							$variation_product = new WC_Product_Variation( $exchanged_product['variation_id'] );
							$variation_attributes = $variation_product->get_variation_attributes();
							if ( isset( $exchanged_product['variations'] ) && ! empty( $exchanged_product['variations'] ) ) {
								$variation_attributes = $exchanged_product['variations'];
							}
							$variation_product_price = wc_get_price_excluding_tax( $variation_product, array( 'qty' => 1 ) );

							$variation_att['variation'] = $variation_attributes;

							$variation_att['totals']['subtotal'] = $exchanged_product['qty'] * $variation_product_price;
							$variation_att['totals']['total'] = $exchanged_product['qty'] * $variation_product_price;

							$item_id = $order->add_product( $variation_product, $exchanged_product['qty'], $variation_att );
// print_r($product->managing_stock());
// 							die;
							if ( $product->managing_stock() ) {
								$qty       = $exchanged_product['qty'];
								$new_stock = $product->reduce_stock( $qty );
							}
							
							$ex_to = $ex_to . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $exchanged_product['qty'] . ' | ';
						} elseif ( isset( $exchanged_product['id'] ) ) {
							$product = wc_get_product( $exchanged_product['id'] );
							$item_id = $order->add_product( $product, $exchanged_product['qty'] );

							if ( $product->managing_stock() ) {
								$qty       = $exchanged_product['qty'];
								$new_stock = $product->reduce_stock( $qty );
							}
							$ex_to = $ex_to . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $exchanged_product['qty'] . ' | ';
						} else {
							$product = wc_get_product( $exchanged_product['id'] );
							$item_id = $order->add_product( $product, $exchanged_product['qty'] );
							if ( $product->managing_stock() ) {
								$qty       = $exchanged_product['qty'];
								$new_stock = $product->reduce_stock( $qty );
							}
							$ex_to = $ex_to . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $exchanged_product['qty'] . ' | ';
						}

						$exchange_warranty['to'][$tok]['order_id'] = (int)$order_id;
						$exchange_warranty['to'][$tok]['material_code'] = "rejected_".$product->get_sku();

						$tok++;
					}
				}
				$ex_fr = trim( $ex_fr, '| ' );
				$ex_to = trim( $ex_to, '| ' );
				$exchange_note = __( 'Product Exchange Request from', 'woocommerce-refund-and-exchange' ) . ' { ' . $ex_fr . ' } ' . __( 'to', 'woocommerce-refund-and-exchange' ) . ' { ' . $ex_to . ' } ' . __( ' has been rejected.', 'woocommerce-refund-and-exchange' );
				wc_get_order( $orderid )->add_order_note( $exchange_note );

				if ( isset( $added_fee ) && ! empty( $added_fee ) ) {
					if ( is_array( $added_fee ) ) {
						foreach ( $added_fee as $fee ) {

							$new_fee  = new WC_Order_Item_Fee();
							$new_fee->set_name( esc_attr( $fee['text'] ) );
							$new_fee->set_total( $fee['val'] );
							$new_fee->set_tax_class( '' );
							$new_fee->set_tax_status( 'none' );
							$new_fee->set_total_tax( $totalProducttax );
							$new_fee->save();
							$item_id = $order->add_item( $new_fee );
						}
					}
				}
				$discount = 0;
				if ( isset( $exchanged_from_products ) && ! empty( $exchanged_from_products ) ) {
					$totalProducttax = '';
					$exchanged_from_products_count = count( $exchanged_from_products );
					$l_amount = $left_amount / $exchanged_from_products_count;
					foreach ( $exchanged_from_products as $exchanged_product ) {
						if ( isset( $exchanged_product['variation_id'] ) && $exchanged_product['variation_id'] > 0 ) {
							$p = wc_get_product( $exchanged_product['variation_id'] );
						} else {
							$p = wc_get_product( $exchanged_product['product_id'] );
						}
						if ( true ) {
							$_tax = new WC_Tax();

							$prePrice = $p->get_price_excluding_tax();
							$pTax = $exchanged_product['qty'] * ( $p->get_price() - $prePrice );
							$totalProducttax += $pTax;
							$item_rate = round( array_shift( $rates ) );
							$price = $exchanged_product['qty'] * $prePrice;
							$discount += $price;
							$tax_rates = WC_Tax::get_rates( $p->get_tax_class() );
							if ( ! empty( $tax_rates ) ) {
								$tax_rate = reset( $tax_rates );

								$dis_tax = $tax_rate['rate'];
							}
						} else {
							$price = $exchanged_product['qty'] * $exchanged_product['price'];
							$discount += $price;
						}
					}
				}
				$dis_tax_amu = 0;
				if ( $left_amount > 0 ) {
					$mwb_rnx_obj = $order;
					$amount_discount = $mwb_rnx_obj->calculate_totals();
					$total_ptax = $mwb_rnx_obj->get_total_tax();
					$amount_discount = $amount_discount - $total_ptax;

					$new_fee  = new WC_Order_Item_Fee();
					$new_fee->set_name( esc_attr( 'Discount' ) );
					$new_fee->set_total( -$amount_discount );
					$new_fee->set_tax_class( '' );
					$new_fee->set_tax_status( 'none' );
					$new_fee->set_total_tax( '' );
					$new_fee->save();
					$item_id = $order->add_item( $new_fee );

				} else {
					if ( $discount > 0 ) {

						$mwb_rnx_obj = wc_get_order( $orderid );
						$tax_rate = 0;
						$tax = new WC_Tax();
						$country_code = WC()->countries->countries[ $mwb_rnx_obj->billing_country ]; // or populate from order to get applicable rates
						$rates = $tax->find_rates( array( 'country' => $country_code ) );
						foreach ( $rates as $rate ) {
							$tax_rate = $rate['rate'];
						}

						$total_ptax = $discount * $tax_rate / 100;
						$orderval = $discount + $total_ptax;
						$orderval = round( $orderval, 2 );

						// Coupons used in the order LOOP (as they can be multiple)
						if ( WC()->version < '3.7.0' ) {
							$coupon_used = $mwb_rnx_obj->get_used_coupons();
						} else {
							$coupon_used = $mwb_rnx_obj->get_coupon_codes();
						}
						foreach ( $coupon_used as $coupon_name ) {
							$coupon_post_obj = get_page_by_title( $coupon_name, OBJECT, 'shop_coupon' );
							$coupon_id = $coupon_post_obj->ID;
							$coupons_obj = new WC_Coupon( $coupon_id );

							 $coupons_amount = $coupons_obj->get_amount();
							 $coupons_type = $coupons_obj->get_discount_type();
							if ( $coupons_type == 'percent' ) {
								$finaldiscount = $orderval * $coupons_amount / 100;
							}
						}

						$discount = $orderval;
						$discount = $discount * 100 / ( 100 + $tax_rate );

						$new_fee  = new WC_Order_Item_Fee();
						$new_fee->set_name( esc_attr( 'Discount' ) );
						$new_fee->set_total( -$discount );
						$new_fee->set_tax_class( '' );
						$new_fee->set_tax_status( 'none' );
						$new_fee->set_total_tax( '' );
						$new_fee->save();
						$order->add_item( $new_fee );
						$items_key = $new_fee->get_id();
						$dis_tax_amu = ( $discount * $dis_tax ) / 100;
					}
				}

				$order_total = $order->calculate_totals();
				$order_total = $dis_tax_amu + $order_total;
				$order->set_total( $order_total, 'total' );

				if ( $order_total == 0 ) {
					$order->update_status( 'wc-processing' );
				} else {
					$manage_stock = get_option( 'ced_rnx_exchange_request_manage_stock' );
					if ( $manage_stock == 'yes' ) {
						if ( isset( $exchanged_products ) && ! empty( $exchanged_products ) ) {
							foreach ( $exchanged_products as $key => $prod_data ) {
								if ( $prod_data['variation_id'] > 0 ) {
									$product = wc_get_product( $prod_data['variation_id'] );
								} else {
									$product = wc_get_product( $prod_data['id'] );
								}
								if ( $product->managing_stock() ) {
									$avaliable_qty = $prod_data['qty'];
									if ( $prod_data['variation_id'] > 0 ) {
										$total_stock = get_post_meta( $prod_data['variation_id'], '_stock', true );
										$total_stock = $total_stock - $avaliable_qty;
										wc_update_product_stock( $prod_data['variation_id'], $total_stock, 'set' );
									} else {
										$total_stock = get_post_meta( $prod_data['id'], '_stock', true );
										$total_stock = $total_stock - $avaliable_qty;
										wc_update_product_stock( $prod_data['id'], $total_stock, 'set' );
									}
								}
							}
						}
					}
				}
				if ( $includeTax ) {
					$order_total = $order_total - $totalProducttax;
				}
				update_post_meta( $order_id, 'ced_rnx_exchange_warrantry_extrajson', $exchange_warranty_display ); 
				update_post_meta( $order_id, 'ced_rnx_exchange_warrantry', $exchange_warranty ); // extra json store
				update_post_meta( $orderid, 'ced_rnx_exchange_product', $exchange_details );

			}

		}

		/**
		 * Exchange cancel callback.
		 *
		 * @name ced_rnx_coupon_regenertor
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function ced_rnx_exchange_req_cancel_callback() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$orderid = $_POST['orderid'];
				$date = $_POST['date'];

				$products = get_post_meta( $orderid, 'ced_rnx_exchange_product', true );

				// Fetch the return request product
				if ( isset( $products ) && ! empty( $products ) ) {
					foreach ( $products as $date => $product ) {
						if ( $product['status'] == 'pending' ) {
							$products[ $date ]['status'] = 'cancel';
							$approvdate = date( 'd-m-Y' );
							$products[ $date ]['cancel_date'] = $approvdate;
							break;
						}
					}
				}

				// Update the status
				update_post_meta( $orderid, 'ced_rnx_exchange_product', $products );

				$order = new WC_Order( $orderid );
				$fname = get_option( 'ced_rnx_notification_from_name' );
				$fmail = get_option( 'ced_rnx_notification_from_mail' );

				$headers[] = "From: $fname <$fmail>";
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$to = get_post_meta( $orderid, '_billing_email', true );
				$subject = get_option( 'ced_rnx_notification_exchange_cancel_subject', false );
				$message = stripslashes( get_option( 'ced_rnx_notification_exchange_cancel', false ) );
				$message = apply_filters( 'mwb_rnx_meta_content', $message );

				$order_id = $orderid;
				$fname = get_post_meta( $orderid, '_billing_first_name', true );
				$lname = get_post_meta( $orderid, '_billing_last_name', true );
				$billing_company = get_post_meta( $order_id, '_billing_company', true );
				$billing_email = get_post_meta( $order_id, '_billing_email', true );
				$billing_phone = get_post_meta( $order_id, '_billing_phone', true );
				$billing_country = get_post_meta( $order_id, '_billing_country', true );
				$billing_address_1 = get_post_meta( $order_id, '_billing_address_1', true );
				$billing_address_2 = get_post_meta( $order_id, '_billing_address_2', true );
				$billing_state = get_post_meta( $order_id, '_billing_state', true );
				$billing_postcode = get_post_meta( $order_id, '_billing_postcode', true );
				$shipping_first_name = get_post_meta( $order_id, '_shipping_first_name', true );
				$shipping_last_name = get_post_meta( $order_id, '_shipping_last_name', true );
				$shipping_company = get_post_meta( $order_id, '_shipping_company', true );
				$shipping_country = get_post_meta( $order_id, '_shipping_country', true );
				$shipping_address_1 = get_post_meta( $order_id, '_shipping_address_1', true );
				$shipping_address_2 = get_post_meta( $order_id, '_shipping_address_2', true );
				$shipping_city = get_post_meta( $order_id, '_shipping_city', true );
				$shipping_state = get_post_meta( $order_id, '_shipping_state', true );
				$shipping_postcode = get_post_meta( $order_id, '_shipping_postcode', true );
				$payment_method_tittle = get_post_meta( $order_id, '_payment_method_title', true );
				$order_shipping = get_post_meta( $order_id, '_order_shipping', true );
				$order_total = get_post_meta( $order_id, '_order_total', true );
				$refundable_amount = get_post_meta( $order_id, 'refundable_amount', true );

				$fullname = $fname . ' ' . $lname;

				$message = str_replace( '[username]', $fullname, $message );
				$message = str_replace( '[order]', '#' . $orderid, $message );
				$message = str_replace( '[siteurl]', home_url(), $message );
				$message = str_replace( '[_billing_company]', $billing_company, $message );
				$message = str_replace( '[_billing_email]', $billing_email, $message );
				$message = str_replace( '[_billing_phone]', $billing_phone, $message );
				$message = str_replace( '[_billing_country]', $billing_country, $message );
				$message = str_replace( '[_billing_address_1]', $billing_address_1, $message );
				$message = str_replace( '[_billing_address_2]', $billing_address_2, $message );
				$message = str_replace( '[_billing_state]', $billing_state, $message );
				$message = str_replace( '[_billing_postcode]', $billing_postcode, $message );
				$message = str_replace( '[_shipping_first_name]', $shipping_first_name, $message );
				$message = str_replace( '[_shipping_last_name]', $shipping_last_name, $message );
				$message = str_replace( '[_shipping_company]', $shipping_company, $message );
				$message = str_replace( '[_shipping_country]', $shipping_country, $message );
				$message = str_replace( '[_shipping_address_1]', $shipping_address_1, $message );
				$message = str_replace( '[_shipping_address_2]', $shipping_address_2, $message );
				$message = str_replace( '[_shipping_city]', $shipping_city, $message );
				$message = str_replace( '[_shipping_state]', $shipping_state, $message );
				$message = str_replace( '[_shipping_postcode]', $shipping_postcode, $message );
				$message = str_replace( '[_payment_method_tittle]', $payment_method_tittle, $message );
				$message = str_replace( '[_order_shipping]', $order_shipping, $message );
				$message = str_replace( '[_order_total]', $order_total, $message );
				$message = str_replace( '[_refundable_amount]', $refundable_amount, $message );
				$message = str_replace( '[formatted_shipping_address]', $order->get_formatted_shipping_address(), $message );
				$message = str_replace( '[formatted_billing_address]', $order->get_formatted_billing_address(), $message );

				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
				$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );

				$subject = str_replace( '[username]', $fullname, $subject );
				$subject = str_replace( '[order]', '#' . $orderid, $subject );
				$subject = str_replace( '[siteurl]', home_url(), $subject );

				$mail_header = str_replace( '[username]', $fullname, $mail_header );
				$mail_header = str_replace( '[order]', '#' . $orderid, $mail_header );
				$mail_header = str_replace( '[siteurl]', home_url(), $mail_header );

				$template = get_option( 'ced_rnx_notification_exchange_cancel_template', 'no' );

				if ( isset( $template ) && $template == 'on' ) {

					$html_content = $message;
				} else {
					$html_content = '<html>
										<head>
											<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
											<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
										</head>
										<body>
											<table cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td style="text-align: center; margin-top: 30px; margin-bottom: 10px; color: #99B1D8; font-size: 12px;">
														' . $mail_header . '
													</td>
												</tr>
												<tr>
													<td>
														<table align="center" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-family:Open Sans; max-width: 600px; width: 100%;">
															<tr>
																<td style="padding: 36px 48px; width: 100%; background-color:#557DA1;color: #fff; font-size: 30px; font-weight: 300; font-family:helvetica;">' . $subject . '</td>
															</tr>
															<tr>
																<td style="width:100%; padding: 36px 48px 10px; background-color:#fdfdfd; font-size: 14px; color: #737373;">' . $message . '</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td style="text-align: center; margin-top: 30px; color: #99B1D8; font-size: 12px;">
														' . $mail_footer . '
													</td>
												</tr>
											</table>
										</body>
									</html>';
				}

				$ced_rnx_restrict_mails = get_option( 'ced_rnx_exchange_restrict_customer_mails', true );
				if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
					$status = wc_mail( $to, $subject, $html_content, $headers );
				}
				$order->update_status( 'wc-exchange-cancel', 'User Request of Exchange Product is Cancelled.' );

				$response['response'] = 'success';
				echo json_encode( $response );
				wp_die();
			}

		}


		/**
		 * This function is enable cancel for exchange approved order
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */

		function ced_rnx_order_can_cancel( $status ) {
			$status[] = 'exchange-approve';
			return $status;
		}

		/**
		 * This function is enable Payment for exchange approved order
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */

		function ced_rnx_order_need_payment( $status ) {
			$status[] = 'exchange-approve';
			return $status;
		}

		/**
		 * This function is update order number listing for exhanged Order
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function ced_rnx_update_order_number_callback( $order_id ) {
			$order_warranty_id = get_post_meta( $order_id, 'ced_rnx_warranty_order', true );
			if ( isset( $order_warranty_id ) && ! empty( $order_warranty_id ) ) {
				$order_id = $order_id . ' → ' . $order_warranty_id;
			}
			$orderid = get_post_meta( $order_id, 'ced_rnx_exchange_order', true );
			if ( isset( $orderid ) && ! empty( $orderid ) ) {
				$order_id = $order_id . ' → ' . $orderid;
			}

			return $order_id;
		}

		/**
		 * This function is add Meta box for Return Product on order detail at admin
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		public function ced_rnx_product_return_meta_box() {
			 add_meta_box( 'ced_rnx_order_return', __( 'Warranty Requested Products', 'woocommerce-refund-and-exchange' ), array( $this, 'ced_rnx_order_return' ), 'shop_order' );
			add_meta_box( 'ced_rnx_order_exchange', __( 'Exchange Products Requested', 'woocommerce-refund-and-exchange' ), array( $this, 'ced_rnx_order_exchange' ), 'shop_order' );
			add_meta_box( 'ced_rnx_order_msg_history', __( 'Order Message History', 'woocommerce-refund-and-exchange' ), array( $this, 'ced_rnx_order_msg_history' ), 'shop_order' );

			// cancel in admin panel
			add_meta_box( 'ced_rnx_order_cancel', __( 'Cancel Requested Products', 'woocommerce-refund-and-exchange' ), array( $this, 'ced_rnx_order_cancel' ), 'shop_order' );
		}
		// Change add cancel product history in admin panel aarti - 03-10-22
		public function ced_rnx_order_cancel() {
			global $post, $thepostid, $theorder;

			include_once CED_REFUND_N_EXCHANGE_DIRPATH . 'admin/ced-rnx-cancel-product-meta.php';
		}


		/**
		 * This function is add Meta box for Exchange Product on order detail at admin
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function ced_rnx_order_exchange() {
			 global $post, $thepostid, $theorder;
			include_once CED_REFUND_N_EXCHANGE_DIRPATH . 'admin/ced-rnx-exchange-product-meta.php';
		}

		/**
		 * This function is metabox template for Refund order product
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 * @param unknown $order
		 */
		public function ced_rnx_order_return() {
			global $post, $thepostid, $theorder;
			include_once CED_REFUND_N_EXCHANGE_DIRPATH . 'admin/ced-rnx-return-product-meta.php';
		}

		/**
		 * This function is add cs and js to order meta
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 * @param unknown $order
		 */
		public function ced_rnx_admin_scripts() {
			$wallet_enable = false;
			$screen = get_current_screen();
			if ( isset( $screen->id ) ) {
				if ( $screen->id == 'shop_order' ) {
					$customer_id = ( $value = get_post_meta( $_GET['post'], '_customer_user', true ) ) ? absint( $value ) : 0;
					$wallet_enabled = get_option( 'ced_rnx_return_wallet_enable', false );
					if ( $wallet_enabled == 'yes' && $customer_id > 0 ) {
						$wallet_enable = true;
					}
				}
			}
			$url = plugins_url();
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'ced-rnx-notification' ) {

				wp_enqueue_style( 'ced-rnx-style-jqueru-ui', CED_REFUND_N_EXCHANGE_URL . 'assets/css/jquery-ui.css' );
				wp_enqueue_style( 'ced-rnx-style-timepicker', CED_REFUND_N_EXCHANGE_URL . 'assets/css/jquery.ui.timepicker.css' );
				wp_enqueue_script( 'ced-rnx-script-timepicker', CED_REFUND_N_EXCHANGE_URL . 'assets/js/jquery.ui.timepicker.js', array( 'jquery' ), CED_REFUND_N_EXCHANGE_VERSION, true );
			}
			wp_dequeue_style( 'select2' );
			wp_deregister_style( 'select2' );
			wp_dequeue_script( 'select2' );
			wp_deregister_script( 'select2' );

			wp_register_script( 'ced-rnx-script-admin', CED_REFUND_N_EXCHANGE_URL . 'assets/js/ced-rnx-admin-script.js', array( 'jquery' ), CED_REFUND_N_EXCHANGE_VERSION, true );
			$ajax_nonce = wp_create_nonce( 'ced-rnx-ajax-seurity-string' );
			$translation_array = array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'wallet'  => $wallet_enable,
				'defuat_catalog_name' => __( 'Default Catalog', 'woocommerce-refund-and-exchange' ),
				'catalog_name' => __( 'Catalog Name:', 'woocommerce-refund-and-exchange' ),
				'select_catalog_product' => __( 'Select Catalog Products:', 'woocommerce-refund-and-exchange' ),
				'maximum_catalog_refund_days' => __( 'Maximum Refund Days:', 'woocommerce-refund-and-exchange' ),
				'maximum_catalog_exchange_days' => __( 'Maximum Exchange Days:', 'woocommerce-refund-and-exchange' ),
				'placeholder_exchange' => __( 'Enter Exchange Days', 'woocommerce-refund-and-exchange' ),
				'placeholder_refund' => __( 'Enter Refund Days', 'woocommerce-refund-and-exchange' ),
				'placeholder_catalog_name' => __( 'Enter Catalog Name', 'woocommerce-refund-and-exchange' ),
				'catalog_disable' => __( 'If value is 0 then catalog will not work.', 'woocommerce-refund-and-exchange' ),
				'ced_rnx_nonce' => $ajax_nonce,
				'ced_rnx_currency_symbol' => get_woocommerce_currency_symbol(),
				'remove'                => __( 'Remove', 'woocommerce-refund-and-exchange' ),
				'message_sent' => __( 'Message has been sent successfully', 'woocommerce-refund-and-exchange' ),
				'message_empty' => __( 'Please enter a Message.', 'woocommerce-refund-and-exchange' ),
			);
			wp_localize_script( 'ced-rnx-script-admin', 'global_rnx', $translation_array );

			if ( $screen->id == 'woocommerce_page_ced-rnx-notification' || ( isset( $_GET['tab'] ) && $_GET['tab'] == 'ced_rnx_setting' ) || $screen->id == 'edit-shop_order' || $screen->id == 'shop_order' || $screen->id == 'users' || $screen->id == 'profile' || 'user-edit' == $screen->id || isset( $_GET['page'] ) && $_GET['page'] == 'ced-rnx-notification' ) {
				wp_enqueue_style( 'ced-rnx-style-admin', CED_REFUND_N_EXCHANGE_URL . 'assets/css/ced-rnx-admin.css' );
				wp_enqueue_script( 'ced-rnx-script-admin' );
				$ced_rnx_side = array(
					'ced_rnx_URL' => CED_REFUND_N_EXCHANGE_URL,
					'Hide_sidebar' => __( 'Hide Sidebar', 'woocommerce-refund-and-exchange' ),
					'Show_sidebar' => __( 'Show Sidebar', 'woocommerce-refund-and-exchange' ),
					'button_text' => __( 'View More Features', 'woocommerce-refund-and-exchange' ),
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
				);
				wp_register_script( 'ced_rnx_sidebar_script', CED_REFUND_N_EXCHANGE_URL . 'assets/js/mwb_get_sidebar.js', array( 'jquery', 'wp-color-picker' ), CED_REFUND_N_EXCHANGE_VERSION, false );
				wp_localize_script( 'ced_rnx_sidebar_script', 'ced_rnx_side', $ced_rnx_side );
				wp_enqueue_script( 'ced_rnx_sidebar_script' );
			}
		}

		/**
		 * This function is add extra fee to Refund product
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function ced_rnx_return_fee_add_callback() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$orderid = $_POST['orderid'];
				$pending_date = $_POST['date'];
				$fees = $_POST['fees'];

				if ( isset( $fees ) ) {
					foreach ( $fees as $k => $fee ) {
						if ( $fee['text'] == '' || $fee['val'] == '' ) {
							unset( $fees[ $k ] );
						}
					}
				}
				$added_fees = get_post_meta( $orderid, 'ced_rnx_return_added_fee', array() );
				$exist = true;
				if ( isset( $added_fees ) && ! empty( $added_fees ) ) {
					foreach ( $added_fees as $date => $added_fee ) {
						if ( $date == $pending_date ) {
							$added_fees[ $pending_date ] = $fees;
							$exist = false;
							break;
						}
					}
				}

				if ( $exist ) {
					$added_fees[ $pending_date ] = $fees;
				}

				update_post_meta( $orderid, 'ced_rnx_return_added_fee', $added_fees );
				$response['response'] = 'success';
				echo json_encode( $response );
				wp_die();
			}
		}

		/**
		 * This function is approve return request and decrease product quantity from order
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function ced_warranty_req_approve_first_level(){
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$orderid = $_POST['orderid'];
				$checkdate = $_POST['date'];
				$selected_product = $_POST['selected_product'];
				$warranty_details = get_post_meta( $orderid, 'ced_rnx_return_product', true );
				if ( isset( $warranty_details ) && ! empty( $warranty_details ) ) {
					foreach ( $warranty_details as $date => $warranty_detail_value ) {
						$exchanged_products_shiprocket = $warranty_detail_value['products'];
						if ( $warranty_detail_value['status'] == 'pending' ) {
							$warranty_details[ $date ]['status_pickup'] = 'reverse_pickup_approved';
							$warranty_details[ $date ]['approve_pickup'] = date( 'd-m-Y' );
							break;
						}
					}
					foreach ( $warranty_details as $date => $exchange_detail ) {
						foreach ($selected_product as $key_exchange => $value_exchange){
							if($value_exchange['approve_status'] == '1'){
								$warranty_details[ $date ]['products'][$key_exchange]['approve_status'] = 'Accept';
								$warranty_details[ $date ]['products'][$key_exchange]['approve_status_key'] = $value_exchange['approve_status'];
							}else if($value_exchange['approve_status'] == '2'){
								unset($exchanged_products_shiprocket[$key_exchange]);
								$warranty_details[ $date ]['products'][$key_exchange]['approve_status'] = 'Reject';
								$warranty_details[ $date ]['products'][$key_exchange]['approve_status_key'] = $value_exchange['approve_status'];
							}							
						}
					}
				}
				//$this->return_shiprock_api($orderid,$exchanged_products_shiprocket);
				$order_detail = wc_get_order( $orderid );				
				$order_detail->update_status('reverse_pickup');
				update_post_meta( $orderid, 'ced_rnx_return_product', $warranty_details );
			}
		}
		function ced_rnx_warranty_req_approve_callback(){
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$orderid = $_POST['orderid'];
				$checkdate = $_POST['date'];
				$warranty_details = get_post_meta( $orderid, 'ced_rnx_return_product', true );
				$selected_product = $_POST['selected_product'];
				$exchange_warranty = array();
				$exchange_warranty_display = array();
				if ( isset( $warranty_details ) && ! empty( $warranty_details ) ) {
					foreach ( $warranty_details as $date => $exchange_detail ) {
						if ($exchange_detail['status_pickup'] == 'reverse_pickup_approved') {

							$exchanged_products = $exchange_detail['products'];
							$exchanged_from_products = $exchange_detail['products'];

							$warranty_details[ $date ]['status'] = 'complete';
							$warranty_details[ $date ]['approve'] = date( 'd-m-Y' );
							break;
						}
					}
				}
				foreach ( $warranty_details as $date => $exchange_detail ) {
					foreach ($selected_product as $key_exchange => $value_exchange){
						if($value_exchange['approve_status'] == '1'){
							$warranty_details[ $date ]['products'][$key_exchange]['approve_status'] = 'Accept';
							$warranty_details[ $date ]['products'][$key_exchange]['approve_status_key'] = $value_exchange['approve_status'];
						}else if($value_exchange['approve_status'] == '2'){
							$warranty_details[ $date ]['products'][$key_exchange]['approve_status'] = 'Reject';
							$warranty_details[ $date ]['products'][$key_exchange]['approve_status_key'] = $value_exchange['approve_status'];
						}							
					}
				}
				foreach($exchanged_products as $keys => $kvalues){
				    foreach ($selected_product as $key_exchange => $value_exchange){
				        if($value_exchange['approve_status'] == '3'){
        				    if($kvalues['product_id'] == $value_exchange['product_id']){
        				        unset($exchanged_products[$keys]);
        				    }
				        }
				    }
				}
				foreach($exchanged_from_products as $keys_1 => $kvalues_1){
				    foreach ($selected_product as $key_exchange => $value_exchange){
				        if($value_exchange['approve_status'] == '3'){
        				    if($kvalues_1['product_id'] == $value_exchange['product_id']){
        				        unset($exchanged_from_products[$keys_1]);
        				    }
				        }
				    }
				}
				// echo "<pre>";
				// print_r($exchanged_products);
				// echo "<pre>";
				// print_r($exchanged_from_products);
				// die;
				$exchange_warranty['status'] = 'warranty-accepted';
				$exchange_warranty_display['status'] = 'warranty-accepted';

				$order_detail = wc_get_order( $orderid );
				$includeTax = isset( $order_detail->prices_include_tax ) ? $order_detail->prices_include_tax : false;
				$user_id = $order_detail->user_id;
				$order_data = array(
					'post_name'     => 'order-' . date( 'M-d-Y-hi-a' ),
					'post_type'     => 'shop_order',
					'post_title'    => 'Order &ndash; ' . date( 'F d, Y @ h:i A' ), 
					'post_status'   => 'wc-warranty-accepted',
					'ping_status'   => 'closed',
					'post_excerpt'  => 'requested',
					'post_author'   => $user_id,
					'post_password' => uniqid( 'order_' ),
					'post_date'     => date( 'Y-m-d H:i:s e' ),
					'comment_status' => 'open',
				);
				$order_id = wp_insert_post( $order_data, true );
				$approve = get_option( 'ced_rnx_notification_exchange_approve' );
				$fname = get_post_meta( $orderid, '_billing_first_name', true );
				$lname = get_post_meta( $orderid, '_billing_last_name', true );

				$fullname = $fname . ' ' . $lname;

				$approve = str_replace( '[username]', $fullname, $approve );
				$approve = str_replace( '[order]', '#' . $orderid, $approve );
				$approve = str_replace( '[siteurl]', home_url(), $approve );
				$message = stripslashes( get_option( 'ced_rnx_notification_exchange_approve', false ) );
				$message = apply_filters( 'mwb_rnx_meta_content', $message );

				$fname = get_post_meta( $orderid, '_billing_first_name', true );
				$lname = get_post_meta( $orderid, '_billing_last_name', true );
				$billing_company = get_post_meta( $orderid, '_billing_company', true );
				$billing_email = get_post_meta( $orderid, '_billing_email', true );
				$billing_phone = get_post_meta( $orderid, '_billing_phone', true );
				$billing_country = get_post_meta( $orderid, '_billing_country', true );
				$billing_address_1 = get_post_meta( $orderid, '_billing_address_1', true );
				$billing_address_2 = get_post_meta( $orderid, '_billing_address_2', true );
				$billing_state = get_post_meta( $orderid, '_billing_state', true );
				$billing_postcode = get_post_meta( $orderid, '_billing_postcode', true );
				$shipping_first_name = get_post_meta( $orderid, '_shipping_first_name', true );
				$shipping_last_name = get_post_meta( $orderid, '_shipping_last_name', true );
				$shipping_company = get_post_meta( $orderid, '_shipping_company', true );
				$shipping_country = get_post_meta( $orderid, '_shipping_country', true );
				$shipping_address_1 = get_post_meta( $orderid, '_shipping_address_1', true );
				$shipping_address_2 = get_post_meta( $orderid, '_shipping_address_2', true );
				$shipping_city = get_post_meta( $orderid, '_shipping_city', true );
				$shipping_state = get_post_meta( $orderid, '_shipping_state', true );
				$shipping_postcode = get_post_meta( $orderid, '_shipping_postcode', true );
				$payment_method_tittle = get_post_meta( $orderid, '_payment_method_title', true );
				$order_shipping = get_post_meta( $orderid, '_order_shipping', true );
				$order_total = get_post_meta( $orderid, '_order_total', true );
				$refundable_amount = get_post_meta( $orderid, 'refundable_amount', true );

				// get title data
				$_billing_salutation = get_post_meta( $orderid, '_billing_salutation', true );
				$_shipping_salutation = get_post_meta( $orderid, '_shipping_salutation', true );

				// get other data 
				$payment_method = get_post_meta( $orderid, '_payment_method', true);
				$payment_method_title = get_post_meta( $orderid, '_payment_method_title', true);
				$date_completed = get_post_meta( $orderid, '_date_completed', true);
				$date_paid = get_post_meta( $orderid, '_date_paid', true);
				$completed_date = get_post_meta( $orderid, '_completed_date', true);
				$cart_discount = get_post_meta( $orderid, '_cart_discount', true);
				$order_shipping = get_post_meta( $orderid, '_order_shipping', true);
				$transaction_id = get_post_meta( $orderid, '_transaction_id', true);

				$fullname = $fname . ' ' . $lname;

				$message = str_replace( '[username]', $fullname, $message );
				$message = str_replace( '[order]', '#' . $orderid, $message );
				$message = str_replace( '[siteurl]', home_url(), $message );
				$message = str_replace( '[_billing_company]', $billing_company, $message );
				$message = str_replace( '[_billing_email]', $billing_email, $message );
				$message = str_replace( '[_billing_phone]', $billing_phone, $message );
				$message = str_replace( '[_billing_country]', $billing_country, $message );
				$message = str_replace( '[_billing_address_1]', $billing_address_1, $message );
				$message = str_replace( '[_billing_address_2]', $billing_address_2, $message );
				$message = str_replace( '[_billing_state]', $billing_state, $message );
				$message = str_replace( '[_billing_postcode]', $billing_postcode, $message );
				$message = str_replace( '[_shipping_first_name]', $shipping_first_name, $message );
				$message = str_replace( '[_shipping_last_name]', $shipping_last_name, $message );
				$message = str_replace( '[_shipping_company]', $shipping_company, $message );
				$message = str_replace( '[_shipping_country]', $shipping_country, $message );
				$message = str_replace( '[_shipping_address_1]', $shipping_address_1, $message );
				$message = str_replace( '[_shipping_address_2]', $shipping_address_2, $message );
				$message = str_replace( '[_shipping_city]', $shipping_city, $message );
				$message = str_replace( '[_shipping_state]', $shipping_state, $message );
				$message = str_replace( '[_shipping_postcode]', $shipping_postcode, $message );
				$message = str_replace( '[_payment_method_tittle]', $payment_method_tittle, $message );
				$message = str_replace( '[_order_shipping]', $order_shipping, $message );
				$message = str_replace( '[_order_total]', $order_total, $message );
				$message = str_replace( '[_refundable_amount]', $refundable_amount, $message );
				$ced_rnx_odr = wc_get_order( $orderid );
				$message = str_replace( '[formatted_shipping_address]', $ced_rnx_odr->get_formatted_shipping_address(), $message );
				$message = str_replace( '[formatted_billing_address]', $ced_rnx_odr->get_formatted_billing_address(), $message );

				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
				$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );
				$ced_rnx_notification_exchange_approve_template = get_option( 'ced_rnx_notification_exchange_approve_template', 'no' );
				$mwb_dis_tot = 0;
				$ced_flag = false;

				$fname = get_option( 'ced_rnx_notification_from_name' );
				$fmail = get_option( 'ced_rnx_notification_from_mail' );

				$ced_rnx_enable_return_ship_label = get_option( 'ced_rnx_enable_return_ship_label', 'no' );

				if ( $ced_rnx_enable_return_ship_label == 'on' ) {
					$headers = array();
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					$to = get_post_meta( $orderid, '_billing_email', true );
					$subject = get_option( 'ced_rnx_return_slip_mail_subject' );
					$subject = str_replace( '[order]', '#' . $orderid, $subject );
					$ced_rnx_order_for_label = wc_get_order( $orderid );
					$ced_rnx_shiping_address = $ced_rnx_order_for_label->get_formatted_shipping_address();
					if ( $ced_rnx_shiping_address == '' ) {
						$ced_rnx_shiping_address = $ced_rnx_order_for_label->get_formatted_billing_address();
					}
					$message1 = get_option( 'ced_rnx_return_ship_template' );
					$message1 = str_replace( '[username]', $fullname, $message1 );
					$message1 = str_replace( '[order]', '#' . $orderid, $message1 );
					$message1 = str_replace( '[siteurl]', home_url(), $message1 );
					$message1 = str_replace( '[Tracking_Id]', 'ID#' . $orderid, $message1 );
					$message1 = str_replace( '[Order_shipping_address]', $ced_rnx_shiping_address, $message1 );
					$message1 = str_replace( '[formatted_shipping_address]', $ced_rnx_order_for_label->get_formatted_shipping_address(), $message1 );
					$message1 = str_replace( '[formatted_billing_address]', $ced_rnx_order_for_label->get_formatted_billing_address(), $message1 );

					$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
					$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
					$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
					$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );

					$subject = str_replace( '[username]', $fullname, $subject );
					$subject = str_replace( '[order]', '#' . $orderid, $subject );
					$subject = str_replace( '[siteurl]', home_url(), $subject );
					$ced_rnx_restrict_mails = get_option( 'ced_rnx_exchange_restrict_customer_mails', true );
					if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
						wc_mail( $to, $subject, $message1, $headers );
					}
				}

				update_post_meta( $order_id, 'ced_rnx_warranty_order', $orderid );
				update_post_meta( $orderid, "date-$date", $order_id );
				update_post_meta( $orderid, 'mwb_rnx_status_warranty', $mwb_dis_tot );

				$ex_fr = '';
				$fromk = 0;
				foreach ( $order_detail->get_items() as $item_id => $item ) {
					if ( isset( $exchanged_from_products ) && ! empty( $exchanged_from_products ) ) {
						foreach ( $exchanged_from_products as $k => $product_data ) {
							if ( $item['product_id'] == $product_data['product_id'] && $item['variation_id'] == $product_data['variation_id'] ) {
								$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
								if($product_data['qty']>1){
									$item['qty'] = $item['qty'] - $product_data['qty'];
								}else{

								}
								$args['qty'] = $item['qty'];
								$ex_fr = $ex_fr . $item['name'] . '(SKU : ' . $product->get_sku() . ') x ' . $product_data['qty'] . ' | ';
								if ( WC()->version < '3.0.0' ) {
									$order->update_product( $item_id, $product, $args );
								} else {
									wc_update_order_item_meta( $item_id, '_qty', $item['qty'] );

									if ( $product->backorders_require_notification() && $product->is_on_backorder( $args['qty'] ) ) {
										$item->add_meta_data( apply_filters( 'woocommerce_backordered_item_meta_name', __( 'Backordered', 'woocommerce' ) ), $args['qty'] - max( 0, $product->get_stock_quantity() ), true );
									}
									$item_data = $item->get_data();

									$price_excluded_tax = wc_get_price_excluding_tax( $product, array( 'qty' => 1 ) );
									$price_tax_excluded = $item_data['total'] / $item_data['quantity'];

									$args['subtotal'] = $price_excluded_tax * $args['qty'];
									$args['total']  = $price_tax_excluded * $args['qty'];

									$item->set_order_id( $orderid );
									$item->set_props( $args );
									$item->save();
								}
								$fromk++;
								break;
							}
							
							
						}
					}
				}

				$order_detail->calculate_totals();
				// $order_detail->update_status( 'wc-completed' );
				$order_detail->update_status( 'wc-warranty-approved' ); // old order status to be exchange approved - 19-11-22
				$order = (object) $order_detail->get_address( 'shipping' );

				// other info
				update_post_meta( $order_id, '_payment_method', $payment_method );
				update_post_meta( $order_id, '_payment_method_title', $payment_method_title );
				update_post_meta( $order_id, '_date_completed', $date_completed );
				update_post_meta( $order_id, '_date_paid', $date_paid );
				update_post_meta( $order_id, '_completed_date', $completed_date );
				update_post_meta( $order_id, '_cart_discount', $cart_discount );
				update_post_meta( $order_id, '_order_shipping', $order_shipping );
				update_post_meta( $order_id, '_transaction_id', $transaction_id );


				// Shipping info
				update_post_meta( $order_id, '_customer_user', $user_id );
				update_post_meta( $order_id, '_shipping_address_1', $order->address_1 );
				update_post_meta( $order_id, '_shipping_address_2', $order->address_2 );
				update_post_meta( $order_id, '_shipping_city', $order->city );
				update_post_meta( $order_id, '_shipping_state', $order->state );
				update_post_meta( $order_id, '_shipping_postcode', $order->postcode );
				update_post_meta( $order_id, '_shipping_country', $order->country );
				update_post_meta( $order_id, '_shipping_company', $order->company );
				update_post_meta( $order_id, '_shipping_first_name', $order->first_name );
				update_post_meta( $order_id, '_shipping_last_name', $order->last_name );
				update_post_meta( $order_id, '_shipping_email', $order->email );
				update_post_meta( $order_id, '_shipping_phone', $order->phone, true );
				update_post_meta( $order_id, '_shipping_salutation', $_shipping_salutation, true );

				// billing info

				$order_detail = wc_get_order( $orderid );
				$order_detail->calculate_totals();
				$order = (object) $order_detail->get_address( 'billing' );

				add_post_meta( $order_id, '_billing_first_name', $order->first_name, true );
				add_post_meta( $order_id, '_billing_last_name', $order->last_name, true );
				add_post_meta( $order_id, '_billing_company', $order->company, true );
				add_post_meta( $order_id, '_billing_address_1', $order->address_1, true );
				add_post_meta( $order_id, '_billing_address_2', $order->address_2, true );
				add_post_meta( $order_id, '_billing_city', $order->city, true );
				add_post_meta( $order_id, '_billing_state', $order->state, true );
				add_post_meta( $order_id, '_billing_postcode', $order->postcode, true );
				add_post_meta( $order_id, '_billing_country', $order->country, true );
				add_post_meta( $order_id, '_billing_email', $order->email, true );
				add_post_meta( $order_id, '_billing_phone', $order->phone, true );
				add_post_meta( $order_id, '_billing_salutation', $_billing_salutation, true );

				// $exchanged_products

				$order = wc_get_order( $order_id );
				if ( WC()->version >= '3.0.0' ) {
					if ( ! $order->get_order_key() ) {
						update_post_meta( $order_id, '_order_key', 'wc-' . uniqid( 'order_' ) );
					}
				}
				$orders = wc_get_order( $order_id );
				$new_url = $orders->get_checkout_order_received_url();
				$message = str_replace( '[new_order_id_created]', '#' . $order_id, $message );
				$message = str_replace( '[new_order_typ_url]', $new_url, $message );

				if ( isset( $ced_rnx_notification_exchange_approve_template ) && $ced_rnx_notification_exchange_approve_template == 'on' ) {
					$html_content = $message;
				} else {
					$ced_flag = true;

				}
				if ( $ced_flag ) {
					$html_content = $this->create_exchange_approve_mail_html( $mail_header, $message, $orderid, $order_id, $exchange_details, $mail_footer );
				}
				$left_amount = get_post_meta( $orderid, 'ced_rnx_left_amount', true );
				if ( $left_amount > 0 ) {

					$new_fee  = new WC_Order_Item_Fee();
					$new_fee->set_name( esc_attr( 'Refundable Amount' ) );
					$new_fee->set_total( $left_amount );
					$new_fee->set_tax_class( '' );
					$new_fee->set_tax_status( 'none' );
					$new_fee->save();
					$item_id = $order_detail->add_item( $new_fee );
					$order_detail->calculate_totals();
				}
				$headers = array();
				$headers[] = "From: $fname <$fmail>";
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$subject = get_option( 'ced_rnx_notification_exchange_approve_subject' );

				$to = get_post_meta( $orderid, '_billing_email', true );

				$subject = str_replace( '[username]', $fullname, $subject );
				$subject = str_replace( '[order]', '#' . $orderid, $subject );
				$subject = str_replace( '[siteurl]', home_url(), $subject );

				$ced_rnx_restrict_mails = get_option( 'ced_rnx_exchange_restrict_customer_mails', true );
				if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
					wc_mail( $to, $subject, $html_content, $headers );
				}
				$ex_to = '';
				$tok = 0;
				if ( isset( $exchanged_products ) && ! empty( $exchanged_products ) ) {
					foreach ( $exchanged_products as $exchanged_product ) {
						if ( isset( $exchanged_product['variation_id'] ) ) {
							$product = wc_get_product( $exchanged_product['variation_id'] );
							$variation_product = new WC_Product_Variation( $exchanged_product['variation_id'] );
							$variation_attributes = $variation_product->get_variation_attributes();
							if ( isset( $exchanged_product['variations'] ) && ! empty( $exchanged_product['variations'] ) ) {
								$variation_attributes = $exchanged_product['variations'];
							}
							$variation_product_price = wc_get_price_excluding_tax( $variation_product, array( 'qty' => 1 ) );

							$variation_att['variation'] = $variation_attributes;

							$variation_att['totals']['subtotal'] = $exchanged_product['qty'] * $variation_product_price;
							$variation_att['totals']['total'] = $exchanged_product['qty'] * $variation_product_price;

							$item_id = $order->add_product( $variation_product, $exchanged_product['qty'], $variation_att );

							if ( $product->managing_stock() ) {
								$qty       = $exchanged_product['qty'];
								$new_stock = $product->reduce_stock( $qty );
							}
							$ex_to = $ex_to . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $exchanged_product['qty'] . ' | ';
						} elseif ( isset( $exchanged_product['product_id'] ) ) {
							$product = wc_get_product( $exchanged_product['product_id'] );
							$item_id = $order->add_product( $product, $exchanged_product['qty'] );

							if ( $product->managing_stock() ) {
								$qty       = $exchanged_product['qty'];
								$new_stock = $product->reduce_stock( $qty );
							}
							$ex_to = $ex_to . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $exchanged_product['qty'] . ' | ';
						} else {
							$product = wc_get_product( $exchanged_product['product_id'] );
							$item_id = $order->add_product( $product, $exchanged_product['qty'] );
							if ( $product->managing_stock() ) {
								$qty       = $exchanged_product['qty'];
								$new_stock = $product->reduce_stock( $qty );
							}
							$ex_to = $ex_to . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $exchanged_product['qty'] . ' | ';
						}
                        $exchange_warranty['from'][$tok]['order_id'] = (int)$orderid;
						$exchange_warranty['from'][$tok]['material_code'] = $product->get_sku();
						
						
						if($selected_product[$tok]['approve_status'] == '1'){
							$exchange_warranty['to'][$tok]['order_id'] = (int)$order_id;
							$exchange_warranty['to'][$tok]['material_code'] = "Accepted_".$product->get_sku();
						}else{
							$exchange_warranty['to'][$tok]['order_id'] = (int)$order_id;
							$exchange_warranty['to'][$tok]['material_code'] = "rejected_".$product->get_sku();
						}

						$exchange_warranty_display['from'][$tok]['order_id'] = (int)$orderid;
						$exchange_warranty_display['from'][$tok]['material_code'] = $product->get_sku();
						
						if($exchanged_product['approve_status_key'] == '1'){
							$exchange_warranty_display['from'][$tok]['approve_status'] = 'Accept';
							$exchange_warranty_display['from'][$tok]['approve_status_key'] = $exchanged_product['approve_status_key'];
						}else if($exchanged_product['approve_status_key'] == '2'){
							$exchange_warranty_display['from'][$tok]['approve_status'] = 'Reject';
							$exchange_warranty_display['from'][$tok]['approve_status_key'] = $exchanged_product['approve_status_key'];
						}							
						$tok++;
					}
				}
				$ex_fr = trim( $ex_fr, '| ' );
				$ex_to = trim( $ex_to, '| ' );
				$exchange_note = __( 'Product Warranty Clain Request from', 'woocommerce-refund-and-exchange' ) . ' { ' . $ex_fr . ' } ' . __( 'to', 'woocommerce-refund-and-exchange' ) . ' { ' . $ex_to . ' } ' . __( ' has been approved.', 'woocommerce-refund-and-exchange' );
				wc_get_order( $orderid )->add_order_note( $exchange_note );

				if ( isset( $added_fee ) && ! empty( $added_fee ) ) {
					if ( is_array( $added_fee ) ) {
						foreach ( $added_fee as $fee ) {

							$new_fee  = new WC_Order_Item_Fee();
							$new_fee->set_name( esc_attr( $fee['text'] ) );
							$new_fee->set_total( $fee['val'] );
							$new_fee->set_tax_class( '' );
							$new_fee->set_tax_status( 'none' );
							$new_fee->set_total_tax( $totalProducttax );
							$new_fee->save();
							$item_id = $order->add_item( $new_fee );
						}
					}
				}
				$discount = 0;
				if ( isset( $exchanged_from_products ) && ! empty( $exchanged_from_products ) ) {
					$totalProducttax = '';
					$exchanged_from_products_count = count( $exchanged_from_products );
					$l_amount = $left_amount / $exchanged_from_products_count;
					foreach ( $exchanged_from_products as $exchanged_product ) {
						if ( isset( $exchanged_product['variation_id'] ) && $exchanged_product['variation_id'] > 0 ) {
							$p = wc_get_product( $exchanged_product['variation_id'] );
						} else {
							$p = wc_get_product( $exchanged_product['product_id'] );
						}
						if ( true ) {
							$_tax = new WC_Tax();

							$prePrice = $p->get_price_excluding_tax();
							$pTax = $exchanged_product['qty'] * ( $p->get_price() - $prePrice );
							$totalProducttax += $pTax;
							$item_rate = round( array_shift( $rates ) );
							$price = $exchanged_product['qty'] * $prePrice;
							$discount += $price;
							$tax_rates = WC_Tax::get_rates( $p->get_tax_class() );
							if ( ! empty( $tax_rates ) ) {
								$tax_rate = reset( $tax_rates );

								$dis_tax = $tax_rate['rate'];
							}
						} else {
							$price = $exchanged_product['qty'] * $exchanged_product['price'];
							$discount += $price;
						}
					}
				}
				$dis_tax_amu = 0;
				if ( $left_amount > 0 ) {
					$mwb_rnx_obj = $order;
					$amount_discount = $mwb_rnx_obj->calculate_totals();
					$total_ptax = $mwb_rnx_obj->get_total_tax();
					$amount_discount = $amount_discount - $total_ptax;

					$new_fee  = new WC_Order_Item_Fee();
					$new_fee->set_name( esc_attr( 'Discount' ) );
					$new_fee->set_total( -$amount_discount );
					$new_fee->set_tax_class( '' );
					$new_fee->set_tax_status( 'none' );
					$new_fee->set_total_tax( '' );
					$new_fee->save();
					$item_id = $order->add_item( $new_fee );

				} else {
					if ( $discount > 0 ) {

						$mwb_rnx_obj = wc_get_order( $orderid );
						$tax_rate = 0;
						$tax = new WC_Tax();
						$country_code = WC()->countries->countries[ $mwb_rnx_obj->billing_country ]; // or populate from order to get applicable rates
						$rates = $tax->find_rates( array( 'country' => $country_code ) );
						foreach ( $rates as $rate ) {
							$tax_rate = $rate['rate'];
						}

						$total_ptax = $discount * $tax_rate / 100;
						$orderval = $discount + $total_ptax;
						$orderval = round( $orderval, 2 );

						// Coupons used in the order LOOP (as they can be multiple)
						if ( WC()->version < '3.7.0' ) {
							$coupon_used = $mwb_rnx_obj->get_used_coupons();
						} else {
							$coupon_used = $mwb_rnx_obj->get_coupon_codes();
						}
						foreach ( $coupon_used as $coupon_name ) {
							$coupon_post_obj = get_page_by_title( $coupon_name, OBJECT, 'shop_coupon' );
							$coupon_id = $coupon_post_obj->ID;
							$coupons_obj = new WC_Coupon( $coupon_id );

							 $coupons_amount = $coupons_obj->get_amount();
							 $coupons_type = $coupons_obj->get_discount_type();
							if ( $coupons_type == 'percent' ) {
								$finaldiscount = $orderval * $coupons_amount / 100;
							}
						}

						$discount = $orderval - $finaldiscount;
						$discount = $discount * 100 / ( 100 + $tax_rate );

						$new_fee  = new WC_Order_Item_Fee();
						$new_fee->set_name( esc_attr( 'Discount' ) );
						$new_fee->set_total( -$discount );
						$new_fee->set_tax_class( '' );
						$new_fee->set_tax_status( 'none' );
						$new_fee->set_total_tax( '' );
						$new_fee->save();
						$order->add_item( $new_fee );
						$items_key = $new_fee->get_id();
						$dis_tax_amu = ( $discount * $dis_tax ) / 100;
					}
				}

				$order_total = $order->calculate_totals();
				$order_total = $dis_tax_amu + $order_total;
				$order->set_total( $order_total, 'total' );

				if ( $order_total == 0 ) {
					$order->update_status( 'wc-processing' );
				} else {
					$manage_stock = get_option( 'ced_rnx_exchange_request_manage_stock' );
					if ( $manage_stock == 'yes' ) {
						if ( isset( $exchanged_products ) && ! empty( $exchanged_products ) ) {
							foreach ( $exchanged_products as $key => $prod_data ) {
								if ( $prod_data['variation_id'] > 0 ) {
									$product = wc_get_product( $prod_data['variation_id'] );
								} else {
									$product = wc_get_product( $prod_data['id'] );
								}
								if ( $product->managing_stock() ) {
									$avaliable_qty = $prod_data['qty'];
									if ( $prod_data['variation_id'] > 0 ) {
										$total_stock = get_post_meta( $prod_data['variation_id'], '_stock', true );
										$total_stock = $total_stock - $avaliable_qty;
										wc_update_product_stock( $prod_data['variation_id'], $total_stock, 'set' );
									} else {
										$total_stock = get_post_meta( $prod_data['id'], '_stock', true );
										$total_stock = $total_stock - $avaliable_qty;
										wc_update_product_stock( $prod_data['id'], $total_stock, 'set' );
									}
								}
							}
						}
					}
				}
				if ( $includeTax ) {
					$order_total = $order_total - $totalProducttax;
				}
				update_post_meta( $order_id, 'ced_rnx_exchange_warrantry_extrajson', $exchange_warranty_display ); // extra json store
				update_post_meta( $order_id, 'ced_rnx_exchange_warrantry', $exchange_warranty ); // extra json store
				update_post_meta( $orderid, 'ced_rnx_return_product', $warranty_details );
			}
			
		}
		function ced_rnx_warranty_rejected(){
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$orderid = $_POST['orderid'];
				$checkdate = $_POST['date'];
				$warranty_details = get_post_meta( $orderid, 'ced_rnx_return_product', true );
                $selected_product = $_POST['selected_product'];
				$exchange_warranty = array();
				if ( isset( $warranty_details ) && ! empty( $warranty_details ) ) {
					foreach ( $warranty_details as $date => $exchange_detail ) {
						if ($exchange_detail['status_pickup'] == 'reverse_pickup_approved') {

							$exchanged_products = $exchange_detail['products'];
							$exchanged_from_products = $exchange_detail['products'];

							$warranty_details[ $date ]['status'] = 'cancel';
							$warranty_details[ $date ]['approve'] = date( 'd-m-Y' );
							break;
						}
					}
				}
				$exchange_warranty['status'] = 'warranty-rejected';
				$exchange_warranty_display['status'] = 'warranty-rejected';

				$order_detail = wc_get_order( $orderid );
				$includeTax = isset( $order_detail->prices_include_tax ) ? $order_detail->prices_include_tax : false;
				$user_id = $order_detail->user_id;
				$order_data = array(
					'post_name'     => 'order-' . date( 'M-d-Y-hi-a' ),
					'post_type'     => 'shop_order',
					'post_title'    => 'Order &ndash; ' . date( 'F d, Y @ h:i A' ), 
					'post_status'   => 'wc-warranty-rejected',
					'ping_status'   => 'closed',
					'post_excerpt'  => 'requested',
					'post_author'   => $user_id,
					'post_password' => uniqid( 'order_' ),
					'post_date'     => date( 'Y-m-d H:i:s e' ),
					'comment_status' => 'open',
				);

				$order_id = wp_insert_post( $order_data, true );
				$approve = get_option( 'ced_rnx_notification_exchange_approve' );
				$fname = get_post_meta( $orderid, '_billing_first_name', true );
				$lname = get_post_meta( $orderid, '_billing_last_name', true );
				$fullname = $fname . ' ' . $lname;
				$approve = str_replace( '[username]', $fullname, $approve );
				$approve = str_replace( '[order]', '#' . $orderid, $approve );
				$approve = str_replace( '[siteurl]', home_url(), $approve );
				$message = stripslashes( get_option( 'ced_rnx_notification_exchange_approve', false ) );
				$message = apply_filters( 'mwb_rnx_meta_content', $message );

				$fname = get_post_meta( $orderid, '_billing_first_name', true );
				$lname = get_post_meta( $orderid, '_billing_last_name', true );
				$billing_company = get_post_meta( $orderid, '_billing_company', true );
				$billing_email = get_post_meta( $orderid, '_billing_email', true );
				$billing_phone = get_post_meta( $orderid, '_billing_phone', true );
				$billing_country = get_post_meta( $orderid, '_billing_country', true );
				$billing_address_1 = get_post_meta( $orderid, '_billing_address_1', true );
				$billing_address_2 = get_post_meta( $orderid, '_billing_address_2', true );
				$billing_state = get_post_meta( $orderid, '_billing_state', true );
				$billing_postcode = get_post_meta( $orderid, '_billing_postcode', true );
				$shipping_first_name = get_post_meta( $orderid, '_shipping_first_name', true );
				$shipping_last_name = get_post_meta( $orderid, '_shipping_last_name', true );
				$shipping_company = get_post_meta( $orderid, '_shipping_company', true );
				$shipping_country = get_post_meta( $orderid, '_shipping_country', true );
				$shipping_address_1 = get_post_meta( $orderid, '_shipping_address_1', true );
				$shipping_address_2 = get_post_meta( $orderid, '_shipping_address_2', true );
				$shipping_city = get_post_meta( $orderid, '_shipping_city', true );
				$shipping_state = get_post_meta( $orderid, '_shipping_state', true );
				$shipping_postcode = get_post_meta( $orderid, '_shipping_postcode', true );
				$payment_method_tittle = get_post_meta( $orderid, '_payment_method_title', true );
				$order_shipping = get_post_meta( $orderid, '_order_shipping', true );
				$order_total = get_post_meta( $orderid, '_order_total', true );
				$refundable_amount = get_post_meta( $orderid, 'refundable_amount', true );

				// get title data
				$_billing_salutation = get_post_meta( $orderid, '_billing_salutation', true );
				$_shipping_salutation = get_post_meta( $orderid, '_shipping_salutation', true );

					// get other data 
				$payment_method = get_post_meta( $orderid, '_payment_method', true);
				$payment_method_title = get_post_meta( $orderid, '_payment_method_title', true);
				$date_completed = get_post_meta( $orderid, '_date_completed', true);
				$date_paid = get_post_meta( $orderid, '_date_paid', true);
				$completed_date = get_post_meta( $orderid, '_completed_date', true);
				$cart_discount = get_post_meta( $orderid, '_cart_discount', true);
				$order_shipping = get_post_meta( $orderid, '_order_shipping', true);
				$transaction_id = get_post_meta( $orderid, '_transaction_id', true);

				$fullname = $fname . ' ' . $lname;
				
				$message = str_replace( '[username]', $fullname, $message );
				$message = str_replace( '[order]', '#' . $orderid, $message );
				$message = str_replace( '[siteurl]', home_url(), $message );
				$message = str_replace( '[_billing_company]', $billing_company, $message );
				$message = str_replace( '[_billing_email]', $billing_email, $message );
				$message = str_replace( '[_billing_phone]', $billing_phone, $message );
				$message = str_replace( '[_billing_country]', $billing_country, $message );
				$message = str_replace( '[_billing_address_1]', $billing_address_1, $message );
				$message = str_replace( '[_billing_address_2]', $billing_address_2, $message );
				$message = str_replace( '[_billing_state]', $billing_state, $message );
				$message = str_replace( '[_billing_postcode]', $billing_postcode, $message );
				$message = str_replace( '[_shipping_first_name]', $shipping_first_name, $message );
				$message = str_replace( '[_shipping_last_name]', $shipping_last_name, $message );
				$message = str_replace( '[_shipping_company]', $shipping_company, $message );
				$message = str_replace( '[_shipping_country]', $shipping_country, $message );
				$message = str_replace( '[_shipping_address_1]', $shipping_address_1, $message );
				$message = str_replace( '[_shipping_address_2]', $shipping_address_2, $message );
				$message = str_replace( '[_shipping_city]', $shipping_city, $message );
				$message = str_replace( '[_shipping_state]', $shipping_state, $message );
				$message = str_replace( '[_shipping_postcode]', $shipping_postcode, $message );
				$message = str_replace( '[_payment_method_tittle]', $payment_method_tittle, $message );
				$message = str_replace( '[_order_shipping]', $order_shipping, $message );
				$message = str_replace( '[_order_total]', $order_total, $message );
				$message = str_replace( '[_refundable_amount]', $refundable_amount, $message );
				$ced_rnx_odr = wc_get_order( $orderid );
				$message = str_replace( '[formatted_shipping_address]', $ced_rnx_odr->get_formatted_shipping_address(), $message );
				$message = str_replace( '[formatted_billing_address]', $ced_rnx_odr->get_formatted_billing_address(), $message );

				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
				$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );
				$ced_rnx_notification_exchange_approve_template = get_option( 'ced_rnx_notification_exchange_approve_template', 'no' );
				$mwb_dis_tot = 0;
				$ced_flag = false;

				$fname = get_option( 'ced_rnx_notification_from_name' );
				$fmail = get_option( 'ced_rnx_notification_from_mail' );

				$ced_rnx_enable_return_ship_label = get_option( 'ced_rnx_enable_return_ship_label', 'no' );

				if ( $ced_rnx_enable_return_ship_label == 'on' ) {
					$headers = array();
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					$to = get_post_meta( $orderid, '_billing_email', true );
					$subject = get_option( 'ced_rnx_return_slip_mail_subject' );
					$subject = str_replace( '[order]', '#' . $orderid, $subject );
					$ced_rnx_order_for_label = wc_get_order( $orderid );
					$ced_rnx_shiping_address = $ced_rnx_order_for_label->get_formatted_shipping_address();
					if ( $ced_rnx_shiping_address == '' ) {
						$ced_rnx_shiping_address = $ced_rnx_order_for_label->get_formatted_billing_address();
					}
					$message1 = get_option( 'ced_rnx_return_ship_template' );
					$message1 = str_replace( '[username]', $fullname, $message1 );
					$message1 = str_replace( '[order]', '#' . $orderid, $message1 );
					$message1 = str_replace( '[siteurl]', home_url(), $message1 );
					$message1 = str_replace( '[Tracking_Id]', 'ID#' . $orderid, $message1 );
					$message1 = str_replace( '[Order_shipping_address]', $ced_rnx_shiping_address, $message1 );
					$message1 = str_replace( '[formatted_shipping_address]', $ced_rnx_order_for_label->get_formatted_shipping_address(), $message1 );
					$message1 = str_replace( '[formatted_billing_address]', $ced_rnx_order_for_label->get_formatted_billing_address(), $message1 );

					$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
					$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
					$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
					$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );

					$subject = str_replace( '[username]', $fullname, $subject );
					$subject = str_replace( '[order]', '#' . $orderid, $subject );
					$subject = str_replace( '[siteurl]', home_url(), $subject );
					$ced_rnx_restrict_mails = get_option( 'ced_rnx_exchange_restrict_customer_mails', true );
					if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
						wc_mail( $to, $subject, $message1, $headers );
					}
				}

				update_post_meta( $order_id, 'ced_rnx_warranty_order', $orderid );
				update_post_meta( $orderid, "date-$date", $order_id );
				update_post_meta( $orderid, 'mwb_rnx_status_warranty', $mwb_dis_tot );

				$ex_fr = '';
				$fromk = 0;
				foreach ( $order_detail->get_items() as $item_id => $item ) {
					if ( isset( $exchanged_from_products ) && ! empty( $exchanged_from_products ) ) {
						foreach ( $exchanged_from_products as $k => $product_data ) {
							if ( $item['product_id'] == $product_data['product_id'] && $item['variation_id'] == $product_data['variation_id'] ) {
								$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
								if($product_data['qty']>1){
									$item['qty'] = $item['qty'] - $product_data['qty'];
								}else{

								}
								$args['qty'] = $item['qty'];
								$ex_fr = $ex_fr . $item['name'] . '(SKU : ' . $product->get_sku() . ') x ' . $product_data['qty'] . ' | ';
								if ( WC()->version < '3.0.0' ) {
									$order->update_product( $item_id, $product, $args );
								} else {
									wc_update_order_item_meta( $item_id, '_qty', $item['qty'] );

									if ( $product->backorders_require_notification() && $product->is_on_backorder( $args['qty'] ) ) {
										$item->add_meta_data( apply_filters( 'woocommerce_backordered_item_meta_name', __( 'Backordered', 'woocommerce' ) ), $args['qty'] - max( 0, $product->get_stock_quantity() ), true );
									}
									$item_data = $item->get_data();

									$price_excluded_tax = wc_get_price_excluding_tax( $product, array( 'qty' => 1 ) );
									$price_tax_excluded = $item_data['total'] / $item_data['quantity'];

									$args['subtotal'] = $price_excluded_tax * $args['qty'];
									$args['total']  = $price_tax_excluded * $args['qty'];

									$item->set_order_id( $orderid );
									$item->set_props( $args );
									$item->save();
								}
								$fromk++;
								break;
							}
							
							
						}
					}
				}

				$order_detail->calculate_totals();
				// $order_detail->update_status( 'wc-completed' );
				$order_detail->update_status( 'wc-warranty-rejected' ); // old order status to be exchange approved - 19-11-22
				$order = (object) $order_detail->get_address( 'shipping' );

				// other info
				update_post_meta( $order_id, '_payment_method', $payment_method );
				update_post_meta( $order_id, '_payment_method_title', $payment_method_title );
				update_post_meta( $order_id, '_date_completed', $date_completed );
				update_post_meta( $order_id, '_date_paid', $date_paid );
				update_post_meta( $order_id, '_completed_date', $completed_date );
				update_post_meta( $order_id, '_cart_discount', $cart_discount );
				update_post_meta( $order_id, '_order_shipping', $order_shipping );
				update_post_meta( $order_id, '_transaction_id', $transaction_id );

				// Shipping info
				update_post_meta( $order_id, '_customer_user', $user_id );
				update_post_meta( $order_id, '_shipping_address_1', $order->address_1 );
				update_post_meta( $order_id, '_shipping_address_2', $order->address_2 );
				update_post_meta( $order_id, '_shipping_city', $order->city );
				update_post_meta( $order_id, '_shipping_state', $order->state );
				update_post_meta( $order_id, '_shipping_postcode', $order->postcode );
				update_post_meta( $order_id, '_shipping_country', $order->country );
				update_post_meta( $order_id, '_shipping_company', $order->company );
				update_post_meta( $order_id, '_shipping_first_name', $order->first_name );
				update_post_meta( $order_id, '_shipping_last_name', $order->last_name );
				update_post_meta( $order_id, '_shipping_email', $order->email );
				update_post_meta( $order_id, '_shipping_phone', $order->phone, true );
				update_post_meta( $order_id, '_shipping_salutation', $_shipping_salutation, true );

				// billing info

				$order_detail = wc_get_order( $orderid );
				$order_detail->calculate_totals();
				$order = (object) $order_detail->get_address( 'billing' );

				add_post_meta( $order_id, '_billing_first_name', $order->first_name, true );
				add_post_meta( $order_id, '_billing_last_name', $order->last_name, true );
				add_post_meta( $order_id, '_billing_company', $order->company, true );
				add_post_meta( $order_id, '_billing_address_1', $order->address_1, true );
				add_post_meta( $order_id, '_billing_address_2', $order->address_2, true );
				add_post_meta( $order_id, '_billing_city', $order->city, true );
				add_post_meta( $order_id, '_billing_state', $order->state, true );
				add_post_meta( $order_id, '_billing_postcode', $order->postcode, true );
				add_post_meta( $order_id, '_billing_country', $order->country, true );
				add_post_meta( $order_id, '_billing_email', $order->email, true );
				add_post_meta( $order_id, '_billing_phone', $order->phone, true );
				add_post_meta( $order_id, '_billing_salutation', $_billing_salutation, true );

				// $exchanged_products

				$order = wc_get_order( $order_id );
				if ( WC()->version >= '3.0.0' ) {
					if ( ! $order->get_order_key() ) {
						update_post_meta( $order_id, '_order_key', 'wc-' . uniqid( 'order_' ) );
					}
				}
				$orders = wc_get_order( $order_id );
				$new_url = $orders->get_checkout_order_received_url();
				$message = str_replace( '[new_order_id_created]', '#' . $order_id, $message );
				$message = str_replace( '[new_order_typ_url]', $new_url, $message );

				if ( isset( $ced_rnx_notification_exchange_approve_template ) && $ced_rnx_notification_exchange_approve_template == 'on' ) {
					$html_content = $message;
				} else {
					$ced_flag = true;

				}
				if ( $ced_flag ) {
					$html_content = $this->create_exchange_approve_mail_html( $mail_header, $message, $orderid, $order_id, $exchange_details, $mail_footer );
				}
				$left_amount = get_post_meta( $orderid, 'ced_rnx_left_amount', true );
				if ( $left_amount > 0 ) {

					$new_fee  = new WC_Order_Item_Fee();
					$new_fee->set_name( esc_attr( 'Refundable Amount' ) );
					$new_fee->set_total( $left_amount );
					$new_fee->set_tax_class( '' );
					$new_fee->set_tax_status( 'none' );
					$new_fee->save();
					$item_id = $order_detail->add_item( $new_fee );
					$order_detail->calculate_totals();
				}
				$headers = array();
				$headers[] = "From: $fname <$fmail>";
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$subject = get_option( 'ced_rnx_notification_exchange_approve_subject' );

				$to = get_post_meta( $orderid, '_billing_email', true );

				$subject = str_replace( '[username]', $fullname, $subject );
				$subject = str_replace( '[order]', '#' . $orderid, $subject );
				$subject = str_replace( '[siteurl]', home_url(), $subject );

				$ced_rnx_restrict_mails = get_option( 'ced_rnx_exchange_restrict_customer_mails', true );
				if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
					wc_mail( $to, $subject, $html_content, $headers );
				}
				$ex_to = '';
				$tok = 0;
				if ( isset( $exchanged_products ) && ! empty( $exchanged_products ) ) {
					foreach ( $exchanged_products as $exchanged_product ) {
						if ( isset( $exchanged_product['variation_id'] ) ) {
							$product = wc_get_product( $exchanged_product['variation_id'] );
							$variation_product = new WC_Product_Variation( $exchanged_product['variation_id'] );
							$variation_attributes = $variation_product->get_variation_attributes();
							if ( isset( $exchanged_product['variations'] ) && ! empty( $exchanged_product['variations'] ) ) {
								$variation_attributes = $exchanged_product['variations'];
							}
							$variation_product_price = wc_get_price_excluding_tax( $variation_product, array( 'qty' => 1 ) );

							$variation_att['variation'] = $variation_attributes;

							$variation_att['totals']['subtotal'] = $exchanged_product['qty'] * $variation_product_price;
							$variation_att['totals']['total'] = $exchanged_product['qty'] * $variation_product_price;

							$item_id = $order->add_product( $variation_product, $exchanged_product['qty'], $variation_att );

							if ( $product->managing_stock() ) {
								$qty       = $exchanged_product['qty'];
								$new_stock = $product->reduce_stock( $qty );
							}
							$ex_to = $ex_to . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $exchanged_product['qty'] . ' | ';
						} elseif ( isset( $exchanged_product['product_id'] ) ) {
							$product = wc_get_product( $exchanged_product['product_id'] );
							$item_id = $order->add_product( $product, $exchanged_product['qty'] );

							if ( $product->managing_stock() ) {
								$qty       = $exchanged_product['qty'];
								$new_stock = $product->reduce_stock( $qty );
							}
							$ex_to = $ex_to . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $exchanged_product['qty'] . ' | ';
						} else {
							$product = wc_get_product( $exchanged_product['product_id'] );
							$item_id = $order->add_product( $product, $exchanged_product['qty'] );
							if ( $product->managing_stock() ) {
								$qty       = $exchanged_product['qty'];
								$new_stock = $product->reduce_stock( $qty );
							}
							$ex_to = $ex_to . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $exchanged_product['qty'] . ' | ';
						}
                        $exchange_warranty['from'][$tok]['order_id'] = (int)$orderid;
						$exchange_warranty['from'][$tok]['material_code'] = $product->get_sku();
						
						$exchange_warranty['to'][$tok]['order_id'] = (int)$order_id;
						$exchange_warranty['to'][$tok]['material_code'] = "rejected_".$product->get_sku();

						$exchange_warranty_display['from'][$tok]['order_id'] = (int)$orderid;
						$exchange_warranty_display['from'][$tok]['material_code'] = $product->get_sku();

						if($exchanged_product['approve_status_key'] == '1'){
							$exchange_warranty_display['from'][$tok]['approve_status'] = 'Accept';
							$exchange_warranty_display['from'][$tok]['approve_status_key'] = $exchanged_product['approve_status_key'];
						}else if($exchanged_product['approve_status_key'] == '2'){
							$exchange_warranty_display['from'][$tok]['approve_status'] = 'Reject';
							$exchange_warranty_display['from'][$tok]['approve_status_key'] = $exchanged_product['approve_status_key'];
						}

						

						$tok++;
					}
				}
				$ex_fr = trim( $ex_fr, '| ' );
				$ex_to = trim( $ex_to, '| ' );
				$exchange_note = __( 'Product Warranty Clain Request from', 'woocommerce-refund-and-exchange' ) . ' { ' . $ex_fr . ' } ' . __( 'to', 'woocommerce-refund-and-exchange' ) . ' { ' . $ex_to . ' } ' . __( ' has been rejected.', 'woocommerce-refund-and-exchange' );
				wc_get_order( $orderid )->add_order_note( $exchange_note );

				if ( isset( $added_fee ) && ! empty( $added_fee ) ) {
					if ( is_array( $added_fee ) ) {
						foreach ( $added_fee as $fee ) {

							$new_fee  = new WC_Order_Item_Fee();
							$new_fee->set_name( esc_attr( $fee['text'] ) );
							$new_fee->set_total( $fee['val'] );
							$new_fee->set_tax_class( '' );
							$new_fee->set_tax_status( 'none' );
							$new_fee->set_total_tax( $totalProducttax );
							$new_fee->save();
							$item_id = $order->add_item( $new_fee );
						}
					}
				}
				$discount = 0;
				if ( isset( $exchanged_from_products ) && ! empty( $exchanged_from_products ) ) {
					$totalProducttax = '';
					$exchanged_from_products_count = count( $exchanged_from_products );
					$l_amount = $left_amount / $exchanged_from_products_count;
					foreach ( $exchanged_from_products as $exchanged_product ) {
						if ( isset( $exchanged_product['variation_id'] ) && $exchanged_product['variation_id'] > 0 ) {
							$p = wc_get_product( $exchanged_product['variation_id'] );
						} else {
							$p = wc_get_product( $exchanged_product['product_id'] );
						}
						if ( true ) {
							$_tax = new WC_Tax();

							$prePrice = $p->get_price_excluding_tax();
							$pTax = $exchanged_product['qty'] * ( $p->get_price() - $prePrice );
							$totalProducttax += $pTax;
							$item_rate = round( array_shift( $rates ) );
							$price = $exchanged_product['qty'] * $prePrice;
							$discount += $price;
							$tax_rates = WC_Tax::get_rates( $p->get_tax_class() );
							if ( ! empty( $tax_rates ) ) {
								$tax_rate = reset( $tax_rates );

								$dis_tax = $tax_rate['rate'];
							}
						} else {
							$price = $exchanged_product['qty'] * $exchanged_product['price'];
							$discount += $price;
						}
					}
				}
				$dis_tax_amu = 0;
				if ( $left_amount > 0 ) {
					$mwb_rnx_obj = $order;
					$amount_discount = $mwb_rnx_obj->calculate_totals();
					$total_ptax = $mwb_rnx_obj->get_total_tax();
					$amount_discount = $amount_discount - $total_ptax;

					$new_fee  = new WC_Order_Item_Fee();
					$new_fee->set_name( esc_attr( 'Discount' ) );
					$new_fee->set_total( -$amount_discount );
					$new_fee->set_tax_class( '' );
					$new_fee->set_tax_status( 'none' );
					$new_fee->set_total_tax( '' );
					$new_fee->save();
					$item_id = $order->add_item( $new_fee );

				} else {
					if ( $discount > 0 ) {

						$mwb_rnx_obj = wc_get_order( $orderid );
						$tax_rate = 0;
						$tax = new WC_Tax();
						$country_code = WC()->countries->countries[ $mwb_rnx_obj->billing_country ]; // or populate from order to get applicable rates
						$rates = $tax->find_rates( array( 'country' => $country_code ) );
						foreach ( $rates as $rate ) {
							$tax_rate = $rate['rate'];
						}

						$total_ptax = $discount * $tax_rate / 100;
						$orderval = $discount + $total_ptax;
						$orderval = round( $orderval, 2 );

						// Coupons used in the order LOOP (as they can be multiple)
						if ( WC()->version < '3.7.0' ) {
							$coupon_used = $mwb_rnx_obj->get_used_coupons();
						} else {
							$coupon_used = $mwb_rnx_obj->get_coupon_codes();
						}
						foreach ( $coupon_used as $coupon_name ) {
							$coupon_post_obj = get_page_by_title( $coupon_name, OBJECT, 'shop_coupon' );
							$coupon_id = $coupon_post_obj->ID;
							$coupons_obj = new WC_Coupon( $coupon_id );

							 $coupons_amount = $coupons_obj->get_amount();
							 $coupons_type = $coupons_obj->get_discount_type();
							if ( $coupons_type == 'percent' ) {
								$finaldiscount = $orderval * $coupons_amount / 100;
							}
						}

						$discount = $orderval - $finaldiscount;
						$discount = $discount * 100 / ( 100 + $tax_rate );

						$new_fee  = new WC_Order_Item_Fee();
						$new_fee->set_name( esc_attr( 'Discount' ) );
						$new_fee->set_total( -$discount );
						$new_fee->set_tax_class( '' );
						$new_fee->set_tax_status( 'none' );
						$new_fee->set_total_tax( '' );
						$new_fee->save();
						$order->add_item( $new_fee );
						$items_key = $new_fee->get_id();
						$dis_tax_amu = ( $discount * $dis_tax ) / 100;
					}
				}

				$order_total = $order->calculate_totals();
				$order_total = $dis_tax_amu + $order_total;
				$order->set_total( $order_total, 'total' );

				if ( $order_total == 0 ) {
					$order->update_status( 'wc-processing' );
				} else {
					$manage_stock = get_option( 'ced_rnx_exchange_request_manage_stock' );
					if ( $manage_stock == 'yes' ) {
						if ( isset( $exchanged_products ) && ! empty( $exchanged_products ) ) {
							foreach ( $exchanged_products as $key => $prod_data ) {
								if ( $prod_data['variation_id'] > 0 ) {
									$product = wc_get_product( $prod_data['variation_id'] );
								} else {
									$product = wc_get_product( $prod_data['id'] );
								}
								if ( $product->managing_stock() ) {
									$avaliable_qty = $prod_data['qty'];
									if ( $prod_data['variation_id'] > 0 ) {
										$total_stock = get_post_meta( $prod_data['variation_id'], '_stock', true );
										$total_stock = $total_stock - $avaliable_qty;
										wc_update_product_stock( $prod_data['variation_id'], $total_stock, 'set' );
									} else {
										$total_stock = get_post_meta( $prod_data['id'], '_stock', true );
										$total_stock = $total_stock - $avaliable_qty;
										wc_update_product_stock( $prod_data['id'], $total_stock, 'set' );
									}
								}
							}
						}
					}
				}
				if ( $includeTax ) {
					$order_total = $order_total - $totalProducttax;
				}
				update_post_meta( $order_id, 'ced_rnx_exchange_warrantry_extrajson', $exchange_warranty_display ); // extra json store
				update_post_meta( $order_id, 'ced_rnx_exchange_warrantry', $exchange_warranty ); // extra json store
				update_post_meta( $orderid, 'ced_rnx_return_product', $warranty_details );
			}
			
		}
		function ced_rnx_return_req_approve_callback() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$orderid = $_POST['orderid'];
				$date = $_POST['date'];
				$products = get_post_meta( $orderid, 'ced_rnx_return_product', true );

				// Fetch the return request product
				if ( isset( $products ) && ! empty( $products ) ) {
					foreach ( $products as $date => $product ) {
						if ( $product['status'] == 'pending' ) {
							$product_datas = $product['products'];
							$products[ $date ]['status'] = 'complete';
							$approvdate = date( 'd-m-Y' );
							$products[ $date ]['approve_date'] = $approvdate;
							break;
						}
					}
				}

				// Update the status
				update_post_meta( $orderid, 'ced_rnx_return_product', $products );

				$request_files = get_post_meta( $orderid, 'ced_rnx_return_attachment', true );

				if ( isset( $request_files ) && ! empty( $request_files ) ) {
					foreach ( $request_files as $date => $request_file ) {
						if ( $request_file['status'] == 'pending' ) {
							$request_files[ $date ]['status'] = 'complete';
							break;
						}
					}
				}

				// Update the status
				update_post_meta( $orderid, 'ced_rnx_return_attachment', $request_files );

				$order = wc_get_order( $orderid );

				// Return the ordered product qty
				$return_pro = '';
				foreach ( $order->get_items() as $item_id => $item ) {
					foreach ( $product_datas as $k => $product_data ) {
						if ( $item['product_id'] == $product_data['product_id'] && $item['variation_id'] == $product_data['variation_id'] ) {
							$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );

							$item['qty'] = $item['qty'] - $product_data['qty'];
							$args['qty'] = $item['qty'];
							$return_pro = $return_pro . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $product_data['qty'] . ' | ';
							if ( WC()->version < '3.0.0' ) {
								$order->update_product( $item_id, $product, $args );
							} else {
								wc_update_order_item_meta( $item_id, '_qty', $item['qty'] );

								$product = wc_get_product( $product->get_id() );

								if ( $product->backorders_require_notification() && $product->is_on_backorder( $args['qty'] ) ) {
									$item->add_meta_data( apply_filters( 'woocommerce_backordered_item_meta_name', __( 'Backordered', 'woocommerce-refund-and-exchange' ) ), $args['qty'] - max( 0, $product->get_stock_quantity() ), true );
								}

								$item_data = $item->get_data();

								$price_excluded_tax = wc_get_price_excluding_tax( $product, array( 'qty' => 1 ) );
								$price_tax_excluded = $item_data['total'] / $item_data['quantity'];

								$args['subtotal'] = $price_excluded_tax * $args['qty'];
								$args['total']  = $price_tax_excluded * $args['qty'];

								$item->set_order_id( $orderid );
								$item->set_props( $args );
								$item->save();
							}
							break;
						}
					}
				}
				$refund_note = __( 'Product Refund Request for', 'woocommerce-refund-and-exchange' ) . ' { ' . trim( $return_pro, '| ' ) . ' } ' . __( ' has been approved.', 'woocommerce-refund-and-exchange' );

				$order = new WC_Order( $orderid );
				$fname = get_option( 'ced_rnx_notification_from_name' );
				$fmail = get_option( 'ced_rnx_notification_from_mail' );

				$headers = array();
				$headers[] = "From: $fname <$fmail>";
				$headers[] = 'Content-Type: text/html; charset=UTF-8';

				$to = get_post_meta( $orderid, '_billing_email', true );

				$subject = get_option( 'ced_rnx_notification_return_approve_subject', false );
				$approve = get_option( 'ced_rnx_notification_return_approve', false );
				$wallet_enable = get_option( 'ced_rnx_return_wallet_enable', 'no' );

				if ( $wallet_enable == 'yes' ) {
					if ( WC()->version < '3.0.0' ) {
						$order_id = $order->id;
					} else {
						$order_id = $order->get_id();
					}
					$customer_id = ( $value = get_post_meta( $order_id, '_customer_user', true ) ) ? absint( $value ) : '';
					if ( $customer_id > 0 ) {
						$approve = get_option( 'ced_rnx_notification_return_approve_wallet', false );
					}
				}

				$fname = get_post_meta( $orderid, '_billing_first_name', true );
				$lname = get_post_meta( $orderid, '_billing_last_name', true );

				$fullname = $fname . ' ' . $lname;

				$approve = str_replace( '[username]', $fullname, $approve );
				$approve = str_replace( '[order]', '#' . $orderid, $approve );
				$approve = str_replace( '[siteurl]', home_url(), $approve );

				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
				$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );

				$mail_header = str_replace( '[username]', $fullname, $mail_header );
				$mail_header = str_replace( '[order]', '#' . $orderid, $mail_header );
				$mail_header = str_replace( '[siteurl]', home_url(), $mail_header );

				$message = '<html>
				<body>
				<style>
				body {
				    box-shadow: 2px 2px 10px #ccc;
				    color: #767676;
				    font-family: Arial,sans-serif;
				    margin: 80px auto;
				    max-width: 700px;
				    padding-bottom: 30px;
				    width: 100%;
				}

				h2 {
					font-size: 30px;
					margin-top: 0;
					color: #fff;
					padding: 40px;
					background-color: #557da1;
				}

				h4 {
					color: #557da1;
					font-size: 20px;
					margin-bottom: 10px;
				}

				.content {
					padding: 0 40px;
				}

				.Customer-detail ul li p {
					margin: 0;
				}

				.details .Shipping-detail {
					width: 40%;
					float: right;
				}

				.details .Billing-detail {
					width: 60%;
					float: left;
				}

				.details .Shipping-detail ul li,.details .Billing-detail ul li {
					list-style-type: none;
					margin: 0;
				}

				.details .Billing-detail ul,.details .Shipping-detail ul {
					margin: 0;
					padding: 0;
				}

				.clear {
					clear: both;
				}

				table,td,th {
					border: 2px solid #ccc;
					padding: 15px;
					text-align: left;
				}

				table {
					border-collapse: collapse;
					width: 100%;
				}

				.info {
					display: inline-block;
				}

				.bold {
					font-weight: bold;
				}

				.footer {
					margin-top: 30px;
					text-align: center;
					color: #99B1D8;
					font-size: 12px;
				}
							dl.variation dd {
							    font-size: 12px;
							    margin: 0;
								}
				</style>

				<div style="text-align: center; padding: 10px;" class="header">
					' . $mail_header . '
				</div>

				<div class="header">
				<h2>' . __( 'Your Refund Request is Approved', 'woocommerce-refund-and-exchange' ) . '</h2>
				</div>
				<div class="content">
					<div class="reason">
						<p>' . $approve . '</p>
					</div>
				<div class="Order">
				<h4>Order #' . $orderid . '</h4>
				<table>
				<tbody>
				<tr>
				<th>' . __( 'Product', 'woocommerce-refund-and-exchange' ) . '</th>
				<th>' . __( 'Quantity', 'woocommerce-refund-and-exchange' ) . '</th>
				<th>' . __( 'Price', 'woocommerce-refund-and-exchange' ) . '</th>
				</tr>';
				$order = wc_get_order( $orderid );
				$requested_products = $products[ $date ]['products'];

				if ( isset( $requested_products ) && ! empty( $requested_products ) ) {
					$total = 0;
					$mwb_get_refnd = get_post_meta( $orderid, 'ced_rnx_return_product', true );
					if ( ! empty( $mwb_get_refnd ) ) {
						foreach ( $mwb_get_refnd as $key => $value ) {
							if ( isset( $value['amount'] ) ) {
								$total_price = $value['amount'];
								break;
							}
						}
					}
					foreach ( $order->get_items() as $item_id => $item ) {
						$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
						foreach ( $requested_products as $requested_product ) {
							if ( $item_id == $requested_product['item_id'] ) {

								if ( isset( $requested_product['variation_id'] ) && $requested_product['variation_id'] > 0 ) {
									$prod = wc_get_product( $requested_product['variation_id'] );

								} else {
									$prod = wc_get_product( $requested_product['product_id'] );
								}

								$prod_price = wc_get_price_excluding_tax( $prod, array( 'qty' => 1 ) );
								$subtotal = $prod_price * $requested_product['qty'];
								$total += $subtotal;
								if ( WC()->version < '3.1.0' ) {
									$item_meta      = new WC_Order_Item_Meta( $item, $_product );
									$item_meta_html = $item_meta->display( true, true );
								} else {
									$item_meta      = new WC_Order_Item_Product( $item, $_product );
									$item_meta_html = wc_display_item_meta( $item_meta, array( 'echo' => false ) );
								}
								$message .= '<tr>
											<td>' . $item['name'] . '<br>';
								$message .= '<small>' . $item_meta_html . '</small>
											<td>' . $requested_product['qty'] . '</td>
											<td>' . ced_rnx_format_price( $requested_product['price'] * $requested_product['qty'] ) . '</td>
										</tr>';

							}
						}
					}
					$message .= '<tr>
									<th colspan="2">Total:</th>
									<td>' . ced_rnx_format_price( $total_price ) . '</td>
								</tr>
								<tr>
									<th colspan="3">Extra:</th>
								</tr>';
				}
				if ( WC()->version < '3.0.0' ) {
					$order_id = $order->id;
				} else {
					$order_id = $order->get_id();
				}
				$added_fees = get_post_meta( $order_id, 'ced_rnx_return_added_fee', true );
				if ( isset( $added_fees ) && ! empty( $added_fees ) ) {
					foreach ( $added_fees as $da => $added_fee ) {
						if ( $date == $da ) {
							foreach ( $added_fee as $fee ) {
								$total -= $fee['val'];
								$total_price -= $fee['val'];
								$message .= ' <tr>
												<th colspan="2">' . $fee['text'] . ':</th>
												<td>' . ced_rnx_format_price( $fee['val'] ) . '</td>
											</tr>';
							}
						}
					}
				}
				if ( WC()->version < '3.0.0' ) {
					$order_id = $order->id;
				} else {
					$order_id = $order->get_id();
				}
				$message .= ' <tr>
								<th colspan="2">' . __( 'Refund Total', 'woocommerce-refund-and-exchange' ) . ':</th>
									<td>' . ced_rnx_format_price( $total_price ) . '</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="Customer-detail">
						<h4>' . __( 'Customer details', 'woocommerce-refund-and-exchange' ) . '</h4>
							<ul>
								<li>
									<p class="info">
										<span class="bold">' . __( 'Email', 'woocommerce-refund-and-exchange' ) . ': </span>' . get_post_meta( $order_id, '_billing_email', true ) . '
									</p>
								</li>
								<li>
									<p class="info">
										<span class="bold">' . __( 'Tel', 'woocommerce-refund-and-exchange' ) . ': </span>' . get_post_meta( $order_id, '_billing_phone', true ) . '
									</p>
								</li>
							</ul>
						</div>
						<div class="details">
							<div class="Shipping-detail">
								<h4>' . __( 'Shipping Address', 'woocommerce-refund-and-exchange' ) . '</h4>
								' . $order->get_formatted_shipping_address() . '
								</div>
								<div class="Billing-detail">
									<h4>' . __( 'Billing Address', 'woocommerce-refund-and-exchange' ) . '</h4>
									' . $order->get_formatted_billing_address() . '
								</div>
								<div class="clear"></div>
							</div>
						</div>
					<div style="text-align: center; padding: 10px;" class="footer">
						' . $mail_footer . '
					</div>
					</body>
				</html>';

				$template = stripslashes( get_option( 'ced_rnx_notification_return_approve_template', 'no' ) );

				if ( isset( $template ) && $template == 'on' ) {
					$refund_approve_template = stripslashes( get_option( 'ced_rnx_notification_return_approve', false ) );
					$refund_approve_template = apply_filters( 'mwb_rnx_meta_content', $refund_approve_template );
					$wallet_enable = get_option( 'ced_rnx_return_wallet_enable', 'no' );
					if ( $wallet_enable == 'yes' ) {
						$wallet_template = stripslashes( get_option( 'ced_rnx_notification_return_approve_wallet_template', 'no' ) );
						if ( isset( $wallet_template ) && $wallet_template == 'on' ) {
							$refund_approve_template = stripslashes( get_option( 'ced_rnx_notification_return_approve_wallet', false ) );
							$refund_approve_template = apply_filters( 'mwb_rnx_meta_content', $refund_approve_template );
						}
					}
				}
				// shortcode replace variable start//////////////////////

				$fname = get_post_meta( $order_id, '_billing_first_name', true );
				$lname = get_post_meta( $order_id, '_billing_last_name', true );
				$billing_company = get_post_meta( $order_id, '_billing_company', true );
				$billing_email = get_post_meta( $order_id, '_billing_email', true );
				$billing_phone = get_post_meta( $order_id, '_billing_phone', true );
				$billing_country = get_post_meta( $order_id, '_billing_country', true );
				$billing_address_1 = get_post_meta( $order_id, '_billing_address_1', true );
				$billing_address_2 = get_post_meta( $order_id, '_billing_address_2', true );
				$billing_state = get_post_meta( $order_id, '_billing_state', true );
				$billing_postcode = get_post_meta( $order_id, '_billing_postcode', true );
				$shipping_first_name = get_post_meta( $order_id, '_shipping_first_name', true );
				$shipping_last_name = get_post_meta( $order_id, '_shipping_last_name', true );
				$shipping_company = get_post_meta( $order_id, '_shipping_company', true );
				$shipping_country = get_post_meta( $order_id, '_shipping_country', true );
				$shipping_address_1 = get_post_meta( $order_id, '_shipping_address_1', true );
				$shipping_address_2 = get_post_meta( $order_id, '_shipping_address_2', true );
				$shipping_city = get_post_meta( $order_id, '_shipping_city', true );
				$shipping_state = get_post_meta( $order_id, '_shipping_state', true );
				$shipping_postcode = get_post_meta( $order_id, '_shipping_postcode', true );
				$payment_method_tittle = get_post_meta( $order_id, '_payment_method_title', true );
				$order_shipping = get_post_meta( $order_id, '_order_shipping', true );
				$order_total = get_post_meta( $order_id, '_order_total', true );
				$refundable_amount = get_post_meta( $order_id, 'refundable_amount', true );

				// shortcode replace variable end///////////////////

				$fullname = $fname . ' ' . $lname;

				$message = str_replace( '[username]', $fullname, $message );
				$message = str_replace( '[order]', '#' . $order_id, $message );
				$message = str_replace( '[siteurl]', home_url(), $message );
				$message = str_replace( '[_billing_company]', $billing_company, $message );
				$message = str_replace( '[_billing_email]', $billing_email, $message );
				$message = str_replace( '[_billing_phone]', $billing_phone, $message );
				$message = str_replace( '[_billing_country]', $billing_country, $message );
				$message = str_replace( '[_billing_address_1]', $billing_address_1, $message );
				$message = str_replace( '[_billing_address_2]', $billing_address_2, $message );
				$message = str_replace( '[_billing_state]', $billing_state, $message );
				$message = str_replace( '[_billing_postcode]', $billing_postcode, $message );
				$message = str_replace( '[_shipping_first_name]', $shipping_first_name, $message );
				$message = str_replace( '[_shipping_last_name]', $shipping_last_name, $message );
				$message = str_replace( '[_shipping_company]', $shipping_company, $message );
				$message = str_replace( '[_shipping_country]', $shipping_country, $message );
				$message = str_replace( '[_shipping_address_1]', $shipping_address_1, $message );
				$message = str_replace( '[_shipping_address_2]', $shipping_address_2, $message );
				$message = str_replace( '[_shipping_city]', $shipping_city, $message );
				$message = str_replace( '[_shipping_state]', $shipping_state, $message );
				$message = str_replace( '[_shipping_postcode]', $shipping_postcode, $message );
				$message = str_replace( '[_payment_method_tittle]', $payment_method_tittle, $message );
				$message = str_replace( '[_order_shipping]', $order_shipping, $message );
				$message = str_replace( '[_order_total]', $order_total, $message );
				$message = str_replace( '[_refundable_amount]', $refundable_amount, $message );
				$message = str_replace( '[formatted_shipping_address]', $order->get_formatted_shipping_address(), $message );
				$message = str_replace( '[formatted_billing_address]', $order->get_formatted_billing_address(), $message );

				$subject = str_replace( '[username]', $fullname, $subject );
				$subject = str_replace( '[order]', '#' . $order_id, $subject );
				$subject = str_replace( '[siteurl]', home_url(), $subject );

				if ( isset( $refund_approve_template ) && $refund_approve_template != '' ) {
					$template = $refund_approve_template;
					$template = str_replace( '[username]', $fullname, $template );
					$template = str_replace( '[order]', '#' . $order_id, $template );
					$template = str_replace( '[siteurl]', home_url(), $template );
					$template = str_replace( '[_billing_company]', $billing_company, $template );
					$template = str_replace( '[_billing_email]', $billing_email, $template );
					$template = str_replace( '[_billing_phone]', $billing_phone, $template );
					$template = str_replace( '[_billing_country]', $billing_country, $template );
					$template = str_replace( '[_billing_address_1]', $billing_address_1, $template );
					$template = str_replace( '[_billing_address_2]', $billing_address_2, $template );
					$template = str_replace( '[_billing_state]', $billing_state, $template );
					$template = str_replace( '[_billing_postcode]', $billing_postcode, $template );
					$template = str_replace( '[_shipping_first_name]', $shipping_first_name, $template );
					$template = str_replace( '[_shipping_last_name]', $shipping_last_name, $template );
					$template = str_replace( '[_shipping_company]', $shipping_company, $template );
					$template = str_replace( '[_shipping_country]', $shipping_country, $template );
					$template = str_replace( '[_shipping_address_1]', $shipping_address_1, $template );
					$template = str_replace( '[_shipping_address_2]', $shipping_address_2, $template );
					$template = str_replace( '[_shipping_city]', $shipping_city, $template );
					$template = str_replace( '[_shipping_state]', $shipping_state, $template );
					$template = str_replace( '[_shipping_postcode]', $shipping_postcode, $template );
					$template = str_replace( '[_payment_method_tittle]', $payment_method_tittle, $template );
					$template = str_replace( '[_order_shipping]', $order_shipping, $template );
					$template = str_replace( '[_order_total]', $order_total, $template );
					$template = str_replace( '[_refundable_amount]', $refundable_amount, $template );
					$template = str_replace( '[formatted_shipping_address]', $order->get_formatted_shipping_address(), $template );
					$template = str_replace( '[formatted_billing_address]', $order->get_formatted_billing_address(), $template );
					$html_content = $template;
				} else {
					$html_content = $message;
				}

				$ced_rnx_restrict_mails = get_option( 'ced_rnx_return_restrict_customer_mails', true );
				if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
					wc_mail( $to, $subject, $html_content, $headers );
				}

				update_post_meta( $orderid, 'refundable_amount', $total_price );
				if ( $total_price > 0 ) {
					$new_fee  = new WC_Order_Item_Fee();
					$new_fee->set_name( esc_attr( 'Refundable Amount' ) );
					$new_fee->set_total( $total_price );
					$new_fee->set_tax_class( '' );
					$new_fee->set_tax_status( 'none' );
					$new_fee->save();
					$item_id = $order->add_item( $new_fee );
				}

				$final_stotal = 0;
				$lastElement = end( $order->get_items() );
				foreach ( $order->get_items() as $item_id => $item ) {
					if ( $item != $lastElement ) {
						$final_stotal += $item['subtotal'];
					}
				}

				update_post_meta( $orderid, 'discount', 0 );

				if ( $final_stotal > 0 ) {
					$mwb_rnx_obj = wc_get_order( $orderid );
					$tax_rate = 0;
					$tax = new WC_Tax();
					$country_code = WC()->countries->countries[ $mwb_rnx_obj->billing_country ]; // or populate from order to get applicable rates
					$rates = $tax->find_rates( array( 'country' => $country_code ) );
					foreach ( $rates as $rate ) {
						$tax_rate = $rate['rate'];
					}

					$total_ptax = $final_stotal * $tax_rate / 100;
					$orderval = $final_stotal + $total_ptax;
					$orderval = round( $orderval, 2 );

					// Coupons used in the order LOOP (as they can be multiple)
					if ( WC()->version < '3.7.0' ) {
						$coupon_used = $mwb_rnx_obj->get_used_coupons();
					} else {
						$coupon_used = $mwb_rnx_obj->get_coupon_codes();
					}
					foreach ( $coupon_used as $coupon_name ) {
						$coupon_post_obj = get_page_by_title( $coupon_name, OBJECT, 'shop_coupon' );
						$coupon_id = $coupon_post_obj->ID;
						$coupons_obj = new WC_Coupon( $coupon_id );

						 $coupons_amount = $coupons_obj->get_amount();
						 $coupons_type = $coupons_obj->get_discount_type();
						if ( $coupons_type == 'percent' ) {
							$finaldiscount = $orderval * $coupons_amount / 100;
						}
					}

					$discount = $finaldiscount * 100 / ( 100 + $tax_rate );

					if ( $discount > 0 ) {
						update_post_meta( $orderid, 'discount', $discount );
					} else {
						update_post_meta( $orderid, '_cart_discount_tax', 0.00 );
						update_post_meta( $orderid, 'discount', 0.00 );
					}
				}

				// Auto accept return request
				if ( isset(
					$_POST['autoaccept
					']
				) ) {
					if ( WC()->version < '3.0.0' ) {
						$order_id = $order->id;
					} else {
						$order_id = $order->get_id();
					}
					$headers = array();
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					$to = get_option( 'ced_rnx_notification_from_mail' );
					$subject = get_option( 'ced_rnx_notification_auto_accept_return_subject' );
					$subject = str_replace( '[order]', '#' . $order_id, $subject );

					$message = get_option( 'ced_rnx_notification_auto_accept_return_rcv' );
					$message = str_replace( '[username]', $fullname, $message );
					$message = str_replace( '[order]', '#' . $order_id, $message );
					$message = str_replace( '[siteurl]', home_url(), $message );
					$message = str_replace( '[formatted_shipping_address]', $order->get_formatted_shipping_address(), $message );
					$message = str_replace( '[formatted_billing_address]', $order->get_formatted_billing_address(), $message );

					$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
					$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
					$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
					$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );

					$subject = str_replace( '[order]', '#' . $order_id, $subject );
					$subject = str_replace( '[siteurl]', home_url(), $subject );

					$mail_header = str_replace( '[order]', '#' . $order_id, $mail_header );
					$mail_header = str_replace( '[siteurl]', home_url(), $mail_header );

					$html_content = '<html>
									<head>
										<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
										<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
									</head>
									<body>
										<table cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td style="text-align: center; margin-top: 30px; padding: 10px; color: #99B1D8; font-size: 12px;">
													' . $mail_header . '
												</td>
											</tr>
											<tr>
												<td>
													<table align="center" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-family:Open Sans; max-width: 600px; width: 100%;">
														<tr>
															<td style="padding: 36px 48px; width: 100%; background-color:#557DA1;color: #fff; font-size: 30px; font-weight: 300; font-family:helvetica;">' . $subject . '</td>
														</tr>
														<tr>
															<td style="width:100%; padding: 36px 48px 10px; background-color:#fdfdfd; font-size: 14px; color: #737373;">' . $message . '</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td style="text-align: center; margin-top: 30px; color: #99B1D8; font-size: 12px;">
													' . $mail_footer . '
												</td>
											</tr>
										</table>

									</body>
								</html>';

					wc_mail( $to, $subject, $html_content, $headers );

				}

				$ced_rnx_enable_return_ship_label = get_option( 'ced_rnx_enable_return_ship_label', 'no' );
				if ( $ced_rnx_enable_return_ship_label == 'on' ) {
					if ( WC()->version < '3.0.0' ) {
						$order_id = $order->id;
					} else {
						$order_id = $order->get_id();
					}
					$ced_rnx_shiping_address = $order->get_formatted_shipping_address();
					if ( $ced_rnx_shiping_address == '' ) {
						$ced_rnx_shiping_address = $order->get_formatted_billing_address();
					}

					$headers = array();
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					$to = get_post_meta( $order_id, '_billing_email', true );
					$subject = get_option( 'ced_rnx_return_slip_mail_subject' );
					$subject = str_replace( '[order]', '#' . $order_id, $subject );

					$message = get_option( 'ced_rnx_return_ship_template' );
					$message = str_replace( '[username]', $fullname, $message );
					$message = str_replace( '[order]', '#' . $order_id, $message );
					$message = str_replace( '[siteurl]', home_url(), $message );
					$message = str_replace( '[Tracking_Id]', 'ID#' . $order_id, $message );
					$message = str_replace( '[Order_shipping_address]', $ced_rnx_shiping_address, $message );
					$message = str_replace( '[formatted_shipping_address]', $order->get_formatted_shipping_address(), $message );
					$message = str_replace( '[formatted_billing_address]', $order->get_formatted_billing_address(), $message );

					$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
					$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
					$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
					$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );

					if ( $message == '' ) {

					}
					$ced_rnx_restrict_mails = get_option( 'ced_rnx_return_restrict_customer_mails', true );
					if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
						wc_mail( $to, $subject, $message, $headers );
					}
				}

				$order->update_status( 'wc-return-approved', __( 'User Request of Refund Product is approved', 'woocommerce-refund-and-exchange' ) );
				$order->calculate_totals();
				$response['response'] = 'success';
				echo json_encode( $response );
				wp_die();
			}
		}

		/**
		 * This function is process cancel Refund request
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */

		function ced_rnx_return_req_cancel_callback() {
			 $check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$orderid = $_POST['orderid'];
				$date = $_POST['date'];

				$products = get_post_meta( $orderid, 'ced_rnx_return_product', true );

				// Fetch the return request product
				if ( isset( $products ) && ! empty( $products ) ) {
					foreach ( $products as $date => $product ) {
						if ( $product['status'] == 'pending' ) {
							$product_datas = $product['products'];
							$products[ $date ]['status'] = 'cancel';
							$approvdate = date( 'd-m-Y' );
							$products[ $date ]['cancel_date'] = $approvdate;
							break;
						}
					}
				}

				// Update the status
				update_post_meta( $orderid, 'ced_rnx_return_product', $products );

				$request_files = get_post_meta( $orderid, 'ced_rnx_return_attachment', true );
				if ( isset( $request_files ) && ! empty( $request_files ) ) {
					foreach ( $request_files as $date => $request_file ) {
						if ( $request_file['status'] == 'pending' ) {
							$request_files[ $date ]['status'] = 'cancel';
						}
					}
				}

				// Update the status
				update_post_meta( $orderid, 'ced_rnx_return_attachment', $request_files );

				$order = wc_get_order( $orderid );
				$fname = get_option( 'ced_rnx_notification_from_name' );
				$fmail = get_option( 'ced_rnx_notification_from_mail' );

				$headers[] = "From: $fname <$fmail>";
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$to = get_post_meta( $orderid, '_billing_email', true );
				$subject = get_option( 'ced_rnx_notification_return_cancel_subject', false );
				$message = stripslashes( get_option( 'ced_rnx_notification_return_cancel', false ) );
				$message = apply_filters( 'mwb_rnx_meta_content', $message );
				$order_id = $orderid;
				$fname = get_post_meta( $orderid, '_billing_first_name', true );
				$lname = get_post_meta( $orderid, '_billing_last_name', true );
				$billing_company = get_post_meta( $order_id, '_billing_company', true );
				$billing_email = get_post_meta( $order_id, '_billing_email', true );
				$billing_phone = get_post_meta( $order_id, '_billing_phone', true );
				$billing_country = get_post_meta( $order_id, '_billing_country', true );
				$billing_address_1 = get_post_meta( $order_id, '_billing_address_1', true );
				$billing_address_2 = get_post_meta( $order_id, '_billing_address_2', true );
				$billing_state = get_post_meta( $order_id, '_billing_state', true );
				$billing_postcode = get_post_meta( $order_id, '_billing_postcode', true );
				$shipping_first_name = get_post_meta( $order_id, '_shipping_first_name', true );
				$shipping_last_name = get_post_meta( $order_id, '_shipping_last_name', true );
				$shipping_company = get_post_meta( $order_id, '_shipping_company', true );
				$shipping_country = get_post_meta( $order_id, '_shipping_country', true );
				$shipping_address_1 = get_post_meta( $order_id, '_shipping_address_1', true );
				$shipping_address_2 = get_post_meta( $order_id, '_shipping_address_2', true );
				$shipping_city = get_post_meta( $order_id, '_shipping_city', true );
				$shipping_state = get_post_meta( $order_id, '_shipping_state', true );
				$shipping_postcode = get_post_meta( $order_id, '_shipping_postcode', true );
				$payment_method_tittle = get_post_meta( $order_id, '_payment_method_title', true );
				$order_shipping = get_post_meta( $order_id, '_order_shipping', true );
				$order_total = get_post_meta( $order_id, '_order_total', true );
				$refundable_amount = get_post_meta( $order_id, 'refundable_amount', true );

				$fullname = $fname . ' ' . $lname;

				$message = str_replace( '[username]', $fullname, $message );
				$message = str_replace( '[order]', '#' . $orderid, $message );
				$message = str_replace( '[siteurl]', home_url(), $message );
				$message = str_replace( '[_billing_company]', $billing_company, $message );
				$message = str_replace( '[_billing_email]', $billing_email, $message );
				$message = str_replace( '[_billing_phone]', $billing_phone, $message );
				$message = str_replace( '[_billing_country]', $billing_country, $message );
				$message = str_replace( '[_billing_address_1]', $billing_address_1, $message );
				$message = str_replace( '[_billing_address_2]', $billing_address_2, $message );
				$message = str_replace( '[_billing_state]', $billing_state, $message );
				$message = str_replace( '[_billing_postcode]', $billing_postcode, $message );
				$message = str_replace( '[_shipping_first_name]', $shipping_first_name, $message );
				$message = str_replace( '[_shipping_last_name]', $shipping_last_name, $message );
				$message = str_replace( '[_shipping_company]', $shipping_company, $message );
				$message = str_replace( '[_shipping_country]', $shipping_country, $message );
				$message = str_replace( '[_shipping_address_1]', $shipping_address_1, $message );
				$message = str_replace( '[_shipping_address_2]', $shipping_address_2, $message );
				$message = str_replace( '[_shipping_city]', $shipping_city, $message );
				$message = str_replace( '[_shipping_state]', $shipping_state, $message );
				$message = str_replace( '[_shipping_postcode]', $shipping_postcode, $message );
				$message = str_replace( '[_payment_method_tittle]', $payment_method_tittle, $message );
				$message = str_replace( '[_order_shipping]', $order_shipping, $message );
				$message = str_replace( '[_order_total]', $order_total, $message );
				$message = str_replace( '[_refundable_amount]', $refundable_amount, $message );
				$message = str_replace( '[formatted_shipping_address]', $order->get_formatted_shipping_address(), $message );
				$message = str_replace( '[formatted_billing_address]', $order->get_formatted_billing_address(), $message );

				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
				$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );

				$subject = str_replace( '[username]', $fullname, $subject );
				$subject = str_replace( '[order]', '#' . $orderid, $subject );
				$subject = str_replace( '[siteurl]', home_url(), $subject );

				$mail_header = str_replace( '[username]', $fullname, $mail_header );
				$mail_header = str_replace( '[order]', '#' . $orderid, $mail_header );
				$mail_header = str_replace( '[siteurl]', home_url(), $mail_header );

				$template = get_option( 'ced_rnx_notification_return_cancel_template', 'no' );

				if ( isset( $template ) && $template == 'on' ) {

					$html_content = $message;
				} else {
					$html_content = '<html>
										<head>
											<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
											<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
										</head>
										<body>
											<table cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td style="text-align: center; margin-top: 30px; padding: 10px; color: #99B1D8; font-size: 12px;">
													' . $mail_header . '
													</td>
												</tr>
												<tr>
													<td>
														<table align="center" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-family:Open Sans; max-width: 600px; width: 100%;">
															<tr>
																<td style="padding: 36px 48px; width: 100%; background-color:#557DA1;color: #fff; font-size: 30px; font-weight: 300; font-family:helvetica;">' . $subject . '</td>
															</tr>
															<tr>
																<td style="width:100%; padding: 36px 48px 10px; background-color:#fdfdfd; font-size: 14px; color: #737373;">' . $message . '</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td style="text-align: center; margin-top: 30px; color: #99B1D8; font-size: 12px;">
													' . $mail_footer . '
													</td>
												</tr>
											</table>
										</body>
									</html>';
				}

				$ced_rnx_restrict_mails = get_option( 'ced_rnx_return_restrict_customer_mails', true );
				if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
					wc_mail( $to, $subject, $html_content, $headers );
				}

				$order->update_status( 'wc-return-cancelled', __( 'User Request of Refund Product is Cancelled', 'woocommerce-refund-and-exchange' ) );
				$response['response'] = 'success';
				echo json_encode( $response );
				wp_die();
			}
		}

		/**
		 * This function is add extra fee to exchange product
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function ced_rnx_exchange_fee_add_callback() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$orderid = $_POST['orderid'];
				$pending_date = $_POST['date'];
				$fees = array();
				if ( isset( $_POST['fees'] ) ) {
					$fees = $_POST['fees'];
					if ( isset( $fees ) ) {
						foreach ( $fees as $k => $fee ) {
							if ( $fee['text'] == '' || $fee['val'] == '' ) {
								unset( $fees[ $k ] );
							}
						}
					}
				}
				$exchange_details = get_post_meta( $orderid, 'ced_rnx_exchange_product', true );
				if ( isset( $exchange_details[ $pending_date ] ) ) {
					if ( isset( $exchange_details[ $pending_date ]['fee'] ) ) {
						$added_fees = $exchange_details[ $pending_date ]['fee'];
					} else {
						$added_fees = array();
					}
				}
				$exist = true;
				if ( isset( $added_fees ) && ! empty( $added_fees ) ) {
					foreach ( $added_fees as $date => $added_fee ) {
						if ( $date == $pending_date ) {
							$exchange_details[ $pending_date ]['fee'] = $fees;
							$exist = false;
							break;
						}
					}
				}

				if ( $exist ) {
					$exchange_details[ $pending_date ]['fee'] = $fees;
				}

				update_post_meta( $orderid, 'ced_rnx_exchange_product', $exchange_details );
				$response['response'] = 'success';
				echo json_encode( $response );
				wp_die();
			}
		}
		public function ced_exchange_req_approve_refund() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$order_id = isset( $_POST['orderid'] ) ? $_POST['orderid'] : 0;
				$ced_rnx_amount_for_refund = isset( $_POST['amount'] ) ? sanitize_text_field( wp_unslash( $_POST['amount'] ) ) : 0;
				$request_type = isset( $_POST['request_type'] ) ? sanitize_text_field( wp_unslash( $_POST['request_type'] ) ) : '';
				$wallet_enable = get_option( 'ced_rnx_return_wallet_enable', 'no' );
				update_post_meta( $order_id, 'ced_rnx_exchange_approve_refunded', 'yes' );
				if ( $wallet_enable == 'yes' && $order_id > 0 ) {
					$customer_id = ( $value = get_post_meta( $order_id, '_customer_user', true ) ) ? absint( $value ) : '';
					if ( $customer_id > 0 ) {
						$walletcoupon = get_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', true );
						if ( empty( $walletcoupon ) ) {
							$coupon_code = ced_rnx_coupon_generator( 5 ); // Code
							$amount = $total_price; // Amount
							$discount_type = 'fixed_cart';
							$coupon_description = "REFUND ACCEPTED - ORDER #$order_id";

							$coupon = array(
								'post_title' => $coupon_code,
								'post_content' => $coupon_description,
								'post_excerpt' => $coupon_description,
								'post_status' => 'publish',
								'post_author' => get_current_user_id(),
								'post_type'     => 'shop_coupon',
							);

							$new_coupon_id = wp_insert_post( $coupon );
							$discount_type = 'fixed_cart';
							update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
							update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
							update_post_meta( $new_coupon_id, 'rnxwallet', true );
							update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $coupon_code );
						} else {
							$the_coupon = new WC_Coupon( $walletcoupon );
							$coupon_id = $the_coupon->get_id();
							if ( isset( $coupon_id ) ) {
								$amount = get_post_meta( $coupon_id, 'coupon_amount', true );
								$remaining_amount = $amount + $ced_rnx_amount_for_refund;
								update_post_meta( $coupon_id, 'coupon_amount', $remaining_amount );
								update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $walletcoupon );
								update_post_meta( $coupon_id, 'rnxwallet', true );
							}
						}
						if ( $request_type == 'refund' ) {
							update_post_meta( $order_id, 'refundable_amount', 0 );
						} else {
							update_post_meta( $order_id, 'ced_rnx_left_amount', 0 );
						}
						$order = wc_get_order( $order_id );

						$new_fee  = new WC_Order_Item_Fee();
						$new_fee->set_name( esc_attr( 'Amount Refunded in wallet' ) );
						$new_fee->set_total( - $ced_rnx_amount_for_refund );
						$new_fee->set_tax_class( '' );
						$new_fee->set_tax_status( 'none' );
						$new_fee->save();
						$item_id = $order->add_item( $new_fee );

						$order->calculate_totals();
						$response['result'] = true;
						$response['msg'] = __( 'Amount is added in customer wallet.', 'woocommerce-refund-and-exchange' );
						echo json_encode( $response );
						wp_die();
					}
				} else {
					$response['result'] = false;
					$response['msg'] = __( 'Wallet is not Enable, Please Enable wallet to add the amount in customer wallet.', 'woocommerce-refund-and-exchange' );

					echo json_encode( $response );
					wp_die();
				}
			}
		}

		/**
		 * This function is approve exchange request and Create new order for exchnage product and decrease product quantity from order
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		public function ced_exchange_req_approve_first_level(){
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$orderid = $_POST['orderid'];
				$checkdate = $_POST['date'];
				$selected_product = $_POST['selected_product'];
				$exchange_details = get_post_meta( $orderid, 'ced_rnx_exchange_product', true );
				if ( isset( $exchange_details ) && ! empty( $exchange_details ) ) {
					foreach ( $exchange_details as $date => $exchange_detail ) {
						if ( $exchange_detail['status'] == 'pending' ) {
							$exchanged_products = $exchange_detail['to'];
							$exchanged_products_shiprocket = $exchange_detail['to'];
							$exchanged_from_products = $exchange_detail['from'];
							if ( isset( $exchange_detail['fee'] ) ) {
								$added_fee = $exchange_detail['fee'];
							}
							$exchange_details[ $date ]['status_pickup'] = 'reverse_pickup_approved';
							$exchange_details[ $date ]['approve_pickup'] = date( 'd-m-Y' );
							break;
						}
					}
					foreach ( $exchange_details as $date => $exchange_detail ) {
						foreach ( $exchange_detail['to'] as $tokey => $tovalue ) {
							foreach ($selected_product as $key_exchange => $value_exchange){
								if($value_exchange['id'] == $tovalue['id']){
									if($value_exchange['approve_status'] == '1'){
										$exchange_details[ $date ]['to'][$key_exchange]['approve_status'] = 'Accept';
										$exchange_details[ $date ]['to'][$key_exchange]['approve_status_key'] = $value_exchange['approve_status'];
									}else if($value_exchange['approve_status'] == '2'){
										unset($exchanged_products_shiprocket[$key_exchange]);
										$exchange_details[ $date ]['to'][$key_exchange]['approve_status'] = 'Reject';
										$exchange_details[ $date ]['to'][$key_exchange]['approve_status_key'] = $value_exchange['approve_status'];
									}	
								}						
							}
						}
					}
				}
				//Order Push on Shiprocket return API
				$this->return_shiprock_api($orderid,$exchanged_products_shiprocket);
				$order_detail = wc_get_order( $orderid );				
				$order_detail->update_status('reverse_pickup');
				update_post_meta( $orderid, 'ced_rnx_exchange_product', $exchange_details );
			}
		}
		public function ced_exchange_req_approve_callback() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$orderid = $_POST['orderid'];
				$checkdate = $_POST['date'];
				$selected_product = $_POST['selected_product'];
				$exchange_details = get_post_meta( $orderid, 'ced_rnx_exchange_product', true );
				$exchange_warranty = array();
				$exchange_warranty_display =  array();
				$exchange_warranty_display['status'] = 'exchange-accepted';
				if ( isset( $exchange_details ) && ! empty( $exchange_details ) ) {
					foreach ( $exchange_details as $date => $exchange_detail ) {
						//if ($exchange_detail['status_pickup'] == 'reverse_pickup_approved') {
							$exchanged_products = $exchange_detail['to'];
							$exchanged_from_products = $exchange_detail['from'];
							if ( isset( $exchange_detail['fee'] ) ) {
								$added_fee = $exchange_detail['fee'];
							}
							$exchange_details[ $date ]['status'] = 'complete';
							$exchange_details[ $date ]['approve'] = date( 'd-m-Y' );
							break;
						//}
					}
					foreach ($selected_product as $key_exchange => $value_exchange){
					if($value_exchange['approve_status'] == '1'){
						$exchange_details[ $date ]['to'][$key_exchange]['approve_status_key'] = $value_exchange['approve_status'];
						$exchange_details[ $date ]['to'][$key_exchange]['approve_status'] = 'Accept';
					}else if($value_exchange['approve_status'] == '2'){
						$exchange_details[ $date ]['to'][$key_exchange]['approve_status_key'] = $value_exchange['approve_status'];
						$exchange_details[ $date ]['to'][$key_exchange]['approve_status'] = 'Reject';
					}
					$exchange_warranty_display['from'][$key_exchange]['order_id'] = (int)$order_id;
					$exchange_warranty_display['from'][$key_exchange]['material_code'] = $value_exchange['sku'];							
				}

				}
				foreach($exchanged_products as $keys => $kvalues){
				    foreach ($selected_product as $key_exchange => $value_exchange){
				        if($value_exchange['approve_status'] == '3'){
        				    if($kvalues['product_id'] == $value_exchange['product_id']){
        				        unset($exchanged_products[$keys]);
        				    }
				        }
				    }
				
				}
				foreach($exchanged_from_products as $keys_1 => $kvalues_1){
				    foreach ($selected_product as $key_exchange => $value_exchange){
				        if($value_exchange['approve_status'] == '3'){
        				    if($kvalues_1['product_id'] == $value_exchange['product_id']){
        				        unset($exchanged_from_products[$keys_1]);
        				    }
				        }
				    }
				}
				// echo "<pre>";
				// print_r($exchanged_from_products);
				// echo "<pre>";
				// print_r($exchanged_products);
				// die;
				$exchange_warranty['status'] = 'exchange-accepted';
				$order_detail = wc_get_order( $orderid );
				$includeTax = isset( $order_detail->prices_include_tax ) ? $order_detail->prices_include_tax : false;
				$user_id = $order_detail->user_id;
				$order_data = array(
					'post_name'     => 'order-' . date( 'M-d-Y-hi-a' ),
					'post_type'     => 'shop_order',
					'post_title'    => 'Order &ndash; ' . date( 'F d, Y @ h:i A' ), 
					'post_status'   => 'wc-exchange-accepted',
					'ping_status'   => 'closed',
					'post_excerpt'  => 'requested',
					'post_author'   => $user_id,
					'post_password' => uniqid( 'order_' ),
					'post_date'     => date( 'Y-m-d H:i:s e' ),
					'comment_status' => 'open',
				);
				$order_id = wp_insert_post( $order_data, true );
                //product approved and rejected comment display in view order page 
				foreach ($selected_product as $key_exchange => $value_exchange){
					if($value_exchange['approve_status'] == '1'){
						$exchange_warranty_display['from'][$key_exchange]['approve_status_key'] = $value_exchange['approve_status'];
						$exchange_warranty_display['from'][$key_exchange]['approve_status'] = 'Accept';
					}else if($value_exchange['approve_status'] == '2'){
						$exchange_warranty_display['from'][$key_exchange]['approve_status_key'] = $value_exchange['approve_status'];
						$exchange_warranty_display['from'][$key_exchange]['approve_status'] = 'Reject';
					}
					$exchange_warranty_display['from'][$key_exchange]['order_id'] = (int)$order_id;
					$exchange_warranty_display['from'][$key_exchange]['material_code'] = $value_exchange['sku'];							
				}

				$approve = get_option( 'ced_rnx_notification_exchange_approve' );
				$fname = get_post_meta( $orderid, '_billing_first_name', true );
				$lname = get_post_meta( $orderid, '_billing_last_name', true );
				$fullname = $fname . ' ' . $lname;
				$approve = str_replace( '[username]', $fullname, $approve );
				$approve = str_replace( '[order]', '#' . $orderid, $approve );
				$approve = str_replace( '[siteurl]', home_url(), $approve );
				$message = stripslashes( get_option( 'ced_rnx_notification_exchange_approve', false ) );
				$message = apply_filters( 'mwb_rnx_meta_content', $message );

				$fname = get_post_meta( $orderid, '_billing_first_name', true );
				$lname = get_post_meta( $orderid, '_billing_last_name', true );
				$billing_company = get_post_meta( $orderid, '_billing_company', true );
				$billing_email = get_post_meta( $orderid, '_billing_email', true );
				$billing_phone = get_post_meta( $orderid, '_billing_phone', true );
				$billing_country = get_post_meta( $orderid, '_billing_country', true );
				$billing_address_1 = get_post_meta( $orderid, '_billing_address_1', true );
				$billing_address_2 = get_post_meta( $orderid, '_billing_address_2', true );
				$billing_state = get_post_meta( $orderid, '_billing_state', true );
				$billing_postcode = get_post_meta( $orderid, '_billing_postcode', true );

				$shipping_first_name = get_post_meta( $orderid, '_shipping_first_name', true );
				$shipping_last_name = get_post_meta( $orderid, '_shipping_last_name', true );
				$shipping_company = get_post_meta( $orderid, '_shipping_company', true );
				$shipping_country = get_post_meta( $orderid, '_shipping_country', true );
				$shipping_address_1 = get_post_meta( $orderid, '_shipping_address_1', true );
				$shipping_address_2 = get_post_meta( $orderid, '_shipping_address_2', true );
				$shipping_city = get_post_meta( $orderid, '_shipping_city', true );
				$shipping_state = get_post_meta( $orderid, '_shipping_state', true );
				$shipping_postcode = get_post_meta( $orderid, '_shipping_postcode', true );
				$payment_method_tittle = get_post_meta( $orderid, '_payment_method_title', true );
				$order_shipping = get_post_meta( $orderid, '_order_shipping', true );
				$order_total = get_post_meta( $orderid, '_order_total', true );
				$refundable_amount = get_post_meta( $orderid, 'refundable_amount', true );

				$shipping_phone = get_post_meta( $orderid, '_shipping_phone', true );
				$shipping_email = get_post_meta( $orderid, '_shipping_email', true );

				// get title data
				$_billing_salutation = get_post_meta( $orderid, '_billing_salutation', true );
				$_shipping_salutation = get_post_meta( $orderid, '_shipping_salutation', true );

				// get other data 
				$payment_method = get_post_meta( $orderid, '_payment_method', true);
				$payment_method_title = get_post_meta( $orderid, '_payment_method_title', true);
				$date_completed = get_post_meta( $orderid, '_date_completed', true);
				$date_paid = get_post_meta( $orderid, '_date_paid', true);
				$completed_date = get_post_meta( $orderid, '_completed_date', true);
				$cart_discount = get_post_meta( $orderid, '_cart_discount', true);
				$order_shipping = get_post_meta( $orderid, '_order_shipping', true);
				$transaction_id = get_post_meta( $orderid, '_transaction_id', true);

				$fullname = $fname . ' ' . $lname;
				$message = str_replace( '[username]', $fullname, $message );
				$message = str_replace( '[order]', '#' . $orderid, $message );
				$message = str_replace( '[siteurl]', home_url(), $message );
				$message = str_replace( '[_billing_company]', $billing_company, $message );
				$message = str_replace( '[_billing_email]', $billing_email, $message );
				$message = str_replace( '[_billing_phone]', $billing_phone, $message );
				$message = str_replace( '[_billing_country]', $billing_country, $message );
				$message = str_replace( '[_billing_address_1]', $billing_address_1, $message );
				$message = str_replace( '[_billing_address_2]', $billing_address_2, $message );
				$message = str_replace( '[_billing_state]', $billing_state, $message );
				$message = str_replace( '[_billing_postcode]', $billing_postcode, $message );
				$message = str_replace( '[_shipping_first_name]', $shipping_first_name, $message );
				$message = str_replace( '[_shipping_last_name]', $shipping_last_name, $message );
				$message = str_replace( '[_shipping_company]', $shipping_company, $message );
				$message = str_replace( '[_shipping_country]', $shipping_country, $message );
				$message = str_replace( '[_shipping_address_1]', $shipping_address_1, $message );
				$message = str_replace( '[_shipping_address_2]', $shipping_address_2, $message );
				$message = str_replace( '[_shipping_city]', $shipping_city, $message );
				$message = str_replace( '[_shipping_state]', $shipping_state, $message );
				$message = str_replace( '[_shipping_postcode]', $shipping_postcode, $message );
				$message = str_replace( '[_payment_method_tittle]', $payment_method_tittle, $message );
				$message = str_replace( '[_order_shipping]', $order_shipping, $message );
				$message = str_replace( '[_order_total]', $order_total, $message );
				$message = str_replace( '[_refundable_amount]', $refundable_amount, $message );
				$ced_rnx_odr = wc_get_order( $orderid );
				$message = str_replace( '[formatted_shipping_address]', $ced_rnx_odr->get_formatted_shipping_address(), $message );
				$message = str_replace( '[formatted_billing_address]', $ced_rnx_odr->get_formatted_billing_address(), $message );

				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
				$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );
				$ced_rnx_notification_exchange_approve_template = get_option( 'ced_rnx_notification_exchange_approve_template', 'no' );
				$mwb_dis_tot = 0;
				$ced_flag = false;

				$fname = get_option( 'ced_rnx_notification_from_name' );
				$fmail = get_option( 'ced_rnx_notification_from_mail' );

				$ced_rnx_enable_return_ship_label = get_option( 'ced_rnx_enable_return_ship_label', 'no' );
				if ( $ced_rnx_enable_return_ship_label == 'on' ) {
					$headers = array();
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					$to = get_post_meta( $orderid, '_billing_email', true );
					$subject = get_option( 'ced_rnx_return_slip_mail_subject' );
					$subject = str_replace( '[order]', '#' . $orderid, $subject );
					$ced_rnx_order_for_label = wc_get_order( $orderid );
					$ced_rnx_shiping_address = $ced_rnx_order_for_label->get_formatted_shipping_address();
					if ( $ced_rnx_shiping_address == '' ) {
						$ced_rnx_shiping_address = $ced_rnx_order_for_label->get_formatted_billing_address();
					}
					$message1 = get_option( 'ced_rnx_return_ship_template' );
					$message1 = str_replace( '[username]', $fullname, $message1 );
					$message1 = str_replace( '[order]', '#' . $orderid, $message1 );
					$message1 = str_replace( '[siteurl]', home_url(), $message1 );
					$message1 = str_replace( '[Tracking_Id]', 'ID#' . $orderid, $message1 );
					$message1 = str_replace( '[Order_shipping_address]', $ced_rnx_shiping_address, $message1 );
					$message1 = str_replace( '[formatted_shipping_address]', $ced_rnx_order_for_label->get_formatted_shipping_address(), $message1 );
					$message1 = str_replace( '[formatted_billing_address]', $ced_rnx_order_for_label->get_formatted_billing_address(), $message1 );

					$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
					$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
					$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
					$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );

					$subject = str_replace( '[username]', $fullname, $subject );
					$subject = str_replace( '[order]', '#' . $orderid, $subject );
					$subject = str_replace( '[siteurl]', home_url(), $subject );
					$ced_rnx_restrict_mails = get_option( 'ced_rnx_exchange_restrict_customer_mails', true );
					if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
						wc_mail( $to, $subject, $message1, $headers );
					}
				}

				update_post_meta( $order_id, 'ced_rnx_exchange_order', $orderid );
				update_post_meta( $orderid, "date-$date", $order_id );
				update_post_meta( $orderid, 'mwb_rnx_status_exchanged', $mwb_dis_tot );

				$ex_fr = '';
				
				foreach ( $order_detail->get_items() as $item_id => $item ) {
				    $fromk = 0;
					if ( isset( $exchanged_from_products ) && ! empty( $exchanged_from_products ) ) {
						foreach ( $exchanged_from_products as $k => $product_data ) {
							if ( $item['product_id'] == $product_data['product_id'] && $item['variation_id'] == $product_data['variation_id'] ) {
								$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
								if($product_data['qty']>1){
									$item['qty'] = $item['qty'] - $product_data['qty'];
								}else{

								}
								$args['qty'] = $item['qty'];
								$ex_fr = $ex_fr . $item['name'] . '(SKU : ' . $product->get_sku() . ') x ' . $product_data['qty'] . ' | ';
								if ( WC()->version < '3.0.0' ) {
									$order->update_product( $item_id, $product, $args );
								} else {
									wc_update_order_item_meta( $item_id, '_qty', $item['qty'] );

									if ( $product->backorders_require_notification() && $product->is_on_backorder( $args['qty'] ) ) {
										$item->add_meta_data( apply_filters( 'woocommerce_backordered_item_meta_name', __( 'Backordered', 'woocommerce' ) ), $args['qty'] - max( 0, $product->get_stock_quantity() ), true );
									}
									$item_data = $item->get_data();

									$price_excluded_tax = wc_get_price_excluding_tax( $product, array( 'qty' => 1 ) );
									$price_tax_excluded = $item_data['total'] / $item_data['quantity'];

									$args['subtotal'] = $price_excluded_tax * $args['qty'];
									$args['total']  = $price_tax_excluded * $args['qty'];

									$item->set_order_id( $orderid );
									$item->set_props( $args );
									$item->save();
								}
								$exchange_warranty['from'][$fromk]['order_id'] = (int)$orderid;
								$exchange_warranty['from'][$fromk]['material_code'] = $product->get_sku();
								//break;
							}
							$fromk++;
						}
					}
				}

				$order_detail->calculate_totals();
				// $order_detail->update_status( 'wc-completed' );
				$order_detail->update_status( 'wc-exchange-approved' ); // old order status to be exchange approved - 19-11-22

				$order = (object) $order_detail->get_address( 'shipping' );

				// other info
				update_post_meta( $order_id, '_payment_method', $payment_method );
				update_post_meta( $order_id, '_payment_method_title', $payment_method_title );
				update_post_meta( $order_id, '_date_completed', $date_completed );
				update_post_meta( $order_id, '_date_paid', $date_paid );
				update_post_meta( $order_id, '_completed_date', $completed_date );
				update_post_meta( $order_id, '_cart_discount', $cart_discount );
				update_post_meta( $order_id, '_order_shipping', $order_shipping );
				update_post_meta( $order_id, '_transaction_id', $transaction_id );

				// Shipping info
				update_post_meta( $order_id, '_customer_user', $user_id );
				update_post_meta( $order_id, '_shipping_address_1', $order->address_1 );
				update_post_meta( $order_id, '_shipping_address_2', $order->address_2 );
				update_post_meta( $order_id, '_shipping_city', $order->city );
				update_post_meta( $order_id, '_shipping_state', $order->state );
				update_post_meta( $order_id, '_shipping_postcode', $order->postcode );
				update_post_meta( $order_id, '_shipping_country', $order->country );
				update_post_meta( $order_id, '_shipping_company', $order->company );
				update_post_meta( $order_id, '_shipping_first_name', $order->first_name );
				update_post_meta( $order_id, '_shipping_last_name', $order->last_name );
				update_post_meta( $order_id, '_shipping_email', $order->email );
				update_post_meta( $order_id, '_shipping_phone', $order->phone, true );
				update_post_meta( $order_id, '_shipping_salutation', $_shipping_salutation, true );

				// billing info
				$order_detail = wc_get_order( $orderid );
				$order_detail->calculate_totals();
				$order = (object) $order_detail->get_address( 'billing' );

				add_post_meta( $order_id, '_billing_first_name', $order->first_name, true );
				add_post_meta( $order_id, '_billing_last_name', $order->last_name, true );
				add_post_meta( $order_id, '_billing_company', $order->company, true );
				add_post_meta( $order_id, '_billing_address_1', $order->address_1, true );
				add_post_meta( $order_id, '_billing_address_2', $order->address_2, true );
				add_post_meta( $order_id, '_billing_city', $order->city, true );
				add_post_meta( $order_id, '_billing_state', $order->state, true );
				add_post_meta( $order_id, '_billing_postcode', $order->postcode, true );
				add_post_meta( $order_id, '_billing_country', $order->country, true );
				add_post_meta( $order_id, '_billing_email', $order->email, true );
				add_post_meta( $order_id, '_billing_phone', $order->phone, true );
				add_post_meta( $order_id, '_billing_salutation', $_billing_salutation, true );
				
				// $exchanged_products
				$order = wc_get_order( $order_id );
				if ( WC()->version >= '3.0.0' ) {
					if ( ! $order->get_order_key() ) {
						update_post_meta( $order_id, '_order_key', 'wc-' . uniqid( 'order_' ) );
					}
				}
				$orders = wc_get_order( $order_id );
				$new_url = $orders->get_checkout_order_received_url();
				$message = str_replace( '[new_order_id_created]', '#' . $order_id, $message );
				$message = str_replace( '[new_order_typ_url]', $new_url, $message );

				if ( isset( $ced_rnx_notification_exchange_approve_template ) && $ced_rnx_notification_exchange_approve_template == 'on' ) {
					$html_content = $message;
				} else {
					$ced_flag = true;

				}
				if ( $ced_flag ) {
					$html_content = $this->create_exchange_approve_mail_html( $mail_header, $message, $orderid, $order_id, $exchange_details, $mail_footer );
				}
				$left_amount = get_post_meta( $orderid, 'ced_rnx_left_amount', true );
				if ( $left_amount > 0 ) {

					$new_fee  = new WC_Order_Item_Fee();
					$new_fee->set_name( esc_attr( 'Refundable Amount' ) );
					$new_fee->set_total( $left_amount );
					$new_fee->set_tax_class( '' );
					$new_fee->set_tax_status( 'none' );
					$new_fee->save();
					$item_id = $order_detail->add_item( $new_fee );
					$order_detail->calculate_totals();
				}
				$headers = array();
				$headers[] = "From: $fname <$fmail>";
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$subject = get_option( 'ced_rnx_notification_exchange_approve_subject' );

				$to = get_post_meta( $orderid, '_billing_email', true );

				$subject = str_replace( '[username]', $fullname, $subject );
				$subject = str_replace( '[order]', '#' . $orderid, $subject );
				$subject = str_replace( '[siteurl]', home_url(), $subject );

				$ced_rnx_restrict_mails = get_option( 'ced_rnx_exchange_restrict_customer_mails', true );
				if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
					wc_mail( $to, $subject, $html_content, $headers );
				}
				$ex_to = '';
				$tok = 0;
				if ( isset( $exchanged_products ) && ! empty( $exchanged_products ) ) {
				    
				    ksort($exchanged_products);
					foreach ( $exchanged_products as $exchanged_product ) {
					   //  echo "K Sort";
					   // echo "<pre>";
					   // print_r($exchanged_product);
					   // //echo "Selected Products POST";
					   // echo "<pre>";
					   // print_r($selected_product);
						if ( isset( $exchanged_product['variation_id'] ) ) {
							$product = wc_get_product( $exchanged_product['variation_id'] );
							$variation_product = new WC_Product_Variation( $exchanged_product['variation_id'] );
							$variation_attributes = $variation_product->get_variation_attributes();
							if ( isset( $exchanged_product['variations'] ) && ! empty( $exchanged_product['variations'] ) ) {
								$variation_attributes = $exchanged_product['variations'];
							}
							$variation_product_price = wc_get_price_excluding_tax( $variation_product, array( 'qty' => 1 ) );

							$variation_att['variation'] = $variation_attributes;

							$variation_att['totals']['subtotal'] = $exchanged_product['qty'] * $variation_product_price;
							$variation_att['totals']['total'] = $exchanged_product['qty'] * $variation_product_price;

							$item_id = $order->add_product( $variation_product, $exchanged_product['qty'], $variation_att );

							if ( $product->managing_stock() ) {
								$qty       = $exchanged_product['qty'];
								$new_stock = $product->reduce_stock( $qty );
							}
							$ex_to = $ex_to . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $exchanged_product['qty'] . ' | ';

							$price_exchange = $variation_product_price;
						} elseif ( isset( $exchanged_product['id'] ) ) {
							$product = wc_get_product( $exchanged_product['id'] );
							$item_id = $order->add_product( $product, $exchanged_product['qty'] );

							if ( $product->managing_stock() ) {
								$qty       = $exchanged_product['qty'];
								$new_stock = $product->reduce_stock( $qty );
							}
							$ex_to = $ex_to . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $exchanged_product['qty'] . ' | ';

							$price_exchange = $product->get_price();
						} else {
							$product = wc_get_product( $exchanged_product['id'] );
							$item_id = $order->add_product( $product, $exchanged_product['qty'] );
							if ( $product->managing_stock() ) {
								$qty       = $exchanged_product['qty'];
								$new_stock = $product->reduce_stock( $qty );
							}
							$ex_to = $ex_to . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $exchanged_product['qty'] . ' | ';

							$price_exchange = $product->get_price();
						}
				// 		foreach ($selected_product as $tokeys => $tovalues){
				// 		    echo "<br>";
				// 		    echo "approve_status - " . $tovalues['approve_status'];
				// 		    echo "<br>";
						    
				// 		    if($tovalues[$tok]['approve_status'] == '1'){
				// 			echo $exchange_warranty['to'][$tok]['order_id'] = (int)$order_id;
				// 			echo "<br>";
				// 			echo $exchange_warranty['to'][$tok]['material_code'] = "Accepted_".$product->get_sku();
				// 			echo "<br>";
				// 		}else{
				// 		    	echo $exchange_warranty['to'][$tok]['order_id'] = (int)$order_id;
				// 			echo "<br>";
				// 			echo $exchange_warranty['to'][$tok]['material_code'] = "rejected_".$product->get_sku();
				// 			echo "<br>";
				// 		}
				// // 		if($tovalues['approve_status'] == '2'){
				// // 			echo $exchange_warranty['to']['order_id'] = (int)$order_id;
				// // 			echo "<br>";
				// // 			echo $exchange_warranty['to']['material_code'] = "rejected_".$product->get_sku();
				// // 			echo "<br>";
				// // 		}elseif($tovalues['approve_status'] == '1'){
				// // 		    	echo $exchange_warranty['to']['order_id'] = (int)$order_id;
				// // 			echo "<br>";
				// // 			echo $exchange_warranty['to']['material_code'] = "Accepted_".$product->get_sku();
				// // 			echo "<br>";
				// // 		}
						   
				// 		}
						
					
				// 	if($selected_product[$tok]['approve_status'] == '1'){
				// 	    echo "<br>";
				// 			$exchange_warranty['to'][$tok]['order_id'] = (int)$order_id;
				// 			echo "<br>";
				// 			$exchange_warranty['to'][$tok]['material_code'] = $selected_product[$tok]['approve_status'].$product->get_sku();
				// 			echo "<br>";
				// 		}
				// 	else{
				// 			$exchange_warranty['to'][$tok]['order_id'] = (int)$order_id;
				// 			echo "<br>";
				// 			$exchange_warranty['to'][$tok]['material_code'] = $selected_product[$tok]['approve_status']." rejected_".$product->get_sku();
				// 		}	
					
				// 	$tok++;	
					
					}
					
				
					
				}
				//$ex_to = '';
				$tok = 0;
				if ( isset( $exchanged_products ) && ! empty( $exchanged_products ) ) {
				    foreach ( $selected_product as $selectedproduct_keys => $selected_products ) {
				        // echo "<pre>";
				        // print_r($selected_products);
				        
				            $exchange_warranty['to'][$tok]['order_id'] = (int)$order_id;
							//
							//echo ($result >= 40) ? "Passed" : " Failed";
							$exchange_warranty['to'][$tok]['material_code'] = ($selected_products['approve_status'] == 1) ? 'Accepted_'.$selected_products['sku'] : 'rejected_'.$selected_products['sku'];
							//echo "<br>";
				        
				    $tok++;	 
				    }
				   
				    
				}
					
				
				
				$ex_fr = trim( $ex_fr, '| ' );
				$ex_to = trim( $ex_to, '| ' );
				$exchange_note = __( 'Product Exchange Request from', 'woocommerce-refund-and-exchange' ) . ' { ' . $ex_fr . ' } ' . __( 'to', 'woocommerce-refund-and-exchange' ) . ' { ' . $ex_to . ' } ' . __( ' has been approved.', 'woocommerce-refund-and-exchange' );
				wc_get_order( $orderid )->add_order_note( $exchange_note );
				if ( isset( $added_fee ) && ! empty( $added_fee ) ) {
					if ( is_array( $added_fee ) ) {
						foreach ( $added_fee as $fee ) {

							$new_fee  = new WC_Order_Item_Fee();
							$new_fee->set_name( esc_attr( $fee['text'] ) );
							$new_fee->set_total( $fee['val'] );
							$new_fee->set_tax_class( '' );
							$new_fee->set_tax_status( 'none' );
							$new_fee->set_total_tax( $totalProducttax );
							$new_fee->save();
							$item_id = $order->add_item( $new_fee );
						}
					}
				}
				$discount = 0;
				if ( isset( $exchanged_from_products ) && ! empty( $exchanged_from_products ) ) {
					$totalProducttax = '';
					$exchanged_from_products_count = count( $exchanged_from_products );
					$l_amount = $left_amount / $exchanged_from_products_count;
					foreach ( $exchanged_from_products as $exchanged_product ) {
						if ( isset( $exchanged_product['variation_id'] ) && $exchanged_product['variation_id'] > 0 ) {
							$p = wc_get_product( $exchanged_product['variation_id'] );
						} else {
							$p = wc_get_product( $exchanged_product['product_id'] );
						}
						if ( true ) {
							$_tax = new WC_Tax();

							$prePrice = $p->get_price_excluding_tax();
							$pTax = $exchanged_product['qty'] * ( $p->get_price() - $prePrice );
							$totalProducttax += $pTax;
							$item_rate = round( array_shift( $rates ) );
							$price = $exchanged_product['qty'] * $prePrice;
							$discount += $price;
							$tax_rates = WC_Tax::get_rates( $p->get_tax_class() );
							if ( ! empty( $tax_rates ) ) {
								$tax_rate = reset( $tax_rates );

								$dis_tax = $tax_rate['rate'];
							}
						} else {
							$price = $exchanged_product['qty'] * $exchanged_product['price'];
							$discount += $price;
						}
					}
				}
				$dis_tax_amu = 0;
				if ( $left_amount > 0 ) {
					$mwb_rnx_obj = $order;
					$amount_discount = $mwb_rnx_obj->calculate_totals();
					$total_ptax = $mwb_rnx_obj->get_total_tax();
					$amount_discount = $amount_discount - $total_ptax;

					$new_fee  = new WC_Order_Item_Fee();
					$new_fee->set_name( esc_attr( 'Discount' ) );
					$new_fee->set_total( -$amount_discount );
					$new_fee->set_tax_class( '' );
					$new_fee->set_tax_status( 'none' );
					$new_fee->set_total_tax( '' );
					$new_fee->save();
					$item_id = $order->add_item( $new_fee );

				} else {
					if ( $discount > 0 ) {

						$mwb_rnx_obj = wc_get_order( $orderid );
						$tax_rate = 0;
						$tax = new WC_Tax();
						$country_code = WC()->countries->countries[ $mwb_rnx_obj->billing_country ]; // or populate from order to get applicable rates
						$rates = $tax->find_rates( array( 'country' => $country_code ) );
						foreach ( $rates as $rate ) {
							$tax_rate = $rate['rate'];
						}

						$total_ptax = $discount * $tax_rate / 100;
						$orderval = $discount + $total_ptax;
						$orderval = round( $orderval, 2 );

						// Coupons used in the order LOOP (as they can be multiple)
						if ( WC()->version < '3.7.0' ) {
							$coupon_used = $mwb_rnx_obj->get_used_coupons();
						} else {
							$coupon_used = $mwb_rnx_obj->get_coupon_codes();
						}
						foreach ( $coupon_used as $coupon_name ) {
							$coupon_post_obj = get_page_by_title( $coupon_name, OBJECT, 'shop_coupon' );
							$coupon_id = $coupon_post_obj->ID;
							$coupons_obj = new WC_Coupon( $coupon_id );

							 $coupons_amount = $coupons_obj->get_amount();
							 $coupons_type = $coupons_obj->get_discount_type();
							if ( $coupons_type == 'percent' ) {
								$finaldiscount = $orderval * $coupons_amount / 100;
							}
						}

						$discount = $orderval;
						$discount = $discount * 100 / ( 100 + $tax_rate );

						$new_fee  = new WC_Order_Item_Fee();
						$new_fee->set_name( esc_attr( 'Discount' ) );
						$new_fee->set_total( -$discount );
						$new_fee->set_tax_class( '' );
						$new_fee->set_tax_status( 'none' );
						$new_fee->set_total_tax( '' );
						$new_fee->save();
						$order->add_item( $new_fee );
						$items_key = $new_fee->get_id();
						$dis_tax_amu = ( $discount * $dis_tax ) / 100;
					}
				}

				$order_total = $order->calculate_totals();
				$order_total = $dis_tax_amu + $order_total;
				$order->set_total( $order_total, 'total' );

				if ( $order_total == 0 ) {
					$order->update_status( 'wc-processing' );
				} else {
					$manage_stock = get_option( 'ced_rnx_exchange_request_manage_stock' );
					if ( $manage_stock == 'yes' ) {
						if ( isset( $exchanged_products ) && ! empty( $exchanged_products ) ) {
							foreach ( $exchanged_products as $key => $prod_data ) {
								if ( $prod_data['variation_id'] > 0 ) {
									$product = wc_get_product( $prod_data['variation_id'] );
								} else {
									$product = wc_get_product( $prod_data['id'] );
								}
								if ( $product->managing_stock() ) {
									$avaliable_qty = $prod_data['qty'];
									if ( $prod_data['variation_id'] > 0 ) {
										$total_stock = get_post_meta( $prod_data['variation_id'], '_stock', true );
										$total_stock = $total_stock - $avaliable_qty;
										wc_update_product_stock( $prod_data['variation_id'], $total_stock, 'set' );
									} else {
										$total_stock = get_post_meta( $prod_data['id'], '_stock', true );
										$total_stock = $total_stock - $avaliable_qty;
										wc_update_product_stock( $prod_data['id'], $total_stock, 'set' );
									}
								}
							}
						}
					}
				}
				if ( $includeTax ) {
					$order_total = $order_total - $totalProducttax;
				}
				update_post_meta( $order_id, 'ced_rnx_exchange_warrantry_extrajson', $exchange_warranty_display ); // extra json store
				update_post_meta( $order_id, 'ced_rnx_exchange_warrantry', $exchange_warranty ); // extra json store
				update_post_meta( $orderid, 'ced_rnx_exchange_product', $exchange_details );

			}

		}
		/**
		 * This function is used for catalog count
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		public function ced_rnx_catalog_count() {
			$catalog_count = $_POST['catalog_count'];
			update_option( 'catalog_count', $catalog_count, 'yes' );
			echo json_encode( $response );
			wp_die();
		}

		/**
		 * This function is used for catalog deletion
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		public function ced_rnx_catalog_delete() {
			$catalog_db_index = $_POST['catalog_db_index'];
			$ced_rnx_catalog = get_option( 'catalog' );
			foreach ( $ced_rnx_catalog as $key => $value ) {
				if ( $key == 'Catalog' . $catalog_db_index ) {
					array_splice( $ced_rnx_catalog, ( $catalog_db_index - 1 ), 1 );
					update_option( 'catalog', $ced_rnx_catalog, 'yes' );
				}
			}
			wp_die();
		}

		/**
		 * This function is show exchange request product on new exchange order
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		public function ced_rnx_show_order_exchange_product( $order_id ) {
			$exchanged_order = get_post_meta( $order_id, 'ced_rnx_exchange_order', true );
			$exchange_details = get_post_meta( $exchanged_order, 'ced_rnx_exchange_product', true );
			$order = new WC_Order( $exchanged_order );
			$line_items  = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );

			if ( isset( $exchange_details ) && ! empty( $exchange_details ) ) {
				foreach ( $exchange_details as $date => $exchange_detail ) {
					if ( $exchange_detail['status'] == 'complete' ) {
						$exchanged_products = $exchange_detail['from'];
						break;
					}
				}
			}

			if ( isset( $exchanged_products ) && ! empty( $exchanged_products ) ) {
				?>
				<thead>
				<tr>
					<th colspan="6"><b><?php _e( 'Exchange Products', 'woocommerce-refund-and-exchange' ); ?></b></th>
					<th></th>
				</tr>
					<tr>
						<th><?php _e( 'Item', 'woocommerce-refund-and-exchange' ); ?></th>
						<th colspan="2"><?php _e( 'Name', 'woocommerce-refund-and-exchange' ); ?></th>
						<th><?php _e( 'Cost', 'woocommerce-refund-and-exchange' ); ?></th>
						<th><?php _e( 'Qty', 'woocommerce-refund-and-exchange' ); ?></th>
						<th><?php _e( 'Total', 'woocommerce-refund-and-exchange' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<?php
				foreach ( $line_items as $item_id => $item ) {

					foreach ( $exchanged_products as $key => $exchanged_product ) {
						if ( $item_id == $exchanged_product['item_id'] ) {
							$_product  = $item->get_product();
							$item_meta = wc_get_order_item_meta( $item_id, $key );
							$thumbnail     = $_product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $_product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
							?>
							<tr>
								<td class="thumb">
								<?php
									echo '<div class="wc-order-item-thumbnail">' . wp_kses_post( $thumbnail ) . '</div>';
								?>
								</td>
								<td class="name" colspan="2">
								<?php
									echo esc_html( $item['name'] );
								if ( $_product && $_product->get_sku() ) {
									echo '<div class="wc-order-item-sku"><strong>' . __( 'SKU:', 'woocommerce-refund-and-exchange' ) . '</strong> ' . esc_html( $_product->get_sku() ) . '</div>';
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
								if ( WC()->version < '3.1.0' ) {
									$item_meta      = new WC_Order_Item_Meta( $item, $_product );
									$item_meta->display();
								} else {
									$item_meta      = new WC_Order_Item_Product( $item, $_product );
									wc_display_item_meta( $item_meta );
								}
								?>
								</td>
								<td><?php echo ced_rnx_format_price( $exchanged_product['price'] ); ?></td>
								<td><?php echo $exchanged_product['qty']; ?></td>
								<td><?php echo ced_rnx_format_price( $exchanged_product['price'] * $exchanged_product['qty'] ); ?></td>
								<td></td>
							</tr>
							<?php
						}
					}
				}
			}
		}

		function create_exchange_approve_mail_html( $mail_header, $message, $orderid, $order_id, $exchange_details, $mail_footer ) {

			if ( isset( $exchange_details ) && ! empty( $exchange_details ) ) {
				foreach ( $exchange_details as $date => $exchange_detail ) {
					$requested_products = $exchange_details[ $date ]['from'];
					$exchanged_to_products = $exchange_details[ $date ]['to'];
				}
			}

			$html_content = '<html>
								<body>
								<style>
								body {
								    box-shadow: 2px 2px 10px #ccc;
								    color: #767676;
								    font-family: Arial,sans-serif;
								    margin: 80px auto;
								    max-width: 700px;
								    padding-bottom: 30px;
								    width: 100%;
								}

								h2 {
									font-size: 30px;
									margin-top: 0;
									color: #fff;
									padding: 40px;
									background-color: #557da1;
								}

								h4 {
									color: #557da1;
									font-size: 20px;
									margin-bottom: 10px;
								}

								.content {
									padding: 0 40px;
								}

								.Customer-detail ul li p {
									margin: 0;
								}

								.details .Shipping-detail {
									width: 40%;
									float: right;
								}

								.details .Billing-detail {
									width: 60%;
									float: left;
								}

								.details .Shipping-detail ul li,.details .Billing-detail ul li {
									list-style-type: none;
									margin: 0;
								}

								.details .Billing-detail ul,.details .Shipping-detail ul {
									margin: 0;
									padding: 0;
								}

								.clear {
									clear: both;
								}

								table,td,th {
									border: 2px solid #ccc;
									padding: 15px;
									text-align: left;
								}

								table {
									border-collapse: collapse;
									width: 100%;
								}

								.info {
									display: inline-block;
								}

								.bold {
									font-weight: bold;
								}

								.footer {
									margin-top: 30px;
									text-align: center;
									color: #99B1D8;
									font-size: 12px;
								}
								dl.variation dd {
								    font-size: 12px;
								    margin: 0;
									}
								</style>
								<div class="header" style="text-align: center; padding: 10px;">
								' . $mail_header . '
								</div>

								<div class="header">
									<h2>' . __( 'Your Exchange Request is Accepted.', 'woocommerce-refund-and-exchange' ) . '</h2>
								</div>

								<div class="content">
									<div class="reason">
										<p>' . $message . '</p>
									</div>
									<div class="Order">
										<h4>Order #' . $orderid . '</h4>
										<h4>' . __( 'Exchanged From', 'woocommerce-refund-and-exchange' ) . '</h4>
												<table>
												<tbody>
													<tr>
														<th>' . __( 'Product', 'woocommerce-refund-and-exchange' ) . '</th>
														<th>' . __( 'Quantity', 'woocommerce-refund-and-exchange' ) . '</th>
														<th>' . __( 'Price', 'woocommerce-refund-and-exchange' ) . '</th>
													</tr>';
									$order = wc_get_order( $orderid );

			if ( isset( $requested_products ) && ! empty( $requested_products ) ) {
				$total = 0;
				foreach ( $order->get_items() as $item_id => $item ) {
					$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
					foreach ( $requested_products as $requested_product ) {
						if ( $item_id == $requested_product['item_id'] ) {
							if ( isset( $requested_product['variation_id'] ) && $requested_product['variation_id'] > 0 ) {
								$requested_product_obj = wc_get_product( $requested_product['variation_id'] );
							} else {
								$requested_product_obj = wc_get_product( $requested_product['product_id'] );
							}
							$subtotal = $requested_product['price'] * $requested_product['qty'];
							$total += $subtotal;
							if ( WC()->version < '3.1.0' ) {
								$item_meta      = new WC_Order_Item_Meta( $item, $_product );
								$item_meta_html = $item_meta->display( true, true );
							} else {
								$item_meta      = new WC_Order_Item_Product( $item, $_product );
								$item_meta_html = wc_display_item_meta( $item_meta, array( 'echo' => false ) );
							}

							$html_content .= '<tr><td>' . $item['name'] . '<br>';
							$html_content .= '<small>' . $item_meta_html . '</small><td>' . $requested_product['qty'] . '</td><td>' . ced_rnx_format_price( $requested_product['price'] * $requested_product['qty'] ) . '</td></tr>';
						}
					}
				}
			}
									$html_content .= '
												<tr>
													<th colspan="2">' . __( 'Total', 'woocommerce-refund-and-exchange' ) . ':</th>
													<td>' . ced_rnx_format_price( $total ) . '</td>
												</tr>
											</tbody>
										</table>
										<h4>' . __( 'Exchanged To', 'woocommerce-refund-and-exchange' ) . '</h4>
										<table>
											<tbody>
												<tr>
													<th>' . __( 'Product', 'woocommerce-refund-and-exchange' ) . '</th>
													<th>' . __( 'Quantity', 'woocommerce-refund-and-exchange' ) . '</th>
													<th>' . __( 'Price', 'woocommerce-refund-and-exchange' ) . '</th>
												</tr>';

											$total_price = 0;
			if ( isset( $exchanged_to_products ) && ! empty( $exchanged_to_products ) ) {
				foreach ( $exchanged_to_products as $key => $exchanged_product ) {
					$variation_attributes = array();
					if ( isset( $exchanged_product['variation_id'] ) ) {
						if ( $exchanged_product['variation_id'] ) {
							$variation_product = new WC_Product_Variation( $exchanged_product['variation_id'] );
							$variation_attributes = $variation_product->get_variation_attributes();
							$variation_labels = array();
							foreach ( $variation_attributes as $label => $value ) {
								if ( is_null( $value ) || $value == '' ) {
									$variation_labels[] = $label;
								}
							}

							if ( isset( $exchanged_product['variations'] ) && ! empty( $exchanged_product['variations'] ) ) {
								$variation_attributes = $exchanged_product['variations'];
							}
						}
					}

					if ( isset( $exchanged_product['p_id'] ) ) {
						if ( $exchanged_product['p_id'] ) {
							$grouped_product = new WC_Product_Grouped( $exchanged_product['p_id'] );
							$grouped_product_title = $grouped_product->get_title();
						}
					}

					if ( isset( $exchanged_product['variation_id'] ) ) {

						$product = wc_get_product( $exchanged_product['variation_id'] );
					} else {
						$product = wc_get_product( $exchanged_product['id'] );
					}
					$pro_price = $exchanged_product['qty'] * $exchanged_product['price'];
					$total_price += $pro_price;
					$title = '';
					if ( isset( $exchanged_product['p_id'] ) ) {
						$title .= $grouped_product_title . ' -> ';
					}
					$title .= $product->get_title();

					if ( isset( $variation_attributes ) && ! empty( $variation_attributes ) ) {
						$title .= wc_get_formatted_variation( $variation_attributes );
					}

					$html_content .= '<tr>
																	<td>' . $title . '</td>
																	<td>' . $exchanged_product['qty'] . '</td>
																	<td>' . ced_rnx_format_price( $pro_price ) . '</td>
																</tr>';

				}
			}
											$html_content .= '<tr>
															<th colspan="2">' . __( 'Sub-Total', 'woocommerce-refund-and-exchange' ) . ':</th>
															<td>' . ced_rnx_format_price( $total_price ) . '</td>
														</tr>';
			if ( isset( $added_fee ) && ! empty( $added_fee ) ) {
				if ( is_array( $added_fee ) ) {
					foreach ( $added_fee as $fee ) {
						$total_price += $fee['val'];
						$html_content .= '<tr>
																		<th colspan="2">' . $fee['text'] . '</th>
																		<td>' . ced_rnx_format_price( $fee['val'] ) . '</td>
																	</tr>';
					}
				}
			}
											$html_content .= '<tr>
															<th colspan="2">' . __( 'Grand Total', 'woocommerce-refund-and-exchange' ) . '</th>
																<td>' . ced_rnx_format_price( $total_price ) . '</td>
															</tr>';

													$html_content .= '</tbody>
												</table>
											</div>';
											$mwb_cpn_dis = $order->get_discount_total();
											$mwb_cpn_tax = $order->get_discount_tax();

											$mwb_dis_tot = 0;
			if ( $total_price - ( $total + $mwb_dis_tot ) > 0 ) {
				$extra_amount = $total_price - ( $total + $mwb_dis_tot );
				$html_content .= '<h2>Extra Amount : ' . ced_rnx_format_price( $extra_amount ) . '</h2>';
			} else {
				if ( $mwb_dis_tot > $total_price ) {
					$total_price = 0;
				} else {
					$total_price = $total_price - $mwb_dis_tot;
				}
				$left_amount = $total - $total_price;
				update_post_meta( $orderid, 'ced_rnx_left_amount', $left_amount );

				$html_content .= '<h2><i>Left Amount After Exchange:</i> ' . ced_rnx_format_price( $left_amount ) . '</h2>';
			}

											$orders = wc_get_order( $order_id );

											$new_url = $orders->get_checkout_order_received_url();

											$html_content .= '<div><b>' . __( 'Your new order id is: #', 'woocommerce-refund-and-exchange' ) . $order_id . '</b></div>';
											$html_content .= '<a href=' . $new_url . '>' . __( 'Click here', 'woocommerce-refund-and-exchange' ) . '</a>';

											$html_content .= ' <div class="Customer-detail">
															<h4>' . __( 'Customer details', 'woocommerce-refund-and-exchange' ) . '</h4>
															<ul>
																<li><p class="info">
																		<span class="bold">' . __( 'Email', 'woocommerce-refund-and-exchange' ) . ': </span>' . get_post_meta( $orderid, '_billing_email', true ) . '
																	</p></li>
																<li><p class="info">
																		<span class="bold">' . __( 'Tel', 'woocommerce-refund-and-exchange' ) . ': </span>' . get_post_meta( $orderid, '_billing_phone', true ) . '
																	</p></li>
															</ul>
														</div>
														<div class="details">
															<div class="Shipping-detail">
																<h4>' . __( 'Shipping Address', 'woocommerce-refund-and-exchange' ) . '</h4>
																' . $order->get_formatted_shipping_address() . '
															</div>
															<div class="Billing-detail">
																<h4>' . __( 'Billing Address', 'woocommerce-refund-and-exchange' ) . '</h4>
																' . $order->get_formatted_billing_address() . '
															</div>
															<div class="clear"></div>
														</div>
													</div>
													<div style="text-align: center; padding: 10px;" class="footer">
													' . $mail_footer . '
													</div>
												</body>
												</html>';

												return $html_content;
		}

		/**
		 * This function is metabox template for order msg history.
		 *
		 * @name ced_rnx_order_msg_history.
		 */
		public function ced_rnx_order_msg_history() {
			global $post, $thepostid, $theorder;
			include_once CED_REFUND_N_EXCHANGE_DIRPATH . 'admin/ced-rnx-admin-order-msg-history-meta.php';
		}

		public function ced_rnx_order_messages_save() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$msg = isset( $_POST['msg'] ) ? sanitize_text_field( wp_unslash( $_POST['msg'] ) ) : '';
				$order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_id'] ) ) : 0;
				$order = new WC_Order( $order_id );
				$to = $order->billing_email;
				$sender = 'Shop Manager';
				$flag = ced_rnx_send_order_msg_callback( $order_id, $msg, $sender, $to );
				echo $flag;
				wp_die();
			}
		}
	}
	new Ced_refund_and_exchange_order_meta();
}
?>