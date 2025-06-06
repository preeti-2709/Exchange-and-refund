<?php
/*
** Drifter Slider Section
*/

$section_heading = get_sub_field('section_heading');
$drifter_slides = get_sub_field('drifter_slider');

if (!empty($drifter_slides)) :
    $slide_count = count($drifter_slides);
?>
<section class="drifter-slider-section" id="drifter-slider-section">
    <div class="container">
        <?php if (!empty($section_heading)) : ?>
            <div class="categories_title section-heading">
                <h2><?php echo esc_html($section_heading); ?></h2>
            </div>
        <?php endif; ?>

        <?php if ($slide_count > 1) : ?>
            <div class="swiper drifter-slider-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($drifter_slides as $slide) :
                        $slide_img = $slide['slider_image'];
                        if (!empty($slide_img)) :
                    ?>
                        <div class="swiper-slide">
                            <img src="<?php echo esc_url($slide_img); ?>" alt="Drifter Slide Image">
                        </div>
                    <?php endif; endforeach; ?>
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        <?php else : ?>
            <?php 
            $single_slide = $drifter_slides[0];
            if (!empty($single_slide['slider_image'])) : ?>
                <div class="drifter-single-image">
                    <img src="<?php echo esc_url($single_slide['slider_image']); ?>" alt="Drifter Image">
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
