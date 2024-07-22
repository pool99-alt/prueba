<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once("modelo/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extraer los datos del formulario
    $vendedor = $_POST['vendedor'];
    $codVendedor = $_POST['codVendedor'];
    $codCliente = $_POST['codCliente'];
    $nombCliente = $_POST['nombCliente'];
    $estadoOrden = $_POST['estadoOrden'];
    $direccion = $_POST['direccion'];
    $fechaEvento = $_POST['fechaEvento'];
    $fechaOperacion = $_POST['fechaOperacion'];
    $fechaEntregaProductos = $_POST['fechaEntregaProductos'];
    $fechaDevolucionProductos = $_POST['fechaDevolucionProductos'];
    $observacion = $_POST['observacion'];
    $subTotal = $_POST['subTotal'];
    $movilidad = $_POST['movilidad'];
    $descuento = $_POST['descuento'];
    $importeTotalPagar = $_POST['importeTotalPagar'];
    $saldoPagar = $_POST['saldoPagar'];
    $codigoAlquiler = $_POST['codigoAlquiler'];


    // Verificar la conexión
    if ($conn->connect_error) {
        $response = array(
            'success' => false,
            'message' => 'Error en la conexión a la base de datos: ' . $conn->connect_error
        );
    } else {
        // Preparar y ejecutar la consulta de inserción
        $sql = "UPDATE alquiler SET estOrden = '$estadoOrden', dirOrden = '$direccion', obsOrden = '$observacion', 
                movilidad = '$movilidad', descuento = '$descuento', monTotal = '$saldoPagar' WHERE codAlquiler = $codigoAlquiler";

        if ($conn->query($sql) === TRUE) {

            // Después de realizar la inserción en la tabla alquiler y obtener el ID
            //$idAlquiler = $conn->insert_id;
            $response = array(
                'success' => true,
                'message' => 'Datos guardados exitosamente.'
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Error al guardar los datos en la base de datos: ' . $conn->error
            );
        }



        // Elimimar los detalles de pedido previos

            // Realizar la inserción en la tabla detallepedido
            $sqlEliminarDetallePrevio = "DELETE FROM detallepedido WHERE idPedido = $codigoAlquiler;";
            
            if ($conn->query($sqlEliminarDetallePrevio) !== TRUE) {
                $response = array(
                    'success' => false,
                    'message' => 'Error al eliminar los detalles en la base de datos: ' . $conn->error
                );

            }



        // Iterar a través de los detalles de productos y realizar las inserciones en detallepedido
        foreach ($_POST['detalles'] as $detalle) {
            $fecha = $detalle['fecha'];
            $fechaEntega = $detalle['fechaEntega'];
            $nombreProducto = $detalle['nombre'];
            $cantidad = $detalle['cantidad'];
            $precioUnitario = $detalle['precio'];
            $estado = $detalle['estado'];
            $stockExterno = empty($detalle['stockExterno']) ? 0 : $detalle['stockExterno'];
            $empresa = $detalle['empresa'];
            $subTotal = $detalle['subTotal'];
            $idProducto = $detalle['codigoProducto'];

            // Realizar la inserción en la tabla detallepedido
            $sqlDetalle = "INSERT INTO detallepedido (idPedido, idProducto, fecRegistro, fecEntrega, fecDevolucion, nomProducto, cantProducto, preProducto, estaDetallePedido, 
                           stocExterno, nomEmpresa, monSubTotal, stocInterno, cantDevuelto, cantRechazado, monPenalidad, monRechazado, obsNota) VALUES (
                            '$codigoAlquiler',
                            '$idProducto',
                            '$fecha',
                            '$fechaEntega',
                            '$fechaDevolucionProductos',
                            '$nombreProducto',
                            '$cantidad',
                            '$precioUnitario',
                            '$estado',
                            '$stockExterno',
                            '$empresa',
                            '$subTotal',
                            0,
                            0,
                            0,
                            0,
                            0,
                            '')";
            
            if ($conn->query($sqlDetalle) !== TRUE) {
                $response = array(
                    'success' => false,
                    'message' => 'Error al guardar los detalles en la base de datos: ' . $conn->error
                );
            }
            
        }




        // Cerrar la conexión
        $conn->close();
        header("Location: https://lasaldelavida.pe/wp-admin/admin.php?page=lasal-alquileres");
        exit(); // Asegura que el script se detenga después de la redirección
    }

    // Devolver la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
