<?php

/**
 * Class Search_By_Base_Color_Widget
 *
 * A custom WordPress widget for WooCommerce that displays product attribute terms
 * (specifically from the 'pa_base-color' taxonomy) as circular colored checkboxes.
 * This allows users to filter products visually by base color.
 *
 * Features:
 * - Displays base colors using term meta for color codes.
 * - Uses circular swatches as clickable checkboxes.
 * - Renders an empty container `#ajax-base-color-results` for injecting filtered results via AJAX.
 * - Supports admin widget UI (optional settings can be added later).
 *
 * To activate this widget, ensure it is registered via:
 * `register_widget('Search_By_Base_Color_Widget');`
 *
 * @since 1.0.0
 */

class Search_By_Base_Color_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'search_by_base_color_widget', // Unique ID
            __('Search by Base Color', 'text-domain'),
            ['description' => __('Filter products by base color', 'text-domain')]
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        echo $args['before_title'] . __('Color', 'text-domain') . $args['after_title'];

        // Display base-color attribute values as checkboxes
        $this->display_base_color_checkboxes();

        echo '<div id="ajax-base-color-results"></div>';
        echo $args['after_widget'];
    }

    private function display_base_color_checkboxes()
    {
        $taxonomy = 'pa_base-color'; // Ensure this matches the taxonomy slug for your attribute

        // Get the terms for the specified taxonomy
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ]);

        if (!empty($terms) && !is_wp_error($terms)) {
            echo '<form id="base-color-filter-form" style="display: flex; gap: 10px; flex-wrap: wrap;">';
            foreach ($terms as $term) {
                // Fetch the color value from term meta
                $color = get_term_meta($term->term_id, 'color', true);
                if (empty($color)) {
                    $color = '#ccc'; // Default color if not set
                }

                echo '<label style="cursor: pointer; display: inline-block; text-align: center;">';
                echo '<input type="checkbox" name="base_color[]" value="' . esc_attr($term->slug) . '" style="display: none;">';
                echo '<span style="display: inline-block; width: 30px; height: 30px; border-radius: 50%; background-color: ' . esc_attr($color) . '; border: 1px solid #000;"></span>';
                echo '</label>';
            }
            echo '</form>';
        } else {
            echo '<p>' . __('No base colors found.', 'text-domain') . '</p>';
        }
    }

    public function form($instance)
    {
        // Optional: Widget settings form in admin
    }

    public function update($new_instance, $old_instance)
    {
        return $new_instance;
    }
}

/**
 * Class Search_By_Size_Widget
 *
 * A custom WordPress widget for WooCommerce that displays product attribute terms
 * (specifically from the 'pa_size' taxonomy) as checkboxes to filter products by size.
 *
 * Features:
 * - Displays size terms as simple checkboxes with labels.
 * - Renders an empty container `#ajax-size-results` for injecting filtered results via AJAX.
 * - Supports admin widget UI (optional settings can be added later).
 *
 * To activate this widget, ensure it is registered via:
 * `register_widget('Search_By_Size_Widget');`
 *
 * @since 1.0.0
 */
class Search_By_Size_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'search_by_size_widget', // Unique ID
            __('Search by Size', 'text-domain'),
            ['description' => __('Filter products by size', 'text-domain')]
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        echo $args['before_title'] . __('Size', 'text-domain') . $args['after_title'];

        // Display size attribute values as checkboxes
        $this->display_size_checkboxes();

        echo '<div id="ajax-size-results"></div>';
        echo $args['after_widget'];
    }

    private function display_size_checkboxes()
    {
        $taxonomy = 'pa_size'; // Taxonomy slug for the size attribute

        // Get the terms for the specified taxonomy
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ]);

        if (!empty($terms) && !is_wp_error($terms)) {
            echo '<form id="size-filter-form" style="" class="category_filter_box" >';
            foreach ($terms as $term) {
                echo '<div class="filter_listing_wrap" >';
                echo '<label style="cursor: pointer;">';
                echo '<input type="checkbox" class="custom_checkbox" name="size[]" value="' . esc_attr($term->slug) . '" style=""><label for="check1" class="custom_label"></label>';
                echo esc_html($term->name);
                echo '</label>';
                echo '</div>';
            }
            echo '</form>';
        } else {
            echo '<p>' . __('No sizes found.', 'text-domain') . '</p>';
        }
    }

    public function form($instance)
    {
        // Optional: Widget settings form in admin
    }

    public function update($new_instance, $old_instance)
    {
        return $new_instance;
    }
}

/**
 * Class Search_By_Category_Widget
 *
 * A custom WordPress widget for WooCommerce that displays product categories
 * as checkboxes to filter products by category.
 *
 * Features:
 * - Displays product categories as simple checkboxes with labels.
 * - Renders an empty container `#ajax-category-results` for injecting filtered results via AJAX.
 * - Supports admin widget UI (optional settings can be added later).
 *
 * To activate this widget, ensure it is registered via:
 * `register_widget('Search_By_Category_Widget');`
 *
 * @since 1.0.0
 */
class Search_By_Category_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'search_by_category_widget', // Unique ID
            __('Search by Category', 'text-domain'),
            ['description' => __('Filter products by category', 'text-domain')]
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        echo $args['before_title'] . __('Category', 'text-domain') . $args['after_title'];

        // Display product categories as checkboxes
        $this->display_category_checkboxes();

        echo '<div id="ajax-category-results"></div>';
        echo $args['after_widget'];
    }

    private function display_category_checkboxes()
    {
        $taxonomy = 'product_cat'; // Taxonomy slug for product categories

        // Get the "Uncategorized" category ID
        $uncategorised = get_term_by('slug', 'uncategorised', $taxonomy);
        $uncategorised_id = $uncategorised ? $uncategorised->term_id : 0;

        // Get parent categories, excluding "uncategorised"
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'parent' => 0, // Only get parent categories
            'exclude' => [$uncategorised_id], // Exclude "uncategorised"
        ]);
        if (!empty($terms) && !is_wp_error($terms)) {
            echo '<form id="category-filter-form" class="category_filter_box">';
            foreach ($terms as $term) {
                echo '<div class="filter_listing_wrap">';
                echo '<label style="cursor: pointer;">';
                echo '<input type="checkbox" name="category[]" class="custom_checkbox" value="' . esc_attr($term->slug) . '" style=""><label for="check2" class="custom_label"></label>';
                echo esc_html($term->name);
                echo '</label>';
                echo '</div>';
            }
            echo '</form>';
        } else {
            echo '<p>' . __('No categories found.', 'text-domain') . '</p>';
        }
    }

    public function form($instance)
    {
        // Optional: Widget settings form in admin
    }

    public function update($new_instance, $old_instance)
    {
        return $new_instance;
    }
}


/**
 * Class Search_By_Finish_Widget
 *
 * A custom WordPress widget for WooCommerce that displays product attribute terms
 * (specifically from the 'pa_finish' taxonomy) as checkboxes to filter products by finish.
 *
 * Features:
 * - Displays finish terms as simple checkboxes with labels.
 * - Renders an empty container `#ajax-finish-results` for injecting filtered results via AJAX.
 * - Supports admin widget UI (optional settings can be added later).
 *
 * To activate this widget, ensure it is registered via:
 * `register_widget('Search_By_Finish_Widget');`
 *
 * @since 1.0.0
 */
class Search_By_Finish_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'search_by_finish_widget', // Unique ID
            __('Search by Finish', 'text-domain'),
            ['description' => __('Filter products by finish', 'text-domain')]
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        echo $args['before_title'] . __('Finish', 'text-domain') . $args['after_title'];

        // Display finish attribute values as checkboxes
        $this->display_finish_checkboxes();

        echo '<div id="ajax-finish-results"></div>';
        echo $args['after_widget'];
    }

    private function display_finish_checkboxes()
    {
        $taxonomy = 'pa_finish'; // Taxonomy slug for the finish attribute

        // Get the terms for the specified taxonomy
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ]);

        if (!empty($terms) && !is_wp_error($terms)) {
            echo '<form id="finish-filter-form" style=" "  class="category_filter_box" >';
            foreach ($terms as $term) {
                echo '<div class="filter_listing_wrap" >';
                echo '<label style="cursor: pointer;">';
                echo '<input type="checkbox" class="custom_checkbox" name="finish[]" value="' . esc_attr($term->slug) . '" style=""><label for="check4" class="custom_label"></label> ';
                echo esc_html($term->name);
                echo '</label>';
                echo '</div>';
            }
            echo '</form>';
        } else {
            echo '<p>' . __('No finishes found.', 'text-domain') . '</p>';
        }
    }

    public function form($instance)
    {
        // Optional: Widget settings form in admin
    }

    public function update($new_instance, $old_instance)
    {
        return $new_instance;
    }
}

/**
 * Class Search_By_Model_Widget
 *
 * A custom WordPress widget for WooCommerce that displays product attribute terms
 * (specifically from the 'pa_model' taxonomy) as checkboxes to filter products by model.
 *
 * Features:
 * - Displays model terms as simple checkboxes with labels.
 * - Renders an empty container `#ajax-model-results` for injecting filtered results via AJAX.
 * - Supports admin widget UI (optional settings can be added later).
 *
 * To activate this widget, ensure it is registered via:
 * `register_widget('Search_By_Model_Widget');`
 *
 * @since 1.0.0
 */
class Search_By_Model_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'search_by_model_widget', // Unique ID
            __('Search by Model', 'text-domain'),
            ['description' => __('Filter products by model', 'text-domain')]
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        echo $args['before_title'] . __('Model', 'text-domain') . $args['after_title'];

        // Display model attribute values as checkboxes
        $this->display_model_checkboxes();

        echo '<div id="ajax-model-results"></div>';
        echo $args['after_widget'];
    }

    private function display_model_checkboxes()
    {
        $taxonomy = 'pa_model'; // Taxonomy slug for the model attribute

        // Get the terms for the specified taxonomy
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => true,
        ]);

        if (!empty($terms) && !is_wp_error($terms)) {
            echo '<form id="model-filter-form" style=" " class="category_filter_box" >';
            foreach ($terms as $term) {
                echo '<div class="filter_listing_wrap" >';
                echo '<label style="cursor: pointer;">';
                echo '<input type="checkbox" class="custom_checkbox" name="model[]" value="' . esc_attr($term->slug) . '""><label for="check3" class="custom_label"></label> ';
                echo esc_html($term->name);
                echo '</label>';
                echo '</div>';
            }
            echo '</form>';
        } else {
            echo '<p>' . __('No models found.', 'text-domain') . '</p>';
        }
    }

    public function form($instance)
    {
        // Optional: Widget settings form in admin
    }

    public function update($new_instance, $old_instance)
    {
        return $new_instance;
    }
}

/**
 * Class Filter_By_Rating_Widget
 *
 * A custom WordPress widget for WooCommerce that displays ratings (1 to 5 stars) as checkboxes
 * to filter products by customer ratings.
 *
 * Features:
 * - Displays stars (⭐) for ratings as checkboxes with labels (1 to 5 stars).
 * - Renders an empty container `#ajax-rating-results` for injecting filtered results via AJAX.
 * - Supports admin widget UI (optional settings can be added later).
 *
 * To activate this widget, ensure it is registered via:
 * `register_widget('Filter_By_Rating_Widget');`
 *
 * @since 1.0.0
 */
class Filter_By_Rating_Widget extends WP_Widget
{
    /**
     * Constructor method for the widget.
     *
     * Registers the widget with a unique ID, title, and description.
     */
    public function __construct()
    {
        parent::__construct(
            'filter_by_rating_widget', // Unique ID for the widget
            __('Filter by Rating', 'text-domain'), // Widget title
            ['description' => __('Filter WooCommerce products by rating.', 'text-domain')] // Description
        );
    }

    /**
     * Outputs the widget content in the front end.
     *
     * @param array $args Display arguments including before and after widget markup.
     * @param array $instance The settings for the particular instance of the widget.
     */
    public function widget($args, $instance)
    {
        echo $args['before_widget']; // Output before widget markup
        echo $args['before_title'] . __('Rating', 'text-domain') . $args['after_title']; // Widget title

        // Display rating filter checkboxes
        $this->display_rating_checkboxes();

        // Placeholder for AJAX results
        echo '<div id="ajax-rating-results"></div>';

        echo $args['after_widget']; // Output after widget markup
    }

    /**
     * Outputs the rating filter checkboxes with star icons.
     *
     * Displays checkboxes for ratings from 1 to 5 stars.
     */
    private function display_rating_checkboxes()
    {
        echo '<form id="rating-filter-form" style=" " class="category_filter_box" >';

        // Loop through ratings from 5 to 1
        for ($rating = 5; $rating >= 1; $rating--) {
            echo '<label style="cursor: pointer; display: flex; align-items: center;">';
            echo '<input type="checkbox" class="custom_checkbox" name="rating[]" value="' . esc_attr($rating) . '" style=""><label for="check5" class="custom_label"></label>';
            echo $this->generate_star_icons($rating); // Display stars for the rating
            echo '</label>';
        }

        echo '</form>';
    }

    /**
     * Generates star icons for a given rating.
     *
     * @param int $rating The number of stars to display (1-5).
     * @return string HTML representation of the stars.
     */
    private function generate_star_icons($rating)
    {
        $stars = '';
        for ($i = 1; $i <= $rating; $i++) {
            $stars .= '<span style="color: gold; font-size: 1.2em; margin-right: 2px;">&#9733;</span>'; // Unicode star
        }
        return $stars;
    }

    /**
     * Outputs the widget settings form in the admin area.
     *
     * @param array $instance The settings for the particular instance of the widget.
     */
    public function form($instance)
    {
        // Optional: Add settings form in the admin widget UI if required
    }

    /**
     * Handles updating settings for the current widget instance.
     *
     * @param array $new_instance New settings for this instance as input by the user.
     * @param array $old_instance Old settings for this instance.
     * @return array Updated settings to save.
     */
    public function update($new_instance, $old_instance)
    {
        return $new_instance;
    }
}
