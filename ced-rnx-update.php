<?php
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (class_exists('Ced_Rnx_Update')) {
	class Ced_Rnx_Update
	{

		public function __construct()
		{
			register_activation_hook(CED_REFUND_N_EXCHANGE_FILE, array($this, 'ced_rnx_check_activation'));
			add_action('ced_rnx_check_event', array($this, 'ced_rnx_check_update'));
			add_filter('http_request_args', array($this, 'ced_rnx_updates_exclude'), 5, 2);
			add_action('install_plugins_pre_plugin-information', array($this, 'ced_rnx_plugin_details'));
			register_deactivation_hook(CED_REFUND_N_EXCHANGE_FILE, array($this, 'ced_rnx_check_deactivation'));
		}

		public function ced_rnx_check_deactivation()
		{
			wp_clear_scheduled_hook('ced_rnx_check_event');
		}

		public function ced_rnx_check_activation()
		{
			wp_schedule_event(time(), 'daily', 'ced_rnx_check_event');
		}

		public function ced_rnx_check_update()
		{
			global $wp_version;
			global $ced_rnx_update_check;
			$plugin_folder = plugin_basename(dirname(CED_REFUND_N_EXCHANGE_FILE));
			$plugin_file = basename((CED_REFUND_N_EXCHANGE_FILE));
			if (defined('WP_INSTALLING')) {
				return false;
			}
			$postdata = array(
				'action' => 'check_update',
				'purchase_code' => CED_RNX_LICENSE_KEY,
			);

			$args = array(
				'method' => 'POST',
				'body' => $postdata,
			);

			$response = wp_remote_post($ced_rnx_update_check, $args);
			if (! isset($response['body'])) {
				return false;
			}
			list($version, $url) = explode('~', $response['body']);

			if ($this->ced_rnx_plugin_get('Version') >= $version) {
				return false;
			}
			if (empty($response['response']['code']) || 200 !== $response['response']['code']) {
				return false;
			}

			$plugin_transient = get_site_transient('update_plugins');
			$a = array(
				'slug' => $plugin_folder,
				'new_version' => $version,
				'url' => $this->ced_rnx_plugin_get('AuthorURI'),
				'package' => $url,
			);
			$o = (object) $a;
			$plugin_transient->response[$plugin_folder . '/' . $plugin_file] = $o;
			set_site_transient('update_plugins', $plugin_transient);
		}

		public function ced_rnx_updates_exclude($r, $url)
		{
			if (0 !== strpos($url, 'http://api.wordpress.org/plugins/update-check')) {
				return $r;
			}
			$plugins = unserialize($r['body']['plugins']);
			unset($plugins->plugins[plugin_basename(__FILE__)]);
			if ($plugins->active) {
				unset($plugins->active[array_search(plugin_basename(__FILE__), $plugins->active)]);
			}
			$r['body']['plugins'] = serialize($plugins);
			return $r;
		}

		// Returns current plugin info.
		public function ced_rnx_plugin_get($i)
		{
			if (! function_exists('get_plugins')) {
				require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			}
			$plugin_folder = get_plugins('/' . plugin_basename(dirname(CED_REFUND_N_EXCHANGE_FILE)));
			$plugin_file = basename((CED_REFUND_N_EXCHANGE_FILE));
			return $plugin_folder[$plugin_file][$i];
		}

		public function ced_rnx_plugin_details()
		{
			global $ced_rnx_update_check;
			global $tab;
			if ($tab == 'plugin-information' && $_REQUEST['plugin'] == 'woocommerce-refund-and-exchange') {

				$postdata = array(
					'action' => 'check_update',
					'license_code' => CED_RNX_LICENSE_KEY,
				);

				$args = array(
					'method' => 'POST',
					'body' => $postdata,
				);

				$data = wp_remote_post($ced_rnx_update_check, $args);

				if (is_wp_error($data)) {
					return;
				}
				if (empty($data['response']['code']) || 200 !== $data['response']['code']) {
					return false;
				}

				if (isset($data['body'])) {
					$all_data = json_decode($data['body'], true);

					if (is_array($all_data) && ! empty($all_data)) {
						$this->create_html_data($all_data);
						die();
					}
				}
			}
		}

		public function create_html_data($all_data)
		{
?>
			<style>
				#TB_window {
					top: 4% !important;
				}

				.mwb_plugin_banner>img {
					height: 55%;
					width: 100%;
					border: 1px solid;
					border-radius: 7px;
				}

				.mwb_plugin_description>h4 {
					background-color: #3779B5;
					padding: 5px;
					color: #ffffff;
					border-radius: 5px;
				}

				.mwb_plugin_requirement>h4 {
					background-color: #3779B5;
					padding: 5px;
					color: #ffffff;
					border-radius: 5px;
				}

				#error-page>p {
					display: none;
				}
			</style>

<?php
		}
	}
	new Ced_Rnx_Update();
}
