<?php
require_once("modelo/conexion.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el id del alquiler desde la solicitud AJAX
    $alquilerId = $_POST['alquilerId'];

    // Verificar la conexión
    if ($conn->connect_error) {
        $response = array(
            'success' => false,
            'message' => 'Error en la conexión a la base de datos: ' . $conn->connect_error
        );
    } else {
        // Consultar los abonos para el id de alquiler
        $sql = "SELECT * FROM pago WHERE idPedido = $alquilerId";
        $result = $conn->query($sql);

        if ($result) {
            $abonos = array();
            while ($row = $result->fetch_assoc()) {
                $abonos[] = $row;
            }
            $response = array(
                'success' => true,
                'abonos' => $abonos
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Error al obtener los abonos de la base de datos: ' . $conn->error
            );
        }
    }

    // Cerrar la conexión
    $conn->close();

    // Devolver la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>