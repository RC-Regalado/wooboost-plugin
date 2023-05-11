<?php
// Función para agregar la página de opciones
function promotions_menu() {
  add_menu_page( 
    'Configuración de promociones', // Título de la página
    'Promociones', // Texto del menú
    'manage_options', // Capacidad necesaria para acceder
    ADMIN . '/admin/render.php'
  );

}

add_action( 'admin_menu', 'promotions_menu' );

// Función para verificar si un producto está en promoción
function is_product_in_promotion($product_id, $product_qty) {
    global $wpdb;

    // Consulta para verificar si el producto está en una promoción
    $query = "
        SELECT 
            promotions.product_id, 
            promotions.producto_gratis
        FROM 
            wp_promociones AS promotions 
        WHERE 
            promotions.producto_condicion = %d 
            AND promotions.condicion = %d
        LIMIT 1
    ";

    // Obtener el resultado de la consulta
    $result = $wpdb->get_row($wpdb->prepare($query, $product_id, $product_qty));

    // Si el producto está en una promoción, devolver el ID del producto gratuito
    if ($result) {
        return $result->free_product_id;
    }

    // Si el producto no está en ninguna promoción, devolver falso
    return false;
}

?>
