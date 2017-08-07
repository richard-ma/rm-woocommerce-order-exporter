<?php
/*
 * Plugin Name: RM Woocommerce order exporter
 * Plugin_URI: https://github.com/richard-ma/rm-woocommerce-order-exporter
 * Author: Richard Ma
 * Author URI: http://richardma.info
 * Version: 1.0
 * Lincense: MIT
 *
 * Plugin Prefix: rmoe_ / RMOE_
 */

// exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// check woocommerce
if (!in_array( 'woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    exit;
}

// require other php files
require_once( plugin_dir_path(__FILE__) . 'rm-functions.php');
require_once( plugin_dir_path(__FILE__) . 'rm-woocommerce-order-exporter-csv.php');
require_once( plugin_dir_path(__FILE__) . 'rm-woocommerce-order-exporter-excel.php');

// require js & css files
function rmoe_admin_enqueue_scripts() {
    global $pagenow;

    if ($pagenow == 'admin.php') {
        wp_enqueue_script('ajax-download', plugins_url('js/ajax-download.js', __FILE__), array('jquery'), 20170807, true);
    }
}
add_action('admin_enqueue_scripts', 'rmoe_admin_enqueue_scripts');

$RMOE_EXPORTER_FORM = plugin_dir_path(__FILE__) . 'rm-woocommerce-order-exporter-form.html';

// add submenu export to woocommerce
function rmoe_add_exporter_page_to_admin_submenu() {
    add_submenu_page(
        'woocommerce',
        __('RM Woocommerce Order Exporter'),
        __('Order Exporter'),
        'manage_options',
        'rmoe_order_exporter',
        'rmoe_admin_exporter_submenu_callback'
    );
}
add_action('admin_menu', 'rmoe_add_exporter_page_to_admin_submenu');

// show order exporter form
function rmoe_admin_exporter_submenu_callback() {
    global $RMOE_EXPORTER_FORM;
    echo file_get_contents($RMOE_EXPORTER_FORM);
}

