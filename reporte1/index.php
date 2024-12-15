<!-- PARA LA TABLA -->
<?php
// Consulta SQL
$consulta = "
    SELECT v.fecha_registro, v.seriefactura, v.nofactura, v.total, v.montooriginal,
           c.primer_nombre as cliente
    FROM adm_venta v 
    JOIN `db_rmym`.`clientes` c ON v.id_cliente = c.idcliente
    WHERE DATE(v.fecha_registro) >= '20241201'
      AND v.estado = 1
      AND v.tipo = 'E';
";

// Función para obtener los datos
function obtenerDatosDeBaseDeDatos($consulta)
{
    $servidor = 'localhost';
    $usuario = 'root';
    $contrasena = 'MyG4b0QL2023**@##';
    $baseDeDatos = 'db_mymsa';

    $conexion = new mysqli($servidor, $usuario, $contrasena, $baseDeDatos);

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    $resultado = $conexion->query($consulta);

    if ($resultado) {
        $datos = $resultado->fetch_all(MYSQLI_ASSOC);
        $conexion->close();
        return $datos;
    } else {
        echo "Error en la consulta: " . $conexion->error;
        $conexion->close();
        return null;
    }
}

// Llamar la función y obtener los datos
$resultado = obtenerDatosDeBaseDeDatos($consulta);
?>

<!-- PARA LA GRÁFICA -->
<?php
// Agrupar por semana y sumar los totales
$semanas = [];
foreach ($resultado as $fila) {
    $semana = date('o-W', strtotime($fila['fecha_registro'])); // Año-Semana
    if (!isset($semanas[$semana])) {
        $semanas[$semana] = 0;
    }
    $semanas[$semana] += $fila["total"];
}

// Preparar datos para Chart.js
$labels = array_keys($semanas); // Semanas como etiquetas
$dataTotal = array_values($semanas); // Totales por semana
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Tabla de data table -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <link rel="stylesheet" href="css/datatables.css" />
    <link rel="stylesheet" href="css/datatables.min.css" />
    <link rel="stylesheet" href="css/estilos.css">
    <title>Reporte</title>
</head>
<body>
    <div class="contenedor_titulo">
        <h2>Reporte MYM</h2>
    </div>

    <div class="contenedor_tabla">
            <table id="tabla-ventas" class="display">
        <thead>
            <tr>
                <th>Fecha Registro</th>
                <th>Serie Factura</th>
                <th>No Factura</th>
                <th>Total</th>
                <th>Monto Original</th>
                <th>Cliente</th>
            </tr>
        </thead>
        <tbody>
            <!-- Se llenará dinámicamente con JavaScript -->
        </tbody>
    </table>
    </div>

    <div class="contenedor_grafica">
    <canvas id="myChart"></canvas>
    </div>
    <button id="exportPDF">Exportar a PDF</button>


<!-- Gráfica -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
        // Datos pasados desde PHP a JavaScript
        const labels = <?php echo json_encode($labels); ?>; // Semanas
        const dataTotal = <?php echo json_encode($dataTotal); ?>; // Totales

        // Configuración de la gráfica
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar', // Puedes usar 'line' o 'bar'
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total por Semana',
                        data: dataTotal,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false // Solo un dataset
                    },
                    title: {
                        display: true,
                        text: 'Total por Semana'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });


         // Exportar a PDF
         document.getElementById('exportPDF').addEventListener('click', async () => {
            const { jsPDF } = window.jspdf;

            // Capturar gráfica como imagen
            const imgData = myChart.toBase64Image();

            // Crear un archivo PDF y agregar la imagen
            const pdf = new jsPDF();
            pdf.addImage(imgData, 'PNG', 10, 10, 180, 100); // Ajustar las dimensiones según necesidad
            pdf.save('grafica.pdf'); // Descargar archivo
        });
    </script>

<!-- Data Table -->
<script src="js/jquery-3.7.1.js"></script>
<script src="js/datatables.js"></script>
<script src="js/datatables.min.js"></script>
<!-- JSZip para exportar a Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<!-- pdfmake para exportar a PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<!-- Botones de exportación -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
        $(document).ready(function () {
            // Datos generados desde PHP
            const datos = <?php echo json_encode($resultado); ?>;

            // Llenar la tabla con los datos
            const tablaCuerpo = $("#tabla-ventas tbody");
            datos.forEach(fila => {
                let filaHTML = "<tr>";
                filaHTML += `<td>${fila.fecha_registro}</td>`;
                filaHTML += `<td>${fila.seriefactura}</td>`;
                filaHTML += `<td>${fila.nofactura}</td>`;
                filaHTML += `<td>${formatCurrency(fila.total)}</td>`;
                filaHTML += `<td>${formatCurrency(fila.montooriginal)}</td>`;
                filaHTML += `<td>${fila.cliente}</td>`;
                filaHTML += "</tr>";
                tablaCuerpo.append(filaHTML);
            });
            function formatCurrency(value) {
    return `Q ${Number(value).toLocaleString('es-GT', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}`;
}


$("#tabla-ventas").DataTable({
    paging: false,
    scrollCollapse: true,
    searching: false,
    scrollY: '400px',
    language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
    },
    dom: 'Bfrtip', 
    buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
    ]
});
});
    </script>
</body>
</html>

