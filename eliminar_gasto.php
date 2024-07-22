<?php
require_once("modelo/conexion.php");
// Incluye el archivo wp-load.php para acceder a las funciones de WordPress
require_once("../../../wp-load.php");

if (isset($_POST['nroGasto'])) {
    $nroGasto = $_POST['nroGasto'];
    
    // Verificar la conexión
    if ($conn->connect_error) {
        die("Error en la conexión a la base de datos: " . $conn->connect_error);
    }

    // Iniciar una transacción para asegurarse de que ambas eliminaciones sean exitosas
    $conn->begin_transaction();

    try {
        // Preparar y ejecutar la consulta de eliminación de los pagos del alquiler
        $sqlEliminarGasto = "DELETE FROM gasto WHERE codGasto = ?";
        $stmtGasto = $conn->prepare($sqlEliminarGasto);
        $stmtGasto->bind_param("i", $nroGasto);
        $stmtGasto->execute();

        // Confirmar la transacción
        $conn->commit();
        
        // Cerrar la conexión
        $stmtGasto->close();
        $conn->close();

        $redirect_url = home_url('/wp-admin/admin.php?page=lasal-gastos');
        header("Location: " . $redirect_url);
        exit();
    } catch (Exception $e) {
        // Revertir la transacción si hay un error
        $conn->rollback();
        
        // Mostrar un mensaje de error
        echo "Error al eliminar el gasto: " . $e->getMessage();
    }
} else {
    echo "No se proporcionó el número del gasto.";
}
?>