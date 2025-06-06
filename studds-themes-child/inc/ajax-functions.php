<?php

// if ( ! defined( 'ABSPATH' ) ) {
//     exit;
// }


/**
 * Register AJAX action hooks.
 *
 * These hooks handle AJAX requests for various frontend features.
 * Both logged-in (`wp_ajax_`) and non-logged-in (`wp_ajax_nopriv_`) users
 * are supported to allow AJAX access for all visitors.
 *
 * @since 1.0.0
 */

// Media Page AJAX Handler
add_action('wp_ajax_load_media_items', 'load_media_items_callback');
add_action('wp_ajax_nopriv_load_media_items', 'load_media_items_callback');

// Events Page AJAX Handler
add_action('wp_ajax_load_events', 'ajax_load_events_callback');
add_action('wp_ajax_nopriv_load_events', 'ajax_load_events_callback');

// Gallery Page AJAX Handler
add_action('wp_ajax_load_gallery_items', 'load_gallery_items');
add_action('wp_ajax_nopriv_load_gallery_items', 'load_gallery_items');

// Get Product Variation AJAX Handler
add_action('wp_ajax_get_product_variations', 'get_product_variations_ajax');
add_action('wp_ajax_nopriv_get_product_variations', 'get_product_variations_ajax');

// Get Variation Additional Image AJAX Handler
add_action('wp_ajax_get_matched_variation_image', 'fetch_variation_additional_image');
add_action('wp_ajax_nopriv_get_matched_variation_image', 'fetch_variation_additional_image');

// Remove order meta AJAX Handler
add_action('wp_ajax_remove_order_meta', 'remove_order_meta_callback');

// Get Product By Category AJAX Handler  
add_action('wp_ajax_get_products_by_category', 'get_products_by_category');
add_action('wp_ajax_nopriv_get_products_by_category', 'get_products_by_category');

// Filter Product By AJAX Handler
add_action('wp_ajax_filter_products', 'filter_products');
add_action('wp_ajax_nopriv_filter_products', 'filter_products');

function filter_products()
{
    // Collect filter values from the request
    $selected_colors = isset($_GET['base_color']) ? array_map('sanitize_text_field', (array) $_GET['base_color']) : [];
    $selected_sizes = isset($_GET['size']) ? array_map('sanitize_text_field', (array) $_GET['size']) : [];
    $selected_categories = isset($_GET['category']) ? array_map('sanitize_text_field', (array) $_GET['category']) : [];
    $selected_finishes = isset($_GET['finish']) ? array_map('sanitize_text_field', (array) $_GET['finish']) : [];
    $selected_models = isset($_GET['model']) ? array_map('sanitize_text_field', (array) $_GET['model']) : [];
    $selected_ratings = isset($_GET['rating']) ? array_map('intval', (array) $_GET['rating']) : [];
    $paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;

    // Set up the base query arguments
    $query_args = [
        'post_type'      => 'product',
        'posts_per_page' => 12,
        'paged'          => $paged,
        'post_status'    => 'publish',
    ];

    $tax_query = ['relation' => 'AND'];
    $meta_query = ['relation' => 'AND'];
    $parent_product_ids = [];

    // Handle Base Color filter
    if (!empty($selected_colors)) {
        $variation_query = new WP_Query([
            'post_type'      => 'product_variation',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => [
                [
                    'key'     => '_variation_base_color',
                    'value'   => $selected_colors,
                    'compare' => 'IN',
                ],
            ],
            'fields' => 'ids',
        ]);

        $variation_ids = $variation_query->posts;
        if (!empty($variation_ids)) {
            foreach ($variation_ids as $variation_id) {
                $parent_id = wp_get_post_parent_id($variation_id);
                if ($parent_id) {
                    $parent_product_ids[] = $parent_id;
                }
            }
        }
    }

    // Handle Size filter
    if (!empty($selected_sizes)) {
        $tax_query[] = [
            'taxonomy' => 'pa_size',
            'field'    => 'slug',
            'terms'    => $selected_sizes,
            'operator' => 'IN',
        ];
    }

    // Handle Category filter
    if (!empty($selected_categories)) {
        $tax_query[] = [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $selected_categories,
            'operator' => 'IN',
        ];
    }

    // Handle Finish filter
    if (!empty($selected_finishes)) {
        $tax_query[] = [
            'taxonomy' => 'pa_finish',
            'field'    => 'slug',
            'terms'    => $selected_finishes,
            'operator' => 'IN',
        ];
    }

    // Handle Model filter
    if (!empty($selected_models)) {
        $tax_query[] = [
            'taxonomy' => 'pa_model',
            'field'    => 'slug',
            'terms'    => $selected_models,
            'operator' => 'IN',
        ];
    }

    // Handle Rating filter
    if (!empty($selected_ratings)) {
        foreach ($selected_ratings as $rating) {
            $meta_query[] = [
                'key'     => '_wc_average_rating',
                'value'   => $rating,
                'compare' => '=',
                'type'    => 'NUMERIC',
            ];
        }
    }

    // Ensure only products matching the Base Color filter are included
    if (!empty($parent_product_ids)) {
        $query_args['post__in'] = $parent_product_ids;
    }

    // Include tax_query and meta_query if applicable
    if (count($tax_query) > 1) {
        $query_args['tax_query'] = $tax_query;
    }
    if (count($meta_query) > 1) {
        $query_args['meta_query'] = $meta_query;
    }

    // Create the WP_Query
    $query = new WP_Query($query_args);

    // Display results
    if ($query->have_posts()) {
        woocommerce_product_loop_start();
        while ($query->have_posts()) {
            $query->the_post();
            wc_get_template_part('content', 'product');
        }
        woocommerce_product_loop_end();

    } else {
        echo '<p>' . __('No products found matching the selected filters.', 'text-domain') . '</p>';
    }

    // Restore global $post variable
    wp_reset_postdata();

    wp_die();
}



/* Register AJAX action hooks End*/

/**
 * AJAX callback function to load media items by year with pagination.
 *
 * This function retrieves 'studds-media' custom post type posts,
 * optionally filtered by a custom taxonomy 'published_year'. It
 * generates the HTML structure and pagination links to be returned
 * via AJAX in JSON format.
 *
 * @return void Outputs JSON containing media items and pagination.
 */
function load_media_items_callback()
{
    // check_ajax_referer('corporate_page_ajax', 'nonce');

    $year = sanitize_text_field($_POST['year']);
    $paged = intval($_POST['page']);

    $args = array(
        'post_type' => 'studds-media',
        'posts_per_page' => 4,
        'paged' => $paged,
    );

    if ($year) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'published_year',
                'field'    => 'slug',
                'terms'    => $year,
            ),
        );
    }

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            $Mdate = get_field('studds_media_date');
            $moreLink = get_field('read_more_link');
            $sourceName = get_field('media_source_name');
        ?>
            <div class="media-item">
                <div class="media-dates">
                    <span class="text-orange"><?php echo wp_date('M', strtotime($Mdate)); ?></span>
                    <span class="text-black"><?php echo wp_date('d', strtotime($Mdate)); ?></span>
                </div>
                <div class="media-image">
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="javascript:void(0);"><?php the_post_thumbnail('medium'); ?></a>
                    <?php endif; ?>
                </div>
                <div class="media-details">
                    <h3><a href="javascript:void(0);"><?php the_title(); ?></a></h3>
                    <p><?php the_excerpt(); ?></p>
                    <a href="<?php echo esc_url($moreLink); ?>" class="know_more_btn">Know MORE</a>
                </div>
            </div>
        <?php
        endwhile;
    endif;
    $content = ob_get_clean();

    ob_start();
    echo paginate_links(array(
        'total'     => $query->max_num_pages,
        'current'   => $paged,
        'format'    => '#',
        'type'      => 'list',
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
        'before_page_number' => '',
        'after_page_number'  => '',
        'add_args'  => false,
        'show_all'  => false,
        'end_size'  => 1,
        'mid_size'  => 1,
        'base'      => '',
        'before_page_number' => '',
        'after_page_number'  => '',
        'link_before' => '<span>',
        'link_after'  => '</span>',
    ));
    $pagination = ob_get_clean();

    wp_send_json_success(array(
        'content' => $content,
        'pagination' => $pagination,
    ));
}

/**
 * AJAX callback to load paginated 'event' posts filtered by year.
 *
 * Retrieves events by custom post type 'event', and filters by the
 * 'published_year' taxonomy. Returns post HTML and pagination links.
 *
 * @return void JSON response with HTML content and pagination.
 */
function ajax_load_events_callback()
{
    // check_ajax_referer('events_nonce', 'nonce');

    $taxonomy = 'published_year';
    $post_type = 'event';

    $year = sanitize_text_field($_POST['year']);
    $paged = isset($_POST['paged']) ? absint($_POST['paged']) : 1;

    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => 9,
        'paged' => $paged,
    );

    if (!empty($year)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $year,
            ),
        );
    }

    $event_query = new WP_Query($args);

    ob_start();
    if ($event_query->have_posts()) :
        while ($event_query->have_posts()) : $event_query->the_post();
            $event_date = get_field('event_date');
            $date_obj = $event_date ? new DateTime($event_date) : false;
        ?>
            <div class="event-item">
                <div class="event-image">
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('medium'); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="event-details">
                    <?php if ($date_obj): ?>
                        <div class="event-details-left">
                            <p class="text-orange"><?php echo esc_html($date_obj->format('M')); ?></p>
                            <p class="text-black"><?php echo esc_html($date_obj->format('d')); ?></p>
                        </div>
                    <?php endif; ?>
                    <div class="event-details-right">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <p><?php the_excerpt(); ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile;
    else :
        echo '<div class="no-media-message">No events found for this year.</div>';
    endif;

    $events_html = ob_get_clean();

    ob_start();
    echo paginate_links(array(
        'total' => $event_query->max_num_pages,
        'current' => $paged,
        'format' => '?paged=%#%',
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
        'type' => 'list',
    ));
    $pagination_html = ob_get_clean();

    wp_send_json_success([
        'events' => $events_html,
        'pagination' => $pagination_html,
    ]);
}


/**
 * AJAX callback to load more gallery items based on tab and current page.
 *
 * Expects:
 * - 'tab'  => either 'image' or 'video'
 * - 'page' => current page number (to fetch next set)
 *
 * Returns:
 * - 'html'         => Rendered HTML for new gallery items
 * - 'max_pages'    => Max number of pages available
 * - 'current_page' => Current page (after loading)
 */

function load_gallery_items()
{

    $type = $_POST['tab'];

    if ($_POST['page'] == 1) {
        $paged = isset($_POST['page']);
    } else {
        $paged = isset($_POST['page']) + 1;
    }

    $posts_per_page = 3;

    $args = [
        'post_type' => 'gallery',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
    ];

    if ($type === 'image') {
        $args['meta_query'] = [[
            'key' => '_thumbnail_id',
            'compare' => 'EXISTS',
        ]];
    } else {
        $args['meta_query'] = [
            'relation' => 'OR',
            [
                'key' => 'single_gallery_video',
                'value' => '',
                'compare' => '!=',
            ],
            [
                'key' => 'youtube_video_link',
                'value' => '',
                'compare' => '!=',
            ],
        ];
    }

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post(); ?>
            <div class="col-md-4 col-6 mb-4">
                <div class="card h-100">
                    <?php if ($type === 'image' && has_post_thumbnail()) :
                        $img_url = get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>
                        <a href="<?php echo esc_url($img_url); ?>" data-fancybox="gallery" data-caption="<?php the_title_attribute(); ?>">
                            <?php the_post_thumbnail('full', ['class' => 'card-img-top']); ?>
                        </a>
                        <?php elseif ($type === 'video') :
                        $youtube_link = get_field('youtube_video_link');
                        $video_file = get_field('single_gallery_video');

                        if ($youtube_link) :
                            preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\&\?\/]+)/', $youtube_link, $matches);
                            $youtube_id = isset($matches[1]) ? $matches[1] : '';
                            $youtube_thumbnail = "https://img.youtube.com/vi/{$youtube_id}/hqdefault.jpg"; ?>
                            <a data-fancybox="gallery" data-caption="<?php the_title_attribute(); ?>" data-type="iframe" href="https://www.youtube.com/embed/<?php echo esc_attr($youtube_id); ?>?autoplay=1">
                                <div class="ratio ratio-16x9">
                                    <img src="<?php echo esc_url($youtube_thumbnail); ?>" class="card-img-top" alt="YouTube Video">
                                </div>
                            </a>
                        <?php elseif ($video_file) :
                            $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: get_template_directory_uri() . '/assets/img/video-placeholder.jpg'; ?>
                            <a data-fancybox="gallery" data-caption="<?php the_title_attribute(); ?>" href="<?php echo esc_url($video_file); ?>">
                                <div class="ratio ratio-16x9">
                                    <img src="<?php echo esc_url($thumbnail); ?>" class="card-img-top" alt="Video File">
                                </div>
                            </a>
                    <?php endif;
                    endif; ?>

                    <div class="card-body">
                        <h5 class="card-title"><?php the_title(); ?></h5>
                    </div>
                </div>
            </div>
<?php
        endwhile;
    endif;

    $html = ob_get_clean();


    wp_send_json_success([
        'html' => $html,
        'max_pages' => $query->max_num_pages,
        'current_page' => $paged
    ]);
}


/**
 * AJAX handler to fetch unique color variations for a given variable product.
 *
 * Accepts a product ID via POST, verifies the product is variable, then retrieves
 * all available variations. Extracts unique color attribute slugs and related
 * color data (color value, image URL) for each variation.
 *
 * Returns a JSON success response with an array of unique colors, or error if
 * product is invalid or not variable.
 *
 * Expected $_POST parameters:
 * - product_id (int): ID of the variable product.
 *
 * @return void JSON response containing color variations or error message.
 */
function get_product_variations_ajax()
{
    $product_id = intval($_POST['product_id']);
    $product = wc_get_product($product_id);

    if (!$product || !$product->is_type('variable')) {
        wp_send_json_error('Invalid product or not variable.');
    }

    $variations = $product->get_available_variations();
    $response = [];
    $added_colors = []; // Array to track unique color slugs

    foreach ($variations as $variation) {
        $color_slug = $variation['attributes']['attribute_pa_color'] ?? '';
        if ($color_slug && !in_array($color_slug, $added_colors)) {
            $term = get_term_by('slug', $color_slug, 'pa_color');

            $vi_params = get_term_meta($term->term_id, 'vi_wpvs_terms_params', true);
            $color_value = '';

            if ($vi_params) {
                $parsed = maybe_unserialize($vi_params);
                if (is_array($parsed) && isset($parsed['color'][0])) {
                    $color_value = $parsed['color'][0];
                }
            }

            $image_url = wp_get_attachment_url($variation['image_id']);

            $response[] = [
                'color' => $color_value,
                'image_url' => $image_url,
                'color_slug' => $color_slug,
            ];

            $added_colors[] = $color_slug; // Add to unique tracker
        }
    }

    wp_send_json_success($response);
}

/**
 * AJAX handler to fetch the second additional image of a matching product variation.
 *
 * Expects:
 * - 'product_id' (int) and 'attributes' (array) via POST.
 * Matches the variation by attributes and returns the second additional image URL
 * using the 'jck_additional_images' field if available.
 *
 * Sends JSON success with image URL or error with a message.
 */
function fetch_variation_additional_image()
{
    // Retrieve product ID and attributes from the request
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $attributes = isset($_POST['attributes']) ? $_POST['attributes'] : [];

    // Validate inputs
    if (!$product_id || empty($attributes)) {
        wp_send_json_error(['message' => __('Invalid product or attributes.', 'your-textdomain')]);
    }

    // Get the product object
    $product = wc_get_product($product_id);

    if ($product && $product->is_type('variable')) {
        // Get all available variations for the product
        $variations = $product->get_available_variations();

        foreach ($variations as $variation) {
            // Check if the variation matches the provided attributes
            $matched = true;

            foreach ($attributes as $key => $value) {
                if (!isset($variation['attributes'][$key]) || strtolower($variation['attributes'][$key]) !== strtolower($value)) {
                    $matched = false;
                    break;
                }
            }

            if ($matched) {
                        // Fetch additional image URL from the matched variation
                    if (isset($variation['jck_additional_images'][1]['url'])) {

                        $image_url = $variation['jck_additional_images'][1]['url'];
                        $main_url = $variation['image']['url'];

                        wp_send_json_success(['image_url' => $image_url, 'main_url' => $main_url]);
                    } else {
                        wp_send_json_error(['message' => __('No additional image found for the matched variation.', 'your-textdomain')]);
                    }

                    // Break after finding the first matched variation
                    break;
                }
        }
    }

    // Return error if no matching variation is found
    wp_send_json_error(['message' => __('No matching variation or additional image found.', 'your-textdomain')]);
}

/**
 * AJAX callback to remove custom order meta and cancel exchange request.
 *
 * Functionality:
 * - Validates the request using a nonce.
 * - Deletes the 'ced_rnx_exchange_product' meta from the order.
 * - Temporarily disables WooCommerce email notifications.
 * - Changes the order status to 'exchangecancelled'.
 * - Adds an admin-only order note about the exchange cancellation.
 *
 * Expects:
 * - 'order_id' via POST.
 *
 * Returns:
 * - JSON success on successful removal and update.
 * - JSON error if order ID or meta is missing.
 * 
 * Already present in functions file
 */

function remove_order_meta_callback()
{
    check_ajax_referer('remove_order_meta_nonce', 'security');

    if (isset($_POST['order_id'])) {
        $order_id = intval($_POST['order_id']);

        // Remove the meta data associated with ced_rnx_exchange_product
        $removed_meta = get_post_meta($order_id, 'ced_rnx_exchange_product', true);
        delete_post_meta($order_id, 'ced_rnx_exchange_product');

        // Disable email notifications temporarily
        add_filter('woocommerce_email_enabled_new_order', '__return_false');
        add_filter('woocommerce_email_enabled_cancelled_order', '__return_false');

        // Update order status to 'completed'
        $order = wc_get_order($order_id);
        $order->update_status('exchangecancelled');

        // Remove the temporary email notification filters
        remove_filter('woocommerce_email_enabled_new_order', '__return_false');
        remove_filter('woocommerce_email_enabled_cancelled_order', '__return_false');
        // Remove other temporary filters if added

        // Update order notes with the removed meta data
        if (!empty($removed_meta)) {
            //$order = wc_get_order($order_id);

            // Convert array to a string representation
            //$removed_meta_string = implode(', ', $removed_meta);

            // Update customer note
            // $old_customer_note = $order->get_customer_note();
            // $new_customer_note = !empty($old_customer_note) ? $old_customer_note . "\n" . 'Your Exchange request has been cancelled for products: ' . $removed_meta_string : 'Removed meta data: ' . $removed_meta_string;
            // $order->set_customer_note($new_customer_note);

            // Update admin noteUser
            $admin_note = 'The exchange request for this order has been cancelled';
            $order->add_order_note($admin_note, true); // Note: The second parameter true makes it private to admin only

            $order->save();
            wp_send_json_success();
        } else {
            wp_send_json_error('Meta data not found for this order');
        }
    } else {
        wp_send_json_error('Order ID not provided');
    }
}

/**
 * AJAX callback to fetch products by selected category ID.
 *
 * Functionality:
 * - Accepts 'category_id' via POST.
 * - Queries all published WooCommerce products under the given category.
 * - Returns a list of <option> elements with product IDs and titles.
 *
 * Notes:
 * - If no products are found, a fallback option is returned.
 * - Uses wp_die() to properly terminate the AJAX request.
 */
function get_products_by_category()
{
    $category_id = $_POST['category_id'];

    // Your query to fetch products based on the category ID
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $category_id,
            ),
        ),
    );
    $products_query = new WP_Query($args);

    // Output the product list as select options
    if ($products_query->have_posts()) {
        while ($products_query->have_posts()) {
            $products_query->the_post();
            echo '<option value="' . get_the_ID() . '">' . get_the_title() . '</option>';
        }
        wp_reset_postdata();
    } else {
        echo '<option value="">No products found</option>';
    }

    wp_die(); // This is required to terminate immediately and return a proper response
}
