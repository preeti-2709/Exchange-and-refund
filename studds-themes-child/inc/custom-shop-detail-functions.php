<?php

/**
* Remove Original Product Description
*/

if ( function_exists( 'shiprocket_show_check_pincode' ) ) {
    remove_action( 'woocommerce_single_product_summary', 'shiprocket_show_check_pincode', 20 );
    add_action( 'woocommerce_after_add_to_cart_form', 'shiprocket_show_check_pincode', 31 );
}

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );


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



add_action('wp_footer', 'add_color_swatch_toggle_script_and_style');
function add_color_swatch_toggle_script_and_style() {
    if (!is_product()) {
        return; // Only load this on single product pages
    }
    ?>
    <style>
        .swatch-toggle-btn {
            margin-top: 10px;
            padding: 6px 12px;
            background-color: #0073aa;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            opacity: 0.99;

        }
        .swatch-toggle-btn:hover {
            background-color: #005177;
        }
    </style>
    <script>
    jQuery(document).ready(function($) {
        var $parent = $('.vi-wpvs-variation-wrap[data-attribute="attribute_pa_color"]');
        console.log($parent);
        if ($parent.length === 0) {
            console.warn('No color swatch variation found');
            return;
        }
        var $children = $parent.find('.vi-wpvs-option-wrap');
        var visibleCount = 5;
        var expanded = false;
        $children.hide().slice(0, visibleCount).show();
        if ($children.length > visibleCount) {
            var $button = $('<button id="toggle-button" class="swatch-toggle-btn">View More</button>');
            $parent.after($button);

            $button.on('click', function() {
                if (!expanded) {
                    $children.show();
                    $button.text('View Less');
                } else {
                    $children.slice(visibleCount).hide();
                    $button.text('View More');
                }
                expanded = !expanded;
            });
        }
    });
    </script>
    <?php
}
