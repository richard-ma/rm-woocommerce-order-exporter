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
require_once( plugin_dir_path(__FILE__) . 'rmoe-class-export.php');
require_once( plugin_dir_path(__FILE__) . 'rmoe-class-csv-export.php');
require_once( plugin_dir_path(__FILE__) . 'rmoe-class-excel-export.php');

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
    if (empty($_POST)) {
?>

<h1>RM Woocommerce Order Exporter</h1>

<form action="#" method="POST">
<div>
<textarea id='post_ids' name='post_ids' rows='10' cols='80'></textarea>
</div>

<div>
<input id='export_csv' type="submit" value="Export CSV" />
<input id='export_excel' type="submit" value="Export EXCEL" />
</div>
</form>

<?php

    } else {
        /*
         * We need API like this
         * $exporter = new RMOE_Export_Factory;
         * $exporter->export($data);
         */

        /*
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="address-list-'.date('Y_m_d_H_i_s').'.csv"');
        header('Cache-Control: max-age=0');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Pragma: public'); // HTTP/1.0
         */
    }
}
