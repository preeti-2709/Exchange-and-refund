<?php
/**
 * Template Name: Events Page
 */
get_header();

// Fetch all terms from the 'published_year' taxonomy
$taxonomy = 'published_year';
$post_type = 'event';

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
    <?php boxshop_breadcrumbs_title(true, 'Events'); ?>
</div>

<div class="event_page_wrap">
    <div class="container">
        <div class="event_title">
            <h2>STUDDS has been sponsoring or participating in events, exhibitions and trade fairs. Click to check out the details!</h2>
        </div>

        <div class="event-tabs">
            <a href="#" class="event-tab <?php echo (!get_query_var($taxonomy) ? 'active' : ''); ?>" data-year="">All</a>
            <?php foreach ($terms as $term) : ?>
                <a href="#" class="event-tab <?php echo (get_query_var($taxonomy) === $term->slug) ? 'active' : ''; ?>" data-year="<?php echo esc_attr($term->slug); ?>">
                    <?php echo esc_html($term->name); ?>
                </a>
            <?php endforeach; ?>
        </div>


        <?php
        $selected_term = get_query_var($taxonomy);
        $paged = max(1, get_query_var('paged') ? get_query_var('paged') : get_query_var('page'));

        $args = array(
            'post_type' => 'event',
            'posts_per_page' => 9,
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

        $event_query = new WP_Query($args);
        ?>

        <?php if ($event_query->have_posts()) : ?>
            <div class="event-grid"></div>
            <nav class="ts-pagination event-page"></nav>
        <?php else : ?>
            <div class="no-media-message">No events found for this year.</div>
        <?php endif; ?>

    </div>
</div>

<?php
wp_reset_postdata();
get_footer();
