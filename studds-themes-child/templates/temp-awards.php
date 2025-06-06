<?php
/**
 * Template Name: Awards Page
 */
get_header();

$bg_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
?>

<div class="category-banner event_section" style="background: url('<?php echo esc_url($bg_image_url); ?>');">
    <div class="category-overlay">
        <h1 class="container category-title"><?php echo get_the_title(); ?></h1>
    </div>
</div>

<div class="breadcrumbs">
    <?php boxshop_breadcrumbs_title(true, 'Awards'); ?>
</div>

<div class="container">
<?php
$args = array(
    'post_type'      => 'award',
    'posts_per_page' => -1, // Or a number like 10
    'post_status'    => 'publish'
);

$awards_query = new WP_Query($args);

if ($awards_query->have_posts()) :
    while ($awards_query->have_posts()) : $awards_query->the_post();
        ?>
        <div class="award-item">
            <?php if (has_post_thumbnail()) : ?>
                <div class="award-image">
                    <?php the_post_thumbnail('medium'); ?>
                </div>
            <?php endif; ?>
            
            <h3 class="award-title"><?php the_title(); ?></h3>
            
            <div class="award-description">
                <?php the_content(); ?>
            </div>
        </div>
        <?php
    endwhile;
    wp_reset_postdata();
else :
    echo '<p>No awards found.</p>';
endif;
?>

</div>

<?php get_footer(); ?>
