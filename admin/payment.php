<?php

// Registrar el método de pago
add_action('plugins_loaded', 'descuento_plania_gateway_init');
function descuento_plania_gateway_init()
{
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

    // Clase Descuento {{{
    class WC_Descuento_Plania_Gateway extends WC_Payment_Gateway
    {
        // Constructor del método de pago
        public function __construct()
        {
            $this->plugin_id = 'plania_gateway';
            $this->id = 'descuento_plania_gateway';
            $this->has_fields = false;
            $this->method_title = 'Descuento en Planilla';
            $this->method_description = 'Permite a los clientes realizar pagos a través de descuentos en planilla.';
            $this->supports = array('products');

            // Cargar la configuración del gateway
            $this->init_form_fields();
            $this->init_settings();

            // Asignar los valores a las propiedades
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->instructions = $this->get_option('instructions');

            // Registrar los hooks
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
            add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);
        }

        // Configuración del formulario de opciones
        public function init_form_fields()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => 'Habilitar/Deshabilitar',
                    'type' => 'checkbox',
                    'label' => 'Habilitar Descuento en Planilla',
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => 'Título',
                    'type' => 'text',
                    'description' => 'Este es el título que verá el cliente durante el proceso de pago.',
                    'default' => 'Descuento en Planilla',
                    'desc_tip' => true
                ),
                'description' => array(
                    'title' => 'Descripción',
                    'type' => 'textarea',
                    'description' => 'Este es el mensaje que se mostrará en la página de checkout.',
                    'default' => 'Pague a través de descuento en planilla.',
                    'desc_tip' => true
                ),
                'instructions' => array(
                    'title' => 'Instrucciones',
                    'type' => 'textarea',
                    'description' => 'Instrucciones que se mostrarán en la página de agradecimiento y en el correo electrónico de confirmación de pedido.',
                    'default' => 'El pago se realizará a través de descuento en planilla. Se le proporcionarán más instrucciones después de que se complete el pedido.',
                    'desc_tip' => true
                )
            );
        }

        // Mostrar información en la página de checkout
        public function payment_fields()
        {
            if ($this->description) {
                echo wpautop(wptexturize($this->description));
            }
        }

        // Validar los campos del formulario de checkout
        public function validate_fields()
        {
            return true;
        }

        // Procesar el pago
        public function process_payment($order_id)
        {
            $order = wc_get_order($order_id);
            $order = wc_create_order(array('customer_id' => get_current_user_id()));
            foreach ($cart_items as $cart_item_key => $cart_item) {
                $product = $cart_item['data'];
                $product_id = $product->get_id();
                $product_name = $product->get_name();
                $product_price = $product->get_price();
                $product_qty = $cart_item['quantity'];
                $product_subtotal = $product_price * $product_qty;
                $order->add_product($product, $product_qty, array('subtotal' => $product_subtotal, 'total' => $product_subtotal));
            }
            $order->set_address($billing_address, 'billing');
            $order->set_address($shipping_address, 'shipping');
            $order->set_payment_method($payment_method);
            $order->calculate_totals();
            $order->update_status('processing', 'Pago recibido mediante Descuento en Planilla');
            WC()->cart->empty_cart();
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }
    }
    // }}}

    add_filter('woocommerce_payment_gateways', 'agregar_descuento_plania_gateway');
    function agregar_descuento_plania_gateway($methods)
    {
        $methods[] = 'WC_Descuento_Plania_Gateway';
        return $methods;
    }

    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'add_descuento_plania_gateway_settings_link');
    function add_descuento_plania_gateway_settings_link($links)
    {
        $descuento_plania_gateway_settings_link = '<a href="admin.php?page=wc-settings&tab=checkout&section=descuento_plania_gateway">' . __('Settings', 'descuento-plania-gateway') . '</a>';
        array_push($links, $descuento_plania_gateway_settings_link);
        return $links;
    }

    add_filter('woocommerce_get_settings_checkout', 'add_descuento_plania_gateway_settings');
    function add_descuento_plania_gateway_settings($settings)
    {
        $settings[] = array(
            'title' => __('Descuento Planía', 'descuento-plania-gateway'),
            'type' => 'title',
            'desc' => __('Configuración del descuento planía', 'descuento-plania-gateway'),
            'id' => 'descuento_plania_gateway_settings'
        );

        $settings[] = array(
            'title' => __('Minimum Order Amount', 'descuento-plania-gateway'),
            'type' => 'text',
            'description' => __('Enter the minimum order amount for which the Descuento Planía Gateway payment method will be available', 'descuento-plania-gateway'),
            'default' => '',
            'desc_tip' => true,
            'id' => 'descuento_plania_gateway_minimum_order_amount'
        );

        $settings[] = array(
            'type' => 'sectionend',
            'id' => 'descuento_plania_gateway_settings'
        );

        return $settings;
    }

    add_filter('woocommerce_available_payment_gateways', 'enable_for_assigned_page');

    function enable_for_assigned_page($available_gateways)
    {
        if (isset($_COOKIE['compra']) && $_COOKIE['compra'] === 'mercadito') {
           return $available_gateways;
        } else {
            // si no tiene el rol, deshabilita la pasarela de pago
            unset($available_gateways['descuento_plania_gateway']);
            return $available_gateways;
        }
    }
}
