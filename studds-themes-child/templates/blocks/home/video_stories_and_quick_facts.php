<?php
/*
** Video Section
*/
?>

<div class="video_story_sec">
    <!-- video section -->

    <?php
        $video_poster = get_sub_field('video_poster');
        $video_src = get_sub_field('video');
    ?>

<?php if (!empty($video_poster) && !empty($video_src)) : ?>
    <section class="video-section">
        <div class="video-content">
            <div class="container">
                <div class="video_content_wrap">
                    <?php if (get_sub_field('video_heading')) : ?>
                        <h2><?php echo esc_html(get_sub_field('video_heading')); ?></h2>
                    <?php endif; ?>

                    <?php if (get_sub_field('video_subheading')) : ?>
                        <p><?php echo esc_html(get_sub_field('video_subheading')); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="video_section_poster">
                <img src="<?php echo esc_url($video_poster); ?>" alt="">
                <div class="poster_overlay"></div>
                <img src="<?php echo get_stylesheet_directory_uri().'/assets/img/playbutton.png'; ?>" alt="Play Icon" data-bs-toggle="modal" data-bs-target="#exampleModal" class="play_btn_video" >
            </div>
            
        </div>
    </section>
<?php endif; ?>

    <!-- Modal -->
    <div class="modal fade video_content_modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">                      
                        <svg width="32" height="31" viewBox="0 0 32 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.08311 30.8742C1.78914 30.8742 1.50175 30.7871 1.2573 30.6238C1.01286 30.4605 0.822329 30.2284 0.709823 29.9568C0.597316 29.6852 0.567885 29.3863 0.625252 29.098C0.682618 28.8097 0.824205 28.5449 1.0321 28.337L28.4963 0.872842C28.775 0.594097 29.1531 0.4375 29.5473 0.4375C29.9415 0.4375 30.3195 0.594097 30.5983 0.872842C30.877 1.15159 31.0336 1.52965 31.0336 1.92385C31.0336 2.31806 30.877 2.69612 30.5983 2.97486L3.13412 30.439C2.99623 30.5772 2.8324 30.6868 2.65203 30.7615C2.47167 30.8361 2.27833 30.8744 2.08311 30.8742Z" fill="white"/>
                            <path d="M29.5472 30.8742C29.352 30.8744 29.1587 30.8361 28.9783 30.7615C28.7979 30.6868 28.6341 30.5772 28.4962 30.439L1.03202 2.97486C0.753277 2.69612 0.59668 2.31806 0.59668 1.92385C0.59668 1.52965 0.753277 1.15159 1.03202 0.872842C1.31077 0.594097 1.68883 0.4375 2.08303 0.4375C2.47724 0.4375 2.8553 0.594097 3.13404 0.872842L30.5982 28.337C30.8061 28.5449 30.9477 28.8097 31.0051 29.098C31.0624 29.3863 31.033 29.6852 30.9205 29.9568C30.808 30.2284 30.6175 30.4605 30.373 30.6238C30.1286 30.7871 29.8412 30.8742 29.5472 30.8742Z" fill="white"/>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                <div class="modal_video_sec">
                        <video src="<?php echo esc_url($video_src); ?>" loop="" autoplay="" muted="" controls="" ></video>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- video section -->

    <!-- Our stories -->
    <?php 
    /*
    **  Our Stories Section
    */ 
    $our_story_text = get_sub_field('our_story_text'); 
    $slides = get_sub_field('our_story_slider');
    ?>

    <?php if (!empty($slides)) : ?>
    <section class="thunder-product-slider our-stories-slider">
        <div class="swiper custom_slider">
            <div class="swiper-wrapper">
                <?php foreach ($slides as $slide) : 
                    $background_text = $slide['background_text'] ?? ''; 
                    $left_main_image = $slide['left_main_image'] ?? ''; 
                    $right_text = $slide['right_text'] ?? ''; 
                    $slider_image_thumbnail = $slide['slider_image_thumbnail'] ?? ''; 
                    $year_after_title = $slide['year'] ?? '';
                ?>
                    <div class="swiper-slide">
                        <?php if (!empty($background_text)) : ?>
                            <h1><?php echo esc_html($background_text); ?></h1>
                        <?php endif; ?>
                        <div class="our_story_wrap">
                            <div class="row">                          
                                <div class="col-lg-6 col-md-6">
                                    <div class="thunder-product-image">
                                        <?php if (!empty($left_main_image)) : ?>
                                            <img src="<?php echo esc_url($left_main_image); ?>" alt="<?php echo esc_attr($thumbnail_text); ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="thunder-product-Video">
                                        <?php if (!empty($right_video_link)) : ?>
                                            <iframe width="100%" height="174" src="<?php echo esc_url($right_video_link); ?>" title="<?php echo esc_attr($year); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                        <?php endif; ?>
                                        <div class="thunder-product-Video-content">
                                            <?php if (!empty($right_text)) : ?>
                                                <div class="thunder-product-text">
                                                    <?php if (!empty($our_story_text)) : ?>
                                                        <h2><?php echo esc_html($our_story_text); ?></h2>
                                                        <?php if (!empty($year_after_title)) : ?>
                                                            <span><?php echo esc_html($year_after_title); ?></span>
                                                        <?php endif; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <p><?php echo esc_html($right_text); ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($right_button_link) && !empty($right_button_title)) : ?>
                                                <a href="<?php echo esc_url($right_button_link); ?>"><?php echo esc_html($right_button_title); ?></a>
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

        <div class="thumbnail_slider_wrap">
            <div thumbsSlider="" class="thunder-product-thumb-slider swiper main_custom_slider">
                <div class="swiper-wrapper">
                    <?php foreach ($slides as $slide) : 
                        $slider_image_thumbnail = $slide['slider_image_thumbnail'] ?? ''; 
                        $year = $slide['year'] ?? ''; 
                    ?>
                        <div class="swiper-slide">
                            <?php if (!empty($slider_image_thumbnail)) : ?>
                                <img src="<?php echo esc_url($slider_image_thumbnail); ?>" alt="<?php echo esc_attr($year); ?>" />
                            <?php endif; ?>
                            <?php if (!empty($year)) : ?>
                                <h4><?php echo esc_html($year); ?></h4>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="our_story_navs">
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>

        <div class="container mobile_slider_wrap">
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

    </section>
    <?php endif; ?>
    <!-- Our stories -->

    <!-- Quick facts -->
    <?php
    /*
    ** Customer Focus Section
    */
    $background_image = get_sub_field('background_image'); 
    $heading = get_sub_field('heading'); 
    $background_text = get_sub_field('background_text'); 

    // if (!empty($background_image)) :
    ?>
    <section class="counter-section" style="background-image: url('<?php echo esc_url($background_image); ?>');">
        <div class="container">
            <div class="counter_sec_title">
                <?php if (!empty($heading)) : ?>
                    <h2><?php echo esc_html($heading); ?></h2>
                <?php endif; ?>
            </div>
            <?php // if (!empty($background_text)) : ?>
                <!-- <div class="new-launches-bg-text">
                    <?php echo esc_html($background_text); ?>
                </div> -->
            <?php // endif; ?>

            <div class="counter-wrapper">
                <?php if (have_rows('counter_section')) : ?>
                    <?php while (have_rows('counter_section')) : the_row(); ?>
                        <?php
                            $thumb = get_sub_field('thumbnail_image');
                            $counter_number = get_sub_field('counter_number');
                            $counter_suffix = get_sub_field('counter_suffix');
                            $counter_label = get_sub_field('counter_label');
                        ?>
                        <div class="counter-box">
                            <?php if (!empty($thumb)) : ?>
                                <div class="counter_box_img">
                                    <img src="<?php echo esc_url($thumb); ?>" alt="">
                                </div>
                            <?php endif; ?>

                            <div class="counter-box-dls">
                                <?php if (!empty($counter_number)) : ?>
                                    <span class="counter-number" data-target="<?php echo esc_attr($counter_number); ?>">0</span>
                                <?php endif; ?>
                                <?php if (!empty($counter_suffix)) : ?>
                                    <span class="counter_surfix"><?php echo $counter_suffix; ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($counter_label)) : ?>
                                <p class="counter-label"><?php echo esc_html($counter_label); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php // endif; ?>

</div>
<!-- Quick facts -->