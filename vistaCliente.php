<?php
/**
 * Plugin Name: Funcionalidades Extra - Lasal
 * Description: Este es un plugin que permite agregar funcionalidad al sistema de alquiler de artículos, con módulos de venta, registro de clientes, reportes, etc.
 */

function lasal_admin_menu()
{
    //Opción para la gestión de clientes
    add_menu_page('Clientes', 'Clientes', 'read', 'lasal-admin-clientes', 'lasal_admin_menu_clientes', 'dashicons-buddicons-buddypress-logo', 2);
    //Opción para la gestión de reportes
    add_menu_page('Reportes','Reportes','read','lasal-reportes','lasal_admin_menu_reportes','dashicons-media-spreadsheet',4);
    add_submenu_page('lasal-reportes','Reporte - Ventas por cliente','Ventas por cliente','read','lasal-reportes-ventas-cliente','lasal_reportes_ventas_cliente');
    add_submenu_page('lasal-reportes','Reporte - Ventas por usuario','Ventas por usuario','read','lasal-reportes-ventas-usuario','lasal_reportes_ventas_usuario');
    add_submenu_page('lasal-reportes','Reporte - Stock por producto','Stock por producto','read','lasal-reportes-stock-producto','lasal_reportes_stock_producto');
    add_submenu_page('lasal-reportes','Reporte - Ventas por producto','Ventas por producto','read','lasal-reportes-ventas-producto','lasal_reportes_ventas_producto');
    add_submenu_page('lasal-reportes','Reporte - Ventas por fecha','Ventas por fecha','read','lasal-reportes-ventas-fecha','lasal_reportes_ventas_fecha');
    add_submenu_page('lasal-reportes','Reporte - Ventas 2023 - Sistema antiguo','Ventas 2023 - Sistema antiguo','read','lasal-reportes-ventas-2023-sistema-antiguo','lasal_reportes_ventas_2023_sistema_antiguo');
    add_submenu_page('lasal-reportes','Reporte - Gastos','Gastos','read','lasal-reportes-gastos','lasal_reportes_gastos');
    //Opción para la gestión de alquileres
    add_menu_page('Alquileres', 'Alquileres', 'read', 'lasal-alquileres', 'lasal_admin_menu_alquileres', 'dashicons-cart', 3);
    //Opción para crear nueva orden
    add_submenu_page('lasal-alquileres', 'Alquiler - Crear nueva orden', 'Nueva orden', 'read', 'lasal-alquileres-nueva-orden', 'lasal_alquileres_nueva_orden');
    add_submenu_page('','Alquiler - Editar orden','Editar orden','read','lasal-alquileres-editar-orden','lasal_alquileres_editar_orden');
    add_submenu_page('','Alquiler - Devolver productos','Devolucion','read','lasal-alquileres-devolucion','lasal_alquileres_devolucion');
    //Opción para la gestión de gastos
    add_menu_page('Gastos', 'Gastos', 'read', 'lasal-gastos', 'lasal_admin_menu_gastos', 'dashicons-money', 4);
    //Opción para crear nueva gasto
    add_submenu_page('lasal-gastos', 'Gastos - Registrar nueva gasto', 'Nueva gasto', 'read', 'lasal-gastos-nuevo-gasto', 'lasal_gastos_nuevo_gasto');
    add_submenu_page('','Gasto - Editar gasto','Editar gasto','read','lasal-gastos-editar-gasto','lasal_gastos_editar_gasto');
}
add_action('admin_menu','lasal_admin_menu');


//-------------------------------------------------------Funciones para mostrar contenido por opción--------------------------------------------------------

//------------------------------------------------------ Gastos----------------------------------------------------
function lasal_admin_menu_gastos() {
  require_once plugin_dir_path(__FILE__) . '/modelo/conexion.php';
  require_once plugin_dir_path( __FILE__ ) . 'controlador/controladorGasto.php';
  require_once(plugin_dir_path(__FILE__) . '../../../wp-load.php');

  global $current_user;
  $nombreVendedor = $current_user->user_login;
  $userID = $current_user->ID;
    ?>
    <!DOCTYPE html>
    <html>
    <head>
      <title>Lista de Gastos</title>
      <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/alquiler.css', __FILE__); ?>"> 
      <script src="<?php echo plugins_url('/js/alquiler.js', __FILE__); ?>"></script>
      <script src="<?php echo plugins_url('/js/filtros_alquiler.js', __FILE__); ?>"></script>
      <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    </head>
    <body>
      <div class="container">
       <div class="cabecera">
              <!-- Barra de búsqueda -->
              <div class="titulo">
                  <label for="cliente">Todos los Gastos</label>
              </div>
              <div class="date-selector">
                  <label for="nroPedido">Vendedor:</label>
                  <input type="text" class="search-input" id="nomVendedor" value="<?php echo $nombreVendedor; ?>" style="height: 10px; margin-top: 9px;" readonly>
              </div>
       </div> 
       <div class="search-container">
          <div class="date-selector">
              <form method="post" action="<?php echo admin_url('admin.php?page=lasal-gastos-nuevo-gasto'); ?>">
                  <button type="submit" class="add-button">Registrar Gasto</button>
              </form>
          </div>
          <!-- Buscar cliente 
          <div class="date-selector">
              <label for="fechaInicio">Desde:</label>
              <input type="date" id="fechaInicio">
          </div>
          <div class="date-selector">
              <label for="fechaFin">Hasta:</label>
              <input type="date" id="fechaFin">
          </div>  
          -->
     </div>
     <br>
     <br>
      <div class="tabla-scroll">
        <table id="tablaAlquileres">
          <thead>
            <tr>
              <th>Nro</th>
              <th>Fecha</th>
              <th>Descripción</th>
              <th>Razón Social (Proveedor)</th>
              <th>Monto sin IGV</th>
              <th>Monto con IGV</th>
              <th>Observaciones</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($gastos as $gasto): ?>
              <tr>
              <td class='numero-alquiler'><?php echo $gasto['codGasto']; ?></td>
              <td><?php echo $gasto['fechaGasto']; ?></td>
              <td><?php echo $gasto['descGasto']; ?></td>
              <td class='nombre-cliente'><?php echo $gasto['razonSocial']; ?></td>
              <td><?php echo $gasto['monSinIgv']; ?></td>
              <td><?php echo $gasto['monConIgv']; ?></td>
              <td><?php echo $gasto['observacion']; ?></td>
              <td>
                  <div class="actions-container">

                    <form method="POST" action="<?php echo admin_url('admin.php?page=lasal-gastos-editar-gasto'); ?>" style="display: inline-block;";>
                      <input type="hidden" name="nroGasto" value="<?php echo $gasto['codGasto']; ?>">
                      <button type="submit" class="edit-button" style="background-color: #FFC300; border: 1px solid #FFC300; border-radius: 5px;" title="Editar">
                      <i class='bx bxs-edit-alt' ></i>
                      </button>
                    </form>

                    <?php $eliminar_action_url = plugins_url('/eliminar_gasto.php', __FILE__); ?>
                    <form method="POST" action="<?php echo $eliminar_action_url; ?>" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este gasto?');" style="display: inline-block;">
                      <input type="hidden" name="nroGasto" value="<?php echo $gasto['codGasto']; ?>">
                      <button type="submit" class="delete-button" style="background-color: #ff6565; border: 1px solid #d1052a; border-radius: 5px;" title="Eliminar">
                      <i class='bx bxs-trash' title="Eliminar"></i>
                      </button>
                    </form>

                  </div>
               </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>




      </div>
    </body>
    </html>

    <?php


}

function lasal_gastos_nuevo_gasto() {
  require_once plugin_dir_path(__FILE__) . '/modelo/conexion.php';
  require_once(plugin_dir_path(__FILE__) . '../../../wp-load.php');
  global $current_user;
  $nombreVendedor = $current_user->user_login;
  $userID = $current_user->ID;

  ?>
  <!DOCTYPE html>
  <html>
  <head>
      <title>Lista de Gastos</title>
      <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/nueva_orden.css', __FILE__); ?>">
      <script src="<?php echo plugins_url('/js/nuevo_gastodfgd.js', __FILE__); ?>"></script>
      <script src="<?php echo plugins_url('/js/prueba_ordecxcnx.js', __FILE__); ?>"></script>
  </head>
  <body>
  <div class="container">
   <form id="formularioGasto" method="POST" action=" " onsubmit="return confirm('¿Estás seguro de registrar el gasto?');">
    <div class="cabecera">
              <!-- Barra de búsqueda -->
              <div class="titulo">
                  <label for="cliente">Nuevo Gasto</label>
              </div>
              <div class="date-selector">
                  <label for="nroPedido">Vendedor:</label>
                  <!-- Elemento input oculto para almacenar el ID del vendedor -->
                  <input type="hidden" id="vendedor-id" name="vendedor-id" value="<?php echo $userID; ?>">
                  <input type="text" class="vendedor" id="nomVendedor" name="vendedor" value="<?php echo $nombreVendedor; ?>" readonly>
              </div>
    </div> 


     <div class="search-container">
          <!-- Selectores de fecha -->
          <div class="date-selector">
              <label for="fechaOperacion">Fecha de Operación:</label>
              <input type="date" id="fechaOperacion" name="fechaOperacion" value="<?php echo date('Y-m-d'); ?>">
          </div>
          <div class="date-selector">
          <label for="razonSocial">Razón Social:</label>
          <input type="text" class="razonSocial" id="razonSocial" placeholder="Razón social" name="razonSocial" style="width: 400px" required>
          </div>
     </div>
     <div class="search-container">
          <div class="date-selector">
          <label for="descGasto">Descripción del gasto:</label>
          </div>
      </div>
      <div class="search-container">
          <div class="date-selector">
          <textarea id="descGasto" rows="10" cols="100" placeholder="Escribe la descripción del gasto..." name="descGasto"></textarea>
          </div>
    </div>


      
<div style="display: flex; flex-direction: row;">
    <!-- Contenedor del lado izquierdo -->
    <div class="search-container">
          <div class="date-selector">
          <label for="observacion">Observaciones del gasto:</label>
          </div>
      </div>
      <div class="search-container">
          <div class="date-selector">
          <textarea id="observacion" rows="10" cols="100" placeholder="Escribe tus observaciones del gasto aquí..." name="observacion"></textarea>
          </div>
    </div>

  <!-- Contenedor del lado derecho -->
  <div class="montos" style="flex: 1;">
    <div class="date-selector" style="margin-bottom: 10px;">
      <label for="monSinIgv">Monto sin IGV:</label>
      <input type="number" class="vendedor" id="monSinIgv" name="monSinIgv" required step="0.01" min="0.00" style="text-align: right;">
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <label for="monConIgv">Monto con IGV:</label>
      <input type="number" class="vendedor" id="monConIgv" name="monConIgv" style="text-align: right;" step="0.01">
    </div>
  </div>
</div>
     <div class="cabecera">
          <div>
              <button type="button" class="cancelar-button" id="cancelarGasto">Cancelar Operación</button>
          </div>
          <div>
              <button type="submit" class="aceptar-button" name="guardarGasto" id="aceptarOrdenx">Aceptar Operación</button>
          </div>   
     </div>
     </form>
     <script>
          document.getElementById('cancelarGasto').addEventListener('click', function() {
            // Mostrar un mensaje de confirmación
            const confirmacion = confirm('¿Seguro que desea cancelar el registro del gasto?');
            
            // Si el usuario confirma, redirigir a la página de la lista de alquileres
            if (confirmacion) {
              window.location.href = "<?php echo admin_url('admin.php?page=lasal-gastos'); ?>";
            }
          });


              // Obtener elementos del DOM
            const monSinIgvInput = document.getElementById('monSinIgv');
            const monConIgvInput = document.getElementById('monConIgv');

            // Función para calcular y actualizar el monto con IGV
            function actualizarMontoConIgv() {
                const monSinIgv = parseFloat(monSinIgvInput.value) || 0;
                // Calcular el monto con IGV
                const montoConIgv = monSinIgv * 1.18;
                // Mostrar el resultado en el campo correspondiente
                monConIgvInput.value = montoConIgv.toFixed(2);
            }

            // Escuchar el evento 'input' en monSinIgvInput
            monSinIgvInput.addEventListener('input', actualizarMontoConIgv);
            
            // Calcular y mostrar el monto con IGV inicialmente
            actualizarMontoConIgv();
      </script>


  </div>

  </body>
  </html>
  <?php
  if (isset($_POST['guardarGasto'])) {
    // Obtener los datos enviados por el formulario de abono
    $fechaGasto = $_POST['fechaOperacion'];
    $descGasto = $_POST['descGasto'];
    $razonSocial = $_POST['razonSocial'];
    $monSinIgv = $_POST['monSinIgv'];
    $monConIgv = $_POST['monConIgv']; 
    $observacion = $_POST['observacion']; 
    $nombVendedor = $nombreVendedor;
    $codVendedor = $userID;
  
  
      // Preparar y ejecutar la consulta de inserción en la tabla pago
      $sqlGasto = "INSERT INTO gasto (fechaGasto, descGasto, razonSocial, monSinIgv, monConIgv, observacion, nombVendedor, codVendedor) VALUES (
                  '$fechaGasto',
                  '$descGasto',
                  '$razonSocial',
                  '$monSinIgv',
                  '$monConIgv',
                  '$observacion',
                  '$nombVendedor',
                  '$codVendedor'
              )";
  
      if ($conn->query($sqlGasto) === TRUE) {
        redireccionar_a_lista_gastos();
        exit(); // Asegura que no se envíen más datos después de la redirección

      } else {
          echo "Error al registrar el gasto: " . $conn->error;
      }
  
      // Cerrar la conexión
      $conn->close();
  
  }



}



function lasal_gastos_editar_gasto() {
    require_once plugin_dir_path(__FILE__) . '/modelo/conexion.php';
    require_once(plugin_dir_path(__FILE__) . '../../../wp-load.php');
    global $current_user;
    $nombreVendedor = $current_user->user_login;
    $userID = $current_user->ID;

    if (isset($_POST['nroGasto'])) {
        $nroGasto = $_POST['nroGasto'];

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Consulta para obtener los productos
    $sql = "SELECT codGasto, fechaGasto, descGasto, razonSocial, monSinIgv, monConIgv, observacion, nombVendedor, codVendedor FROM gasto WHERE codGasto = ?;";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $nroGasto); // "i" indica que se espera un parámetro de tipo entero
    $stmt->execute();
    $stmt->store_result();

    // Vincular los resultados a variables
    $stmt->bind_result($codGasto, $fechaGasto, $descGasto, $razonSocial, $monSinIgv, $monConIgv, $observacion, $nombVendedor, $codVendedor);
    
    // Obtener los resultados
    $stmt->fetch();
    
    // Liberar resultados y cerrar la consulta
    $stmt->free_result();
    $stmt->close();

    // Aquí puedes usar las variables recuperadas para llenar los controles del formulario
    // Por ejemplo:
    //echo "Fecha de Gasto: " . $fechaGasto;
    // Y así sucesivamente con los demás campos

    } else {
        echo "No se proporcionó el número del gasto.";
    }

  
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Lista de Gastos</title>
        <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/nueva_orden.css', __FILE__); ?>">
        <script src="<?php echo plugins_url('/js/nuevo_gastodfgd.js', __FILE__); ?>"></script>
        <script src="<?php echo plugins_url('/js/prueba_ordecxcnx.js', __FILE__); ?>"></script>
    </head>
    <body>
    <div class="container">
     <form id="formularioGasto" method="POST" action=" " onsubmit="return confirm('¿Estás seguro de actualizar el gasto?');">
      <div class="cabecera">
                <!-- Barra de búsqueda -->
                <div class="titulo">
                    <label for="cliente">Editar Gasto</label>
                </div>
                <div class="date-selector">
                    <label for="nroPedido">Vendedor:</label>
                    <!-- Elemento input oculto para almacenar el ID del vendedor -->
                    <input type="hidden" id="vendedor-id" name="vendedor-id" value="<?php echo $codVendedor; ?>">
                    <input type="hidden" id="codGasto" name="codGasto" value="<?php echo $codGasto; ?>">
                    <input type="text" class="vendedor" id="nomVendedor" name="vendedor" value="<?php echo $nombVendedor; ?>" readonly>
                </div>
      </div> 
  
  
       <div class="search-container">
            <!-- Selectores de fecha -->
            <div class="date-selector">
                <label for="fechaOperacion">Fecha de Operación:</label>
                <input type="date" id="fechaOperacion" name="fechaOperacion" value="<?php echo $fechaGasto; ?>">
            </div>
            <div class="date-selector">
            <label for="razonSocial">Razón Social:</label>
            <input type="text" class="razonSocial" id="razonSocial" placeholder="Razón social" name="razonSocial" value="<?php echo $razonSocial; ?>" style="width: 400px" required>
            </div>
       </div>
       <div class="search-container">
            <div class="date-selector">
            <label for="descGasto">Descripción del gasto:</label>
            </div>
        </div>
        <div class="search-container">
            <div class="date-selector">
            <textarea id="descGasto" rows="10" cols="100" placeholder="Escribe la descripción del gasto..." name="descGasto"><?php echo $descGasto; ?></textarea>
            </div>
      </div>
  
  
        
  <div style="display: flex; flex-direction: row;">
      <!-- Contenedor del lado izquierdo -->
      <div class="search-container">
            <div class="date-selector">
            <label for="observacion">Observaciones del gasto:</label>
            </div>
        </div>
        <div class="search-container">
            <div class="date-selector">
            <textarea id="observacion" rows="10" cols="100" placeholder="Escribe tus observaciones del gasto aquí..." name="observacion"><?php echo $observacion; ?></textarea>
            </div>
      </div>
  
    <!-- Contenedor del lado derecho -->
    <div class="montos" style="flex: 1;">
      <div class="date-selector" style="margin-bottom: 10px;">
        <label for="monSinIgv">Monto sin IGV:</label>
        <input type="number" class="vendedor" id="monSinIgv" name="monSinIgv" required step="0.01" min="0.00" style="text-align: right;" value="<?php echo $monSinIgv; ?>">
      </div>
      <div class="date-selector" style="margin-bottom: 10px;">
        <label for="monConIgv">Monto con IGV:</label>
        <input type="number" class="vendedor" step="0.01" id="monConIgv" name="monConIgv" style="text-align: right;" value="<?php echo $monConIgv; ?>">
      </div>
    </div>
  </div>
       <div class="cabecera">
            <div>
                <button type="button" class="cancelar-button" id="cancelarGasto">Cancelar Operación</button>
            </div>
            <div>
                <button type="submit" class="aceptar-button" name="guardarGasto" id="aceptarOrdenx">Aceptar Operación</button>
            </div>   
       </div>
       </form>
       <script>
            document.getElementById('cancelarGasto').addEventListener('click', function() {
              // Mostrar un mensaje de confirmación
              const confirmacion = confirm('¿Seguro que desea cancelar la actualización del gasto?');
              
              // Si el usuario confirma, redirigir a la página de la lista de alquileres
              if (confirmacion) {
                window.location.href = "<?php echo admin_url('admin.php?page=lasal-gastos'); ?>";
              }
            });
  
  
                // Obtener elementos del DOM
              const monSinIgvInput = document.getElementById('monSinIgv');
              const monConIgvInput = document.getElementById('monConIgv');
  
              // Función para calcular y actualizar el monto con IGV
              function actualizarMontoConIgv() {
                  const monSinIgv = parseFloat(monSinIgvInput.value) || 0;
                  // Calcular el monto con IGV
                  const montoConIgv = monSinIgv * 1.18;
                  // Mostrar el resultado en el campo correspondiente
                  monConIgvInput.value = montoConIgv.toFixed(2);
              }
  
              // Escuchar el evento 'input' en monSinIgvInput
              monSinIgvInput.addEventListener('input', actualizarMontoConIgv);
              
              // Calcular y mostrar el monto con IGV inicialmente
              actualizarMontoConIgv();
        </script>
  
  
    </div>
  
    </body>
    </html>
    <?php
    if (isset($_POST['guardarGasto'])) {
      // Obtener los datos enviados por el formulario de abono
      $fechaGasto = $_POST['fechaOperacion'];
      $descGasto = $_POST['descGasto'];
      $razonSocial = $_POST['razonSocial'];
      $monSinIgv = $_POST['monSinIgv'];
      $monConIgv = $_POST['monConIgv']; 
      $observacion = $_POST['observacion'];
      $codGasto = $_POST['codGasto'];
      $nombVendedor = $nombreVendedor;
      $codVendedor = $userID;
    
    
        // Preparar y ejecutar la consulta de inserción en la tabla pago
        //$sqlGasto = "INSERT INTO gasto (fechaGasto, descGasto, razonSocial, monSinIgv, monConIgv, observacion, nombVendedor, codVendedor) VALUES (
            $sqlGasto = "UPDATE gasto SET descGasto='$descGasto', razonSocial='$razonSocial', monSinIgv='$monSinIgv', monConIgv='$monConIgv', observacion='$observacion', fechaGasto='$fechaGasto' WHERE codGasto='$codGasto';";

        
    
        if ($conn->query($sqlGasto) === TRUE) {
          redireccionar_a_lista_gastos();
          exit(); // Asegura que no se envíen más datos después de la redirección
  
        } else {
            echo "Error al registrar el gasto: " . $conn->error;
        }
    
        // Cerrar la conexión
        $conn->close();
    
    }
  

}

//------------------------------------------------------ Clientes----------------------------------------------------
function lasal_admin_menu_clientes()
{
    require_once plugin_dir_path( __FILE__ ) . 'controlador/controladorCliente.php';
    require_once(plugin_dir_path(__FILE__) . '../../../wp-load.php');
    global $current_user;
    $nombreVendedor = $current_user->user_login;
    ?>
    <!DOCTYPE html>
    <html>
    <head>
      <title>Lista de Clientes</title>
      <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/cliente.css', __FILE__); ?>">
      <script src="<?php echo plugins_url('/js/cliente.js', __FILE__); ?>"></script>
    </head>
    <body>
      <div class="container">
        <div class="cabecera">
              <!-- Barra de búsqueda -->
              <div class="titulo">
                  <label for="cliente">Gestionar Clientes</label>
              </div>
              <div class="date-selector">
                  <label for="nroPedido">Vendedor:</label>
                  <input type="text" class="vendedor" value=<?php echo $nombreVendedor; ?> id="nomVendedor" style="height: 10px; margin-top: 9px;" readonly>
              </div>
        </div>
        <div class="search-container">
          <div class="date-selector">
              <button id="add-button" class="add-button">Agregar clientes</button>
          </div>
          <div class="date-selector">
              <label for="nroPedido">Buscar cliente:</label>
              <input type="text" class="search-input" placeholder="Ingresa el nombre, DNI/RUC" style="width: 300px; height: 10px; margin-top: 9px;">
          </div>
        </div>
        <br>
        <br>
        <div class="tabla-scroll">
        <table>
          <thead>
            <tr>
              <th>Nro</th>
              <th>Nombre</th>
              <th>DNI/RUC</th>
              <th>Correo electrónico</th>
              <th>Teléfono</th>
              <th>Dirección</th>
              <th>Acciones</th>
            </tr>
          </thead>       
          <tbody>
            <?php foreach ($clientes as $cliente): ?>
              <tr>
                <td><?php echo $cliente['cliId']; ?></td>
                <td><?php echo $cliente['cliNombre']; ?></td>
                <td><?php echo $cliente['cliDni']; ?></td>
                <td><?php echo $cliente['cliCorreo']; ?></td>
                <td><?php echo $cliente['cliTelefono']; ?></td>
                <td><?php echo $cliente['cliDireccion']; ?></td>
                <td>
                  <div class="actions-container">
                    <button class="edit-button" data-cliente-id="<?php echo $cliente['cliId']; ?>">Editar</button>
                    <form method="POST" style="display: inline-block;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este cliente?');">
                      <input type="hidden" name="eliminarCliente">
                      <input type="hidden" name="clienteId" value="<?php echo $cliente['cliId']; ?>">
                      <button type="submit" class="delete-button">Eliminar</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        </div>
    
        <!-- Modal para agregar/editar cliente -->
        <div id="modal-overlay" class="modal-overlay">
          <div id="modal" class="modal">
            <h2 id="modal-title">Agregar cliente</h2>
            <form id="cliente-form" method="POST">
              <input type="hidden" id="cliente-id" name="clienteId">
              <label for="nombre">Nombre:</label>
              <input type="text" id="nombre" name="nombre" value="" required pattern="[A-Za-zÁÉÍÓÚáéíóú\s]+" title="Solo puede ingresar letras">

              <label for="dni">DNI/RUC:</label>
              <input type="text" id="dni" name="dni" value="" required pattern="\d+" title="Solo puede ingresar números">

              <label for="email">Correo electrónico:</label>
              <input type="text" id="email" name="email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" title="Ingresa una dirección de correo válida">

              <label for="telefono">Teléfono:</label>
              <input type="text" id="telefono" name="telefono" value="" >

              <label for="direccion">Dirección:</label>
              <input type="text" id="direccion" name="direccion" value="" required>

              <div class="buttons">
                <button type="submit" name="guardarCliente">Guardar cliente</button>
                <button id="modal-close" type="button" class="close-button">Cerrar</button>
              </div>
            </form>
          </div>
        </div>



      </div>
    </body>
    </html>
<?php
}

//------------------------------------------------------ Reportes----------------------------------------------------
function lasal_admin_menu_reportes()
{
  redireccionar_a_reporte_ventas_fecha();
}

function lasal_reportes_ventas_cliente()
{
    // Aquí cargamos la lógica del controlador y la variable $datosFiltrados
    require_once(plugin_dir_path(__FILE__) . '../../../wp-load.php');
    require_once plugin_dir_path(__FILE__) . '/modelo/conexion.php';
    global $current_user;
    $nombreVendedor = $current_user->user_login;

    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Lista de Ventas de Productos</title>
        <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/venta_cliente.css', __FILE__); ?>">
        <script src="<?php echo plugins_url('/js/venta_cliente.js', __FILE__); ?>"></script>
    </head>
    <body>
        <div class="container">
            <div class="cabecera">
              <!-- Barra de búsqueda -->
              <div class="titulo">
                  <label for="cliente">Ventas por Cliente</label>
              </div>
              <div class="date-selector">
                  <label for="nroPedido">Vendedor:</label>
                  <input type="text" class="vendedor" id="nomVendedor" value="<?php echo $nombreVendedor; ?>" readonly>
            </div>
        </div>
        <div class="search-container">
            <!-- Selectores de fecha -->
            <div class="date-selector">
                <label for="fechaInicio">Desde:</label>
                <input type="date" id="fechaInicio">
            </div>
            <div class="date-selector">
                <label for="fechaFin">Hasta:</label>
                <input type="date" id="fechaFin">
            </div>
            <!-- Barra de búsqueda -->
            <input type="text" class="search-input" placeholder="Buscar por nombre o DNI " style="width: 300px;">
            <button type="button" class="add-button" id="exportarBtn">Exportar a CSV</button>
        </div>
        <br>
        <br>
        <div class="tabla-scroll">
        <table id="tablaAlquileres">
            <thead>
            <tr>
                <th>Nro Orden</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>DNI</th>
                <th>Total</th>
                <th>Pagado</th>
                <th>Penalidades</th>
                <th>Rechazado</th>
                <th>Deuda Total</th>
            </tr>
            </thead>
            <tbody>
            <?php
              // Ejecutar el segundo query para obtener los datos
              $sql = "SELECT a.codAlquiler, a.fecEvento, a.nombCliente, c.cliDni, a.monTotal, a.pagado, a.penalidad, a.rechazado, round((a.monTotal + a.penalidad - a.rechazado - a.pagado),2) AS deudaTotal
              FROM alquiler a INNER JOIN cliente c WHERE a.codCliente = c.cliId;";
              $result = mysqli_query($conn, $sql);

              // Verificar si hay resultados y mostrar los datos en la tabla
              if ($result && mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                      echo "<tr>";
                      echo "<td>" . $row['codAlquiler'] . "</td>";
                      echo "<td>" . $row['fecEvento'] . "</td>";
                      echo "<td>" . $row['nombCliente'] . "</td>";
                      echo "<td>" . $row['cliDni'] . "</td>";
                      echo "<td>" . number_format($row['monTotal'],2) . "</td>";
                      echo "<td>" . number_format($row['pagado'],2) . "</td>";
                      echo "<td>" . number_format($row['penalidad'],2) . "</td>";
                      echo "<td>" . number_format($row['rechazado'],2) . "</td>";
                      echo "<td>" . number_format($row['deudaTotal'],2) . "</td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='7'>No se encontraron datos</td></tr>";
              }
              
              // Cerrar la conexión a la base de datos
              mysqli_close($conn);
            ?>
            </tbody>
        </table>
          </div>
    </div>
    </body>
    </html>
    <?php
}

function lasal_reportes_ventas_usuario()
{
    // Aquí cargamos la lógica del controlador y la variable $datosFiltrados
    require_once(plugin_dir_path(__FILE__) . '../../../wp-load.php');
    require_once plugin_dir_path(__FILE__) . '/modelo/conexion.php';
    global $current_user;
    $nombreVendedor = $current_user->user_login;

    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Lista de Ventas de Productos</title>
        <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/venta_usuario.css', __FILE__); ?>">
        <script src="<?php echo plugins_url('/js/venta_usuario.js', __FILE__); ?>"></script>
    </head>
    <body>
        <div class="container">
            <div class="cabecera">
              <!-- Barra de búsqueda -->
              <div class="titulo">
                  <label for="cliente">Ventas por Usuario</label>
              </div>
              <div class="date-selector">
                  <label for="nroPedido">Vendedor:</label>
                  <input type="text" class="vendedor" id="nomVendedor" value="<?php echo $nombreVendedor; ?>" readonly>
            </div>
        </div>
        <div class="search-container">
            <!-- Selectores de fecha -->
            <div class="date-selector">
                <label for="fechaInicio">Desde:</label>
                <input type="date" id="fechaInicio">
            </div>
            <div class="date-selector">
                <label for="fechaFin">Hasta:</label>
                <input type="date" id="fechaFin">
            </div>
            <!-- Selectores de usuarios -->
            <div class="date-selector">
                <label for="usuario">Usuario:</label>
                <select id="usuario">
                    <option value="Todas">Todas</option>
                    <option value="Cecilia">Cecilia</option>
                    <option value="Mili">Mili</option>
                    <option value="Alvaro">Alvaro</option>
                </select>
            </div>
            <button type="button" class="add-button" id="exportarBtn">Exportar a CSV</button>
        </div>
        <br>
        <br>
        <div class="tabla-scroll">
        <table id="tablaAlquileres">
            <thead>
            <tr>
                <th>Nro Orden</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Saldo</th>
                <th>Usuario</th>
            </tr>
            </thead>
            <tbody>
            <?php
              // Ejecutar el segundo query para obtener los datos
              $sql = "SELECT codAlquiler, fecEvento, nombCliente, round((monTotal + penalidad - rechazado),2) AS total, round((monTotal + penalidad - rechazado - pagado),2) 
                      AS saldo, nombVendedor FROM alquiler;";
              $result = mysqli_query($conn, $sql);

              // Verificar si hay resultados y mostrar los datos en la tabla
              if ($result && mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                      echo "<tr>";
                      echo "<td>" . $row['codAlquiler'] . "</td>";
                      echo "<td>" . $row['fecEvento'] . "</td>";
                      echo "<td>" . $row['nombCliente'] . "</td>";
                      echo "<td>" . number_format($row['total'],2) . "</td>";
                      echo "<td>" . number_format($row['saldo'],2) . "</td>";
                      echo "<td>" . $row['nombVendedor'] . "</td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='7'>No se encontraron datos</td></tr>";
              }
              
              // Cerrar la conexión a la base de datos
              mysqli_close($conn);
            ?>
            </tbody>
        </table>
          </div>
    </div>
    </body>
    </html>
    <?php
}

function lasal_reportes_stock_producto()
{
  // Aquí cargamos la lógica del controlador y la variable $datosFiltrados
  require_once(plugin_dir_path(__FILE__) . '../../../wp-load.php');
  require_once plugin_dir_path(__FILE__) . '/modelo/conexion.php';
  global $current_user;
  $nombreVendedor = $current_user->user_login;

  ?>
  <!DOCTYPE html>
  <html>
  <head>
      <title>Lista de Ventas por Fecha</title>
      <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/stock_producto.css', __FILE__); ?>">
      <script src="<?php echo plugins_url('/js/stock_producto.js', __FILE__); ?>"></script>
  </head>
  <body>
  <div class="container">
  <div class="container">
      <div class="cabecera">
              <!-- Barra de búsqueda -->
            <div class="titulo">
                  <label for="cliente">Stock por Producto</label>
            </div>
            <div class="date-selector">
                  <label for="nroPedido">Vendedor:</label>
                  <input type="text" class="vendedor" id="nomVendedor" value="<?php echo $nombreVendedor; ?>" readonly>
            </div>
      </div>
      <div class="search-container">
          <!-- Selectores de fecha -->
          <div class="date-selector">
              <label for="categoria">Categoría:</label>
              <select id="categoria">
                  <option value="Todas">Todas</option>
                  <option value="Decoración">Decoración</option>
                  <option value="Mantelería">Mantelería</option>
                  <option value="Mobiliario">Mobiliario</option>
              </select>
          </div>
          <!-- Barra de búsqueda -->
          <input type="text" class="search-input" placeholder="Buscar por nombre o ID Producto" style="width: 300px;">
          <button type="button" class="add-button" id="exportarBtn">Exportar a CSV</button>
      </div>
      <br>
      <br>
      <div class="tabla-scroll">
      <table id="tablaProductos">
          <thead>
              <tr>
                  <th>ID Producto</th>
                  <th>Nombre</th>
                  <th>Categoría</th>
                  <th>Imágen</th>
                  <th>Precio</th>
                  <th>Precio Reposición</th>
                  <th>Stock</th>
              </tr>
          </thead>
          <tbody>
              <?php
              // Ejecutar el segundo query para obtener los datos
              $sql = "SELECT ID, nomproducto, categoria, rutaimagen, precioalquiler, precioreposicion, cantstock FROM kleon_wp_lasaldelavida.v_producto";
              $result = mysqli_query($conn, $sql);

              // Verificar si hay resultados y mostrar los datos en la tabla
              if ($result && mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                      echo "<tr>";
                      echo "<td>" . $row['ID'] . "</td>";
                      echo "<td>" . $row['nomproducto'] . "</td>";
                      echo "<td>" . $row['categoria'] . "</td>";
                      echo "<td><img src='" . $row['rutaimagen'] . "' alt='Imagen' width='100'></td>";
                      echo "<td>" . number_format($row['precioalquiler'],2) . "</td>";
                      echo "<td>" . number_format($row['precioreposicion'],2) . "</td>";
                      echo "<td>" . $row['cantstock'] . "</td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='7'>No se encontraron datos</td></tr>";
              }
              
              // Cerrar la conexión a la base de datos
              mysqli_close($conn);
              ?>
          </tbody>
      </table>
    </div>
  </div>
  </body>
  </html>
  <?php   
}

function lasal_reportes_ventas_producto()
{
    // Aquí cargamos la lógica del controlador y la variable $datosFiltrados
    require_once plugin_dir_path( __FILE__ ) . 'controlador/controladorReporteVentaProducto.php';
    require_once(plugin_dir_path(__FILE__) . '../../../wp-load.php');
    global $current_user;
    $nombreVendedor = $current_user->user_login;
    $datosFiltrados = obtenerTodosDatos($conn); // Pasamos $conn como parámetro a la función

    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Lista de Ventas de Productos</title>
        <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/venta_producto.css', __FILE__); ?>">
        <script src="<?php echo plugins_url('/js/venta_producto.js', __FILE__); ?>"></script>
    </head>
    <body>
        <div class="container">
            <div class="cabecera">
              <!-- Barra de búsqueda -->
              <div class="titulo">
                  <label for="cliente">Ventas por Producto</label>
              </div>
              <div class="date-selector">
                  <label for="nroPedido">Vendedor:</label>
                  <input type="text" class="vendedor" id="nomVendedor" value="<?php echo $nombreVendedor; ?>" readonly>
            </div>
        </div>
        <div class="search-container">
            <!-- Selectores de fecha -->
            <div class="date-selector">
                <label for="fechaInicio">Desde:</label>
                <input type="date" id="fechaInicio">
            </div>
            <div class="date-selector">
                <label for="fechaFin">Hasta:</label>
                <input type="date" id="fechaFin">
            </div>
            <!-- Barra de búsqueda -->
            <input type="text" class="search-input" placeholder="Buscar por nombre o ID Producto" style="width: 300px;">
            <button type="button" class="add-button" id="exportarBtn">Exportar a CSV</button>
        </div>
        <br>
        <br>
        <div class="tabla-scroll">
        <table id="tablaProductos">
            <thead>
            <tr>
                <th>ID Producto</th>
                <th>Fecha</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Imagen</th>
                <th>Orden Alquiler</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Subtotal</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($datosFiltrados)) {
                foreach ($datosFiltrados as $row) {
                    echo "<tr>";
                    echo "<td>" . $row[0] . "</td>";
                    echo "<td>" . $row[1] . "</td>";
                    echo "<td>" . $row[2] . "</td>";
                    echo "<td>" . $row[3] . "</td>";
                    echo "<td><img src='" . $row[4] . "' alt='Imagen' width='100'></td>";
                    echo "<td>" . $row[5] . "</td>";
                    echo "<td>" . $row[6] . "</td>";
                    echo "<td>" . number_format($row[7],2) . "</td>";
                    echo "<td>" . number_format($row[8],2) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No hay datos disponibles.</td></tr>";
            }
            ?>
            </tbody>
        </table>
          </div>
    </div>
    </body>
    </html>
    <?php
}

function lasal_reportes_ventas_fecha()
{
  // Aquí cargamos la lógica del controlador y la variable $datosFiltrados
  require_once plugin_dir_path( __FILE__ ) . 'controlador/controladorReporteVentaFecha.php';
  require_once(plugin_dir_path(__FILE__) . '../../../wp-load.php');
  global $current_user;
  $nombreVendedor = $current_user->user_login;
  $datosFiltrados = obtenerTodosDatos($conn); // Pasamos $conn como parámetro a la función

  ?>
  <!DOCTYPE html>
  <html>
  <head>
      <title>Lista de Ventas por Fecha</title>
      <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/venta_producto.css', __FILE__); ?>">
      <script src="<?php echo plugins_url('/js/venta_fecha.js', __FILE__); ?>"></script>
  </head>
  <body>
  <div class="container">
  <div class="container">
      <div class="cabecera">
              <!-- Barra de búsqueda -->
            <div class="titulo">
                  <label for="cliente">Ventas por Fecha</label>
            </div>
            <div class="date-selector">
                  <label for="nroPedido">Vendedor:</label>
                  <input type="text" class="vendedor" id="nomVendedor" value="<?php echo $nombreVendedor; ?>" readonly>
            </div>
      </div>
      <div class="search-container">
          <!-- Selectores de fecha -->
          <div class="date-selector">
              <label for="fechaInicio">Desde:</label>
              <input type="date" id="fechaInicio">
          </div>
          <div class="date-selector">
              <label for="fechaFin">Hasta:</label>
              <input type="date" id="fechaFin">
          </div>
          <button type="button" class="add-button" id="exportarBtn">Exportar a CSV</button>
      </div>
      <br>
      <br>
      <div class="tabla-scroll">
      <table id="tablaAlquileres">
          <thead>
          <tr>
              <th>ID Orden Alquiler</th>
              <th>Fecha</th>
              <th>Cliente</th>
              <th>Movilidad</th>
              <th>Descuento</th>
              <th>Monto</th>
              <th>Pagado</th>
              <th>Penalidad</th>
              <th>Rechazado</th>
              <th>Deuda Total</th>
          </tr>
          </thead>
          <tbody>
          <?php
          if (!empty($datosFiltrados)) {
              foreach ($datosFiltrados as $row) {
                  echo "<tr>";
                  echo "<td>" . $row[0] . "</td>";
                  echo "<td>" . $row[1] . "</td>";
                  echo "<td>" . $row[2] . "</td>";
                  echo "<td>" . number_format($row[3],2) . "</td>";
                  echo "<td>" . number_format($row[4],2) . "</td>";
                  echo "<td>" . number_format($row[5],2) . "</td>";
                  echo "<td>" . number_format($row[6],2) . "</td>";
                  echo "<td>" . number_format($row[7],2) . "</td>";
                  echo "<td>" . number_format($row[8],2) . "</td>";
                  echo "<td>" . number_format($row[9],2) . "</td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='10'>No hay datos disponibles.</td></tr>";
          }
          ?>
          </tbody>
      </table>
      </div>
  </div>
  </body>
  </html>
  <?php
}

function lasal_reportes_ventas_2023_sistema_antiguo() {
    // URL del archivo Excel
    $excel_url = 'https://lasaldelavida.pe/wp-content/uploads/2024/02/Ventas-2023-Sistema-Antiguo.xlsx';

    // Redirigir al usuario
    wp_redirect($excel_url);
    exit; // Asegúrate de salir después de redirigir para evitar problemas adicionales
}

function lasal_reportes_gastos() {
    // Aquí cargamos la lógica del controlador y la variable $datosFiltrados
    require_once(plugin_dir_path(__FILE__) . '../../../wp-load.php');
    require_once plugin_dir_path(__FILE__) . '/modelo/conexion.php';
    global $current_user;
    $nombreVendedor = $current_user->user_login;

    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Lista de Gastos</title>
        <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/venta_cliente.css', __FILE__); ?>">
        <script src="<?php echo plugins_url('/js/reporte_gasto.js', __FILE__); ?>"></script>
    </head>
    <body>
        <div class="container">
            <div class="cabecera">
              <!-- Barra de búsqueda -->
              <div class="titulo">
                  <label for="cliente">Gastos La Sal de la Vida</label>
              </div>
              <div class="date-selector">
                  <label for="nroPedido">Vendedor:</label>
                  <input type="text" class="vendedor" id="nomVendedor" value="<?php echo $nombreVendedor; ?>" readonly>
            </div>
        </div>
        <div class="search-container">
            <!-- Selectores de fecha -->
            <div class="date-selector">
                <label for="fechaInicio">Desde:</label>
                <input type="date" id="fechaInicio">
            </div>
            <div class="date-selector">
                <label for="fechaFin">Hasta:</label>
                <input type="date" id="fechaFin">
            </div>
            <!-- Barra de búsqueda -->
            <input type="text" class="search-input" placeholder="Buscar por razón social " style="width: 300px;">
            <button type="button" class="add-button" id="exportarBtn">Exportar a CSV</button>
        </div>
        <br>
        <br>
        <div class="tabla-scroll">
        <table id="tablaAlquileres">
            <thead>
            <tr>
                <th>Nro</th>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Razón Social</th>
                <th>Monto sin IGV</th>
                <th>Monto con IGV</th>
                <th>Observación</th>
            </tr>
            </thead>
            <tbody>
            <?php
              // Ejecutar el segundo query para obtener los datos
              $sql = "SELECT codGasto, fechaGasto, descGasto, razonSocial, monSinIgv, monConIgv, observacion FROM gasto;";
              $result = mysqli_query($conn, $sql);

              // Verificar si hay resultados y mostrar los datos en la tabla
              if ($result && mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                      echo "<tr>";
                      echo "<td>" . $row['codGasto'] . "</td>";
                      echo "<td>" . $row['fechaGasto'] . "</td>";
                      echo "<td>" . $row['descGasto'] . "</td>";
                      echo "<td>" . $row['razonSocial'] . "</td>";
                      echo "<td>" . number_format($row['monSinIgv'],2) . "</td>";
                      echo "<td>" . number_format($row['monConIgv'],2) . "</td>";
                      echo "<td>" . $row['observacion'] . "</td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='7'>No se encontraron datos</td></tr>";
              }
              
              // Cerrar la conexión a la base de datos
              mysqli_close($conn);
            ?>
            </tbody>
        </table>
          </div>
    </div>
    </body>
    </html>
    <?php 
}

//------------------------------------------------------ Alquileres----------------------------------------------------
function lasal_admin_menu_alquileres()
{
  require_once plugin_dir_path(__FILE__) . '/modelo/conexion.php';
  require_once plugin_dir_path( __FILE__ ) . 'controlador/controladorAlquiler.php';
  require_once(plugin_dir_path(__FILE__) . '../../../wp-load.php');

  global $current_user;
  $nombreVendedor = $current_user->user_login;
  $userID = $current_user->ID;
    ?>
    <!DOCTYPE html>
    <html>
    <head>
      <title>Lista de Clientes</title>
      <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/alquiler.css', __FILE__); ?>"> 
      <script src="<?php echo plugins_url('/js/alquiler.js', __FILE__); ?>"></script>
      <script src="<?php echo plugins_url('/js/filtros_alquiler.js', __FILE__); ?>"></script>
      <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    </head>
    <body>
      <div class="container">
       <div class="cabecera">
              <!-- Barra de búsqueda -->
              <div class="titulo">
                  <label for="cliente">Todos los Alquileres</label>
              </div>
              <div class="date-selector">
                  <label for="nroPedido">Vendedor:</label>
                  <input type="text" class="search-input" id="nomVendedor" value="<?php echo $nombreVendedor; ?>" style="height: 10px; margin-top: 9px;" readonly>
              </div>
       </div> 
       <div class="search-container">
          <div class="date-selector">
              <form method="post" action="<?php echo admin_url('admin.php?page=lasal-alquileres-nueva-orden'); ?>">
                  <button type="submit" class="add-button">Nueva Orden</button>
              </form>
          </div>
          <!-- Buscar cliente -->
          <div class="date-selector">
              <!-- Selectores de fecha -->
              <label for="fechaInicio">Desde:</label>
              <input type="date" id="fechaInicio">
          </div>
          <div class="date-selector">
              <label for="fechaFin">Hasta:</label>
              <input type="date" id="fechaFin">
          </div>  
          <div class="date-selector">
              <label for="filtroUsuario">Usuario:</label>
              <select id="filtroUsuario" class="box-input">
                  <option value="">Todos los usuarios</option>                 
                  <option value="Cecilia">Cecilia</option>
                  <option value="Mili">Mili</option>
                  <option value="Alvaro">Alvaro</option>
              </select>
          </div>  
          <div class="date-selector">
              <!-- Combo box para filtrar por estado de deuda -->
              <label for="filtroDeuda">Deuda:</label>
              <select id="filtroDeuda" class="box-input">
                  <option value="">Todos</option>
                  <option value="Con deuda">Con deuda</option>
                  <option value="Sin deuda">Sin deuda</option>
                  <option value="Con saldo a favor">Con saldo a favor</option>
              </select>
          </div> 
          <div class="date-selector">
              <!-- Combo box para filtrar por estado de la orden -->
              <label for="filtroEstadoOrden">Estado:</label>
              <select id="filtroEstadoOrden" class="box-input">
                  <option value="">Todos los estados</option>
                  <!-- <option value="En revision">En revision</option> -->
                  <option value="Pendiente de entrega">Pendiente de entrega</option>
                  <option value="Pendiente de devolucion">Pendiente de devolucion</option>
                  <option value="Devuelto">Devuelto</option>
                  <option value="Finalizado">Finalizado</option>
              </select>
          </div>
          <div class="date-selector">
              <!-- Barra de búsqueda -->
              <input type="text" class="search-input" id="buscarAlquiler" placeholder="Buscar por Cliente/Orden" style="height: 10px; margin-top: 15px;">
          </div>
     </div>
     <br>
     <br>
      <div class="tabla-scroll">
        <table id="tablaAlquileres">
          <thead>
            <tr>
              <th>Nro</th>
              <th>Fecha Operación</th>
              <th>Fecha Evento</th>
              <th>Cliente</th>
              <th>Monto Total</th>
              <th>Pagado</th>
              <th>Penalidad</th>
              <th>Rechazado</th>
              <th>Deuda Total</th>
              <th>Vendido por</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($alquileres as $alquiler): ?>
              <tr>
              <td class='numero-alquiler'><?php echo $alquiler['codAlquiler']; ?></td>
              <td><?php echo $alquiler['fecOperacion']; ?></td>
              <td><?php echo $alquiler['fecEvento']; ?></td>
              <td class='nombre-cliente'><?php echo $alquiler['nombCliente']; ?></td>
              <td><?php echo $alquiler['monTotal']; ?></td>
              <td><?php echo $alquiler['pagado']; ?></td>
              <td><?php echo $alquiler['penalidad']; ?></td>
              <td><?php echo $alquiler['rechazado']; ?></td>
              <td <?php
                  $deudaTotal = $alquiler['monTotal'] + (isset($alquiler['penalidad']) ? $alquiler['penalidad'] : 0.00)
                                - (isset($alquiler['pagado']) ? $alquiler['pagado'] : 0.00)
                                - (isset($alquiler['rechazado']) ? $alquiler['rechazado'] : 0.00);
                  if ($deudaTotal > 0.00) {
                      echo 'style="color: red;"';
                  } else if ($deudaTotal < 0.00) {
                      echo 'style="color: green;"';
                  }
                  ?>><?php echo number_format($deudaTotal, 2); ?></td>
              <td><?php echo $alquiler['nombVendedor']; ?></td>
              <td><?php echo $alquiler['estOrden']; ?></td>
                <td>
                  <div class="actions-container">
                    <button class="edit-button btn-pagos" data-cliente-nombre="<?php echo $alquiler['nombCliente']; ?>" data-alquiler-id="<?php echo $alquiler['codAlquiler']; ?>" style="background-color: #84b6f4; border: 1px solid #95b8f6; border-radius: 5px; " title="Pagos">
                     <i class='bx bx-dollar' title="Pagos"></i>
                    </button>

                    <button class="edit-button btn-abonos" data-alquiler-id="<?php echo $alquiler['codAlquiler']; ?>" style="background-color: #c5c6c8; border: 1px solid #9b9b9b; border-radius: 5px;" title="Abonar">
                     <i class='bx bx-plus-medical' title="Abonar"></i>
                    </button>

                    <form method="POST" action="<?php echo admin_url('admin.php?page=lasal-alquileres-editar-orden'); ?>" style="display: inline-block;";>
                      <input type="hidden" name="nroAlquiler" value="<?php echo $alquiler['codAlquiler']; ?>">
                      <button type="submit" class="edit-button" style="background-color: #FFC300; border: 1px solid #FFC300; border-radius: 5px;" title="Editar">
                      <i class='bx bxs-edit-alt' ></i>
                      </button>
                    </form>
                     
                    <form method="POST" action="<?php echo admin_url('admin.php?page=lasal-alquileres-devolucion&nroAlquiler=' . $alquiler['codAlquiler']); ?>" style="display: inline-block;">
                     <input type="hidden" name="nroAlquiler" value="<?php echo $alquiler['codAlquiler']; ?>">
                     <button type="submit" class="edit-button" style="background-color: #d3bcf6; border: 1px solid #b186f1; border-radius: 5px;" title="Devolución">
                     <i class='bx bxs-truck' title="Devolución"></i>
                     </button>
                    </form>

                    <?php $pdf_action_url = plugins_url('/cotizacion_pdf/cotizacion.php', __FILE__); ?>
                    <form method="POST" action="<?php echo $pdf_action_url; ?>" target="_blank" style="display: inline-block;">    
                      <input type="hidden" name="nroAlquiler" value="<?php echo $alquiler['codAlquiler']; ?>">                
                      <button type="submit" class="edit-button" style="background-color: #d8af97; border: 1px solid #a68069; border-radius: 5px;" title="Imprimir cotización">
                      <i class='bx bxs-printer' title="Imprimir cotización"></i>
                      </button>
                    </form>
                    
                    <?php $pdf_action_url = plugins_url('/cotizacion_pdf/guia.php', __FILE__); ?>
                    <form method="POST" action="<?php echo $pdf_action_url; ?>" target="_blank" style="display: inline-block;">    
                      <input type="hidden" name="nroAlquiler" value="<?php echo $alquiler['codAlquiler']; ?>">                
                      <button type="submit" class="edit-button" style="background-color: #77dd77; border: 1px solid #b0f2c2; border-radius: 5px;" title="Guia interna de remisión">
                      <i class='bx bxs-file-pdf' title="Guia interna de remisión"></i>
                      </button>
                    </form>

                    <?php $eliminar_action_url = plugins_url('/eliminar_alquiler.php', __FILE__); ?>
                    <form method="POST" action="<?php echo $eliminar_action_url; ?>" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta orden de alquiler?');" style="display: inline-block;">
                      <input type="hidden" name="nroAlquiler" value="<?php echo $alquiler['codAlquiler']; ?>">
                      <button type="submit" class="delete-button" style="background-color: #ff6565; border: 1px solid #d1052a; border-radius: 5px;" title="Eliminar">
                      <i class='bx bxs-trash' title="Eliminar"></i>
                      </button>
                    </form>

                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>






<!-- Modal para ver el registro histórico de abonos -->
<div id="modal-overlay-pagos" class="modal-overlay">
  <div id="modal-pagos" class="modal">
    <div class="modal-title-container">
    <h2 id="modal-title">Pagos del pedido Nro:</h2>
    <input type="number" id="nroOrdenAlquiler" name="nroOrdenAlquiler" readonly style="width: 100px; font-weight: bold;">
    </div>
    <div class="modal-title-container">
    <h2 id="modal-title">Cliente:</h2>
    <input type="text" id="nombreCliente" name="nombreCliente" readonly style="width: 300px; height: 10px; margin-top: 9px; font-weight: bold;">
    </div>
    <table id="historico-abonos">


    </table>
    <!-- Contenedor del lado derecho -->
    <div class="montos" style="flex: 1;">
    
    <br>
    <br>
    <div class="date-selector" style="margin-bottom: 10px;">
      <label for="nroPedido">Importe total de la operación:</label>
      <input type="number" class="vendedor" id="importeTotal" style="text-align: right;" readonly>
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <label for="nroPedido">Total pagado:</label>
      <input type="number" class="vendedor" id="totalPagado" style="text-align: right;" readonly>
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <label for="nroPedido">Importe restante:</label>
      <input type="number" class="vendedor" id="importeRestante" style="text-align: right;" readonly>
    </div>
  </div>
    <div class="buttons">
      <button id="cerrar-modal-pagos" type="button" class="close-button">Cerrar</button>
    </div>
  </div>
</div>













<!-- Modal para abonar pedido -->
<div id="modal-overlay-abonos" class="modal-overlay">
  <div id="modal-abonos" class="modal-abono">
    <form id="abono-form" method="POST" action=" " onsubmit="return confirm('¿Estás seguro de registrar el pago?');">
    <div class="modal-title-container">
    <h2 id="modal-title">Abonar pedido Nro:</h2>
    <input type="number" id="nroOrden" name="nroAlquiler" readonly style="width: 100px; font-weight: bold;">
    </div>
      <label for="fecha">Fecha:</label>
      <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>">
      <br>
      <br>
      <label for="fecha">Sustento de pago:</label>
      <input type="text" id="infoPago" name="infoPago">
      <label for="importe">Importe:</label>
      <input type="number" id="importe" name="importe" step="0.01" style="text-align: right;" required>
      <br>
      <br>
      <div class="payment-container">
        <label for="medioPago">Pago con:</label>
        <select id="medioPago" name="medioPago">
          <option value="efectivo">Efectivo</option>
          <option value="debito">Débito</option>
          <option value="credito">Crédito</option>
          <option value="transferencia">Transferencia</option>
          <option value="plin">Plin</option>
          <option value="yape">Yape</option>
        </select>

        <div class="codigoTransaccion" style="display: none;">
          <label for="codigoTransaccion" style="margin-left: 20px;">Código de transacción:</label>
          <input type="text" style="margin-left: 20px;" id="codigoTransaccion" name="codigoTransaccion">
        </div>
      </div>
      <br>
      <br>
      <div class="buttons">
        <button type="submit" name="guardarAbono">Guardar</button>
        <button id="cerrar-modal-abonos" type="button" class="close-button">Cerrar</button>
      </div>
    </form>
  </div>
</div>


<script>
  const medioPagoSelector = document.getElementById('medioPago');
  const codigoTransaccionContainer = document.querySelector('.codigoTransaccion');

  medioPagoSelector.addEventListener('change', () => {
    const selectedMedioPago = medioPagoSelector.value;
    if (selectedMedioPago === 'debito' || selectedMedioPago === 'credito' || selectedMedioPago === 'transferencia' || selectedMedioPago === 'plin' || selectedMedioPago === 'yape') {
      codigoTransaccionContainer.style.display = 'block';
    } else {
      codigoTransaccionContainer.style.display = 'none';
    }
  });
</script>







      </div>
    </body>
    </html>

    <?php

if (isset($_POST['guardarAbono'])) {
  // Obtener los datos enviados por el formulario de abono
  $fechaAbono = $_POST['fecha'];
  $sustentoPago = $_POST['infoPago'];
  $importeAbono = $_POST['importe'];
  $medioPago = $_POST['medioPago'];
  $codigoTransaccion = $_POST['codigoTransaccion']; // Si está presente en el formulario
  $nroOrden = $_POST['nroAlquiler']; // Convertir a número entero
  $idVendedor = $userID;
  $vendedor = $nombreVendedor;


    // Preparar y ejecutar la consulta de inserción en la tabla pago
    $sqlPago = "INSERT INTO pago (idPedido, pagoFecha, pagoImporte, medPago, codTransaccion, pagoSustento, idVendedor, nomVendedor) VALUES (
                '$nroOrden',
                '$fechaAbono',
                '$importeAbono',
                '$medioPago',
                '$codigoTransaccion',
                '$sustentoPago',
                '$idVendedor',
                '$vendedor'
            )";

    if ($conn->query($sqlPago) === TRUE) {
        //echo "Abono registrado exitosamente.";
        echo '<br>';
        $sqlPagoAlquiler = "UPDATE alquiler SET pagado = pagado + $importeAbono WHERE codAlquiler = $nroOrden";


        if ($conn->query($sqlPagoAlquiler) === TRUE) {
          //echo "El pago en el alquiler se registró correctamente.";
          // Redirige utilizando la función personalizada después de una inserción exitosa
          redireccionar_a_lista_alquileres();
          exit(); // Asegura que no se envíen más datos después de la redirección
        } else {
          echo "Error al registrar el pago en el alquiler: " . $conn->error;
        }

      
    } else {
        echo "Error al registrar el abono: " . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();

}



}



function lasal_alquileres_nueva_orden()
{
  require_once plugin_dir_path(__FILE__) . '/modelo/conexion.php';
  require_once(plugin_dir_path(__FILE__) . '../../../wp-load.php');
  global $current_user;
  $nombreVendedor = $current_user->user_login;
  $userID = $current_user->ID;

  ?>
  <!DOCTYPE html>
  <html>
  <head>
      <title>Lista de Ventas de Productos</title>
      <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/nueva_orden.css', __FILE__); ?>">
      <script src="<?php echo plugins_url('/js/nueva_orden.js', __FILE__); ?>"></script>
      <script src="<?php echo plugins_url('/js/prueba_ordenx.js', __FILE__); ?>"></script>
  </head>
  <body>
  <div class="container">
   <form id="formularioOrden" method="POST" action="">
    <div class="cabecera">
              <!-- Barra de búsqueda -->
              <div class="titulo">
                  <label for="cliente">Nuevo Alquiler</label>
              </div>
              <div class="date-selector">
                  <label for="nroPedido">Vendedor:</label>
                  <!-- Elemento input oculto para almacenar el ID del vendedor -->
                  <input type="hidden" id="vendedor-id" name="vendedor-id" value="<?php echo $userID; ?>">
                  <input type="text" class="vendedor" id="nomVendedor" name="vendedor" value="<?php echo $nombreVendedor; ?>" readonly>
              </div>
    </div> 
    <div class="search-container">
          <!-- Buscar cliente -->
          <div class="date-selector">
              <label for="cliente">Cliente:</label>
              <!-- Elemento input oculto para almacenar el ID del cliente -->
              <input type="hidden" id="cliente-id" name="cliente-id">
              <input type="text" class="search-cliente" name="codCliente" id="nomCliente" readonly>
              <button type="button" class="buscar-cliente" id="buscar-clientes">Buscar Cliente</button>
          </div>
          <div class="date-selector">
          <label for="estado">Estado de Orden:</label>
              <select id="estado" name="estado">
                  <!-- <option value="En revision">En revision</option> -->
                  <option value="Pendiente de entrega">Pendiente de entrega</option>
                  <option value="Pendiente de devolucion">Pendiente de devolucion</option>
                  <option value="Devuelto">Devuelto</option>
                  <option value="Finalizado">Finalizado</option>
              </select>
          </div>   
     </div>
     <div class="search-container">
          <!-- Selectores de fecha -->
          <div class="date-selector">
              <label for="fechaEvento">Fecha de Evento:</label>
              <input type="date" id="fechaEvento" name="fechaEvento" required>
          </div>
          <div class="date-selector">
              <label for="fechaOperacion">Fecha de Operación:</label>
              <input type="date" id="fechaOperacion" name="fechaOperacion" value="<?php echo date('Y-m-d'); ?>" readonly>
          </div>
     </div>
     <div class="search-container">
          <!-- Barra de búsqueda -->
          <div class="date-selector">
          <label for="direccion">Dirección:</label>
          <input type="text" class="direccion" id="direccion" placeholder="Dirección de envío" name="direccionEnvio" required>
          </div>
          <!-- Selectores de fecha -->
          <div class="date-selector">
              <label for="fechaEntregaProductos">Fecha entrega productos:</label>
              <input type="date" id="fechaEntregaProductos" name="fechaEntregaProductos" required>
          </div>
          <div class="date-selector">
              <label for="fechaDevolucionProductos">Fecha devolución productos:</label>
              <input type="date" id="fechaDevolucionProductos" name="fechaDevolucionProductos" required>
          </div>
     </div>

      <table id="tablaProductos">
          <thead>
          <br>
          <br>
          <div class="">
              <!-- Barra de búsqueda -->
              <div class="date-selector">
                  <label for="producto">Productos:</label>
                  <!-- Elemento input oculto para almacenar el ID del producto -->
                  <input type="hidden" id="producto-id" name="producto-id">
                  <button type="button" class="buscar-producto" id="buscar-productos">Buscar Producto</button>                  
              </div>
          </div> 
          <br>
          <tr>
              <th>Fecha</th>
              <th>Fecha entrega</th>
              <th>Producto</th>
              <th>Categoría</th>
              <th>Cantidad</th>
              <th>Stock interno</th>
              <th>Precio</th>
              <th>Acciones</th>
              <th>Estado</th>
              <th>Stock externo</th>
              <th>Empresa</th>
              <th>Subtotal</th>
          </tr>
          </thead>
          <tbody>


          </tbody>
      </table>
      <div>
              <div>
                  <br>
                  <button type="button" class="evaluar-button" id="evaluarDisponibilidad">Evaluar Disponibilidad</button>
              </div>
      </div>
      <br>
      
<div style="display: flex; flex-direction: row;">
  <!-- Contenedor del lado izquierdo -->
  <div class="search-container">
          <div class="date-selector">
          <label for="direccion">Observaciones:</label>
          </div>
      </div>
      <div class="search-container">
          <div class="date-selector">
          <textarea id="comentarios" rows="10" cols="100" placeholder="Escribe tus observaciones del pedido aquí..." name="observaciones"></textarea>
          </div>
     </div>

  <!-- Contenedor del lado derecho -->
  <div class="montos" style="flex: 1;">
    <div class="date-selector" style="margin-bottom: 10px;">
      <label for="nroPedido">Subtotal:</label>
      <input type="text" class="vendedor" id="subTotal" name="subTotal" style="text-align: right;" readonly>
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <label for="nroPedido">Movilidad:</label>
      <input type="number" class="vendedor" id="montoMovilidad" name="movilidad" required step="0.01" min="0.00" style="text-align: right;">
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <label for="nroPedido">Descuento:</label>
      <input type="number" class="vendedor" id="montoDescuento" name="descuento" required step="0.01" min="0.00" style="text-align: right;">
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <input type="hidden" class="vendedor" id="importeTotal" name="importeTotal" readonly>
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <input type="hidden" class="vendedor" id="descuentoTotal" readonly>
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <label for="nroPedido">Saldo a Pagar:</label>
      <input type="number" class="vendedor" id="saldoPagar" name="saldoPagar" style="text-align: right;" readonly>
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <button type="button" class="recalcular-button" id="recalcular">Recalcular</button>
    </div>
  </div>
</div>
     <div class="cabecera">
          <div>
              <button type="button" class="cancelar-button" id="cancelarOrden">Cancelar Operación</button>
          </div>
          <div>
              <button type="submit" class="aceptar-button" name="guardarOrden" id="aceptarOrden" disabled>Aceptar Operación</button>
              <div id="mensajeError" style="color: red; display: none;">Debe recalcular los montos</div>
          </div>   
     </div>
     </form>
     <script>
          document.getElementById('cancelarOrden').addEventListener('click', function() {
            // Mostrar un mensaje de confirmación
            const confirmacion = confirm('¿Seguro que desea cancelar la operación?');
            
            // Si el usuario confirma, redirigir a la página de la lista de alquileres
            if (confirmacion) {
              window.location.href = "<?php echo admin_url('admin.php?page=lasal-alquileres'); ?>";
            }
          });
      </script>






      <!-- Modal para buscar clientes -->
      <div id="modal-overlay-clientes" class="modal-overlay">
        <div id="modal-clientes" class="modal">
          <h2 id="modal-title">Lista de Clientes</h2>
          <input type="text" id="buscar-cliente-input" placeholder="Buscar cliente">
          <br>
          <br>
          <div class="tabla-scroll">
          <table id="lista-clientes">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>DNI</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Consulta para obtener los clientes
              $sql = "SELECT cliId, cliNombre, cliDni, cliCorreo, cliTelefono FROM cliente";
              $stmt = $conn->prepare($sql);
              $stmt->execute();
              $stmt->store_result();

              // Vincular los resultados a variables
              $stmt->bind_result($cliId, $cliNombre, $cliDni, $cliCorreo, $cliTelefono);

              // Recorrer los resultados y mostrarlos en la tabla
              while ($stmt->fetch()) {
                echo "<tr>";
                echo "<td>" . $cliId . "</td>";
                echo "<td class='nombre-cliente'>" . $cliNombre . "</td>";
                echo "<td class='dni-cliente'>" . $cliDni . "</td>";
                echo "<td class='correo-cliente'>" . $cliCorreo . "</td>";
                echo "<td class='telefono-cliente'>" . $cliTelefono . "</td>";
                echo "<td><button class='seleccionar-cliente' data-cliente-id='" . $cliId . "' data-cliente-nombre='" . $cliNombre . "'>Seleccionar</button></td>";
                echo "</tr>";
              }

              // Liberar resultados y cerrar la consulta
              $stmt->free_result();
              $stmt->close();
              ?>
            </tbody>
          </table>
          </div>
          <br>
          <div class="buttons">
            <button id="cerrar-modal-clientes" type="button" class="close-button">Cerrar</button>
          </div>
        </div>
      </div>





      <!-- Modal para buscar productos -->
      <div id="modal-overlay-productos" class="modal-overlay">
        <div id="modal-productos" class="modal">
          <h2 id="modal-title">Lista de Productos</h2>
          <input type="text" id="buscar-producto-input" placeholder="Buscar producto">
          <br>
          <br>
          <div class="tabla-scroll">
          <table id="lista-productos">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th>Imágen</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
            <?php
                  // Utilizar las variables globales de WordPress para la conexión a la base de datos
                  global $wpdb;

                  // Consulta para obtener los productos
                  $sql = "SELECT ID, nomproducto, categoria, descproducto, rutaimagen, cantstock, precioalquiler FROM v_producto;";
                  $results = $wpdb->get_results($sql);

                  // Recorrer los resultados y mostrarlos en la tabla
                  foreach ($results as $row) {
                      echo "<tr>";
                      echo "<td class='id-producto'>" . $row->ID . "</td>";
                      echo "<td class='nombre-producto'>" . $row->nomproducto . "</td>";
                      echo "<td class='categoria-producto'>" . $row->categoria . "</td>";
                      echo "<td class='descripcion-producto'>" . $row->descproducto . "</td>";
                      // Mostrar la imagen utilizando la etiqueta <img>
                      echo "<td class='imagen-producto'><img src='" . $row->rutaimagen . "' alt='Imagen del producto' style='max-width: 100px;'></td>";
                      echo "<td><button class='seleccionar-producto' data-producto-id='" . $row->ID . "' data-producto-nombre='" . $row->nomproducto . "' data-producto-categoria='" . $row->categoria . "' data-producto-stock='" . $row->cantstock . "' data-producto-precio='" . $row->precioalquiler . "'>Añadir</button></td>";
                      echo "</tr>";
                  }
            ?>
            </tbody>
          </table>
          </div>
          <br>
          <div class="buttons">
            <button id="cerrar-modal-productos" type="button" class="close-button">Cerrar</button>
          </div>
        </div>
      </div>











  </div>
  <script>



  </script>
  </body>
  </html>
  <?php



}

function lasal_alquileres_devolucion(){
  require_once plugin_dir_path(__FILE__) . '/modelo/conexion.php';
  require_once(plugin_dir_path(__FILE__) . '../../../wp-load.php');
  global $current_user;
  $nombreVendedor = $current_user->user_login;
  $userID = $current_user->ID;

  // Obtener el valor del parámetro nroAlquiler de la URL
  $nroAlquiler = isset($_GET['nroAlquiler']) ? intval($_GET['nroAlquiler']) : 0;
 

  // Verificar la conexión
  if ($conn->connect_error) {
      die("Error de conexión: " . $conn->connect_error);
  }

  // Consulta para obtener los productos
  $sqlCabecera = "SELECT nombCliente, fecEvento FROM alquiler WHERE codAlquiler  = $nroAlquiler;";
  $stmt = $conn->prepare($sqlCabecera);
  $stmt->execute();
  $stmt->store_result();

  // Vincular los resultados a variables
  $stmt->bind_result($nombCliente, $fecEvento);

  while ($stmt->fetch()) {

  }

  ?>
  <!DOCTYPE html>
  <html>
  <head>
      <title>Lista de Ventas de Productos</title>
      <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/devolucion.css', __FILE__); ?>">
      <script src="<?php echo plugins_url('/js/devolucion.js', __FILE__); ?>"></script>
  </head>
  <body>
  <div class="container">
  <?php $guardar_devolucion_url = plugins_url('/guardar_devolucion.php', __FILE__); ?>
   <form id="formularioOrden" method="POST" action="<?php echo $guardar_devolucion_url; ?>" onsubmit="return confirm('¿Estás seguro de registrar la devolución?');"> 
    <div class="cabecera">
              <!-- Barra de búsqueda -->
              <div class="titulo">
                  <label for="cliente">Gestionar Devolución</label>
              </div>
              <div class="date-selector">
                  <label for="nroPedido">Vendedor:</label>
                  <!-- Elemento input oculto para almacenar el ID del vendedor -->
                  <input type="hidden" id="vendedor-id" name="vendedor-id" value="<?php echo $userID; ?>">
                  <input type="text" class="vendedor" id="nomVendedor" name="vendedor" value="<?php echo $nombreVendedor; ?>" readonly>
              </div>
    </div> 
    <div class="search-container">
          <!-- Buscar cliente -->
          <div class="date-selector">
              <label for="cliente">Cliente:</label>
              <!-- Elemento input oculto para almacenar el ID del cliente -->
              <input type="hidden" id="cliente-id" name="cliente-id">
              <input type="text" class="search-cliente" value="<?php echo $nombCliente; ?>" name="codCliente" id="nomCliente" readonly>
          </div>
          <!-- Imprimir PDF -->
          <div class="date-selector">
              <button type="button" class="cancelar-button" style="background-color: #FFC300; border: 1px solid #2E2725; " id="imprimirPDF">PDF Devoluciones</button>
          </div>
     </div>
     <div class="search-container">
          <!-- Selectores de fecha -->
          <div class="date-selector">
              <label for="nroPedido">Nro de Pedido:</label>
              <input type="text" class="search-input" id="nroPedido" name="nroAlquiler" value="<?php echo $nroAlquiler; ?>" readonly>
          </div>
          <div class="date-selector">
              <label for="fechaEvento">Fecha de Evento:</label>
              <input type="date" id="fechaEvento" name="fechaEvento" value="<?php echo $fecEvento; ?>" readonly>
          </div>
     </div>

      <table id="tablaDevoluciones">
          <thead>
          <br>
          <br>
              <th>Fecha devolución</th>
              <th>Producto</th>
              <th>Categoría</th>
              <th>Cantidad</th>
              <th>Externo</th>
              <th>Precio alquiler</th>
              <th>Precio reposición</th>
              <th>Devuelve</th>
              <th>Penalidad</th>
              <th>Rechaza</th>
              <th>Costo rechazado</th>
              <th>Nota</th>
          </tr>
          </thead>
          <tbody>
          <?php
                // Verificar la conexión
                if ($conn->connect_error) {
                    die("Error de conexión: " . $conn->connect_error);
                }

                // Consulta para obtener los productos
                $sql = "SELECT 
                d.idDetallePedido,
                a.nombCliente,
                a.fecEvento,
                d.idPedido,            
                d.idProducto,
                d.fecDevolucion,
                d.nomProducto,
                vp.categoria,
                d.cantProducto,
                d.stocExterno,
                d.preProducto,
                vp.precioreposicion,
                d.cantDevuelto,
                d.cantRechazado,
                d.monPenalidad,
                d.monRechazado,
                d.obsNota
                FROM alquiler a
                INNER JOIN detallepedido d ON a.codAlquiler = d.idPedido
                INNER JOIN kleon_wp_lasaldelavida.v_producto vp ON d.idProducto = vp.ID WHERE idPedido = $nroAlquiler;";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $stmt->store_result();

                // Vincular los resultados a variables
                $stmt->bind_result($idDetallePedido, $nombCliente, $fecEvento, $idPedido, $idProducto, $fecDevolucion, $nomProducto, $categoria, $cantProducto, $stocExterno, 
                                   $preProducto, $preReposicion, $cantDevuelto, $cantRechazado, $monPenalidad, $monRechazado, $obsNota);

                // Recorrer los resultados y mostrarlos en la tabla
                $fechaActual = date('Y-m-d');
                while ($stmt->fetch()) {
                    echo "<tr>";
                    $displayFecha = ($fecDevolucion !== '0000-00-00') ? $fecDevolucion : $fechaActual;
                    echo "<td><input type='date' name='fechaDevolucion[]' value='{$displayFecha}' readonly></td>";
                    echo "<td class='nombre-producto'>" . $nomProducto . "</td>";
                    echo "<td class='categoria-producto'>" . $categoria . "</td>";
                    echo "<td class='cantidad-producto' name=''>" . $cantProducto . "</td>";
                    echo "<td class='cantidad-externa'>" . $stocExterno . "</td>";
                    echo "<td class='precio-alquiler' name=''>" . number_format($preProducto,2) . "</td>";
                    echo "<td class='precio-reposicion' name=''>" . number_format($preReposicion,2) . "</td>";
                    echo "<td><input type='number' class='cantidad-devuelta' name='cantDevuelta[]' style='width: 60px;' min='0' value='" . (!empty($cantDevuelto) ? $cantDevuelto : "0") . "'></td>";
                    echo "<td class='monto-penalidad' name='montoPenalidad[]'>" .number_format($monPenalidad,2). "</td>";
                    echo "<td><input type='number' class='cantidad-rechazada' name='cantRechazada[]' style='width: 60px;' min='0' value='" . (!empty($cantRechazado) ? $cantRechazado : "0") . "'></td>";
                    echo "<td class='monto-rechazado' name='montoRechazado[]'>" .number_format($monRechazado,2). "</td>";
                    echo "<td><input type='text' name='notaDevolucion[]' value='{$obsNota}'></td>";
                    echo "<td><input type='hidden' name='idDetallePedido[]' value='{$idDetallePedido}'></td>";
                    echo "<td><input type='hidden' name='precioReposicion[]' value='{$preReposicion}'></td>";
                    echo "<td><input type='hidden' name='precioAlquiler[]' value='{$preProducto}'></td>";
                    echo "<td><input type='hidden' name='cantAlquilada[]' value='{$cantProducto}'></td>";
                    echo "<td><input type='hidden' name='cantExterna[]' value='{$stocExterno}'></td>";
                    echo "<td><input type='hidden' name='idProducto[]' value='{$idProducto}'></td>";
                    echo "</tr>";
                }

                // Liberar resultados y cerrar la consulta
                $stmt->free_result();
                $stmt->close();
          ?>

          </tbody>
      </table>
      <br>


<div style="display: flex; flex-direction: row;">


  <!-- Contenedor del lado derecho -->
  <div class="montos" style="flex: 1;">
    <div class="date-selector" style="margin-bottom: 10px;">
      <label for="nroPedido">Total Penalidad:</label>
      <input type="number" class="vendedor" id="totalPenalidad" name="totalPenalidad" style="text-align: right;" readonly>
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <label for="nroPedido">Total Rechazado:</label>
      <input type="number" class="vendedor" id="totalRechazado" name="totalRechazado" style="text-align: right;" readonly>
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <button type="button" class="recalcular-button" id="recalcular">Confirmar devolución</button>
    </div>
  </div>
</div>

     <div class="cabecera">
          <div>
              <button type="button" class="cancelar-button" id="cancelarDevolucion">Cancelar Operación</button>
          </div>
          <div>
              <button type="submit" class="aceptar-button" name="aceptarDevolucion" id="aceptarDevolucion" disabled>Efectuar Devolución</button>
          </div>   
     </div>
     </form>
     <script>
          document.getElementById('cancelarDevolucion').addEventListener('click', function() {
            // Mostrar un mensaje de confirmación
            const confirmacion = confirm('¿Seguro que desea cancelar la operación?');
            
            // Si el usuario confirma, redirigir a la página de la lista de alquileres
            if (confirmacion) {
              window.location.href = "<?php echo admin_url('admin.php?page=lasal-alquileres'); ?>";
            }
          });
      </script>




  </div>
  </body>
  </html>
  <?php

}



function lasal_alquileres_editar_orden(){

  require_once plugin_dir_path(__FILE__) . '/modelo/conexion.php';
  require_once(plugin_dir_path(__FILE__) . '../../../wp-load.php');


  if (isset($_POST['nroAlquiler'])) {
    $nroAlquiler = intval($_POST['nroAlquiler']); // Asegúrate de que sea un número entero

    if ($conn->connect_error) {
      die("Error de conexión: " . $conn->connect_error);
    } 

      // Consulta para obtener los productos
      $sql = "SELECT codCliente, nombCliente, fecEvento, fecEntregaProducto, fecDevolucionProducto, fecOperacion, codVendedor, nombVendedor, dirOrden, estOrden, obsOrden, movilidad, descuento FROM alquiler WHERE codAlquiler = $nroAlquiler;";
      $stmt = $conn->prepare($sql);
      $stmt->execute();
      $stmt->store_result();

      // Vincular los resultados a variables
      $stmt->bind_result($codCliente, $nombCliente, $fecEvento, $fecEntregaProducto, $fecDevolucionProducto, $fecOperacion, $codVendedor, $nombVendedor, $dirOrden, $estOrden, $obsOrden, $movilidad, $descuento);
      while ($stmt->fetch()) {
  
      }


} else {
    echo "No se proporcionó el ID de la orden de alquiler.";
}


  ?>
  <!DOCTYPE html>
  <html>
  <head>
      <title>Lista de Ventas de Productos</title>
      <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/nueva_orden.css', __FILE__); ?>">
      <script src="<?php echo plugins_url('/js/editar_alquiler.js', __FILE__); ?>"></script>
  </head>
  <body>
  <div class="container">
   <form id="formularioOrden" method="POST" action=""> 
    <div class="cabecera">
              <!-- Barra de búsqueda -->
              <div class="titulo">
                  <label for="cliente">Editar Alquiler</label>
              </div>
              <div class="date-selector">
                  <label for="nroPedido">Vendedor:</label>
                  <!-- Elemento input oculto para almacenar el ID del vendedor -->
                  <input type="hidden" id="vendedor-id" name="vendedor-id" value="<?php echo $codVendedor; ?>">
                  <input type="text" class="vendedor" id="nomVendedor" name="vendedor" value="<?php echo $nombVendedor; ?>" readonly>
              </div>
    </div> 
    <div class="search-container">
          <!-- Buscar cliente -->
          <div class="date-selector">
              <label for="cliente">Cliente:</label>
              <!-- Elemento input oculto para almacenar el ID del cliente -->
              <input type="hidden" id="cliente-id" name="cliente-id" value="<?php echo $codCliente; ?>">
              <input type="text" class="search-cliente" name="codCliente" id="nomCliente" value="<?php echo $nombCliente; ?>" readonly>
          </div>
          <div class="date-selector">
          <label for="estado">Estado de Orden:</label>
              <select id="estado" name="estado">
                  <!-- <option value="En revision" <?php if ($estOrden === 'En revision') echo 'selected'; ?>>En revision</option> -->
                  <option value="Pendiente de entrega" <?php if ($estOrden === 'Pendiente de entrega') echo 'selected'; ?>>Pendiente de entrega</option>
                  <option value="Pendiente de devolucion" <?php if ($estOrden === 'Pendiente de devolucion') echo 'selected'; ?>>Pendiente de devolucion</option>
                  <option value="Devuelto" <?php if ($estOrden === 'Devuelto') echo 'selected'; ?>>Devuelto</option>
                  <option value="Finalizado" <?php if ($estOrden === 'Finalizado') echo 'selected'; ?>>Finalizado</option>
              </select>
          </div>  
     </div>
     <div class="search-container">
          <!-- Selectores de fecha -->
          <div class="date-selector">
              <label for="nroPedido">Nro de Pedido:</label>
              <input type="text" class="search-input" id="nroPedido" value="<?php echo $nroAlquiler; ?>" readonly>
          </div>
          <div class="date-selector">
              <label for="fechaEvento">Fecha de Evento:</label>
              <input type="date" id="fechaEvento" name="fechaEvento" value="<?php echo $fecEvento; ?>" readonly>
          </div>
          <div class="date-selector">
              <label for="fechaOperacion">Fecha de Operación:</label>
              <input type="date" id="fechaOperacion" name="fechaOperacion" value="<?php echo $fecOperacion; ?>" readonly>
          </div>
     </div>
     <div class="search-container">
          <!-- Barra de búsqueda -->
          <div class="date-selector">
          <label for="direccion">Dirección:</label>
          <input type="text" class="direccion" id="direccion" placeholder="Dirección de envío" name="direccionEnvio" value="<?php echo $dirOrden; ?>" required>
          </div>
          <!-- Selectores de fecha -->
          <div class="date-selector">
              <label for="fechaEntregaProductos">Fecha entrega productos:</label>
              <input type="date" id="fechaEntregaProductos" name="fechaEntregaProductos" value="<?php echo $fecEntregaProducto; ?>" readonly>
          </div>
          <div class="date-selector">
              <label for="fechaDevolucionProductos">Fecha devolución productos:</label>
              <input type="date" id="fechaDevolucionProductos" name="fechaDevolucionProductos" value="<?php echo $fecDevolucionProducto; ?>" readonly>
          </div>
     </div>

      <table id="tablaProductos">
          <thead>
          <br>
          <br>
          <div class="">
              <!-- Barra de búsqueda -->
              <div class="date-selector">
                  <label for="producto">Productos:</label>
                  <!-- Elemento input oculto para almacenar el ID del producto -->
                  <input type="hidden" id="producto-id" name="producto-id">
                  <button type="button" class="buscar-producto" id="buscar-productos">Buscar Producto</button>                  
              </div>
          </div> 
          <br>
          <tr>
              <th>Fecha</th>
              <th>Fecha entrega</th>
              <th>Producto</th>
              <th>Categoría</th>
              <th>Cantidad</th>
              <th>Stock interno</th>
              <th>Precio</th>
              <th>Acciones</th>
              <th>Estado</th>
              <th>Stock externo</th>
              <th>Empresa</th>
              <th>Subtotal</th>
          </tr>
          </thead>
          <tbody>
    <?php
    $sqlDetalle = "SELECT dp.idDetallePedido, dp.idProducto, dp.fecRegistro, dp.fecEntrega, dp.nomProducto, dp.cantProducto,
                    dp.stocInterno, dp.preProducto, dp.estaDetallePedido, dp.stocExterno, dp.nomEmpresa, dp.monSubTotal, vp.categoria, vp.cantstock
                    FROM kleon_wp_lasaldelavida.detallepedido AS dp
                    JOIN kleon_wp_lasaldelavida.v_producto AS vp ON dp.idProducto = vp.ID
                     WHERE dp.idPedido = $nroAlquiler;";
    
    $resultDetalle = $conn->query($sqlDetalle);

    if ($resultDetalle->num_rows > 0) {
        while ($detalle = $resultDetalle->fetch_assoc()) {
            echo "<tr>";
            echo "<td><input type='date' value='{$detalle['fecRegistro']}' readonly></td>";
            echo "<td><input type='date' value='{$detalle['fecEntrega']}'></td>";
            echo "<td>{$detalle['nomProducto']}</td>";
            echo "<td>{$detalle['categoria']}</td>";
            echo "<td><input type='number' id='cantidad' style='width: 60px;' min='0' value='{$detalle['cantProducto']}' title='Debe seleccionar como mínimo 1' required></td>";
            echo "<td></td>";
            echo "<td>" . number_format($detalle['preProducto'], 2) . "</td>";
            echo "<td><button type='button' class='quitar-producto' style='background-color: #ffcccc; border: none; color: #333; padding: 5px 10px; cursor: pointer; border-radius: 4px; margin-right: 2px;'>Quitar</button>";
            echo "<button type='button' class='entregar-producto' style='background-color: #ffd699; border: none; color: #333; padding: 5px 10px; cursor: pointer; border-radius: 4px;'>Entregar</button></td>";
            echo "<td>{$detalle['estaDetallePedido']}</td>";
            echo "<td><input type='number' id='cantExterna' style='width: 60px;' min='0' value='{$detalle['stocExterno']}'></td>";
            echo "<td><input type='text' value='{$detalle['nomEmpresa']}'></td>";
            echo "<td>" . number_format($detalle['monSubTotal'], 2) . "</td>";
            echo "<td><input type='hidden' id='idProducto' value='{$detalle['idProducto']}'></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='12'>No se encontraron detalles de alquiler.</td></tr>";
    }
    ?>

          </tbody>
      </table>
      <div>
              <div>
                  <br>
                  <button type="button" class="evaluar-button" id="evaluarDisponibilidad">Evaluar Disponibilidad</button>
              </div>
      </div>
      <br>
      
<div style="display: flex; flex-direction: row;">
  <!-- Contenedor del lado izquierdo -->
  <div class="search-container">
          <div class="date-selector">
          <label for="direccion">Observaciones:</label>
          </div>
      </div>
      <div class="search-container">
          <div class="date-selector">
          <textarea id="comentarios" rows="10" cols="100" placeholder="Escribe tus observaciones del pedido aquí..." name="observaciones"><?php echo $obsOrden; ?></textarea>
          </div>
     </div>

  <!-- Contenedor del lado derecho -->
  <div class="montos" style="flex: 1;">
    <div class="date-selector" style="margin-bottom: 10px;">
      <label for="nroPedido">Subtotal:</label>
      <input type="text" class="vendedor" id="subTotal" name="subTotal" style="text-align: right;" readonly>
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <label for="nroPedido">Movilidad:</label>
      <input type="number" class="vendedor" id="montoMovilidad" name="movilidad" value="<?php echo $movilidad; ?>" required step="0.01" min="0.00" style="text-align: right;">
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <label for="nroPedido">Descuento:</label>
      <input type="number" class="vendedor" id="montoDescuento" name="descuento" value="<?php echo $descuento; ?>" required step="0.01" min="0.00" style="text-align: right;">
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <input type="hidden" class="vendedor" id="importeTotal" name="importeTotal" readonly>
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <input type="hidden" class="vendedor" id="descuentoTotal" readonly>
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <label for="nroPedido">Saldo a Pagar:</label>
      <input type="number" class="vendedor" id="saldoPagar" name="saldoPagar" style="text-align: right;" readonly>
    </div>
    <div class="date-selector" style="margin-bottom: 10px;">
      <button type="button" class="recalcular-button" id="recalcular">Recalcular</button>
    </div>
  </div>
</div>

     <div class="cabecera">
          <div>
              <button type="button" class="cancelar-button" id="cancelarOrden">Cancelar Operación</button>
          </div>
          <div>
              <button type="submit" class="aceptar-button" name="guardarOrden" id="aceptarOrden" disabled>Aceptar Operación</button>
              <div id="mensajeError" style="color: red; display: none;">Debe recalcular los montos</div>
          </div>
     </div>
     </form>
     <script>
          document.getElementById('cancelarOrden').addEventListener('click', function() {
            // Mostrar un mensaje de confirmación
            const confirmacion = confirm('¿Seguro que desea cancelar la operación?');
            
            // Si el usuario confirma, redirigir a la página de la lista de alquileres
            if (confirmacion) {
              window.location.href = "<?php echo admin_url('admin.php?page=lasal-alquileres'); ?>";
            }
          });
      </script>






      <!-- Modal para buscar productos -->
      <div id="modal-overlay-productos" class="modal-overlay">
        <div id="modal-productos" class="modal">
          <h2 id="modal-title">Lista de Productos</h2>
          <input type="text" id="buscar-producto-input" placeholder="Buscar producto">
          <br>
          <br>
          <div class="tabla-scroll">
          <table id="lista-productos">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th>Imágen</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
            <?php
                  // Utilizar las variables globales de WordPress para la conexión a la base de datos
                  global $wpdb;

                  // Consulta para obtener los productos
                  $sql = "SELECT ID, nomproducto, categoria, descproducto, rutaimagen, cantstock, precioalquiler FROM v_producto;";
                  $results = $wpdb->get_results($sql);

                  // Recorrer los resultados y mostrarlos en la tabla
                  foreach ($results as $row) {
                      echo "<tr>";
                      echo "<td class='id-producto'>" . $row->ID . "</td>";
                      echo "<td class='nombre-producto'>" . $row->nomproducto . "</td>";
                      echo "<td class='categoria-producto'>" . $row->categoria . "</td>";
                      echo "<td class='descripcion-producto'>" . $row->descproducto . "</td>";
                      // Mostrar la imagen utilizando la etiqueta <img>
                      echo "<td class='imagen-producto'><img src='" . $row->rutaimagen . "' alt='Imagen del producto' style='max-width: 100px;'></td>";
                      echo "<td><button class='seleccionar-producto' data-producto-id='" . $row->ID . "' data-producto-nombre='" . $row->nomproducto . "' data-producto-categoria='" . $row->categoria . "' data-producto-stock='" . $row->cantstock . "' data-producto-precio='" . $row->precioalquiler . "'>Añadir</button></td>";
                      echo "</tr>";
                  }
            ?>
            </tbody>
          </table>
          </div>
          <br>
          <div class="buttons">
            <button id="cerrar-modal-productos" type="button" class="close-button">Cerrar</button>
          </div>
        </div>
      </div>











  </div>
  <script>



  </script>
  </body>
  </html>
  <?php


}



function redireccionar_a_nueva_orden() {
  // Utiliza la función wp_redirect para redirigir al usuario a la página lasal_alquileres_nueva_orden
  wp_redirect(admin_url('admin.php?page=lasal-alquileres-nueva-orden'));
  exit;
}

function redireccionar_a_lista_alquileres() {
  // Utiliza la función wp_redirect para redirigir al usuario a la página lasal_alquileres
  wp_redirect(admin_url('admin.php?page=lasal-alquileres'));
  exit;
}

function redireccionar_a_reporte_ventas_fecha() {
  // Utiliza la función wp_redirect para redirigir al usuario al reporte de ventas por fecha
  wp_redirect(admin_url('admin.php?page=lasal-reportes-ventas-fecha'));
  exit;
}

function redireccionar_a_lista_gastos() {
    // Utiliza la función wp_redirect para redirigir al usuario a la página lasal_alquileres
    wp_redirect(admin_url('admin.php?page=lasal-gastos'));
    exit;
}


?>