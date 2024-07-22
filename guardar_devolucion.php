<?php
require_once("modelo/conexion.php");
// Incluye el archivo wp-load.php para acceder a las funciones de WordPress
require_once("../../../wp-load.php");

if (isset($_POST['nroAlquiler'])) {
    $nroOrden = $_POST['nroAlquiler'];
    $totalPenalidad = $_POST['totalPenalidad'];
    $totalRechazado = $_POST['totalRechazado'];

    // Obtener los datos actualizados de cada producto
    $idDetallePedidoArray = $_POST['idDetallePedido'];
    $fechaDevolucionArray = $_POST['fechaDevolucion'];
    $cantDevueltaArray = $_POST['cantDevuelta'];
    $cantRechazadaArray = $_POST['cantRechazada'];
    $notaDevolucionArray = $_POST['notaDevolucion'];
    $precioAlquilerArray = $_POST['precioAlquiler'];
    $precioReposicionArray = $_POST['precioReposicion'];
    $cantidadAlquiladaArray = $_POST['cantAlquilada'];
    $cantidadExternaArray = $_POST['cantExterna'];
    $idProductoArray = $_POST['idProducto'];

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Error en la conexión a la base de datos: " . $conn->connect_error);
    }

    $conn->begin_transaction();

    try {
        // Preparar y ejecutar la consulta de actualización de la orden de alquiler
        $sqlActualizarOrden = "UPDATE alquiler SET penalidad = '$totalPenalidad', rechazado = '$totalRechazado' WHERE codAlquiler = '$nroOrden'";
        $conn->query($sqlActualizarOrden);

        // Preparar y ejecutar la consulta de actualización de los productos en detallepedido
        //$sqlActualizarDetalle = "UPDATE detallepedido SET fecDevolucion = ?, cantDevuelto = ?, cantRechazado = ?, obsNota = ?, monPenalidad = ?, monRechazado = ? WHERE idDetallePedido = ?";
        $sqlActualizarDetalle = "UPDATE detallepedido SET cantDevuelto = ?, cantRechazado = ?, obsNota = ?, monPenalidad = ?, monRechazado = ? WHERE idDetallePedido = ?";
        $stmtDetalle = $conn->prepare($sqlActualizarDetalle);

        for ($i = 0; $i < count($idDetallePedidoArray); $i++) {
            $idDetallePedido = $idDetallePedidoArray[$i];
            $fechaDevolucion = $fechaDevolucionArray[$i];
            $cantDevuelta = $cantDevueltaArray[$i];
            $cantRechazada = $cantRechazadaArray[$i];
            $notaDevolucion = $notaDevolucionArray[$i];
            $precioAlquiler = $precioAlquilerArray[$i];
            $precioReposicion = $precioReposicionArray[$i];
            $cantAlquilada = $cantidadAlquiladaArray[$i];
            $cantExterna = $cantidadExternaArray[$i];
            $idProducto = $idProductoArray[$i];
            // Asignar 0 a $precioReposicion si está vacío
            $precioReposicion = empty($precioReposicion) ? 0 : $precioReposicion;
            $montoPenalidad = (($cantAlquilada + $cantExterna) - $cantDevuelta) * $precioReposicion;
            $montoRechazado = $cantRechazada * $precioAlquiler;

            //$stmtDetalle->bind_param("siisddi", $fechaDevolucion, $cantDevuelta, $cantRechazada, $notaDevolucion, $montoPenalidad, $montoRechazado, $idDetallePedido);
            $stmtDetalle->bind_param("iisddi", $cantDevuelta, $cantRechazada, $notaDevolucion, $montoPenalidad, $montoRechazado, $idDetallePedido);
            $stmtDetalle->execute();

            if ($cantDevuelta == 0) {
                //echo 'nada que ver :0';
            }
            else{
                // Calcular la cantidad actualizada de stock solo si hay cantidad devuelta
                $sqlStock = "SELECT cantstock FROM kleon_wp_lasaldelavida.v_producto WHERE ID = ?";
                $stmtStock = $conn->prepare($sqlStock);
                $stmtStock->bind_param("i", $idProducto);
                $stmtStock->execute();
                $resultStock = $stmtStock->get_result();

                if ($resultStock->num_rows > 0) {
                    $rowStock = $resultStock->fetch_assoc();
                    $stockActual = $rowStock["cantstock"];
                }

                $cantidadActualizada = $stockActual - ($cantAlquilada + $cantExterna) + $cantDevuelta;
                echo $cantidadActualizada;
                echo '<br>';


            // Actualizar el stock en la tabla wp_postmeta
            $sqlUpdateStock = "UPDATE kleon_wp_lasaldelavida.wped_postmeta SET meta_value = '$cantidadActualizada' WHERE post_id = '$idProducto' AND meta_key = '_stock'";
            $conn->query($sqlUpdateStock);
            }
        }

        // Confirmar la transacción
        $conn->commit();

        // Cerrar la conexión
        $stmtDetalle->close();
        $conn->close();

        $redirect_url = home_url('/wp-admin/admin.php?page=lasal-alquileres');
        header("Location: " . $redirect_url);
        exit();


    } catch (Exception $e) {
        // Revertir la transacción si hay un error
        $conn->rollback();

        // Mostrar un mensaje de error
        echo "Error al actualizar la orden de alquiler: " . $e->getMessage();
    }
} else {
    echo "No se proporcionó el número de alquiler.";
}
