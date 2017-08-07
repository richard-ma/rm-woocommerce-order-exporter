<?php
/*
 * Plugin Name: Woocommerce order exporter
 * Plugin_URI: https://github.com/richard-ma/rm-woocommerce-order-exporter
 * Author: Richard Ma
 * Author URI: http://richardma.info
 * Version: 1.0
 * Lincense: MIT
 */

// exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// check woocommerce
if (!in_array( 'woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    exit;
}

// require other php file
require_once( plugin_dir_path(__FILE__) . 'rm-woocommerce-order-exporter-csv.php');
require_once( plugin_dir_path(__FILE__) . 'rm-woocommerce-order-exporter-excel.php');
