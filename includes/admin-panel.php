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
    add_meta_box( 'jmp_pro_builder', 'הגדרת שדות וחוקים מותנים', 'jmp_render_pro_builder', 'product_option_rule' );
});

function jmp_render_pro_builder( $post ) {
    $applied_ids = get_post_meta( $post->ID, '_jmp_ids', true ) ?: [];
    $fields = get_post_meta( $post->ID, '_jmp_fields_config', true ) ?: [];
    $conditions = get_post_meta( $post->ID, '_jmp_conditions', true ) ?: [];
    ?>
    <style>
        .jmp-box { background:#fff; border:1px solid #ccd0d4; padding:15px; margin-bottom:20px; border-radius:4px; }
        .jmp-field-item { border-bottom:1px solid #eee; padding:15px 0; position:relative; }
        .jmp-field-item:last-child { border-bottom:none; }
        .jmp-row { display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap; }
        .jmp-row input, .jmp-row select { padding: 5px; border: 1px solid #ccc; }
        .delete-btn { color: #a00; cursor: pointer; font-size: 12px; text-decoration: underline; }
    </style>

    <div style="direction:rtl; text-align:right;">
        <div class="jmp-box">
            <label><strong>1. בחר מוצרים (לפי שם/ID):</strong></label><br>
            <input type="text" name="jmp_ids_raw" value="<?php echo implode(',', $applied_ids); ?>" style="width:100%; margin-top:5px;" placeholder="הכנס מזהי מוצרים מופרדים בפסיק">
        </div>

        <div class="jmp-box">
            <label><strong>2. הגדרת שדות (Fields):</strong></label>
            <div id="jmp-fields-list">
                <?php foreach ($fields as $i => $f) : ?>
                    <div class="jmp-field-item">
                        <div class="jmp-row">
                            <input type="text" name="fields[<?php echo $i; ?>][name]" value="<?php echo $f['name']; ?>" placeholder="Field Name (e.g. back)">
                            <input type="text" name="fields[<?php echo $i; ?>][label]" value="<?php echo $f['label']; ?>" placeholder="Field Label (כותרת)">
                            <select name="fields[<?php echo $i; ?>][type]">
                                <option value="text" <?php selected($f['type'], 'text'); ?>>Text (חריטה)</option>
                                <option value="select" <?php selected($f['type'], 'select'); ?>>Select (בחירה)</option>
                            </select>
                            <input type="number" name="fields[<?php echo $i; ?>][price]" value="<?php echo $f['price']; ?>" placeholder="מחיר" style="width:80px;">
                            <label><input type="checkbox" name="fields[<?php echo $i; ?>][emoji]" <?php checked(isset($f['emoji'])); ?>> אימוג'ים</label>
                        </div>
                        <span class="delete-btn" onclick="this.parentElement.remove()">מחק שדה</span>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="add-new-field" class="button">הוסף שדה +</button>
        </div>

        <div class="jmp-box">
            <label><strong>3. חוקים מותנים (If/Then):</strong></label>
            <div id="jmp-conditions-list">
                <?php foreach ($conditions as $ci => $c) : ?>
                    <div class="jmp-field-item" style="background:#f9f9f9; padding:10px;">
                        <div class="jmp-row">
                            <span>אם</span>
                            <input type="text" name="cond[<?php echo $ci; ?>][if_field]" value="<?php echo $c['if_field']; ?>" placeholder="שם שדה המקור">
                            <span>שווה ל-</span>
                            <input type="text" name="cond[<?php echo $ci; ?>][value]" value="<?php echo $c['value']; ?>" placeholder="ערך">
                            <span>אז</span>
                            <select name="cond[<?php echo $ci; ?>][action]">
                                <option value="show" <?php selected($c['action'], 'show'); ?>>הצג (Show)</option>
                                <option value="hide" <?php selected($c['action'], 'hide'); ?>>הסתר (Hide)</option>
                            </select>
                            <input type="text" name="cond[<?php echo $ci; ?>][target_field]" value="<?php echo $c['target_field']; ?>" placeholder="שם שדה היעד">
                        </div>
                        <span class="delete-btn" onclick="this.parentElement.remove()">מחק חוק</span>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="add-new-condition" class="button">הוסף חוק מותנה +</button>
        </div>
    </div>

    <script>
        document.getElementById('add-new-field').addEventListener('click', function() {
            const list = document.getElementById('jmp-fields-list');
            const i = list.children.length;
            const html = `
                <div class="jmp-field-item">
                    <div class="jmp-row">
                        <input type="text" name="fields[${i}][name]" placeholder="Field Name">
                        <input type="text" name="fields[${i}][label]" placeholder="Field Label">
                        <select name="fields[${i}][type]"><option value="text">Text</option><option value="select">Select</option></select>
                        <input type="number" name="fields[${i}][price]" placeholder="מחיר" style="width:80px;">
                    </div>
                    <span class="delete-btn" onclick="this.parentElement.remove()">מחק</span>
                </div>`;
            list.insertAdjacentHTML('beforeend', html);
        });

        document.getElementById('add-new-condition').addEventListener('click', function() {
            const list = document.getElementById('jmp-conditions-list');
            const i = list.children.length;
            const html = `
                <div class="jmp-field-item" style="background:#f9f9f9; padding:10px;">
                    <div class="jmp-row">
                        <span>אם</span> <input type="text" name="cond[${i}][if_field]">
                        <span>שווה ל</span> <input type="text" name="cond[${i}][value]">
                        <span>אז</span> <select name="cond[${i}][action]"><option value="show">הצג</option></select>
                        <input type="text" name="cond[${i}][target_field]">
                    </div>
                    <span class="delete-btn" onclick="this.parentElement.remove()">מחק</span>
                </div>`;
            list.insertAdjacentHTML('beforeend', html);
        });
    </script>
    <?php
}

add_action( 'save_post', function( $post_id ) {
    if ( isset( $_POST['jmp_ids_raw'] ) ) {
        $ids = array_map('trim', explode(',', $_POST['jmp_ids_raw']));
        update_post_meta( $post_id, '_jmp_ids', $ids );
    }
    if ( isset( $_POST['fields'] ) ) update_post_meta( $post_id, '_jmp_fields_config', $_POST['fields'] );
    if ( isset( $_POST['cond'] ) ) update_post_meta( $post_id, '_jmp_conditions', $_POST['cond'] );
});
