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

function rmoe_admin_exporter_submenu_callback() {
    if (empty($_POST)) {
    // show order exporter form
?>

<h1>RM Woocommerce Order Exporter</h1>

<form action="#" method="POST">
<div>
<textarea id='order_ids' name='order_ids' rows='10' cols='80'></textarea>
</div>

<div>
<span>TYPE: </span>
<input id='export_csv' name="export_type" type="radio" value="csv" /><span>CSV</span>
<input id='export_excel' name="export_type" type="radio" value="excel" /><span>EXCEL</span>
<input id='submit_button' type="submit" value="Export" />
</div>

</form>

<?php

    } else {
        // get form parameters
        $order_ids = rm_explode_ids(trim($_POST['order_ids']));
        $export_type = trim($_POST['export_type']);

        //var_dump($order_ids);
        //var_dump($export_type);

        // get orders
        foreach($order_ids as $key => $order_id) {
            $order = wc_get_order($order_id);

            if (false === $order) {
                continue; // order id doesn't exsist.
            } else {
                // get order data
                $order = $order->get_data();
                //var_dump($order);
                
                $data = array(
                    'id' => $order_id,
                    'name' => $order['shipping']['first_name'] . ' ' . $order['shipping']['last_name'],
                    'address' => $order['shipping']['address_1'] . ' ' . $order['shipping']['address_2'],
                    'city' => $order['shipping']['city'],
                    'province' => $order['shipping']['state'],
                    'post' => $order['shipping']['postcode'],
                    'country' => $order['shipping']['country'],
                    'tel' => $order['billing']['phone'],
                );

                var_dump($data);
            }

        }
        
        // export
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
