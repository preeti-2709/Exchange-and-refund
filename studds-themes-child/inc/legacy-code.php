<?php

/**
 * Legacy or unused code that might be removed anytime
 *
 * @since 1.0.0
 */



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

/*
** Change to your desired number of products per page
** Set the desired number of products per page
*/ 
function custom_woocommerce_products_per_page($per_page)
{
    return 12;
}
add_filter('loop_shop_per_page', 'custom_woocommerce_products_per_page', 20);

function custom_set_products_per_page($query) {
    if ( $query->is_main_query() && is_post_type_archive('product')) {

        $query->set('posts_per_page', 12);
    }
}
add_action('pre_get_posts', 'custom_set_products_per_page');

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




function add_cors_headers()
{
    $allowed_origin = get_site_url();
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: " . $allowed_origin);
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: *");
    }
}
add_action('init', 'add_cors_headers');