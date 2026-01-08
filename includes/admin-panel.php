<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// רישום סוג הפוסט של החוקים
add_action( 'init', 'jmp_register_cpt_pro' );
function jmp_register_cpt_pro() {
    register_post_type( 'product_option_rule', array(
        'labels' => array('name' => 'אפשרויות מוצר', 'singular_name' => 'חוק'),
        'public' => false, 'show_ui' => true, 'menu_icon' => 'dashicons-admin-generic',
        'supports' => array( 'title' ),
    ));
}

// יצירת ממשק בונה השדות (Builder)
add_action( 'add_meta_boxes', function() {
    add_meta_box( 'jmp_pro_builder', 'בונה אפשרויות מתקדם', 'jmp_render_pro_builder', 'product_option_rule' );
});

function jmp_render_pro_builder( $post ) {
    $applied_ids = get_post_meta( $post->ID, '_jmp_ids', true );
    $fields = get_post_meta( $post->ID, '_jmp_fields_config', true ) ?: [];
    ?>
    <div style="direction:rtl; text-align:right; padding:10px; background:#fefefe; border:1px solid #ddd;">
        <label><strong>מזהי מוצרים (מופרדים בפסיק):</strong></label>
        <input type="text" name="jmp_ids" value="<?php echo esc_attr($applied_ids); ?>" style="width:100%; margin-bottom:20px; padding:8px;">

        <div id="jmp-fields-wrapper">
            <?php foreach ( $fields as $i => $f ) : ?>
                <div class="field-row" style="border:1px solid #ccc; padding:15px; margin-bottom:10px; background:#fff; border-radius:5px;">
                    <strong>שדה #<?php echo $i+1; ?></strong>
                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:10px; margin-top:10px;">
                        <select name="fields[<?php echo $i; ?>][type]">
                            <option value="text" <?php selected($f['type'], 'text'); ?>>שדה טקסט</option>
                            <option value="image" <?php selected($f['type'], 'image'); ?>>העלאת תמונה</option>
                            <option value="dropdown" <?php selected($f['type'], 'dropdown'); ?>>תפריט בחירה (לוגיקה)</option>
                        </select>
                        <input type="text" name="fields[<?php echo $i; ?>][label]" value="<?php echo esc_attr($f['label']); ?>" placeholder="כותרת השדה">
                        <input type="number" name="fields[<?php echo $i; ?>][price]" value="<?php echo esc_attr($f['price']); ?>" placeholder="מחיר">
                    </div>
                    <div style="margin-top:12px; padding:10px; background:#f9f9f9;">
                        <label><input type="checkbox" name="fields[<?php echo $i; ?>][emoji]" <?php checked(isset($f['emoji'])); ?>> אפשר בחירת אימוג'ים (V)</label> | 
                        <label><input type="checkbox" name="fields[<?php echo $i; ?>][logic]" <?php checked(isset($f['logic'])); ?>> הפעל לוגיקה (יפתח את השדה הבא רק בבחירת "כן")</label>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" style="margin-top:10px; color:red; cursor:pointer; border:none; background:none; text-decoration:underline;">מחק שדה</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add_field" class="button button-primary" style="margin-top:10px;">הוסף שדה חדש +</button>
    </div>

    <script>
        document.getElementById('add_field').addEventListener('click', function() {
            const wrap = document.getElementById('jmp-fields-wrapper');
            const i = wrap.children.length;
            const html = `<div class="field-row" style="border:1px solid #ccc; padding:15px; margin-bottom:10px; background:#fff; border-radius:5px;">
                <strong>שדה חדש</strong>
                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:10px; margin-top:10px;">
                    <select name="fields[${i}][type]"><option value="text">טקסט</option><option value="image">תמונה</option><option value="dropdown">תפריט בחירה</option></select>
                    <input type="text" name="fields[${i}][label]" placeholder="כותרת">
                    <input type="number" name="fields[${i}][price]" placeholder="מחיר">
                </div>
                <div style="margin-top:12px;"><label><input type="checkbox" name="fields[${i}][emoji]"> אימוג'ים</label> | <label><input type="checkbox" name="fields[${i}][logic]"> לוגיקה מותנית</label></div>
                <button type="button" onclick="this.parentElement.remove()" style="margin-top:10px; color:red;">מחק</button>
            </div>`;
            wrap.insertAdjacentHTML('beforeend', html);
        });
    </script>
    <?php
}

add_action( 'save_post', function( $post_id ) {
    if ( isset( $_POST['jmp_ids'] ) ) update_post_meta( $post_id, '_jmp_ids', sanitize_text_field($_POST['jmp_ids']) );
    if ( isset( $_POST['fields'] ) ) update_post_meta( $post_id, '_jmp_fields_config', $_POST['fields'] );
});
