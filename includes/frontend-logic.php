<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'woocommerce_before_add_to_cart_button', 'jmp_render_dynamic_fields' );
function jmp_render_dynamic_fields() {
    global $product;
    $rules = get_posts(array('post_type' => 'product_option_rule', 'posts_per_page' => -1));
    
    foreach ($rules as $rule) {
        $ids = explode(',', get_post_meta($rule->ID, '_jmp_ids', true));
        if (in_array($product->get_id(), $ids)) {
            $fields = get_post_meta($rule->ID, '_jmp_fields_config', true) ?: [];
            echo '<div class="jmp-pro-container" style="direction:rtl; border:2px solid #d4af37; padding:15px; border-radius:10px; margin-bottom:20px; background:#fff;">';
            
            foreach ($fields as $index => $f) {
                $style = (isset($f['logic']) && $index > 0) ? 'display:none;' : '';
                $class = isset($f['logic']) ? 'jmp-logic-trigger' : '';
                
                echo "<div class='jmp-field-item' id='field-{$index}' style='{$style} margin-bottom:15px;'>";
                echo "<label style='display:block; margin-bottom:5px;'><b>{$f['label']}</b>" . ($f['price'] > 0 ? " (+{$f['price']}₪)" : "") . "</label>";
                
                if ($f['type'] == 'text') {
                    echo "<input type='text' name='jmp_val[{$f['label']}]' style='width:100%; padding:8px;' id='input-{$index}'>";
                    if (isset($f['emoji'])) {
                        echo "<div style='margin-top:5px; font-size:20px;' onclick='document.getElementById(\"input-{$index}\").value += event.target.innerText'> ❤️ ⭐ 🧿 👑 🌙 </div>";
                    }
                } elseif ($f['type'] == 'dropdown') {
                    echo "<select name='jmp_val[{$f['label']}]' class='{$class}' data-target='field-".($index+1)."' style='width:100%; padding:8px;'>";
                    echo "<option value='לא'>לא</option><option value='כן'>כן</option></select>";
                } elseif ($f['type'] == 'image') {
                    echo "<input type='file' name='jmp_file_{$index}' accept='image/*'>";
                }
                echo "</div>";
            }
            echo '</div>';
            ?>
            <script>
                document.querySelectorAll('.jmp-logic-trigger').forEach(select => {
                    select.addEventListener('change', function() {
                        const target = document.getElementById(this.dataset.target);
                        if (target) target.style.display = this.value === 'כן' ? 'block' : 'none';
                    });
                });
            </script>
            <?php
        }
    }
}
