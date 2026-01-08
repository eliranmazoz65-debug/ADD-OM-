<?php
/**
 * Plugin Name: Jewelry Master Personalizer PRO
 * Description: מערכת חריטה מתקדמת עם לוגיקה מותנית וחיפוש מוצרים
 * Version: 2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// טעינת הקבצים מהתיקייה הפנימית
require_once plugin_dir_path( __FILE__ ) . 'includes/admin-panel.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/frontend-logic.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/cart-logic.php';
