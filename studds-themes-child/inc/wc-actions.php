<?php

/**
 * WC Action hooks
 *
 * @since 1.0.0
 */

// Display numeric average rating with star rating below product price on single product page
add_action('woocommerce_single_product_summary', 'custom_display_numeric_rating_with_stars_after_price', 11);

// Register Base color settings page
add_action('admin_menu', 'register_base_color_settings_page');

// Add Base color field
add_action('woocommerce_variation_options_pricing', 'add_base_color_field_to_variations', 10, 3);

// Save Base color field
add_action('woocommerce_save_product_variation', 'save_base_color_field_for_variations', 10, 2);

// Search by base color
add_action('widgets_init', 'register_search_filters_widget');

// Enqueue color picker in admin
add_action('admin_enqueue_scripts', 'enqueue_color_picker');



/* WC Action hooks end*/

/**
 * Outputs the average numeric rating and star rating on single product pages.
 *
 * - Runs after the price (priority 11) in the product summary section.
 * - Only displays if the product has at least one rating.
 */

function custom_display_numeric_rating_with_stars_after_price()
{
    global $product;
    if (! $product->get_rating_count()) {
        return;
    }
    $average = $product->get_average_rating();
    echo '<div class="custom-rating-summary" style="margin-top:10px; display:flex; align-items:center; gap:5px;">';
    echo '<strong>' . number_format($average, 1) . '</strong>';
    echo wc_get_rating_html($average);
    echo '</div>';
}

/**
 * Add a custom 'Base Color' dropdown field to WooCommerce variation pricing options.
 *
 * This displays a select dropdown in each variation's settings on the product edit page,
 * allowing admins to assign a base color to each variation.
 *
 * @param int    $loop           Index of the variation in the variations loop.
 * @param array  $variation_data Variation data array.
 * @param object $variation      WC_Product_Variation object.
 */

function add_base_color_field_to_variations($loop, $variation_data, $variation)
{
    // Fetch base color terms (replace 'base_color' with your actual attribute slug if different)
    $base_colors = get_terms([
        'taxonomy' => 'pa_base-color', // Replace with the correct attribute taxonomy if needed
        'hide_empty' => false,
    ]);

    // Create a dropdown field
?>
    <div class="options_group">
        <p class="form-row form-row-full">
            <label for="variation_base_color_<?php echo esc_attr($variation->ID); ?>">
                <?php _e('Base Color', 'woocommerce'); ?>
            </label>
            <select name="variation_base_color[<?php echo esc_attr($loop); ?>]" id="variation_base_color_<?php echo esc_attr($variation->ID); ?>" class="short">
                <option value=""><?php _e('Select a Base Color', 'woocommerce'); ?></option>
                <?php foreach ($base_colors as $color): ?>
                    <option value="<?php echo esc_attr($color->slug); ?>" <?php selected(get_post_meta($variation->ID, '_variation_base_color', true), $color->slug); ?>>
                        <?php echo esc_html($color->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
    </div>
<?php
}

/**
 * Save the custom base color field for WooCommerce product variations.
 *
 * This action saves the 'variation_base_color' field submitted
 * from the product edit screen to the variation's post meta
 * whenever a variation is saved.
 *
 * @param int $variation_id The variation product ID.
 * @param int $i Index of the variation in the posted data.
 */

function save_base_color_field_for_variations($variation_id, $i)
{
    if (isset($_POST['variation_base_color'][$i])) {
        $base_color = sanitize_text_field($_POST['variation_base_color'][$i]);
        update_post_meta($variation_id, '_variation_base_color', $base_color);
    }
}

/**
 * Hooks into 'widgets_init' to make the widget available in the WordPress Widgets admin.
 *
 */
function register_search_filters_widget()
{
    register_widget('Search_By_Base_Color_Widget'); // New base color widget
    register_widget('Search_By_Size_Widget');       // New size widget
    register_widget('Search_By_Category_Widget');   // New category widget
    register_widget('Search_By_Finish_Widget');     // New finish widget
    register_widget('Search_By_Model_Widget');     // New Model widget
    register_widget('Filter_By_Rating_Widget');     // New rating widget
    
}

/**
 * Enqueue WordPress color picker script and custom JS on the base color settings admin page.
 *
 * This hook ensures that the color picker and necessary JS file (`base-color-settings.js`) are only loaded
 * when the admin page related to 'base-color-settings' is being viewed, optimizing performance.
 *
 * @param string $hook_suffix The current admin page's hook suffix.
 */

function enqueue_color_picker($hook_suffix)
{
    if (strpos($hook_suffix, 'base-color-settings') !== false) {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('base-color-settings', get_stylesheet_directory_uri() . '/assets/js/base-color-settings.js', ['wp-color-picker'], false, true);
    }
}

/**
 * Register a custom admin submenu under "Products" for managing Base Colors.
 *
 * This adds a submenu titled "Base Colors" under the "Products" menu in the WordPress admin area,
 * allowing administrators to assign color codes to WooCommerce product attribute terms (e.g., base colors).
 *
 * The color codes can be used to visually represent product variations or filters.
 *
 * @since 1.0.0
 */
function register_base_color_settings_page()
{
    add_submenu_page(
        'edit.php?post_type=product',
        __('Base Color Settings', 'text-domain'),
        __('Base Colors', 'text-domain'),
        'manage_woocommerce',
        'base-color-settings',
        'render_base_color_settings_page' // Callback function mentioned below
    );
}


/**
 * Render the Base Color Settings admin page.
 *
 * Its a callback function for the register_base_color_settings_page 
 * 
 * This page allows the admin to assign a color hex code to each term in the 'pa_base-color' taxonomy.
 * On form submission, term meta is updated using `update_term_meta`.
 *
 * It displays all terms in the taxonomy and provides a color input field (requires wp-color-picker).
 *
 * @since 1.0.0
 */
function render_base_color_settings_page()
{
    $taxonomy = 'pa_base-color';
    $terms = get_terms([
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ]);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['base_color_nonce']) && wp_verify_nonce($_POST['base_color_nonce'], 'save_base_colors')) {
        foreach ($_POST['base_colors'] as $term_id => $color_code) {
            update_term_meta($term_id, 'color', sanitize_hex_color($color_code));
        }
        echo '<div class="updated"><p>' . __('Base colors updated successfully.', 'text-domain') . '</p></div>';
    }

    echo '<div class="wrap">';
    echo '<h1>' . __('Base Color Settings', 'text-domain') . '</h1>';
    echo '<form method="POST">';
    wp_nonce_field('save_base_colors', 'base_color_nonce');

    if (!empty($terms) && !is_wp_error($terms)) {
        echo '<table class="form-table">';
        echo '<thead><tr><th>' . __('Base Color', 'text-domain') . '</th><th>' . __('Color Code', 'text-domain') . '</th></tr></thead>';
        echo '<tbody>';
        foreach ($terms as $term) {
            $current_color = get_term_meta($term->term_id, 'color', true);
            echo '<tr>';
            echo '<td>' . esc_html($term->name) . '</td>';
            echo '<td><input type="text" name="base_colors[' . esc_attr($term->term_id) . ']" value="' . esc_attr($current_color) . '" class="color-field"></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>' . __('No base colors found.', 'text-domain') . '</p>';
    }

    echo '<p><input type="submit" class="button-primary" value="' . __('Save Changes', 'text-domain') . '"></p>';
    echo '</form>';
    echo '</div>';
}
