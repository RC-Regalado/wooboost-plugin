<?php

// State code {{{
function get_department_name($code)
{
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
// }}}

function analize_meta()
{
    $order_data = isset($_POST['order_data']) ? $_POST['order_data'] : array();
    $args = array();
    $orders = wc_get_orders($args);
    $info = '';
    foreach ($orders as $order) {
        $order_data = array();
        $order_data["id"] = $order->ID;

        // Data Collect {{{
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
                case 'total':
                    $order_data['total'] = $order->get_total();
                    break;
                case 'products':
                    $order_items = $order->get_items();

                    $products = array();
                    foreach ($order_items as $item_id => $item) {
                        $product_name = $item->get_name();
                        $product_qty = $item->get_quantity();
                        $product_total = $item->get_total();
                        $products[] = sprintf("%s %s - $%s ",
                            $product_qty ,
                            $product_name ,
                            $product_total
                        );
                    }
                    $order_data['products'] = implode(';', $products);
                    break;
            }
        }
        // }}}
        $info .= implode(',', $order_data) . "\n";
    }
    return $info;
}

function headers()
{
    $str = ['Order ID'];
    foreach ($_POST['order_data'] as $data) {
        switch ($data) {
            case 'date':
                array_push($str, 'Fecha');
                break;
            case 'status':
                array_push($str, 'Estado');
                break;
            case 'customer_name':
                array_push($str, 'Nombre');
                break;
            case 'dui':
                array_push($str, 'DUI');
                break;
            case 'country':
                array_push($str, 'País');
                break;
            case 'state':
                array_push($str, 'Departamento');
                break;
            case 'phone':
                array_push($str, 'Teléfono');
                break;
            case 'customer_email':
                array_push($str, 'Correo');
                break;
            case 'billing_address':
                array_push($str, 'Dirección');
                break;
            case 'shipping_address':
                array_push($str, 'Dirección');
                break;
            case 'payment_method':
                array_push($str, 'Método de pago');
                break;
            case 'products':
                array_push($str, 'Productos');
                break;
            case 'total':
                array_push($str, 'Monto total');
                break;
        }
    }
    return implode(',', $str);
}


function generate_csv()
{
    if (!isset($_POST['order_data'])) {
        return;
    }

    $csv_data = headers() . "\n";
    $csv_data .= analize_meta();

    // Crear archivo CSV temporal
    $csv_file = wp_upload_dir()['basedir'] . '/reporte_ventas.csv';
    file_put_contents($csv_file, $csv_data);

    // Generar botón de descarga
    return '<a href="' . esc_url(wp_upload_dir()['baseurl'] . '/reporte_ventas.csv') . '" download="reporte_ventas.csv" class="button">' .
        'Estadísticas listas para su descarga'.
        '</a>';
}
