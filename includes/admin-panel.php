<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// רישום סוג הפוסט והוספת תמיכה בחיפוש מוצרים
add_action( 'init', 'jmp_register_cpt_pro' );
function jmp_register_cpt_pro() {
    register_post_type( 'product_option_rule', array(
        'labels' => array('name' => 'אפשרויות מוצר', 'singular_name' => 'חוק'),
        'public' => false, 'show_ui' => true, 'menu_icon' => 'dashicons-admin-generic',
        'supports' => array( 'title' ),
    ));
}

// הוספת סקריפטים לניהול (בשביל חיפוש מוצרים נוח)
add_action('admin_enqueue_scripts', function($hook) {
    if (get_post_type() !== 'product_option_rule') return;
    wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'));
    wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
});

function jmp_render_pro_builder( $post ) {
    $applied_ids = get_post_meta( $post->ID, '_jmp_ids', true ) ?: [];
    $fields = get_post_meta( $post->ID, '_jmp_fields_config', true ) ?: [];
    ?>
    <div style="direction:rtl; text-align:right; padding:15px; background:#f4f4f4; border-radius:8px;">
        
        <label><strong>בחר מוצרים להחלת החוק:</strong></label><br>
        <select name="jmp_ids[]" class="jmp-product-search" multiple="multiple" style="width:100%; margin-bottom:20px;">
            <?php 
            foreach($applied_ids as $id) {
                echo '<option value="'.$id.'" selected>'.get_the_title($id).'</option>';
            }
            ?>
        </select>

        <hr>
        <div id="jmp-fields-wrapper">
            <h3>מבנה השדות והחוקים</h3>
            <?php foreach ( $fields as $i => $f ) : ?>
                <div class="field-row" style="border:1px solid #ddd; padding:15px; margin-bottom:15px; background:#fff; position:relative;">
                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:10px;">
                        <div>
                            <label>שם פנימי (Field Name):</label><br>
                            <input type="text" name="fields[<?php echo $i; ?>][name]" value="<?php echo esc_attr($f['name']); ?>" placeholder="למשל: back_engraving">
                        </div>
                        <div>
                            <label>כותרת להצגה (Label):</label><br>
                            <input type="text" name="fields[<?php echo $i; ?>][label]" value="<?php echo esc_attr($f['label']); ?>" style="width:100%;">
                        </div>
                        <div>
                            <label>סוג שדה:</label><br>
                            <select name="fields[<?php echo $i; ?>][type]" style="width:100%;">
                                <option value="text" <?php selected($f['type'], 'text'); ?>>Text</option>
                                <option value="select" <?php selected($f['type'], 'select'); ?>>Select</option>
                                <option value="image" <?php selected($f['type'], 'image'); ?>>Image Upload</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-top:10px; background:#f9f9f9; padding:10px; border-top:1px solid #eee;">
                        <label><input type="checkbox" name="fields[<?php echo $i; ?>][has_logic]" <?php checked(isset($f['has_logic'])); ?>> הוסף חוק מותנה לשדה זה</label>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" style="color:red; margin-top:10px; border:none; background:none; cursor:pointer;">[מחק שדה]</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add_field" class="button button-primary">הוסף שדה חדש +</button>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // הפיכת שדה ה-IDs לחיפוש מוצרים חכם
        $('.jmp-product-search').select2({
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                data: function(params) { return { q: params.term, action: 'jmp_get_products' }; },
                processResults: function(data) { return { results: data }; }
            },
            minimumInputLength: 2
        });
    });
    </script>
    <?php
}

// פונקציית עזר לחיפוש מוצרים ב-AJAX
add_action('wp_ajax_jmp_get_products', function() {
    $search = sanitize_text_field($_GET['q']);
    $products = wc_get_products(array('status' => 'publish', 'limit' => -1, 's' => $search));
    $results = [];
    foreach ($products as $p) { $results[] = ['id' => $p->get_id(), 'text' => $p->get_name()]; }
    wp_send_json($results);
});

add_action( 'save_post', function( $post_id ) {
    if ( isset( $_POST['jmp_ids'] ) ) update_post_meta( $post_id, '_jmp_ids', $_POST['jmp_ids'] );
    if ( isset( $_POST['fields'] ) ) update_post_meta( $post_id, '_jmp_fields_config', $_POST['fields'] );
});
