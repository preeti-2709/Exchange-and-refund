<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class CED_rnx_cancel_return {
    public function __construct() {

            add_action( 'woocommerce_order_details_after_order_table', array( $this, 'ced_rnx_order_cancel_button' ) );
        }
       
        function ced_rnx_order_cancel_button( $order ) {
            if ( WC()->version < '3.0.0' ) {
                $order_id = $order->id;
            } else {
                $order_id = $order->get_id();
            }
            $order_status = 'wc-' . $order->get_status();
            $product_datas = get_post_meta( $order_id, 'partial_cancel_details', true );
            
            if ( isset( $product_datas ) && ! empty( $product_datas ) ) {
                    ?>
                    <h2><?php _e( 'Cancel Product History', 'woocommerce-refund-and-exchange' ); ?></h2>                               
                        <table class="shop_table order_details">
                            <thead>
                                <tr>
                                    <th class="product-name"><?php _e( 'Product', 'woocommerce-refund-and-exchange' ); ?></th>
                                    <th class="product-total"><?php _e( 'Total', 'woocommerce-refund-and-exchange' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                               $return_products = $product_datas['partial_cancel_product'];
                            foreach ( $order->get_items() as $item_id => $item ) {
                                foreach ( $return_products as $return_product ) {
                                    if ( isset( $return_product['item_id'] ) ) {
                                        if ( $return_product['item_id'] == $item_id ) {

                                            if($return_product['qty'] == '0'){
                                                $final_qty = $return_product['prev_qty'];
                                            }else{
                                                $final_qty = $return_product['qty'];
                                            }
                                            ?>
                                        <tr>
                                            <td class="woocommerce-table__product-name product-name">
                                            <?php
                                            $product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
                                            $is_visible        = $product && $product->is_visible();
                                            $product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

                                            echo $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $item['name'] ) : $item['name'];
                                            echo '<strong class="product-quantity">' . sprintf( '&times; %s', $final_qty ) . '</strong>';

                                            do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );

                                            if ( WC()->version < '3.0.0' ) {
                                                $order->display_item_meta( $item );
                                                $order->display_item_downloads( $item );
                                            } else {
                                                wc_display_item_meta( $item );
                                                wc_display_item_downloads( $item );
                                            }

                                            do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );
                                            ?>
                                            <spna style="color:black;"><?php echo $return_product['subject'];?></spna>
                                            </td>
                                            <td class="product-total"><strong>
                                            <?php
                                            if($return_product['price'] == '0'){
                                                $price = $return_product['total'];
                                            }else{
                                                $price = $return_product['price'];
                                            }
                                            $total_final = $price + $total_final;
                                            echo ced_rnx_format_price( $price );
                                            ?>
                                            </strong></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                            }
                            ?>
                            <tr>
                                <th scope="row"><?php _e( 'Return Amount', 'woocommerce-refund-and-exchange' ); ?></th>
                                <th><strong><?php echo ced_rnx_format_price( $total_final ); ?></strong></th>
                            </tr>
                            </tbody>
                        </table>

            <?php 
                
            }
            
           
        }
}
new CED_rnx_cancel_return();
?>