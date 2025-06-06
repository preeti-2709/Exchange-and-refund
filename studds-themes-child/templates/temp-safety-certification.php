<?php
/**
 * Template Name: Safety Certificaction
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
    <?php boxshop_breadcrumbs_title(true, 'Safety Certifiaction'); ?>
</div>

<div class="safety-certification-content">
    <?php echo get_the_content(); ?>
</div>

<div class="safety-certification-page">
    <?php if (have_rows('certification')): ?>
        <div class="container my-5">

        <?php while (have_rows('certification')): the_row(); 
            $section_title = get_sub_field('title'); 
            $has_data = have_rows('certificate_data'); ?>
            <?php if ($has_data): ?>
            <div class="mb-5">
                <?php if(!empty($section_title)):?>
                    <h3 class="fw-bold border-start border-3 ps-3 border-danger mb-4">
                        <?php echo esc_html($section_title); ?>
                    </h3>
                <?php endif; ?>

                <div class="row">
                <?php while (have_rows('certificate_data')): the_row(); 
                    $cert_name = get_sub_field('certificate_name');
                    $cert_img = get_sub_field('certificate_image');
                    if ($cert_img): ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                    <div class="text-center">
                        <img src="<?php echo esc_url($cert_img); ?>" alt="<?php echo esc_attr($cert_name); ?>" class="img-fluid border" />
                        <?php if ($cert_name): ?>
                        <p class="mt-2 fw-semibold"><?php echo esc_html($cert_name); ?></p>
                        <?php endif; ?>
                    </div>
                    </div>
                <?php endif; endwhile; ?>
                </div>
            </div>
            <?php endif; ?>

        <?php endwhile; ?>

        </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
