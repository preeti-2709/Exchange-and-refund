<?php

/**
 * Custom code added to functions
 *
 * @since 1.0.0
 */

 /* customisation PERCENTAGE COUPON DISCOUNT DECIMAL AMOUNT ISSUE  end */

//add_action('woocommerce_single_product_summary', 'bbloomer_product_sold_count', 11);

function bbloomer_product_sold_count()
{
    global $product;
    $units_sold = $product->get_total_sales();
    if ($units_sold) echo '<p>' . sprintf(__('Units Sold: %s', 'woocommerce'), $units_sold) . '</p>';
}

add_action('woocommerce_after_shop_loop_item', 'wpm_product_sold_count', 11);

function wpm_product_sold_count()
{
    global $product;

    $units_sold = $product->get_total_sales();
    if ($units_sold > 5) {
        echo '<p class="sold-product">' . sprintf(__('Total Product: %s Sold', 'woocommerce'), $units_sold) . '</p>';
    }
}



/* Pree customisation FS docs changes for thunder CR end */

// Step 1: Register Custom Endpoint
function custom_my_account_endpoint()
{
    add_rewrite_endpoint('custom-form', EP_ROOT | EP_PAGES);
}
add_action('init', 'custom_my_account_endpoint');



// Step 3: Add Form to My Account Page
function add_custom_form_to_my_account($items)
{
    $items['custom-form'] = __('Custom Form', 'your-text-domain');
    return $items;
}
add_filter('woocommerce_account_menu_items', 'add_custom_form_to_my_account');

// Display custom endpoint content in the My Account page
function display_custom_form_content()
{
    wc_get_template('myaccount/custom-myaccount-template.php');
}
add_action('woocommerce_account_custom-form_endpoint', 'display_custom_form_content');


function create_custom_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_table';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        serial_no varchar(255) NOT NULL,
        product_category varchar(255) NOT NULL,
        product_name varchar(255) NOT NULL,
        customer_name varchar(255) NOT NULL,
        source_name varchar(255) NOT NULL,
        date_of_purchase date NOT NULL,
        email varchar(255) NOT NULL,
        mobile varchar(255) NOT NULL,
        city varchar(255) NOT NULL,
        state varchar(255) NOT NULL,
        country varchar(255) NOT NULL,
        zipcode varchar(255) NOT NULL,
        address text NOT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
// Create the table when WordPress initializes
add_action('init', 'create_custom_table');



// add_action('admin_post_submit_activation', 'handle_submit_activation');
// add_action('admin_post_nopriv_submit_activation', 'handle_submit_activation');


function handle_submit_activation()
{
    if (isset($_POST['submit_activation'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'custom_table';

        // Define regex patterns
        $regex_patterns = array(
            'serial_no' => '/^[A-Za-z0-9\-]{1,50}$/',
            'product_name' => '/^[A-Za-z0-9\s\-]{1,100}$/',
            'customer_name' => '/^[A-Za-z\s]{1,100}$/',
            'source_name' => '/^[A-Za-z\s]{1,100}$/',
            'date_of_purchase' => '/^\d{4}-\d{2}-\d{2}$/',
            'email' => '/^[\w\.\-]+@[\w\-]+\.[A-Za-z]{2,}$/',
            'mobile' => '/^\d{10,15}$/',
            'city' => '/^[A-Za-z\s\-]{1,100}$/',
            'state' => '/^[A-Za-z\s\-]{1,100}$/',
            'country' => '/^[A-Za-z\s\-]{1,100}$/',
            'zipcode' => '/^\d{4,10}$/',
            'address' => '/^[A-Za-z0-9\s\-\.\,\#]{1,255}$/'
        );

        // Initialize an array to store errors
        $errors = array();

        // Validate inputs using regex
        foreach ($_POST as $key => $value) {
            if (isset($regex_patterns[$key]) && !preg_match($regex_patterns[$key], $value)) {
                $errors[] = "Invalid input for $key";
            }
        }

        // If there are any errors, display them and halt the process
        if (!empty($errors)) {
            foreach ($errors as $error) { ?>
                <script>
                    alert('<?php echo $error; ?>');
                    var serialNo = "<?php echo $_POST['serial_no'] ?>";
                    window.location.replace('https://studds-revamp.postyoulike.com/warranty-activation/?serial_no=' + serialNo);
                </script>";

            <?php }
            exit;
        }

        // Sanitize input data
        $serial_no = sanitize_text_field($_POST['serial_no']);
        $product_name = sanitize_text_field($_POST['model_name']);
        $customer_name = sanitize_text_field($_POST['customer_name']);
        $source_name = sanitize_text_field($_POST['source_name']);
        $date_of_purchase = sanitize_text_field($_POST['date_of_purchase']);
        $email = sanitize_email($_POST['email']);
        $mobile = sanitize_text_field($_POST['mobile']);
        $city = sanitize_text_field($_POST['city']);
        $state = sanitize_text_field($_POST['state']);
        $country = sanitize_text_field($_POST['country']);
        $zipcode = sanitize_text_field($_POST['zipcode']);
        $address = sanitize_textarea_field($_POST['address']);
        $created_at = current_time('mysql');

        // Check if the serial number already exists in the database
        $existing_serial = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE serial_no = %s", $serial_no)
        );

        // If the serial number already exists, display an error message and halt the process
        if ($existing_serial > 0) {
            ?>
            <script>
                alert('Your Warranty is already activated!');
                var serialNo = "<?php echo $serial_no; ?>";
                window.location.replace('https://studds-revamp.postyoulike.com/warranty-activation/?serial_no=' + serialNo);
            </script>
    <?php
            exit;
        }

        // Insert data into the database
        $wpdb->insert(
            $table_name,
            array(
                'serial_no' => $serial_no,
                'product_name' => $product_name,
                'customer_name' => $customer_name,
                'source_name' => $source_name,
                'date_of_purchase' => $date_of_purchase,
                'email' => $email,
                'mobile' => $mobile,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'zipcode' => $zipcode,
                'address' => $address,
                'created_at' => $created_at,
            )
        );

        // Redirect to the referring page with success message
        $redirect_url = esc_url_raw($_SERVER['HTTP_REFERER']);
        wp_redirect(add_query_arg(array('success' => 'true'), $redirect_url));
        exit;
    }
}

remove_action('woocommerce_order_status_processing', 'wc_maybe_reduce_stock_levels', 20);

add_action('woocommerce_order_status_processing', 'custom_maybe_reduce_stock_levels', 20, 1);

function custom_maybe_reduce_stock_levels($order_id)
{
    // Check if stock was reduced from webhook
    $stock_reduced_from_webhook = get_post_meta($order_id, '_stock_reduced_from_webhook', true);

    if (!$stock_reduced_from_webhook) {
        // If stock was not reduced from webhook, proceed with default stock reduction
        wc_maybe_reduce_stock_levels($order_id);
    }
}



//preeeee============================================
function add_fontawesome_preload()
{
    ?>
    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/css/fonts/FontTawesome/fontawesome-webfont.woff2?v=4.7.0" as="font" crossorigin>
<?php
}
add_action('wp_head', 'add_fontawesome_preload');


// Add preconnect tag for Google Fonts
function add_google_fonts_preconnect()
{
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action('wp_head', 'add_google_fonts_preconnect', 0);



// // Schedule event to change order status from Pending Payment to Admin Cancelled after a time limit
// add_action( 'woocommerce_order_status_pending_to_cancelled', 'custom_schedule_order_status_change', 10, 2 );
// function custom_schedule_order_status_change( $order_id, $order ) {
//     // Change the time limit as needed (in seconds)
//     $time_limit = 60; // 1 minute
//     $scheduled_time = $time_limit + time();

//     // Schedule event to change order status
//     wp_schedule_single_event( $scheduled_time, 'custom_change_order_status_to_admin_cancelled', array( $order_id ) );
// }

// // Change order status to Admin Cancelled
// add_action( 'custom_change_order_status_to_admin_cancelled', 'custom_change_order_status_to_admin_cancelled_callback' );
// function custom_change_order_status_to_admin_cancelled_callback( $order_id ) {
//     // Get the order object
//     $order = wc_get_order( $order_id );

//     // Check if the order is still pending payment
//     if ( $order->has_status( 'wc-pending' )) {
//         // Change order status to Admin Cancelled
//         $order->update_status( 'wc-admin-cancelled' );
//     }
// }


// // Hook into the init action and schedule the event on plugin activation
// add_action('init', 'schedule_order_cancellation_event');

// function schedule_order_cancellation_event() {
//     if (!wp_next_scheduled('cancel_pending_orders_event')) {
//         // Schedule the event to run after 1 minute
//         wp_schedule_single_event(time() + 60, 'cancel_pending_orders_event');
//     }
// }

// // Hook into the event to display "Hello" after running cron
// add_action('cancel_pending_orders_event', 'display_hello_after_cron');

// function display_hello_after_cron() {
//     // Specify the path to the log file in the wp-content folder
//     $log_file = WP_CONTENT_DIR . '/cron_log.txt';
//     $message = "Hello";

//     // Append the message to the log file
//     file_put_contents($log_file, $message . "\n", FILE_APPEND);

//     // Output "Hello" to the browser for testing purposes
//     echo "Hello";
// }

// // Schedule the follow-up email when the order status changes to completed

// function schedule_follow_up_email($order_id) {
//     if (!wp_next_scheduled('send_follow_up_email', array($order_id))) {
//         error_log("Scheduling follow-up email for order ID: $order_id");
//         wp_schedule_single_event(time() + 1 * MINUTE_IN_SECONDS, 'send_follow_up_email', array($order_id)); // Changed to 1 minute for testing
//     }
// }
// add_action('woocommerce_order_status_completed', 'schedule_follow_up_email');



// // Send the follow-up email
// function send_follow_up_email($order_id) {
//     if (!$order_id) {
//         error_log("No order ID found");
//         return;
//     }

//     $order = wc_get_order($order_id);
//     if (!$order) {
//         error_log("Order not found for order ID: $order_id");
//         return;
//     }

//     $email = $order->get_billing_email();
//     $customer_name = $order->get_billing_first_name();
//     $items = $order->get_items();
//     if (empty($items)) {
//         error_log("No items found for order ID: $order_id");
//         return;
//     }

//     $product_id = reset($items)->get_product_id();
//     $review_link = add_query_arg('review_order_id', $order_id, get_permalink($product_id) . '#reviews');
//     $current_time = current_time('mysql'); // Get current date and time

//     $subject = 'We would love your feedback';
//     $message = sprintf(
//         'Hi %s,' . "\n\n" . 'Thank you for your recent purchase. We hope you are enjoying your product. We would love to hear your feedback. Please leave a review <a href="%s">here...!</a>' . "\n\n" . 'Sent on: %s',
//         $customer_name,
//         $review_link,
//         $current_time
//     );

//     // Send the email
//     $result = wp_mail($email, $subject, $message);
//     if ($result) {
//         error_log("Follow-up email sent to: $email");
//     } else {
//         error_log("Failed to send follow-up email to: $email");
//     }
// }
// add_action('send_follow_up_email', 'send_follow_up_email');

// // Notify the admin when a review is submitted
// function notify_admin_on_review_submission($comment_id, $comment_approved, $commentdata) {
//     // Check if it's a WooCommerce product review
//     if (get_post_type($commentdata['comment_post_ID']) == 'product' && $comment_approved == 1) {
//         // Get the product details
//         $product = wc_get_product($commentdata['comment_post_ID']);
//         if (!$product) {
//             error_log("Product not found for comment ID: $comment_id");
//             return;
//         }

//         $product_name = $product->get_name();

//         // Check if the review is submitted via the email link
//         if (isset($_GET['review_order_id'])) {
//             $order_id = intval($_GET['review_order_id']);
//             // Get the admin email
//             $admin_email = get_option('admin_email');
//             $current_time = current_time('mysql'); // Get current date and time

//             // Email subject and message
//             $subject = 'New Product Review Submitted';
//             $message = sprintf(
//                 'A new review has been submitted for %s. You can view the review here: %s' . "\n\n" . 'Submitted on: %s',
//                 $product_name,
//                 get_permalink($commentdata['comment_post_ID']) . '#comment-' . $comment_id,
//                 $current_time
//             );

//             // Send the email to the admin
//             $result = wp_mail($admin_email, $subject, $message);
//             if ($result) {
//                 error_log("Admin notification sent for review ID: $comment_id");
//             } else {
//                 error_log("Failed to send admin notification for review ID: $comment_id");
//             }
//         }
//     }
// }
// add_action('comment_post', 'notify_admin_on_review_submission', 10, 3);

// // Force WP-Cron execution for testing
// if (isset($_GET['run_cron']) && $_GET['run_cron'] == '1') {
//     wp_cron();
//     echo "WP-Cron executed.";
//     exit;
// }

// //Working Code
// // Hook into WooCommerce order status change to "completed"
// add_action('woocommerce_order_status_completed', 'schedule_review_links_email_with_local_time', 10, 1);

// function schedule_review_links_email_with_local_time($order_id) {
//     // Check if the order exists
//     if (!$order_id) {
//         error_log("No order ID provided for scheduling email.");
//         return;
//     }

//     // Log for debugging
//     error_log("Attempting to schedule review links email for order ID: " . $order_id);

//     // Calculate the time difference in seconds (IST is UTC+5:30)
//     $time_difference = 5 * 60 * 60 + 30 * 60; // 5 hours and 30 minutes

//     // Get the current server time
//     $server_time = time();

//     // Convert the desired execution time to server time
//     $execution_time = $server_time + 60 - $time_difference; // 1 minute after local time

//     // Ensure execution time is in the future
//     if ($execution_time < $server_time) {
//         error_log("Execution time is in the past. Adjusting time.");
//         $execution_time = $server_time + 60; // Set to 1 minute in the future
//     }

//     // Schedule the email to be sent after the adjusted time
//     if (!wp_next_scheduled('send_review_links_email', array($order_id))) {
//         wp_schedule_single_event($execution_time, 'send_review_links_email', array($order_id));
//         error_log("Scheduled review links email for order ID: " . $order_id . " at " . date('Y-m-d H:i:s', $execution_time) . " server time.");
//     } else {
//         error_log("Email for order ID: " . $order_id . " is already scheduled.");
//     }
// }

// // Custom function to send the review links email
// add_action('send_review_links_email', 'send_review_links_email', 10, 1);

// function send_review_links_email($order_id) {
//     // Log for debugging
//     error_log("Sending review links email for order ID: " . $order_id);

//     // Get the order
//     $order = wc_get_order($order_id);
//     if (!$order) {
//         error_log("Order ID: " . $order_id . " not found.");
//         return; // Exit if the order is not found
//     }

//     $email = $order->get_billing_email(); // Get customer email
//     $items = $order->get_items(); // Get order items

//     // Initialize email content
//     $subject = 'Thank you for your purchase! Leave a review';
//     $message = '<p>Thank you for your purchase! We would love to hear your thoughts on the products you bought:</p><ul>';

//     // Generate review links for each product
//     foreach ($items as $item) {
//         $product_id = $item->get_product_id();
//         $product_name = $item->get_name();
//         $product_url = get_permalink($product_id);
//         $review_link = $product_url . '#reviews'; // Link to reviews section

//         $message .= "<li><a href='{$review_link}'>{$product_name}</a></li>";
//     }

//     $message .= '</ul><p>Thank you for your feedback!</p>';

//     // Set headers for HTML email
//     $headers = array('Content-Type: text/html; charset=UTF-8');

//     // Send email
//     wp_mail($email, $subject, $message, $headers);

//     error_log("Review links email sent for order ID: " . $order_id);
// }
//Working code close

// // Hook into WooCommerce order status change to "completed"
// add_action('woocommerce_order_status_completed', 'schedule_review_links_email_with_local_time', 10, 1);

// function schedule_review_links_email_with_local_time($order_id) {
//     // Check if the order exists
//     if (!$order_id) {
//         error_log("No order ID provided for scheduling email.");
//         return;
//     }

//     // Log for debugging
//     error_log("Attempting to schedule review links email for order ID: " . $order_id);

//     // Calculate the time difference in seconds (IST is UTC+5:30)
//     $time_difference = 5 * 60 * 60 + 30 * 60; // 5 hours and 30 minutes

//     // Get the current server time
//     $server_time = time();

//     // Convert the desired execution time to server time
//     $execution_time = $server_time + 60 - $time_difference; // 1 minute after local time

//     // Ensure execution time is in the future
//     if ($execution_time < $server_time) {
//         error_log("Execution time is in the past. Adjusting time.");
//         $execution_time = $server_time + 60; // Set to 1 minute in the future
//     }

//     // Schedule the email to be sent after the adjusted time
//     if (!wp_next_scheduled('send_review_links_email', array($order_id))) {
//         wp_schedule_single_event($execution_time, 'send_review_links_email', array($order_id));
//         error_log("Scheduled review links email for order ID: " . $order_id . " at " . date('Y-m-d H:i:s', $execution_time) . " server time.");
//     } else {
//         error_log("Email for order ID: " . $order_id . " is already scheduled.");
//     }
// }

// // Custom function to send the review links email
// add_action('send_review_links_email', 'send_review_links_email', 10, 1);

// function send_review_links_email($order_id) {
//     // Log for debugging
//     error_log("Sending review links email for order ID: " . $order_id);

//     // Get the order
//     $order = wc_get_order($order_id);
//     if (!$order) {
//         error_log("Order ID: " . $order_id . " not found.");
//         return; // Exit if the order is not found
//     }

//     $email = $order->get_billing_email(); // Get customer email
//     $items = $order->get_items(); // Get order items

//     // Initialize email content
//     $subject = 'Thank you for your purchase! Leave a review';
//     $message = '<p>Thank you for your purchase! We would love to hear your thoughts on the products you bought:</p><ul>';

//     // Generate review links for each product
//     foreach ($items as $item) {
//         $product_id = $item->get_product_id();
//         $product_name = $item->get_name();
//         $product_url = get_permalink($product_id);
//         $review_link = $product_url . '#reviews'; // Link to reviews section

//         $message .= "<li><a href='{$review_link}'>{$product_name}</a></li>";
//     }

//     $message .= '</ul><p>Thank you for your feedback!</p>';

//     // Set headers for HTML email
//     $headers = array('Content-Type: text/html; charset=UTF-8');

//     // Send email
//     wp_mail($email, $subject, $message, $headers);

//     error_log("Review links email sent for order ID: " . $order_id);
// }


// Was working on this code Hook into WooCommerce order status change to "completed"




    // $message = '<p>Thank you for your purchase! We would love to hear your thoughts on the products you bought:</p><ul>';

    // // Generate review links for each product
    // foreach ($items as $item) {
    //     $product_id = $item->get_product_id();
    //     $product_name = $item->get_name();
    //     $product_url = get_permalink($product_id);
    //     $review_link = $product_url . '#reviews'; // Link to reviews section

    //     $message .= "<li><a href='{$review_link}'>{$product_name}</a></li>";
    // }

    // $message .= '</ul><p>Thank you for your feedback!</p>';



    
//==========================================
//Send review email along with Completed email code.

// // Hook into WooCommerce order status change to "completed"
// add_action('woocommerce_order_status_completed', 'send_review_links_email_on_order_complete', 10, 1);

// function send_review_links_email_on_order_complete($order_id) {
//     // Check if the order exists
//     if (!$order_id) {
//         error_log("No order ID provided for sending email.");
//         return;
//     }

//     // Call the function to send review links email immediately
//     send_review_links_email_function($order_id);
// }

// add_action('send_review_links_email', 'send_review_links_email_function', 10, 1);

// function send_review_links_email_function($order_id) {
//     error_log("Starting send_review_links_email function for order ID: " . $order_id);

//     // Get the current server time
//     $server_time = time();
//     $time_difference = 5 * 60 * 60 + 30 * 60; // 5 hours and 30 minutes
//     $local_time = $server_time + $time_difference; // Convert server time to local time

//     // Log for debugging
//     error_log("Cron Job Run Time (Server Time): " . date('Y-m-d H:i:s', $server_time) . " UTC");
//     error_log("Cron Job Run Time (Local Time): " . date('Y-m-d H:i:s', $local_time) . " IST");

//     // Get the order
//     $order = wc_get_order($order_id);
//     if (!$order) {
//         error_log("Order ID: " . $order_id . " not found.");
//         return; // Exit if the order is not found
//     }

//     $email = $order->get_billing_email(); // Get customer email
//     $items = $order->get_items(); // Get order items

//     // Initialize email content
//     $subject = 'Thank you for your purchase! Leave a review';
//     $message = '<p>Thank you for your purchase! We would love to hear your thoughts on the products you bought:</p><ul>';

//     // Generate review links for each product
//     foreach ($items as $item) {
//         $product_id = $item->get_product_id();
//         $product_name = $item->get_name();
//         $product_url = get_permalink($product_id);
//         $review_link = $product_url . '#reviews'; // Link to reviews section

//         $message .= "<li><a href='{$review_link}'>{$product_name}</a></li>";
//     }

//     $message .= '</ul><p>Thank you for your feedback!</p>';

//     // Set headers for HTML email
//     $headers = array('Content-Type: text/html; charset=UTF-8');

//     // Send email
//     if (wp_mail($email, $subject, $message, $headers)) {
//         error_log("Review links email sent successfully for order ID: " . $order_id);
//     } else {
//         error_log("Failed to send review links email for order ID: " . $order_id);
//     }
// }



/* pree customisation from fs docs */




/* Code to solve the notices and issues */

add_action('plugins_loaded', function () {
    if (class_exists('CED_rnx_admin_interface')) {
        $instance = new CED_rnx_admin_interface();

        // Manually assign the property if it doesn't exist
        if (!property_exists($instance, 'id')) {
            $instance->id = 'custom_value'; // Set it to whatever value is appropriate
        }
    }
});


function add_cors_headers()
{
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: https://studds-revamp.postyoulike.com");
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: *");
    }
}
add_action('init', 'add_cors_headers');



/* Code to solve the notices and issues end */

/* */

function mailtrap($phpmailer)
{
    $phpmailer->isSMTP();
    $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 2525;
    $phpmailer->Username = '631e41b803eb2a';
    $phpmailer->Password = '01c7117aaef6b1';
}
add_action('phpmailer_init', 'mailtrap');

/* Theme settings code */
if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title'  => 'Theme Settings',
        'menu_title'  => 'Theme Settings',
        'menu_slug'   => 'theme-settings',
        'capability'  => 'edit_posts',
        'redirect'    => false,
        'position'    => 61,
        'icon_url'    => 'dashicons-admin-generic',
    ));
    // Footer subpage
    acf_add_options_sub_page(array(
        'page_title'  => 'Footer Settings',
        'menu_title'  => 'Footer',
        'parent_slug' => 'theme-settings',
    ));
}

/* Theme settings code */

/* Overriding the theme-functions.php file's function to change the functionality for breadcrumbs */

if (!function_exists('boxshop_breadcrumbs')) {
    function boxshop_breadcrumbs()
    {
        global $boxshop_theme_options;

        $delimiter_char = '&rsaquo;';
        if (class_exists('WooCommerce')) {
            if (function_exists('woocommerce_breadcrumb') && function_exists('is_woocommerce') && is_woocommerce()) {
                woocommerce_breadcrumb(array('wrap_before' => '<div class="breadcrumbs"><div class="breadcrumbs-container">', 'delimiter' => '<span>' . $delimiter_char . '</span>', 'wrap_after' => '</div></div>'));
                return;
            }
        }

        if (function_exists('bbp_breadcrumb') && function_exists('is_bbpress') && is_bbpress()) {
            $args = array(
                'before'             => '<div class="breadcrumbs"><div class="breadcrumbs-container">',
                'after'             => '</div></div>',
                'sep'                 => $delimiter_char,
                'sep_before'         => '<span class="brn_arrow">',
                'sep_after'         => '</span>',
                'current_before'     => '<span class="current">',
                'current_after'     => '</span>'
            );

            bbp_breadcrumb($args);
            /* Remove bbpress breadcrumbs */
            add_filter('bbp_no_breadcrumb', '__return_true', 999);
            return;
        }

        $allowed_html = array(
            'a'        => array('href' => array(), 'title' => array()),
            'span'    => array('class' => array()),
            'div'    => array('class' => array())
        );
        $output = '';

        $delimiter = '<span class="brn_arrow">' . $delimiter_char . '</span>';

        $front_id = get_option('page_on_front');
        if (!empty($front_id)) {
            $home = get_the_title($front_id);
        } else {
            $home = esc_html__('Home', 'boxshop');
        }
        $ar_title = array(
            'search'         => esc_html__('Search results for ', 'boxshop'),
            '404'             => esc_html__('Error 404', 'boxshop'),
            'tagged'         => esc_html__('Tagged ', 'boxshop'),
            'author'         => esc_html__('Articles posted by ', 'boxshop'),
            'page'         => esc_html__('Page', 'boxshop'),
            'portfolio'     => esc_html__('Portfolio', 'boxshop')
        );

        $before = '<span class="current">'; /* tag before the current crumb */
        $after = '</span>'; /* tag after the current crumb */
        global $wp_rewrite;
        $rewriteUrl = $wp_rewrite->using_permalinks();
        if ((!is_home() && !is_front_page()) || (is_paged() && !is_home())) {

            $output .= '<div class="breadcrumbs"><div class="breadcrumbs-container">';

            global $post;
            $homeLink = esc_url(home_url('/'));
            $output .= '<a href="' . $homeLink . '">' . $home . '</a> / ';

            if (is_category()) {
                global $wp_query;
                $cat_obj = $wp_query->get_queried_object();
                $thisCat = $cat_obj->term_id;
                $thisCat = get_category($thisCat);
                $parentCat = get_category($thisCat->parent);
                if ($thisCat->parent != 0) {
                    $output .= get_category_parents($parentCat, true, ' ' . $delimiter . ' ');
                }
                $output .= $before . single_cat_title('', false) . $after;
            } elseif (is_search()) {
                $output .= $before . $ar_title['search'] . '"' . get_search_query() . '"' . $after;
            } elseif (is_day()) {
                $output .= '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
                $output .= '<a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
                $output .= $before . get_the_time('d') . $after;
            } elseif (is_month()) {
                $output .= '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
                $output .= $before . get_the_time('F') . $after;
            } elseif (is_year()) {
                $output .= $before . get_the_time('Y') . $after;
            } elseif (is_single() && !is_attachment()) {
                if (get_post_type() != 'post') {
                    $post_type = get_post_type_object(get_post_type());
                    $slug = $post_type->rewrite;
                    $post_type_name = $post_type->labels->singular_name;
                    if (strcmp('Portfolio Item', $post_type->labels->singular_name) == 0) {
                        $post_type_name = $ar_title['portfolio'];
                    }
                    if ($rewriteUrl) {
                        $output .= '<a href="' . $homeLink . $slug['slug'] . '/">' . $post_type_name . '</a> ' . $delimiter . ' ';
                    } else {
                        $output .= '<a href="' . $homeLink . '?post_type=' . get_post_type() . '">' . $post_type_name . '</a> ' . $delimiter . ' ';
                    }

                    $output .= $before . get_the_title() . $after;
                } else {
                    $cat = get_the_category();
                    $cat = $cat[0];
                    if (is_single() || get_post_type() === 'post') {
                        $output .= '<a href="' . $homeLink . 'blog' . '/">Blog</a> / ';
                    } else {
                        $output .= get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                    }
                    $output .= $before . get_the_title() . $after;
                }
            } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                $post_type_name = $post_type->labels->singular_name;
                if (strcmp('Portfolio Item', $post_type->labels->singular_name) == 0) {
                    $post_type_name = $ar_title['portfolio'];
                }
                if (is_tag()) {
                    $output .= $before . $ar_title['tagged'] . '"' . single_tag_title('', false) . '"' . $after;
                } elseif (is_taxonomy_hierarchical(get_query_var('taxonomy'))) {
                    if ($rewriteUrl) {
                        $output .= '<a href="' . $homeLink . $slug['slug'] . '/">' . $post_type_name . '</a> ' . $delimiter . ' ';
                    } else {
                        $output .= '<a href="' . $homeLink . '?post_type=' . get_post_type() . '">' . $post_type_name . '</a> ' . $delimiter . ' ';
                    }

                    $curTaxanomy = get_query_var('taxonomy');
                    $curTerm = get_query_var('term');
                    $termNow = get_term_by('name', $curTerm, $curTaxanomy);
                    $pushPrintArr = array();
                    if ($termNow !== false) {
                        while ((int)$termNow->parent != 0) {
                            $parentTerm = get_term((int)$termNow->parent, get_query_var('taxonomy'));
                            array_push($pushPrintArr, '<a href="' . get_term_link((int)$parentTerm->term_id, $curTaxanomy) . '">' . $parentTerm->name . '</a> ' . $delimiter . ' ');
                            $curTerm = $parentTerm->name;
                            $termNow = get_term_by('name', $curTerm, $curTaxanomy);
                        }
                    }
                    $pushPrintArr = array_reverse($pushPrintArr);
                    array_push($pushPrintArr, $before  . get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'))->name  . $after);
                    $output .= implode($pushPrintArr);
                } else {
                    $output .= $before . $post_type_name . $after;
                }
            } elseif (is_attachment()) {
                if ((int)$post->post_parent > 0) {
                    $parent = get_post($post->post_parent);
                    $cat = get_the_category($parent->ID);
                    if (count($cat) > 0) {
                        $cat = $cat[0];
                        $output .= get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                    }
                    $output .= '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
                }
                $output .= $before . get_the_title() . $after;
            } elseif (is_page() && !$post->post_parent) {
                $output .= $before . get_the_title() . $after;
            } elseif (is_page() && $post->post_parent) {
                $parent_id  = $post->post_parent;
                $breadcrumbs = array();
                while ($parent_id) {
                    $page = get_post($parent_id);
                    $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                    $parent_id  = $page->post_parent;
                }
                $breadcrumbs = array_reverse($breadcrumbs);
                foreach ($breadcrumbs as $crumb) {
                    $output .= $crumb . ' ' . $delimiter . ' ';
                }
                $output .= $before . get_the_title() . $after;
            } elseif (is_tag()) {
                $output .= $before . $ar_title['tagged'] . '"' . single_tag_title('', false) . '"' . $after;
            } elseif (is_author()) {
                global $author;
                $userdata = get_userdata($author);
                $output .= $before . $ar_title['author'] . $userdata->display_name . $after;
            } elseif (is_404()) {
                $output .= $before . $ar_title['404'] . $after;
            }

            if (get_query_var('paged')) {
                if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_page_template() ||  is_post_type_archive() || is_archive()) {
                    $output .= $before . ' (';
                }
                $output .= $ar_title['page'] . ' ' . get_query_var('paged');
                if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_page_template() ||  is_post_type_archive() || is_archive()) {
                    $output .= ')' . $after;
                }
            } else {
                if (get_query_var('page')) {
                    if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_page_template() ||  is_post_type_archive() || is_archive()) {
                        $output .= $before . ' (';
                    }
                    $output .= $ar_title['page'] . ' ' . get_query_var('page');
                    if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_page_template() ||  is_post_type_archive() || is_archive()) {
                        $output .= ')' . $after;
                    }
                }
            }
            $output .= '</div></div>';
        }

        if (is_home() && !is_front_page()) {
            $output .= '<div class="breadcrumbs"><div class="breadcrumbs-container">';
            $homeLink = esc_url(home_url('/'));
            $output .= '<a href="' . $homeLink . '">Home</a> / Blogs';
            $output .= '</div></div>';
        }


        echo wp_kses($output, $allowed_html);

        wp_reset_postdata();
    }
}

if (!function_exists('boxshop_breadcrumbs_title')) {
    function boxshop_breadcrumbs_title($show_breadcrumb = false, $show_page_title = false, $page_title = '', $extra_class_title = '')
    {
        global $boxshop_theme_options;



        $breadcrumb_bg = '';
        if (is_single() || get_post_type() === 'post') {
            $extra_class = 'breadcrumb-v2';
        } else {
            $extra_class = 'breadcrumb-' . $boxshop_theme_options['ts_breadcrumb_layout'];
        }

        if ($boxshop_theme_options['ts_enable_breadcrumb_background_image'] && $boxshop_theme_options['ts_breadcrumb_layout'] != 'v2') {
            if ($boxshop_theme_options['ts_bg_breadcrumbs'] == '') {
                $breadcrumb_bg = get_template_directory_uri() . '/images/bg_breadcrumb_' . $boxshop_theme_options['ts_breadcrumb_layout'] . '.jpg';
            } else {
                $breadcrumb_bg = $boxshop_theme_options['ts_bg_breadcrumbs'];
            }
        }

        $style = '';
        if ($breadcrumb_bg != '') {
            if (isset($boxshop_theme_options['ts_breadcrumb_bg_parallax']) && $boxshop_theme_options['ts_breadcrumb_bg_parallax']) {
                $extra_class .= ' ts-breadcrumb-parallax';
            }
        }

        echo '<div class="breadcrumb-title-wrapper ' . $extra_class . '" ' . $style . '><div class="breadcrumb-content"><div class="breadcrumb-title">';
        if ($show_page_title) {
            if (is_single() || get_post_type() === 'post' || is_page('faq') || is_page('event')  || is_page('media') || is_page('gallery')) {
                echo "";
            } else {
                echo '<h1 class="heading-title page-title entry-title ' . $extra_class_title . '">' . $page_title . '</h1>';
            }
        }
        if ($show_breadcrumb) {
            boxshop_breadcrumbs();
        }
        echo '</div></div></div>';
    }
}
/* Overriding the theme-functions.php file's function to change the functionality for breadcrumbs end */


/* Override the header icons code */
if (!function_exists('boxshop_tiny_account')) {
    function boxshop_tiny_account()
    {
        $login_url = '#';
        $register_url = '#';
        $profile_url = '#';
        $logout_url = wp_logout_url(get_permalink());

        if (class_exists('WooCommerce')) {
            $myaccount_page_id = get_option('woocommerce_myaccount_page_id');
            if ($myaccount_page_id) {
                $login_url = get_permalink($myaccount_page_id);
                $register_url = $login_url;
                $profile_url = $login_url;
            }
        } else {
            $login_url = wp_login_url();
            $register_url = wp_registration_url();
            $profile_url = admin_url('profile.php');
        }

        $_user_logged = is_user_logged_in();
        ob_start();

    ?>
        <div class="ts-tiny-account-wrapper">
            <div class="account-control">
                <img src="<?php echo get_stylesheet_directory_uri() . '/assets/img/header_user_icon.svg';  ?>" alt="User">
                <?php if (!$_user_logged): ?>
                    <a class="login" href="<?php echo esc_url($login_url); ?>" title="<?php esc_attr_e('Login', 'boxshop'); ?>"><span><?php esc_html_e('Login', 'boxshop'); ?></span></a>
                    /
                    <a class="sign-up" href="https://studds-revamp.postyoulike.com/sign-up/" title="<?php esc_attr_e('Create New Account', 'boxshop'); ?>"><span><?php esc_html_e('Sign up', 'boxshop'); ?></span></a>
                <?php else: ?>
                    <a class="my-account" href="<?php echo esc_url($profile_url); ?>" title="<?php esc_attr_e('My Account', 'boxshop'); ?>"><span><?php esc_html_e('My Account', 'boxshop'); ?></span></a> /
                    <a class="log-out" href="<?php echo esc_url($logout_url); ?>" title="<?php esc_attr_e('Logout', 'boxshop'); ?>"><span><?php esc_html_e('Logout', 'boxshop'); ?></span></a>
                <?php endif; ?>
            </div>
            <?php if (!$_user_logged): ?>
                <div class="account-dropdown-form dropdown-container">
                    <div class="form-content">
                        <?php wp_login_form(array('form_id' => 'ts-login-form', 'remember' => false, 'label_username' => __('Username', 'boxshop'), 'label_log_in' => __('Login', 'boxshop'))); ?>

                        <p class="forgot-pass"><a href="<?php echo esc_url(wp_lostpassword_url()); ?>" title="<?php esc_attr_e('Forgot Your Password?', 'boxshop'); ?>"><?php esc_html_e('Forgot Your Password?', 'boxshop'); ?></a></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    <?php
        return ob_get_clean();
    }
}


if (!function_exists('boxshop_tiny_cart')) {
    function boxshop_tiny_cart()
    {
        if (!class_exists('WooCommerce')) {
            return '';
        }
        $cart_empty = WC()->cart->is_empty();
        $cart_url = wc_get_cart_url();
        $checkout_url = wc_get_checkout_url();
        $cart_number = WC()->cart->get_cart_contents_count();
        ob_start();
    ?>
        <div class="ts-tiny-cart-wrapper">
            <a class="cart-control" href="<?php echo esc_url($cart_url); ?>" title="<?php esc_attr_e('View your shopping bag', 'boxshop'); ?>">
                <img src="<?php echo get_stylesheet_directory_uri() . '/assets/img/header_shopping_cart.svg'; ?>" alt="Shopping Cart">
                <span class="cart-number"><?php echo esc_html($cart_number) . ' ' . _n('item', 'items', $cart_number, 'boxshop') ?></span>
                <span class="hyphen">-</span>
                <span class="cart-total"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
            </a>
            <span class="cart-drop-icon drop-icon"></span>
            <div class="cart-dropdown-form dropdown-container">
                <div class="form-content">
                    <?php if ($cart_empty): ?>
                        <label><?php esc_html_e('Your shopping cart is empty', 'boxshop'); ?></label>
                    <?php else: ?>
                        <ul class="cart_list">
                            <?php
                            $cart = WC()->cart->get_cart();
                            foreach ($cart as $cart_item_key => $cart_item):
                                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                                if (!($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key))) {
                                    continue;
                                }

                                $product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                                $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                            ?>
                                <li>
                                    <a href="<?php echo esc_url($product_permalink); ?>">
                                        <?php echo apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key); ?>
                                    </a>
                                    <div class="cart-item-wrapper">
                                        <h3 class="product-name">
                                            <a href="<?php echo esc_url($product_permalink); ?>">
                                                <?php echo apply_filters('woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key); ?>
                                            </a>
                                        </h3>
                                        <?php echo apply_filters('woocommerce_widget_cart_item_quantity', '<span class="quantity">' . $cart_item['quantity'] . '</span> ', $cart_item, $cart_item_key); ?>
                                        <?php echo apply_filters('woocommerce_widget_cart_item_quantity', '<span class="price"><span class="icon"> x </span> ' . $product_price . '</span>', $cart_item, $cart_item_key); ?>
                                        <?php echo apply_filters('woocommerce_cart_item_remove_link', sprintf('<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-cart_item_key="%s">&times;</a>', esc_url(wc_get_cart_remove_url($cart_item_key)), esc_html__('Remove this item', 'boxshop'), $cart_item_key), $cart_item_key); ?>
                                    </div>
                                </li>

                            <?php endforeach; ?>
                        </ul>
                        <div class="dropdown-footer">
                            <div class="total"><span class="total-title"><?php esc_html_e('Subtotal :', 'boxshop'); ?></span><?php echo WC()->cart->get_cart_subtotal(); ?> </div>

                            <a href="<?php echo esc_url($cart_url); ?>" class="button button-border-primary view-cart"><?php esc_html_e('View cart', 'boxshop'); ?></a>
                            <a href="<?php echo esc_url($checkout_url); ?>" class="button checkout button-border-secondary"><?php esc_html_e('Checkout', 'boxshop'); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<?php
        return ob_get_clean();
    }
}
/* Override the header icons code */



/* Override for search bar added */
function boxshop_custom_search_placeholder($form)
{
    $form = str_replace('placeholder="Search …"', 'placeholder="Search blog posts..."', $form);
    return $form;
}
add_filter('get_search_form', 'boxshop_custom_search_placeholder');

add_action('pre_get_posts', function ($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (isset($_GET['search'])) {
            $query->is_search = true; // Tell WP it's a search!
            $query->set('s', sanitize_text_field($_GET['search']));
        }
        if (isset($_GET['post_type']) && $_GET['post_type'] === 'post') {
            $query->set('post_type', 'post');
        }
    }
});

/* Override for search bar added end*/




?>
<?php

// // remove_action('woocommerce_before_shop_loop_item_title', 'boxshop_template_loop_product_thumbnail', 10);
// add_action('woocommerce_before_shop_loop_item_title', 'child_boxshop_template_loop_product_thumbnail', 10);



function child_boxshop_template_loop_product_thumbnail()
{
    global $product, $boxshop_theme_options;
    $lazy_load = isset($boxshop_theme_options['ts_prod_lazy_load']) && $boxshop_theme_options['ts_prod_lazy_load'] && !(defined('DOING_AJAX') && DOING_AJAX);
    $placeholder_img_src = isset($boxshop_theme_options['ts_prod_placeholder_img']) ? $boxshop_theme_options['ts_prod_placeholder_img'] : wc_placeholder_img_src();

    if (defined('YITH_INFS') && (is_shop() || is_product_taxonomy())) { /* Compatible with YITH Infinite Scrolling */
        $lazy_load = false;
    }

    $prod_galleries = $product->get_gallery_image_ids();

    $image_size = apply_filters('boxshop_loop_product_thumbnail', 'woocommerce_thumbnail');

    $dimensions = wc_get_image_size($image_size);

    $has_back_image = (isset($boxshop_theme_options['ts_effect_product']) && (int)$boxshop_theme_options['ts_effect_product'] == 0) ? false : true;

    if (!is_array($prod_galleries) || (is_array($prod_galleries) && count($prod_galleries) == 0)) {
        $has_back_image = false;
    }

    if (wp_is_mobile()) {
        $has_back_image = false;
    }

    // define thumbnail slider variables
    $thumbnail_slider = apply_filters('boxshop_loop_product_thumbnail_slider', false);
    $thumbnail_slider_number = apply_filters('boxshop_loop_product_thumbnail_slider_number', 3);
    $thumbnail_slider_variation = apply_filters('boxshop_loop_product_thumbnail_slider_variation', false);
    $thumbnail_slider_variation_color = apply_filters('boxshop_loop_product_thumbnail_slider_variation_color', false);

    $show_main_thumbnail = true;
    $variable_prices = '';
    $dots_html = array();

    if ($thumbnail_slider) {
        $has_back_image = false;
        // load variation
        if ($thumbnail_slider_variation && $product->get_type() == 'variable') {
            $children = $product->get_children();
            if (is_array($children) && count($children) > 0) {
                $show_main_thumbnail = false;
                $prod_galleries = array();
                $added_colors = array(); // prevent duplicate color in variations
                $count = 0;
                foreach ($children as $children_id) {
                    $accept_child = true;

                    if ($thumbnail_slider_variation_color) {
                        $variation_attributes = wc_get_product_variation_attributes($children_id);
                        $attribute_color = wc_attribute_taxonomy_name('color'); // pa_color
                        $attribute_color_name = wc_variation_attribute_name($attribute_color); // attribute_pa_color
                        if (taxonomy_exists($attribute_color)) {
                            if (empty($color_terms)) { // Prevent load list of colors many times
                                $color_terms = wc_get_product_terms($product->get_id(), $attribute_color, array('fields' => 'all'));
                                $color_term_ids = wp_list_pluck($color_terms, 'term_id');
                                $color_term_slugs = wp_list_pluck($color_terms, 'slug');
                            }
                            foreach ($variation_attributes as $attribute_name => $attribute_value) {
                                if ($attribute_name == $attribute_color_name) {

                                    if (in_array($attribute_value, $added_colors)) {
                                        $accept_child = false;
                                        break;
                                    }

                                    $term_id = 0;
                                    $found_slug = array_search($attribute_value, $color_term_slugs);
                                    if ($found_slug !== false) {
                                        $term_id = $color_term_ids[$found_slug];
                                    }

                                    if ($term_id !== false && absint($term_id) > 0) {
                                        $color_datas = get_term_meta($term_id, 'ts_product_color_config', true);
                                        if (strlen($color_datas) > 0) {
                                            $color_datas = unserialize($color_datas);
                                        } else {
                                            $color_datas = array(
                                                'ts_color_color'     => "#ffffff",
                                                'ts_color_image'     => 0
                                            );
                                        }
                                        $color_datas['ts_color_image'] = absint($color_datas['ts_color_image']);
                                        if ($color_datas['ts_color_image'] > 0) {
                                            $dots_html[] = '<div class="owl-dot color-image"><span>' . wp_get_attachment_image($color_datas['ts_color_image'], 'boxshop_prod_color_thumb', true, array('alt' => $attribute_value)) . '</span></div>';
                                        } else {
                                            $dots_html[] = '<div class="owl-dot color"><span style="background-color: ' . $color_datas['ts_color_color'] . '"></span></div>';
                                        }
                                    } else {
                                        $dots_html[] = '<div class="owl-dot"><span></span></div>';
                                    }

                                    $added_colors[] = $attribute_value;
                                    break;
                                }
                            }
                        }
                    }

                    if ($accept_child) {
                        $prod_galleries[] = get_post_meta($children_id, '_thumbnail_id', true);
                        $variation = wc_get_product($children_id);
                        $variable_prices .= '<span class="price">' . $variation->get_price_html() . '</span>';

                        $count++;
                        if ($count == $thumbnail_slider_number) {
                            break;
                        }
                    }
                }
            }
        }

        if (count($prod_galleries) == 0) {
            $thumbnail_slider = false;
        }
    }

    if ($show_main_thumbnail) {
        $thumbnail_slider_number--;
    }

    $classes = array();
    $classes[] = $has_back_image ? 'has-back-image' : 'no-back-image';
    $classes[] = $thumbnail_slider ? 'slider loading' : '';

    if ($variable_prices) {
        echo '<span class="variable-prices hidden">' . $variable_prices . '</span>';
    }

    echo '<figure class="' . implode(' ', $classes) . '">';
    if (!$lazy_load) {
        if ($show_main_thumbnail) {
            echo woocommerce_get_product_thumbnail($image_size);
        }

        if ($has_back_image) {
            echo wp_get_attachment_image($prod_galleries[0], $image_size, 0, array('class' => 'product-image-back'));
        }

        if ($thumbnail_slider) {
            for ($i = 0; $i < $thumbnail_slider_number; $i++) {
                if (isset($prod_galleries[$i])) {
                    $image_attr = array();
                    if (isset($dots_html[$i])) {
                        $image_attr = array('data-dot' => str_replace('"', '\'', $dots_html[$i]));
                    }
                    echo wp_get_attachment_image($prod_galleries[$i], $image_size, false, $image_attr);
                }
            }
        }
    } else {
        if ($show_main_thumbnail) {
            $front_img_src = '';
            $alt = '';
            if (has_post_thumbnail($product->get_id())) {
                $post_thumbnail_id = get_post_thumbnail_id($product->get_id());
                $image_obj = wp_get_attachment_image_src($post_thumbnail_id, $image_size, 0);
                if (isset($image_obj[0])) {
                    $front_img_src = $image_obj[0];
                }
                $alt = trim(strip_tags(get_post_meta($post_thumbnail_id, '_wp_attachment_image_alt', true)));
            } else if (wc_placeholder_img_src()) {
                $front_img_src = wc_placeholder_img_src();
            }

            echo '<img src="' . esc_url($placeholder_img_src) . '" data-src="' . esc_url($front_img_src) . '" loading="lazy" class="attachment-shop_catalog wp-post-image ts-lazy-load" alt="' . esc_attr($alt) . '" width="' . $dimensions['width'] . '" height="' . $dimensions['height'] . '" />';
        }

        if ($has_back_image) {
            $back_img_src = '';
            $alt = '';
            $image_obj = wp_get_attachment_image_src($prod_galleries[0], $image_size, 0);
            if (isset($image_obj[0])) {
                $back_img_src = $image_obj[0];
                $alt = trim(strip_tags(get_post_meta($prod_galleries[0], '_wp_attachment_image_alt', true)));
            } else if (wc_placeholder_img_src()) {
                $back_img_src = wc_placeholder_img_src();
            }

            echo '<img src="' . esc_url($placeholder_img_src) . '" data-src="' . esc_url($back_img_src) . '" loading="lazy" class="product-image-back ts-lazy-load" alt="' . esc_attr($alt) . '" width="' . $dimensions['width'] . '" height="' . $dimensions['height'] . '" />';
        }

        if ($thumbnail_slider) {
            for ($i = 0; $i < $thumbnail_slider_number; $i++) {
                if (isset($prod_galleries[$i])) {
                    $img_src = '';
                    $alt = '';
                    $image_obj = wp_get_attachment_image_src($prod_galleries[$i], $image_size, 0);
                    if (isset($image_obj[0])) {
                        $img_src = $image_obj[0];
                        $alt = trim(strip_tags(get_post_meta($prod_galleries[$i], '_wp_attachment_image_alt', true)));
                    } else if (wc_placeholder_img_src()) {
                        $img_src = wc_placeholder_img_src();
                    }

                    $data_dot = '';
                    if (isset($dots_html[$i])) {
                        $data_dot = 'data-dot="' . str_replace('"', '\'', $dots_html[$i]) . '"';
                    }

                    echo '<img src="' . esc_url($placeholder_img_src) . '" data-src="' . esc_url($img_src) . '" ' . $data_dot . ' class="product-image-back ts-lazy-load" alt="' . esc_attr($alt) . '" width="' . $dimensions['width'] . '" height="' . $dimensions['height'] . '" />';
                }
            }
        }
    }
    echo '</figure>';
}


// function override_ts_product_categories_widget()
// {
//     unregister_widget('TS_Product_Categories_Widget');
// }
// add_action('widgets_init', 'override_ts_product_categories_widget', 11); // priority 11 or higher


// Remove default related products section
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

// require_once 'modules/included-files.php';

function custom_woocommerce_products_per_page($per_page)
{
    return 12; // Change to your desired number of products per page
}
add_filter('loop_shop_per_page', 'custom_woocommerce_products_per_page', 20);

function custom_set_products_per_page($query)
{
    if ($query->is_main_query() && is_post_type_archive('product')) {

        $query->set('posts_per_page', 12); // Set the desired number of products per page
    }
}
add_action('pre_get_posts', 'custom_set_products_per_page');
