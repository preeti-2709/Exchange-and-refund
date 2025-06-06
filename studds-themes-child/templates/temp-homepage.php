<?php /* Template Name: Home Page */ 
get_header(); 
?>

<!-- Carousel Section -->
<?php if (have_rows('home_flexible_content')) : ?>
    <?php while (have_rows('home_flexible_content')) : the_row(); ?>
        <?php echo get_template_part('templates/blocks/home/' . get_row_layout()); ?>
    <?php endwhile; ?>
<?php endif; ?>

<?php
get_footer(); 
?>