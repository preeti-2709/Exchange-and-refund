<?php

/**
 * Template Name: Media Page
 */
get_header();

// Fetch all terms from the 'published_year' taxonomy
$taxonomy = 'published_year';
$post_type = 'studds-media';

$all_terms = get_terms(array(
    'taxonomy' => $taxonomy,
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'DESC',
));

$terms = [];

if (!is_wp_error($all_terms)) {
    foreach ($all_terms as $term) {
        $term_query = new WP_Query(array(
            'post_type' => $post_type,
            'posts_per_page' => 1,
            'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => $term->term_id,
                ),
            ),
            'fields' => 'ids',
        ));

        if ($term_query->have_posts()) {
            $terms[] = $term;
        }

        wp_reset_postdata();
    }
}

$bg_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
?>

<div class="category-banner event_section" style="background: url('<?php echo esc_url($bg_image_url); ?>');">
    <div class="category-overlay">
        <h1 class="container category-title"><?php echo get_the_title(); ?></h1>
    </div>
</div>

<div class="breadcrumbs">
    <?php boxshop_breadcrumbs_title(true, 'Media'); ?>
</div>

<div class="event_page_wrap media_page_wrap">
    <div class="container">
        <div class="event_title">
            <h2><?php echo get_the_content(); ?></h2>
        </div>

        <div class="media-tabs">
            <a href="#" class="media-tab active" data-year="">All</a>
            <?php foreach ($terms as $term): ?>
                <a href="#" class="media-tab" data-year="<?php echo esc_attr($term->slug); ?>">
                    <?php echo esc_html($term->name); ?>
                </a>
            <?php endforeach; ?>
        </div>


        <?php
        $selected_term = isset($_GET['year']) ? sanitize_text_field($_GET['year']) : '';
        // $paged = max(1, get_query_var('paged') ? get_query_var('paged') : get_query_var('page'));
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;


        $args = array(
            'post_type' => 'studds-media',
            'posts_per_page' => 4,
            'paged' => $paged,
        );

        if ($selected_term) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $selected_term,
                ),
            );
        }

        $media_query = new WP_Query($args);
        ?>

        <?php if ($media_query->have_posts()) : ?>
            <div class="media-grid"></div>
            <nav class="ts-pagination media-page"></nav>

        <?php else : ?>
            <div class="no-media-message">No media items found for this year.</div>
        <?php endif; ?>

    </div>
</div>

<?php
wp_reset_postdata();
get_footer();
?>