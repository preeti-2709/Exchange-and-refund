<?php
@ini_set('upload_max_size', '256M');
@ini_set('post_max_size', '256M');
@ini_set('max_execution_time', '300');
function boxshop_child_register_scripts()
{
    $parent_style = 'boxshop-style';

    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css', array('boxshop-reset'), boxshop_get_theme_version());
    wp_enqueue_style(
        'boxshop-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array($parent_style)
    );

    /* CSS and Js Enqueued - revamp */
    /* Bootstrap and Slider Css and Js */
    wp_enqueue_style('swiper-bundle-min', get_stylesheet_directory_uri() . '/assets/css/swiper-bundle.min.css', array(), rand(1, 100));
    wp_enqueue_style('bootstrap-min', get_stylesheet_directory_uri() . '/assets/css/bootstrap.min.css', array(), rand(1, 100));

    wp_enqueue_script('swiper-bundle-min', get_stylesheet_directory_uri() . '/assets/js/swiper-bundle.min.js', array('jquery'), rand(1, 100), true);
    wp_enqueue_script('bootstrap-bundle-min', get_stylesheet_directory_uri() . '/assets/js/bootstrap.bundle.min.js', array('jquery'), rand(1, 100), true);
    /* Bootstrap and Slider Css and Js end */

    wp_enqueue_style('studds-custom', get_stylesheet_directory_uri() . '/assets/css/studds-custom.css', array(), rand());
    wp_enqueue_style('studds-custom-swatches', get_stylesheet_directory_uri() . '/assets/css/studds-custom-swatches.css', array(), rand());

    wp_enqueue_script('studds-custom', get_stylesheet_directory_uri() . '/assets/js/studds-custom.js', array('jquery'), rand(1, 100), true);

    wp_enqueue_script('home-slider', get_stylesheet_directory_uri() . '/assets/js/home-slider.js', array('jquery'), rand(1, 100), true);

    wp_enqueue_script('custom-slider', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery'), rand(1, 100), true);
    wp_localize_script('studds-custom', 'ajax_params', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
    if (!is_product()) {
        wp_enqueue_script('studds-shop-page', get_stylesheet_directory_uri() . '/assets/js/studds-shop.js', array('jquery'), rand(1, 100), true);

        wp_localize_script('studds-shop-page', 'shop_params', [
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
    }
    // wp_enqueue_script( 'custom', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery'), rand(1, 100), true);


    // LightGallery CSS
    wp_enqueue_style('fancybox-css', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css');
    wp_enqueue_script('fancybox-js', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js', [], null, true);

    wp_add_inline_script('fancybox-js', '
          Fancybox.bind("[data-fancybox=\\"gallery\\"]", {
            Toolbar: {
              display: [
                "zoom",
                "slideshow",
                "fullscreen",
                "thumbs",
                "close"
              ]
            },
            loop: true,
            protect: true
          });
        ');

    // Zoom plugin (must be separate even with bundle)

    /* corporate pages js */
    if (is_page_template('templates/temp-media.php') || is_page_template('templates/temp-events.php') || is_page_template('templates/temp-gallery.php')) {
        wp_enqueue_script('corporate-pages', get_stylesheet_directory_uri() . '/assets/js/corporate-pages.js', array('jquery'), rand(1, 100), true);
        wp_localize_script('corporate-pages', 'corporate_page_ajax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            // 'nonce'   => wp_create_nonce('media_ajax_nonce'),
        ]);
    }
    /* corporate pages js */

    //  if (is_page_template('warranty-activation.php')) {
    wp_enqueue_script('warranty-activation', get_stylesheet_directory_uri() . '/assets/js/warranty-activation.js', array('jquery'), rand(1, 100), true);
    wp_localize_script('warranty-activation', 'warranty_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('warranty_nonce')
    ));
    // }

    /* CSS and Js Enqueued end - revamp */
}
add_action('wp_enqueue_scripts', 'boxshop_child_register_scripts');

function pp_scripts()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('homepagejs', get_template_directory_uri() . '/js/home.js', array(), rand(1, 100), true);
}
add_action('wp_enqueue_scripts', 'pp_scripts');


add_action('init', 'custom_bootstrap_slider');

// Deny auto Login Authentication
add_filter('woocommerce_registration_auth_new_customer', '__return_false');

/**
 * Register a Custom post type for.
 */
function custom_bootstrap_slider()
{
    $labels = array(
        'name'               => _x('Slider', 'post type general name'),
        'singular_name'      => _x('Slide', 'post type singular name'),
        'menu_name'          => _x('Bootstrap Slider', 'admin menu'),
        'name_admin_bar'     => _x('Slide', 'add new on admin bar'),
        'add_new'            => _x('Add New', 'Slide'),
        'add_new_item'       => __('Name'),
        'new_item'           => __('New Slide'),
        'edit_item'          => __('Edit Slide'),
        'view_item'          => __('View Slide'),
        'all_items'          => __('All Slide'),
        'featured_image'     => __('Featured Image', 'text_domain'),
        'search_items'       => __('Search Slide'),
        'parent_item_colon'  => __('Parent Slide:'),
        'not_found'          => __('No Slide found.'),
        'not_found_in_trash' => __('No Slide found in Trash.'),
    );

    $args = array(
        'labels'             => $labels,
        'menu_icon'      => 'dashicons-star-half',
        'description'        => __('Description.'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => true,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail')
    );

    register_post_type('slider', $args);
}

// function initCors( $value ) {
//   $origin_url = '*';

//   // Check if production environment or not
//   if (ENVIRONMENT === 'production') {
//     $origin_url = 'https://shop.studds.com';
//   }

//   header( 'Access-Control-Allow-Origin: ' . $origin_url );
//   header( 'Access-Control-Allow-Methods: GET' );
//   header( 'Access-Control-Allow-Credentials: true' );
//   return $value;
// }

// shortcode image and video

add_shortcode('shortcode_gallary', 'shortcode_handler_function_gallary');

function shortcode_handler_function_gallary($atts, $content, $tag)
{
?>
    <br>
    <div class="container">
        <div class="wpb_text_column wpb_content_element ">
            <div class="wpb_wrapper">
                <h2 class="h2-big" style="text-align: center; margin-bottom: 10px;font-size:35px!important;"><span style="color: #ffffff;">GALLARY</span></h2>
                <!-- <h5 class="heading-title entry-title" style="text-align: center; margin-bottom: 10px;">
                            <a class="post-title heading-title video_gallary active" href="javascript:void(0);">VIDEO </a>
                            <span></span>
                            <a class="post-title heading-title image_gallary" href="javascript:void(0);">IMAGES</a>
                            </h5> -->
            </div>
        </div>
        <br>
        <!-- Tab panes -->
        <div class="tab-content">
            <div id="home" class="container tab-pane active"><br>
                <p>
                    <?php
                    echo do_shortcode('[embedyt] https://www.youtube.com/embed?listType=playlist&list=UUQQao1u4kPsNDY-ppt8gufA&layout=gallery[/embedyt]');
                    ?>
                </p>
            </div>
            <div id="menu1" class="container tab-pane" style="display:none;"><br>
                <p>
                    <?php
                    //echo do_shortcode('[smartslider3 slider="2"]');
                    ?>

                </p>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Remove Original Product Description
 */

// remove_action('woocommerce_single_product_summary', 'shiprocket_show_check_pincode', 20);
// add_action( 'woocommerce_single_product_summary' , 'shiprocket_show_check_pincode', 31 );

// To change add to cart text on single product page
add_filter('woocommerce_product_single_add_to_cart_text', 'woocommerce_custom_single_add_to_cart_text');
function woocommerce_custom_single_add_to_cart_text()
{
    return __('Add To Cart', 'woocommerce');
}
//Start Suffix and Preffix
add_filter('woocommerce_get_price_html', 'custom_price_suffix', 100, 2);
function custom_price_suffix($price, $product)
{
    if (is_singular('product')) {
        $price = $price . '<span class="incprice"> (INCL. OF ALL TAXES)</span>';
    }
    return apply_filters('woocommerce_get_price', $price);
}

add_filter('woocommerce_get_price_html', 'bbloomer_add_price_prefix', 99, 2);

function bbloomer_add_price_prefix($price, $product)
{
    if (is_singular('product')) {
        $price = '<span class="mrpcs">MRP</span> ' . $price;
    }
    return $price;
}




add_filter('woocommerce_cart_totals_order_total_html', function ($value) {

    if (WC()->cart->get_subtotal_tax() == 0) {
        $value .= ' <small class="tax_label">(INCL. OF ALL TAXES)</small>';
    }

    return $value;
});
// End Suffix and preffix
/**
 * Change WooCommerce Add to cart message with cart link.
 */
// function quadlayers_add_to_cart_message_html( $message, $products ) {

//     $count = 0;
//     $titles = array();
//     foreach ( $products as $product_id => $qty ) {
//     $titles[] = ( $qty > 1 ? absint( $qty ) . ' &times; ' : '' ) . sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'woocommerce' ), strip_tags( get_the_title( $product_id ) ) );
//     $count += $qty;
//     }

//     $titles     = array_filter( $titles );
//     $added_text = sprintf( _n( '%s has been added to your cart.', // Singular
//     '%s have been added to your cart.', // Plural
//     $count, // Number of products added
//     'woocommerce' // Textdomain
//     ), wc_format_list_of_items( $titles ) );
//     $message    = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', esc_url( wc_get_page_permalink( 'cart' ) ), esc_html__( 'View cart', 'woocommerce' ), esc_html( $added_text ) );

//     return $message;
//     }
//     add_filter( 'wc_add_to_cart_message_html', 'quadlayers_add_to_cart_message_html', 10, 2 );

// add_filter('woocommerce_product_add_to_cart_text','woo_custom_product_add_to_cart_text' ); 
//   function woo_custom_product_add_to_cart_text() { 
//   return '<button type="submit" class="custom-button">' . __( 'My Cart', 'woocommerce' ) . '</button>'; 
// }




/* Add Inline Input error in Checkout page */

add_filter('woocommerce_form_field', 'bbloomer_checkout_fields_in_label_error', 10, 4);
function bbloomer_checkout_fields_in_label_error($field, $key, $args, $value)
{
    if (strpos($field, '</label>') !== false && $args['required']) {
        $error = '<span class="error" style="display:none">';
        $error .= sprintf(__('%s is a required field.', 'woocommerce'), $args['label']);
        $error .= '</span>';
        $field = substr_replace($field, $error, strpos($field, '</label>'), 0);
    }
    return $field;
}
/** End **/

/** Display SKU For Variation in Admin Panle Product **/

add_filter('woocommerce_product_get_sku', 'bbloomer_variable_product_skus_admin', 9999, 2);
function bbloomer_variable_product_skus_admin($sku, $product)
{
    if (! is_admin()) return $sku;
    global $post_type, $pagenow;
    if ('edit.php' === $pagenow && 'product' === $post_type) {
        if ($product->is_type('variable')) {
            $sku = '';
            foreach ($product->get_children() as $child_id) {
                $variation = wc_get_product($child_id);
                if ($variation && $variation->exists()) $sku .= '(' . $variation->get_sku() . ') ';
            }
        }
    }
    return $sku;
}
/** End **/

/** Move Label inside the input field **/

add_filter('woocommerce_checkout_fields', 'bbloomer_labels_inside_checkout_fields', 9999);
function bbloomer_labels_inside_checkout_fields($fields)
{
    foreach ($fields as $section => $section_fields) {
        foreach ($section_fields as $section_field => $section_field_settings) {
            $fields[$section][$section_field]['placeholder'] = $fields[$section][$section_field]['label'];
            $fields[$section][$section_field]['label'] = '';
        }
    }
    return $fields;
}

/** End **/

add_filter('woocommerce_checkout_fields', 'bbloomer_shipping_phone_checkout');
function bbloomer_shipping_phone_checkout($fields)
{
    $fields['shipping']['shipping_phone'] = array(
        'label' => 'Phone',
        'type' => 'tel',
        'required' => false,
        'class' => array('form-row-wide'),
        'validate' => array('phone'),
        'autocomplete' => 'tel',
        'priority' => 25,
    );
    return $fields;
}

add_action('woocommerce_admin_order_data_after_shipping_address', 'bbloomer_shipping_phone_checkout_display');
function bbloomer_shipping_phone_checkout_display($order)
{
    echo '<p><b>Shipping Phone:</b> ' . get_post_meta($order->get_id(), '_shipping_phone', true) . '</p>';
}

add_action('woocommerce_checkout_update_order_meta', 'set_billing_email_from_shipping_email', 50, 2);
function set_billing_email_from_shipping_email($order_id, $data)
{
    // Get customer shipping email
    $email = get_post_meta($order_id, '_billing_email', true);
    // Set billing email from shipping email
    update_post_meta($order_id, '_shipping_email', $email);
}


//the additional information tab Changes
add_filter('woocommerce_product_tabs', 'woo_rename_tabs', 98);
function woo_rename_tabs($tabs)
{

    $tabs['additional_information']['title'] = __('Product Information');

    return $tabs;
}
// Display Sku in product information tab - 06-07-22
function display_product_attributes($product_attributes, $product)
{
    // Simple product
    if ($product->is_type('simple')) {
        // Get product SKU
        $get_sku = ($sku = $product->get_sku()) ? $sku : esc_html__('N/A', 'woocommerce');
        $product_attributes['sku-field sku-field-single'] = array(
            'label' => __('Product Code', 'woocommerce'),
            'value' => $get_sku,
        );
    ?>
        <script>
            jQuery(document).ready(function($) {
                var Weight_update = jQuery('#tab-additional_information').find('.woocommerce-product-attributes-item--weight td').text().split(" ");
                var grams = Math.round(Weight_update[0] * 1000);
                if (grams) {
                    jQuery('#tab-additional_information').find('.woocommerce-product-attributes-item--weight td').text(grams + '  ± 50 g');
                }
            });
        </script>
    <?php
    } elseif ($product->is_type('variable')) {
        $children_ids = $product->get_children();
        // Loop
        foreach ($children_ids as $child_id) {
            // Get product
            $product = wc_get_product($child_id);
            // Get product SKU
            $get_sku = ($sku = $product->get_sku()) ? $sku : esc_html__('N/A', 'woocommerce');
            $product_attributes['sku-field sku-field-variable sku-field-variable-' . $child_id] = array(
                'label' => __('Product Code', 'woocommerce'),
                'value' => $get_sku,
            );
        }
    ?>
        <script>
            jQuery(document).ready(function($) {
                // Hide all rows
                $('.sku-field-variable').css('display', 'none');
                //change product weight adding +- 50 gm
                var Text = jQuery('#tab-additional_information').find('.woocommerce-product-attributes-item--weight td').text().split(" ");
                if (Text) {
                    //jQuery('#tab-additional_information').find('.woocommerce-product-attributes-item--weight td').text(Text[0]+'  ± 50 '+Text[1]);
                }
                // Change
                $('input.variation_id').change(function() {
                    var Weight_update = jQuery('#tab-additional_information').find('.woocommerce-product-attributes-item--weight td').text().split(" ");
                    //console.log(Weight_update);
                    if (Weight_update) {
                        jQuery('#tab-additional_information').find('.woocommerce-product-attributes-item--weight td').text(Weight_update[0] + '  ± 50 g');
                    }
                    // Hide all rows
                    $('.sku-field-variable').css('display', 'none');

                    if ($('input.variation_id').val() != '') {
                        var var_id = $('input.variation_id').val();

                        // Display current
                        $('.sku-field-variable-' + var_id).css('display', 'table-row');
                    }
                });
            });
        </script>
    <?php
    }
    return $product_attributes;
}
add_filter('woocommerce_display_product_attributes', 'display_product_attributes', 10, 2);
// End - Display Sku in product information tab - 06-07-22
//Display EAN Code in product information
function display_product_attributes_EAN($product_attributes, $product)
{
    // Simple product
    if ($product->is_type('simple')) {

        // Get product SKU
        $get_ean = get_post_meta($product->get_id(), 'ean_product_text_field', true);
        // Add
        $product_attributes['ean-field ean-field-single'] = array(
            'label' => __('EAN Code', 'woocommerce'),
            'value' => $get_ean,
        );
    }
    // Variable product
    elseif ($product->is_type('variable')) {
        // Get childIDs in an array
        $children_ids = $product->get_children();
        // Loop
        foreach ($children_ids as $child_id) {
            // Get product
            $product = wc_get_product($child_id);
            $value = get_post_meta($child_id, 'custom_field', true);
            if ($value) {
                // rows
                $product_attributes['ean-field ean-field-variable ean-field-variable-' . $child_id] = array(
                    'label' => __('EAN Code', 'woocommerce'),
                    'value' => $value,
                );
            }
        }
    ?>
        <script>
            jQuery(document).ready(function($) {

                // Hide all rows
                $('.ean-field-variable').css('display', 'none');

                // Change
                $('input.variation_id').change(function() {

                    // Hide all rows
                    $('.ean-field-variable').css('display', 'none');

                    if ($('input.variation_id').val() != '') {

                        var var_id = $('input.variation_id').val();

                        //console.log($( '.ean-field-variable-' + var_id ));
                        // Display current
                        $('.ean-field-variable-' + var_id).show();
                        $('.ean-field-variable-' + var_id).css('display', 'table-row');
                    }
                });
            });
        </script>
    <?php
    }

    return $product_attributes;
}
add_filter('woocommerce_display_product_attributes', 'display_product_attributes_EAN', 10, 2);
//END - EAN 

// -----------------------------------------
// 1. Add custom field input @ Product Data > Variations > Single Variation

add_action('woocommerce_variation_options_pricing', 'bbloomer_add_custom_field_to_variations', 10, 3);

function bbloomer_add_custom_field_to_variations($loop, $variation_data, $variation)
{
    woocommerce_wp_text_input(array(
        'id' => 'custom_field[' . $loop . ']',
        'class' => 'short',
        'label' => __('Product EAN Code', 'woocommerce'),
        'value' => get_post_meta($variation->ID, 'custom_field', true)
    ));
}

// -----------------------------------------
// 2. Save custom field on product variation save

add_action('woocommerce_save_product_variation', 'bbloomer_save_custom_field_variations', 10, 2);

function bbloomer_save_custom_field_variations($variation_id, $i)
{
    $custom_field = $_POST['custom_field'][$i];
    if (isset($custom_field)) update_post_meta($variation_id, 'custom_field', esc_attr($custom_field));
}

// -----------------------------------------
// 3. Store custom field value into variation data

add_filter('woocommerce_available_variation', 'bbloomer_add_custom_field_variation_data');

function bbloomer_add_custom_field_variation_data($variations)
{
    $variations['custom_field'] = '<div class="woocommerce_custom_field">EAN Code: <span>' . get_post_meta($variations['variation_id'], 'custom_field', true) . '</span></div>';
    return $variations;
}
//END - Custom fields code in variation product 

// Display Custom fields in product tab 
add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');
function woocommerce_product_custom_fields()
{
    global $woocommerce, $post;
    echo '<div class="product_custom_field">';
    // Custom Product Text Field
    woocommerce_wp_text_input(
        array(
            'id' => 'ean_product_text_field',
            'placeholder' => 'EAN Code',
            'label' => __('EAN Code', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );
    echo '</div>';
}
// Save Fields
add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');
function woocommerce_product_custom_fields_save($post_id)
{
    // Custom Product Number Field
    $woocommerce_custom_product_number_field = $_POST['ean_product_text_field'];
    if (!empty($woocommerce_custom_product_number_field))
        update_post_meta($post_id, 'ean_product_text_field', esc_attr($woocommerce_custom_product_number_field));
}
//END Custom fields in product tab 
//
//Display EN Code Custom field in order detail page
function action_woocommerce_checkout_create_order_line_item($item, $cart_item_key, $values, $order)
{
    // The WC_Product instance Object
    $product = $item->get_product();

    // Get value
    $value = $product->get_meta('custom_field');

    // NOT empty
    if (! empty($value)) {
        $item->update_meta_data('EAN CODE', $value);
    }
}
add_action('woocommerce_checkout_create_order_line_item', 'action_woocommerce_checkout_create_order_line_item', 10, 4);
//End

//Display Net Quantity and address in product information tab - 05-07-22
function Netprefix_woocommerce_display_product_attributes($product_attributes, $product)
{
    $cat = get_the_terms($product->ID, 'product_cat');
    $model = $product->get_attribute('model');

    foreach ($cat as $categoria) {
        if ($categoria->parent == 0) {
            $categoria;
        }
    }
    //echo $categoria->name;
    if ($categoria->name == 'Gloves') {
        $product_attributes['customfield'] = [
            'label' => __('Net Quantity', 'text-domain'),
            'value' => '1 <span class="helmets">Pair (2N)</span>',
        ];
    } elseif ($categoria->name == 'Rain Suits') {
        $product_attributes['customfield'] = [
            'label' => __('Net Quantity', 'text-domain'),
            'value' => '1 SET <span class="helmets">(1N jacket and 1N trousers)</span>',
        ];
    } elseif ($model === 'Thunder Bluetooth') {
        $product_attributes['net_quantity'] = [
            'label' => __('Net Quantity', 'text-domain'),
            'value' => '1 SET <span class="helmets">(1 Unit Helmet + 1 Unit USB Cable + 1 Unit Battery)</span>',
        ];
    } else {
        $product_attributes['customfield'] = [
            'label' => __('Net Quantity', 'text-domain'),
            'value' => '1 <span class="gloves">Number</span>',
        ];
    }
    return $product_attributes;
}
add_filter('woocommerce_display_product_attributes', 'Netprefix_woocommerce_display_product_attributes', 10, 2);

function Addprefix_woocommerce_display_product_attributes($product_attributes, $product)
{
    $product_attributes['customfield_add'] = [
        'label' => __('Manufactured and Marketed By', 'text-domain'),
        'value' => 'STUDDS Accessories Limited,<br>Plot No. 918, Sector 68, IMT, Faridabad - 121004, Haryana India ',
    ];

    return $product_attributes;
}
add_filter('woocommerce_display_product_attributes', 'Addprefix_woocommerce_display_product_attributes', 10, 2);

function ProductCert_woocommerce_display_product_attributes($product_attributes, $product)
{
    $product_attributes['customfield_cert'] = [
        'label' => __('Certification', 'text-domain'),
        'value' => 'ISI',
    ];

    return $product_attributes;
}
//add_filter('woocommerce_display_product_attributes', 'ProductCert_woocommerce_display_product_attributes', 10, 2);

//END Display Net Quantity and address in product information tab - 05-07-22

add_filter('woocommerce_product_tabs', 'wp_woo_rename_reviews_tab', 98);
function wp_woo_rename_reviews_tab($tabs)
{
    global $product;
    $check_product_review_count = $product->get_review_count();
    if ($check_product_review_count == 0) {
        $tabs['reviews']['title'] = 'Reviews';
    } else {
        $tabs['reviews']['title'] = 'Reviews(' . $check_product_review_count . ')';
    }
    return $tabs;
}
// Header Start For Product qty. in Order List
function filter_manage_edit_shop_order_columns($columns)
{
    // Add new column
    // $columns['order_products'] = __( 'Product Qty.', 'woocommerce' );

    return $columns;
}
add_filter('manage_edit-shop_order_columns', 'filter_manage_edit_shop_order_columns', 10, 1);

// Populate the Column
function action_manage_shop_order_posts_custom_column($column, $post_id)
{
    // Compare
    if ($column == 'order_products') {
        // Get an instance of the WC_Order object from an Order ID
        $order = wc_get_order($post_id);

        // Is a WC_Order
        if (is_a($order, 'WC_Order')) {
            foreach ($order->get_items() as $item) {
                // Product ID
                $product_id = $item->get_variation_id() > 0 ? $item->get_variation_id() : $item->get_product_id();

                // Get product
                $product = wc_get_product($product_id);

                // Get stock quantity
                $get_stock_quantity = $product->get_stock_quantity();

                // NOT empty
                if (! empty($get_stock_quantity)) {
                    $stock_output = ' (' . $get_stock_quantity . ')';
                } else {
                    $stock_output = '';
                }

                // Output
                echo '▪ <a href="' . admin_url('post.php?post=' . $item->get_product_id() . '&action=edit') . '">' .  $item->get_name() . '</a> × ' . $item->get_quantity() . $stock_output . '<br />';
            }
        }
    }
}
// add_action( 'manage_shop_order_posts_custom_column' , 'action_manage_shop_order_posts_custom_column', 10, 2 );
// Header End For Product qty. in Order List

// Admin orders list: bulk order status change dropdown Start
function filter_dropdown_bulk_actions_shop_order($actions)
{
    // Targeting shop_manager
    if (current_user_can('shop_manager')) {
        $actions = (array) null;
    }

    return $actions;
}
add_filter('bulk_actions-edit-shop_order', 'filter_dropdown_bulk_actions_shop_order', 20, 1);

// Admin orders list: quick action
function filter_order_actions($actions, $order)
{
    // Targeting shop_manager
    if (current_user_can('shop_manager')) {
        $actions = (array) null;
    }

    return $actions;
}
add_filter('woocommerce_admin_order_actions', 'filter_order_actions', 10, 2);

// Admin order pages: order status dropdown
function filter_order_statuses($order_statuses)
{
    global $post, $pagenow;

    if ($pagenow === 'post.php' || $pagenow === 'post-new.php') {
        // Get ID
        $order_id = $post->ID;

        // Get an instance of the WC_Order object
        $order = wc_get_order($order_id);

        // TRUE
        if ($order) {
            // Get current order status
            $order_status = 'wc-' . $order->get_status();

            // New order status
            $new_order_statuses = array();

            foreach ($order_statuses as $key => $option) {
                // Targeting "shop_manager"
                if (current_user_can('sales') && $key == $order_status) {
                    $new_order_statuses[$key] = $option;
                }
            }

            if (sizeof($new_order_statuses) > 0) {
                return $new_order_statuses;
            }
        }
    }
    return $order_statuses;
}
add_filter('wc_order_statuses', 'filter_order_statuses', 10, 1);
// End Code Base

/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Add ZIP Code Validation Error 
 *
 */
add_action('woocommerce_checkout_process', 'custom_checkout_field_process');

function custom_checkout_field_process()
{
    global $woocommerce;

    // Check if set, if its not set add an error. This one is only requite for companies
    if (! (preg_match('/^[0-9]{6}$/D', $_POST['billing_postcode']))) {
        wc_add_notice("<b>Billing PIN Code Format</b> is Incorrect! Please enter only correct number.", 'error');
    }
}
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    ZIP Code Validation for Checkout page.
 *
 */
add_filter('woocommerce_validate_postcode', 'validate_indian_postcode', 10, 3);

function validate_indian_postcode($valid, $postcode, $country)
{
    if ($country == "IN")
        $valid = (bool) preg_match('/^([0-9]{6})$/', $postcode);
    // checks your $postcode, if valid $valid will be true because of preg_match else false
    return $valid;
}

/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Remove the Addresses tab
 *
 */
add_filter('woocommerce_account_menu_items', 'QuadLayers_remove_acc_tab', 999);
function QuadLayers_remove_acc_tab($items)
{
    unset($items['edit-address']);
    return $items;
}
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Insert the content of the Addresses tab into an existing tab (edit-account in this case)
 *
 */
add_action('woocommerce_account_edit-account_endpoint', 'woocommerce_account_edit_address');
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Remove the downloads Options
 *
 */
add_filter('woocommerce_account_menu_items', 'QuadLayers_remove_acc_address', 9999);
function QuadLayers_remove_acc_address($items)
{
    unset($items['downloads']);
    return $items;
}
//End

/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Display Recent order in ADMIN Dashboard tab
 *
 */
function action_woocommerce_account_dashboard()
{
    // Set limit
    $limit = 2;

    // Get customer $limit last orders
    $customer_orders = wc_get_orders(array(
        'customer'  => get_current_user_id(),
        'limit'     => $limit
    ));

    // Count customers orders
    $count = count($customer_orders);

    // Greater than or equal to
    if ($count >= 1) {
        // Message
        echo '<p>' . sprintf(_n('Your last order', 'Your last %s orders Status', $count, 'woocommerce'), $count) . '</p>';
    ?>
        <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
            <thead>
                <tr>
                    <?php foreach (wc_get_account_orders_columns() as $column_id => $column_name) : ?>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr($column_id); ?>"><span class="nobr"><?php echo esc_html($column_name); ?></span></th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php
                foreach ($customer_orders as $customer_order) {
                    $order = wc_get_order($customer_order); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                    $item_count = $order->get_item_count() - $order->get_item_count_refunded();
                ?>
                    <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr($order->get_status()); ?> order">
                        <?php foreach (wc_get_account_orders_columns() as $column_id => $column_name) : ?>
                            <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr($column_id); ?>" data-title="<?php echo esc_attr($column_name); ?>">
                                <?php if (has_action('woocommerce_my_account_my_orders_column_' . $column_id)) : ?>
                                    <?php do_action('woocommerce_my_account_my_orders_column_' . $column_id, $order); ?>

                                <?php elseif ('order-number' === $column_id) : ?>
                                    <a href="<?php echo esc_url($order->get_view_order_url()); ?>">
                                        <?php echo esc_html(_x('#', 'hash before order number', 'woocommerce') . $order->get_order_number()); ?>
                                    </a>

                                <?php elseif ('order-date' === $column_id) : ?>
                                    <time datetime="<?php echo esc_attr($order->get_date_created()->date('c')); ?>"><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></time>

                                <?php elseif ('order-status' === $column_id) : ?>
                                    <?php

                                    $statusproducts = esc_html(wc_get_order_status_name($order->get_status()));

                                    ?>
                                    <span class="<?php echo $statusproducts; ?>"><?php echo $statusproducts; ?></span>


                                <?php elseif ('order-total' === $column_id) : ?>
                                    <?php
                                    /* translators: 1: formatted order total 2: total order items */
                                    echo wp_kses_post(sprintf(_n('%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce'), $order->get_formatted_order_total(), $item_count));
                                    ?>

                                <?php elseif ('order-actions' === $column_id) : ?>
                                    <?php
                                    $actions = wc_get_account_orders_actions($order);

                                    if (! empty($actions)) {
                                        foreach ($actions as $key => $action) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                                            echo '<a href="' . esc_url($action['url']) . '" class="woocommerce-button button ' . sanitize_html_class($key) . '">' . esc_html($action['name']) . '</a>';
                                        }
                                    }
                                    ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    <?php
    } else {
    ?>
        <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
            <a class="woocommerce-Button button" href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>"><?php esc_html_e('Browse products', 'woocommerce'); ?></a>
            <?php esc_html_e('No order has been made yet.', 'woocommerce'); ?>
        </div>
        <?php
    }
}
add_action('woocommerce_account_dashboard', 'action_woocommerce_account_dashboard');
// End
// 


add_filter('woocommerce_checkout_fields', 'remove_company_name');

function remove_company_name($fields)
{
    unset($fields['billing']['billing_company']);
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'remove_company_name1');

function remove_company_name1($fields1)
{
    unset($fields1['shipping']['shipping_company']);
    return $fields1;
}
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Show Additional Content in Login and Signup section
 *
 */
add_action('woocommerce_login_form_start', 'bbloomer_add_login_text');

function bbloomer_add_login_text()
{
    if (is_checkout()) return;
    echo '<h3 class="bb-login-subtitle">Registered Customers</h3><p class="bb-login-description">If you have an account with us, log in using your email address.</p>';
}

add_action('woocommerce_register_form_start', 'bbloomer_add_reg_text');

function bbloomer_add_reg_text()
{
    echo '<h3 class="bb-register-subtitle">New Customers</h3><p class="bb-register-description">By creating an account with our store, you will be able to move through the checkout process faster, store multiple shipping addresses, view and track your orders in your account and more.</p>';
}

/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Remove _link in woocommerce order api for SAP
 *
 */
function prefix_wc_rest_prepare_order_object($response, $object, $request)
{
    // Get the value
    foreach ($response->get_links() as $_linkKey => $_linkVal) {
        $response->remove_link($_linkKey);
    }
    return $response;
}
add_filter('woocommerce_rest_prepare_shop_order_object', 'prefix_wc_rest_prepare_order_object', 10, 3);
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Disabled Cancel button on Shipped Order Status.
 *
 */
add_filter('woocommerce_valid_order_statuses_for_cancel', 'custom_valid_order_statuses_for_cancel', 10, 1);
function custom_valid_order_statuses_for_cancel($statuses)
{

    // Set HERE the order statuses where you want the cancel button to appear
    // return array_merge( $statuses, array('processing', 'shipped'));
    return array_merge($statuses, array('processing', 'ready-to-ship', 'partial-cancel'));
}
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Admin order edit page: order status dropdown
 *
 */
function filter_wc_order_statuses($order_statuses)
{
    global $post, $pagenow;
    // Target edit pages
    if ($pagenow === 'post.php' && isset($_GET['post']) && $_GET['action'] == 'edit' && get_post_type($_GET['post']) === 'shop_order') {
        $order_id = $post->ID;
        // Get an instance of the WC_Order object
        $order = wc_get_order($order_id);
        // Is a WC order
        if (current_user_can('shop_manager')) {
            if (is_a($order, 'WC_Order')) {
                $order_status = $order->get_status();
                foreach ($order_statuses as $statuskey => $order_statuses_key) {
                    if ("wc-" . $order_status != $statuskey) {

                        if ("wc-" . $order_status == 'wc-reverse_pickup' && $statuskey == 'wc-exchange_received') {
                        } else {
                            unset($order_statuses[$statuskey]);
                        }
                    }
                }
            }
        }
    }
    return $order_statuses;
}
add_filter('wc_order_statuses', 'filter_wc_order_statuses', 10, 1);
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    uploading invoice sap to portal
 *
 */
function wc_invoice_url($order_id)
{
    global $wpdb;
    $order_id_new = explode(' → ', $order_id);
    $orderid = $order_id_new['0'];
    $invoice_url = '';
    $credit_note = '';
    $invoice = array();
    $result = $wpdb->get_results("SELECT * FROM product_invoice WHERE order_id = $orderid");
    if (!empty($result)) {
        foreach ($result as $key => $value) {
            $billing = explode('CN', $value->billing_id);
            if (empty($billing['0'])) {
                $invoice['credit_note'] = home_url() . '/upload_invoice/' . $value->invoice_file;
            } else {
                $invoice['invoice_url'] = home_url() . '/upload_invoice/' . $value->invoice_file;
            }
        }
        $url = $invoice;
    } else {
        $url = '';
    }
    return $url;
}
add_action('rest_api_init', function () {
    register_rest_route('wc/v3', 'woocommerce-invoice-push', array(
        'methods' => 'POST', // array( 'GET', 'POST', 'PUT', )
        'callback' => 'uploade_invoice_sap',
        'permission_callback' => '__return_true'
    ));
});
function uploade_invoice_sap(WP_REST_Request $request)
{
    $body = $request;
    global $wpdb;
    $body = $body['update'];
    $folder  = 'upload_invoice/';
    $status = false;
    $i = 0;

    foreach ($body as $key => $PostData) {
        $invoice_file_all = array();
        $order_id_all = array();
        $table_name = 'product_invoice';
        $order_id = $PostData['order_id'];
        //check order available or not in woocommerce
        $order_exist = wc_get_order($order_id);
        $result = $wpdb->get_results("SELECT * FROM $table_name WHERE order_id = $order_id limit 1");
        if (empty($order_exist)) {
            $data['update'][$i]['status'] = false;
            $data['update'][$i]['Message'] = 'invalid order id.';
            // $data['update'][$i]['Data']['error_code'] = '404';
            $data['update'][$i]['Data']['order_id'] = $order_id;
            $data['update'][$i]['Data']['invoice_file'] = '';
        } else if ($PostData['file'] == '') {
            $data['update'][$i]['status'] = false;
            $data['update'][$i]['Message'] = 'invalid file.';
            // $data['update'][$i]['Data']['error_code'] = '404';
            $data['update'][$i]['Data']['order_id'] = $order_id;
            $data['update'][$i]['Data']['invoice_file'] = '';
        } else if ($PostData['delete'] == true) {
            unlink('upload_invoice/' . $result['0']->invoice_file);
            $wpdb->query("DELETE FROM $table_name WHERE order_id = $order_id");

            $data['update'][$i]['status'] = true;
            $data['update'][$i]['Message'] = 'invoice delete successfully.';
            $data['update'][$i]['Data']['order_id'] = $order_id;
            $data['update'][$i]['Data']['invoice_file'] = $result['0']->invoice_file;
        } else {
            if ($result['0']->billing_id == $PostData['billing_id']) {
                unlink('upload_invoice/' . $result['0']->invoice_file);
                $wpdb->query("DELETE FROM $table_name WHERE order_id = $order_id");
            }
            $offset = strtotime("+5 hours 30 minutes");
            $time = date("dmY_His", $offset);
            $billing_id = $PostData['billing_id'];

            $extension = 'pdf';
            $fileName = $PostData['order_id'] . "_" . $billing_id . "_" . $time . '.' . $extension;
            // $Test_file = base64_encode($PostData['file']);


            $base64string = $PostData['file'];
            $base64data = base64_decode($base64string, true);
            if (strpos($base64data, '%PDF') !== 0) {
                $data['update'][$i]['status'] = false;
                $data['update'][$i]['Message'] = 'invalid file.';
                // $data['update'][$i]['Data']['error_code'] = '404';
                $data['update'][$i]['Data']['order_id'] = $order_id;
                $data['update'][$i]['Data']['invoice_file'] = '';
                $response = new WP_REST_Response($data);
                $response->set_status(400);
                return $data;
                exit();
            }
            $uploadpath = 'upload_invoice/';
            $file       = $uploadpath . $fileName;
            file_put_contents($file, $base64data);
            $order_id = $PostData['order_id'];
            $invoice_file = $fileName;
            $createdate = date("Y-m-d H:i:s");
            $invoice_file_all[] = $invoice_file;
            $order_id_all[] = $PostData['order_id'];
            $wpdb->insert($table_name, array('order_id' => $order_id, 'invoice_file' => $invoice_file, 'createdate' => $createdate, 'billing_id' => $billing_id));
            $data['update'][$i]['status'] = true;
            $data['update'][$i]['Message'] = 'invoice uploaded successfully.';
            $data['update'][$i]['Data']['order_id'] = $PostData['order_id'];
            $data['update'][$i]['Data']['invoice_file'] = $invoice_file;
        }
        $i++;
        $status = true;
        sleep(1);
    }
    if ($status == true) {
        $response = new WP_REST_Response($data);
        $response->set_status(200);
        return $data;
        // echo json_encode($data);
        exit();
    } else {
        $data['update']['0']['status'] = false;
        $data['update']['0']['Message'] = 'No invoice upload.';
        $data['update']['0']['Data']['order_id'] = $order_id_all;
        $data['update']['0']['Data']['invoice_file'] = $invoice_file_all;

        $response = new WP_REST_Response($data);
        $response->set_status(200);
        return $data;
        // echo json_encode($data);
        exit();
    }
}
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Modify order response adding gst and company titile
 *
 */
add_filter('woocommerce_rest_prepare_shop_order_object', 'prefix_wc_rest_prepare_order_object_1', 10, 3);
function prefix_wc_rest_prepare_order_object_1($response, $object, $request)
{
    // Get the value
    if ($response->data['meta_data']) {
        // $response->data["billing"]['Gst_flag'] = 'No';
        $response->data["billing"]['customer_type'] = 'Sold to Party';
        $response->data["shipping"]['customer_type'] = 'Ship to Party';
        foreach ($response->data['meta_data'] as $data_key) {
            $meta_data_array = $data_key->get_data();

            // if($meta_data_array['key'] == '_billing_gst_no'){
            //     $response->data["billing"]['Gst_flag'] = 'Yes';
            //     $response->data["billing"]['Gst_Number'] = $meta_data_array['value'];

            // }
            if ($meta_data_array['key'] == '_billing_salutation') {
                $response->data["billing"]['title'] = $meta_data_array['value'];
            }
            if ($meta_data_array['key'] == '_shipping_salutation') {
                $response->data["shipping"]['title'] = $meta_data_array['value'];
            }
            if ($response->data['status'] == 'return-approved' && $meta_data_array['key'] == 'ced_rnx_return_product') {
                $response->data["return"][] = $meta_data_array['value'];
            }
            if ($response->data['status'] == 'exchange-approve' && $meta_data_array['key'] == 'ced_rnx_exchange_product') {
                $response->data["exchange"][] = $meta_data_array['value'];
            }
            if ($response->data['status'] == 'processing' && $meta_data_array['key'] == 'ced_rnx_exchange_warrantry') {
                $response->data['exchange_warranty'] = $meta_data_array['value'];
            }
        }
    }
    if ($response->data['date_paid'] == '') {
        // $response->data['date_paid'] = date("Y-m-d H:i:s");
    }
    $response->data['fee_lines'] = [];
    $response->data['meta_data'] = '';
    $response->data['shipping_lines'] = '';
    $response->data['coupon_lines'] = '';
    return $response;
}
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    overrite state drop down in checkout page - 11-10-22
 *
 */
add_filter('woocommerce_states', 'custom_woocommerce_states');
function custom_woocommerce_states($states)
{
    $states['IN'] = array( // Indian states.
        'AD' => __('Andhra Pradesh', 'woocommerce'),
        'AR' => __('Arunachal Pradesh', 'woocommerce'),
        'AS' => __('Assam', 'woocommerce'),
        'BR' => __('Bihar', 'woocommerce'),
        'CG' => __('Chhattisgarh', 'woocommerce'),
        'GA' => __('Goa', 'woocommerce'),
        'GJ' => __('Gujarat', 'woocommerce'),
        'HR' => __('Haryana', 'woocommerce'),
        'HP' => __('Himachal Pradesh', 'woocommerce'),
        'JK' => __('Jammu and Kashmir', 'woocommerce'),
        'JH' => __('Jharkhand', 'woocommerce'),
        'KA' => __('Karnataka', 'woocommerce'),
        'KL' => __('Kerala', 'woocommerce'),
        'LA' => __('Ladakh', 'woocommerce'),
        'MP' => __('Madhya Pradesh', 'woocommerce'),
        'MH' => __('Maharashtra', 'woocommerce'),
        'MN' => __('Manipur', 'woocommerce'),
        'ML' => __('Meghalaya', 'woocommerce'),
        'MZ' => __('Mizoram', 'woocommerce'),
        'NL' => __('Nagaland', 'woocommerce'),
        'OD' => __('Odisha', 'woocommerce'),
        'PB' => __('Punjab', 'woocommerce'),
        'RJ' => __('Rajasthan', 'woocommerce'),
        'SK' => __('Sikkim', 'woocommerce'),
        'TN' => __('Tamil Nadu', 'woocommerce'),
        'TS' => __('Telangana', 'woocommerce'),
        'TR' => __('Tripura', 'woocommerce'),
        'UK' => __('Uttarakhand', 'woocommerce'),
        'UP' => __('Uttar Pradesh', 'woocommerce'),
        'WB' => __('West Bengal', 'woocommerce'),
        'AN' => __('Andaman and Nicobar Islands', 'woocommerce'),
        'CH' => __('Chandigarh', 'woocommerce'),
        'DN' => __('Dadra and Nagar Haveli', 'woocommerce'),
        'DD' => __('Daman and Diu', 'woocommerce'),
        'DL' => __('Delhi', 'woocommerce'),
        'LD' => __('Lakshadeep', 'woocommerce'),
        'PY' => __('Pondicherry (Puducherry)', 'woocommerce'),
    );
    return $states;
}
// change cancel related - 12-10-22
add_action('woocommerce_order_details_after_order_table_items', 'cancel_order_details');
function cancel_order_details($order)
{
    $order_status = $order->get_status();
    if ($order_status == 'cancelled' || $order_status == 'partial-cancel') {
        $cancel_datas = get_post_meta($order->get_id(), 'partial_cancel_details', true);
        if (!empty($cancel_datas)) {
            $cancel_products = $cancel_datas['partial_cancel_product'];
            $total_amount = 0.0; // Custom change in code as it was showing issue in cart page - 27-05-2025
            foreach ($cancel_products as $cancel_items) {
                $total_amount += (float) $cancel_items['price']; // Custom code - 27-05-2025
            }
        } else {
            $total_amount = $order->get_total();
        }
        $order_id = $order->get_id();

        $return_requirest = Refund_Status_Api($order_id);
        $return_requirest = json_decode($return_requirest, true);
        // $return_requirest = '';
        if (!empty($return_requirest) && $return_requirest['txnid'] != '' && $return_requirest['easebuzz_id'] != '') {
            foreach ($return_requirest['refunds'] as $return_data) {
                $return_amount = $return_data['refund_amount'];
                $return_status = $return_data['refund_status'];
                $return_id = $return_data['refund_id'];
            }
        } else {
            $return_status = 'Initiative';
        }
        if ($return_amount) :
        ?>
            <tfoot>
                <tr>
                    <th></th>
                    <th scope="row">Total Refund Amount:</th>
                    <td><strong>₹<?php echo $return_amount; //echo esc_html( $total_amount ) 
                                    ?></strong></td>
                </tr>
                <tr>
                    <th></th>
                    <th scope="row">Refund Status:</th>
                    <td><?php echo $return_status; ?></td>
                </tr>
            </tfoot>
    <?php
            // specify the order_id so WooCommerce knows which to update
            $order_data = array(
                'order_id' => $order_id,
                'customer_note' => $return_id
            );
            // update the customer_note on the order
            wc_update_order($order_data);
        endif;
    }
}
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Check refund status in rest api
 *
 */
function Refund_Status_Api($order_id)
{
    $txnid = get_post_meta($order_id, 'txnid', true);
    $key = 'RWN6LH487F';
    $easebuzz_id = get_post_meta($order_id, '_transaction_id', true);
    // $easebuzz_id = 'E2210041P0RGPU';
    $salt = 'YTYT4L3QWX';

    $checkhash = hash('sha512', "$key|$easebuzz_id|$salt");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://dashboard.easebuzz.in/refund/v1/retrieve");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    $data = array(
        'key' => $key,
        'easebuzz_id' => $easebuzz_id,
        'hash' => $checkhash,
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $contents = curl_exec($ch);
    curl_close($ch);
    return $contents;
}
add_filter('shiprocket_return_status_cron', 'shiprocket_return_status_cron');

/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    get return status details and update in portal and shiprocket_return_status_cron();
 *
 */
function shiprocket_return_status_cron()
{
    $token = Authorization_ShiprockAPI_Cron();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://apiv2.shiprocket.in/v1/external/orders/processing/return?channel_id=2933586");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ));
    $contents = curl_exec($ch);
    curl_close($ch);
    $order_status_json = json_decode($contents, true);
    if (!empty($order_status_json)) {
        global $wpdb;
        $table = 'shiprocket_return_api';
        foreach ($order_status_json['data'] as $order_key => $order_values) {
            $order_id = isset($order_values['channel_order_id']) ? sanitize_text_field(wp_unslash($order_values['channel_order_id'])) : 0;
            $status_shiprocket = $order_values['status'];
            $query = $wpdb->get_results("SELECT * FROM $table WHERE order_id = $order_id and (active = '0' or status != '$status_shiprocket') ");

            if (!empty($query)) {
                $order_detail = wc_get_order($order_id);
                $status_order = $order_detail->get_status();
                if ($order_values['status'] == 'RETURN PENDING') {
                    $status = 'wc-return-pending';
                } else if ($order_values['status'] == 'RETURN INITIATED') {
                    $status = 'wc-return-initiated';
                } else if ($order_values['status'] == 'RETURN PICKUP GENERATED') {
                    $status = 'wc-return-pickup-gen';
                } else if ($order_values['status'] == 'RETURN PICKUP RESCHEDULED') {
                    $status = 'wc-return-pickup-res';
                } else if ($order_values['status'] == 'RETURN PICKED UP') {
                    $status = 'wc-return-picked-up';
                } else if ($order_values['status'] == 'RETURN PICKUP ERROR') {
                    $status = 'wc-return-pickup-err';
                } else if ($order_values['status'] == 'RETURN IN-TRANSIT') {
                    $status = 'wc-return-in-transit';
                } else if ($order_values['status'] == 'RETURN DELIVERED') {
                    $status = 'wc-exchange_received';
                } else if ($order_values['status'] == 'RETURN CANCELLED') {
                    $status = 'wc-return-canceled';
                }
                $exchange_note = __('Order status changed from ' . $status_order . ' to ' . $order_values['status'] . ' throught shiprocket');
                $datesent = date('Y-m-d H:i:s');
                $active = '1';
                $sql = $wpdb->prepare("UPDATE $table SET active = %s,modify_date = %s,status = %s where order_id = %s ", $active, $datesent, $order_values['status'], $order_id);
                $wpdb->query($sql);

                $order_detail->add_order_note($exchange_note);
                $order_detail->update_status($status);
            }
        }
        $response = new WP_REST_Response($order_status_json);
        $response->set_status(200);
        return $order_status_json;
    }
}
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    create token in shiprocket rest api
 *
 */

function Authorization_ShiprockAPI_Cron()
{
    $url = 'https://apiv2.shiprocket.in/v1/external/auth/login';
    $headers = array(
        'Content-Type' => 'application/json',
    );
    $body = wp_json_encode(array(
        'email' => 'aarti.ojha@studds.com',
        'password' => 'Studds@1234',
    ));

    $response = wp_remote_post($url, array(
        'headers' => $headers,
        'body' => $body,
    ));

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        return "Error: $error_message";
    } else {
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);

        // Decode the JSON response to extract the token
        $response_data = json_decode($response_body, true);
        if (isset($response_data['token'])) {
            $token = $response_data['token'];
            return $token;
        } else {
            return "<p>Token not found in the response.</p>";
        }
    }
}

//add_shortcode('shiprocket_login', 'Authorization_ShiprockAPI_Cron');


/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Show products from specific product categories on the Shop page.
 *
 */
add_action('woocommerce_product_query', 'wpdd_limit_shop_categories');
function wpdd_limit_shop_categories($q)
{
    $tax_query = (array) $q->get('tax_query');
    // Taking current system Time
    $_SESSION['start'] = time(); // Destroying session after 1 minute
    $_SESSION['timeout'] = time();
    // $_SESSION['expire'] = $_SESSION['start'] + (1 * 10) ;
    // $_SESSION['expire'] = time();
    $get_product_price = WC()->session->get('ced_rnx_exchange_price');
    $exchange_product_id = WC()->session->get('rnx_exchange_product');
    $exchange_categories = get_the_terms($exchange_product_id, 'product_cat');
    $categ = !empty($exchange_categories) && is_array($exchange_categories) ? $exchange_categories[0]->name : 'Uncategorized'; // according to uat - 27-05-25
    // if($categ == 'open-face-helmets' || $categ == 'full-face-helmets' || $categ == 'flip-up-full-face-helmets' || $categ == 'flip-off-full-face-helmets' || $categ == 'off-road-full-face-helmets'){
    // if(WC()->session->get( 'exchange_session_start' ) == '1'){
    //     $tax_query[] = array(
    //         'taxonomy' => 'product_cat',
    //         'field' => 'slug',
    //         'terms' => 'helmets',
    //         'include_children' => true,
    //     );
    //     $meta_query = array(
    //         array(
    //             'meta_key'  => '_price',
    //             'value'     => $get_product_price,
    //             'compare'   => '=',
    //             'type'      => 'NUMERIC'
    //         ),
    //         array(
    //                             'key' => '_price',
    //                             'value' => $get_product_price,
    //                             'compare' => '=',
    //                             'type' => 'NUMERIC'
    //                           )
    //     );
    //     $q->set( 'meta_query', $meta_query );
    //     $q->set( 'tax_query', $tax_query );
    // }
    // }else{
    if (WC()->session->get('exchange_session_start') == '1') {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => array($categ),
            'include_children' => true,
        );
        $meta_query = array(
            array(
                'meta_key'  => '_price',
                'value'     => $get_product_price,
                'compare'   => '=',
                'type'      => 'NUMERIC'
            ),
            array(
                'key' => '_price',
                'value' => $get_product_price,
                'compare' => '=',
                'type' => 'NUMERIC'
            )
        );
        $q->set('meta_query', $meta_query);
        $q->set('tax_query', $tax_query);
        //} 
    }
}
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Show Was Now Price In WooCommerce Cart
 *
 */
add_filter('woocommerce_cart_item_price', 'wpd_show_regular_price_on_cart', 30, 3);
function wpd_show_regular_price_on_cart($price, $values, $cart_item_key)
{
    $is_on_sale = $values['data']->is_on_sale();
    if ($is_on_sale) {
        $_product = $values['data'];
        $regular_price = $_product->get_regular_price();
        $price = '<span class="wpd-discount-price" style="text-decoration: line-through; opacity: 0.5; padding-right: 5px;">' . wc_price($regular_price) . '</span>' . $price;
    }
    return $price;
}
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Added Ms. salutation In Checkout Form 
 *
 */
add_filter('F4/WCSF/get_salutation_options', function ($options, $settings) {
    $options['ms'] = 'Ms.';
    return $options;
}, 10, 2);
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Added product Image into the Checkout page.
 *
 */
add_filter('woocommerce_cart_item_name', 'bbloomer_product_image_review_order_checkout', 9999, 3);

function bbloomer_product_image_review_order_checkout($name, $cart_item, $cart_item_key)
{
    if (! is_checkout()) return $name;
    $product = $cart_item['data'];
    $thumbnail = $product->get_image(array('50', '50'), array('class' => 'alignleft'));
    return $thumbnail . $name;
}
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Changes Lost your password statement in the my account login section
 *
 */
function change_lost_your_password($text)
{
    if ($text == 'Lost your password?') {
        $text = 'Forgot Your Password?';
    }
    return $text;
}
add_filter('gettext', 'change_lost_your_password');
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Move country Section after the PIN Code in Checkout page.
 *
 */
add_filter('woocommerce_default_address_fields', 'bbloomer_reorder_checkout_fields');
function bbloomer_reorder_checkout_fields($fields)
{
    // default priorities: // 'first_name' - 10 // 'last_name' - 20 // 'company' - 30 // 'country' - 40 // 'address_1' - 50 // 'address_2' - 60 // 'city' - 70 // 'state' - 80 // 'postcode' - 90
    // e.g. move 'company' above 'first_name': // just assign priority less than 10
    $fields['country']['priority'] = 91;
    return $fields;
}
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Remove the Order by popularity and Rating option from the dropdown in Shop Page.
 *
 */
add_filter('woocommerce_catalog_orderby', 'bbloomer_remove_sorting_option_woocommerce_shop');
function bbloomer_remove_sorting_option_woocommerce_shop($options)
{
    unset($options['popularity']);
    unset($options['rating']);
    return $options;
}











/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Redirect Page after the login on My Account Page.
 *
 */
add_shortcode('wc_login_form_bbloomer', 'bbloomer_separate_login_form');
function bbloomer_separate_login_form()
{
    if (is_admin()) return;
    if (is_user_logged_in()) return;
    ob_start();
    do_action('woocommerce_before_customer_login_form');
    woocommerce_login_form(array('redirect' => '/my-account'));
    return ob_get_clean();
}
/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Change WooCommerce Add to cart message with cart link.
 *
 */

function quadlayers_add_to_cart_message_html($message, $products)
{
    $count = 0;
    $titles = array();
    foreach ($products as $product_id => $qty) {
        $titles[] = sprintf(_x('%s', 'Item name in quotes', 'woocommerce'), strip_tags(get_the_title($product_id))) . ($qty > 1 ? ' &times; ' . absint($qty) : '');
        $count += $qty;
    }
    $titles     = array_filter($titles);
    $added_text = sprintf(_n(
        '%s  - added to your cart.', // Singular
        '%s  - added to your cart.', // Plural
        $count, // Number of products added
        'woocommerce' // Textdomain
    ), wc_format_list_of_items($titles));
    $message    = sprintf('<p class="addtocartmessage">%s</p><div class="addtocartbuttone"><a href="%s" class="button wc-forward">%s</a></div> ', esc_html($added_text), esc_url(wc_get_page_permalink('cart')), esc_html__('View cart', 'woocommerce'));

    return $message;
}
add_filter('wc_add_to_cart_message_html', 'quadlayers_add_to_cart_message_html', 10, 2);

/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Change Already registered user error statement.
 *
 */
add_filter('woocommerce_registration_error_email_exists', function ($html) {
    $html = str_replace('An account is already registered with your email address. <a href="#" class="showlogin">Please log in.</a>', 'An account is already registered with your email address. <a href="https://shop.studds.com/my-account/" class="showlogin">Please log in</a>', $html);
    return $html;
});
add_filter('woocommerce_billing_fields', 'remove_account_billing_phone_and_email_fields', 20, 1);
function remove_account_billing_phone_and_email_fields($billing_fields)
{
    // Only on my account 'edit-address'
    if (is_wc_endpoint_url('edit-address')) {
        unset($billing_fields['billing_company']);
    }
    return $billing_fields;
}

add_filter('woocommerce_shipping_fields', 'remove_shipping_phone_field', 20, 1);
function remove_shipping_phone_field($fields)
{
    //$fields ['shipping_phone']['required'] = false; // To be sure "NOT required"

    unset($fields['shipping_company']); // Remove shipping phone field
    return $fields;
}


add_action('woocommerce_variable_add_to_cart', 'bbloomer_update_price_with_variation_price');

function bbloomer_update_price_with_variation_price()
{
    global $product;
    $price = $product->get_price_html();
    wc_enqueue_js("      
      $(document).on('found_variation', 'form.cart', function( event, variation ) {   
         if(variation.price_html) $('.summary > p.price').html(variation.price_html);
         $('.woocommerce-variation-price').hide();
      });
      $(document).on('hide_variation', 'form.cart', function( event, variation ) {   
         $('.summary > p.price').html('" . $price . "');
      });
   ");
}

/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Pin Code and State Validation for Shipping and Billing Section on Checkout page.
 *
 */

add_action('woocommerce_checkout_process', 'wh_phoneValidateCheckoutFields');

function wh_phoneValidateCheckoutFields()
{
    //Shipping Pin Code and State Validation 
    $shipping_state = filter_input(INPUT_POST, 'shipping_state');
    $shipping_postcode = filter_input(INPUT_POST, 'shipping_postcode');
    if ($shipping_postcode != '') {
        if ($shipping_state === "AD") {

            //$shipping_postcode;
            $min = 507130;
            $max = 535594;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "AR") {

            //$shipping_postcode;
            $min = 790001;
            $max = 792131;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "AS") {

            //$shipping_postcode;
            $min = 781001;
            $max = 788931;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "BR") {

            //$shipping_postcode;
            $min = 800001;
            $max = 855117;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "CG") {

            //$shipping_postcode;
            $min = 490001;
            $max = 497778;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "GA") {

            //$shipping_postcode;
            $min = 403001;
            $max = 403806;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "GJ") {

            //$shipping_postcode;
            $min = 360001;
            $max = 396590;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "HR") {

            //$shipping_postcode;
            $min = 121001;
            $max = 136156;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "HP") {

            //$shipping_postcode;
            $min = 171001;
            $max = 177601;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "JK" or $shipping_state === "LA") {

            //$shipping_postcode;
            $min = 180001;
            $max = 194404;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "JH") {

            //$shipping_postcode;
            $min = 813208;
            $max = 835325;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "KA") {

            //$shipping_postcode;
            $min = 560001;
            $max = 591346;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "KL") {

            //$shipping_postcode;
            $min = 670001;
            $max = 695615;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "MP") {

            //$shipping_postcode;
            $min = 450001;
            $max = 488448;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "MH") {

            //$shipping_postcode;
            $min = 400001;
            $max = 445402;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "MN") {

            //$shipping_postcode;
            $min = 795001;
            $max = 795159;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "ML") {

            //$shipping_postcode;
            $min = 783123;
            $max = 794115;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "MZ") {

            //$shipping_postcode;
            $min = 796001;
            $max = 796901;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "NL") {

            //$shipping_postcode;
            $min = 797001;
            $max = 798627;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "OD") {

            //$shipping_postcode;
            $min = 751001;
            $max = 770076;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "PB") {

            //$shipping_postcode;
            $min = 140001;
            $max = 160104;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "RJ") {

            //$shipping_postcode;
            $min = 301001;
            $max = 345034;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "SK") {

            //$shipping_postcode;
            $min = 737101;
            $max = 737139;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "TN") {

            //$shipping_postcode;
            $min = 600001;
            $max = 643253;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "TS") {

            //$shipping_postcode;
            $min = 500001;
            $max = 509412;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "TR") {

            //$shipping_postcode;
            $min = 799001;
            $max = 799290;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "UK") {

            //$shipping_postcode;
            $min = 244712;
            $max = 263680;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "UP") {

            //$shipping_postcode;
            $min = 201001;
            $max = 285223;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "WB") {

            //$shipping_postcode;
            $min = 700001;
            $max = 743711;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "AN") {

            //$shipping_postcode;
            $min = 744101;
            $max = 744304;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "CH") {

            //$shipping_postcode;
            $min = 140119;
            $max = 160102;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "DN") {

            //$shipping_postcode;
            $min = 396193;
            $max = 396240;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "DD") {

            //$shipping_postcode;
            $min = 362520;
            $max = 396220;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "DL") {

            //$shipping_postcode;
            $min = 110001;
            $max = 110097;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "LD") {

            //$shipping_postcode;
            $min = 682551;
            $max = 682559;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
        if ($shipping_state === "PY") {

            //$shipping_postcode;
            $min = 533464;
            $max = 673310;
            $options = array("options" => array("min_range" => $min, "max_range" => $max));
            if (filter_var($shipping_postcode, FILTER_VALIDATE_INT, $options) === false) {

                wc_add_notice(__('<b>Shipping Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
            }
        }
    }
}


add_action('woocommerce_checkout_process', 'wh_shippingValidateCheckoutFields');
function wh_shippingValidateCheckoutFields()
{


    //Billing Pin Code and State Validation

    $billing_state = filter_input(INPUT_POST, 'billing_state');
    $billing_postcode = filter_input(INPUT_POST, 'billing_postcode');
    if ($billing_state === "AD") {

        //$shipping_postcode;
        $min = 507130;
        $max = 535594;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billin Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "AR") {

        //$shipping_postcode;
        $min = 790001;
        $max = 792131;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "AS") {

        //$shipping_postcode;
        $min = 781001;
        $max = 788931;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "BR") {

        //$shipping_postcode;
        $min = 800001;
        $max = 855117;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "CG") {

        //$shipping_postcode;
        $min = 490001;
        $max = 497778;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "GA") {

        //$shipping_postcode;
        $min = 403001;
        $max = 403806;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "GJ") {

        //$shipping_postcode;
        $min = 360001;
        $max = 396590;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "HR") {

        //$shipping_postcode;
        $min = 121001;
        $max = 136156;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "HP") {

        //$shipping_postcode;
        $min = 171001;
        $max = 177601;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "JK" or $billing_state === "LA") {

        //$shipping_postcode;
        $min = 180001;
        $max = 194404;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "JH") {

        //$shipping_postcode;
        $min = 813208;
        $max = 835325;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "KA") {

        //$shipping_postcode;
        $min = 560001;
        $max = 591346;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "KL") {

        //$shipping_postcode;
        $min = 670001;
        $max = 695615;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "MP") {

        //$shipping_postcode;
        $min = 450001;
        $max = 488448;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "MH") {

        //$shipping_postcode;
        $min = 400001;
        $max = 445402;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "MN") {

        //$shipping_postcode;
        $min = 795001;
        $max = 795159;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "ML") {

        //$shipping_postcode;
        $min = 783123;
        $max = 794115;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "MZ") {

        //$shipping_postcode;
        $min = 796001;
        $max = 796901;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "NL") {

        //$shipping_postcode;
        $min = 797001;
        $max = 798627;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "OD") {

        //$shipping_postcode;
        $min = 751001;
        $max = 770076;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "PB") {

        //$shipping_postcode;
        $min = 140001;
        $max = 160104;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "RJ") {

        //$shipping_postcode;
        $min = 301001;
        $max = 345034;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "SK") {

        //$shipping_postcode;
        $min = 737101;
        $max = 737139;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "TN") {

        //$shipping_postcode;
        $min = 600001;
        $max = 643253;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "TS") {

        //$shipping_postcode;
        $min = 500001;
        $max = 509412;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "TR") {

        //$shipping_postcode;
        $min = 799001;
        $max = 799290;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "UK") {

        //$shipping_postcode;
        $min = 244712;
        $max = 263680;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "UP") {

        //$shipping_postcode;
        $min = 201001;
        $max = 285223;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "WB") {

        //$shipping_postcode;
        $min = 700001;
        $max = 743711;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "AN") {

        //$shipping_postcode;
        $min = 744101;
        $max = 744304;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "CH") {

        //$shipping_postcode;
        $min = 140119;
        $max = 160102;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "DN") {

        //$shipping_postcode;
        $min = 396193;
        $max = 396240;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "DD") {

        //$shipping_postcode;
        $min = 362520;
        $max = 396220;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "DL") {

        //$shipping_postcode;
        $min = 110001;
        $max = 110097;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "LD") {

        //$shipping_postcode;
        $min = 682551;
        $max = 682559;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
    if ($billing_state === "PY") {

        //$shipping_postcode;
        $min = 533464;
        $max = 673310;
        $options = array("options" => array("min_range" => $min, "max_range" => $max));
        if (filter_var($billing_postcode, FILTER_VALIDATE_INT, $options) === false) {

            wc_add_notice(__('<b>Billing Details</b> - You have entered the wrong PIN code for the selected State'), 'error');
        }
    }
}

/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Validate Special Charecter in the City Field in both Shipping and Billing Section.
 *
 */
add_action('woocommerce_checkout_process', 'wh_BcityValidateCheckoutFields');
function wh_BcityValidateCheckoutFields()
{


    //Billing Pin Code and State Validation
    $searchForValue = ',';
    $billing_city = filter_input(INPUT_POST, 'billing_city');
    $billing_address_1 = filter_input(INPUT_POST, 'billing_address_1');
    $billing_address_2 = filter_input(INPUT_POST, 'billing_address_2');
    if (!preg_match("/^[0-9a-zA-Z_.\\s-]*$/", $billing_city)) {
        wc_add_notice(__('<b>Billing Details</b> - Check City Field - Only letters and white space allowed.'), 'error');
    }
    if (!preg_match('/^[a-zA-Z\\.0-9\/\\,\s_#@()&|:-]*$/', $billing_address_1)) {
        wc_add_notice(__('<b>Billing Details</b> - Check House Number and Street Name Field - Please remove special character or symbols from House Number and Street Name.'), 'error');
    }
    if (!preg_match('/^[a-zA-Z\\.0-9\/\\,\s_#@()&|:-]*$/', $billing_address_2)) {
        wc_add_notice(__('<b>Billing Details</b> - Check House Number and Street Name (Optional) Field - Please remove special character or symbols from House Number and Street Name (Optional).'), 'error');
    }
}
add_action('woocommerce_checkout_process', 'wh_ScityValidateCheckoutFields');
function wh_ScityValidateCheckoutFields()
{

    $shipping_city = filter_input(INPUT_POST, 'shipping_city');
    $shipping_address_1 = filter_input(INPUT_POST, 'shipping_address_1');
    $shipping_address_2 = filter_input(INPUT_POST, 'shipping_address_2');
    $ship_to_different_address = filter_input(INPUT_POST, 'ship_to_different_address');
    if ($shipping_city != '') {
        $searchForValue = ',';
        if (!preg_match("/^[0-9a-zA-Z_.\\s-]*$/", $shipping_city)) {
            wc_add_notice(__('<b>Shipping Details</b> - Check City Field - Only letters and white space allowed.'), 'error');
        }
    }
    if ($shipping_address_1 != '') {
        if (!preg_match('/^[a-zA-Z\\.0-9\/\\,\s_#@()&|:-]*$/', $shipping_address_1)) {
            wc_add_notice(__('<b>Shipping Details</b> - Check House Number and Street Name Field - Please remove special character or symbols from House Number and Street Name.'), 'error');
        }
    }
    if ($shipping_address_2 != '') {
        if (!preg_match('/^[a-zA-Z\\.0-9\/\\,\s_#@()&|:-]*$/', $shipping_address_2)) {
            wc_add_notice(__('<b>Shipping Details</b> - Check House Number and Street Name (Optional) Field - Please remove special character or symbols from House Number and Street Name (Optional).'), 'error');
        }
    }
}

/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Disable WooCommerce state select field from using Select2 JQuery
 *
 */
add_action('wp_enqueue_scripts', 'wsis_dequeue_stylesandscripts_select2', 100);

function wsis_dequeue_stylesandscripts_select2()
{
    if (class_exists('woocommerce')) {
        wp_dequeue_style('selectWoo');
        wp_deregister_style('selectWoo');

        wp_dequeue_script('selectWoo');
        wp_deregister_script('selectWoo');
    }
}

// add_filter( 'woocommerce_checkout_fields' , 'override_billing_checkout_fields', 20, 1 );
// function override_billing_checkout_fields( $fields ) {
//     $fields['billing']['billing_phone']['placeholder'] = 'Telefon';
//     // $fields['billing']['billing_email']['placeholder'] = 'Email';
//     return $fields;
// }

/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Sort Products By Stock Status - WooCommerce Shop
 *
 */
// add_filter( 'woocommerce_get_catalog_ordering_args', 'bbloomer_first_sort_by_stock_amount', 9999 );

// function bbloomer_first_sort_by_stock_amount( $args ) {
//   $args['orderby'] = 'meta_value';
//   $args['meta_key'] = '_stock_status';
//   return $args;
// }

add_filter('woocommerce_get_catalog_ordering_args', 'custom_sort_by_stock_and_title', 9999);

function custom_sort_by_stock_and_title($args)
{
    // Check if we're on a search page and the search query contains "visor"
    if (is_search() && isset($_GET['s']) && strpos($_GET['s'], 'visor') !== false) {
        // Prioritize products with "visor" in their titles first
        $args['orderby'] = 'title';
        $args['order'] = 'ASC';
    } else {
        // Default sorting by stock status for shop and category pages
        $args['orderby'] = 'meta_value';
        $args['meta_key'] = '_stock_status';
    }

    return $args;
}


// add_filter( 'woocommerce_get_catalog_ordering_args', 'modify_catalog_order_for_woocommerce', 9999 );

// function modify_catalog_order_for_woocommerce( $args ) {
//     // Check if we're on a shop or archive page
//     if ( is_shop() || is_product_category() || is_product_tag() || is_tax() ) {
//         $args['orderby'] = 'meta_value title'; // Sort by meta value and title
//         $args['order'] = 'ASC'; // Show in-stock products first
//         $args['meta_key'] = '_stock_status'; // Meta key for stock status
//     }
//     return $args;
// }


/**
 *
 *  @author     Rishabh, STUDDS
 *  @link       https://studds.com
 *  @link       https://studds.com
 *  @snippet    Redirect on My Account Login page. If user entered wrong credentials
 *
 */

add_action('wp_login_failed', 'my_front_end_login_fail');  // hook failed login

function my_front_end_login_fail($username)
{
    $referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
    // if there's a valid referrer, and it's not the default log-in screen
    if (!empty($referrer) && !strstr($referrer, 'wp-login') && !strstr($referrer, 'wp-admin')) {
        wp_redirect($referrer . '?login=failed');  // let's append some information (login=failed) to the URL for the theme to use
        exit;
    }
}
// add_filter( 'woocommerce_attribute_label', 'custom_attribute_label', 10, 3 );
// function custom_attribute_label( $label, $name, $product ) {
//     // For "pa_farge" attribute taxonomy on single product pages.
//     if( $name == 'Weight' && is_product() ) {
//         $label = __('NEW NAME', 'woocommerce');
//     }
//     return $label;
// }

add_action('wp_loaded', 'my_remove_bulk_actions');
function my_remove_bulk_actions()
{
    if (! is_admin())
        return;

    if (! current_user_can('administrator')) {
        add_filter('bulk_actions-edit-shop_order', '__return_empty_array', 100);
    }
}

function wp23958_remove_shop_manager_capabilities()
{
    $shop_manager = get_role('sales'); // Target user role
    //List of capabilities which we want to edit
    $caps = array(
        'delete_shop_orders',
        'delete_private_shop_orders',
        'delete_published_shop_orders',
        'delete_others_shop_orders',
    );
    // Remove capabilities from our list 
    foreach ($caps as $cap) {
        $shop_manager->remove_cap($cap);
    }
}

/**
 * @snippet       Show $0.00 Price Beside Free Shipping Rates 

 */

add_filter('woocommerce_cart_shipping_method_full_label', 'bbloomer_add_0_to_shipping_label', 9999, 2);

function bbloomer_add_0_to_shipping_label($label, $method)
{
    if (! ($method->cost > 0)) {
        $label .= ': ' . wc_price(0);
    }
    return $label;
}
add_filter('woocommerce_order_shipping_to_display', 'bbloomer_add_0_to_shipping_label_ordered', 9999, 3);

function bbloomer_add_0_to_shipping_label_ordered($shipping, $order, $tax_display)
{
    if (! (0 < abs((float) $order->get_shipping_total())) && $order->get_shipping_method()) {
        $shipping .= ': ' . wc_price(0);
    }
    return $shipping;
}

/**
 * @snippet       PERCENTAGE COUPON DISCOUNT DECIMAL AMOUNT ISSUE 

 */

add_filter('woocommerce_coupon_get_discount_amount', 'filter_woocommerce_coupon_get_discount_amount', 10, 5);
function filter_woocommerce_coupon_get_discount_amount($discount, $discounting_amount, $cart_item, $single, $instance)
{
    // Round the discount for all other coupon types than 'fixed_cart'
    if (! $instance->is_type('fixed_cart'))
        $discount = round($discount);

    return $discount;
}

/**
 * Plugin Name: Admitad tracking code
 * Version: 1.2
 * Description: This plugin will add Tag tracking code to WooCommerce.
 */
function wp_tagtag_tracking()
{
    ?>
    <script src='https://www.artfut.com/static/tagtag.min.js?campaign_code=3fa05b148a' onerror='var self = this;window.ADMITAD=window.ADMITAD||{},ADMITAD.Helpers=ADMITAD.Helpers||{},ADMITAD.Helpers.generateDomains=function(){for(var e=new Date,n=Math.floor(new Date(2020,e.getMonth(),e.getDate()).setUTCHours(0,0,0,0)/1e3),t=parseInt(1e12*(Math.sin(n)+1)).toString(30),i=[' de'],o=[],a=0;a<i.length;++a)o.push({domain:t++i[a],name:t});return o},ADMITAD.Helpers.findTodaysDomain=function(e){function n(){var o=new XMLHttpRequest,a=i[t].domain,D='https://' +a+'/';o.open('HEAD',D,!0),o.onload=function(){setTimeout(e,0,i[t])},o.onerror=function(){++t<i.length?setTimeout(n,0):setTimeout(e,0,void 0)},o.send()}var t=0,i=ADMITAD.Helpers.generateDomains();n()},window.ADMITAD=window.ADMITAD||{},ADMITAD.Helpers.findTodaysDomain(function(e){if(window.ADMITAD.dynamic=e,window.ADMITAD.dynamic){var n=function(){return function(){return self.src?self:''}}(),t=n(),i=(/campaign_code=([^&]+)/.exec(t.src)||[])[1]||'';t.parentNode.removeChild(t);var o=document.getElementsByTagName('head')[0],a=document.createElement('script');a.src='https://www.' +window.ADMITAD.dynamic.domain+'/static/'+window.ADMITAD.dynamic.name.slice(1)+window.ADMITAD.dynamic.name.slice(0,1)+'.min.js?campaign_code='+i,o.appendChild(a)}});'></script>

    <script type='text/javascript'>
        var cookie_name = 'deduplication_cookie';
        var days_to_store = 30;
        var deduplication_cookie_value = 'admitad';
        var channel_name = 'utm_source';
        getSourceParamFromUri = function() {
            var pattern = channel_name + '=([^&]+)';
            var re = new RegExp(pattern);
            return (re.exec(document.location.search) || [])[1] || ''
        };
        getSourceCookie = function() {
            var matches = document.cookie.match(new RegExp('(?:^|; )' + cookie_name.replace(/([\.$?*|{}\(\)\[\]\\/\+^])/g, '\$1') + '=([^;]*)'));
            return matches ? decodeURIComponent(matches[1]) : undefined
        };
        setSourceCookie = function() {
            var param = getSourceParamFromUri();
            var params = (new URL(document.location)).searchParams;
            if (!params.get(channel_name) && params.get('gclid')) {
                param = 'advAutoMarkup'
            } else if (!params.get(channel_name) && params.get('fbclid')) {
                param = 'facebook'
            } else if (!param) {
                return
            }
            var period = days_to_store * 60 * 60 * 24 * 1000;
            var expiresDate = new Date((period) + +new Date);
            var cookieString = cookie_name + '=' + param + '; path=/; expires=' + expiresDate.toGMTString();
            document.cookie = cookieString;
            document.cookie = cookieString + '; domain=.' + location.host
        };
        setSourceCookie();
        if (!getSourceCookie(cookie_name)) {
            ADMITAD.Invoice.broker = 'na'
        } else if (getSourceCookie(cookie_name) != deduplication_cookie_value) {
            ADMITAD.Invoice.broker = getSourceCookie(cookie_name)
        } else {
            ADMITAD.Invoice.broker = 'adm'
        }
    </script>
    <?php
    $current_user = wp_get_current_user();
    if (! is_user_logged_in())
        return;
    if (! get_transient($current_user->user_login))
        return;
    ?>
    <script type='text/javascript'>
        ADMITAD = window.ADMITAD || {};
        ADMITAD.Invoice = ADMITAD.Invoice || {};
        ADMITAD.Invoice.accountId = '<?php echo $current_user->user_email; ?>';
    </script>
<?php
    delete_transient($current_user->user_login);
}
add_action('wp_head', 'wp_tagtag_tracking');
add_action('woocommerce_thankyou', 'conversion_tracking_thank_you_page');
function conversion_tracking_thank_you_page($order_id)
{
    $current_user = wp_get_current_user();
    $order = wc_get_order($order_id);
    $order_data = $order->get_data();
    $currency = $order->get_currency();
    $order_items = $order->get_items();
    $coupon_code = $order->get_coupon_codes();
    $discount_code = !empty($coupon_code) ? $coupon_code[0] : '';
?>
    <script type='text/javascript'>
        ADMITAD = window.ADMITAD || {};
        ADMITAD.Invoice = ADMITAD.Invoice || {};
        ADMITAD.Invoice.accountId = '<?php echo $current_user->user_email; ?>';
        ADMITAD.Invoice.category = '1';
        var orderedItems = [];
        <?php foreach ($order_items as $item_id => $item) :
            $item_data = $item->get_data();
            $product = $item->get_product();
            $item_type = $item_data['product_id'] > 0 ? 'product' : 'variation';
            $product_id = $item_type === 'product' ? $item_data['product_id'] : $product->get_id();
            $price = $item_data['subtotal'];
            if ($item_type === 'variation') {
                $variation_id = $item_data['variation_id'];
                $variation = wc_get_product($variation_id);
                if ($variation) {
                    $product_id = $variation_id;
                    $price = $item_data['subtotal'] / $item_data['quantity'];
                }
            }
        ?>
            orderedItems.push({
                Product: {
                    productID: '<?php echo $product_id; ?>',
                    category: '1',
                    price: '<?php echo $price; ?>',
                    priceCurrency: '<?php echo $currency; ?>',
                },
                orderQuantity: '<?php echo $item_data['quantity']; ?>',
                additionalType: 'sale'
            });
        <?php endforeach; ?>
        ADMITAD.Invoice.referencesOrder = ADMITAD.Invoice.referencesOrder || [];
        ADMITAD.Invoice.referencesOrder.push({
            orderNumber: '<?php echo $order_data['id']; ?>',
            discountCode: '<?php echo $discount_code; ?>',
            orderedItem: orderedItems
        });
    </script>
<?php
}
function cross_device_wp_login($user_login)
{
    set_transient($user_login, '1', 0);
}
add_action('wp_login', 'cross_device_wp_login');

// Thunder CR attributes
function custom_display_product_attributes($product_attributes, $product)
{
    // Get the product model attribute value
    $model = $product->get_attribute('model');

    // Check if the product has a specific model
    if (!empty($model === 'Thunder Bluetooth')) {
        // Check the model value and add net quantity accordingly
        $product_attributes['battery_heading'] = [
            'label' => '<span style="width: 200px; display: inline-block;">' . __('<span style="font-size:18px;">Battery Specification:</span>', 'text-domain') . '</span>',

        ];

        if ($model === 'Thunder Bluetooth') {
            $product_attributes['Battery Capacity'] = [
                'label' => __('Capacity', 'text-domain'),
                'value' => '3.7V 230mAh',
            ];
            $product_attributes['Battery Type'] = [
                'label' => __('Battery Type', 'text-domain'),
                'value' => 'Lithium-ion Rechargeable Battery',
            ];
            $product_attributes['Battery Country of Origin'] = [
                'label' => __('Country of Origin', 'text-domain'),
                'value' => 'China',
            ];
            $product_attributes['Battery Manufactured By'] = [
                'label' => __('Manufactured By', 'text-domain'),
                'value' => 'Dongguan Wiliyoun Electronics Co <br>Address: Jinhe Community, Zhangmutou Town, Dongguan City, Guangdong Province, 523041 Dongguan, China',
            ];
            $product_attributes['Battery Packed/ Imported By'] = [
                'label' => __('Packed/ Imported By', 'text-domain'),
                'value' => 'Studds Accessories Limited, <br>Regd. Office: Plot No. 918, Sector-68, IMT, Faridabad, Haryana- 121004',
            ];
        } else {
            // Default net quantity for other models
            $product_attributes['net_quantity'] = [
                'label' => __('Net Quantity', 'text-domain'),
                'value' => '1 <span class="gloves">Number</span>',
            ];
        }
    }

    return $product_attributes;
}
add_filter('woocommerce_display_product_attributes', 'custom_display_product_attributes', 10, 2);
// Thunder CR attributes

/**
 * @snippet       Readmore Short Description

 */


add_action('woocommerce_after_single_product', 'studds_woocommerce_short_description_truncate_read_more');

function studds_woocommerce_short_description_truncate_read_more()
{
    wc_enqueue_js('
      var show_char = 208;
      var ellipses = "... ";
      var content = $(".woocommerce-product-details__short-description").html();
      if (content.length > show_char) {
         var a = content.substr(0, show_char);
         var b = content.substr(show_char - content.length);
         var html = a + "<span class=\'truncated\'>" + ellipses + "<a class=\'read-more\'>Read more</a></span><span class=\'truncated\' style=\'display:none\'>" + b + "</span>";
         $(".woocommerce-product-details__short-description").html(html);
      }
      $(".read-more").click(function(e) {
         e.preventDefault();
         $(".woocommerce-product-details__short-description .truncated").toggle();
      });
   ');
}

/**
 * @snippet       Display Mobile input field after the email field on checkout page.

 */

add_filter('woocommerce_billing_fields', 'move_mobile_after_email_on_checkout');

function move_mobile_after_email_on_checkout($fields)
{
    // Check if the billing_phone field exists
    if (isset($fields['billing_phone'])) {
        $mobile_field = $fields['billing_phone'];
        // Remove the billing_phone field from its original position
        unset($fields['billing_phone']);
        // Reinsert the billing_phone field after the email field
        $fields['billing_email']['priority'] = 100;
        $fields['billing_phone'] = $mobile_field;
    }
    return $fields;
}

/**
 
 @snippet       Cross Sell Custom Module with Slider

 **/
function custom_cross_sell_product_slider()
{
    $cross_sell_ids = WC()->cart->get_cross_sells();
    if (empty($cross_sell_ids)) return;
?>
    <div class="cross-sell-container" style="margin-bottom: 25px;">
        <h4 class="heading-title" style="background: #efefef; padding: 10px;">YOU MAY ALSO LIKE</h4>
        <div class="cross-sell-slider" style="display: block;">
            <?php foreach ($cross_sell_ids as $cross_sell_id) : ?>
                <?php
                $product = wc_get_product($cross_sell_id);
                if (!$product || !$product->is_in_stock()) {
                    continue;
                }
                ?>
                <div class="product-wrapper-cross">
                    <?php
                    $image_id = $product->get_image_id();
                    $image_url = wp_get_attachment_image_url($image_id, 'small');
                    echo '<a href="' . esc_url($product->get_permalink()) . '"><img src="' . esc_url($image_url) . '" alt="' . esc_attr($product->get_name()) . '"></a>';
                    echo '<h5 class="heading-title product-name product-name-cross-sell" style="margin: 0 0 10px 0;">' . $product->get_name() . '</h5>';
                    echo '<p class="price" style="padding:0px !important;">' . $product->get_price_html() . '</p>';
                    if ($product->is_type('simple')) {
                        echo '<a class="cross_sell_product" href="' . esc_url($product->add_to_cart_url()) . '" data-quantity="1" >Add to Cart</a>';
                    } elseif ($product->is_type('variable')) {
                        echo '<a class="cross_sell_product" href="' . esc_url(get_permalink($product->get_id())) . '" target="_blank">View Product</a>';
                    }
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        jQuery(function($) {
            $('.cross-sell-slider').owlCarousel({
                items: 4,
                loop: true,
                autoplay: true,
                autoplaySpeed: 1000,
                autoplayHoverPause: false,
                nav: true,
                dots: false,
                margin: 10,
                responsive: {
                    0: {
                        items: 2
                    },
                    768: {
                        items: 3
                    },
                    992: {
                        items: 5

                    }
                }
            });
            // Function to update cross-sell section
            function updateCrossSellSection() {
                var crossSellContainer = $('.cross-sell-container');
                var crossSellIds = <?php echo json_encode($cross_sell_ids); ?>;
                if (crossSellIds.length === 0) {
                    crossSellContainer.hide(); // Hide cross-sell section if no products
                } else {
                    crossSellContainer.show(); // Show cross-sell section if products available
                }
            }

            // Initial update
            updateCrossSellSection();

            // Update cross-sell section when cart is updated
            $(document.body).on('updated_cart_totals', function() {
                updateCrossSellSection();
            });
        });
    </script>
<?php
}
add_action('woocommerce_before_cart_collaterals', 'custom_cross_sell_product_slider');

/**
 
 @snippet       Send an email to customer regarding the product review after 2 days of Delivery.
 @Function 1st  Schedule Cron Job for 2 days delayed 
 @Function 2nd  Send an email when cron job run with Product review page link.

 **/
add_action('woocommerce_order_status_completed', 'schedule_review_links_email_with_local_time', 10, 1);

function schedule_review_links_email_with_local_time($order_id)
{
    // Check if the order exists
    if (!$order_id) {
        error_log("No order ID provided for scheduling email.");
        return;
    }

    // Calculate the time difference in seconds (IST is UTC+5:30)
    $time_difference = 5 * 60 * 60 + 30 * 60; // 5 hours and 30 minutes

    // Get the current server time
    $server_time = time();
    $local_time = $server_time + $time_difference; // Convert server time to local time

    // Convert the desired execution time to server time
    $execution_time = $server_time + 60 - $time_difference; // 1 minute after local time

    // Ensure execution time is in the future
    if ($execution_time < $server_time) {
        //error_log("Execution time is in the past. Adjusting time.");
        $execution_time = $server_time + (2 * 24 * 60 * 60); // Set to 1 minute in the future
        //$execution_time = $server_time + 60; // IN UAT its 60 added
    }

    // Schedule the email to be sent after the adjusted time
    if (!wp_next_scheduled('send_review_links_email', array($order_id))) {
        wp_schedule_single_event($execution_time, 'send_review_links_email', array($order_id));
        error_log("Scheduled review links email for order ID: " . $order_id . " at " . date('Y-m-d H:i:s', $execution_time) . " server time.");
    } else {
        //error_log("Email for order ID: " . $order_id . " is already scheduled.");
    }
}

add_action('send_review_links_email', 'send_review_links_email_function', 10, 1);

function send_review_links_email_function($order_id)
{

    // Get the current server time
    $server_time = time();
    $time_difference = 5 * 60 * 60 + 30 * 60; // 5 hours and 30 minutes
    $local_time = $server_time + $time_difference; // Convert server time to local time

    // Get the order
    $order = wc_get_order($order_id);
    if (!$order) {
        //error_log("Order ID: " . $order_id . " not found.");
        return; // Exit if the order is not found
    }

    $email = $order->get_billing_email(); // Get customer email
    $customer_first_name = $order->get_billing_first_name(); // Get customer's first name
    $customer_last_name = $order->get_billing_last_name();
    $items = $order->get_items(); // Get order items

    // Initialize email content
    $subject = 'Thank you for your purchase! Leave a review';
    $message = '<!DOCTYPE html>
<html lang="en-US" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>STUDDS Accessories Limited</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--[if !mso]><meta http-equiv="X-UA-Compatible" content="IE=edge"><![endif]--><!--[if (mso 16)]><style type="text/css"> a { text-decoration: none; } span { vertical-align: middle; } </style><![endif]--><!--[if mso]><xml><o:OfficeDocumentSettings><o:AllowPNG/></o:OfficeDocumentSettings></xml><![endif]--><!--[if mso | IE]><style type="text/css"> .viwec-responsive { width: 100% !important; } small { display: block; font-size: 13px; } table { font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif; } </style><![endif]-->
    <style type="text/css">@media only screen and (min-width:380px){a{text-decoration:none}td{overflow:hidden}table{font-family:Roboto,RobotoDraft,Helvetica,Arial,sans-serif}.viwec-responsive-min-width{min-width:600px}}@media only screen and (max-width:380px){a{text-decoration:none}td{overflow:hidden}table{font-family:Roboto,RobotoDraft,Helvetica,Arial,sans-serif}img{padding-bottom:10px}.viwec-button-responsive,.viwec-responsive,.viwec-responsive table{width:100%!important;min-width:100%}.viwec-responsive-padding{padding:0!important}.viwec-mobile-50{width:50%!important}}</style>
  </head>
  <body vlink="#FFFFFF" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    <div id="wrapper" style="box-sizing: border-box; padding: 0; margin: 0; background-color: #ffffff; background-image: none;" bgcolor="#ffffff">
      <table border="0" cellpadding="0" cellspacing="0" height="100%" align="center" width="100%" style="margin: 0;">
        <tbody>
          <tr>
            <td style="padding: 20px 10px;">
              <table border="0" cellpadding="0" cellspacing="0" height="100%" align="center" width="600" style="font-size: 15px; margin: 0 auto; padding: 0; border-collapse: collapse; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif;">
                <tbody>
                  <tr>
                    <td align="center" valign="top" id="body_content" style="background-color: #ffffff; background-image: none; background-size: cover;" bgcolor="#ffffff">
                      <div class="viwec-responsive-min-width">
                        <table align="center" width="600" border="0" cellpadding="0" cellspacing="0">
                          <tr>
                            <td class="viwec-row" valign="top" width="600" style="background-repeat: no-repeat; background-size: cover; background-position: top; padding: 15px 15px 0px; background-image: none; background-color: #ed3237; border-radius: 0px; width: 600px; border-top: 0 hidden; border-right: 0 hidden; border-bottom: 0 hidden; border-left: 0 hidden;" bgcolor="#ed3237">
                              <table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin: 0; padding: 0;">
                                <tr>
                                  <td valign="top" width="100%" class="viwec-responsive-padding viwec-inline-block" border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; border-collapse: collapse; margin: 0; padding: 0; font-size: 0;">
                                    <!--[if mso | IE]>
																					<table width="100%" role="presentation" border="0" cellpadding="0" cellspacing="0">
																						<tr>
																							<td valign="top" class="" style="vertical-align:top;width:100%;">
																								<![endif]-->
                                    <table align="left" width="100%" class="viwec-responsive" border="0" cellpadding="0" cellspacing="0" style="margin: 0; padding: 0; border-collapse: collapse;">
                                      <tr>
                                        <td>
                                          <table width="100%" align="left" border="0" cellpadding="0" cellspacing="0" style="margin: 0; padding: 0; border-collapse: collapse; width: 100%;">
                                            <tr>
                                              <td class="viwec-column" valign="top" width="570" style="line-height: 1.5; padding: 0px; background-image: none; background-color: transparent; border-radius: 0px; width: 570px; border-top: 0 hidden; border-right: 0 hidden; border-bottom: 0 hidden; border-left: 0 hidden;" bgcolor="transparent">
                                                <table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                                                  <tr>
                                                    <td valign="top" style="">
                                                      <table class="html_image" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                                                        <tr>
                                                          <td valign="top" dir="ltr" style="font-size: 15px; width: 570px; text-align: center; padding: 0px; background-image: none; background-color: transparent;" width="570" align="center" bgcolor="transparent">
                                                            <img alt="" width="100" src="https://shop.studds.com/wp-content/uploads/2023/02/logo-sm-1.png" max-width="100%" style="border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; background-color: transparent; max-width: 100%; vertical-align: middle; width: 100px;" border="0" bgcolor="transparent">
                                                          </td>
                                                        </tr>
                                                      </table>
                                                    </td>
                                                  </tr>
                                                </table>
                                              </td>
                                            </tr>
                                          </table>
                                        </td>
                                      </tr>
                                    </table>
                                    <!--[if mso | IE]>
																								</td>
																							</tr>
																						</table>
																						<![endif]-->
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                          <tr>
                            <td class="viwec-row" valign="top" width="600" style="background-repeat: no-repeat; background-size: cover; background-position: top; padding: 0px 35px 10px; background-image: none; background-color: #f9f9f9; border-radius: 0px; width: 600px; border-top: 0 hidden; border-right: 1px solid #444444; border-bottom: 0 hidden; border-left: 1px solid #444444;" bgcolor="#f9f9f9">
                              <table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin: 0; padding: 0;">
                                <tr>
                                  <td valign="top" width="100%" class="viwec-responsive-padding viwec-inline-block" border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; border-collapse: collapse; margin: 0; padding: 0; font-size: 0;">
                                    <!--[if mso | IE]>
																						<table width="100%" role="presentation" border="0" cellpadding="0" cellspacing="0">
																							<tr>
																								<td valign="top" class="" style="vertical-align:top;width:100%;">
																									<![endif]-->
                                    <table align="left" width="100%" class="viwec-responsive" border="0" cellpadding="0" cellspacing="0" style="margin: 0; padding: 0; border-collapse: collapse;">
                                      <tr>
                                        <td>
                                          <table width="100%" align="left" border="0" cellpadding="0" cellspacing="0" style="margin: 0; padding: 0; border-collapse: collapse; width: 100%;">
                                            <tr>
                                              <td class="viwec-column" valign="top" width="528" style="line-height: 1.5; padding: 0px; background-image: none; background-color: transparent; border-radius: 0px; width: 528px; border-top: 0 hidden; border-right: 0 hidden; border-bottom: 0 hidden; border-left: 0 hidden;" bgcolor="transparent">
                                                <table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                                                  <tr>
                                                    <td valign="top" style="">
                                                      <table class="html_text" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                                                        <tr>
                                                          <td valign="top" dir="ltr" style="font-size: 15px; width: 528px; line-height: 30px; background-image: none; padding: 35px 0px 0px; border-radius: 0px; border-top: 0 hidden; border-right: 0 hidden; border-bottom: 0 hidden; border-left: 0 hidden;" width="528">
                                                            <p style="display: block; margin-bottom: 12px; line-height: inherit; text-align: left;" align="left">
                                                              <span style="color: #444444; font-size: 24px;"><b>Hi ' . $customer_first_name . '&nbsp;' . $customer_last_name . ',</b></span><br>
                                                              <span style="color: #444444; font-size: 15px;">We hope you’re enjoying your new purchase!</span>
                                                            </p>
                                                            <p style="display: block; margin: 0; line-height: 20px; text-align: left;" align="left">
                                                                <span style="color: #444444; font-size: 15px;">We are thrilled to inform you that your order has been successfully delivered. We would love to hear your thoughts on the product.</span>
                                                                <br><br>
                                                                <span style="color: #444444; font-size: 15px;">Could you please take a moment to leave us a review? Your feedback helps us to improve and serve you better.</span>
                                                            </p>
                                                            <p style="display: block; margin: 0; line-height: 20px; text-align: left;" align="left">
                                                               <ul style="color: #444444;">';
    foreach ($items as $item) {
        $product_id = $item->get_product_id();
        $product_name = $item->get_name();
        $product_url = get_permalink($product_id);
        $review_link = $product_url . '#reviews'; // Link to reviews section

        $message .= "<li><a href='{$review_link}' style='color:red;'>{$product_name}</a></li>";
    }
    $message .= '</ul>
                                                                
                                                                   
                                                              
                                                               <span style="color: #444444;">Thank you for choosing STUDDS!</span>
                                                            </p>
                                                          </td>
                                                        </tr>
                                                      </table>
                                                    </td>
                                                  </tr>
                                                </table>
                                                
                                                
                                              </td>
                                            </tr>
                                          </table>
                                        </td>
                                      </tr>
                                    </table>
                                    <!--[if mso | IE]>
																								</td>
																							</tr>
																						</table>
																						<![endif]-->
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                          
                         
                          
                         
                          <tr>
                            <td class="viwec-row" valign="top" width="600" style="background-repeat: no-repeat; background-size: cover; background-position: top; padding: 25px 35px; background-image: none; background-color: #ed3237; border-radius: 0px; width: 600px; border-top: 0 hidden; border-right: 0 hidden; border-bottom: 0 hidden; border-left: 0 hidden;" bgcolor="#ed3237">
                              <table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin: 0; padding: 0;">
                                <tr>
                                  <td valign="top" width="100%" class="viwec-responsive-padding viwec-inline-block" border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; border-collapse: collapse; margin: 0; padding: 0; font-size: 0;">
                                    <!--[if mso | IE]>
																						<table width="100%" role="presentation" border="0" cellpadding="0" cellspacing="0">
																							<tr>
																								<td valign="top" class="" style="vertical-align:top;width:100%;">
																									<![endif]-->
                                    <table align="left" width="100%" class="viwec-responsive" border="0" cellpadding="0" cellspacing="0" style="margin: 0; padding: 0; border-collapse: collapse;">
                                      <tr>
                                        <td>
                                          <table width="100%" align="left" border="0" cellpadding="0" cellspacing="0" style="margin: 0; padding: 0; border-collapse: collapse; width: 100%;">
                                            <tr>
                                              <td class="viwec-column" valign="top" width="530" style="line-height: 1.5; padding: 0px; background-image: none; background-color: transparent; border-radius: 0px; width: 530px; border-top: 0 hidden; border-right: 0 hidden; border-bottom: 0 hidden; border-left: 0 hidden;" bgcolor="transparent">
                                                <table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                                                  <tr>
                                                    <td valign="top" style="">
                                                      <table class="html_text" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                                                        <tr>
                                                          <td valign="top" dir="ltr" style="font-size: 15px; width: 530px; line-height: 22px; background-image: none; padding: 0px; border-radius: 0px; border-top: 0 hidden; border-right: 0 hidden; border-bottom: 0 hidden; border-left: 0 hidden;" width="530">
                                                            <p style="display: block; margin: 0; line-height: inherit; text-align: center;" align="center">
                                                              <span style="color: #ffffff; font-size: 18px;">STAY CONNECTED</span>
                                                            </p>
                                                          </td>
                                                        </tr>
                                                      </table>
                                                    </td>
                                                  </tr>
                                                </table>
                                                <table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                                                  <tr>
                                                    <td valign="top" style="">
                                                      <table class="html_social" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                                                        <tr>
                                                          <td valign="top" dir="ltr" style="font-size: 15px; width: 530px; text-align: center; padding: 10px 0px; background-image: none;" width="530" align="center">
                                                            <table align="center" border="0" cellpadding="0" cellspacing="0">
                                                              <tr>
                                                                <td valign="top" style="padding: 0;">
                                                                  <a href="https://www.facebook.com/StuddsAccessoriesLtd/" style="text-decoration: none; word-break: break-word;">
                                                                    <img src="https://shop.studds.com/wp-content/plugins/email-template-customizer-for-woo/assets/img/fb-blue-white.png" width="32" style="border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; vertical-align: middle; background-color: transparent; max-width: 100%;" border="0" bgcolor="transparent">
                                                                  </a>
                                                                </td>
                                                                <td valign="top" style="padding: 0;">
                                                                  <a href="https://twitter.com/StuddsHelmet" style="text-decoration: none; word-break: break-word;">
                                                                    <img src="https://shop.studds.com/wp-content/plugins/email-template-customizer-for-woo/assets/img/twi-cyan-white.png" width="32" style="border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; vertical-align: middle; background-color: transparent; max-width: 100%;" border="0" bgcolor="transparent">
                                                                  </a>
                                                                </td>
                                                                <td valign="top" style="padding: 0;">
                                                                  <a href="https://www.instagram.com/studdshelmets/" style="text-decoration: none; word-break: break-word;">
                                                                    <img src="https://shop.studds.com/wp-content/plugins/email-template-customizer-for-woo/assets/img/ins-color-white.png" width="32" style="border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; vertical-align: middle; background-color: transparent; max-width: 100%;" border="0" bgcolor="transparent">
                                                                  </a>
                                                                </td>
                                                              </tr>
                                                            </table>
                                                          </td>
                                                        </tr>
                                                      </table>
                                                    </td>
                                                  </tr>
                                                </table>
                                                <table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                                                  <tr>
                                                    <td valign="top" style="">
                                                      <table class="html_text" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                                                        <tr>
                                                          <td valign="top" dir="ltr" style="font-size: 15px; width: 530px; line-height: 22px; background-image: none; padding: 0px; border-radius: 0px; border-top: 0 hidden; border-right: 0 hidden; border-bottom: 0 hidden; border-left: 0 hidden;" width="530">
                                                            <p style="display: block; margin: 0; line-height: inherit; text-align: center;" align="center">
                                                              <a href="https://shop.studds.com/my-account/contact-us/" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Need Help?&nbsp; | &nbsp;</a>
                                                              <a href="mailto:customercare@studds.com" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">customercare@studds.com</a>
                                                              <span style="color: #ffffff;">&nbsp; |&nbsp;</span>
                                                              <a href="tel:01294296555" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">&nbsp;</a>
                                                              <a href="tel:%C2%A001294296500" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; font-family: Roboto; font-size: 11.5pt;">
                                                                <span style="color: white;">&nbsp;</span>
                                                              </a>
                                                              <span style="font-family: Roboto; font-size: 11.5pt; color: white;">
                                                                <a title="tel:01294296555" href="tel:01294296555" target="_blank" rel="noreferrer noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">0129-4296555</a> &nbsp; </span>
                                                            </p>
                                                          </td>
                                                        </tr>
                                                      </table>
                                                    </td>
                                                  </tr>
                                                </table>
                                              </td>
                                            </tr>
                                          </table>
                                        </td>
                                      </tr>
                                    </table>
                                    <!--[if mso | IE]>
																											</td>
																										</tr>
																									</table>
																									<![endif]-->
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                          <tr>
                            <td class="viwec-row" valign="top" width="600" style="background-repeat: no-repeat; background-size: cover; background-position: top; padding: 15px 35px; background-image: none; background-color: #ed3237; border-radius: 0px; width: 600px; border-top: 1px solid #ffffff; border-right: 0 hidden; border-bottom: 0 hidden; border-left: 0 hidden;" bgcolor="#ed3237">
                              <table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin: 0; padding: 0;">
                                <tr>
                                  <td valign="top" width="100%" class="viwec-responsive-padding viwec-inline-block" border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; border-collapse: collapse; margin: 0; padding: 0; font-size: 0;">
                                    <!--[if mso | IE]>
																									<table width="100%" role="presentation" border="0" cellpadding="0" cellspacing="0">
																										<tr>
																											<td valign="top" class="" style="vertical-align:top;width:100%;">
																												<![endif]-->
                                    <table align="left" width="100%" class="viwec-responsive" border="0" cellpadding="0" cellspacing="0" style="margin: 0; padding: 0; border-collapse: collapse;">
                                      <tr>
                                        <td>
                                          <table width="100%" align="left" border="0" cellpadding="0" cellspacing="0" style="margin: 0; padding: 0; border-collapse: collapse; width: 100%;">
                                            <tr>
                                              <td class="viwec-column" valign="top" width="530" style="line-height: 1.5; padding: 0px; background-image: none; background-color: transparent; border-radius: 0px; width: 530px; border-top: 0 hidden; border-right: 0 hidden; border-bottom: 0 hidden; border-left: 0 hidden;" bgcolor="transparent">
                                                <table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                                                  <tr>
                                                    <td valign="top" style="">
                                                      <table class="html_text" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                                                        <tr>
                                                          <td valign="top" dir="ltr" style="font-size: 15px; width: 530px; line-height: 22px; background-image: none; background-color: transparent; padding: 0px 0px 10px; border-radius: 0px; border-top: 0 hidden; border-right: 0 hidden; border-bottom: 0 hidden; border-left: 0 hidden;" width="530" bgcolor="transparent">
                                                            <p style="display: block; margin: 0; line-height: inherit; text-align: center;" align="center">
                                                              <span class="ui-provider ve b c d e f g h i j k l m n o p q r s t u v w x y z ab ac ae af ag ah ai aj ak" dir="ltr" style="color: #ffffff; text-align: center;">
                                                                <a href="https://shop.studds.com/our-policies/#warranty-policy" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Warranty Policy</a>&nbsp; |&nbsp;&nbsp; </span>
                                                              <a href="https://shop.studds.com/our-policies/#exchange-policy" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Exchange Policy</a>
                                                              <span style="color: #ffffff; text-align: center;">&nbsp; |&nbsp;&nbsp;</span>
                                                              <a href="https://shop.studds.com/our-policies/#order-cancellation-policy" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Cancellation Policy</a>
                                                            </p>
                                                          </td>
                                                        </tr>
                                                      </table>
                                                    </td>
                                                  </tr>
                                                </table>
                                                <table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                                                  <tr>
                                                    <td valign="top" style="">
                                                      <table class="html_text" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                                                        <tr>
                                                          <td valign="top" dir="ltr" style="font-size: 15px; width: 530px; line-height: 22px; background-image: none; padding: 0px; border-radius: 0px; border-top: 0 hidden; border-right: 0 hidden; border-bottom: 0 hidden; border-left: 0 hidden;" width="530">
                                                            <p style="display: block; margin: 0; line-height: inherit; text-align: center;" align="center">
                                                              <span style="color: #ffffff; font-family: verdana, geneva, sans-serif; font-size: 14px;">
                                                                <a href="https://shop.studds.com/shopping-shipping-delivery-policy/" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Shopping, Shipping &amp; Delivery Policy &nbsp;</a>
                                                                <span style="text-align: center;">|&nbsp;&nbsp; <a href="https://shop.studds.com/terms-of-use/" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Terms Of Use |&nbsp;</a>
                                                                </span>&nbsp; <a href="https://shop.studds.com/privacy-policy/" target="_blank" rel="noopener" style="text-decoration: none; word-break: break-word; color: #ffffff;">Privacy Policy</a>
                                                              </span>
                                                            </p>
                                                          </td>
                                                        </tr>
                                                      </table>
                                                    </td>
                                                  </tr>
                                                </table>
                                              </td>
                                            </tr>
                                          </table>
                                        </td>
                                      </tr>
                                    </table>
                                    <!--[if mso | IE]>
																											</td>
																										</tr>
																									</table>
																									<![endif]-->
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
  </body>
</html>';

    // Set headers for HTML email
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Send email
    if (wp_mail($email, $subject, $message, $headers)) {
        error_log("Review links email sent successfully for order ID: " . $order_id);
    } else {
        error_log("Failed to send review links email for order ID: " . $order_id);
    }
}

add_filter('rest_post_dispatch', 'customize_wc_batch_update_response_structure', 20, 3);

function customize_wc_batch_update_response_structure($response, $server, $request)
{
    $route = $request->get_route();

    // Apply only to WooCommerce product batch update endpoint
    if (strpos($route, '/wc/v3/products/batch') !== false && $request->get_method() === 'POST') {
        $data = $response->get_data();

        if (isset($data['update']) && is_array($data['update'])) {
            $custom_updates = [];

            foreach ($data['update'] as $product) {
                $custom_updates[] = [
                    'id'             => $product['id'],
                    'sku'            => $product['sku'],
                    'stock_quantity' => (int) $product['stock_quantity'],
                    'regular_price'  => (float) $product['regular_price'],
                ];
            }

            $response->set_data(['update' => $custom_updates]);
        }
    }

    return $response;
}

function remove_nofollow_from_cancel_reply($link)
{
    return str_replace('rel="nofollow"', '', $link);
}
add_filter('cancel_comment_reply_link', 'remove_nofollow_from_cancel_reply');



// Function to decrypt values
function decrypt_srn_mdn($data)
{
    define('SECRET_KEY', 'b7D!');
    $key = substr(hash('sha256', SECRET_KEY, true), 0, 16);
    $iv = substr(hash('sha256', 'studds_iv_key', true), 0, 16);
    return openssl_decrypt(hex2bin($data), "AES-128-CBC", $key, 0, $iv);
}


// Include the warranty entries file
include_once 'warranty-entries.php';

require_once get_stylesheet_directory() . '/api-integration-for-warranty-registration.php';

/* REVAMP CODE */
/*
** Including the inc folder with custom code
** 29-05-25
*/

add_action('after_setup_theme', 'child_theme_load_inc_files');
function child_theme_load_inc_files()
{
    $inc_dir = get_stylesheet_directory() . '/inc';

    if (is_dir($inc_dir)) {
        $inc_files = glob($inc_dir . '/*.php');

        if (!empty($inc_files)) {
            foreach ($inc_files as $file) {
                include $file;
            }
        }
    }
}


// Youtube ID Extractor
function YouTube_ID_Extractor($url)
{
    preg_match("/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]+)/", $url, $matches);
    return $matches[1] ?? '';
}

add_action('init', function () {
    if (isset($_GET['send_test_mail'])) {
        $to = 'studds_test_mail@mailinator.com'; // Replace with your real email
        $subject = 'Test Mail from WordPress';
        $message = 'Hello, this is a test email.';
        $headers = ['Content-Type: text/html; charset=UTF-8', 'From: Your Site <noreply@yourdomain.com>'];

        $sent = wp_mail($to, $subject, $message, $headers);

        if ($sent) {
            echo 'Mail sent successfully!';
        } else {
            echo 'Mail sending failed.';
        }

        exit;
    }
});


?>