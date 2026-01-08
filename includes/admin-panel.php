<?php
// יצירת תפריט ניהול
add_action( 'init', 'jmp_register_cpt' );
function jmp_register_cpt() {
    register_post_type( 'product_option_rule', array(
        'labels' => array('name' => 'אפשרויות מוצר', 'singular_name' => 'חוק'),
        'public' => false, 'show_ui' => true, 'menu_icon' => 'dashicons-list-view',
        'supports' => array( 'title' ),
    ));
}

// הוספת שדות להגדרת החוק
add_action( 'add_meta_boxes', function() {
    add_meta_box( 'jmp_settings', 'הגדרות חוק', 'jmp_render_meta', 'product_option_rule' );
});

function jmp_render_meta( $post ) {
    $products = get_post_meta( $post->ID, '_jmp_ids', true );
    $price = get_post_meta( $post->ID, '_jmp_price', true );
    ?>
    <p>מזהי מוצרים (מופרדים בפסיק): <input type="text" name="jmp_ids" value="<?php echo $products; ?>"></p>
    <p>תוספת מחיר לצד שני: <input type="number" name="jmp_price" value="<?php echo $price; ?>"> ₪</p>
    <?php
}

add_action( 'save_post', function( $post_id ) {
    if ( isset( $_POST['jmp_ids'] ) ) update_post_meta( $post_id, '_jmp_ids', $_POST['jmp_ids'] );
    if ( isset( $_POST['jmp_price'] ) ) update_post_meta( $post_id, '_jmp_price', $_POST['jmp_price'] );
});
