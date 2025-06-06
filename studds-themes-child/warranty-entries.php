<?php

add_action('wp_ajax_warranty_activation', 'handle_warranty_form');
add_action('wp_ajax_nopriv_warranty_activation', 'handle_warranty_form'); // For non-logged-in users

function handle_warranty_form() {
    global $wpdb;
    $table_name = $wpdb->prefix . "studds_warranty_registrations";


    // Sanitize input data
    $data = array(
        'model_name' => sanitize_text_field($_POST['model_name']),
        'serial_number' => sanitize_text_field($_POST['serial_number']),
        'first_name' => sanitize_text_field($_POST['first_name']),
        'last_name' => sanitize_text_field($_POST['last_name']),
        'email' => sanitize_email($_POST['email']),
        'contact_number' => sanitize_text_field($_POST['contact_number']),
        'billing_date' => sanitize_text_field($_POST['billing_date']),
        'invoice_number' => sanitize_text_field($_POST['invoice_number']),
        'city' => sanitize_text_field($_POST['city']),
        'state' => sanitize_text_field($_POST['state']),
        'country' => sanitize_text_field($_POST['country']),
        'status' => 'Active',
        'created_at' => current_time('mysql')
    );

    // Handle file upload
    if (!empty($_FILES['invoice_pdf']['name'])) {
        $upload_overrides = array('test_form' => false);

        // Get the file extension
        $file_type = wp_check_filetype($_FILES['invoice_pdf']['name']);
        $allowed_extensions = ['pdf', 'png', 'jpg', 'jpeg', 'ttf'];

        // Check if the file type is in the allowed extensions
        if (!in_array(strtolower($file_type['ext']), $allowed_extensions)) {
            wp_send_json_error('Only PDF, PNG, JPG, JPEG, and TTF files are allowed.');
        }

        // Validate file size (for example, allow a maximum of 5MB)
        if ($_FILES['invoice_pdf']['size'] > 5 * 1024 * 1024) { // 5MB limit
            wp_send_json_error('File size exceeds the allowed limit of 5MB.');
        }

        // Upload the file
        $uploaded_file = wp_handle_upload($_FILES['invoice_pdf'], $upload_overrides);

        if ($uploaded_file && !isset($uploaded_file['error'])) {
            $data['invoice_pdf'] = $uploaded_file['url']; // Store file URL
        } else {
            wp_send_json_error('Error uploading file: ' . $uploaded_file['error']);
        }
    } else {
        wp_send_json_error('No file uploaded.');
    }

    // Handle warranty start and end date calculation
    if (isset($_POST['billing_date']) && !empty($_POST['billing_date'])) {
        $warranty_start_date = sanitize_text_field($_POST['billing_date']);
        $warranty_end_date = date('Y-m-d', strtotime($warranty_start_date . ' +1 year')); // Add one year to the start date

        // Include the calculated warranty dates in the response
        $data['warranty_start_date'] = $warranty_start_date;
        $data['warranty_end_date'] = $warranty_end_date;

        // Prepare the success message response
        $response = array(
            'success' => true,
            'data' => array(
                'warranty_start_date' => $warranty_start_date,
                'warranty_end_date' => $warranty_end_date,
                'message' => 'The warranty is activated.'
            )
        );

        // Insert the data into the database
        $inserted = $wpdb->insert($table_name, $data);

        if ($inserted) {
            wp_send_json($response);
        } else {
            wp_send_json_error('Error activating warranty. Please try again.');
        }
    } else {
        // Return failure if no billing date is provided
        $response = array(
            'success' => false,
            'data' => 'Invalid warranty data. Please try again.'
        );
        
        wp_send_json($response);
    }

    wp_die(); // Always call wp_die() to end AJAX request properly
}


/**
 * Adds a custom admin menu for Warranty Registrations.
 */
function studds_warranty_admin_menu() {
    add_menu_page(
        'Warranty Registrations',
        'Warranty Registrations',
        'manage_options',
        'studds-warranty',
        'studds_warranty_admin_page',
        'dashicons-clipboard',
        20
    );
}

// Hook to add custom menu to WordPress admin
add_action('admin_menu', 'studds_warranty_admin_menu');

/**
 * Renders the Warranty Registrations admin page.
 */
function studds_warranty_admin_page() {
    global $wpdb;

    // Define table name
    $table_name = $wpdb->prefix . "studds_warranty_registrations";

    // Handle search query
    $search_query = '';
    if (!empty($_GET['search'])) {
        
        // Sanitize the search input to prevent SQL injection.
        $search_term = sanitize_text_field($_GET['search']);
        
        // Construct the search query to filter records based on multiple columns.
        $search_query = "WHERE 
            model_name LIKE '%$search_term%' OR 
            invoice_number LIKE '%$search_term%' OR 
            serial_number LIKE '%$search_term%' OR 
            first_name LIKE '%$search_term%' OR 
            last_name LIKE '%$search_term%' OR 
            email LIKE '%$search_term%' OR 
            contact_number LIKE '%$search_term%' OR 
            city LIKE '%$search_term%' OR 
            state LIKE '%$search_term%' OR 
            country LIKE '%$search_term%'";
    }

    // Pagination setup
    $limit = 10; // Entries per page
    
    // Get current page number, default to 1.
    $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($page - 1) * $limit;

    // Get the total number of entries in the database that match the search query.
    $total_entries = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $search_query");

    // Fetch entries from the database based on search and pagination settings.
    $results = $wpdb->get_results("SELECT * FROM $table_name $search_query ORDER BY created_at DESC LIMIT $limit OFFSET $offset");

    // Calculate total pages required for pagination.
    $total_pages = ceil($total_entries / $limit);
?>

<div class="wrap">
    <h1>Warranty Registrations</h1>
    <div style="float:right; margin-bottom:20px">
        <!-- Search Form -->
        <form method="get">
            <input type="hidden" name="page" value="studds-warranty">
            <input type="text" name="search" placeholder="Search" value="<?php echo esc_attr($_GET['search'] ?? ''); ?>">
            <button type="submit" class="button button-primary">Search</button>
        </form>
    </div>
    <br>

    <!-- Warranty Registrations Table -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Model Name</th>
                <th>Invoice Number</th>
                <th>Serial Number</th>
                <th>Billing Date</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>City</th>
                <th>State</th>
                <th>Country</th>
                <th>Warranty Start Date</th>
                <th>Warranty End Date</th>
                <th>Invoice</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Check if there are records available
            if (!empty($results)) : ?>
                <?php foreach ($results as $row) : ?>
                    <tr>
                        <td><?php echo esc_html($row->model_name); ?></td>
                        <td><?php echo esc_html($row->invoice_number); ?></td>
                        <td><?php echo esc_html($row->serial_number); ?></td>
                         <td><?php echo esc_html($row->billing_date); ?></td>
                        <td><?php echo esc_html($row->first_name); ?></td>
                        <td><?php echo esc_html($row->last_name); ?></td>
                        <td><?php echo esc_html($row->email); ?></td>
                        <td><?php echo esc_html($row->contact_number); ?></td>
                       
                        <td><?php echo esc_html($row->city); ?></td>
                        <td><?php echo esc_html($row->state); ?></td>
                        <td><?php echo esc_html($row->country); ?></td>
                        <td><?php echo esc_html($row->warranty_start_date); ?></td>
                        <td><?php echo esc_html($row->warranty_end_date); ?></td>
                        <td><a href="<?php echo esc_url($row->invoice_pdf); ?>" target="_blank">View Invoice</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else :   // If no records found?>
                <tr><td colspan="14">No records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>



<?php
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        
        
        // Generate pagination links
        $pagination = paginate_links(array(
            'base'      => add_query_arg('paged', '%#%'),
            'format'    => '',
            'current'   => $current_page,
            'total'     => $total_pages,
            'type'      => 'array',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
        ));
    ?>
        <?php
        if (!empty($pagination)) : ?>
            <div class="tablenav top">
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo esc_html($total_entries); ?> <?php echo esc_html__('items', 'studds-warranty'); ?></span>
                    <span class="pagination-links">
                        <?php
                        // First Page Link
                        if ($current_page > 1) : ?>
                            <a class="first-page button" href="<?php echo esc_url(add_query_arg('paged', 1)); ?>">
                                <span class="screen-reader-text"><?php _e('First page', 'studds-warranty'); ?></span><span aria-hidden="true">«</span>
                            </a>
                        <?php else : ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                        <?php endif; ?>

                        <?php
                        // Previous Page Link
                        if ($current_page > 1) : ?>
                            <a class="prev-page button" href="<?php echo esc_url(add_query_arg('paged', $current_page - 1)); ?>">
                                <span class="screen-reader-text"><?php _e('Previous page', 'studds-warranty'); ?></span><span aria-hidden="true">‹</span>
                            </a>
                        <?php else : ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                        <?php endif; ?>


                        <span class="paging-input">
                            <label for="current-page-selector" class="screen-reader-text"><?php _e('Current Page', 'studds-warranty'); ?></label>
                            <input class="current-page" id="current-page-selector" type="text" name="paged" value="<?php echo $current_page; ?>" size="4">
                            <span class="tablenav-paging-text"> <?php _e('of', 'studds-warranty'); ?> <span class="total-pages"><?php echo $total_pages; ?></span></span>
                        </span>

                        <?php
                        // Next Page Link
                        if ($current_page < $total_pages) : ?>
                            <a class="next-page button" href="<?php echo esc_url(add_query_arg('paged', $current_page + 1)); ?>">
                                <span class="screen-reader-text"><?php _e('Next page', 'studds-warranty'); ?></span><span aria-hidden="true">›</span>
                            </a>
                        <?php else : ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                        <?php endif; ?>

                        <?php
                        // Last Page Link
                        if ($current_page < $total_pages) : ?>
                            <a class="last-page button" href="<?php echo esc_url(add_query_arg('paged', $total_pages)); ?>">
                                <span class="screen-reader-text"><?php _e('Last page', 'studds-warranty'); ?></span><span aria-hidden="true">»</span>
                            </a>
                        <?php else : ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
                        <?php endif; ?>

                    </span>
                </div>
            </div>
        <?php endif; ?>
</div>

<?php } ?>
