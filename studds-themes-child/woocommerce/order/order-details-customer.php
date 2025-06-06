<?php
/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.6.0
 */

defined( 'ABSPATH' ) || exit;

$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<section class="woocommerce-customer-details">

	<?php if ( $show_shipping ) : ?>

	<section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses" style="margin-top: 20px;background-color: #e9e9e9;padding: 20px;border: 1px solid #c1b3b3;">
		<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1">

	<?php endif; ?>

	<h3 style="font-size: 18px;margin-top: 30px;"><strong><?php esc_html_e( 'Billing address', 'woocommerce' ); ?></strong></h3>

	<address>
		<?php 
		$billing_address = $order->get_address( 'billing' ); 
		echo "<strong>".$billing_address['first_name']." ". $billing_address['last_name']."</strong>" ;
		echo "<br/>";
		echo $billing_address['address_1'].", ".$billing_address['address_2'];
		echo "<br/>";
		echo $billing_address['city']. "-" . $billing_address['postcode'];
		?>
		<br/><br/>
		<strong>Phone number & Email</strong><br/>
		<?php if ( $order->get_billing_phone() ) : ?>
			<p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_billing_phone() ); ?></p>
		<?php endif; ?>

		<?php if ( $order->get_billing_email() ) : ?>
			<p class="woocommerce-customer-details--email"><?php echo esc_html( $order->get_billing_email() ); ?></p>
		<?php endif; ?>
	</address>

	<?php if ( $show_shipping ) : ?>

		</div><!-- /.col-1 -->

		<div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address col-2">
			<h3 style="font-size: 18px;margin-top: 30px;"><strong><?php esc_html_e( 'Shipping address', 'woocommerce' ); ?></strong></h3>
			<address>
			
				<?php 
					$shipping_address = $order->get_address( 'shipping' ); 
					if($shipping_address){
						echo "<strong>".$shipping_address['first_name']." ". $shipping_address['last_name']."</strong>" ;
						echo "<br/>";
						echo $shipping_address['address_1'].", ".$shipping_address['address_2'];
						echo "<br/>";
						echo $shipping_address['city']. "-" . $shipping_address['postcode'];
				// 		echo $shipping_address['city']. "-" . $shipping_address['postcode'];
					}
					?>
					<br/><br/>
					<strong>Phone number & Email</strong><br/>

				<?php if ( $order->get_shipping_phone() ) : ?>
					<p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_shipping_phone() ); ?></p>
				<?php endif; ?>
				<?php if ( $order->get_billing_email() ) : ?>
			<p class="woocommerce-customer-details--email"><?php echo esc_html( $order->get_billing_email() ); ?></p>
		<?php endif; ?>
			</address>
		</div><!-- /.col-2 -->

	</section><!-- /.col2-set -->

	<?php endif; ?>

	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>

</section>
