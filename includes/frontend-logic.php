<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'woocommerce_before_add_to_cart_button', 'jmp_render_fields' );
function jmp_render_fields() {
    global $product;
    $current_id = $product->get_id();

    // חיפוש חוקים רלוונטיים
    $rules = get_posts(array('post_type' => 'product_option_rule', 'posts_per_page' => -1));

    foreach ($rules as $rule) {
        $ids = explode(',', get_post_meta($rule->ID, '_jmp_applied_ids', true));
        if (in_array($current_id, $ids)) {
            $price = get_post_meta($rule->ID, '_jmp_backside_price', true);
            ?>
            <div id="jmp-customizer" style="background:#f9f9f9; padding:15px; border:1px solid #ddd; margin-bottom:20px; direction:rtl;">
                <p><label>טקסט חריטה קדמי:</label><br><input type="text" name="jmp_front_text" style="width:100%"></p>
                <p><label><input type="checkbox" id="jmp_toggle_back" name="jmp_use_back" value="yes"> חריטה צד שני (+<?php echo $price; ?>₪)</label></p>
                <div id="jmp_back_field" style="display:none;">
                    <label>טקסט חריטה צד אחורי:</label><br><input type="text" name="jmp_back_text" style="width:100%">
                    <input type="hidden" name="jmp_back_fee" value="<?php echo $price; ?>">
                </div>
            </div>
            <script>
                document.getElementById('jmp_toggle_back').addEventListener('change', function() {
                    document.getElementById('jmp_back_field').style.display = this.checked ? 'block' : 'none';
                });
            </script>
            <?php
            break;
        }
    }
}
