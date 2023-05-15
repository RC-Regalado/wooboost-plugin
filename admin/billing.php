<?php
function billing_menu() {
  add_menu_page( 
    'Estadísticas de venta', // Título de la página
    'Estadísticas', // Texto del menú
    'manage_options', // Capacidad necesaria para acceder
    ADMIN . '/includes/billing/index.php'
  );

}

add_action( 'admin_menu', 'billing_menu' );

function billing_page(){
    require_once(ADMIN . '/includes/billing/index.php');
}
?>
