<?php


/**
 * Function to create the Studds Helmets Warranty Data Sync table in the WordPress database.
 * Ensures the table does not already exist before creating it.
 */
function create_studds_helmets_warranty_data_sync_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'studds_helmets_warranty_data_sync';

    // Create table if it doesn't exist
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id INT(11) NOT NULL AUTO_INCREMENT,
            unique_serial_no VARCHAR(100) NOT NULL UNIQUE,
            material_code VARCHAR(100) NOT NULL,
            modal_name VARCHAR(100),
            `current_date` DATE,
            `time` TIME,
            entry_update_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    // Add warranty_start_date if it doesn't exist
    $column = $wpdb->get_results("SHOW COLUMNS FROM `$table_name` LIKE 'warranty_start_date'");
    if (empty($column)) {
        $wpdb->query("ALTER TABLE `$table_name` ADD `warranty_start_date` DATE NULL AFTER `time`");
    }
}
add_action('after_setup_theme', 'create_studds_helmets_warranty_data_sync_table');


add_action('rest_api_init', 'register_rest_api_routes');

/**
 * Register REST API Routes
 */
function register_rest_api_routes()
{
    register_rest_route('warranty-data-sync/v1', '/add', array(
        'methods'  => 'POST',
        'callback' => 'add_sap_data',
        'permission_callback' => 'validate_api_auth_key', // Modify this for authentication
    ));
}

function validate_api_auth_key($request)
{
    $auth_key = $request->get_header('Authorization'); // Get Auth Key from Headers
    $valid_key = 'jvhzxcbjkhvbskjhbdkfbkfjahskdfj'; // Set Your Secure API Key Here

    if ($auth_key !== "Bearer $valid_key") {
        return new WP_Error('rest_forbidden', __('Invalid API Key'), array('status' => 403));
    }

    return true;
}

/**
 * Handles the REST API request to add/update warranty data.
 *
 * @param WP_REST_Request $request The incoming REST API request.
 * @return WP_REST_Response JSON response indicating success or failure.
 */
function add_sap_data($request)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'studds_helmets_warranty_data_sync';

    // Get parameters from the request
    $params = $request->get_json_params();
    $data = isset($params['data']) ? $params['data'] : [];

    // Validate that data is provided and is in the correct format
    if (empty($data) || !is_array($data)) {
        return new WP_REST_Response(array('status' => 'error', 'message' => 'Invalid data format'), 400);
    }

    // Initialize response arrays and counters
    $response = [
        'success' => [],
        'failed' => [],
        'skipped' => [],
        'success_count' => 0,   // Count for success
        'failed_count' => 0,    // Count for failed
        'skipped_count' => 0,   // Count for skipped
    ];

    // Loop through each data entry to insert in the database
    foreach ($data as $item) {
        $unique_serial_no = isset($item['unique_serial_no']) ? sanitize_text_field($item['unique_serial_no']) : '';
        $material_code = isset($item['material_code']) ? sanitize_text_field($item['material_code']) : '';
        $modal_name = isset($item['modal_name']) ? sanitize_text_field($item['modal_name']) : '';
        $warranty_start_date = isset($item['warranty_start_date']) ? sanitize_text_field($item['warranty_start_date']) : '';
        $current_date = isset($item['current_date']) ? sanitize_text_field($item['current_date']) : current_time('Y-m-d');
        $time = isset($item['time']) ? sanitize_text_field($item['time']) : current_time('H:i:s');

        // Validate required fields
        if (empty($unique_serial_no) || empty($material_code) || empty($modal_name)) {
            $response['failed'][] = [
                'unique_serial_no' => $unique_serial_no,
                'material_code' => $material_code,
                'reason' => 'Missing required fields'
            ];
            $response['failed_count']++;
            continue;
        }

        // Check if the serial number already exists
        $existing_entry = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE unique_serial_no = %s AND material_code = %s",
            $unique_serial_no, $material_code
        ));

        if ($existing_entry > 0) {
            // Skip record if the combination of serial number and material code already exists
            $response['skipped'][] = [
                'unique_serial_no' => $unique_serial_no,
                'material_code' => $material_code,
                'reason' => 'Record already exists'
            ];
            $response['skipped_count']++;
            continue;
        }

        // Insert new record
        $inserted = $wpdb->insert(
            $table_name,
            [
                'unique_serial_no' => $unique_serial_no,
                'material_code' => $material_code,
                'modal_name' => $modal_name,
                'current_date' => $current_date,
                'warranty_start_date' => $warranty_start_date,
                'time' => $time,
                'entry_update_date' => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        if ($inserted) {
            $response['success'][] = [
                'unique_serial_no' => $unique_serial_no,
                'material_code' => $material_code,
                'reason' => 'inserted'
            ];
            $response['success_count']++;
        } else {
            $response['failed'][] = [
                'unique_serial_no' => $unique_serial_no,
                'material_code' => $material_code,
                'reason' => 'Database insert failed'
            ];
            $response['failed_count']++;
        }
    }

   // Return JSON response with success, failure, skipped counts, and details
    return new WP_REST_Response(array(
        'status' => 'success',
        'message' => 'Data processed',
        'details' => $response
    ), 200);
}

