<?php
/*
**  Auto Play Image Video Slider Section
*/
?>
<?php
    $button_title_and_link = get_sub_field('button_title_and_link');
    $button_name = get_sub_field('button_name');
    $popup_main_heading = get_sub_field('popup_main_heading');
?>


<?php if (have_rows('select_video_file')) : ?>
        <?php while (have_rows('select_video_file')) : the_row(); ?>
        <?php 
            $file = get_sub_field('video'); 
            if (!empty($file)) : ?>
                <link rel="preload" href="<?php echo $file; ?>">
            <?php endif; ?>
        <?php endwhile; ?>

    <section class="hero-banner">
        <div class="swiper main_custom_slider">
            <div class="swiper-wrapper">
                <?php while (have_rows('select_video_file')) : the_row(); ?>
                    <?php 
                    $file = get_sub_field('video'); 
                    if (!empty($file)) :
                        $file_type = wp_check_filetype($file);
                        $file_ext = $file_type['ext']; ?>
                        <div class="swiper-slide">
                            <?php if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) : ?>
                                <img src="<?php echo esc_url($file); ?>" alt="Slide Image" loading="lazy">
                            <?php elseif (in_array($file_ext, ['mp4', 'webm', 'ogg'])) : ?>
                                <video id="hero-section-autoplay-video" src="<?php echo esc_url($file); ?>" loading="lazy" loop autoplay muted></video>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>
        </div>
        <div class="scroll_down_btm">
            <a href="#drifter-slider-section"><img src="<?php echo get_stylesheet_directory_uri().'/assets/img/scroll_down.png'; ?>" alt=""> scroll down</a>
        </div>
        <div class="explore_more_btn">
            <a href="<?php echo $button_title_and_link['url']; ?>"><?php echo $button_title_and_link['title']; ?>               
                <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path d="M15.7071 6.79289C16.0976 7.18342 16.0976 7.81658 15.7071 8.20711L9.34315 14.5711C8.95262 14.9616 8.31946 14.9616 7.92893 14.5711C7.53841 14.1805 7.53841 13.5474 7.92893 13.1569L13.5858 7.5L7.92893 1.84315C7.53841 1.45262 7.53841 0.819457 7.92893 0.428932C8.31946 0.0384078 8.95262 0.0384078 9.34315 0.428932L15.7071 6.79289ZM0 7.5L0 6.5L15 6.5V7.5V8.5L0 8.5L0 7.5Z" fill="white"/>
                </svg>                     
            </a>
        </div>
        <!-- New Updates Button code -->
        <?php if( have_rows('new_updates_popup') ): ?>
            <div class="new-updates-container">
                <div class="new-updates-tab desktop_view"><?php echo $button_name;?></div>

                <div class="new-updates-popup">
                    <div class="popup-header">
                    <button class="close-popup">                    
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.83023 21C4.66603 21 4.50552 20.9514 4.36898 20.8602C4.23245 20.769 4.12603 20.6393 4.06319 20.4876C4.00036 20.3359 3.98392 20.169 4.01596 20.0079C4.048 19.8469 4.12708 19.699 4.2432 19.5829L19.5829 4.24315C19.7386 4.08747 19.9498 4 20.17 4C20.3902 4 20.6013 4.08747 20.757 4.24315C20.9127 4.39884 21.0002 4.61 21.0002 4.83018C21.0002 5.05036 20.9127 5.26152 20.757 5.41721L5.41726 20.757C5.34024 20.8341 5.24873 20.8953 5.14799 20.937C5.04725 20.9788 4.93926 21.0001 4.83023 21Z" fill="white"/>
                            <path d="M20.1699 21C20.0609 21.0001 19.9529 20.9788 19.8522 20.937C19.7514 20.8953 19.6599 20.8341 19.5829 20.757L4.24315 5.41721C4.08747 5.26152 4 5.05036 4 4.83018C4 4.61 4.08747 4.39884 4.24315 4.24315C4.39884 4.08747 4.61 4 4.83018 4C5.05036 4 5.26152 4.08747 5.41721 4.24315L20.757 19.5829C20.8731 19.699 20.9522 19.8469 20.9842 20.0079C21.0162 20.169 20.9998 20.3359 20.937 20.4876C20.8741 20.6393 20.7677 20.769 20.6312 20.8602C20.4946 20.9514 20.3341 21 20.1699 21Z" fill="white"/>
                        </svg>
                    </button>
                    </div>

                    <div class="popup-content">
                        <?php
                            $banners_data = [];
                            while( have_rows('new_updates_popup') ): the_row();
                                $banners_data[] = [
                                    'sub_heading' => get_sub_field('sub_heading'),
                                    'heading'     => get_sub_field('heading'),
                                    'link'        => get_sub_field('link'),
                                    'image'       => get_sub_field('image'),
                                ];
                            endwhile;

                            $count = count($banners_data);
                        ?>

                        <?php if ($count === 2): ?>
                            <!-- Use only the "banner" structure -->
                            <div class="bottom-banners">
                                <?php foreach ($banners_data as $data): ?>
                                    <div class="banner">
                                        <?php if ($data['image']): ?>
                                            <img src="<?php echo esc_url($data['image']); ?>" />
                                        <?php endif; ?>
                                        <div class="overlay">
                                            <?php if ($data['heading']): ?><p><?php echo esc_html($data['heading']); ?></p><?php endif; ?>
                                            <?php if ($data['link']): ?><a href="<?php echo esc_url($data['link']); ?>">Shop Now →</a><?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <!-- Use the existing structure with top-banner and bottom-banners -->
                            <?php foreach ($banners_data as $i => $data): ?>
                                <?php if ($i === 0): ?>
                                    <div class="top-banner">
                                        <div class="banner-text">
                                            <?php if ($data['sub_heading']): ?><small><?php echo esc_html($data['sub_heading']); ?></small><?php endif; ?>
                                            <?php if ($data['heading']): ?><p><?php echo esc_html($data['heading']); ?></p><?php endif; ?>
                                            <?php if ($data['link']): ?><a href="<?php echo esc_url($data['link']); ?>" class="shop-now">Shop Now →</a><?php endif; ?>
                                        </div>
                                        <?php if ($data['image']): ?>
                                            <img src="<?php echo esc_url($data['image']); ?>" class="banner-img" />
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <?php if ($i === 1): ?><div class="bottom-banners"><?php endif; ?>
                                        <div class="banner">
                                            <?php if ($data['image']): ?>
                                                <img src="<?php echo esc_url($data['image']); ?>" />
                                            <?php endif; ?>
                                            <div class="overlay">
                                                <?php if ($data['heading']): ?><p><?php echo esc_html($data['heading']); ?></p><?php endif; ?>
                                                <?php if ($data['link']): ?><a href="<?php echo esc_url($data['link']); ?>">Shop Now →</a><?php endif; ?>
                                            </div>
                                        </div>
                                    <?php if ($i === $count - 1): ?></div><?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
         <!-- New Updates Button code end -->
    </section>
<?php endif; ?>


