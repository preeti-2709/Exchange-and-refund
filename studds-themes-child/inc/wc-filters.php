<?php

/**
 * WC Filter hooks
 *
 * @since 1.0.0
 */

// Remove the default Reviews tab from product page
add_filter('woocommerce_product_tabs', 'remove_reviews_tab', 98);

// Add custom base color meta to WooCommerce variation data.
add_filter('woocommerce_available_variation', 'add_base_color_to_variation_data');

// Modifies the permalink and adds rewrite rules to handle new `/blog/post-name/` structure
add_filter('post_link', 'custom_blog_post_permalink', 10, 2);
add_filter('rewrite_rules_array', 'custom_blog_post_rewrite_rules');

// Email Masking 
add_filter('user_email', 'mask_email_address');

// Customize the search form placeholder text in WordPress.
add_filter('get_search_form', 'boxshop_custom_search_placeholder');

/* WC Filter hooks end*/ 

/**
 * Remove the default Reviews tab from the WooCommerce single product page.
 *
 * This filter unsets the 'reviews' tab from the product tabs array,
 * effectively hiding the reviews section from the product page.
 *
 * @param array $tabs Array of product tabs.
 * @return array Modified array of product tabs without the reviews tab.
 */
function remove_reviews_tab($tabs)
{
    unset($tabs['reviews']);
    return $tabs;
}

/**
 * Add custom base color meta to WooCommerce variation data.
 *
 * This filter adds the '_variation_base_color' meta value to the
 * variation data sent to the frontend, making the base color
 * accessible via JavaScript or templates.
 *
 * @param array $variation Variation data array.
 * @return array Modified variation data with 'base_color' included.
 */

function add_base_color_to_variation_data($variation)
{
    $base_color = get_post_meta($variation['variation_id'], '_variation_base_color', true);
    if (!empty($base_color)) {
        $variation['base_color'] = $base_color;
    }
    return $variation;
}


/**
 * Custom Blog Post Permalink & Rewrite Rules
 *
 * This code snippet customizes the permalink structure for standard blog posts (`post` post type)
 * to use a `/blog/` prefix, e.g., `yoursite.com/blog/post-name/` instead of `yoursite.com/post-name/`.
 *
 * Functions:
 * 1. `custom_blog_post_permalink()` – Modifies the permalink for posts on the front-end.
 * 2. `custom_blog_post_rewrite_rules()` – Adds rewrite rules to handle the new `/blog/post-name/` structure.
 *
 * IMPORTANT:
 * After adding this code, you must visit **Settings > Permalinks** in the WordPress admin
 * and click **Save Changes** to flush rewrite rules and apply the changes properly.
 */


 /* Update the blog detail page url /blog/ */

function custom_blog_post_permalink($permalink, $post)
{
    if ($post->post_type == 'post') {
        return home_url('/blog/' . $post->post_name . '/');
    }
    return $permalink;
}
function custom_blog_post_rewrite_rules($rules)
{
    $new_rules = array(
        'blog/([^/]+)/?$' => 'index.php?name=$matches[1]',
    );
    return $new_rules + $rules;
}

/**
 * Mask User Email Address for Privacy
 *
 * This function masks the local part (before the "@") of an email address to enhance privacy.
 * Example: john.doe@example.com → j*****e@example.com
 *
 * Masking rules:
 * - If user part is 1 character: mask with '*'
 * - If user part is 2 characters: show first, mask second (e.g., "ab" → "a*")
 * - If user part is longer: show first and last character, mask the rest with '*'
 *
 * @param string $email  The original email address.
 * @return string        The masked email address.
 */
function mask_email_address($email)
{
    list($user, $domain) = explode('@', $email);

    if (strlen($user) <= 1) {
        $masked_user = '*';
    } elseif (strlen($user) == 2) {
        $masked_user = substr($user, 0, 1) . '*';
    } else {
        $masked_user = substr($user, 0, 1) . str_repeat('*', strlen($user) - 2) . substr($user, -1);
    }

    return $masked_user . '@' . $domain;
}

/**
 * Customize the search form placeholder text in WordPress.
 *
 * This filter modifies the default placeholder text in the WordPress search form
 * and replaces it with a custom message: "Search blog posts...".
 *
 * @param string $form The HTML output of the search form.
 * @return string Modified search form HTML with custom placeholder text.
 */
function boxshop_custom_search_placeholder($form)
{
    $form = str_replace('placeholder="Search …"', 'placeholder="Search blog posts..."', $form);
    return $form;
}