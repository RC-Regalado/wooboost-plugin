<h1>Hello</h1>
<?php
global $wpdb;
$users = $wpdb->get_results("SELECT ID, user_login FROM {$wpdb->users}");

$options = '';
foreach ($users as $user) {
    $options .= '<option value="' . $user->user_login . '">' . $user->user_login . '</option>';
}

?>
<label for="user">Selecciona un usuario:</label>
<input type="text" name="user" id="user" list="userlist">
<datalist id="userlist">
    <?php echo $options; ?>
</datalist>
<select name="user_select" id="user_select">
    <?php echo $options; ?>
</select>

  <h1>Factura</h1>
  <form action="procesar_factura.php" method="POST">
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" required><br>

    <label for="apellido">Apellido:</label>
    <input type="text" id="apellido" name="apellido" required><br>

    <label for="direccion">Dirección:</label>
    <input type="text" id="direccion" name="direccion" required><br>

    <label for="telefono">Teléfono:</label>
    <input type="tel" id="telefono" name="telefono" required><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>

    <label for="dui">DUI:</label>
    <input type="text" id="dui" name="dui" required><br>

    <input type="submit" value="Enviar">
  </form>

