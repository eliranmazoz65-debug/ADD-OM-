<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// יצירת תפריט ניהול מרכזי
add_action( 'init', 'jmp_register_options_cpt' );
function jmp_register_options_cpt() {
    register_post_type( 'product_option_rule', array(
        'labels' => array('name' => 'אפשרויות מוצר', 'singular_name' => 'חוק'),
        'public' => false, 'show_ui' => true, 'menu_icon' => 'dashicons-list-view',
        'supports' => array( 'title' ),
    ));
}

// הוספת פאנל הגדרות בתוך החוק
add_action( 'add_meta_boxes', function() {
    add_meta_box( 'jmp_settings', 'הגדרות החוק והלוגיקה', 'jmp_render_meta_box', 'product_option_rule' );
});

function jmp_render_meta_box( $post ) {
    $applied_ids = get_post_meta( $post->ID, '_jmp_applied_ids', true );
    $backside_price = get_post_meta( $post->ID, '_jmp_backside_price', true );
    ?>
    <div style="direction:rtl; text-align:right;">
        <p><strong>מזהי מוצרים (מופרדים בפסיק):</strong><br>
        <input type="text" name="jmp_applied_ids" value="<?php echo esc_attr($applied_ids); ?>" style="width:100%;"></p>
        <p><strong>תוספת מחיר לצד שני (₪):</strong><br>
        <input type="number" name="jmp_backside_price" value="<?php echo esc_attr($backside_price); ?>"></p>
    </div>
    <?php
}

add_action( 'save_post', function( $post_id ) {
    if ( isset( $_POST['jmp_applied_ids'] ) ) update_post_meta( $post_id, '_jmp_applied_ids', sanitize_text_field( $_POST['jmp_applied_ids'] ) );
    if ( isset( $_POST['jmp_backside_price'] ) ) update_post_meta( $post_id, '_jmp_backside_price', sanitize_text_field( $_POST['jmp_backside_price'] ) );
});
