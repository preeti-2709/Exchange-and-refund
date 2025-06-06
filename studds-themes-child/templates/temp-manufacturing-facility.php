<?php 
/* Template Name: Manufacturing Facility Page */ 
get_header(); 
$bg_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
?>

<div class="category-banner event_section" style="background: url('<?php echo esc_url($bg_image_url); ?>');">
    <div class="category-overlay">
        <h1 class="container category-title"><?php echo get_the_title(); ?></h1>
    </div>
</div>

<div class="breadcrumbs">
    <?php boxshop_breadcrumbs_title(true, 'Manufacturing Facility'); ?>
</div>

<div class="care-and-maintenance-content">
    <?php echo get_the_content(); ?>
</div>
<div class="manufacturing-facility-page">
    <?php if( have_rows('manufacturing_facility_list') ): ?>
        <div class="container py-5">
            <div class="row">
                <?php while( have_rows('manufacturing_facility_list') ): the_row(); 
                    $name = get_sub_field('name_of_unit');
                    $address = get_sub_field('address');
                    $zip = get_sub_field('zip_code');
                    $phone = get_sub_field('phone_number');
                    $email = get_sub_field('email_id');
                    $lat = get_sub_field('latitude');
                    $lng = get_sub_field('longitude');
                ?>

                <?php if( $name && $address && $zip && $phone && $email && $lat && $lng ): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title text-uppercase font-weight-bold"><?= esc_html($name); ?></h5>
                                <p class="card-text mb-1"><?= esc_html($address); ?>, <?= esc_html($zip); ?></p>
                                <p class="card-text mb-1"><strong>Phone:</strong> <?= esc_html($phone); ?></p>
                                <p class="card-text"><strong>Email:</strong> <a href="mailto:<?= esc_attr($email); ?>"><?= esc_html($email); ?></a></p>
                            </div>
                            <div class="card-footer p-0">
                                <iframe 
                                    width="100%" 
                                    height="250" 
                                    style="border:0;" 
                                    loading="lazy" 
                                    allowfullscreen 
                                    src="https://www.google.com/maps/embed/v1/place?key=AIzaSyC_vmsoOLQZdf8Ch79FdAshDT4sRnRa2GM&q=<?= esc_attr($lat); ?>,<?= esc_attr($lng); ?>">
                                </iframe>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>
</div>


<?php
get_footer(); 
?>