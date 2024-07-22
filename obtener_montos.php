<?php
require_once("modelo/conexion.php");

// Verificar la conexión
if ($conn->connect_error) {
    die("Error en la conexión a la base de datos: " . $conn->connect_error);
}

// Obtener el ID del alquiler desde la solicitud POST
$idAlquiler = $_POST['alquilerId'];

// Consultar los montos desde la tabla alquiler
$sqlMontos = "SELECT monTotal, penalidad, rechazado FROM alquiler WHERE codAlquiler = $idAlquiler";
$resultMontos = $conn->query($sqlMontos);

if ($resultMontos->num_rows > 0) {
    $row = $resultMontos->fetch_assoc();
    $montoTotal = $row['monTotal'];
    $penalidad = $row['penalidad'];
    $rechazado = $row['rechazado'];

    $response = array(
        'success' => true,
        'montoTotal' => $montoTotal,
        'penalidad' => $penalidad,
        'rechazado' => $rechazado
    );
} else {
    $response = array(
        'success' => false,
        'message' => 'No se encontraron montos para el alquiler.'
    );
}

// Devolver la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>