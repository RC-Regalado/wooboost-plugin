<?php

add_action('add_meta_boxes', 'custom_page_product_metabox');

function custom_page_product_metabox()
{
    add_meta_box('custom-page-product', 'Asignar producto a página', 'assign_page_callback', 'product', 'side', 'high');
}

function assign_page_callback($post)
{
    wp_nonce_field(basename(__FILE__), 'page_product_metabox_nonce');

    $assigned_page = get_post_meta($post->ID, '_assigned_page', true);
    ?>
    <p>
        <label for="assigned_page">Página asignada:</label>
        <select name="assigned_page">
<?php
    $options = array(
  'all' => 'Todas',
  'mercadito' => 'Mercadito',
  'oficina' => 'Oficina',
  'tour' => 'Tour',
  'otros' => 'Otros'
);

    foreach ($options as $value => $lbl) {
        $selected = $assigned_page == $value ? 'selected' : '';
        echo '<option value="' . $value . '" ' . $selected . '>' . $lbl . '</option>';
    }
    ?>
       </select>
    </p>
    <?php
}

add_action('save_post', 'page_metabox_save');

function page_metabox_save($post_id)
{
    if (! isset($_POST['page_product_metabox_nonce']) || ! wp_verify_nonce($_POST['page_product_metabox_nonce'], basename(__FILE__))) {
        return;
    }

    if (isset($_POST['assigned_page'])) {
        update_post_meta($post_id, '_assigned_page', sanitize_text_field($_POST['assigned_page']));
    }
}

// Agregar el meta box para ingresar los porcentajes de descuento
add_action('add_meta_boxes', 'custom_discount_metabox');
function custom_discount_metabox()
{
    add_meta_box('custom-discount', 'Descuentos', 'custom_discount_metabox_callback', 'product', 'side', 'high');
}

function custom_discount_metabox_callback($post)
{
    wp_nonce_field(basename(__FILE__), 'custom_discount_metabox_nonce');
    $values = get_post_meta($post->ID, '_custom_discount_meta', true);
    ?>
    <p>
        <label for="custom_discount_meta[one]">1 producto:</label>
        <input type="text" name="custom_discount_meta[one]" value="<?php echo isset($values['one']) ? esc_attr($values['one']) : ''; ?>" />
    </p>
    <p>
        <label for="custom_discount_meta[two]">2 productos:</label>
        <input type="text" name="custom_discount_meta[two]" value="<?php echo isset($values['two']) ? esc_attr($values['two']) : ''; ?>" />
    </p>
    <p>
        <label for="custom_discount_meta[three]">3 productos:</label>
        <input type="text" name="custom_discount_meta[three]" value="<?php echo isset($values['three']) ? esc_attr($values['three']) : ''; ?>" />
    </p>
    <?php
}

// Guardar los valores del meta box
add_action('save_post', 'custom_discount_metabox_save');
function custom_discount_metabox_save($post_id)
{
    if (!isset($_POST['custom_discount_meta']) || !wp_verify_nonce($_POST['custom_discount_metabox_nonce'], basename(__FILE__))) {
        return;
    }
    $values = $_POST['custom_discount_meta'];
    update_post_meta($post_id, '_custom_discount_meta', $values);
}

function custom_discount_calculate($cart_object)
{
    foreach ($cart_object->get_cart() as $key => $value) {
        // print_r($value['data']);

        $product_id = $value['data']->get_id();
        $product_count = $value['quantity'];

        if ($product_count == 1) {
            $discount = get_post_meta($product_id, '_custom_discount_meta', true)['one'];
        } elseif ($product_count == 2) {
            $discount = get_post_meta($product_id, '_custom_discount_meta', true)['two'];
        } elseif ($product_count >= 3) {
            $discount = get_post_meta($product_id, '_custom_discount_meta', true)['three'];
        }

        $discount = intval($discount);
        $price = $value['data']->get_price();
        $new_price = $price - ($price * ($discount / 100));
        $value['data']->set_price($new_price);
    }
}
add_action('woocommerce_before_calculate_totals', 'custom_discount_calculate');

add_action('manage_product_posts_custom_column', 'custom_product_column_content');
function custom_product_column_content($column_name)
{
    global $post, $product;

    if ($column_name === 'stock') {
        $stock = get_post_meta($post->ID, '_stock', true);
        ?>
        <div class="stock-wrapper">
            <input type="number" class="stock-input" value="<?php echo esc_attr($stock); ?>" min="0" max="9999" step="1">
        </div>
        <?php
    }
}

?>
