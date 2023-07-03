<?php
function modo_mantenimiento()
{
  if (get_option('modo-mantenimiento-activo')) {
    if (!current_user_can('edit_themes') || !is_admin()) {
      wp_die('Sitio en mantenimiento. Por favor, vuelve más tarde.', 'Mantenimiento', array('response' => 503));
    }
  }
}
add_action('wp_loaded', 'modo_mantenimiento');

// Agrega una página de opciones en el menú de configuración
function modo_mantenimiento_menu_page()
{
  add_options_page(
    'Modo de Mantenimiento',     // Título de la página
    'Modo de Mantenimiento',     // Título del menú
    'manage_options',            // Capacidad requerida para acceder a la página
    'modo-mantenimiento',         // Slug único de la página
    'modo_mantenimiento_page'    // Función de callback para mostrar el contenido de la página
  );
}
add_action('admin_menu', 'modo_mantenimiento_menu_page');

// Registra la opción de habilitar/deshabilitar el modo de mantenimiento
function modo_mantenimiento_settings()
{
  register_setting(
    'modo-mantenimiento-group',     // Nombre del grupo de opciones
    'modo-mantenimiento-activo',    // Nombre de la opción
    'intval'                        // Callback para validar el valor de la opción (en este caso, convierte el valor a un entero)
  );
}
add_action('admin_init', 'modo_mantenimiento_settings');

// Callback para mostrar el contenido de la página de opciones
function modo_mantenimiento_page()
{
?>
  <div class="wrap">
    <h1>Modo de Mantenimiento</h1>
    <form method="post" action="options.php">
      <?php settings_fields('modo-mantenimiento-group'); ?>
      <?php do_settings_sections('modo-mantenimiento-group'); ?>
      <table class="form-table">
        <tr>
          <th scope="row">Habilitar Modo de Mantenimiento</th>
          <td>
            <label for="modo-mantenimiento-activo">
              <input type="checkbox" id="modo-mantenimiento-activo" name="modo-mantenimiento-activo" value="1" <?php checked(get_option('modo-mantenimiento-activo'), 1); ?>>
              Habilitar
            </label>
          </td>
        </tr>
      </table>
      <?php submit_button(); ?>
    </form>
  </div>
<?php
}
