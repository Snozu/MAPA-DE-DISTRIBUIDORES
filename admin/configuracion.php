<?php
// Verificar permisos
if (!current_user_can('manage_options')) {
    return;
}

// Guardar configuraciones si el formulario fue enviado
if (isset($_POST['guardar_configuracion'])) {
    $icono_default = sanitize_text_field($_POST['icono_default']);
    $color_fondo_boton_activo = sanitize_text_field($_POST['color_fondo_boton_activo']);
    $color_texto_boton_activo = sanitize_text_field($_POST['color_texto_boton_activo']);
    $color_hover_boton = sanitize_text_field($_POST['color_hover_boton']);
    $color_texto_boton_inactivo = sanitize_text_field($_POST['color_texto_boton_inactivo']);
    $color_hover_lista = sanitize_text_field($_POST['color_hover_lista']);  // Nueva opción de color para el hover de la lista

    // Actualizar las opciones en la base de datos
    update_option('distribuidores_icono_default', $icono_default);
    update_option('color_fondo_boton_activo', $color_fondo_boton_activo);
    update_option('color_texto_boton_activo', $color_texto_boton_activo);
    update_option('color_hover_boton', $color_hover_boton);
    update_option('color_texto_boton_inactivo', $color_texto_boton_inactivo);
    update_option('color_hover_lista', $color_hover_lista);  // Guardar el nuevo valor de hover de la lista

    echo '<div class="notice notice-success is-dismissible"><p>Configuración guardada con éxito.</p></div>';
}

// Obtener la configuración actual
$icono_default = get_option('distribuidores_icono_default', 'https://motocarrostvs.triumphmexico.shop/wp-content/uploads/2024/09/mototaxi-1.png');
$color_fondo_boton_activo = get_option('color_fondo_boton_activo', '#0066ff');  // Color por defecto
$color_texto_boton_activo = get_option('color_texto_boton_activo', '#ffffff');  // Color por defecto
$color_hover_boton = get_option('color_hover_boton', '#333333');  // Color por defecto
$color_texto_boton_inactivo = get_option('color_texto_boton_inactivo', '#333333');  // Color por defecto
$color_hover_lista = get_option('color_hover_lista', '#a30000');  // Color por defecto para el hover de la lista
?>

<div class="wrap">
    <h1>Configuraciones de Distribuidores</h1>

    <form method="post">
        <!-- Configuración del ícono -->
        <h2>Ícono por Defecto</h2>
        <input type="text" name="icono_default" value="<?php echo esc_attr($icono_default); ?>" class="regular-text" />
        <p class="description">Ingresa la URL del ícono que quieres usar por defecto en los mapas de distribuidores.</p>

        <!-- Configuración de colores -->
        <h2>Configuración de Colores</h2>

        <div class="color-grid">
            <!-- Fondo del botón activo -->
            <div class="color-box" style="grid-column: span 2;">
                <label for="color_fondo_boton_activo" class="color-label">Fondo Botón Activo</label>
                <input type="color" id="color_fondo_boton_activo" name="color_fondo_boton_activo" value="<?php echo esc_attr($color_fondo_boton_activo); ?>" />
                <span class="color-code"><?php echo esc_attr($color_fondo_boton_activo); ?></span>
            </div>

            <!-- Texto del botón activo -->
            <div class="color-box">
                <label for="color_texto_boton_activo" class="color-label">Texto Botón Activo</label>
                <input type="color" id="color_texto_boton_activo" name="color_texto_boton_activo" value="<?php echo esc_attr($color_texto_boton_activo); ?>" />
                <span class="color-code"><?php echo esc_attr($color_texto_boton_activo); ?></span>
            </div>

            <!-- Color hover del botón -->
            <div class="color-box">
                <label for="color_hover_boton" class="color-label">Color Hover Botón</label>
                <input type="color" id="color_hover_boton" name="color_hover_boton" value="<?php echo esc_attr($color_hover_boton); ?>" />
                <span class="color-code"><?php echo esc_attr($color_hover_boton); ?></span>
            </div>

            <!-- Texto del botón inactivo -->
            <div class="color-box">
                <label for="color_texto_boton_inactivo" class="color-label">Texto Botón Inactivo</label>
                <input type="color" id="color_texto_boton_inactivo" name="color_texto_boton_inactivo" value="<?php echo esc_attr($color_texto_boton_inactivo); ?>" />
                <span class="color-code"><?php echo esc_attr($color_texto_boton_inactivo); ?></span>
            </div>

            <!-- Nueva opción para el hover de la lista -->
            <div class="color-box">
                <label for="color_hover_lista" class="color-label">Color Hover Lista</label>
                <input type="color" id="color_hover_lista" name="color_hover_lista" value="<?php echo esc_attr($color_hover_lista); ?>" />
                <span class="color-code"><?php echo esc_attr($color_hover_lista); ?></span>
            </div>
        </div>

        <!-- Botón para guardar la configuración -->
        <?php submit_button('Guardar Configuración', 'primary', 'guardar_configuracion'); ?>
    </form>
</div>

<!-- Estilos CSS para el diseño inspirado -->
<style>
/* Configuración general */
body {
    background-color: #f4f4f4;
    font-family: 'Arial', sans-serif;
}

.wrap {
    max-width: 1200px;
    margin: 0 auto;
}

.wrap h1 {
    font-size: 2rem;
    font-weight: bold;
    color: #000000;
    margin-bottom: 20px;
}

.color-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-template-rows: repeat(2, 1fr);
    gap: 20px;
    margin-top: 20px;
    padding: 10px;
    background-color: #f4f4f4;
    border-radius: 15px;
}

.color-box {
    position: relative;
    padding: 10px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    transition: transform 0.3s ease-in-out;
    background-color: transparent;
    border-radius: 10px;
    cursor: pointer;
}

.color-box:hover {
    transform: scale(1.05);
}

.color-box input[type="color"] {
    width: 100%;
    height: 80px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-bottom: 5px;
}

.color-label {
    font-weight: bold;
    font-size: 0.9rem;
    color: #333;
}

.color-code {
    font-size: 0.8rem;
    color: #666;
}

input[type="submit"].button-primary {
    background-color: #000000;
    color: #fff;
    font-weight: bold;
    border-radius: 12px;
    padding: 10px 30px;
    font-size: 0.9rem;
    transition: background-color 0.3s ease;
    cursor: pointer;
    border: none;
    margin-top: 20px;
}

input[type="submit"].button-primary:hover {
    background-color: #000000;
}

.regular-text {
    border-radius: 8px;
    padding: 10px;
    font-size: 0.9rem;
    border: 1px solid #ddd;
    width: 100%;
    margin-bottom: 10px;
}

.description {
    font-size: 0.8rem;
    color: #999;
    margin-top: 5px;
}

/* Dinamismo para el hover de la lista */
.shortcode-mapa-lista li:hover {
    background-color: <?php echo esc_attr($color_hover_lista); ?>;
    color: white;
}

/* Responsivo */
@media (max-width: 768px) {
    .color-grid {
        grid-template-columns: repeat(1, 1fr);
        grid-template-rows: repeat(4, 1fr);
    }

    .color-box:nth-child(1),
    .color-box:nth-child(4) {
        grid-column: span 1;
        grid-row: span 1;
    }
}
</style>
