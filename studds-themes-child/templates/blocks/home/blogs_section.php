<?php
/*
** Blog Section
*/

    $main_heading = get_sub_field('main_heading'); 
    $show_selected_blogs_or_latest = get_sub_field('show_selected_blogs_or_latest');
    $select_blogs = get_sub_field('select_blogs');
    if ($show_selected_blogs_or_latest == 'selected' && !empty($select_blogs)) {
        $args = [
            'post_type'      => 'post',
            'post__in'       => $select_blogs,
            'orderby'        => 'post__in',
            'posts_per_page' => -1,
        ];
    } else {
        $args = [
            'post_type'      => 'post',
            'posts_per_page' => 3,
            'order'          => 'DESC',
            'orderby'        => 'date',
        ];
    }

$query = new WP_Query($args);
$post_count = $query->post_count;

$hide_arrows_class = ($post_count <= 3) ? 'hide-swiper-arrows' : '';
      
?>
<section class="blog-section">
    <div class="container">

        <div class="title-and-icon <?php echo $hide_arrows_class; ?>">
            <?php if ($post_count > 3): ?>
                <div class="swiper-button-prev"></div>
            <?php endif; ?>
                <div class="section-header">
                    <span>our</span>
                    <h2><?php echo esc_html($main_heading); ?></h2>
                </div>
            <?php if ($post_count > 3): ?>
                <div class="swiper-button-next"></div>
            <?php endif; ?>
        </div>

        <div class="swiper blog-section-carousel">
            <div class="swiper-wrapper">
                <?php if ($query->have_posts()) : 
                    while ($query->have_posts()) : $query->the_post();
                        $post_id      = get_the_ID();
                        $post_title   = get_the_title();
                        $post_date    = get_the_date('d M Y');
                        $post_excerpt = wp_trim_words(get_the_excerpt(), 20);
                        $post_image   = get_the_post_thumbnail_url($post_id, 'large');
                        $post_link    = get_permalink();
                        ?>
                        <div class="swiper-slide blog-card">
                            <a href="<?php echo esc_url($post_link); ?>" class="blog-link">
                                <div class="blog-image">
                                    <?php if ($post_image) : ?>
                                        <img src="<?php echo esc_url($post_image); ?>" alt="<?php echo esc_attr($post_title); ?>">
                                    <?php else : ?>
                                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/img/placeholder-default-image.webp'); ?>" alt="Default Image">
                                    <?php endif; ?>
                                </div>
                                <div class="blog-content">
                                    <span class="blog-date"><?php echo esc_html($post_date); ?></span>
                                    <h3 class="blog-title"><?php echo esc_html($post_title); ?></h3>
                                    <!-- <p class="blog-excerpt"><?php //echo esc_html($post_excerpt); ?></p> -->
                                </div>
                            </a>
                        </div>
                    <?php endwhile;
                    wp_reset_postdata();
                else : ?>
                    <p>No blog posts found.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class=" mobile_slider_wrap">
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
</section>

