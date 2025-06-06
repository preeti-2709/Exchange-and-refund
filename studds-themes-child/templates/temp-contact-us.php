<?php 
/* Template Name: Contact Us Page */ 

get_header(); 

$bg_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
?>

<div class="category-banner event_section" style="background: url('<?php echo esc_url($bg_image_url); ?>');">
    <div class="category-overlay">
        <h1 class="container category-title"><?php echo get_the_title(); ?></h1>
    </div>
</div>

<div class="breadcrumbs">
    <?php boxshop_breadcrumbs_title(true, 'Contact Us'); ?>
</div>

<div class="care-and-maintenance-content">
    <?php echo get_the_content(); ?>
</div>


<div class="container my-5">
    <div class="row">
        <div class="col-lg-6 mb-4">

            <?php if ($main_text = get_field('main_text')) : ?>
                <h5 class="text-primary font-weight-bold mb-3"><?php echo esc_html($main_text); ?></h5>
            <?php endif; ?>

            <?php if (have_rows('studds_address')) : ?>
                <ul class="list-unstyled">
                    <?php while (have_rows('studds_address')) : the_row();
                        $icon = get_sub_field('icon');
                        $info = get_sub_field('info');
                        if (!empty($info)) : ?>
                            <li class="d-flex align-items-start mb-3">
                                <?php if (!empty($icon)) : ?>
                                    <img src="<?php echo esc_url($icon); ?>" alt="icon" class="mr-3" width="20" height="20">
                                <?php endif; ?>
                                <span><?php echo nl2br($info); ?></span>
                            </li>
                        <?php endif;
                    endwhile; ?>
                </ul>
            <?php endif; ?>

        </div>

        <div class="col-lg-6">
            <?php
            $latitude = get_field('latitude');
            $longitude = get_field('longitude');
            $shortcode = get_field('add_from_shortcode');

            if (!empty($shortcode)) {
                echo do_shortcode($shortcode);
            } 
            
            if (!empty($latitude) && !empty($longitude)) {
            ?>
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe 
                        width="100%" 
                        height="250" 
                        style="border:0;" 
                        loading="lazy" 
                        allowfullscreen 
                        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyC_vmsoOLQZdf8Ch79FdAshDT4sRnRa2GM&q=<?= esc_attr($latitude); ?>,<?= esc_attr($longitude); ?>">
                    </iframe>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
