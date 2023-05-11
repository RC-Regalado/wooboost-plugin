<?php
// Guardar {{{
function guardar_promocion()
{
    global $wpdb;
    $tabla = $wpdb->prefix . 'promociones';

    $nombre = $_POST['nombre'];
    $producto = $_POST['producto'];
    $cantidad = $_POST['cantidad'];
    $free = $_POST['free_product_id'];

    $data = array(
        'nombre_promocion' => $nombre,
        'producto_condicion' => $producto,
        'condicion' => $cantidad,
        'producto_gratis' => $free
    );

    if ($promocion_id) {
        $where = array( 'id' => $promocion_id );
        $format = array( '%s', '%d', '%d', '%d' );
        $result = $wpdb->update($tabla, $data, $where, $format);
    } else {
        $format = array( '%s', '%d', '%d', '%d' );
        $result = $wpdb->insert($tabla, $data, $format);
    }

    if (!$result) {
        wp_die('Hubo un error al guardar la promoción');
    }
}

if (isset($_POST['nombre'])) {
    // Verificar si el formulario fue enviado
    if (! wp_verify_nonce($_POST['nonce_promocion'], 'guardar_promocion')) {
        wp_die('Acceso no autorizado', 'Error de seguridad');
        file_put_contents('rc-log', "Error");
    }

    guardar_promocion();
    // Mostrar mensaje de éxito
    echo '<div class="notice notice-success is-dismissible">';
    echo '<p>Promoción agregada correctamente.</p>';
    echo '</div>';
} else if (isset($_GET['erase'])) {
    echo '<div class="notice notice-success is-dismissible">';
    echo '<p>Promoción eliminada con éxito.</p>';
    echo '</div>';
}
// }}}
?>

<div class="wrap">
    <h1>Configuración de promociones</h1>
    <form method="post">
        <label for="nombre">Nombre de la promoción:
            <input type="text" id="nombre" name="nombre" ><br>
        </label>

        <label for="producto">Producto de la promoción:</label>
        <?php
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
        );
$productos = new WP_Query($args);
?>
        <select id="producto" name="producto">
            <?php while ($productos->have_posts()) : $productos->the_post(); ?>
                <option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
            <?php endwhile;
wp_reset_postdata(); ?>
        </select><br>

        <label for="cantidad">Cantidad de product:</label>
        <input type="number" id="cantidad" name="cantidad" value="1"><br>

        <div class="form-group">
            <label for="free_product_id">Producto gratis:</label>
            <select name="free_product_id" id="free_product_id">
                <?php
// Obtener todos los productos de WooCommerce
$args = array(
    'post_type' => 'product',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
);
$products = get_posts($args);

// Iterar sobre los productos y crear las opciones del select
foreach($products as $product) {
    echo '<option value="' . $product->ID . '">' . $product->post_title . '</option>';
}
?>
            </select>
        </div>
        <input type="hidden" name="action" value="guardar_promocion">
        <?php wp_nonce_field('guardar_promocion', 'nonce_promocion'); ?>
        <input type="submit" value="Guardar promoción">

    </form>
</div>

<?php
global $wpdb;

// Consulta a la tabla wp_promotions
$results = $wpdb->get_results("SELECT p.id, p.nombre_promocion, pr.post_title as producto_condicion, c.post_title as producto_gratis, p.condicion " .
  "FROM wp_promociones p ".
  "JOIN wp_posts pr ON p.producto_condicion = pr.ID " .
  "JOIN wp_posts c ON p.producto_gratis = c.ID;");

// Imprimir la tabla HTML con los resultados
?>
	<h1>Promociones</h1>
	<table class="widefat">
		<thead>
			<tr>
				<th>ID</th>
				<th>Nombre</th>
				<th>Producto de condición</th>
				<th>Condición</th>
				<th>Producto de regalo</th>
				<th>Acciones</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($results as $result) : ?>
				<tr>
					<td><?php echo esc_html($result->id); ?></td>
					<td><?php echo esc_html($result->nombre_promocion); ?></td>
					<td><?php echo esc_html($result->producto_condicion); ?></td>
					<td><?php echo esc_html($result->condicion); ?></td>
					<td><?php echo esc_html($result->producto_gratis); ?></td>
					<td>
						<a href="<?php echo esc_url(admin_url('admin.php?page=cihuatan-plugin/admin/render.php&id=' . $result->id)); ?>">Editar</a>
						|
						<a href="<?php echo esc_url(admin_url('admin.php?page=cihuatan-plugin/admin/render.php&erase=' . $result->id)); ?>">Eliminar</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

