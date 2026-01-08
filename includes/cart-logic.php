<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// 1. שמירת המידע לסל הקניות
add_filter( 'woocommerce_add_cart_item_data', function( $cart_item_data, $product_id ) {
    if ( isset( $_POST['jmp_val'] ) ) {
        $cart_item_data['custom_jewelry_options'] = $_POST['jmp_val'];
    }
    return $cart_item_data;
}, 10, 2 );

// 2. הצגת המידע בתוך עמוד ההזמנה של מנהל האתר
add_action( 'woocommerce_checkout_create_order_line_item', function( $item, $cart_item_key, $values, $order ) {
    if ( isset( $values['custom_jewelry_options'] ) ) {
        foreach ( $values['custom_jewelry_options'] as $label => $value ) {
            if(!empty($value) && $value !== 'לא') {
                $item->add_meta_data( $label, $value );
            }
        }
    }
}, 10, 4 );

// 3. עדכון המחיר הסופי בעגלה לפי בחירות הלקוח
add_action( 'woocommerce_before_calculate_totals', function( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
    foreach ( $cart->get_cart() as $item ) {
        // כאן ניתן להוסיף לוגיקה לחישוב מחיר נוסף אם צריך
    }
});
