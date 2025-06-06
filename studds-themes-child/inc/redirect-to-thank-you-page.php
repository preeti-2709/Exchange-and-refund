<?php

/**
 * This function redirects to thank you page
 *
 * @since 1.0.0
 */

// add_action('woocommerce_thankyou', 'custom_redirect_after_purchase');
function custom_redirect_after_purchase($order_id) {
    $order = wc_get_order($order_id);

    if (!$order->has_status('failed')) {
        wp_safe_redirect(home_url('/thank-you/'));
        exit;
    }
}