<?php

/**
 * Template Name: Warranty Activation
 */
get_header();
?>

<?php
global $wpdb;

$warranty_table_name = $wpdb->prefix . 'studds_helmets_warranty_data_sync';
$table_name = $wpdb->prefix . "studds_warranty_registrations";

if (!empty($_GET)) { ?>
    <section class="default-content">
        <div class="page-container">

            <div class="default-content__container warranty_dls_contain">

                <?php
                // Define a secret key for decryption (same as encryption)
                define('SECRET_KEY', 'b7D!');

                // Function to decrypt query parameters
                function decrypt($data)
                {
                    $key = substr(hash('sha256', SECRET_KEY, true), 0, 16); // Ensure a 16-byte key
                    $iv = substr(hash('sha256', 'studds_iv_key', true), 0, 16); // 16-byte IV
                    return openssl_decrypt(hex2bin($data), "AES-128-CBC", $key, 0, $iv);
                }

                // Retrieve and decrypt query parameters
                $serial_number = isset($_GET['unique_serial_no']) ? decrypt_srn_mdn($_GET['unique_serial_no']) : '';
                $model_name = isset($_GET['model_name']) ? decrypt_srn_mdn($_GET['model_name']) : '';
                $material_code = isset($_GET['material_code']) ? decrypt_srn_mdn($_GET['material_code']) : '';

                // Check if both serial_number and modal_name exist together in the database
                $exists = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM $warranty_table_name WHERE unique_serial_no = %s AND modal_name = %s",
                    $serial_number,
                    $model_name
                ));


                if ($exists > 0) {

                    // Check if the serial number exists in the database
                    $query = $wpdb->prepare(
                        "SELECT id FROM {$table_name} WHERE serial_number = %s",
                        $serial_number
                    );

                    $entry_id = $wpdb->get_var($query);

                    // If entry exists, retrieve warranty details
                    if (!empty($entry_id)) {

                        // Get the start date
                        $query = $wpdb->prepare(
                            "SELECT warranty_start_date FROM {$table_name} WHERE id = %s",
                            $entry_id
                        );
                        $start_date = $wpdb->get_var($query);

                        // Get the end date
                        $query = $wpdb->prepare(
                            "SELECT warranty_end_date FROM {$table_name} WHERE id = %s",
                            $entry_id
                        );
                        $end_date = $wpdb->get_var($query);

                        // Get first name
                        $query = $wpdb->prepare(
                            "SELECT first_name FROM {$table_name} WHERE id = %s",
                            $entry_id
                        );

                        $fname = $wpdb->get_var($query);
                        $current_date = date('Y-m-d'); // Get today's date in YYYY-MM-DD format


                        // Check if the current date is within the warranty period
                        if (!empty($start_date) && !empty($end_date) && ($current_date >= $start_date && $current_date <= $end_date)) { ?>
                            <div class="warranty_dls_wrap">
                                <div class="warranty_dls_title">
                                    <img src="<?= get_stylesheet_directory_uri(); ?>/assets/img/green_success.svg">
                                    The warranty is already active. <br>
                                </div>
                                <p>Your helmet's warranty is active. Enjoy hassle-free support during the warranty period. </p>
                                <div class="activatation_dates">
                                    <p>Activation Date <span><?= $start_date; ?></span></p>
                                </div>
                                <div class="expiry_date">
                                    <p>Expiry Date <span><?= $end_date; ?></span></p>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="warranty_dls_wrap expired_warranty_sec">
                                <div class="warranty_dls_title">
                                    <img src="<?= get_stylesheet_directory_uri(); ?>/assets/img/warranty-expired.svg">
                                    The warranty has expired. <br>
                                </div>
                                <p>Your helmet's warranty has expired. Warranty claims and support are no longer available. </p>
                                <div class="activatation_dates">
                                    <p>Activation Date <span><?= $start_date; ?></span></p>
                                </div>
                                <div class="expiry_date">
                                    <p>Expiry Date <span><?= $end_date; ?></span></p>
                                </div>
                            </div>
                        <?php }
                    } else {
                        ?> <div id="warranty-form-container">
                            <h2 class="warn_activation">Warranty Activation Form</h2>
                            <form id="warranty-form" enctype="multipart/form-data" method="post">

                                <div class="warranty_dls_sec">
                                    <div class="warranty_dls_box">
                                        <label>First Name *</label>
                                        <input type="text" name="first_name" value="">
                                    </div>

                                    <div class="warranty_dls_box">
                                        <label>Last Name *</label>
                                        <input type="text" name="last_name" value="">
                                    </div>
                                </div>

                                <div class="warranty_dls_sec">
                                    <div class="warranty_dls_box">
                                        <label>Email *</label>
                                        <input type="email" name="email" value="">
                                    </div>

                                    <div class="warranty_dls_box">
                                        <label>Contact Number *</label>
                                        <input type="text" name="contact_number" value="">
                                    </div>
                                </div>


                                <div class="warranty_dls_sec">
                                    <div class="warranty_dls_box">
                                        <label>City *</label>
                                        <input type="text" name="city" value="">
                                    </div>

                                    <div class="warranty_dls_box">
                                        <label>State *</label>
                                        <input type="text" name="state" value="">
                                    </div>
                                </div>

                                <div class="warranty_dls_sec">
                                    <div class="warranty_dls_box">
                                        <label>Country *</label>
                                        <input type="text" name="country" value="">
                                    </div>

                                    <div class="warranty_dls_box">
                                        <label>Billing Date *</label>
                                        <input type="date" name="billing_date" value="">
                                    </div>

                                </div>

                                <div class="warranty_dls_sec">
                                    <div class="warranty_dls_box">
                                        <label>Model Name *</label>
                                        <input type="text" name="model_name" value="<?php echo esc_attr(!empty($model_name) ? $model_name : ''); ?>" readonly>
                                    </div>

                                    <div class="warranty_dls_box">
                                        <label>Product Serial Number *</label>
                                        <input type="text" name="serial_number" value="<?php echo esc_attr(!empty($serial_number) ? $serial_number : ''); ?>" readonly>
                                    </div>
                                </div>

                                <div class="warranty_dls_sec">
                                    <div class="warranty_dls_box">
                                        <label>Invoice Number *</label>
                                        <input type="text" name="invoice_number" value="">
                                    </div>

                                    <div class="warranty_dls_box">
                                        <label>Invoice*</label>
                                        <input type="file" name="invoice_pdf" accept=".pdf, .png, .jpg, .jpeg, .ttf">
                                    </div>
                                </div>

                                <div class="warranty_dls_sec d-none">
                                    <div class="warranty_dls_box">
                                        <label>Warranty Start Date</label>
                                        <input type="text" name="warranty_start_date" value="" readonly>
                                    </div>
                                    <div class="warranty_dls_box">
                                        <label>Warranty End Date</label>
                                        <input type="text" name="warranty_end_date" value="" readonly>
                                    </div>
                                </div>

                                <div class="activate_wrn_btns">
                                    <button type="submit" id="warranty-form-submit-btn" name="submit_warranty">
                                        <span>Submit</span>
                                    </button>
                                    <div class="loading_img_gif d-none">
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/button-loader.gif">
                                    </div>
                                </div>

                            </form>
                        </div>

                        <div id="warranty-success-message" class="d-none">
                            <div class="warranty_dls_wrap">
                                <div class="warranty_dls_title">
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/green_success.svg">
                                    The warranty is activated. <br>
                                </div>
                                <p>Your helmet's warranty is active. Enjoy hassle-free support during the warranty period.</p>
                                <div class="activatation_dates">
                                    <p>Activation Date <span></span></p>
                                </div>
                                <div class="expiry_date">
                                    <p>Expiry Date <span></span></p>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                } else { ?>

                    <div class="warranty_dls_wrap expired_warranty_sec">
                        <div class="warranty_dls_title">
                            <img src="<?= get_stylesheet_directory_uri(); ?>/assets/img/warranty-expired.svg">
                            Unauthorized Access!<br>
                        </div>
                        <p>The scanned warranty is invalid or not recognized. Please check the warranty details and try again. If you need assistance, contact customer support.</p>
                    </div>

                <?php }
                ?>
            </div>
        </div>
    </section>

<?php

}
?>
<?php

get_footer(); ?>