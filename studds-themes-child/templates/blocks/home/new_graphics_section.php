<?php
/*
** New Graphics Section
*/
?>
<?php
// $page_id = get_the_ID();
$section_title = get_sub_field('section_title');
$show_graphics = get_sub_field('show_graphic_product');
$selected_products = get_sub_field('new_graphics_product');
$button_text = get_sub_field('button_text');

function get_product_variations_for_display($product_id) {

    $product = wc_get_product($product_id);

    if (!$product || !$product->is_type('variable')) {
        return [];
    }

    $variations = $product->get_available_variations();
    $response = [];
    $added_colors = [];

    foreach ($variations as $variation) {
        $color_slug = $variation['attributes']['attribute_pa_color'] ?? '';
        if ($color_slug && !in_array($color_slug, $added_colors)) {
            $term = get_term_by('slug', $color_slug, 'pa_color');

            $vi_params = get_term_meta($term->term_id, 'vi_wpvs_terms_params', true);
            $color_value = '';

            if ($vi_params) {
                $parsed = maybe_unserialize($vi_params);
                if (is_array($parsed) && isset($parsed['color'][0])) {
                    $color_value = $parsed['color'][0];
                }
            }

            $image_url = wp_get_attachment_url($variation['image_id']);
            $term_colors          =  $vi_params['color'];
            $term_color_separator = "1";
            $instance = VIWPVS_WOOCOMMERCE_PRODUCT_VARIATIONS_SWATCHES_Frontend_Frontend::get_attribute_option_color($term->term_id, $term_colors, $term_color_separator);

            $response[] = [
                'color' => $color_value,
                'image_url' => $image_url,
                'color_slug' => $color_slug,
                'term' => $term, 
                'instance' => $instance, 
            ];


            $added_colors[] = $color_slug;
        }
    }

    return $response;
}

$args = array(
    'post_type' => 'product',
    'post_status' => 'publish',
);

if ($show_graphics === 'selected_one' && !empty($selected_products)) {
    $args['post__in'] = array_map(function ($post) {
        return is_object($post) ? $post->ID : $post;
    }, $selected_products);
    $args['orderby'] = 'post__in';
    $args['posts_per_page'] = -1;
} else {
    $args['posts_per_page'] = 10;
}

$loop = new WP_Query($args);

?>
<section class="new-graphics-section">
    <div class="container">
        <div class="product-swiper-section">
            <div class="new_launches_title  section_subtitle">
                <h2><?php echo $section_title; ?></h2>
            </div>
           <!-- <div class="main_product_bg_text">
                <h4 id="main_product_bg_title"><?php //echo $bg_text; ?></h4>
            </div> -->
            
            <div class="swiper myProductSwiper">
                <div class="swiper-wrapper">
                    <?php
                    if ($loop->have_posts()) :
                        $slider_id = 1;
                        while ($loop->have_posts()) : $loop->the_post();
                            global $product;
                            $product_id = get_the_ID();
                            // $bg_text = get_field('new_launches_background_text', $product_id);

                            /*
                            ** Sub categories must be selected to show this - sub categories/model 
                            */ 
                            $model_text = '';
                            $models = get_the_terms($product_id, 'pa_model');

                            if (!empty($models) && !is_wp_error($models)) {
                                $model_text_whole = esc_html($models[0]->name);
                                $model_text = strtok($model_text_whole, ' ');
                            }

                            $variation_data = get_product_variations_for_display($product_id);
                            $variation_count = count($variation_data);
                            $show_arrows = $variation_count > 3;

                            $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
                    ?>
                            
                            <div class="swiper-slide graphics-section-single-product product-data-graphics" data-bg-text="<?php echo $bg_text; ?>" data-product-id="<?php echo get_the_ID(); ?>">
                                <div class="main_product_bg_text">
                                    <h4 id="main_product_bg_title"><?php echo $model_text; ?></h4>
                                </div>
                                <div class="product-box">
                                    <div class="main-image-swiper swiper">
                                        <div class="swiper-wrapper">
                                            <?php if (!empty($variation_data)) :
                                                foreach ($variation_data as $variation) : ?>
                                                    <div class="swiper-slide">
                                                        <img class="product-img" src="<?php echo esc_url($variation['image_url']); ?>" alt="Variation Image">
                                                    </div>
                                                <?php endforeach;
                                            else : ?>
                                                <div class="swiper-slide">
                                                    <img class="product-img" src="<?php echo esc_url($image[0]); ?>" alt="<?php the_title(); ?>">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>


                                    <!-- Color-type name swiper (populated via AJAX) -->

                                    <div class="color_watches_dls">
                                        <div class="graphics-color-swatches-slider">
                                            <?php if (!empty($variation_data) && $show_arrows) : ?>
                                                <div class="swiper-button-prev"></div>
                                            <?php endif; ?>
                                            <div class="color-swatches-swiper swiper color-swatches-swiper-<?= $slider_id; ?>">
                                                    <div class="swiper-wrapper color-swatches">                                                
                                                        <?php if (!empty($variation_data)) :
                                                            foreach ($variation_data as $index => $variation) : ?>
                                                                <div class="swiper-slide swatch-slide" data-image-url="<?php echo esc_url($variation['image_url']); ?>">
                                                                    <div class="swatch-circle color-<?php echo esc_attr($variation['color_slug']); ?>"
                                                                        style="background: <?php echo $variation['instance']; ?>;">
                                                                    </div>
                                                                </div>
                                                            <?php endforeach;
                                                        endif; ?>                                                
                                                    </div>
                                                
                                                <div class="swiper-pagination"></div>

                                            </div>
                                            <?php if (!empty($variation_data) && $show_arrows) : ?>
                                                <div class="swiper-button-next"></div>
                                            <?php endif; ?>
                                        </div>
                                        <h4 class="product-title">
                                            <a href="<?php echo get_the_permalink();  ?>" data-productName="<?php echo $bg_text; ?>">
                                                <?php echo get_the_title(); ?>
                                            </a>
                                        </h4>
                                        <a href="<?php echo get_permalink($product_id); ?>" class="discover-button"><?php echo $button_text; ?></a>    
                                    </div>

                                    <!-- Main image navigation buttons -->


                                </div>
                            </div>

                    <?php 
                        $slider_id++;    
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>

                <!-- Swiper Nav -->
                <div class="swiper-button-prev main-slider-prev"></div>
                <div class="swiper-button-next main-slider-next"></div>

            </div>
        </div>
    </div>
</section>
<!-- Swiper -->