<?php
/*
require_once("modelo/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos de la solicitud POST
    $nombVendedor = $_POST['nombVendedor'];
    $codVendedor = $_POST['codVendedor'];

    // Verificar la conexiÃ³n
    if ($conn->connect_error) {
        $response = array(
            'success' => false,
            'message' => 'Error en la conexiÃ³n a la base de datos: ' . $conn->connect_error
        );
    } else {
        // Consultar los abonos para el id de alquiler
        $sql = "INSERT INTO alquiler (nombVendedor, codVendedor) VALUES ('$nombVendedor', $codVendedor)";
        $result = $conn->query($sql);

        if ($result) {
            $response = array(
                'success' => true,
                'message' => 'Registro insertado correctamente'
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Error al insertar el registro: ' . $conn->error
            );
        }
    }

    // Cerrar la conexiÃ³n
    $conn->close();

    // Devolver la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
*/


require_once("modelo/conexion.php");

// Verificar la conexi¨®n
if ($conn->connect_error) {
    die("Error en la conexi¨®n a la base de datos: " . $conn->connect_error);
}

// Obtener el ID del alquiler desde la solicitud POST
$idAlquiler = $_POST['alquilerId'];


$sql = "INSERT INTO alquiler (nombVendedor, codVendedor) VALUES ('Pepito', 114)";
$result = $conn->query($sql);

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