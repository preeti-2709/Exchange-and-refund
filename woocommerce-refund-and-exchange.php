<?php

/**
 * Plugin Name:       WooCommerce Refund & Exchange With RMA
 * Plugin URI:        https://wpswings.com
 * Description:       WooCommerce Refund and Exchange extension allows users to submit product refund and exchange request. The plugin provides a dedicated mailing system that would help to communicate better between store owner and customers.
 * Version:           3.1.5
 * Author:            WP Swings <webmaster@wpswings.com>

 * Text Domain:       woocommerce-refund-and-exchange
 * Domain Path:       /languages
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package woocommerce_refund_and_exchange
 */

/**
 * Exit if accessed directly.
 */

if (! defined('ABSPATH')) {
	exit;
}

$ced_rnx_lite_activated = true;
$activated              = true;
if (function_exists('is_multisite') && is_multisite()) {
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
	if (! is_plugin_active('woocommerce/woocommerce.php')) {
		$activated = false;
	}
	if (is_plugin_active('woo-refund-and-exchange-lite/woocommerce-refund-and-exchange-lite.php')) {
		$ced_rnx_lite_activated = true;
	}
} else {
	if (! in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
		$activated = false;
	}
	if (in_array('woo-refund-and-exchange-lite/woocommerce-refund-and-exchange-lite.php', apply_filters('active_plugins', get_option('active_plugins')))) {
		$ced_rnx_lite_activated = true;
	}
}
if ($activated) {
	if ($ced_rnx_lite_activated) {
		add_action('admin_init', 'ced_rnx_lite_plugin_deactivate');
		/**
		 * Call Admin notices
		 *
		 * @name ced_rnx_plugin_deactivate()
		 * @author wpswings<webmaster@wpswings.com>
		 * @link http://www.wpswings.com/
		 */
		function ced_rnx_lite_plugin_deactivate()
		{
			deactivate_plugins('woo-refund-and-exchange-lite/woocommerce-refund-and-exchange-lite.php');
			do_action('woocommerce_product_options_stock_fields');
		}
	}
}

/**
 * Check if WooCommerce is active
 */
if ($activated) {

	define('CED_REFUND_N_EXCHANGE_DIRPATH', plugin_dir_path(__FILE__));
	define('CED_REFUND_N_EXCHANGE_URL', plugin_dir_url(__FILE__));
	define('CED_REFUND_N_EXCHANGE_VERSION', '3.1.5');

	include_once CED_REFUND_N_EXCHANGE_DIRPATH . 'includes/woocommerce-rnx-class.php';
	include_once CED_REFUND_N_EXCHANGE_DIRPATH . 'admin/class-order-meta.php';
	include_once CED_REFUND_N_EXCHANGE_DIRPATH . 'admin/class-admin-setting.php';
	include_once CED_REFUND_N_EXCHANGE_DIRPATH . 'front/class-order-return.php';
	include_once CED_REFUND_N_EXCHANGE_DIRPATH . 'front/class-order-exchange.php';
	// include_once CED_REFUND_N_EXCHANGE_DIRPATH . 'front/class-order-cancel.php';
	include_once CED_REFUND_N_EXCHANGE_DIRPATH . 'gateway/wallet-gateway.php';

	/**
	 * This function is used for formatting the price
	 *
	 * @author wpswings<webmaster@wpswings.com>
	 * @link http://www.wpswings.com/
	 * @param int $price .
	 * @return string
	 */
	function ced_rnx_format_price($price)
	{
		$price           = apply_filters('formatted_woocommerce_price', number_format($price, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator()), $price, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator());
		$currency_symbol = get_woocommerce_currency_symbol();
		$currency_pos    = get_option('woocommerce_currency_pos');
		switch ($currency_pos) {
			case 'left':
				$uprice = $currency_symbol . '<span class="ced_rnx_formatted_price">' . $price . '</span>';
				break;
			case 'right':
				$uprice = '<span class="ced_rnx_formatted_price">' . $price . '</span>' . $currency_symbol;
				break;
			case 'left_space':
				$uprice = $currency_symbol . '&nbsp;<span class="ced_rnx_formatted_price">' . $price . '</span>';
				break;
			case 'right_space':
				$uprice = '<span class="ced_rnx_formatted_price">' . $price . '</span>&nbsp;' . $currency_symbol;
				break;
		}
		return $uprice;
	}

	/**
	 * This function is used for formatting the price seprator
	 *
	 * @author wpswings<webmaster@wpswings.com>
	 * @link http://www.wpswings.com/
	 * @param unknown $price .
	 * @return price
	 */
	function ced_rnx_currency_seprator($price)
	{
		$price = apply_filters('formatted_woocommerce_price', number_format($price, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator()), $price, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator());
		return $price;
	}


	/**
	 * This function is to add pages for return and exchange request form
	 *
	 * @author wpswings<webmaster@wpswings.com>
	 * @link http://www.wpswings.com/
	 */
	function ced_rnx_add_pages()
	{
		$ced_rnx_pages = array();

		$ced_rnx_return_request_form = array(
			'post_author'    => 1,
			'post_name'      => 'return-request-form',
			'post_title'     => 'Return Request Form',
			'post_type'      => 'page',
			'post_status'    => 'publish',

		);

		$page_id = wp_insert_post($ced_rnx_return_request_form);

		if ($page_id) {
			$ced_rnx_pages['pages']['ced_return_from'] = $page_id;
		}

		$ced_exchange_request_form = array(
			'post_author'    => 1,
			'post_name'      => 'exchange-request-form',
			'post_title'     => 'Exchange Request Form',
			'post_type'      => 'page',
			'post_status'    => 'publish',

		);

		$page_id = wp_insert_post($ced_exchange_request_form);

		if ($page_id) {
			$ced_rnx_pages['pages']['ced_exchange_from'] = $page_id;
		}

		$ced_return_exchange_request_form = array(
			'post_author'    => 1,
			'post_name'      => 'request-form',
			'post_title'     => 'Return/Exchange Request Form',
			'post_type'      => 'page',
			'post_status'    => 'publish',

		);

		$page_id = wp_insert_post($ced_return_exchange_request_form);

		if ($page_id) {
			$ced_rnx_pages['pages']['ced_request_from'] = $page_id;
		}

		$ced_cancel_product_request_form = array(
			'post_author'    => 1,
			'post_name'      => 'product-cancel-request-form',
			'post_title'     => 'Product Cancel Request Form',
			'post_type'      => 'page',
			'post_status'    => 'publish',

		);

		$page_id = wp_insert_post($ced_cancel_product_request_form);

		if ($page_id) {
			$ced_rnx_pages['pages']['ced_cancel_request_from'] = $page_id;
		}

		$mwb_view_order_msg = array(
			'post_author'    => 1,
			'post_name'      => 'view-order-msg',
			'post_title'     => 'View Order Messages',
			'post_type'      => 'page',
			'post_status'    => 'publish',

		);

		$page_id = wp_insert_post($mwb_view_order_msg);

		if ($page_id) {
			$ced_rnx_pages['pages']['mwb_view_order_msg'] = $page_id;
		}

		update_option('ced_rnx_pages', $ced_rnx_pages);
	}
	register_activation_hook(__FILE__, 'ced_rnx_add_pages');

	function ced_rnx_delete_post()
	{
		$ced_rnx_pages = get_option('ced_rnx_pages');
		foreach ($ced_rnx_pages['pages'] as $key => $value) {
			wp_delete_post($value, false);
		}
		delete_option('ced_rnx_pages');
	}
	register_deactivation_hook(__FILE__, 'ced_rnx_delete_post');

	/**
	 * This function is used to load language'.
	 *
	 * @author wpswings<webmaster@wpswings.com>
	 * @link http://www.wpswings.com/
	 */
	function ced_rnx_load_plugin_textdomain()
	{
		$domain = 'woocommerce-refund-and-exchange';
		$locale = apply_filters('plugin_locale', get_locale(), $domain);
		load_textdomain($domain, CED_REFUND_N_EXCHANGE_DIRPATH . 'languages/' . $domain . '-' . $locale . '.mo');
		$var = load_plugin_textdomain($domain, false, plugin_basename(dirname(__FILE__)) . '/languages');
	}
	add_action('plugins_loaded', 'ced_rnx_load_plugin_textdomain');


	/**
	 * This function checks session is set or not
	 *
	 * @author wpswings<webmaster@wpswings.com>
	 * @link http://www.wpswings.com/
	 */
	function ced_rnx_set_session()
	{
		global $wp;
		if (isset($_POST['ced_rnx_order_id_submit'])) {
			$order_id      = $_POST['order_id'];
			$billing_email = get_post_meta($order_id, '_billing_email', true);
			$req_email     = $_POST['order_email'];
			$order         = wc_get_order($order_id);
			if (! empty($req_email) && ! empty($billing_email) && ! empty($order_id) && ! empty($order) && $req_email === $billing_email) {
				WC()->session->set('ced_rnx_email', $billing_email);
				$url = $order->get_checkout_order_received_url();
				wp_safe_redirect($url);
				exit();
			} elseif (empty($req_email) && empty($order_id)) {
				WC()->session->set('ced_rnx_notification', __('Please Enter OrderId and Email', 'woocommerce-refund-and-exchange'));
			} elseif (empty($req_email)) {
				WC()->session->set('ced_rnx_notification', __('Please Enter Email', 'woocommerce-refund-and-exchange'));
			} elseif (empty($order_id)) {
				WC()->session->set('ced_rnx_notification', __('Please Enter OrderId', 'woocommerce-refund-and-exchange'));
			} else {
				WC()->session->set('ced_rnx_notification', __('OrderId or Email is Invalid', 'woocommerce-refund-and-exchange'));
			}
		}
	}
	add_action('init', 'ced_rnx_set_session');
	/**
	 * This function set a woocommerce customer session for guest user
	 *
	 * @author wpswings<webmaster@wpswings.com>
	 * @link http://www.wpswings.com/
	 */
	add_action(
		'woocommerce_init',
		function () {
			if (is_user_logged_in() || is_admin()) {
				return;
			}

			if (isset(WC()->session)) {
				if (! WC()->session->has_session()) {
					WC()->session->set_customer_session_cookie(true);
				}
			}
		}
	);

	/**
	 * Add settings link on plugin page
	 *
	 * @name admin_settings_for_pmr()
	 * @author wpswings<webmaster@wpswings.com>
	 * @link http://www.wpswings.com/
	 */
	function ced_rnx_admin_settings($actions, $plugin_file)
	{
		static $plugin;
		if (! isset($plugin)) {

			$plugin = plugin_basename(__FILE__);
		}
		if ($plugin == $plugin_file) {
			$settings = array(
				'settings' => '<a href="' . home_url('/wp-admin/admin.php?page=wc-settings&tab=ced_rnx_setting') . '">' . __('Settings', 'woocommerce-refund-and-exchange') . '</a>',
			);
			$actions = array_merge($settings, $actions);
		}
		return $actions;
	}

	// add link for settings .
	add_filter('plugin_action_links', 'ced_rnx_admin_settings', 10, 5);

	/**
	 * Adding custom setting links at the plugin activation list.
	 *
	 * @param  array  $links_array      array containing the links to plugin.
	 * @param  string $plugin_file_name plugin file name.
	 * @return array
	 */
	function ced_rnx_custom_settings_at_plugin_tab($links_array, $plugin_file_name)
	{
		if (strpos($plugin_file_name, basename(__FILE__))) {
			$links_array[] = '<a target="_blank" href="https://docs.wpswings.com/woocommerce-refund-and-exchange-rma/?utm_source=wpswings-rma-doc&utm_medium=rma-cc-backend&utm_campaign=documentation">' . __('Docs', 'woocommerce-refund-and-exchange') . '</a>';
		}
		return $links_array;
	}
	add_filter('plugin_row_meta', 'ced_rnx_custom_settings_at_plugin_tab', 10, 2);


	/**
	 * Dynamically Generate Coupon Code
	 *
	 * @name ced_rnx_coupon_generator
	 * @param number $length
	 * @return string
	 * @author wpswings<webmaster@wpswings.com>
	 * @link http://www.wpswings.com/
	 */
	function ced_rnx_coupon_generator($length = 10)
	{
		$password = '';
		$alphabets = range('A', 'Z');
		$numbers = range('0', '9');
		$final_array = array_merge($alphabets, $numbers);
		while ($length--) {
			$key = array_rand($final_array);
			$password .= $final_array[$key];
		}

		$rnx_prefix = get_option('ced_rnx_return_coupon_prefeix', '');
		$password = $rnx_prefix . $password;
		return $password;
	}

	function ced_rnx_wallet_feature_enable()
	{
		$enabled = false;
		$wallet_enabled = get_option('ced_rnx_return_wallet_enable', 'no');
		if ($wallet_enabled == 'yes') {
			$enabled = true;
		}
		return $enabled;
	}

	register_activation_hook(__FILE__, 'ced_rnx_activation_process');

	/**
	 * install function, perform all necessary operation
	 * on plugin activation.
	 *
	 * @since 1.0.0
	 */
	function ced_rnx_activation_process()
	{
		$ced_rnx_activation_date = get_option('ced_rnx_activation_date', false);
		if (! $ced_rnx_activation_date) {
			$today_date = current_time('timestamp');
			update_option('ced_rnx_activation_date', $today_date);
		}
	}
	add_action('admin_notices', 'ced_rnx_license_notification');

	/**
	 * Licennse activation notification messege.
	 *
	 * @since 1.0.0
	 */


	function ced_rnx_send_order_msg_callback($order_id, $msg, $sender, $to)
	{
		$flag       = false;
		$filename   = array();
		$attachment = array();
		$order_msg  = array();
		$date       = strtotime(date('Y-m-d H:i:s'));
		if (isset($_FILES['mwb_order_msg_attachment']['tmp_name']) && ! empty($_FILES['mwb_order_msg_attachment']['tmp_name'])) {
			$count = sizeof($_FILES['mwb_order_msg_attachment']['tmp_name']);
			$file_uploaded = false;
			if (isset($_FILES['mwb_order_msg_attachment']['tmp_name'][0]) && ! empty($_FILES['mwb_order_msg_attachment']['tmp_name'][0])) {
				$file_uploaded = true;
			}
			if ($file_uploaded) {
				for ($i = 0; $i < $count; $i++) {
					if (isset($_FILES['mwb_order_msg_attachment']['tmp_name'][$i])) {
						$directory = ABSPATH . 'wp-content/attachment';
						if (! file_exists($directory)) {
							mkdir($directory, 0755, true);
						}
						$sourcepath             = $_FILES['mwb_order_msg_attachment']['tmp_name'][$i];
						$targetpath             = $directory . '/' . $order_id . '-' . $_FILES['mwb_order_msg_attachment']['name'][$i];
						$attachment[$i]       = $targetpath;
						$filename[$i]['name'] = $_FILES['mwb_order_msg_attachment']['name'][$i];
						$file_type              = $_FILES['mwb_order_msg_attachment']['type'][$i];
						if ('image/png' == $file_type || 'image/jpeg' == $file_type || 'image/jpg' == $file_type) {
							$filename[$i]['img'] = true;
							move_uploaded_file($sourcepath, $targetpath);
							$order_msg[$date]['files'] = $filename;
						} else {
							$filename[$i]['img'] = false;
						}
					}
				}
			}
		}
		$order_msg[$date]['sender'] = $sender;
		$order_msg[$date]['msg']    = $msg;

		$get_msg = get_option($order_id . '-mwb_cutomer_order_msg', array());
		$msg_count = get_post_meta($order_id, 'mwb_order_msg_count', 0);
		if (isset($get_msg) && ! empty($get_msg)) {
			array_push($get_msg, $order_msg);
		} else {
			$get_msg = array();
			array_push($get_msg, $order_msg);
		}
		update_option($order_id . '-mwb_cutomer_order_msg', $get_msg);
		$email_enable = get_option('ced_rnx_order_message_emails', false);

		if ('yes' == $email_enable) {
			$customer_email = WC()->mailer()->emails['wc_rma_messages_email'];
			$email_status   = $customer_email->trigger($msg, $attachment, $to, $order_id);
		}

		$flag = true;
		return $flag;
	}

	add_filter('mwb_rnx_meta_content', 'wptexturize');
	add_filter('mwb_rnx_meta_content', 'convert_smilies');
	add_filter('mwb_rnx_meta_content', 'convert_chars');
	add_filter('mwb_rnx_meta_content', 'wpautop');
	add_filter('mwb_rnx_meta_content', 'shortcode_unautop');
	add_filter('mwb_rnx_meta_content', 'prepend_attachment');
} else {
	/**
	 * Show warning message if woocommerce is not install
	 *
	 * @name ced_rnx_plugin_error_notice()
	 * @author wpswings<webmaster@wpswings.com>
	 * @link http://www.wpswings.com/
	 */
	function ced_rnx_plugin_error_notice()
	{
?>
		<div class="error notice is-dismissible">
			<p><?php esc_html_e('Woocommerce is not activated, Please activate Woocommerce first to install WooCommerce Refund and Exchange.', 'woocommerce-refund-and-exchange'); ?></p>
		</div>
		<style>
			#message {
				display: none;
			}
		</style>
<?php
	}
	add_action('admin_init', 'ced_rnx_plugin_deactivate');


	/**
	 * Call Admin notices
	 *
	 * @name ced_rnx_plugin_deactivate()
	 * @author wpswings<webmaster@wpswings.com>
	 * @link http://www.wpswings.com/
	 */

	function ced_rnx_plugin_deactivate()
	{
		deactivate_plugins(plugin_basename(__FILE__));
		do_action('woocommerce_product_options_stock_fields');
		add_action('admin_notices', 'ced_rnx_plugin_error_notice');
	}
}

register_uninstall_hook(__FILE__, 'mwb_fn_uninstall');

function mwb_fn_uninstall()
{
	$ced_rnx_pages = get_option('ced_rnx_pages');
	foreach ($ced_rnx_pages['pages'] as $key => $value) {
		wp_delete_post($value, false);
	}
	delete_option('ced_rnx_pages');
}

// $ced_rnx_license_key = get_option( 'ced_rnx_license_key' );
// define( 'CED_RNX_LICENSE_KEY', $ced_rnx_license_key );
// define( 'CED_REFUND_N_EXCHANGE_FILE', __FILE__ );
// $ced_rnx_update_check = 'https://wpswings.com/pluginupdates/codecanyon/woocommerce-refund-and-exchange/update.php';
// require_once( 'ced-rnx-update.php' );
?>