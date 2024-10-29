<?php
/*
Plugin Name: Distribuidores Global
Description: Plugin para registrar y mostrar distribuidores en un mapa.
Version: 1.0
Author: Haziel Zul | Grupo Motomex | Coordinador Web
*/

if (!defined('ABSPATH')) {
    exit; // Salir si se accede directamente
}

// Crear la tabla en la base de datos al activar el plugin
function crear_tabla_distribuidores() {
    global $wpdb;
    $tabla_distribuidores = "{$wpdb->prefix}distribuidores";
    $charset_collate = $wpdb->get_charset_collate();

    // Crear la tabla con las nuevas columnas tiene_taller, correo, y telefono_agencia
    $sql = "CREATE TABLE $tabla_distribuidores (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(255) NOT NULL,
        direccion varchar(255) NOT NULL,
        latitud float(10, 6) NOT NULL,
        longitud float(10, 6) NOT NULL,
        contacto varchar(255) NOT NULL,
        correo varchar(255),  -- Columna para correo
        tiene_taller tinyint(1) NOT NULL DEFAULT 0,  -- Columna para el taller
        telefono_agencia varchar(255),  -- Nueva columna para teléfono de agencia
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'crear_tabla_distribuidores');


function actualizar_tabla_distribuidores() {
    global $wpdb;
    $tabla_distribuidores = "{$wpdb->prefix}distribuidores";

    // Verificar si la columna 'correo' no existe
    $columna_correo = $wpdb->get_results("SHOW COLUMNS FROM $tabla_distribuidores LIKE 'correo'");
    
    if (empty($columna_correo)) {
        // Si no existe, añadir la columna 'correo'
        $wpdb->query("ALTER TABLE $tabla_distribuidores ADD correo varchar(255)");
    }

    // Verificar si la columna 'tiene_taller' ya existe
    $columna_taller = $wpdb->get_results("SHOW COLUMNS FROM $tabla_distribuidores LIKE 'tiene_taller'");
    
    if (empty($columna_taller)) {
        // Si no existe, añadir la columna 'tiene_taller'
        $wpdb->query("ALTER TABLE $tabla_distribuidores ADD tiene_taller tinyint(1) NOT NULL DEFAULT 0");
    }

    // Verificar si la columna 'telefono_agencia' ya existe
    $columna_telefono_agencia = $wpdb->get_results("SHOW COLUMNS FROM $tabla_distribuidores LIKE 'telefono_agencia'");
    
    if (empty($columna_telefono_agencia)) {
        // Si no existe, añadir la columna 'telefono_agencia'
        $wpdb->query("ALTER TABLE $tabla_distribuidores ADD telefono_agencia varchar(255)");
    }
}

add_action('plugins_loaded', 'actualizar_tabla_distribuidores');



// Registrar el submenú de Editar Distribuidores
function distribuidores_global_menu() {
    
    add_menu_page(
        'Distribuidores Global',               // Título de la página
        'Distribuidores Global',               // Título del menú
        'manage_options',                      // Capacidad de usuario
        'distribuidores-global',               // Slug de la página
        'mostrar_pagina_distribuidores',       // Función que renderiza la página
        'dashicons-location-alt',              // Icono del menú
        6                                      // Posición en el menú
    );

    // Submenú para Editar Distribuidores
    add_submenu_page(
        'distribuidores-global',               // Slug del menú principal
        'Editar Distribuidores',               // Título de la página
        'Editar Distribuidores',               // Título del submenú
        'manage_options',                      // Capacidad del usuario
        'editar-distribuidores',               // Slug de la página de edición
        'mostrar_pagina_editar_distribuidores' // Función que renderiza la página de edición
    );

    // Submenú para Configuraciones
    add_submenu_page(
        'distribuidores-global',               // Slug del menú principal
        'Configuración',                      // Título de la página
        'Configuración',                      // Título del submenú
        'manage_options',                     // Capacidad del usuario
        'configuracion-distribuidores',       // Slug de la página de configuración
        'mostrar_pagina_configuracion'        // Función que renderiza la página de configuración
    );

    // Añadir un nuevo submenú para importar/exportar CSV
    add_submenu_page(
        'distribuidores-global',               // Slug del menú principal
        'Importar/Exportar Distribuidores CSV', // Título de la página
        'Gestión CSV',                         // Título del submenú
        'manage_options',                      // Capacidad del usuario
        'importar-exportar-csv',               // Slug de la página de configuración
        'mostrar_pagina_importar_exportar_csv' // Función que renderiza la página de configuración
    );
}
add_action('admin_menu', 'distribuidores_global_menu');

// Incluir el archivo de import/export CSV
include plugin_dir_path(__FILE__) . 'admin/csv-import-export.php';


// Función para mostrar la página de editar distribuidores
function mostrar_pagina_editar_distribuidores() {
    include plugin_dir_path(__FILE__) . 'admin/editar-distribuidores.php';
}

function cargar_estilos_admin_distribuidores($hook) {
    // Encolar Tailwind CSS en ambas páginas
    if ($hook != 'toplevel_page_distribuidores-global' && $hook != 'distribuidores-global_page_editar-distribuidores') {
        return;
    }

    // Encolar Tailwind CSS desde un CDN
    wp_enqueue_style('tailwind-css', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');
    
    // Encolar tu propio CSS si necesitas personalizaciones adicionales
    wp_enqueue_style('distribuidores-admin-css', plugin_dir_url(__FILE__) . 'admin/css/styles.css');

    // Encolar estilos y scripts de Leaflet
    wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css');
    wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js', array(), null, true);

    // Encolar el archivo de tu mapa (global-map.js)
    wp_enqueue_script('global-map-js', plugin_dir_url(__FILE__) . 'admin/js/global-map.js', array('jquery', 'leaflet-js'), null, true);
    
    // Obtener la URL del ícono predeterminado
    $icono_default = get_option('distribuidores_icono_default', 'https://motocarrostvs.triumphmexico.shop/wp-content/uploads/2024/09/mototaxi-1.png');
    
    // Añadir el script en línea para pasar el ícono personalizado a global-map.js
    wp_add_inline_script('global-map-js', 'var distribuidoresIcono = "' . esc_js($icono_default) . '";', 'before');

    // Obtener los datos de los distribuidores
    global $wpdb;
    $distribuidores = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}distribuidores");
    $distribuidores_data = array();

    foreach ($distribuidores as $distribuidor) {
        $distribuidores_data[] = array(
            'nombre' => $distribuidor->nombre,
            'direccion' => $distribuidor->direccion,
            'latitud' => $distribuidor->latitud,
            'longitud' => $distribuidor->longitud,
            'contacto' => $distribuidor->contacto
        );
    }

    // Pasar los datos de distribuidores a JavaScript
    wp_localize_script('global-map-js', 'distribuidoresData', $distribuidores_data);
}
add_action('admin_enqueue_scripts', 'cargar_estilos_admin_distribuidores');


function mostrar_pagina_distribuidores() {include plugin_dir_path(__FILE__) . 'admin/admin-page.php';}


// Encolar scripts y estilos para el frontend
function cargar_estilos_mapa() {
    // Encolar el CSS y JavaScript de Leaflet y estilos personalizados para el frontend
    wp_enqueue_style('distribuidores-css', plugin_dir_url(__FILE__) . 'admin/css/styles.css');
    wp_enqueue_style('leaflet-css', 'https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.css');
    wp_enqueue_script('leaflet-js', 'https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.js', array(), null, true);
    
    // Encolar el script personalizado solo cuando el shortcode del mapa esté presente
}
add_action('wp_enqueue_scripts', 'cargar_estilos_mapa');


// Shortcode para mostrar el mapa en cualquier página del frontend
function mostrar_mapa() {
    // Incluir los estilos y scripts
    cargar_estilos_mapa();

    ob_start(); 
    include plugin_dir_path(__FILE__) . 'views/mapa-shortcode.php';
    return ob_get_clean();
}
add_shortcode('mostrar_mapa', 'mostrar_mapa');


function manejar_formulario_distribuidores() {
    global $wpdb;

    // Verificar que la solicitud venga del formulario correcto y que el usuario tenga permisos
    if (isset($_POST['submit_distribuidor'])) {
        $nombre = sanitize_text_field($_POST['nombre']);
        $direccion = sanitize_text_field($_POST['direccion']);
        $latitud = sanitize_text_field($_POST['latitud']);
        $longitud = sanitize_text_field($_POST['longitud']);
        $contacto = sanitize_text_field($_POST['contacto']);
        $correo = sanitize_email($_POST['correo']);  // Capturamos el campo de correo
        $telefono_agencia = sanitize_text_field($_POST['telefono_agencia']); // Capturamos el campo de teléfono de agencia

        $tiene_taller = isset($_POST['tiene_taller']) ? 1 : 0;

        // Insertar distribuidor en la base de datos
        $wpdb->insert(
            "{$wpdb->prefix}distribuidores",
            array(
                'nombre' => $nombre,
                'direccion' => $direccion,
                'latitud' => $latitud,
                'longitud' => $longitud,
                'contacto' => $contacto,
                'correo' => $correo,  // Guardar el correo
                'telefono_agencia' => $telefono_agencia,  // Guardar el teléfono de agencia
                'tiene_taller' => $tiene_taller
            ),
            array('%s', '%s', '%f', '%f', '%s', '%s', '%s', '%d') // Tipos de los valores: string, float, integer
        );

        // Redirigir para evitar el reenvío del formulario
        wp_redirect(admin_url('admin.php?page=editar-distribuidores'));
        exit;
    }

    // Verificar la eliminación del distribuidor
    if (isset($_POST['eliminar_distribuidor']) && isset($_POST['distribuidor_id']) && current_user_can('manage_options')) {
        $distribuidor_id = intval($_POST['distribuidor_id']);
        if ($distribuidor_id) {
            // Eliminar el distribuidor de la base de datos
            $wpdb->delete("{$wpdb->prefix}distribuidores", array('id' => $distribuidor_id));
        }
        // Redirigir tras la eliminación a la página de Editar Distribuidores
        wp_redirect(admin_url('admin.php?page=editar-distribuidores&delete=success'));
        exit;
    }
}
add_action('admin_post_manejar_formulario_distribuidores', 'manejar_formulario_distribuidores');



function manejar_edicion_distribuidor() {
    global $wpdb;

    // Verificar que la solicitud venga del formulario correcto y que el usuario tenga permisos
    if (isset($_POST['distribuidor_id']) && current_user_can('manage_options')) {
        $distribuidor_id = intval($_POST['distribuidor_id']);

        // Verificar que todos los campos estén presentes
        if (isset($_POST['nombre'], $_POST['direccion'], $_POST['latitud'], $_POST['longitud'], $_POST['contacto'], $_POST['correo'], $_POST['telefono_agencia'])) {
            
            // Recoger los valores del formulario
            $nombre = sanitize_text_field($_POST['nombre']);
            $direccion = sanitize_text_field($_POST['direccion']);
            $latitud = sanitize_text_field($_POST['latitud']);
            $longitud = sanitize_text_field($_POST['longitud']);
            $contacto = sanitize_text_field($_POST['contacto']);
            $correo = sanitize_email($_POST['correo']);
            $telefono_agencia = sanitize_text_field($_POST['telefono_agencia']); // Capturar el teléfono de agencia
            $tiene_taller = isset($_POST['tiene_taller']) ? 1 : 0;

            // Actualizar el distribuidor en la base de datos
            $resultado = $wpdb->update(
                "{$wpdb->prefix}distribuidores",
                array(
                    'nombre' => $nombre,
                    'direccion' => $direccion,
                    'latitud' => $latitud,
                    'longitud' => $longitud,
                    'contacto' => $contacto,
                    'correo' => $correo,
                    'telefono_agencia' => $telefono_agencia,  // Actualizar el teléfono de agencia
                    'tiene_taller' => $tiene_taller
                ),
                array('id' => $distribuidor_id),
                array('%s', '%s', '%f', '%f', '%s', '%s', '%s', '%d'), // Incluir el tipo de dato del teléfono
                array('%d')
            );

            if ($resultado !== false) {
                // Redirigir a la página de Editar Distribuidores en lugar de la página principal
                wp_redirect(admin_url('admin.php?page=editar-distribuidores&edit=success'));
                exit;
            } else {
                // Si hubo un error al actualizar o no se realizó ningún cambio
                wp_redirect(admin_url('admin.php?page=editar-distribuidores&edit=fail'));
                exit;
            }
        } else {
            // Si no todos los campos están presentes
            wp_redirect(admin_url('admin.php?page=editar-distribuidores&edit=missing_fields'));
            exit;
        }
    } else {
        // Si no se proporciona el ID del distribuidor o no se tienen permisos
        wp_redirect(admin_url('admin.php?page=editar-distribuidores&edit=fail'));
        exit;
    }
}
add_action('admin_post_editar_distribuidor', 'manejar_edicion_distribuidor');



function mostrar_pagina_configuracion() {
    include plugin_dir_path(__FILE__) . 'admin/configuracion.php';
}

function configuracion_distribuidores_registrar() {
    register_setting('configuracion_distribuidores', 'distribuidores_icono_default');
}
add_action('admin_init', 'configuracion_distribuidores_registrar');


