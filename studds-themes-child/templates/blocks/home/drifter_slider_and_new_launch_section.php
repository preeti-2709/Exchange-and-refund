<div class="dc-and-new-launch-section">
    
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
                        <div class="swiper drifter-slider-swiper desktop_view">
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

                    <div class="mobile_view dc_collection_banner">
                        <div class="collection_wrap">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/img/dc_collection_banner.jpg'; ?>" alt="Banner">
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

    <?php
        /*
        ** New Launch Section
        */

        $new_launches_heading = get_sub_field('new_launches_heading'); 
        $new_launches_text = get_sub_field('new_launches_text', false, false); 
        $new_launches_product = get_sub_field('new_launches_product');

        $product_id = $new_launches_product->ID;
        $thumbnail_id = get_post_thumbnail_id($product_id);

        $slider_image_thumbnail = get_the_post_thumbnail_url($product_id, 'full'); 

        $background_text = get_field('new_launches_background_text', $product_id);
        $video_link = get_field('product_video_link', $product_id); 

        // $video_name = $video_link_and_name['title'];
        // $video_link = $video_link_and_name['url'];

        $explore_more_button = get_sub_field('explore_more_button');
        $video_button_text = get_sub_field('video_button_text');


        /*
        ** Sub categories must be selected to show this - sub categories/model 
        */ 
        $subcategory_text = '';
        $product_cats = get_the_terms($product_id, 'product_cat');
        if (!empty($product_cats) && !is_wp_error($product_cats)) {
            foreach ($product_cats as $cat) {
                if ($cat->parent != 0) {
                    $subcategory_text = strtok($cat->name, ' ');
                    break; 
                }
            }
        }


    ?>

    <?php if(!empty($slider_image_thumbnail)): ?>
        <section class="launch-section">
            <div class="container">
                
                    <div class="new_launches_title">
                        <?php if (!empty($new_launches_heading)) : ?>
                            <h2><?php echo $new_launches_heading; ?></h2>
                        <?php endif; ?>
                    </div>
                    <div class="launch-content">
                        <div class="tagline_vertical tablate_show">
                        <?php if (!empty($subcategory_text)) : ?>
                            <span><?php echo $subcategory_text; ?></span>
                        <?php endif; ?>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                            <div class="tagline_title">
                            <?php if (!empty($new_launches_text)) : ?>
                                <h2><?php echo $new_launches_text; ?></h2>
                            <?php endif; ?>
                            </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                            <div class="tagline_img">
                                <img src="<?php echo $slider_image_thumbnail ?>" alt="">
                            </div>
                            </div>
                            <div class="col-lg-2 tablate_hide ">
                            <div class="tagline_vertical">
                            <?php if (!empty($subcategory_text)) : ?>
                                <span class="test"><?php echo $subcategory_text; ?></span>
                            <?php endif; ?>
                            </div>
                            </div>
                        </div>
                        <div class="row explore_rows">
                            <div class="col-lg-4 col-md-6">
                            <div class="left-column">
                                <div class="buttons">
                                <a href="<?php echo $explore_more_button['url']; ?>" class="btn explore"><?php echo $explore_more_button['title']; ?></a>
                                <div class="youtube_slide" data-bs-toggle="modal" data-bs-target="#youtube_video">
                                    <div class="btn play"> <img src="<?php echo get_stylesheet_directory_uri().'/assets/img/playbutton.svg'; ?>" alt=""><?php echo $video_button_text; ?></div>
                                </div>
                                </div>
                            </div> 
                            </div>

                            <!-- Modal Code -->
                            <div class="modal fade video_content_modal" id="youtube_video" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="modal_video_sec">
                                                <?php if (!empty($video_link)) : ?>
                                                <iframe 
                                                    id="video_iframe"
                                                    src="<?php echo $video_link; ?>" 
                                                    title="YouTube video"
                                                    frameborder="0" 
                                                    allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                    allowfullscreen>
                                                </iframe>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Code end -->

                            <div class="col-lg-8 col-md-6">
                            <div class="right-column">
                                <?php if( have_rows('product_features', $product_id) ): ?>
                                    <div class="features swiper helmet-parts-swiper">
                                        <div class="swiper-wrapper">
                                            <?php while( have_rows('product_features', $product_id) ): the_row(); 
                                                $image = get_sub_field('featured_image');
                                                $name = get_sub_field('featured_title');
                                            ?>
                                                <div class="feature swiper-slide">
                                                    <?php if( $image ): ?>
                                                        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($name); ?>">
                                                    <?php endif; ?>
                                                    <p><?php echo esc_html($name); ?></p>
                                                </div>
                                            <?php endwhile; ?>
                                        </div>
                                        <!-- Optional Pagination or Navigation -->
                                    </div>
                                    <div class="swiper-button-prev mobile_view"></div>
                                    <div class="swiper-button-next mobile_view"></div>
                                    <div class="swiper-pagination helmet-parts-pagination custom"></div>
                                <?php endif; ?>
                            </div>
                            </div>
                        </div>     
                    
                    </div>
                
            </div>
        </section>
    <?php endif; ?>
</div>