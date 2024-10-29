<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa Interactivo con SVG Externo</title>
    <style>
        .state {
            fill: #cccccc;
            stroke: #333333;
            stroke-width: 1;
            transition: fill 0.3s ease;
        }

        .state:hover {
            fill: #ff0000;
        }

        .highlight {
            fill: #00ff00; /* Color de resalto */
        }

        .clickable {
            cursor: pointer;
        }

        /* Resalto después de hacer clic */
        .clicked {
            fill: #00ff00;
        }
    </style>
</head>
<body>

<h1>Mapa Interactivo desde Archivo Externo</h1>

<div>
    <?php
    // Cargar el contenido del archivo SVG externo
    $svg_file = file_get_contents('https://agroequipos.triumphmexico.shop/wp-content/uploads/2024/09/MAPA.svg');
    echo $svg_file; // Mostrar el contenido del archivo SVG
    ?>
</div>

<!-- JavaScript para interactividad -->
<script>
    window.onload = function() {
        // Función para resaltar el estado al hacer clic
        function highlightState(id) {
            const state = document.getElementById(id);
            if (state) {
                state.classList.toggle('clicked'); // Cambiar la clase al hacer clic
            }
        }

        document.getElementById('MX-AGU').addEventListener('click', function() {
            alert('Has hecho clic en Aguascalientes!');
            highlightState('MX-AGU');
        });

        document.getElementById('MX-BCN').addEventListener('click', function() {
            alert('Has hecho clic en Baja California Norte!');
            highlightState('MX-BCN');
        });

        document.getElementById('MX-MEX').addEventListener('click', function() {
            alert('Has hecho clic en Ciudad de México!');
            highlightState('MX-MEX');
        });

        document.getElementById('MX-YUC').addEventListener('click', function() {
            alert('Has hecho clic en Yucatán!');
            highlightState('MX-YUC');
        });
    };
</script>

</body>
</html>
