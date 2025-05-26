<?php
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Show cancel Product detail on Order Page on admin Side

if ( ! is_int( $thepostid ) ) {
    $thepostid = $post->ID;
}
if ( ! is_object( $theorder ) ) {
    $theorder = wc_get_order( $thepostid );
}
$order = $theorder;
if ( WC()->version < '3.0.0' ) {
    $order_id = $order->id;
} else {
    $order_id = $order->get_id();
}
$return_data = get_post_meta( $order_id, 'partial_cancel_details', true );

$line_items  = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
// echo "<pre>";

// print_r($line_items); die;
if ( isset( $return_data ) && ! empty( $return_data ) ) { ?>

    <div>
        <div id="ced_rnx_return_wrapper">
            <table>
                <thead>
                    <tr>
                        <th><?php _e( 'Item', 'woocommerce-refund-and-exchange' ); ?></th>
                        <th><?php _e( 'Name', 'woocommerce-refund-and-exchange' ); ?></th>
                        <th><?php _e( 'Cost', 'woocommerce-refund-and-exchange' ); ?></th>
                        <th><?php _e( 'Qty', 'woocommerce-refund-and-exchange' ); ?></th>
                        <th><?php _e( 'Total', 'woocommerce-refund-and-exchange' ); ?></th>
                    </tr>
                </thead>
                <tbody>
            <?php
                $total = 0;

                $return_products = $return_data['partial_cancel_product'];

                foreach ( $line_items as $item_id => $item ) {
                    foreach ( $return_products as $refundkey => $return_product ) {
                      
                        if ( $item_id == $return_product['item_id'] ) {
                           
                           $refund_product_detail = $order->get_meta_data();
                            foreach ( $refund_product_detail as $rpd_value ) {
                                $refund_product_data = $rpd_value->get_data();
                                if ( $refund_product_data['key'] == 'partial_cancel_details' ) {
                                    $refund_product_values = $refund_product_data['value'];
                                    foreach ( $refund_product_values as $rpv_value ) {
                                        $refund_product_values1 = $rpv_value;
                                    
                                        foreach ( $refund_product_values1 as $rpv1_value ) {
                                            $refund_product_id = $rpv1_value['product_id'];
                                            $get_return_product = wc_get_product( $refund_product_id );
                                            
                                            $new_refund_image = wp_get_attachment_image_src( get_post_thumbnail_id( $refund_product_id ), 'single-post-thumbnail' );
                                                $refund_product_new[] = array(
                                                'name'  => $get_return_product->get_name(),
                                                'sku'   => $get_return_product->get_sku(),
                                                'image' => $new_refund_image[0],
                                                'price'  => $get_return_product->get_price(),
                                            );
                                        }
                                    }
                                }
                            }
                            $_product  = $item->get_product();
                            $item_meta = wc_get_order_item_meta( $item_id, $key );
                            $thumbnail     = $_product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $_product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
                            ?>
                            <tr>
                                <td class="thumb">
                                <?php
                                if ( isset( $refund_product_new[ $refundkey ]['image'] ) && ! empty( $refund_product_new[ $refundkey ]['image'] ) ) {
                                    echo '<div class="wc-order-item-thumbnail"><img src ="' . $refund_product_new[ $refundkey ]['image'] . '"></div>';
                                }
                                ?>
                                </td>
                                <td class="name">
                                <?php
                                    echo esc_html( $item['name'] );
                                if ( isset( $refund_product_new[ $refundkey ]['name'] ) && ! empty( $refund_product_new[ $refundkey ]['name'] ) ) {
                                    echo esc_html( $refund_product_new[ $refundkey ]['name'] );
                                }
                                if ( isset( $refund_product_new[ $refundkey ]['sku'] ) && ! empty( $refund_product_new[ $refundkey ]['sku'] ) ) {
                                    echo '<div class="wc-order-item-sku"><strong>' . __( 'SKU:', 'woocommerce-refund-and-exchange' ) . '</strong> ' . esc_html( $refund_product_new[ $refundkey ]['sku'] ) . '</div>';
                                }
                                if ( ! empty( $item['variation_id'] ) ) {
                                    echo '<div class="wc-order-item-variation"><strong>' . __( 'Variation ID:', 'woocommerce-refund-and-exchange' ) . '</strong> ';
                                    if ( ! empty( $item['variation_id'] ) && 'product_variation' === get_post_type( $item['variation_id'] ) ) {
                                        echo esc_html( $item['variation_id'] );
                                    } elseif ( ! empty( $item['variation_id'] ) ) {
                                        echo esc_html( $item['variation_id'] ) . ' (' . __( 'No longer exists', 'woocommerce-refund-and-exchange' ) . ')';
                                    }
                                    echo '</div>';
                                }
                                if ( WC()->version < '3.1.0' ) {
                                    $item_meta      = new WC_Order_Item_Meta( $item, $_product );
                                    $item_meta->display();
                                } else {
                                    $item_meta      = new WC_Order_Item_Product( $item, $_product );
                                    wc_display_item_meta( $item_meta );
                                }
                                echo $return_product['subject'];
                                if($return_product['qty'] == '0'){
                                    $final_qty = $return_product['prev_qty'];
                                }else{
                                    $final_qty = $return_product['qty'];
                                }
                                if($return_product['price'] == '0'){
                                    $price = $return_product['total'];
                                }else{
                                    $price = $return_product['price'];
                                }
                                $total_final = $price + $total_final;
                                ?>
                                </td>
                                <td><?php echo ced_rnx_format_price( $refund_product_new[ $refundkey ]['price'] ); ?></td>
                                <td><?php echo $final_qty; ?></td>
                                <td><?php echo ced_rnx_format_price( $price ); ?></td>
                            </tr>
                            <?php
                            $total += $refund_product_new[ $refundkey ]['price'] * $final_qty;
                        }
                    }
                }
                ?>
                    <tr>
                        <th colspan="4"><?php _e( 'Total Amount', 'woocommerce-refund-and-exchange' ); ?></th>
                        <th><?php echo ced_rnx_format_price( $total ); ?></th>
                    </tr>
                </tbody>
            </table>    
        </div>
    </div>
    <div class="ced_rnx_extra_reason ced_rnx_extra_reason_for_refund">
        <p><?php _e( 'Fees amount will be deducted from Refund amount', 'woocommerce-refund-and-exchange' ); ?></p>
        <p><strong>
        
        <?php _e( 'Refund Amount', 'woocommerce-refund-and-exchange' ); ?> :</strong> <?php echo ced_rnx_format_price( $total ); ?> <input type="hidden" name="ced_rnx_total_amount_for_refund" class="ced_rnx_total_amount_for_refund" value="<?php echo $return_data['amount']; ?>"></p>
    </div>

    <?php }

?>