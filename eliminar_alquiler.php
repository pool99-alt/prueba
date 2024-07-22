<?php
require_once("modelo/conexion.php");
// Incluye el archivo wp-load.php para acceder a las funciones de WordPress
require_once("../../../wp-load.php");

if (isset($_POST['nroAlquiler'])) {
    $nroOrden = $_POST['nroAlquiler'];
    
    // Verificar la conexión
    if ($conn->connect_error) {
        die("Error en la conexión a la base de datos: " . $conn->connect_error);
    }

    // Iniciar una transacción para asegurarse de que ambas eliminaciones sean exitosas
    $conn->begin_transaction();

    try {
        // Preparar y ejecutar la consulta de eliminación de productos relacionados en detallepedido
        $sqlEliminarDetalle = "DELETE FROM detallepedido WHERE idPedido = ?";
        $stmtDetalle = $conn->prepare($sqlEliminarDetalle);
        $stmtDetalle->bind_param("i", $nroOrden);
        $stmtDetalle->execute();

        // Preparar y ejecutar la consulta de eliminación de los pagos del alquiler
        $sqlEliminarPago = "DELETE FROM pago WHERE idPedido = ?";
        $stmtPago = $conn->prepare($sqlEliminarPago);
        $stmtPago->bind_param("i", $nroOrden);
        $stmtPago->execute();

        // Preparar y ejecutar la consulta de eliminación de orden de alquiler
        $sqlEliminar = "DELETE FROM alquiler WHERE codAlquiler = ?";
        $stmt = $conn->prepare($sqlEliminar);
        $stmt->bind_param("i", $nroOrden);
        $stmt->execute();

        // Confirmar la transacción
        $conn->commit();
        
        // Cerrar la conexión
        $stmtDetalle->close();
        $stmtPago->close();
        $stmt->close();
        $conn->close();

        $redirect_url = home_url('/wp-admin/admin.php?page=lasal-alquileres');
        header("Location: " . $redirect_url);
        exit();
    } catch (Exception $e) {
        // Revertir la transacción si hay un error
        $conn->rollback();
        
        // Mostrar un mensaje de error
        echo "Error al eliminar la orden de alquiler: " . $e->getMessage();
    }
} else {
    echo "No se proporcionó el número de alquiler.";
}
?>