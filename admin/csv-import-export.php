<?php
// Mostrar la página para importar/exportar CSV
function mostrar_pagina_importar_exportar_csv() {
    ?>
    <div class="wrap">
        <h1>Importar/Exportar Distribuidores en CSV</h1>

        <!-- Formulario para descargar distribuidores en CSV -->
        <h2>Exportar CSV</h2>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="exportar_distribuidores_csv">
            <?php submit_button('Descargar Distribuidores en CSV'); ?>
        </form>

        <!-- Formulario para subir archivo CSV -->
        <h2>Importar CSV</h2>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
            <input type="hidden" name="action" value="importar_distribuidores_csv">
            <input type="file" name="csv_file" accept=".csv" required>
            <?php submit_button('Subir CSV'); ?>
        </form>
    </div>
    <?php
}

// Función para exportar los distribuidores en CSV
function exportar_distribuidores_csv() {
    if (!current_user_can('manage_options')) {
        wp_die('No tienes permisos suficientes para acceder a esta página.');
    }

    global $wpdb;
    $tabla_distribuidores = "{$wpdb->prefix}distribuidores";
    $distribuidores = $wpdb->get_results("SELECT * FROM $tabla_distribuidores", ARRAY_A);

    if (empty($distribuidores)) {
        wp_die('No hay distribuidores disponibles para exportar.');
    }

    // Forzar el uso de UTF-8
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment;filename=distribuidores.csv');
    
    // Insertar la marca BOM de UTF-8 para que Excel u otros programas lo detecten correctamente
    echo "\xEF\xBB\xBF"; // Esta línea agrega el BOM

    $output = fopen('php://output', 'w');

    // Encabezados del CSV
    fputcsv($output, array('ID', 'Nombre', 'Dirección', 'Latitud', 'Longitud', 'Teléfono', 'Correo', 'Teléfono de Agencia', 'tiene_taller'));

    // Filas de los distribuidores
    foreach ($distribuidores as $distribuidor) {
        fputcsv($output, array(
            $distribuidor['id'], 
            $distribuidor['nombre'], 
            $distribuidor['direccion'], 
            $distribuidor['latitud'], 
            $distribuidor['longitud'], 
            $distribuidor['contacto'],  // Este campo es el teléfono de contacto
            $distribuidor['correo'],     // Este campo es el correo
            $distribuidor['telefono_agencia'], // Este campo es el teléfono de agencia
            $distribuidor['tiene_taller'] // Mantiene el valor "0" o "1"
        ));
    }

    fclose($output);
    exit;
}

add_action('admin_post_exportar_distribuidores_csv', 'exportar_distribuidores_csv');


// Función para detectar la codificación automáticamente
function detectar_codificacion($archivo) {
    $contenido = file_get_contents($archivo);
    $codificacion = mb_detect_encoding($contenido, 'UTF-8, ISO-8859-1, ASCII', true);
    return $codificacion ?: 'UTF-8'; // Si no detecta nada, usar UTF-8 por defecto
}

// Función para importar distribuidores desde un archivo CSV
function importar_distribuidores_csv() {
    if (!current_user_can('manage_options')) {
        wp_die('No tienes permisos suficientes para acceder a esta página.');
    }

    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        wp_die('Error al subir el archivo CSV.');
    }

    $file = $_FILES['csv_file']['tmp_name'];

    // Detectar la codificación del archivo
    $codificacion = detectar_codificacion($file);
    
    // Abrir el archivo en modo de lectura con la codificación detectada
    $handle = fopen($file, 'r');
    if ($handle === false) {
        wp_die('No se pudo abrir el archivo CSV.');
    }

    // Reconvertir a UTF-8 si la codificación no es UTF-8
    if ($codificacion !== 'UTF-8') {
        stream_filter_prepend($handle, 'convert.iconv.' . $codificacion . '/UTF-8');
    }

    global $wpdb;
    $tabla_distribuidores = "{$wpdb->prefix}distribuidores";

    // Saltar la primera línea (encabezados del CSV)
    fgetcsv($handle);

    while (($row = fgetcsv($handle, 1000, detect_separator($file))) !== false) {
        // Validar que cada campo esté presente y bien formateado
        $id = !empty($row[0]) ? intval($row[0]) : null; // Si está vacío, asignamos null
        $nombre = !empty($row[1]) ? sanitize_text_field($row[1]) : '';
        $direccion = !empty($row[2]) ? sanitize_text_field($row[2]) : '';
        $latitud = !empty($row[3]) ? floatval($row[3]) : null;
        $longitud = !empty($row[4]) ? floatval($row[4]) : null;
        $contacto = !empty($row[5]) ? sanitize_text_field($row[5]) : '';
        $correo = !empty($row[6]) ? sanitize_email($row[6]) : '';
        $telefono_agencia = !empty($row[7]) ? sanitize_text_field($row[7]) : '';
        $tiene_taller = isset($row[8]) ? intval($row[8]) : 0;

        if ($id !== null) {
            // Verificar si el distribuidor ya existe por el 'ID'
            $distribuidor_existente = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT id FROM $tabla_distribuidores WHERE id = %d",
                    $id
                )
            );

            // Si el distribuidor existe, actualizamos los datos
            if ($distribuidor_existente) {
                $wpdb->update(
                    $tabla_distribuidores,
                    array(
                        'nombre' => $nombre,
                        'direccion' => $direccion,
                        'latitud' => $latitud,
                        'longitud' => $longitud,
                        'contacto' => $contacto,
                        'correo' => $correo,
                        'telefono_agencia' => $telefono_agencia,
                        'tiene_taller' => $tiene_taller
                    ),
                    array('id' => $id), // Actualizar por ID
                    array('%s', '%s', '%f', '%f', '%s', '%s', '%s', '%d'),
                    array('%d')
                );
            }
        } else {
            // Si no hay ID, insertar un nuevo distribuidor
            $wpdb->insert(
                $tabla_distribuidores,
                array(
                    'nombre' => $nombre,
                    'direccion' => $direccion,
                    'latitud' => $latitud,
                    'longitud' => $longitud,
                    'contacto' => $contacto,
                    'correo' => $correo,
                    'telefono_agencia' => $telefono_agencia,
                    'tiene_taller' => $tiene_taller
                ),
                array('%s', '%s', '%f', '%f', '%s', '%s', '%s', '%d')
            );
        }
    }

    fclose($handle);

    // Redirigir después de la importación
    wp_redirect(admin_url('admin.php?page=importar-exportar-csv&import=success'));
    exit;
}

// Función para detectar el separador automáticamente
function detect_separator($file) {
    $handle = fopen($file, 'r');
    $line = fgets($handle);
    fclose($handle);

    if (strpos($line, ',') !== false) {
        return ',';
    } elseif (strpos($line, ';') !== false) {
        return ';';
    } elseif (strpos($line, "\t") !== false) {
        return "\t";
    } else {
        return ','; // Por defecto, coma
    }
}

add_action('admin_post_importar_distribuidores_csv', 'importar_distribuidores_csv');
