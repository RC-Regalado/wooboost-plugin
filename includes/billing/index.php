
<h1>Ventas realizadas</h1>

<form method="post" action="">
  <h4>Seleccione los datos a incluir en la estadística:</h4>
  <div>
    <input type="checkbox" name="order_data[]" value="date"> Fecha de la orden<br>
    <input type="checkbox" name="order_data[]" value="status"> Estado de la orden<br>
    <input type="checkbox" name="order_data[]" value="customer_name"> Nombre del cliente<br>
    <input type="checkbox" name="order_data[]" value="type">Tipo de factura<br>
    <input type="checkbox" name="order_data[]" value="dui">DUI<br>
    <input type="checkbox" name="order_data[]" value="country">País<br>
    <input type="checkbox" name="order_data[]" value="state">Departamento<br>
    <input type="checkbox" name="order_data[]" value="phone">Teléfono<br>
    <input type="checkbox" name="order_data[]" value="customer_email"> Correo electrónico del cliente<br>
    <input type="checkbox" name="order_data[]" value="billing_address"> Dirección de facturación del cliente<br>
    <input type="checkbox" name="order_data[]" value="payment_method"> Métodos de pago utilizados<br>
    <input type="checkbox" name="order_data[]" value="products"> Productos<br>
    <input type="checkbox" name="order_data[]" value="total"> Monto total<br>
  </div>
  <br>
  <button type="submit" class="button">Generar informe</button>
</form>
<?php
require_once('meta.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  echo generate_csv();
}

?>

<style>
table {
  width: 90%;
  border: 1px solid #ccc;
  border-collapse: collapse;
}

th,
td {
  border: 1px solid #ccc;
  padding: 10px;
}

</style>

<?php
/*
 *        <?php foreach ($pedidos as $pedido) : ?>
            <tr>
                <td><?php echo $pedido['fecha']; ?></td>
                <td><?php echo $pedido['nombre']; ?></td>
                <td><?php echo $pedido['precio']; ?></td>
                <td><?php echo $pedido['tipo_pago']; ?></td>
            </tr>
        <?php endforeach; ?>

 * 
 * */
?>
