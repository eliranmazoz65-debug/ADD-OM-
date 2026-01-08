<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', 'jmp_register_cpt_pro' );
function jmp_register_cpt_pro() {
    register_post_type( 'product_option_rule', array(
        'labels' => array('name' => 'אפשרויות מוצר', 'singular_name' => 'חוק'),
        'public' => false, 'show_ui' => true, 'menu_icon' => 'dashicons-admin-generic',
        'supports' => array( 'title' ),
    ));
}

add_action( 'add_meta_boxes', function() {
    add_meta_box( 'jmp_pro_builder', 'הגדרת שדות וחוקים מותנים', 'jmp_render_advanced_builder', 'product_option_rule' );
});

function jmp_render_advanced_builder( $post ) {
    $applied_ids = get_post_meta( $post->ID, '_jmp_ids', true ) ?: [];
    $fields = get_post_meta( $post->ID, '_jmp_fields_config', true ) ?: [];
    $conditions = get_post_meta( $post->ID, '_jmp_conditions', true ) ?: [];
    ?>
    <div style="direction:rtl; text-align:right;">
        <div style="margin-bottom:20px; background:#fff; padding:15px; border:1px solid #ccd0d4;">
            <label><strong>1. בחר מוצרים (לפי שם):</strong></label><br>
            <input type="text" name="jmp_ids_raw" value="<?php echo implode(',', $applied_ids); ?>" placeholder="הכנס IDs מופרדים בפסיק (שיפור לחיפוש שמי יתווסף בהמשך)">
        </div>

        <div id="fields-list" style="margin-bottom:20px;">
            <label><strong>2. הגדרת שדות (Fields):</strong></label><br>
            <?php foreach ($fields as $i => $f) : ?>
                <div style="background:#fff; border:1px solid #ccd0d4; padding:10px; margin-top:5px;">
                    <input type="text" name="fields[<?php echo $i; ?>][name]" value="<?php echo $f['name']; ?>" placeholder="Field Name">
                    <input type="text" name="fields[<?php echo $i; ?>][label]" value="<?php echo $f['label']; ?>" placeholder="Label">
                    <select name="fields[<?php echo $i; ?>][type]">
                        <option value="text" <?php selected($f['type'], 'text'); ?>>טקסט</option>
                        <option value="select" <?php selected($f['type'], 'select'); ?>>בחירה (כן/לא)</option>
                    </select>
                </div>
            <?php endforeach; ?>
            <button type="button" class="button" onclick="alert('לחץ על הוסף שדה בקוד ה-JS למטה')">הוסף שדה +</button>
        </div>

        <div id="conditions-list">
            <label><strong>3. חוקים מותנים (If/Then):</strong></label><br>
            <?php foreach ($conditions as $ci => $c) : ?>
                <div style="background:#f0f0f1; padding:10px; margin-top:5px;">
                    אם שדה <b><?php echo $c['if_field']; ?></b> שווה ל-<b><?php echo $c['value']; ?></b> -> הצג את <b><?php echo $c['target_field']; ?></b>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

add_action( 'save_post', function( $post_id ) {
    if ( isset( $_POST['fields'] ) ) update_post_meta( $post_id, '_jmp_fields_config', $_POST['fields'] );
    // שמירת שאר הנתונים...
});
