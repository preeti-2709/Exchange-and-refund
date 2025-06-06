<?php

/**
 * Register custom shortcodes.
 *
 * @since 1.0.0
 */

 // Product Feature
 add_shortcode('product_features_split', 'product_features_split_shortcode');

 // Helmet Parts Slider 
 add_shortcode('helmet_parts_slider', 'helmet_parts_slider_shortcode');

/* Register custom shortcodes end */  


/**
 * Shortcode: [product_features_split]
 *
 * Displays product features (from ACF repeater field) in two balanced columns.
 * Each feature includes an optional image, title, and description.
 * Automatically splits the features into two columns based on count.
 *
 * Usage:
 * - Add ACF repeater field `product_features` with subfields:
 *   - `featured_image` (Image)
 *   - `featured_title` (Text)
 *   - `featured_content` (Textarea or WYSIWYG)

 * @return string HTML content for the two-column product features layout.
 * @since 1.0.0
 */
function product_features_split_shortcode()
{
    ob_start();

    $features = get_field('product_features');

    if ($features && is_array($features)) {

        // Split the array into two columns
        $total = count($features);
        $half = ceil($total / 2);
        $left_column = array_slice($features, 0, $half);
        $right_column = array_slice($features, $half);
    ?>

        <div class="product-features-grid" style="display: flex; gap: 30px; flex-wrap: wrap;">

            <!-- Left Column -->
            <div class="left-column">
                <?php foreach ($left_column as $feature) :
                    $image = $feature['featured_image']['url'] ?? '';
                    $title = $feature['featured_title'] ?? '';
                    $content = $feature['featured_content'] ?? '';

                    if (empty($image) && empty($title) && empty($content)) continue;
                ?>
                    <div class="feature-item">
                        <?php if ($image) : ?>
                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
                        <?php endif; ?>
                        <?php if ($title) : ?>
                            <h4><?php echo esc_html($title); ?></h4>
                        <?php endif; ?>
                        <?php if ($content) : ?>
                            <p><?php echo esc_html($content); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Right Column -->
            <div class="right-column" style="flex: 1; min-width: 300px;">
                <?php foreach ($right_column as $feature) :
                    $image = $feature['featured_image']['url'] ?? '';
                    $title = $feature['featured_title'] ?? '';
                    $content = $feature['featured_content'] ?? '';

                    if (empty($image) && empty($title) && empty($content)) continue;
                ?>
                    <div class="feature-item">
                        <?php if ($image) : ?>
                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
                        <?php endif; ?>
                        <?php if ($title) : ?>
                            <h4><?php echo esc_html($title); ?></h4>
                        <?php endif; ?>
                        <?php if ($content) : ?>
                            <p><?php echo esc_html($content); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>

    <?php
    }

    return ob_get_clean();
}

/**
 * Shortcode: [helmet_parts_slider]
 *
 * Displays a Swiper-powered slider of helmet parts using ACF repeater field `helmet_parts`.
 * Each slide shows an optional image, name, and price of the helmet part.
 * Includes Swiper navigation buttons and responsive breakpoints for multiple devices.
 *
 * Usage:
 * - Add ACF repeater field `helmet_parts` with subfields:
 *   - `helmet_part_image` (Image)
 *   - `helmet_part_name` (Text)
 *   - `helmet_part_price` (Number or Text)

 * Requirements:
 * - Swiper JS and CSS must be enqueued separately.
 *
 * @return string HTML markup and JavaScript for the helmet parts slider.
 * @since 1.0.0
 */

function helmet_parts_slider_shortcode()
{
    ob_start();

    $helmet_parts = get_field('helmet_parts');
    if ($helmet_parts && is_array($helmet_parts)) {
    ?>
        <div class="swiper helmet-parts-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($helmet_parts as $part) :
                    $image = $part['helmet_part_image'] ?? '';
                    $name = $part['helmet_part_name'] ?? '';
                    $price = $part['helmet_part_price'] ?? '';

                    // Skip if all are empty
                    if (empty($image) && empty($name) && empty($price)) continue;
                ?>
                    <div class="swiper-slide">
                        <?php if (!empty($image)) : ?>
                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($name); ?>" style="max-width:100%;">
                        <?php endif; ?>

                        <?php if (!empty($name)) : ?>
                            <h3><?php echo esc_html($name); ?></h3>
                        <?php endif; ?>

                        <?php if (!empty($price)) : ?>
                            <p><?php echo esc_html(get_woocommerce_currency_symbol() . $price); ?></p>

                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Swiper controls -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>

        <script>
            jQuery(document).ready(function($) {
                new Swiper('.helmet-parts-swiper', {
                    loop: true,
                    slidesPerView: 1,
                    spaceBetween: 20,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 1
                        },
                        768: {
                            slidesPerView: 2
                        },
                        1024: {
                            slidesPerView: 4
                        },
                    }
                });
            });
        </script>
    <?php
    }

    return ob_get_clean();
}

