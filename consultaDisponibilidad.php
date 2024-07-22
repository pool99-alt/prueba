<?php
require_once("modelo/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados por AJAX
    $datosGenerales = json_decode(file_get_contents("php://input"));
    // Extraer los detalles de los productos del array
    $detallesProductos = $datosGenerales->detalles;

    $responseData = array(); // Crear un array para almacenar las cantidades disponibles
        // Iterar a través de los detalles de productos
        foreach ($detallesProductos as $detalle) {
            $idProducto = $detalle->codigoProducto;
            $fechaEntrega = $detalle->fechaEntega;
            $fechaDevolucion = $detalle->fechaDevolucion;

            
            $cantStock = 0;
            $minStock = 99999999999;
        
            // Obtener el stock inicial del producto
            $sqlStock = "SELECT cantstock FROM kleon_wp_lasaldelavida.v_producto WHERE ID = $idProducto";
            $resultStock = $conn->query($sqlStock);
        
            if ($resultStock->num_rows > 0) {
                $rowStock = $resultStock->fetch_assoc();
                $cantStock = $rowStock["cantstock"];
            }
        
            // Realizar cálculos para cada día dentro del rango
            $fechaAnalisis = $fechaEntrega;
            while ($fechaAnalisis <= $fechaDevolucion) {
                $sqlEntregado = "SELECT COALESCE(SUM(d.cantProducto), 0) AS total_entregado
                                 FROM kleon_wp_lasaldelavida.detallepedido d
                                 WHERE d.idProducto = $idProducto
                                 AND d.fecEntrega <= '$fechaAnalisis'";
        
                $sqlDevuelto = "SELECT COALESCE(SUM(d.cantProducto), 0) AS total_devuelto
                                FROM kleon_wp_lasaldelavida.detallepedido d
                                WHERE d.idProducto = $idProducto
                                AND d.fecDevolucion <= '$fechaAnalisis'";
        
                $resultEntregado = $conn->query($sqlEntregado);
                $resultDevuelto = $conn->query($sqlDevuelto);
        
                $totalEntregado = $resultEntregado->fetch_assoc()["total_entregado"];
                $totalDevuelto = $resultDevuelto->fetch_assoc()["total_devuelto"];
        
                $cantStockDia = $cantStock - $totalEntregado + $totalDevuelto;
        
                if ($cantStockDia < $minStock) {
                    $minStock = $cantStockDia;
                }
        
                $fechaAnalisis = date('Y-m-d', strtotime($fechaAnalisis . ' +1 day'));
            }
        
            $cantidadesDisponibles[$idProducto] = $minStock; // Almacenar el stock mínimo en el array           
        }

        $response = array(
            'success' => true,
            'cantidadesDisponibles' => $cantidadesDisponibles // Agregar este array al objeto de respuesta
        );



    // Devolver la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
