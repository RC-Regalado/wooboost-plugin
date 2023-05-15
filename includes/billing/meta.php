<?php
function get_department_name($code) {
    $parts = explode('-', $code);
    $country_code = $parts[0];
    $department_code = $parts[1];
    
    switch($department_code) {
        case 'AH':
            return 'Ahuachapán';
        case 'CA':
            return 'Cabañas';
        case 'CH':
            return 'Chalatenango';
        case 'CU':
            return 'Cuscatlán';
        case 'LI':
            return 'La Libertad';
        case 'PZ':
            return 'La Paz';
        case 'UN':
            return 'La Unión';
        case 'MO':
            return 'Morazán';
        case 'SM':
            return 'San Miguel';
        case 'SS':
            return 'San Salvador';
        case 'SA':
            return 'Santa Ana';
        case 'SV':
            return 'San Vicente';
        case 'SO':
            return 'Sonsonate';
        case 'US':
            return 'Usulután';
        default:
            return '';
    }
}

function analize_meta()
{
    $order_data = isset($_POST['order_data']) ? $_POST['order_data'] : array();
    $args = array();
    $orders = wc_get_orders($args);
    foreach ($orders as $order) {
        $order_data = array();
        $order_data["id"] = $order->ID;

        foreach ($_POST['order_data'] as $data) {
            switch ($data) {
                case 'date':
                    $order_data['date'] = $order->get_date_created()->format('Y-m-d H:i:s');
                    break;
                case 'status':
                    $order_data['status'] = $order->get_status();
                    break;
                case 'customer_name':
                    $order_data['customer_name'] = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
                    break;
                case 'dui':
                    $order_data['dui'] = get_post_meta($order->ID, "_billing_dui", true);
                    break;
                case 'country':
                    $order_data['country'] = $order->get_billing_country();
                    break;
                case 'state':
                    $order_data['state'] = get_department_name($order->get_billing_state());
                    break;
                case 'phone':
                    $order_data['phone'] = $order->get_billing_phone();
                    break;
                case 'customer_email':
                    $order_data['customer_email'] = $order->get_billing_email();
                    break;
                case 'billing_address':
                    $order_data['billing_address'] = $order->get_formatted_billing_address();
                    break;
                case 'shipping_address':
                    $order_data['shipping_address'] = $order->get_formatted_shipping_address();
                    break;
                case 'payment_method':
                    $order_data['payment_method'] = $order->get_payment_method_title();
                    break;
                case 'products':
                    $order_items = $order->get_items();

                    $products = array();
                    foreach ($order_items as $item_id => $item) {
                        $product_name = $item->get_name();
                        $product_qty = $item->get_quantity();
                        $product_total = $item->get_total();
                        $products[] = array(
                          'name' => $product_name,
                          'quantity' => $product_qty,
                          'total' => $product_total,
                        );
                    }
                    $order_data['products'] = $products;
                    break;
            }
        }
    }
    return $order_data;
}


function generate_csv_button($data)
{
    $csv_data = '';
    $headers = array('Fecha', 'Nombre', 'Precio', 'Tipo de pago');
    $csv_data .= implode(',', $headers) . "\n";

    // Convertir datos a formato CSV
    foreach ($data as $row) {
        $csv_data .= implode(',', $row) . "\n";
    }

    // Crear archivo CSV temporal
    $csv_file = wp_upload_dir()['basedir'] . '/reporte_ventas.csv';
    file_put_contents($csv_file, $csv_data);

    // Generar botón de descarga
    $button = '<a href="' . esc_url(wp_upload_dir()['baseurl'] . '/reporte_ventas.csv') . '" download="reporte_ventas.csv" class="button">Descargar CSV</a>';

}
