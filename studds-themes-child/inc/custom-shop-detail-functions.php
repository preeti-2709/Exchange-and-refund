<?php

/**
* Remove Original Product Description
*/

if ( function_exists( 'shiprocket_show_check_pincode' ) ) {
    remove_action( 'woocommerce_single_product_summary', 'shiprocket_show_check_pincode', 20 );
    add_action( 'woocommerce_after_add_to_cart_form', 'shiprocket_show_check_pincode', 31 );
}

add_action('woocommerce_before_single_product', 'inject_variation_image_map_for_color');
function inject_variation_image_map_for_color() {
    global $product;
    if (!$product || !$product->is_type('variable')) {
        return;
    }
    $variations = $product->get_available_variations();
    $color_image_map = [];
    foreach ($variations as $variation) {
        $attributes = $variation['attributes'];
        $image_url = $variation['image']['url'];

        // Skip if color or image missing
        if (!isset($attributes['attribute_pa_color']) || !$image_url) {
            continue;
        }

        $color = $attributes['attribute_pa_color'];

        // Only map first image per color
        if (!isset($color_image_map[$color])) {
            $color_image_map[$color] = $image_url;
        }
    }
    echo '<script type="text/javascript">';
    echo 'window.colorSwatchImages = ' . json_encode($color_image_map) . ';';
    echo '</script>';
}

add_action('wp_footer', 'replace_color_swatch_with_images_script');
function replace_color_swatch_with_images_script() { ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (typeof window.colorSwatchImages === 'undefined') return;
            const swatches = document.querySelectorAll('.vi-wpvs-option-wrap');
            swatches.forEach((swatch) => {
                const color = swatch.getAttribute('data-attribute_value');
                const imageUrl = window.colorSwatchImages[color];
                if (imageUrl) {
                    const img = document.createElement('img');
                    img.src = imageUrl;
                    img.alt = color;
                    img.style.width = '44px';
                    img.style.height = '44px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '0%';

                    // If image is a PNG, apply background
                    if (imageUrl.endsWith('.png')) {
                        img.style.backgroundColor = '#fff'; // or use a hex code like '#f5f5f5'
                    }

                    const colorSpan = swatch.querySelector('.vi-wpvs-option');
                    if (colorSpan) {
                        colorSpan.innerHTML = '';
                        colorSpan.appendChild(img);
                    }
                }
            });
        });
    </script>

<?php }

