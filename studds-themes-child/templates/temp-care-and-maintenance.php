<?php
/**
 * Template Name: Care & Maintenance
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
    <?php boxshop_breadcrumbs_title(true, 'Care and Maintenance'); ?>
</div>

<div class="care-and-maintenance-content">
    <?php echo get_the_content(); ?>
</div>

<?php

$helmet_care_title = get_field('helmet_care_title');
$helmet_care_content = get_field('helmet_care_content');
$helmet_care_image = get_field('helmet_care_right_image');
$instruction_title = get_field('instruction_title');
$instruction_content = get_field('instruction_content');
$maintenance_and_cleaning_title = get_field('maintenance_and_cleaning_title');
$maintenance_and_cleaning_content = get_field('maintenance_and_cleaning_content');
?>

<div class="care-maintenance-page">
    <div class="container py-5">

    <?php if (!empty($helmet_care_title) || !empty($helmet_care_content) || !empty($helmet_care_image)) : ?>
        <div class="bg-light p-4 rounded mb-5">
            <div class="row">
                <?php if (!empty($helmet_care_title) || !empty($helmet_care_content)) : ?>
                    <div class="col-6">
                        <?php if (!empty($helmet_care_title)) : ?>
                            <h2 class="mb-3 fw-bold"><?php echo esc_html($helmet_care_title); ?></h2>
                        <?php endif; ?>

                        <?php if (!empty($helmet_care_content)) : ?>
                            <div><?php echo wp_kses_post($helmet_care_content); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($helmet_care_image)) : ?>
                    <div class="col-6">
                        <img src="<?php echo esc_url($helmet_care_image); ?>" alt="Helmet Care Image" class="img-fluid">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="helmet-usage-instructions">
        <div class="mb-5">
        <?php if (!empty($instruction_title)) : ?>
                <h2 class="fw-bold mb-4"><?php echo esc_html($instruction_title); ?></h2>
            <?php endif; ?>

            <div class="row">
                <?php if (have_rows('instruction_content')) :
                    while (have_rows('instruction_content')) : the_row();
                        $sub_title = get_sub_field('title');
                        $sub_content = get_sub_field('content');

                        if (!empty($sub_title) || !empty($sub_content)) : ?>
                            <div class="col-md-6 mb-4">
                                <?php if (!empty($sub_title)) : ?>
                                    <h4 class="fw-semibold mb-3"><?php echo esc_html($sub_title); ?></h4>
                                <?php endif; ?>

                                <?php if (!empty($sub_content)) : ?>
                                    <div><?php echo wp_kses_post($sub_content); ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endif;
                    endwhile;
                endif; ?>
            </div>

            <?php if (have_rows('helmet_usage_images')) : ?>
                <div class="row text-center mt-4">
                    <?php
                    $image_count = 1;
                    while (have_rows('helmet_usage_images')) : the_row();
                        $image = get_sub_field('image');
                        if (!empty($image)) :
                    ?>
                        <div class="col-4 col-md-2 mb-4">
                            <img src="<?php echo esc_url($image); ?>" alt="Helmet usage images" class="img-fluid mb-2">
                            <!-- <p class="mb-0">Picture <?php //echo $image_count++; ?></p> -->
                        </div>
                    <?php endif; endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if (have_rows('other_instructions')) : ?>
            <div class="bg-light py-4 px-3 px-md-5 rounded mb-5">
                <div class="row">
                    <?php while (have_rows('other_instructions')) : the_row(); 
                        $title = get_sub_field('title');
                        $content = get_sub_field('content');

                        if (!empty($title) || !empty($content)) : ?>
                            <div class="col-md-4 mb-4 mb-md-0">
                                <?php if (!empty($title)) : ?>
                                    <h5 class="fw-bold mb-2"><?php echo esc_html($title); ?></h5>
                                <?php endif; ?>
                                
                                <?php if (!empty($content)) : ?>
                                    <div class="text-muted">
                                        <?php echo wp_kses_post($content); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; 
                    endwhile; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($maintenance_and_cleaning_title) || !empty($maintenance_and_cleaning_content)) : ?>
    <div class="bg-dark text-white py-5">
        <div class="container">
        <?php if (!empty($maintenance_and_cleaning_title)) : ?>
            <h2 class="text-uppercase mb-4"><?php echo esc_html($maintenance_and_cleaning_title); ?></h2>
        <?php endif; ?>

        <?php if (!empty($maintenance_and_cleaning_content)) : ?>
            <div class="maintenance-content">
            <?php echo wp_kses_post($maintenance_and_cleaning_content); ?>
            </div>
        <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>
