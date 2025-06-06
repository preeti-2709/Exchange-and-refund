<?php
/**
 * Custom Excerpt by Character Limit for Boxshop Theme
 *
 * This function outputs or returns an excerpt limited by character count (instead of the default word count).
 * It's useful when you want more precise control over excerpt length for post previews or listings.
 *
 * @param int     $char_limit  Number of characters to limit the excerpt to. Default is 150.
 * @param object  $post_obj    Optional. WP_Post object. Defaults to current global post.
 * @param bool    $strip_tags  Whether to strip HTML tags from the excerpt. Default true.
 * @param string  $more_text   String to append after truncation (e.g., "..."). Default '...'.
 * @param bool    $echo        Whether to echo or return the result. Default true (echo).
 *
 * @return string|null         Returns excerpt if $echo is false, otherwise echoes the output.
 */

function boxshop_the_excerpt_max_chars($char_limit = 150, $post_obj = null, $strip_tags = true, $more_text = '...', $echo = true)
{
    if ($post_obj === null) {
        global $post;
        $post_obj = $post;
    }

    $excerpt = $post_obj->post_excerpt ? $post_obj->post_excerpt : $post_obj->post_content;

    if ($strip_tags) {
        $excerpt = strip_tags($excerpt);
    }

    $excerpt = trim($excerpt);

    if (strlen($excerpt) > $char_limit) {
        $excerpt = substr($excerpt, 0, $char_limit);
        $excerpt = preg_replace('/\s+?(\S+)?$/', '', $excerpt); // avoid breaking words
        $excerpt .= $more_text;
    }

    if ($echo) {
        echo esc_html($excerpt);
    } else {
        return esc_html($excerpt);
    }
}

/**
 * Custom Pagination Override for Boxshop Theme
 *
 * This function outputs custom pagination markup using `paginate_links()`.
 * It optionally accepts a custom WP_Query object. If none is passed, it uses the global `$wp_query`.
 *
 * Notes:
 * - Reduces the total number of pages by 3 (via `$max_num_pages - 3`) – adjust as needed.
 * - Adds custom classes and markup structure for pagination display.
 * - Outputs pagination as a `<nav>` element with `ul`-based page list.
 *
 * Parameters:
 * @param WP_Query|null $query Optional custom query object.
 *
 * Usage:
 * Call `boxshop_custom_pagination()` after a query loop to render paginated links.
 */

if (!function_exists('boxshop_custom_pagination')) {
    function boxshop_custom_pagination($query = null)
    {
        global $wp_query;
        $max_num_pages = $wp_query->max_num_pages - 3;
        $paged = $wp_query->get('paged');
        if ($query != null) {
            $max_num_pages = $query->max_num_pages - 3;
            $paged = $query->get('paged');
        }
        if (!$paged) {
            $paged = 1;
        }


    ?>
        <nav class="ts-pagination">
            <?php
            echo paginate_links(array(
                'base'             => esc_url_raw(str_replace(999999999, '%#%', get_pagenum_link(999999999, false))),
                'format'       => '',
                'add_args'     => '',
                'current'      => max(1, $paged),
                'total'        => $max_num_pages,
                'prev_text'    => '&larr;',
                'next_text'    => '&rarr;',
                'type'         => 'list',
                'end_size'     => 3,
                'mid_size'     => 3
            ));
            ?>
        </nav>
    <?php
    }
}



/**
 * Displays breadcrumbs except on the homepage.
 *
 * Supports blog, single posts, pages, categories, tags, search, 404, and archives.
 * Adds "Blog" link for single posts.
 *
 * Usage: Call `custom_breadcrumbs()` in theme templates.
 */
function custom_breadcrumbs() {
    if (is_front_page()) return;

    echo '<div class="breadcrumbs"><div class="breadcrumbs-container">';
    echo '<a href="' . home_url() . '">Home</a> &rsaquo; ';

    if (is_home()) {
        echo 'Blog';
    } elseif (is_single()) {
        if (get_post_type() === 'post') {
            echo '<a href="' . get_permalink(get_option('page_for_posts')) . '">Blog</a> &rsaquo; ';
        }
        the_title();
    } elseif (is_page()) {
        the_title();
    } elseif (is_category() || is_tag() || is_tax()) {
        single_term_title();
    } elseif (is_search()) {
        echo 'Search results for "' . get_search_query() . '"';
    } elseif (is_404()) {
        echo '404 - Page not found';
    } elseif (is_archive()) {
        the_archive_title();
    }

    echo '</div></div>';
}