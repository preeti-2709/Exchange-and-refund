<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CED_rnx_order_exchange' ) ) {

	/**
	 * This is class for managing exchange process at front end.
	 *
	 * @name    CED_rnx_order_exchange
	 * @category Class
	 * @author wpswings<webmaster@wpswings.com>
	 */
	class CED_rnx_order_exchange {

		/**
		 * This function is for construct of class
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		public function __construct() {

			add_action( 'woocommerce_order_details_after_order_table', array( $this, 'ced_rnx_order_exchange_button' ) );
			add_action( 'wp_ajax_ced_rnx_exchange_products', array( $this, 'ced_rnx_exchange_products_callback' ) );
			add_action( 'wp_ajax_nopriv_ced_rnx_exchange_products', array( $this, 'ced_rnx_exchange_products_callback' ) );
			add_action( 'wp_ajax_ced_set_exchange_session', array( $this, 'ced_rnx_set_exchange_session' ) );
			add_action( 'wp_ajax_nopriv_ced_set_exchange_session', array( $this, 'ced_rnx_set_exchange_session' ) );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'ced_rnx_add_exchange_products' ), 8 );
			add_action( 'wp_ajax_ced_rnx_add_to_exchange', array( $this, 'ced_rnx_add_to_exchange_callback' ) );
			add_action( 'wp_ajax_nopriv_ced_rnx_add_to_exchange', array( $this, 'ced_rnx_add_to_exchange_callback' ) );
			add_action( 'wp_ajax_ced_rnx_exchnaged_product_remove', array( $this, 'ced_rnx_exchnaged_product_remove_callback' ) );
			add_action( 'wp_ajax_nopriv_ced_rnx_exchnaged_product_remove', array( $this, 'ced_rnx_exchnaged_product_remove_callback' ) );
			add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'ced_rnx_exchnaged_product_add_button' ) );
			add_action( 'wp_ajax_ced_rnx_submit_exchange_request', array( $this, 'ced_rnx_submit_exchange_request_callback' ) );
			add_action( 'wp_ajax_nopriv_ced_rnx_submit_exchange_request', array( $this, 'ced_rnx_submit_exchange_request_callback' ) );
			add_action( 'woocommerce_thankyou', array( $this, 'ced_rnx_exchange_pay_cancel' ), 10, 1 );

			add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'ced_rnx_my_account_my_orders_actions' ), 100, 2 );

			add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'ced_rnx_my_account_change_exchange_to_completed' ), 100, 2 ); // 21-11-22(change exchange to completed status-aarti)
		}


		/**
		 * This function is to remove cancel button from my order detail page. - 21-11-22 - aarti
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function ced_rnx_my_account_my_orders_actions( $actions, $order ) {
			if ( $order->get_status() == 'exchange-approve' ) {
				unset( $actions['cancel'] );
			}
			$order_id = $order->get_id();
			$exchange_orderid = get_post_meta( $order_id, 'ced_rnx_exchange_order', true );

			if($order->get_status() == 'processing' && !empty($exchange_orderid)){
				unset( $actions['ced_rnx_cancel_order_product'] );
			}
			$warranty_orderid = get_post_meta( $order_id, 'ced_rnx_warranty_order', true );

			if($order->get_status() == 'processing' && !empty($warranty_orderid)){
				unset( $actions['ced_rnx_cancel_order_product'] );
			}


			if($order->get_status() == 'completed' && !empty($warranty_orderid)){
				unset( $actions['exchange'] );
				unset($actions['return']);
			}
			return $actions;

		}
		/**
		 * This function is to change status exchange-approved to completed. - 21-11-22 - aarti
		 */
		function ced_rnx_my_account_change_exchange_to_completed( $actions, $order ) {
			$order_id = $order->get_id();

			//old status change exchange to completed
			if($order->get_status() == 'ready-to-ship'){
				$exchange_orderid = get_post_meta( $order_id, 'ced_rnx_exchange_order', true );
				if($order->get_status() == 'ready-to-ship' && !empty($exchange_orderid)){
					$exchange_order = wc_get_order( $exchange_orderid );
					if($exchange_order->get_status() != 'completed'){
						$exchange_order->update_status( 'wc-completed', __( 'User Request of Exchange Product is approved', 'woocommerce-refund-and-exchange' ) );
							$exchange_note = __( 'Product Exchange Request - Order status changed from Exchange Approved to Completed.', 'woocommerce-refund-and-exchange' );
							$exchange_order->add_order_note( $exchange_note );
					}					
				}				
			}
			//exchange reject status change to exchange reject to completed
			if($order->get_status() == 'exchange-cancel'){
				// $exchange_orderid = get_post_meta( $order_id, 'ced_rnx_exchange_order', true );
				$return_datas = get_post_meta( $order_id, 'ced_rnx_exchange_product', true );
				if ( isset( $return_datas ) && ! empty( $return_datas ) ) {
					foreach ( $return_datas as $key => $return_data ) {
						$cancel_date = $return_data['cancel_date'];
						$status = $return_data['status'];
					}
				}
				if( strtotime($cancel_date) < strtotime(date("d-m-Y")) && $status == 'cancel') {
					$order->update_status( 'wc-completed', __( 'User Request of Exchange Product is cancel', 'woocommerce-refund-and-exchange' ) );
							$exchange_cancel_note = __( 'Product Exchange Request - Order status changed from Exchange Cancelled to Completed.', 'woocommerce-refund-and-exchange' );
							$order->add_order_note( $exchange_cancel_note );
				}
				
			}
			return $actions;
	
		}

		/**
		 * This function is to add pay or cancel button for guest user
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function ced_rnx_exchange_pay_cancel( $order_id ) {
			$order = wc_get_order( $order_id );
			$payment_url = $order->get_checkout_payment_url();
			$cancel_url = $order->get_cancel_order_url( wc_get_page_permalink( 'myaccount' ) );
			if ( $order->needs_payment() ) {
				?>
				<a class="button pay" href="<?php echo $payment_url; ?>"><?php _e( 'Pay', 'woocommerce-refund-and-exchange' ); ?></a>
				<?php
			}
		}

		/**
		 * This function is to submit exchange product request
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function ced_rnx_submit_exchange_request_callback() {
			$Exchange_Data = $_POST['data_list'];
			$Exchange_Modify =  stripslashes($Exchange_Data);
			$Exchange_explode = explode("/",$Exchange_Modify);
			$Final_Data = json_decode($Exchange_explode[0],true);
			$_POST = $Final_Data;
			// $check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $_POST ) {
				$order_id = $_POST['orderid'];
				$current_user = wp_get_current_user();
				$user_email = $current_user->user_email;
				$user_name = $current_user->display_name;
				$order_id = $_POST['orderid'];
				$subject = trim( $_POST['subject'] );
				$reason = trim( $_POST['reason'] );
            // Save Exchange Request Product
				$products = array();
				if ( null != WC()->session->get( 'exchange_requset' ) ) {
					$products = WC()->session->get( 'exchange_requset' );
				}				
			$pending = true;
			if ( isset( $products ) && ! empty( $products ) ) {
				foreach ( $products as $date => $product ) {
						if ( $product['status'] == 'pending' ) {
							$products[ $date ]['orderid'] = $_POST['orderid'];
							$products[ $date ]['subject'] = $_POST['subject'];
							$products[ $date ]['reason'] = $_POST['reason'];
							$products[ $date ]['status'] = 'pending'; // update requested products
							$pending = false;
							break;
						}
				}
			        $arrreason = array_values(array_filter($_POST['product_reason']));
					//Add Extra Json Meta in From Product
					foreach($arrreason as $key_id => $key_value){
						$products[ $date ]['from'][$key_id]['product_id_key'] = $key_value['product_id_key'];
						
					}
					//Add Extra Json Meta in To Product
					foreach($_POST['product_reason'] as $key_id1 => $key_value1){
						if(!empty($key_value1['subject'])){
							//$products[ $date ]['from'][$key_id]['product_id_key'] = $key_value['product_id_key'];
							$products[ $date ]['to'][$key_id1]['product_id_key'] = $key_value1['product_id_key'];
							$products[ $date ]['to'][$key_id1]['subject'] = $key_value1['subject'];
							$products[ $date ]['to'][$key_id1]['reason'] = $key_value1['reason'];
							
						}
						
					}
				if ( $pending ) {
					$date = date( 'd-m-Y' );
					$products = array();
					$products[ $date ]['orderid'] = $_POST['orderid'];
					$products[ $date ]['subject'] = $_POST['subject'];
					$products[ $date ]['reason'] = $_POST['reason'];
					$products[ $date ]['status'] = 'pending';
					
				}
				$filename = array();
				$order_id = $_POST['orderid'];
				foreach($_POST['product_reason'] as $key_id => $key_value){
					$product_id = $key_value['product_id'];
					$count = sizeof($_FILES['files']['tmp_name'][$product_id][$key_id]);

					for ( $i = 0;$i < $count;$i++ ) {
						if ( isset( $_FILES['files']['tmp_name'][$product_id][$key_id][ $i ] ) ) {

							$directory = ABSPATH . 'wp-content/uploads/Exchange_Images';
							if ( ! file_exists( $directory ) ) {
								mkdir( $directory, 0755, true );
							}
							$sourcePath = $_FILES['files']['tmp_name'][$product_id][$key_id][ $i ];						
							$targetPath = $directory . '/' . $product_id . '-' . $_FILES['files']['name'][$product_id][$key_id][ $i ];
							$file_type  = $_FILES['files']['type'][$product_id][$key_id][ $i ];
							if ( 'image/png' == $file_type || 'image/jpeg' == $file_type || 'image/jpg' == $file_type ) {
								$filename[] = $product_id . '-' . $_FILES['files']['name'][$product_id][$key_id][ $i ];
								$fileNameExchange = $product_id . '-' . $_FILES['files']['name'][$product_id][$key_id][ $i ];
								move_uploaded_file( $sourcePath, $targetPath );


							}
						}
						$products[ $date ]['to'][$key_id]['files'][$i] = $fileNameExchange;
					}
				}
				update_post_meta( $order_id, 'ced_rnx_exchange_product', $products );
				$exchange_subject = $subject;
				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
				$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );
                $order = wc_get_order( $order_id );
				$message = '<!DOCTYPE html>
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
                    padding-left: 30px;
                    padding-right: 30px;
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
            
                  @media (max-width:820px) {
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
                                                      <td class="column column-1" width="100%" style="mso-table-lspace:0;mso-table-rspace:0;font-weight:400;text-align:left;padding-bottom:5px;padding-top:5px;vertical-align:top;border-top:0;border-right:0;border-bottom:0;border-left:0">
                                                        <table class="image_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                                                          <tr>
                                                            <td class="pad" style="width:100%;padding-right:0;padding-left:0">
                                                              <div class="alignment" align="center" style="line-height:10px">
	                                                                <img alt="" width="100" src="https://shop.studds.com//wp-content/uploads/2023/01/STUDDS-White-250-X-250-LOGO.png" max-width="100%" style="border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; background-color: transparent; max-width: 100%; vertical-align: middle; width: 100px;" border="0" bgcolor="transparent">
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
                                                                    <span class="tinyMce-placeholder">Hi '. $order->get_billing_first_name().'</span>
                                                                </h1>
                                                            </td>
                                                          </tr>
                                                        </table>
                                                        <table class="paragraph_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;word-break:break-word">
                                                            <tr>
                                                              <td class="pad">
                                                                <div style="color:#000;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:14px;font-weight:400;letter-spacing:0;line-height:120%;text-align:left;mso-line-height-alt:16.8px">
                                                                  <p style="margin:0">We have received your product exchange request against order id #' . $order_id . '</p>
                                                                </div>
                                                              </td>
                                                            </tr>
                                                          </table>
                                                          <table class="heading_block block-3" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                                                            <tr>
                                                              <td class="pad" style="padding-bottom:15px;padding-top:35px;text-align:center;width:100%">
                                                                <h3 style="margin:0;color:#ed3237;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:18px;font-weight:700;letter-spacing:normal;line-height:120%;text-align:left;margin-top:0;margin-bottom:0">
                                                                  <span class="tinyMce-placeholder">' . __( 'Product', 'woocommerce-refund-and-exchange' ) . '</span>
                                                                </h3>
                                                              </td>
                                                            </tr>
                                                          </table>
                                                          <table class="html_block block-4" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                                                            <tr>
                                                              <td class="pad">
                                                                <div style="font-family:Arial,Helvetica Neue,Helvetica,sans-serif;text-align:center" align="center">
                                                                  <table style="width:100%; border: 1px solid #d2cfcf; border-collapse: collapse; font-family: Arial" cm-sr-title="Email Id: rishabh.gupta@studds.com" cm-sr-comment="true">
                                                                    <tbody>
                                                                      <tr>
                                                                        <th style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . __( 'Product', 'woocommerce-refund-and-exchange' ) . '</th>
                                                                        <th style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . __( 'Quantity', 'woocommerce-refund-and-exchange' ) . '</th>
                                                                        <th style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . __( 'Price', 'woocommerce-refund-and-exchange' ) . '</th>
                                                                      </tr>';
                                                                      $requested_products = $products[ $date ]['from'];

                                                                      if ( isset( $requested_products ) && ! empty( $requested_products ) ) {
                                                                          // echo "i am here-1";
                                                                          $total = 0;
                                                                          foreach ( $order->get_items() as $item_id => $item ) {
                                                                              $product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
                                                                              foreach ( $requested_products as $requested_product ) {
                                                                                  if ( isset( $requested_product['item_id'] ) ) {
                                                                                      if ( $item_id == $requested_product['item_id'] ) {
                                                                                          if ( isset( $requested_product['variation_id'] ) && $requested_product['variation_id'] > 0 ) {
                                                                                              $prod = wc_get_product( $requested_product['variation_id'] );
                                                      
                                                                                          } else {
                                                                                              $prod = wc_get_product( $requested_product['product_id'] );
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
                                                                                          $message .= '<tr>
                                                                                            <td style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . $item['name'] . '<br>';
                                                                                            $message .= '<small style="border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;">' . $item_meta_html . '</small>
                                                                                            <td style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . $requested_product['qty'] . '</td>
                                                                                            <td style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . ced_rnx_format_price( $requested_product['price'] * $requested_product['qty'] ) . '</td>
                                                                                        </tr>';
                        
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                $message .= '
                                    </tbody>
                                </table>
                            </div>
                            </td>
                        </tr>
                        <table class="heading_block block-3" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                            <tr>
                              <td class="pad" style="padding-bottom:15px;padding-top:35px;text-align:center;width:100%">
                                <h3 style="margin:0;color:#ed3237;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:18px;font-weight:700;letter-spacing:normal;line-height:120%;text-align:left;margin-top:0;margin-bottom:0">
                                  <span class="tinyMce-placeholder">' . __( 'Exchanged Product', 'woocommerce-refund-and-exchange' ) . '</span>
                                </h3>
                              </td>
                            </tr>
                        </table>
                        <table class="html_block block-4" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                            <tr>
                            <td class="pad">
                                <div style="font-family:Arial,Helvetica Neue,Helvetica,sans-serif;text-align:center" align="center">
                                    <table style="width:100%; border: 1px solid #d2cfcf; border-collapse: collapse; font-family: Arial" cm-sr-title="Email Id: rishabh.gupta@studds.com" cm-sr-comment="true">
                                        <tbody>
                                            <tr>
                                                <th style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . __( 'Product', 'woocommerce-refund-and-exchange' ) . '</th>
                                                <th style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . __( 'Quantity', 'woocommerce-refund-and-exchange' ) . '</th>
                                                <th style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . __( 'Price', 'woocommerce-refund-and-exchange' ) . '</th>
                                                <th style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">Reason</th>
                                            </tr>';
                                    $exchanged_to_products = $products[ $date ]['to'];
                                    $total_price = 0;
                                    if ( isset( $exchanged_to_products ) && ! empty( $exchanged_to_products ) ) {
                                        // echo "i am here-2";
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
                    
                                            $pro_price = $exchanged_product['qty'] * $exchanged_product['price'];
                                            $total_price += $pro_price;
                                            $product = new WC_Product( $exchanged_product['id'] );
                                            $title = '';
                                            if ( isset( $exchanged_product['p_id'] ) ) {
                                                $title .= $grouped_product_title . ' -> ';
                                            }
                                            $title .= $product->get_title();
                    
                                            if ( isset( $variation_attributes ) && ! empty( $variation_attributes ) ) {
                                                //$title .= wc_get_formatted_variation( $variation_attributes );
                                            }
                                            $message .= '<tr>
                                                <td style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . $title . '</td>
                                                <td style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . $exchanged_product['qty'] . '</td>
                                                <td style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . ced_rnx_format_price( $pro_price ) . '</td>
                                                <td style="border: 1px solid #d2cfcf; border-collapse: collapse; color:#222222; font-style:normal; font-weight: normal; background-color:#ffffff; text-decoration:normal; text-align:left; font-size:12px;padding-top:10px;padding-bottom:10px;padding-left:10px;">' . $exchanged_product['subject'] . '</td>
                                            </tr>';
                                        }
                                    }

						$message .= '
                    </tbody>
                </table>
              </div>
            </td>
          </tr>
        </table>';
		$message .= ' <table class="heading_block block-7" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
            <tr>
              <td class="pad" style="padding-bottom:15px;padding-top:20px;text-align:center;width:100%">
                <h3 style="margin:0;color:#ed3237;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;font-weight:700;letter-spacing:normal;line-height:120%;text-align:left;margin-top:0;margin-bottom:0">
                  <span class="tinyMce-placeholder">' . __( 'Customer details', 'woocommerce-refund-and-exchange' ) . '</span>
                </h3>
              </td>
            </tr>
          </table>	
          <table class="paragraph_block block-8" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;word-break:break-word">
            <tr>
              <td class="pad" style="padding-bottom:10px;padding-top:10px">
                <div style="color:#000;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:14px;font-weight:400;letter-spacing:0;line-height:120%;text-align:left;mso-line-height-alt:16.8px">
                  <p style="margin:0;margin-bottom:16px">' . __( 'Email', 'woocommerce-refund-and-exchange' ) . ': ' . get_post_meta( $order_id, '_billing_email', true ) . '</p>
                  <p style="margin:0">' . __( 'Mobile', 'woocommerce-refund-and-exchange' ) . ':  ' . get_post_meta( $order_id, '_billing_phone', true ) . '</p>
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
                <td class="column column-1" width="50%" style="mso-table-lspace:0;mso-table-rspace:0;font-weight:400;text-align:left;border-left:1px solid #3c434a;padding-bottom:5px;padding-left:30px;padding-right:15px;padding-top:5px;vertical-align:top;border-top:0;border-right:0;border-bottom:0">
                  <table class="heading_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                    <tr>
                      <td class="pad" style="padding-bottom:15px;padding-top:20px;text-align:center;width:100%">
                        <h3 style="margin:0;color:#ed3237;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;font-weight:700;letter-spacing:normal;line-height:120%;text-align:left;margin-top:0;margin-bottom:0">
                          <span class="tinyMce-placeholder">' . __( 'Shipping Address', 'woocommerce-refund-and-exchange' ) . '</span>
                        </h3>
                      </td>
                    </tr>
                  </table>
                  <table class="paragraph_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;word-break:break-word">
                    <tr>
                      <td class="pad" style="padding-bottom:10px;padding-right:15px;padding-top:10px">
                        <div style="color:#000;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:14px;font-weight:400;letter-spacing:0;line-height:120%;text-align:left;mso-line-height-alt:16.8px">
                          <p style="margin:0">' . $order->get_formatted_shipping_address() . '</p>
                        </div>
                      </td>
                    </tr>
                  </table>
                </td>
                <td class="column column-2" width="50%" style="mso-table-lspace:0;mso-table-rspace:0;font-weight:400;text-align:left;border-right:1px solid #3c434a;padding-bottom:5px;padding-top:5px;vertical-align:top;border-top:0;border-bottom:0;border-left:0">
                    <table class="heading_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0">
                      <tr>
                        <td class="pad" style="padding-bottom:15px;padding-top:20px;text-align:center;width:100%">
                          <h3 style="margin:0;color:#ed3237;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;font-weight:700;letter-spacing:normal;line-height:120%;text-align:left;margin-top:0;margin-bottom:0">
                            <span class="tinyMce-placeholder">' . __( 'Billing Address', 'woocommerce-refund-and-exchange' ) . '</span>
                          </h3>
                        </td>
                      </tr>
                    </table>
                    <table class="paragraph_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;word-break:break-word">
                        <tr>
                          <td class="pad" style="padding-bottom:10px;padding-top:10px">
                            <div style="color:#000;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:14px;font-weight:400;letter-spacing:0;line-height:120%;text-align:left;mso-line-height-alt:16.8px">
                              <p style="margin:0">' . $order->get_formatted_billing_address() . '</p>
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
                                            <img src="https://shop.studds.com//wp-content/plugins/email-template-customizer-for-woo/assets/img/fb-blue-white.png" width="32" style="border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; vertical-align: middle; background-color: transparent; max-width: 100%;" border="0" bgcolor="transparent">
                                        </a>
                                      </td>
                                      <td style="vertical-align:middle;text-align:center;padding-top:5px;padding-bottom:5px;padding-left:5px;padding-right:5px">
                                        <a href="https://twitter.com/StuddsHelmet" style="text-decoration: none; word-break: break-word;">
                                            <img src="https://shop.studds.com//wp-content/plugins/email-template-customizer-for-woo/assets/img/twi-cyan-white.png" width="32" style="border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; vertical-align: middle; background-color: transparent; max-width: 100%;" border="0" bgcolor="transparent">
                                        </a>
                                      </td>
                                      <td style="vertical-align:middle;text-align:center;padding-top:5px;padding-bottom:5px;padding-left:5px;padding-right:5px">
                                        <a href="https://www.instagram.com/studdshelmets/" style="text-decoration: none; word-break: break-word;">
                                            <img src="https://shop.studds.com//wp-content/plugins/email-template-customizer-for-woo/assets/img/ins-white-color.png" width="32" style="border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; vertical-align: middle; background-color: transparent; max-width: 100%;" border="0" bgcolor="transparent">
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
                                    <p style="margin:0" style="color:white;"><a href="https://shop.studds.com/contact-us/" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Need Help?</a> | customercare@studds.com | 0129-4296500</p>
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
                                    <p style="margin:0"><a href="https://shop.studds.com/our-policies/#warranty-policy" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Warrenty Policy</a> | <a href="https://shop.studds.com/our-policies/#exchange-policy" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Exchange Policy</a> | <a href="https://shop.studds.com/our-policies/#order-cancellation-policy" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Cancellation Policy</a></p>
                                  </div>
                                </td>
                              </tr>
                            </table>
                            <table class="paragraph_block block-2" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace:0;mso-table-rspace:0;word-break:break-word">
                              <tr>
                                <td class="pad">
                                  <div style="color:#fff;direction:ltr;font-family:Arial,Helvetica Neue,Helvetica,sans-serif;font-size:14px;font-weight:400;letter-spacing:0;line-height:120%;text-align:center;mso-line-height-alt:16.8px">
                                    <p style="margin:0"><a href="https://shop.studds.com/contact-us/" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Shopping, Shipping & Delivery Policy</a> &nbsp;| &nbsp;<a href="https://shop.studds.com/terms-of-use/" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Terms Of Use</a> | &nbsp;<a href="https://shop.studds.com/privacy-policy/" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; font-family: Roboto;color: #ffffff;"" >Privacy Policy</a></p>
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
</body>
</html>';

				// Send mail to merchant

				$headers = array();

				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$to = get_post_meta( $order_id, '_billing_email', true );
				//$to = get_option( 'ced_rnx_notification_from_mail' );
				$subject = get_option( 'ced_rnx_notification_merchant_exchange_subject' );
				$subject = str_replace( '[order]', '#' . $order_id, $subject );

				wc_mail( $to, $subject, $message, $headers );

				// Send mail to User that we recieved your request

				$fname = get_option( 'ced_rnx_notification_from_name' );
				$fmail = get_option( 'ced_rnx_notification_from_mail' );
				$to = get_post_meta( $order_id, '_billing_email', true );
				//Empty colon
				$headers[] = "From: $fname <$fmail>";
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$subject = get_option( 'ced_notification_exchange_subject' );
				$message = stripslashes( get_option( 'ced_notification_exchange_rcv' ) );
				$message = apply_filters( 'mwb_rnx_meta_content', $message );

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

				$fullname = $fname . ' ' . $lname;

				$message = str_replace( '[username]', $fullname, $message );
				$message = str_replace( '[order]', 'Order #' . $order_id, $message );
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
				$subject = str_replace( '[order]', 'Order #' . $order_id, $subject );
				$subject = str_replace( '[siteurl]', home_url(), $subject );

				$mail_header = str_replace( '[username]', $fullname, $mail_header );
				$mail_header = str_replace( '[order]', 'Order #' . $order_id, $mail_header );
				$mail_header = str_replace( '[siteurl]', home_url(), $mail_header );

				$template = get_option( 'ced_notification_exchange_template', 'no' );

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

				$ced_rnx_restrict_mails = get_option( 'ced_rnx_exchange_restrict_customer_mails', true );
				//if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
					wc_mail( $to, $subject, $html_content, $headers );
				//}
				// echo "i am here-3";
				//die;
				update_post_meta( $order_id, 'ced_rnx_request_made', true );

				$order = new WC_Order( $order_id );
				$order->update_status( 'wc-exchange-request', __( 'User Request to Exchange Product', 'woocommerce-refund-and-exchange' ) );
				WC()->session->__unset( 'exchange_requset' );
				WC()->session->__unset( 'ced_rnx_exchange' );
				$response['success'] = true;
				$response['msg'] = __( 'Message send successfully. You have received a notification mail regarding this, Please check your mail. Soon You redirect to the My Account Page. Thanks', 'woocommerce-refund-and-exchange' );
				echo json_encode( $response );
			}
			else {
		        WC()->session->__unset( 'exchange_requset' );
				WC()->session->__unset( 'ced_rnx_exchange' );
                $response['success'] = false;
                $response['data']['msg'] = __('Something went wrong. Please try again later.', 'woocommerce-refund-and-exchange');
                echo json_encode($response);
            }
            wp_die();
			}
		}

		/**
		 * This function is to add button on order detail page
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function ced_rnx_exchnaged_product_add_button() {
			global $product;
			$sku = $product->get_sku();
			if ( WC()->version < '3.0.0' ) {
				$product_id = $product->id;
				$product_type = $product->product_type;
				$price = $product->get_display_price();
			} else {
				$product_id = $product->get_id();
				$product_type = $product->get_type();
				$price = wc_get_price_including_tax( $product );
			}
			$get_product_price = WC()->session->get( 'ced_rnx_exchange_price');
			$exchange_product_id = WC()->session->get( 'ced_rnx_exchange_product_id');

			$ced_rnx_exchange_variation_enable = get_option( 'ced_rnx_exchange_variation_enable', false );
			$exchange_product_id = WC()->session->get( 'rnx_exchange_product' );

			$categories = get_the_terms( $product_id, 'product_cat' );
			$exchange_categories = get_the_terms( $exchange_product_id, 'product_cat' );

			// if(($price == $get_product_price && $exchange_categories[0]->name == 'Helmets' && $categories[0]->name == $exchange_categories[0]->name) || ($price == $get_product_price && $exchange_categories[0]->name == 'Accessories' && $categories[1]->name == $exchange_categories[1]->name)){
		// print_r(WC()->session->get( 'ced_rnx_exchange' ));
			if($price == $get_product_price){
				if ( $ced_rnx_exchange_variation_enable == 'yes' ) {
					// echo "here-1";
					if ( $product_type == 'variable' && $product->is_in_stock() ) {
						if ( null != WC()->session->get( 'ced_rnx_exchange_variable_product' ) ) {
							foreach ( WC()->session->get( 'ced_rnx_exchange_variable_product' ) as $key => $value ) {
								if ( $value == get_the_ID() ) {
									?>
								<div class="ced_rnx_exchange_wrapper">
									<button  data-product_id="<?php echo $product_id; ?>" class="ced_rnx_add_to_exchanged_detail_variable button alt">
										<?php echo apply_filters( 'ced_rnx_exchange_product_button', __( 'ADD TO EXCHANGE', 'woocommerce-refund-and-exchange' ) ); ?>
									</button>
								</div>
									<?php
								}
							}
						}
					}
					if ( $product_type == 'simple' && $product->is_in_stock() ) {
						if ( null != WC()->session->get( 'ced_rnx_exchange_variable_product' ) ) {
							foreach ( WC()->session->get( 'ced_rnx_exchange_variable_product' ) as $key => $value ) {
								if ( $value == get_the_ID() ) {
									?>
								<div class="ced_rnx_exchange_wrapper">
									<button data-price="<?php echo $price; ?>" data-product_sku="<?php echo $sku; ?>" data-product_id="<?php echo $product_id; ?>" class="ced_rnx_add_to_exchanged_detail button alt"><?php echo apply_filters( 'ced_rnx_exchange_product_button', __( 'ADD TO EXCHANGE', 'woocommerce-refund-and-exchange' ) ); ?>
										
									</button>
								</div>
									<?php
								}
							}
						}
					}
				} else if ( null != WC()->session->get( 'ced_rnx_exchange' ) ) {
					// echo "here-2";
					if ( $product_type == 'simple' && $product->is_in_stock() ) {
						if ( null != WC()->session->get( 'ced_rnx_exchange' ) ) {
							?>
							<div class="ced_rnx_exchange_wrapper">
								<button data-price="<?php echo $price; ?>" data-product_sku="<?php echo $sku; ?>" data-product_id="<?php echo $product_id; ?>" class="ced_rnx_add_to_exchanged_detail button alt"><?php echo apply_filters( 'ced_rnx_exchange_product_button', __( 'ADD TO EXCHANGE', 'woocommerce-refund-and-exchange' ) ); ?>
									
								</button>
							</div>
							<?php
						}
					}
					if ( $product_type == 'variable' && $product->is_in_stock() ) {

						if ( null != WC()->session->get( 'ced_rnx_exchange' ) ) {
							?>
							<div class="ced_rnx_exchange_wrapper">
								<button  data-product_id="<?php echo $product_id; ?>" class="ced_rnx_add_to_exchanged_detail_variable button alt">
									<?php echo apply_filters( 'ced_rnx_exchange_product_button', __( 'ADD TO EXCHANGE', 'woocommerce-refund-and-exchange' ) ); ?>
								</button>
							</div>
							<?php
						}
					}
					if ( $product_type == 'grouped' && $product->is_in_stock() ) {
						if ( null != WC()->session->get( 'ced_rnx_exchange' ) ) {
							?>
							<div class="ced_rnx_exchange_wrapper">
								<button data-price="<?php echo $price; ?>" data-product_sku="<?php echo $sku; ?>" data-product_id="<?php echo $product_id; ?>" class="ced_rnx_add_to_exchanged_detail button alt"><?php echo apply_filters( 'ced_rnx_exchange_product_button', __( 'ADD TO EXCHANGE', 'woocommerce-refund-and-exchange' ) ); ?>
									
								</button>
							</div>
							<?php
						}
					}
				}
			}else{ ?>
				<!-- <div class="ced_rnx_exchange_wrapper"><span style="margin-bottom: 10px;font-weight: 900 !important;color: red !important;" class="product_price_label">Please select other product</span></div> -->
			<?php

			}
		}

		/**
		 * This function is to remove exchange product
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function ced_rnx_exchnaged_product_remove_callback() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$order_id = $_POST['orderid'];
				$key = $_POST['id'];

				$exchange_details = array();
				if ( null != WC()->session->get( 'exchange_requset' ) ) {
					$exchange_details = WC()->session->get( 'exchange_requset' );
				}
				if ( isset( $exchange_details ) && ! empty( $exchange_details ) ) {
					foreach ( $exchange_details as $date => $exchange_detail ) {
						if ( $exchange_detail['status'] == 'pending' ) {
							$exchange_products = $exchange_detail['to'];
							unset( $exchange_products[ $key ] );
							$exchange_details[ $date ]['to'] = $exchange_products;
							break;
						}
					}
				}

				WC()->session->set( 'exchange_requset', $exchange_details );
				$exchange_details = array();
				if ( null != WC()->session->get( 'exchange_requset' ) ) {
					$exchange_details = WC()->session->get( 'exchange_requset' );
				}

				$total_price = 0;

				if ( isset( $exchange_products ) && ! empty( $exchange_products ) ) {
					foreach ( $exchange_products as $key => $exchanged_product ) {
						$pro_price = $exchanged_product['qty'] * $exchanged_product['price'];
						$total_price += $pro_price;
					}
				}
				$response['response'] = 'success';
				$response['total_price'] = $total_price;
				echo json_encode( $response );
				wp_die();
			}
		}

		/**
		 * This function is to add exchange product
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function ced_rnx_add_to_exchange_callback() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$products = array();
				$order_id = WC()->session->get( 'ced_rnx_exchange' );
				$exchange_product = $_POST['products'];
				$product_id = $exchange_product['id'];

				$exchange_product_id = WC()->session->get( 'rnx_exchange_product' );
				$exchange_ordering = WC()->session->get( 'ced_rnx_exchange_ordering' );

				WC()->session->__unset( 'exchange_session_start' );
				WC()->session->__unset( 'expire_time' );
				// Start for variation
				$adding_to_cart = wc_get_product( $product_id );

				if ( isset( $exchange_product['variation_id'] ) ) {
					$product_variation = new WC_Product_Variation( $exchange_product['variation_id'] );
					if ( WC()->version < '3.0.0' ) {
						$exchange_product['price'] = $product_variation->get_display_price();
					} else {
						$exchange_product['price'] = wc_get_price_including_tax( $product_variation );
						;
					}
					$exchange_product['product_id'] = $exchange_product_id;
				}else{
					$exchange_product['product_id'] = $exchange_product_id;
				}
				$exchange_details = array();
				if ( null != WC()->session->get( 'exchange_requset' ) ) {
					$exchange_details = WC()->session->get( 'exchange_requset' );
				}
				$pending = true;
				if ( isset( $exchange_details ) && ! empty( $exchange_details ) ) {
					foreach ( $exchange_details as $date => $exchange_detail ) {
						if ( $exchange_detail['status'] == 'pending' ) {
							$pending_key = $date;
							if ( isset( $exchange_detail['to'] ) ) {
								$exchange_products = $exchange_detail['to'];
							} else {
								$exchange_products = array();
							}
							$pending = false;
							break;
						}
					}
				}

				if ( $pending ) {
					$exchange_products = array();
				}

				if ( isset( $exchange_product['grouped'] ) ) {
					$exchange_pro = array();
					foreach ( $exchange_product['grouped'] as $k => $val ) {
						$g_child = array();
						echo "here-0";
						$child_product = new WC_Product( $k );
						$g_child['id'] = $k;
						$g_child['qty'] = $val;
						$g_child['sku'] = $child_product->get_sku();
						if ( WC()->version < '3.0.0' ) {
							$g_child['price'] = $child_product->get_display_price();
						} else {
							$g_child['price'] = $child_product->get_price();
						}
						$g_child['product_id'] = $exchange_product_id;
						$exchange_pro[] = $g_child;
					}
					$exchange_product = $exchange_pro;
				}
				// if ( isset( $exchange_products ) && ! empty( $exchange_products ) ) {
				// 	foreach ( $exchange_products as $key => $product ) {
				// 		$exist = true;
				// 		if ( ! isset( $exchange_product['id'] ) ) {
				// 			if ( is_array( $exchange_product ) ) {
				// 				foreach ( $exchange_product as $a => $exchange_pro ) {
				// 					if ( $product['id'] == $exchange_pro['id'] && $product['sku'] == $exchange_pro['sku'] ) {
				// 						echo "here-1";
				// 						$count++;
				// 						$exist = false;
				// 						$exchange_products[ $exchange_ordering ]['qty'] += $exchange_pro['qty'];
				// 						$exchange_products[ $exchange_ordering ]['product_id'] = $exchange_product_id;
				// 						unset( $exchange_product[ $a ] );
				// 					}
				// 				}
				// 			}
				// 			$exchange_products[ $exchange_ordering ]['product_id'] = $exchange_product_id;

				// 		} elseif ( $product['id'] == $exchange_product['id'] ) {
				// 			if ( isset( $exchange_product['variation_id'] ) ) {
				// 				/*if ( $product['variation_id'] == $exchange_product['variation_id'] ) {
				// 					$var_matched = true;
				// 					if ( isset( $exchange_product['variations'] ) && ! empty( $exchange_product['variations'] ) ) {
				// 						$saved_product_variations = $exchange_product['variations'];
				// 						foreach ( $saved_product_variations as $saved_product_key => $saved_product_variation ) {
				// 							if ( array_key_exists( $saved_product_key, $product['variations'] ) ) {
				// 								if ( $product['variations'][ $saved_product_key ] != $saved_product_variation ) {
				// 									$var_matched = false;
				// 								}
				// 							}
				// 						}
				// 					}
				// 					$exchange_products[ $exchange_ordering ]['product_id'] = $exchange_product_id;
				// 					if ( $var_matched ) {
				// 						$exist = false;
				// 						$exchange_products[ $exchange_ordering ]['qty'] += $exchange_product['qty'];
				// 						break;
				// 					}
				// 				}*/
				// 			} else {
				// 				echo "here-3";
				// 				$exchange_products[ $exchange_ordering ]['product_id'] = $exchange_product_id;
				// 				$exist = false;
				// 				$exchange_products[ $exchange_ordering ]['qty'] += $exchange_product['qty'];
				// 				break;
				// 			}
							
							
				// 		}
				// 	}

				// 	if ( isset( $exchange_product ) ) {
				// 		if ( ! isset( $exchange_product['id'] ) ) {
				// 			if ( is_array( $exchange_product ) ) {
				// 				foreach ( $exchange_product as $a => $exchange_pro ) {
				// 					$exchange_products[] = $exchange_pro;
				// 					unset( $exchange_product[ $a ] );
				// 				}
				// 			}
				// 		}
				// 	}
				// 	if ( $exist ) {
				// 		if ( ! empty( $exchange_product ) ) {
				// 			$exchange_products[$exchange_ordering] = $exchange_product;
				// 		}
				// 	}
				// } else {

				// 	if ( isset( $exchange_product ) ) {
				// 		if ( ! isset( $exchange_product['id'] ) ) {
				// 			if ( is_array( $exchange_product ) ) {
				// 				foreach ( $exchange_product as $a => $exchange_pro ) {
				// 					// echo "i ad here";
				// 					$exchange_products[$exchange_ordering] = $exchange_pro;
				// 				}
				// 			}
				// 		} elseif ( ! empty( $exchange_product ) ) {

				// 			$exchange_products[$exchange_ordering] = $exchange_product;
				// 		}
				// 		// $exchange_products[]['product_id'] = $exchange_product_id;
				// 	}
				// }
				$exchange_products[$exchange_ordering] = $exchange_product;
				if ( $pending ) {
					$exchange_details = array();
					$date = date( 'd-m-Y' );
					$exchange_details[ $date ]['to'] = $exchange_products;
				} else {
					$exchange_details[ $pending_key ]['to'] = $exchange_products;
				}
				// echo "<pre>";
				// print_r($exchange_details);
				// die;
				WC()->session->set( 'exchange_requset', $exchange_details );
				$response['response'] = 'success';
				$response['message'] = apply_filters( 'ced_rnx_product_exchanged_message', __( 'View Order', 'woocommerce-refund-and-exchange' ) );
				$ced_rnx_pages = get_option( 'ced_rnx_pages', false );
				$page_id = $ced_rnx_pages['pages']['ced_exchange_from'];
				$exchange_url = get_permalink( $page_id );
				$response['url'] = esc_html( add_query_arg( 'order_id', $order_id, $exchange_url ) );
				echo json_encode( $response );
				wp_die();
			}

		}
		/**
		 * This function is add exchange button on product detail page
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */

		function ced_rnx_add_exchange_products() {
			global $product;
			if ( is_admin() ) {
				return;
			}
			if ( WC()->version < '3.0.0' ) {
				$product_type = $product->product_type;
				$product_id = $product->id;
				$price = $product->get_display_price();
			} else {
				$product_id = $product->get_id();
				$product_type = $product->get_type();
				$price = wc_get_price_including_tax( $product );
			}
			$ced_rnx_exchange_variation_enable = get_option( 'ced_rnx_exchange_variation_enable', false );

			$ced_rnx_add_to_cart_enable = get_option( 'ced_rnx_add_to_cart_enable', 'no' );
			if ( $ced_rnx_exchange_variation_enable == 'yes' && null != WC()->session->get( 'ced_rnx_exchange_variable_product' ) ) {
				if ( $ced_rnx_add_to_cart_enable != 'yes' ) {
					remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
				}
			} else if ( null != WC()->session->get( 'ced_rnx_exchange' ) ) {
				if ( $ced_rnx_add_to_cart_enable != 'yes' ) {
					remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
				}
				if ( $product_type == 'simple' && $product->is_in_stock() ) {
					?>
					<!-- <div class="ced_rnx_exchange_wrapper"><a class="button ced_rnx_ajax_add_to_exchange" data-product_sku="<?php echo $product->get_sku(); ?>" data-product_id="<?php echo $product_id; ?>" data-quantity="1" data-price="<?php echo $price; ?>"><?php echo apply_filters( 'ced_rnx_exchange_product_button', __( 'Exchange', 'woocommerce-refund-and-exchange' ) ); ?></a></div> -->
					<?php
				}
			}
		}

		/**
		 * This function is used to set session for exchange request
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		public function ced_rnx_set_exchange_session() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				if ( isset( $_POST['products'] ) ) {
					$orderid = $_POST['orderid'];
					$ordering = $_POST['ordering'];
					$products_price = $_POST['price'];
					$product_id_new = $_POST['product_ids'];

					WC()->session->set( 'ced_rnx_exchange', $orderid );
					WC()->session->set( 'ced_rnx_exchange_ordering', $ordering );

					WC()->session->set( 'ced_rnx_exchange_price', $products_price );
					WC()->session->set( 'ced_rnx_exchange_product_id', $product_id_new );

					WC()->session->set( 'exchange_session_start', '1');
					WC()->session->set( 'expire_time', time());
					$ced_rnx_exchange_variation_enable = get_option( 'ced_rnx_exchange_variation_enable', false );
					
					WC()->session->set( 'rnx_exchange_product', $_POST['products']['0']['product_id'] );
					if ( $ced_rnx_exchange_variation_enable == 'yes' ) {
						
						$product_ids = array();
						foreach ( $_POST['products'] as $key => $value ) {
							$product_ids[] = $value['product_id'];
						}
						WC()->session->set( 'ced_rnx_exchange_variable_product', $product_ids );
					}

					return 'true';
					wp_die();
				}
			}
		}
		/**
		 * This function is used to save selected exchange products
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		public function ced_rnx_exchange_products_callback() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				if ( isset( $_POST['orderid'] ) ) {
					$orderid = $_POST['orderid'];
					$mwb_rnx_obj = wc_get_order( $orderid );
					$mwb_cpn_dis = $mwb_rnx_obj->get_discount_total();
					$mwb_cpn_tax = $mwb_rnx_obj->get_discount_tax();
					$mwb_dis_tot = $mwb_cpn_dis + $mwb_cpn_tax;
					$mwb_dis_tot = 0;
					if ( isset( $_POST['products'] ) ) {
						if ( strpos( $_SERVER['REQUEST_URI'], '/exchange-request-form/' ) >= 0 ) {
							WC()->session->__unset( 'ced_rnx_exchange' );
						}
						$products  = array();
						if ( null != WC()->session->get( 'exchange_requset' ) ) {
							$products = WC()->session->get( 'exchange_requset' );
						}

						$pending = true;
						if ( isset( $products ) && ! empty( $products ) ) {
							foreach ( $products as $date => $product ) {
								if ( $product['status'] == 'pending' ) {
									$products[ $date ]['status'] = 'pending'; // update requested products
									$products[ $date ]['from'] = $_POST['products'];
									$pending = false;
									break;
								}
							}
						}
						if ( $pending ) {
							$date = date( 'd-m-Y' );
							$products = array();
							$products[ $date ]['status'] = 'pending';
							$products[ $date ]['from'] = $_POST['products'];
						}

						WC()->session->set( 'exchange_requset', $products );

						$response['response'] = 'success';
						$response['mwb_coupon_amt'] = 0;
						echo json_encode( $response );
						wp_die();
					} else {
						$response['mwb_coupon_amt'] = 0;
						echo json_encode( $response );
						wp_die();
					}
				}
			}
		}
		/**
		 * This function is to add exchange button and Show exchange products
		 *
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function ced_rnx_order_exchange_button( $order ) {
			$ced_rnx_show_exchange_button = true;
			$ced_rnx_next_exchange = true;
			$items = $order->get_items();
			$ced_rnx_catalog = get_option( 'catalog', array() );
			$exchange_button_hide = get_option( 'ced_rnx_exchange_button_hide_enable', false );
			if ( ! is_user_logged_in() && null == WC()->session->get( 'ced_rnx_email' ) ) {
				$exchange_button_hide = 'yes';
			}
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
			$ced_rnx_enable = get_option( 'ced_rnx_return_exchange_enable', false );

			if ( WC()->version < '3.0.0' ) {
				$order_id = $order->id;
			} else {
				$order_id = $order->get_id();
			}
			if ( $ced_rnx_enable == 'yes' ) {
				$ced_rnx_made = get_post_meta( $order_id, 'ced_rnx_request_made', true );
				if ( isset( $ced_rnx_made ) && ! empty( $ced_rnx_made ) ) {
					$ced_rnx_next_exchange = false;
				}
			}

			$ced_rnx_exchange = get_option( 'ced_rnx_exchange_enable', false );
			if ( $ced_rnx_exchange == 'yes' ) {
				$statuses = get_option( 'ced_rnx_exchange_order_status', array() );
				$order_status = 'wc-' . $order->get_status();
				// print_r($statuses);
				$exchanged_details = get_post_meta( $order_id, 'ced_rnx_exchange_product', true );
				if ( isset( $exchanged_details ) && ! empty( $exchanged_details ) ) {
					foreach ( $exchanged_details as $key => $exchanged_detail ) {
						if ( isset( $exchanged_detail['from'] ) && isset( $exchanged_detail['to'] ) && isset( $exchanged_detail['orderid'] ) ) {
							$selected_total_price = 0;
							$date = date_create( $key );
							$date_format = get_option( 'date_format' );
							$date = date_format( $date, $date_format );
							?>
							<h3 style="font-size: 18px;margin-top: 35px;margin-bottom: 0px;"><?php _e( 'Exchange Requested Product', 'woocommerce-refund-and-exchange' ); ?></h3>
							<p style="margin-bottom: 12px;"><?php _e( 'Following product exchange request is made on', 'woocommerce-refund-and-exchange' ); ?> <b><?php echo $date; ?>.</b></p>
							<?php
							$exchanged_products = $exchanged_detail['from'];
							$exchanged_to_products = $exchanged_detail['to'];

							if ( isset( $exchanged_detail['fee'] ) ) {
								$exchanged_fees = $exchanged_detail['fee'];
							} else {
								$exchanged_fees = array();
							}
							?>
							
							<table class="shop_table order_details excahngeorder">
								<thead>
									<tr>
										<th></th>
										<th class="product-name"><?php _e( 'Product', 'woocommerce-refund-and-exchange' ); ?></th>
										<th></th>
										<th class="product-name"><?php _e( 'Requested Product', 'woocommerce-refund-and-exchange' ); ?></th>
										<!--<th class="product-total"><?php //_e( 'Total', 'woocommerce-refund-and-exchange' ); ?></th>-->
									</tr>
								</thead>
								<tbody>
								<?php
								$start_row = 0;
								foreach ( $order->get_items() as $item_id => $item ) {
										if ( isset( $item['variation_id'] ) && $item['variation_id'] > 0 ) {
											$variation_id = $item['variation_id'];
											$product_id = $item['product_id'];
										} else {
											$product_id = $item['product_id'];
										}
									foreach ( $exchanged_products as $key => $exchanged_product ) {
										if ( $exchanged_product['item_id'] == $item_id ) {
											$pro_price = $exchanged_product['qty'] * $exchanged_product['price'];
											$selected_total_price += $pro_price;

											$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
											$thumbnail = wp_get_attachment_image( $product->get_image_id(), 'thumbnail' );
											?>

											<tr class="woocommerce-table__line-item order_item">
                                            
											<td class="thumbnail_views mobileexchange-view"> 
										    <p class="mobile-exchange"><u>Product:</u></p>
											<?php 
												if ( isset( $thumbnail ) && ! empty( $thumbnail ) ) {
													echo '<div class="ced_rnx_prod_img_view">' . wp_kses_post( $thumbnail ) . '</div>';
												} else {
												?>
												<div class="ced_rnx_prod_img_view"><img alt="Placeholder" width="150" height="150" class="attachment-thumbnail size-thumbnail wp-post-image" src="<?php echo home_url(); ?>/wp-content/plugins/woocommerce/assets/images/placeholder.png"></div>
												<?php } ?>
											</td>
											<td class="product-name mobileexchange-view">
												<?php
													
													$is_visible        = $product && $product->is_visible();
													$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

													echo $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $item['name'] ) : $item['name'];
													echo '<strong class="product-quantity">' . sprintf( '&times; %s', $exchanged_product['qty'] ) . '</strong>';

													do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );
												if ( WC()->version < '3.0.0' ) {
													$order->display_item_meta( $item );
													$order->display_item_downloads( $item );
												} else {
													wc_display_item_meta( $item );
													wc_display_item_downloads( $item );
												}
													do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );
												
												?>
											</td>
											<?php
											foreach ($exchanged_to_products as $key_exchange => $value_exchange){
												if($value_exchange['product_id'] == $product_id && $value_exchange['product_id_key'] == $exchanged_product['product_id_key']){
													$exchanged_product = $value_exchange;
												}
													// $total_price_exchange_tb = $total_price_exchange_tb + ($value_exchange['price'] - $mwb_actual_price);
												// }else if($key_exchange == $start_row){
												// 	$exchanged_product = $value_exchange;
												// 	// $total_price_exchange_tb = $total_price_exchange_tb + ($value_exchange['price'] - $mwb_actual_price);
												// }
											}
											// echo "<pre>";
											// print_r($exchange_to_product);
											if(!empty($exchanged_product)){
												$variation_attributes_1 = array();
												if ( isset( $exchanged_product['variation_id'] ) ) {
													if ( $exchanged_product['variation_id'] ) {
														$variation_product_1 = new WC_Product_Variation( $exchanged_product['variation_id'] );
														$variation_attributes_1 = isset( $exchanged_product['variations'] ) ? $exchanged_product['variations'] : $variation_product_1->get_variation_attributes();
														$variation_labels = array();
														foreach ( $variation_attributes_1 as $label => $value ) {
															if ( is_null( $value ) || $value == '' ) {
																$variation_labels[] = $label;
															}
														}
														if ( count( $variation_labels ) ) {
															$all_line_items = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
															$var_attr_info = array();
															foreach ( $all_line_items as $ear_item ) {
																$variationID = isset( $ear_item['item_meta']['_variation_id'] ) ? $ear_item['item_meta']['_variation_id'][0] : 0;

																if ( $variationID && $variationID == $exchanged_product['variation_id'] ) {
																	$itemMeta = isset( $ear_item['item_meta'] ) ? $ear_item['item_meta'] : array();

																	foreach ( $itemMeta as $metaKey => $metaInfo ) {
																		$metaName = 'attribute_' . sanitize_title( $metaKey );
																		if ( in_array( $metaName, $variation_labels ) ) {
																			$variation_attributes_1[ $metaName ] = isset( $term->name ) ? $term->name : $metaInfo[0];
																		}
																	}
																}
															}
														}
													}
													if ( isset( $exchanged_product['p_id'] ) ) {
														if ( $exchanged_product['p_id'] ) {
															$grouped_product = new WC_Product_Grouped( $exchanged_product['p_id'] );
															$grouped_product_title = $grouped_product->get_title();
														}
													}
													$pro_price = $exchanged_product['qty'] * $exchanged_product['price'];
													$total_price += $pro_price;
													$product_1 = new WC_Product( $exchanged_product['id'] );
													$thumbnail = wp_get_attachment_image( $variation_product_1->get_image_id(), 'thumbnail' );
												}else{
													$pro_price = $exchanged_product['qty'] * $exchanged_product['price'];
													$total_price += $pro_price;
													$product_1 = new WC_Product( $exchanged_product['id'] );
													$thumbnail = wp_get_attachment_image( $product_1->get_image_id(), 'thumbnail' );
												}
											}


										?>
									
											<td class="thumbnail_views mobileexchange-view">
											  
											    <p class="mobile-exchange"><u>Requested Product:</u></p>
												<?php 
												if ( isset( $thumbnail ) && ! empty( $thumbnail ) ) {
													echo '<div class="ced_rnx_prod_img_view">' . wp_kses_post( $thumbnail ) . '</div>';
												} else {
												?>
												<div class="ced_rnx_prod_img_view"><img alt="Placeholder" width="150" height="150" class="attachment-thumbnail size-thumbnail wp-post-image" src="<?php echo home_url(); ?>/wp-content/plugins/woocommerce/assets/images/placeholder.png"></div>
												<?php } ?>
												
											</td>
											<td class="product-name product-request-name mobileexchange-view" style="margin-left: 0px;padding-left: 0px!important;">
												
												<?php
													
													$is_visible        = $product_1 && $product_1->is_visible();
													$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product_1->get_permalink( $item ) : '', $item, $order );

													echo $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $product_1->get_title() ) : $product_1->get_title();
													echo '<strong class="product-quantity">' . sprintf( '&times; %s', $exchanged_product['qty'] ) . '</strong>';

													do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );
												// if ( WC()->version < '3.0.0' ) {
												// 	$order->display_item_meta( $item );
												// 	$order->display_item_downloads( $item );
												// } else {
												// 	wc_display_item_meta( $item );
												// 	wc_display_item_downloads( $item );
												// }
												// Correct Meta Information for Exchanged Product Meta.
												echo wc_get_formatted_variation( $variation_attributes_1 );
													do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );
												if(empty( $item['variation_id'])){
													echo "<br/>";
												
												}
												if ( $exchanged_detail['status'] == 'complete' ) {
												?>    
												<span style="color:#ff6161;line-height: 14px;"><strong>Exchange Request : </strong><?php 
													if($exchanged_product['approve_status_key'] == '1'){
														echo "Approved";
													}else if($exchanged_product['approve_status_key'] == '2'){
														echo "Rejected";
													}?>
												</span>   
												<?php }    
												    
												elseif(isset($exchanged_product['approve_status_key'])){
												?>
												<span style="color:#ff6161;line-height: 14px;"><strong>Exchange Request : </strong><?php 
													if($exchanged_product['approve_status_key'] == '1'){
														echo "Reverse pickup approved";
													}else if($exchanged_product['approve_status_key'] == '2'){
														echo "Reverse pickup rejected";
													}?>
												</span>
											<?php }?>
											</td>
										
											<!--<td class="product-total">-->
											<!--	<?php //echo ced_rnx_format_price( $pro_price ); ?>-->
											
											<!--</td>-->
											</tr>
												<?php
										}
									}
									$start_row++;
								}
								?>
										<!--<tr>-->
										<!--	<th colspan="4"><?php //_e( 'Total', 'woocommerce-refund-and-exchange' ); ?></th>-->
										<!--	<th><?php //echo ced_rnx_format_price( $selected_total_price ); ?></th>-->
										<!--</tr>-->
									</tbody>
								</table>
								<?php
									
									/*if ( isset( $exchanged_fees ) ) {
										if ( is_array( $exchanged_fees ) ) {
											?>
											<tr>
												<th colspan="2"><?php _e( 'Extra Cost', 'woocommerce-refund-and-exchange' ); ?></th>
											</tr>
											<?php
											foreach ( $exchanged_fees as $fee ) {
												?>
												<tr>
													<th><?php echo $fee['text']; ?></th>
													<td><?php echo ced_rnx_format_price( $fee['val'] ); ?></td>
												</tr>
												<?php
												$total_price += $fee['val'];
											}
										}
									}*/
									?>
							<!-- <table class="shop_table order_details"> 
								<?php
								if ( $total_price - $selected_total_price > 0 ) {
									?>
									<tr>
										<th class="product-name"><?php _e( 'Pay Amount', 'woocommerce-refund-and-exchange' ); ?></th>
										<th class="product-total"><?php echo ced_rnx_format_price( $total_price - $selected_total_price ); ?></th>
									</tr>
								<?php } else { ?>
									<tr>
										<th class="product-name"><?php _e( 'Refundable Amount', 'woocommerce-refund-and-exchange' ); ?></th>
										<th class="product-total"><?php echo ced_rnx_format_price( $selected_total_price - $total_price ); ?></th>
									</tr>
								<?php } ?>
							</table>-->
							<p>
							<?php
							if ( $exchanged_detail['status'] == 'complete' ) {
							 //   echo "<pre>";
							 //   print_r($exchanged_details);
								$approve_date = date_create( $exchanged_detail['approve'] );
								$date_format = get_option( 'date_format' );
								$approve_date = date_format( $approve_date, $date_format );

								_e( 'Above product exchange request is approved on', 'woocommerce-refund-and-exchange' );
								?>
								 <b><?php echo $approve_date; ?>.</b>
								<?php
							}
							if ( $exchanged_detail['status'] == 'cancel' ) {
								$approve_date = date_create( $exchanged_detail['cancel_date'] );
								$approve_date = date_format( $approve_date, 'F d, Y' );

								_e( 'Above product exchange request is cancelled on', 'woocommerce-refund-and-exchange' );
								?>
								 <b><?php echo $approve_date; ?>.</b>
								<?php
							}
							if ( $exchanged_detail['status'] == 'reject' ) {
								$rejected_date = date_create( $exchanged_detail['reject_date'] );
								$rejected_date = date_format( $rejected_date, 'F d, Y' );

								_e( 'Above product exchange request is Rejected on', 'woocommerce-refund-and-exchange' );
								?>
								 <b><?php echo $rejected_date; ?>.</b>
								 <?php
							}
							?>
						</p>
							<?php
						}
					}
				}
				if ( in_array( $order_status, $statuses ) && 'yes' != $exchange_button_hide ) {
					if ( WC()->version < '3.0.0' ) {
						$order_date = date_i18n( 'F j, Y', strtotime( $order->order_date ) );
					} else {
						$order = new WC_Order( $order );
						$order_date = date_i18n( 'F j, Y', strtotime( $order->get_date_created() ) );
					}
					$today_date = date_i18n( 'F j, Y' );
					$order_date = strtotime( $order_date );
					$today_date = strtotime( $today_date );
					$days = $today_date - $order_date;
					$day_diff = floor( $days / ( 60 * 60 * 24 ) );
					$day_allowed = get_option( 'ced_rnx_exchange_days', false );
					if ( isset( $ced_rnx_catalog_exchange_days ) && $ced_rnx_catalog_exchange_days != 0 ) {
						if ( $ced_rnx_catalog_exchange_days >= $day_diff ) {
							$ced_rnx_pages = get_option( 'ced_rnx_pages' );
							$page_id = $ced_rnx_pages['pages']['ced_exchange_from'];
							$exchange_url = get_permalink( $page_id );
							$order_total = $order->get_total();
							$exchange_min_amount = get_option( 'ced_rnx_exchange_minimum_amount', false );
							if ( isset( $exchange_min_amount ) && ! empty( $exchange_min_amount ) ) {
								if ( WC()->version < '3.0.0' ) {
									$order_id = $order->id;
								} else {
									$order_id = $order->get_id();
								}
								if ( $exchange_min_amount <= $order_total ) {
									?>
								<form action="<?php echo esc_html( add_query_arg( 'order_id', $order_id, $exchange_url ) ); ?>" method="post">
									<input type="hidden" value="<?php echo esc_html( $order_id ); ?>" name="order_id">
									<p>
									<?php
									if ( $order_status == 'wc-exchange-request' ) {
										?>
										<!-- <input type="submit" class="btn button" value="<?php _e( 'Update Exchange Request', 'woocommerce-refund-and-exchange' ); ?>"></p> -->
										<?php
									} else {
										if ( $ced_rnx_next_exchange ) {
											?>
										
										<!-- <input type="submit" class="btn button" value="<?php _e( 'Exchange Product', 'woocommerce-refund-and-exchange' ); ?>"></p> -->
											<?php
										}
									}
									?>
									</p>
								</form>
									<?php
								}
							} else {
								if ( WC()->version < '3.0.0' ) {
									$order_id = $order->id;
								} else {
									$order_id = $order->get_id();
								}
								?>
							<form action="<?php echo esc_html( add_query_arg( 'order_id', $order_id, $exchange_url ) ); ?>" method="post">
								<input type="hidden" value="<?php echo esc_html( $order_id ); ?>" name="order_id">
								<p>
								<?php
								if ( $order_status == 'wc-exchange-request' ) {
									?>
									<!-- <input type="submit" class="btn button" value="<?php _e( 'Update Exchange Request', 'woocommerce-refund-and-exchange' ); ?>"></p> -->
									<?php
								} else {
									if ( $ced_rnx_next_exchange ) {
										?>
									
									<!-- <input type="submit" class="btn button" value="<?php _e( 'Exchange Product', 'woocommerce-refund-and-exchange' ); ?>"></p> -->
										<?php
									}
								}
								?>
								</p>
							</form>
								<?php
							}
						}
					} else {
						if ( $day_allowed >= $day_diff && $day_allowed != 0 ) {
							$ced_rnx_pages = get_option( 'ced_rnx_pages' );
							$page_id = $ced_rnx_pages['pages']['ced_exchange_from'];
							$exchange_url = get_permalink( $page_id );
							$order_total = $order->get_total();
							$exchange_min_amount = get_option( 'ced_rnx_exchange_minimum_amount', false );
							if ( isset( $exchange_min_amount ) && ! empty( $exchange_min_amount ) ) {
								if ( WC()->version < '3.0.0' ) {
									$order_id = $order->id;
								} else {
									$order_id = $order->get_id();
								}

								if ( $exchange_min_amount <= $order_total ) {
									?>
									<form action="<?php echo esc_html( add_query_arg( 'order_id', $order_id, $exchange_url ) ); ?>" method="post">
										<input type="hidden" value="<?php echo esc_html( $order_id ); ?>" name="order_id">
										<p>
										<?php
										if ( $order_status == 'wc-exchange-request' ) {
											?>
											<!-- <input type="submit" class="btn button" value="<?php _e( 'Update Exchange Request', 'woocommerce-refund-and-exchange' ); ?>"></p> -->
											<?php
										} else {
											if ( $ced_rnx_next_exchange ) {
												?>
											
											<!-- <input type="submit" class="btn button" value="<?php _e( 'Exchange Product', 'woocommerce-refund-and-exchange' ); ?>"> -->
										</p>
												<?php
											}
										}
										?>
										</p>
									</form>
									<?php
								}
							} else {
								if ( WC()->version < '3.0.0' ) {
									$order_id = $order->id;
								} else {
									$order_id = $order->get_id();
								}
								?>
								<form action="<?php echo esc_html( add_query_arg( 'order_id', $order_id, $exchange_url ) ); ?>" method="post">
									<input type="hidden" value="<?php echo esc_html( $order_id ); ?>" name="order_id">
									<p>
									<?php
									if ( $order_status === 'wc-exchange-request' ) {
										?>
										<!-- <input type="submit" class="btn button" value="<?php _e( 'Update Exchange Request', 'woocommerce-refund-and-exchange' ); ?>"> -->
									</p>
										<?php
									} else {
										if ( $ced_rnx_next_exchange ) {
											?>
										
										<!-- <input type="submit" class="btn button" value="<?php _e( 'Exchange Product', 'woocommerce-refund-and-exchange' ); ?>"> -->
									</p>
											<?php
										}
									}
									?>
									</p>
								</form>
								<?php
							}
						}
					}
				}
			}
		}
	}
	new CED_rnx_order_exchange();
}
?>
