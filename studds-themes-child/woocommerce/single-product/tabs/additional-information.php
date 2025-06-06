<?php
defined('ABSPATH') || exit;

global $product;

// Get the current product (could be variation or main product)
$current_product = $product;
$main_product = $product;

// If this is a variation, get the parent product for ACF data
if ($product->is_type('variation')) {
    $main_product = wc_get_product($product->get_parent_id());
}

// Get attributes from current product (variation-specific if applicable)
$attributes = $current_product->get_attributes();
$weight = $current_product->get_weight();
$sku = $current_product->get_sku();
$product_attributes_filtered = apply_filters('woocommerce_display_product_attributes', array(), $current_product);

// Get ACF image from main product (consistent across variations)
$acf_image = get_field('additional_info_image', $main_product->get_id());
$acf_image_html = $acf_image ? '<img src="' . esc_url($acf_image['url']) . '" alt="' . esc_attr($acf_image['alt']) . '" style="max-width: 100%; height: auto;" />' : '';

// For variable products, we need to handle attributes differently
if ($main_product->is_type('variable') && !$product->is_type('variation')) {
    $display_attributes = $main_product->get_attributes();
    $display_weight = $main_product->get_weight();
    $display_sku = $main_product->get_sku();
    $product_attributes_filtered = apply_filters('woocommerce_display_product_attributes', array(), $main_product);

} else {
    // This is either a simple product or a specific variation
    $display_attributes = $current_product->get_attributes();
    $display_weight = $current_product->get_weight();
    $display_sku = $current_product->get_sku();
    $product_attributes_filtered = apply_filters('woocommerce_display_product_attributes', array(), $current_product);

    // For variations, also get variation attributes
    if ($current_product->is_type('variation')) {
        $variation_attributes = $current_product->get_variation_attributes();
        $parent_attributes = $main_product->get_attributes();

        // Merge parent attributes with variation-specific values
        $merged_attributes = array();
        foreach ($parent_attributes as $attr_key => $attr_obj) {
            $variation_key = str_replace('pa_', '', $attr_key);
            if (isset($variation_attributes['attribute_' . $attr_key]) && !empty($variation_attributes['attribute_' . $attr_key])) {
                // Use variation-specific value
                $merged_attributes[$attr_key] = $attr_obj;
            } elseif (!isset($variation_attributes['attribute_' . $attr_key])) {
                // Include non-variable attributes from parent
                $merged_attributes[$attr_key] = $attr_obj;
            }
        }
        $display_attributes = array_merge($merged_attributes, $display_attributes);
    }
}

// Gather all attribute keys
$attribute_keys = array_keys($display_attributes);
$total_attributes = count($attribute_keys);
$half = ceil($total_attributes / 2);
$left_keys = array_slice($attribute_keys, 0, $half);
$right_keys = array_slice($attribute_keys, $half);
?>

<div class="custom-additional-info" style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 30px; padding: 30px 0;">

    <!-- LEFT COLUMN -->
    <div class="left-column" style="flex:1; min-width: 280px;">
        <ul style="list-style: none; padding: 0;">
            <?php if (!isset($display_attributes['pa_weight']) && $display_weight) : ?>
                <li data-attribute="weight"><strong><?php esc_html_e('Weight(In gram)', 'woocommerce'); ?>:</strong> <span class="attr-value"><?php echo esc_html($display_weight . ' ± 50 ' . get_option('woocommerce_weight_unit')); ?></span></li>
            <?php endif; ?>

            <?php if (!isset($display_attributes['pa_dimensions']) && $current_product->has_dimensions()) : ?>
                <li data-attribute="dimensions"><strong><?php esc_html_e('Dimensions(In centimeter)', 'woocommerce'); ?>:</strong>
                    <span class="attr-value"><?php echo esc_html($current_product->get_length() . ' × ' . $current_product->get_width() . ' × ' . $current_product->get_height() . ' ' . get_option('woocommerce_dimension_unit')); ?></span>
                </li>
            <?php endif; ?>

            <?php foreach ($left_keys as $key) :
                if (isset($display_attributes[$key])) :
                    $attr = $display_attributes[$key];
                    $label = wc_attribute_label($attr->get_name());

                    // Handle variation-specific attribute values
                    if ($current_product->is_type('variation')) {
                        $variation_attributes = $current_product->get_variation_attributes();
                        $attr_key = 'attribute_' . $key;
                        if (isset($variation_attributes[$attr_key]) && !empty($variation_attributes[$attr_key])) {
                            // Get the term name for the variation value
                            $term = get_term_by('slug', $variation_attributes[$attr_key], $key);
                            $values = $term ? array($term->name) : array($variation_attributes[$attr_key]);
                        } else {
                            $values = wc_get_product_terms($main_product->get_id(), $attr->get_name(), ['fields' => 'names']);
                        }
                    } else {
                        $values = wc_get_product_terms($current_product->get_id(), $attr->get_name(), ['fields' => 'names']);
                    }

                    if (!empty($values)) : ?>
                        <li data-attribute="<?php echo esc_attr($key); ?>"><strong><?php echo esc_html($label); ?>:</strong> <span class="attr-value"><?php echo esc_html(implode(', ', $values)); ?></span></li>
                    <?php endif;
                endif;
            endforeach; ?>

            <?php
            $ean_code = '';
            foreach ($product_attributes_filtered as $key => $attr) {
                if (strpos($key, 'ean-field') !== false && !empty($attr['value'])) {
                    $ean_code = $attr['value'];
                    break;
                }
            }
            ?>

            <?php if ($ean_code) : ?>
                <li data-attribute="ean"><strong><?php esc_html_e('EAN Code', 'woocommerce'); ?>:</strong> <span class="attr-value"><?php echo esc_html($ean_code); ?></span></li>
            <?php endif; ?>

        </ul>
    </div>

    <!-- CENTER COLUMN (ACF IMAGE) -->
    <div class="center-column" style="flex:1; min-width: 280px; text-align: center;">
        <?php echo $acf_image_html; ?>
    </div>

    <!-- RIGHT COLUMN -->
    <div class="right-column" style="flex:1; min-width: 280px;">
        <ul style="list-style: none; padding: 0;">
            <?php foreach ($right_keys as $key) :
                if (isset($display_attributes[$key])) :
                    $attr = $display_attributes[$key];
                    $label = wc_attribute_label($attr->get_name());

                    // Handle variation-specific attribute values
                    if ($current_product->is_type('variation')) {
                        $variation_attributes = $current_product->get_variation_attributes();
                        $attr_key = 'attribute_' . $key;
                        if (isset($variation_attributes[$attr_key]) && !empty($variation_attributes[$attr_key])) {
                            // Get the term name for the variation value
                            $term = get_term_by('slug', $variation_attributes[$attr_key], $key);
                            $values = $term ? array($term->name) : array($variation_attributes[$attr_key]);
                        } else {
                            $values = wc_get_product_terms($main_product->get_id(), $attr->get_name(), ['fields' => 'names']);
                        }
                    } else {
                        $values = wc_get_product_terms($current_product->get_id(), $attr->get_name(), ['fields' => 'names']);
                    }

                    if (!empty($values)) : ?>
                        <li data-attribute="<?php echo esc_attr($key); ?>"><strong><?php echo esc_html($label); ?>:</strong> <span class="attr-value"><?php echo esc_html(implode(', ', $values)); ?></span></li>
                    <?php endif;
                endif;
            endforeach; ?>

            <?php if ($display_sku) : ?>
                <li data-attribute="sku"><strong><?php esc_html_e('Product Code', 'woocommerce'); ?>:</strong> <span class="attr-value"><?php echo esc_html($display_sku); ?></span></li>
            <?php endif; ?>

            <li><strong><?php esc_html_e('Net Quantity', 'woocommerce'); ?>:</strong> 1</li>

            <?php
            $store_address = get_option('woocommerce_store_address');
            if ($store_address) : ?>
                <li><strong><?php esc_html_e('Manufactured and Marketed By', 'woocommerce'); ?>:</strong> <?php echo esc_html($store_address); ?></li>
            <?php endif; ?>
        </ul>
    </div>

</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Store original content for reset
    const originalLeftColumn = $('.custom-additional-info .left-column').html();
    const originalRightColumn = $('.custom-additional-info .right-column').html();

    // Handle variation changes for variable products
    $('form.variations_form').on('found_variation', function(event, variation) {
        // Update weight
        if (variation.weight) {
            $('[data-attribute="weight"] .attr-value').html(variation.weight + ' ± 50 ' + ' <?php echo get_option('woocommerce_weight_unit'); ?>');
        }

        // Update SKU/Product Code
        if (variation.sku) {
            $('[data-attribute="sku"] .attr-value').html(variation.sku);
        }

        if (variation.base_color) {
            var originalText = variation.base_color;
            var capitalizedText = originalText.charAt(0).toUpperCase() + originalText.slice(1);
            $('[data-attribute="pa_base-color"] .attr-value').html(capitalizedText);
        }

        // Update EAN Code if provided
        if (variation.custom_field) {
            var eanCode = $(variation.custom_field).find('span').text().trim();
            $('[data-attribute="ean"] .attr-value').html(eanCode);
        }
        // Update dimensions
        if (variation.dimensions && variation.dimensions.length && variation.dimensions.width && variation.dimensions.height) {
            $('[data-attribute="dimensions"] .attr-value').html(
                variation.dimensions.length + ' × ' + variation.dimensions.width + ' × ' + variation.dimensions.height + ' <?php echo get_option('woocommerce_dimension_unit'); ?>'
            );
        }
        // Update all other attributes
        if (variation.attributes) {
            $.each(variation.attributes, function(attrName, attrValue) {
                // Remove 'attribute_' prefix to match our data-attribute values
                var cleanAttrName = attrName.replace('attribute_', '');
                var $attrElement = $('[data-attribute="' + cleanAttrName + '"] .attr-value');
               if ($attrElement.length && attrValue) {
                    var displayValue = attrValue;
                    var $selectOption = $('select[name="' + attrName + '"] option:selected');
                    if ($selectOption.length) {
                        displayValue = $selectOption.text();
                    }
                    $attrElement.text(displayValue.trim());
                }


            });
        }
    });

    // Handle variation reset
    $('form.variations_form').on('reset_data', function(event) {
        // Restore the original HTML
        $('.custom-additional-info .left-column').html(originalLeftColumn);
        $('.custom-additional-info .right-column').html(originalRightColumn);
    });

    // Additional handler for when variation selection changes
    $('.variations select').on('change', function() {
        var $form = $(this).closest('form.variations_form');
        var allSelected = true;
        var selectedAttributes = {};

        // Check if all required variations are selected
        $form.find('.variations select').each(function() {
            var val = $(this).val();
            if (!val || val === '') {
                allSelected = false;
            } else {
                var attrName = $(this).attr('name');
                selectedAttributes[attrName] = val;
            }
        });

        // Update attributes immediately if all are selected
        // if (allSelected) {
        //     $.each(selectedAttributes, function(attrName, attrValue) {
        //         var cleanAttrName = attrName.replace('attribute_', '');
        //         var $attrElement = $('[data-attribute="' + cleanAttrName + '"] .attr-value');

        //         if ($attrElement.length && attrValue) {
        //             var displayValue = attrValue;
        //             var $selectOption = $('select[name="' + attrName + '"] option[value="' + attrValue + '"]');
        //             if ($selectOption.length) {
        //                 displayValue = $selectOption.text();
        //             }
        //             $attrElement.html(displayValue);
        //         }
        //     });
        // }
    });
});
</script>

<?php
// Add this to your functions.php to pass additional variation data to JavaScript
add_filter('woocommerce_available_variation', 'add_custom_variation_data', 10, 3);
function add_custom_variation_data($variation_data, $product, $variation) {
    $attributes = $variation->get_variation_attributes();
    // Add readable attribute names and values
    $variation_data['variation_attributes_readable'] = array();

    foreach ($attributes as $attr_key => $attr_value) {
        $taxonomy = str_replace('attribute_', '', $attr_key);

        // Get the term object to get the name
        if (taxonomy_exists($taxonomy)) {
            $term = get_term_by('slug', $attr_value, $taxonomy);
            if ($term) {
                $clean_key = str_replace('pa_', '', $taxonomy);
                $variation_data['variation_attributes_readable'][$clean_key] = $term->name;
            }
        } else {
            // For custom attributes (not taxonomies)
            $clean_key = str_replace('pa_', '', $taxonomy);
            $variation_data['variation_attributes_readable'][$clean_key] = $attr_value;
        }
    }

    return $variation_data;
}
?>