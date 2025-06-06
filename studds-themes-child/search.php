<?php
/**
 * Template Name: Blog Template
 */
global $boxshop_page_datas, $boxshop_theme_options;
get_header();

$extra_class = "";
$page_column_class = boxshop_page_layout_columns_class($boxshop_page_datas['ts_page_layout']);
$show_breadcrumb = (!is_home() && !is_front_page() && isset($boxshop_page_datas['ts_show_breadcrumb']) && absint($boxshop_page_datas['ts_show_breadcrumb']) == 1);
$show_page_title = (!is_home() && !is_front_page() && absint($boxshop_page_datas['ts_show_page_title']) == 1);
$featured_img_url = get_the_post_thumbnail_url(get_queried_object_id(), 'full');

// Get search keyword
$search_query = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

// Get correct pagination page
$paged = 1;
if ( get_query_var('paged') ) {
    $paged = get_query_var('paged');
} elseif ( get_query_var('page') ) {
    $paged = get_query_var('page');
} elseif ( isset($_GET['page']) ) {
    $paged = intval($_GET['page']);
}
?>
<!-- Banner Section -->
<div class="category-banner blog_section" style="background: url('<?php echo esc_url($featured_img_url); ?>');">
    <div class="category-overlay">
        <h1 class="container category-title">
            <?php printf(__('Search Results for: %s', 'boxshop'), '<span>' . esc_html($search_query) . '</span>'); ?>
        </h1>
    </div>
</div>

<?php
if (($show_breadcrumb || $show_page_title) && isset($boxshop_theme_options['ts_breadcrumb_layout'])) {
    $extra_class = 'show_breadcrumb_' . $boxshop_theme_options['ts_breadcrumb_layout'];
}
boxshop_breadcrumbs_title($show_breadcrumb, woocommerce_page_title(false));
?>

<div class="page-template blog-template container container-post <?php echo esc_attr($extra_class); ?> blog_container">

    <!-- Search Form -->
    <div class="bloglisting_header">
        <div class="blank_search"></div>
        <form method="get" action="<?php echo esc_url(home_url('/blog/')); ?>" id="blog-search-form">
            <div class="search-table">
                <div class="search-field search-content">
                    <input type="text" name="search" id="search" placeholder="Search Blogs" value="<?php echo esc_attr($search_query); ?>" autocomplete="off">
                    <input type="hidden" name="post_type" value="post">
                </div>
                <div class="search-button">
                    <input type="submit" id="searchsubmit" value="">
                </div>
            </div>
        </form>
    </div>

    <!-- Page Slider -->
    <?php if ($boxshop_page_datas['ts_page_slider'] && $boxshop_page_datas['ts_page_slider_position'] == 'before_main_content'): ?>
        <div class="top-slideshow">
            <div class="top-slideshow-wrapper">
                <?php boxshop_show_page_slider(); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Left Sidebar -->
    <?php if ($page_column_class['left_sidebar']): ?>
        <aside id="left-sidebar" class="ts-sidebar <?php echo esc_attr($page_column_class['left_sidebar_class']); ?>">
            <?php if (is_active_sidebar($boxshop_page_datas['ts_left_sidebar'])): ?>
                <?php dynamic_sidebar($boxshop_page_datas['ts_left_sidebar']); ?>
            <?php endif; ?>
        </aside>
    <?php endif; ?>

    <!-- Main Content -->
    <div id="main-content" class="<?php echo esc_attr($page_column_class['main_class']); ?> main_blog_details">
        <div id="primary" class="site-content blog_listing">
            <?php
            $posts_per_page = 12;
            $offset = ($paged - 1) * $posts_per_page;

            // Featured posts - First 3 posts
            $args_featured = array(
                'post_type' => 'post',
                's' => $search_query,
                'posts_per_page' => 3,
                'offset' => $offset,
            );

            $query_featured = new WP_Query($args_featured);

            if ($query_featured->have_posts()):
                echo '<div class="custom-blog-featured list_post_column">';
                $count = 0;
                while ($query_featured->have_posts()): $query_featured->the_post();
                    if ($count == 0) {
                        echo '<div class="post-wrapper blog_list">';
                        get_template_part('content', get_post_format());
                        echo '</div><div class="post-wrapper blog_list_two second-post">';
                    } else {
                        get_template_part('content', get_post_format());
                    }
                    $count++;
                endwhile;
                echo '</div>'; 
                echo '</div>';
                wp_reset_postdata();
            endif;

            // Grid posts - Remaining 9 posts
            $args_grid = array(
                'post_type' => 'post',
                's' => $search_query,
                'posts_per_page' => 9,
                'offset' => $offset + 3,
                'paged' => $paged,
            );

            $query_grid = new WP_Query($args_grid);
			$total_posts_found = $query_grid->found_posts;

            if ($query_grid->have_posts()):
                echo '<div class="custom-blog-grid list-posts">';
                while ($query_grid->have_posts()): $query_grid->the_post();
                    echo '<div class="post-wrapper">';
                    get_template_part('content', get_post_format());
                    echo '</div>';
                endwhile;
                echo '</div>';
                wp_reset_postdata();
            endif;

            // Pagination
            boxshop_custom_pagination($query_grid);
            ?>

        </div>
    </div>
</div>

<?php get_footer(); ?>
