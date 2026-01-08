<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// שמירת נתונים לסל
add_filter( 'woocommerce_add_cart_item_data', function( $cart_item_data, $product_id ) {
    if ( ! empty( $_POST['jmp_use_back'] ) ) {
        $cart_item_data['jmp_fee'] = $_POST['jmp_back_fee'];
        $cart_item_data['jmp_back_text'] = $_POST['jmp_back_text'];
    }
    if ( ! empty( $_POST['jmp_front_text'] ) ) {
        $cart_item_data['jmp_front_text'] = $_POST['jmp_front_text'];
    }
    return $cart_item_data;
}, 10, 2 );

// עדכון המחיר בעגלה
add_action( 'woocommerce_before_calculate_totals', function( $cart ) {
    foreach ( $cart->get_cart() as $item ) {
        if ( isset( $item['jmp_fee'] ) ) {
            $item['data']->set_price( $item['data']->get_price() + floatval($item['jmp_fee']) );
        }
    }
});
