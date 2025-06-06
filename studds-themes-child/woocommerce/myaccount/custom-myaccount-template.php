<?php
/**
 * Template Name: Custom My Account Template
 */

// Add your HTML form code here
function enqueue_woocommerce_styles() {
    wp_enqueue_style('woocommerce-styles', get_template_directory_uri() . '/woocommerce-styles.css');
}
add_action('wp_enqueue_scripts', 'enqueue_woocommerce_styles');
$warranty_id = $serial = isset($_GET['serial']) ? sanitize_text_field($_GET['serial']) : ''; 
?>
<!-- Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<?php 

    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_table'; // Replace 'your_table_name' with your actual table name

    // Prepare SQL query to fetch data based on warranty_id
    $query123 = $wpdb->prepare("SELECT * FROM $table_name WHERE serial_no = %d", $warranty_id);

    // Retrieve data from the database
    $results123 = $wpdb->get_results($query123);

    // Check if data exists
    if ($results123) {
       echo "<h3 style='text-transform: uppercase;font-weight: 800;font-size: 22px;'>Warranty Information</h3>";
        echo '<table>';
            foreach ($results123 as $row123) {
                
                echo '<tr>';
                    echo '<td><b>Serial Number:</b></td>'; // Replace column1 with your actual column name
                    echo '<td>' . esc_html($row123->serial_no) . '</td>'; // Replace column2 with your actual column name
                echo '</tr>';
                echo '<tr>';
                    echo '<td><b>Product category:</b></td>'; // Replace column1 with your actual column name
                    echo '<td>' . esc_html($row123->product_category ) . '</td>'; // Replace column2 with your actual column name
                echo '</tr>';
                echo '<tr>';
                    echo '<td><b>Product Name:</b></td>'; // Replace column1 with your actual column name
                    echo '<td>' . esc_html($row123->product_name ) . '</td>'; // Replace column2 with your actual column name
                echo '</tr>';
                echo '<tr>';
                    echo '<td><b>Customer Name:</b></td>'; // Replace column1 with your actual column name
                    echo '<td>' . esc_html($row123->customer_name ) . '</td>'; // Replace column2 with your actual column name
                echo '</tr>';
                echo '<tr>';
                    echo '<td><b>From where did you bought?:</b></td>'; // Replace column1 with your actual column name
                    echo '<td>' . esc_html($row123->source_name ) . '</td>'; // Replace column2 with your actual column name
                echo '</tr>';
                echo '<tr>';
                    echo '<td><b>Date of purchase:</b></td>'; // Replace column1 with your actual column name
                    echo '<td>' . esc_html($row123->date_of_purchase ) . '</td>'; // Replace column2 with your actual column name
                echo '</tr>';
                echo '<tr>';
                    echo '<td><b>Email Id:</b></td>'; // Replace column1 with your actual column name
                    echo '<td>' . esc_html($row123->email ) . '</td>'; // Replace column2 with your actual column name
                echo '</tr>';
                echo '<tr>';
                    echo '<td><b>Mobile:</b></td>'; // Replace column1 with your actual column name
                    echo '<td>' . esc_html($row123->mobile ) . '</td>'; // Replace column2 with your actual column name
                echo '</tr>';
                echo '<tr>';
                    echo '<td><b>City:</b></td>'; // Replace column1 with your actual column name
                    echo '<td>' . esc_html($row123->city ) . '</td>'; // Replace column2 with your actual column name
                echo '</tr>';
                echo '<tr>';
                    echo '<td><b>State:</td>'; // Replace column1 with your actual column name
                    echo '<td>' . esc_html($row123->state ) . '</td>'; // Replace column2 with your actual column name
                echo '</tr>';
                echo '<tr>';
                    echo '<td><b>Country:</b></td>'; // Replace column1 with your actual column name
                    echo '<td>' . esc_html($row123->country ) . '</td>'; // Replace column2 with your actual column name
                echo '</tr>';
                echo '<tr>';
                    echo '<td><b>Pin Code:</b></td>'; // Replace column1 with your actual column name
                    echo '<td>' . esc_html($row123->zipcode ) . '</td>'; // Replace column2 with your actual column name
                echo '</tr>';
                echo '<tr>';
                    echo '<td><b>Address:</b></td>'; // Replace column1 with your actual column name
                    echo '<td>' . esc_html($row123->address ) . '</td>'; // Replace column2 with your actual column name
                echo '</tr>';
        
    }
    echo '</table>';
    }else{
?>
     <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" class="woocommerce-form woocommerce-form-register register">
        <div class="woocommerce-columns woocommerce-columns--2 columns">
            <?php 
                if ( isset( $_GET['success'] ) && $_GET['success'] == 'true' ) {
                    echo '<div class="success-message">Data submitted successfully!</div>';
                }
            ?>
        <div class="woocommerce-column woocommerce-column--1 col-1">
            <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-first">
                <label style="color:white" for="serial_no"><?php _e('Serial No:', 'your-text-domain'); ?></label>
                <input style="max-width: 100%!important;" type="text" id="serial_no" name="serial_no" value="<?php echo $warranty_id; ?>" class="woocommerce-Input woocommerce-Input--text input-text" />
            </div>

            

            <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-first">
                <label style="color:white" for="product_name"><?php _e('Model Name:', 'your-text-domain'); ?></label>
                <input style="max-width: 100%!important;" type="text" id="model_name" name="model_name" value="<?php echo $model_name; ?>" class="woocommerce-Input woocommerce-Input--text input-text" />
            </div>

            <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-last">
                <label style="color:white" for="customer_name"><?php _e('Customer Name:', 'your-text-domain'); ?></label>
                <input style="max-width: 100%!important;" type="text" id="customer_name" name="customer_name" class="woocommerce-Input woocommerce-Input--text input-text" />
            </div>

            <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-first">
                <label style="color:white" for="source_name"><?php _e('Source Name (Dealer/Website):', 'your-text-domain'); ?></label>
                <input style="max-width: 100%!important;" type="text" id="source_name" name="source_name" class="woocommerce-Input woocommerce-Input--text input-text" />
            </div>
        </div>

        <div class="woocommerce-column woocommerce-column--2 col-2">
            <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-last">
                <label style="color:white" for="date_of_purchase"><?php _e('Date of Purchase:', 'your-text-domain'); ?></label>
                <input style="max-width: 100%!important;" type="date" id="date_of_purchase" name="date_of_purchase" class="woocommerce-Input woocommerce-Input--date input-date" />
            </div>

            <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-first">
                <label style="color:white" for="email"><?php _e('Email:', 'your-text-domain'); ?></label>
                <input style="max-width: 100%!important;" type="email" id="email" name="email" class="woocommerce-Input woocommerce-Input--email input-email" />
            </div>

            <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-last">
                <label style="color:white" for="mobile"><?php _e('Mobile:', 'your-text-domain'); ?></label>
                <input style="max-width: 100%!important;" type="text" id="mobile" name="mobile" class="woocommerce-Input woocommerce-Input--text input-text" />
            </div>

            <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-first">
                <label style="color:white" for="city"><?php _e('City:', 'your-text-domain'); ?></label>
                <input style="max-width: 100%!important;" type="text" id="city" name="city" class="woocommerce-Input woocommerce-Input--text input-text" />
            </div>

            <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-last">
                <label style="color:white" for="state"><?php _e('State:', 'your-text-domain'); ?></label>
                <input style="max-width: 100%!important;" type="text" id="state" name="state" class="woocommerce-Input woocommerce-Input--text input-text" />
            </div>

            <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-first">
                <label style="color:white" for="country"><?php _e('Country:', 'your-text-domain'); ?></label>
                <input style="max-width: 100%!important;" type="text" id="country" name="country" class="woocommerce-Input woocommerce-Input--text input-text" />
            </div>

            <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-last">
                <label style="color:white" for="zipcode"><?php _e('Zipcode:', 'your-text-domain'); ?></label>
                <input style="max-width: 100%!important;" type="text" id="zipcode" name="zipcode" class="woocommerce-Input woocommerce-Input--text input-text" />
            </div>

            <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label style="color:white" for="address"><?php _e('Address:', 'your-text-domain'); ?></label>
                <input style="max-width: 100%!important;" type="text" id="address" name="address" class="woocommerce-Input woocommerce-Input--textarea textarea" />
            </div>
        </div>
    </div>

    <p class="woocommerce-form-row form-row">
        <input type="hidden" name="action" value="submit_activation">
    <?php wp_nonce_field('submit_activation_nonce', 'submit_activation_nonce'); ?>
    <input type="hidden" name="redirect_url" value="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" />
    <input type="submit" name="submit_activation" value="<?php _e('Activate', 'your-text-domain'); ?>" class="woocommerce-Button button" />
    </p>
</form>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    jQuery(document).ready(function($) {
        $('#product_category').change(function() {
            var category_id = $(this).val();
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'get_products_by_category',
                    category_id: category_id
                },
                success: function(response) {
                    $('#product_list').html(response);
                }
            });
        });
    });
</script>
<script>
    jQuery(document).ready(function($) {
        $('#product_list').select2({
            placeholder: 'Search for a product',
            allowClear: true // Option to clear the selection
        });
    });
</script>
<?php } ?>

