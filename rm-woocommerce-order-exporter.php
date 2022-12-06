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
?>

<h1>RM Woocommerce Order Exporter</h1>

<form action="#" method="POST">
<div>
<span>Filter by Status: </span>
<select id='status' name='status'>
	<option value='textarea' selected>Filter by id</option>
	<option value='pending'>Pending</option>
	<option value='processing'>Processing</option>
	<option value='on-hold'>On-Hold</option>
	<option value='completed'>Completed</option>
	<option value='cancelled'>Cancelled</option>
	<option value='refunded'>Refunded</option>
	<option value='failed'>Failed</option>
	<option value='trash'>Trash</option>
</select>
</div>

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
}

add_action('admin_init', 'rmoe_export');
function rmoe_export() {
    if ($_SERVER['REQUEST_URI'] == '/wp-admin/admin.php?page=rmoe_order_exporter' && !empty($_POST['export_type']) && !empty($_POST['status'])) {
		$export_type = trim($_POST['export_type']);
		$orders = array();

		if ($_POST['status'] == 'textarea') {
			// get form parameters
			$order_ids = rm_explode_ids(trim($_POST['order_ids']));

			//var_dump($order_ids);

			foreach($order_ids as $key => $order_id) {
				$order = wc_get_order($order_id);
				array_push($orders, $order);
			}
		} else {
			$status = "wc-" . trim($_POST['status']);
			
			//var_dump($status);
			
			$orders = wc_get_orders(array(
				'status' => $status,
			));
		}

		// get orders
		$output_orders = array();
		
		foreach($orders as $key => $order) {
            if (false === $order) {
                continue; // order id doesn't exsist.
            } else {
                // get order data
                $order_data = $order->get_data();
                //var_dump($order);
                
                $data = array(
                    //'id' => $order_data['id'], // Update get order_id for Custom Order Numbers for WooCommerce plugin
                    'id' => $order->get_order_number(),
                    'name' => $order_data['shipping']['first_name'] . ' ' . $order_data['shipping']['last_name'],
                    'address' => $order_data['shipping']['address_1'] . ', ' . $order_data['shipping']['address_2'],
                    'address_1' => $order_data['shipping']['address_1'],
                    'address_2' => $order_data['shipping']['address_2'],
                    'city' => $order_data['shipping']['city'],
                    //'province' => WC()->countries->states[$order_data['shipping']['country']][$order_data['shipping']['state']],
                    'province' => $order_data['shipping']['state'],
                    'post' => $order_data['shipping']['postcode'],
                    //'country' => WC()->countries->countries[$order_data['shipping']['country']],
                    'country' => $order_data['shipping']['country'],
                    'tel' => $order_data['billing']['phone'],
					'notes' => $order_data['customer_note'],
					'shipping_method' => $order->get_shipping_method(),
                    'products' => array()
                );

                $products = $order->get_items();

                foreach($products as $item) {
                    $product = array(
                        'name' => $item->get_product()->get_name(),
                        'quantity' => $item->get_quantity(),
                        'sku' => $item->get_product()->get_sku(),
                        'image' => str_replace(get_site_url().'/', ABSPATH, wp_get_attachment_image_url($item->get_product()->get_image_id(), 'full')),
						'size' => $item->get_product()->get_attribute('size')
                    );
					//var_dump($product);
                    array_push($data['products'], $product);
                }

                //var_dump($data);
                array_push($output_orders, $data);
            }

        }
        
        // export
        /*
         * We need API like this
         * $exporter = new RMOE_Export_Factory;
         * $exporter->export($orders);
         */

        $exporter = NULL;
        if ($export_type == 'csv') {
            // csv
            $exporter = new RMOE_CsvExport;

        } elseif ($export_type == 'excel') {
            // excel
            $exporter = new RMOE_ExcelExport;
        }

        $exporter->export($output_orders);
        exit;
    }
}
