<?php
/*
** Testimonial Slider Section
*/
$heading = get_sub_field('heading'); 
$show_testimonial = get_sub_field('show_testimonial'); 
$select_testimonial = get_sub_field('select_testimonial'); 

// Prepare testimonial query
$args = array(
    'post_type' => 'ts_testimonial',
    'posts_per_page' => -1,
);

// If "Show Selected Testimonials" is selected
if ($show_testimonial === 'selected_one' && !empty($select_testimonial)) {
    $args['post__in'] = wp_list_pluck($select_testimonial, 'ID');
    $args['orderby'] = 'post__in';
}

$testimonials = new WP_Query($args);
?>

<section class="testimonial_slider">
    <div class="container">
        <div class="testimonial_title">
            <?php if (!empty($heading)) : ?>
                <h2><?php echo esc_html($heading); ?></h2>
            <?php endif; ?>
        </div>

        <?php if ($testimonials->have_posts()) : ?>
            <div class="swiper-button-prev desktop_view"></div>
            <div class="swiper testi_review_section">
                <div class="swiper-wrapper">
                    <?php while ($testimonials->have_posts()) : $testimonials->the_post(); ?>
                        <?php
                        $title = get_the_title();
                        $content = get_the_content();
                        $designation = get_field('designation');
                        $rating = get_field('ts_rating');
                        ?>
                        <div class="swiper-slide">
                            <div class="review_box_wrap">
                                <div class="review_box">
                                    <div class="testimonial_dls">
                                        <?php if (!empty($title)) : ?>
                                            <h2><?php echo esc_html($title); ?></h2>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($designation)) : ?>
                                            <span><?php echo esc_html($designation); ?></span>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($content)) : ?>
                                            <p><?php echo $content; ?></p>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($rating) && is_numeric($rating)) : ?>
                                            <div class="star_group">
                                                <?php
                                                for ($i = 1; $i <= intval($rating); $i++) {
                                                    echo '<img src="' . get_stylesheet_directory_uri() . '/assets/img/review_star.svg" alt="star">';
                                                }
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="swiper-button-next desktop_view"></div>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <p>No testimonials found.</p>
        <?php endif; ?>

        <div class="mobile_for_wrap">
            <div class="mobile_slider_wrap">
                    <div class="slider-status">
                        <div class="count"><span class="current">01</span> / <span class="total">6</span></div>
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                    </div>
                    <div class="prev-and-next">
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </div>
            </div>
    </div>
</section>
