<?php
/*
**  Accessories Section
*/
$accessories_text = get_sub_field('accessories_text');
$accessories_category_slider = get_sub_field('accessories_category_slider');
?>

<?php if (!empty($accessories_category_slider)) : ?>
    <section class="thunder-product-slider accessories_slider">
        <div class="swiper custom_slider test">
            <div class="thunder-product-text">
                <?php if (!empty($accessories_text)) : ?>
                    <h2><?php echo esc_html($accessories_text); ?></h2>
                <?php endif; ?>
            </div>
            <div class="swiper-wrapper">
                <?php
                foreach ($accessories_category_slider as $accessories_category) :

                    $accessory_id = $accessories_category->term_id;
                    $accessory_name = $accessories_category->name;
                    $accessory_image = get_field('category_background_image', 'product_cat_' . $accessory_id);
                    $accessory_description = $accessories_category->description;
                    $accessory_category_button_link = esc_url(get_term_link($accessories_category));
                ?>
                    <div class="swiper-slide">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-5 col-md-6">
                                    <div class="thunder-product-image">
                                        <?php if (!empty($accessory_image)) : ?>
                                            <img src="<?php echo esc_url($accessory_image); ?>" alt="<?php echo esc_attr($thumbnail_text); ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-7 col-md-6">
                                    <div class="thunder-product-Video">

                                        <div class="thunder-product-Video-content">
                                            <?php if (!empty($accessory_name)) : ?>
                                                <h3><?php echo esc_html($accessory_name); ?></h3>
                                            <?php endif; ?>
                                            <?php if (!empty($accessory_description)) : ?>
                                                <p><?php echo esc_html($accessory_description); ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($accessory_category_button_link)) : ?>
                                                <a href="<?php echo esc_url($accessory_category_button_link); ?>">Shop Now</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="container mobile_for_wrap">
            <div class="mobile_slider_wrap mobile_view">
                <div class="slider-status">
                    <div class="count"><span class="current">01</span> / <span class="total">6</span></div>
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                </div>
                <div class="prev-and-next">
                    <!-- <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div> -->
                </div>
            </div>
            <div class="testi_thumb_slider">
                <?php //if (count($slides) > 6) : 
                ?>
                <div class="swiper-button-prev"></div>
                <?php //endif; 
                ?>
                <div thumbsSlider="" class="thunder-product-thumb-slider swiper main_custom_slider">
                    <div class="swiper-wrapper">
                        <?php // foreach ($slides as $slide) : 
                        foreach ($accessories_category_slider as $accessories_category) :

                            $accessory_id = $accessories_category->term_id;
                            $icon_and_points = get_field('icon_and_points', 'product_cat_' . $accessory_id);
                            $thumbnail_id = get_term_meta($accessory_id, 'thumbnail_id', true);
                            $accessory_center_image = wp_get_attachment_url($thumbnail_id);
                            $thumbnail_text = $accessories_category->name;

                        ?>
                            <div class="swiper-slide">
                                <?php if (!empty($accessory_center_image)) : ?>
                                    <div class="swiper_corner_block">
                                        <img src="<?php echo esc_url($accessory_center_image); ?>" alt="<?php echo esc_attr($thumbnail_text); ?>" />
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($thumbnail_text)) : ?>
                                    <h4><?php echo esc_html($thumbnail_text); ?></h4>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php //if (count($slides) > 6) : 
                ?>
                <div class="swiper-button-next"></div>
                <?php //endif; 
                ?>
            </div>
        </div>


    </section>
<?php endif; ?>