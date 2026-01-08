<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'woocommerce_before_add_to_cart_button', 'jmp_render_advanced_frontend' );

function jmp_render_advanced_frontend() {
    global $product;
    // כאן נכנס הקוד שסורק את החוקים ומציג את השדות עם ה-JavaScript להסתרה והצגה
    // (הקוד המלא שנתתי לך בהודעה הקודמת)
}
