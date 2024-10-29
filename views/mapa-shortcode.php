<?php
global $wpdb;

// Obtener la URL del ícono predeterminado desde las opciones
$icono_default = get_option('distribuidores_icono_default', 'https://motocarrostvs.triumphmexico.shop/wp-content/uploads/2024/09/Recurso-1@4x.png');

// Obtener los distribuidores desde la base de datos
$distribuidores = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}distribuidores");

// Obtener los colores personalizados de la base de datos
$color_fondo_activo = get_option('color_fondo_boton_activo', '#0066ff');  // Color por defecto
$color_texto_activo = get_option('color_texto_boton_activo', '#ffffff');  // Color por defecto
$color_hover = get_option('color_hover_boton', '#333333');  // Color por defecto
$color_texto_inactivo = get_option('color_texto_boton_inactivo', '#333333');  // Color por defecto
$color_hover_lista = get_option('color_hover_lista', '#a30000');  // Color por defecto para el hover de la lista
?>


<div class="mapa-plugin-container">

   <!-- Buscador -->
<div class="shortcode-mapa-buscador-container">
    
    <!--<div class="icono-texto">
        Localiza tu distribuidor <i class="fas fa-map-marker-alt"></i>
    </div> --> 
    
    <div class="input-container">
        <input type="text" id="buscador" placeholder="Encuentra tu distribuidor autorizado más cercano.">
        <i class="fas fa-search icono-input"></i>
    </div>
</div>




    
<div class="shortcode-mapa-container">
 

    <!-- Contenedor para el contador de distribuidores y el switch de vista -->
    <div class="header-info-container">
        <!-- Mostrar el número de distribuidores -->
        <div class="contador-distribuidores">
            <strong><?php echo count($distribuidores); ?></strong> Distribuidores Encontrados
        </div>

        <!-- Opciones de vista (switch) -->
        <div class="view-options mb-4">
            <button id="mapaConLista" class="btn-view active">Mapa con Lista</button>
            <button id="soloLista" class="btn-view">Solo Lista</button>
        </div>
    </div>

    <!-- Botones de filtrado por tipo de distribuidor -->
    <div class="filtro-distribuidores mb-4">
        <button id="todosDistribuidores" class="btn-filtro active">Distribuidores</button>
        <button id="soloTaller" class="btn-filtro">Centros de servicios</button>
    </div>

    <div class="shortcode-mapa-lista-mapa-container" id="vistaMapaConLista">
        <!-- Lista de distribuidores para la vista de Mapa con Lista -->
        <div class="shortcode-mapa-lista">
            <ul>
                <?php foreach ($distribuidores as $distribuidor) { 
                    // Formatear el número de teléfono para el enlace de WhatsApp
                    $whatsappLink = "https://wa.me/52" . preg_replace('/\D/', '', $distribuidor->contacto); // Ajusta el código de país si es necesario
                ?>
               <li class="distribuidor-item" data-latitud="<?php echo $distribuidor->latitud; ?>" 
                data-longitud="<?php echo $distribuidor->longitud; ?>" 
                data-nombre="<?php echo strtolower($distribuidor->nombre); ?>" 
                data-direccion="<?php echo strtolower($distribuidor->direccion); ?>"
                data-tiene-taller="<?php echo $distribuidor->tiene_taller; ?>">
                <strong><?php echo esc_html($distribuidor->nombre); ?></strong><br>
                <?php echo esc_html($distribuidor->direccion); ?><br>

                <?php if (!empty($distribuidor->contacto)) { ?>
                <!-- Enlace de WhatsApp solo si el contacto existe -->
                <a href="<?php echo esc_url($whatsappLink); ?>" target="_blank" class="whatsapp-link">
                <?php echo esc_html($distribuidor->contacto); ?>
                </a><br>
                <?php } ?>

                <!-- Enlace de 'Cómo llegar' a Google Maps -->
                <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $distribuidor->latitud; ?>,<?php echo $distribuidor->longitud; ?>" target="_blank" class="como-llegar-link">
                Cómo llegar
                </a><br>

                <?php if (!empty($distribuidor->correo)) { ?>
                <a href="mailto:<?php echo esc_attr($distribuidor->correo); ?>"><?php echo esc_html($distribuidor->correo); ?></a><br>
                <?php } ?>
                </li>
                
                <?php } ?>
            </ul>
        </div>

        <!-- Mapa de distribuidores -->
        <div class="shortcode-mapa-mapa-container">
            <div id="map" class="shortcode-mapa-mapa"></div>
        </div>
    </div>

            <!-- Vista de solo lista (oculta por defecto) -->
           <div class="shortcode-lista-bloques hidden" id="vistaSoloLista">
    <ul class="distribuidor-grid">
        <?php foreach ($distribuidores as $distribuidor) { 
            $whatsappLink = "https://wa.me/52" . preg_replace('/\D/', '', $distribuidor->contacto); 
            $mapsLink = "https://www.google.com/maps/dir/?api=1&destination={$distribuidor->latitud},{$distribuidor->longitud}";
        ?>
        <li class="distribuidor-item-bloque" 
            data-nombre="<?php echo strtolower($distribuidor->nombre); ?>" 
            data-direccion="<?php echo strtolower($distribuidor->direccion); ?>"
            data-tiene-taller="<?php echo $distribuidor->tiene_taller; ?>">
            <strong><?php echo esc_html($distribuidor->nombre); ?></strong><br>
            <?php echo esc_html($distribuidor->direccion); ?><br>
            
            <!-- Mostrar enlace de WhatsApp solo si el contacto existe -->
            <?php if (!empty($distribuidor->contacto)) { ?>
                <a href="<?php echo esc_url($whatsappLink); ?>" target="_blank" class="whatsapp-link">
                    <?php echo esc_html($distribuidor->contacto); ?>
                </a><br>
            <?php } ?>
            
            <!-- Enlace de "Cómo llegar" a Google Maps -->
            <a href="<?php echo esc_url($mapsLink); ?>" target="_blank" class="como-llegar-link">
                Cómo llegar
            </a><br>

            <!-- Mostrar el correo solo si existe -->
            <?php if (!empty($distribuidor->correo)) { ?>
                <a href="mailto:<?php echo esc_attr($distribuidor->correo); ?>"><?php echo esc_html($distribuidor->correo); ?></a><br>
            <?php } ?>
        </li>
        <?php } ?>
    </ul>
</div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
   // Crear el mapa y deshabilitar la atribución predeterminada
    var map = L.map('map', {
        attributionControl: false // Desactivar la atribución predeterminada de Leaflet
    }).setView([23.6345, -102.5528], 5);

    // Añadir la capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    // Agregar tu propia atribución con el logo de Grupo Motomex
    L.control.attribution({
        prefix: false // Remover cualquier prefijo predeterminado de Leaflet
    }).addAttribution('<a href="https://www.grupomotomex.com.mx" target="_blank"><img src="https://agroequipos.triumphmexico.shop/wp-content/uploads/2024/09/LOGO-horizontal.png" alt="Grupo Motomex" style="height: 10px; vertical-align: middle;"></a> Grupo Motomex').addTo(map);

    var iconUrl = '<?php echo esc_js($icono_default); ?>';
    var customIcon = new L.Icon({
        iconUrl: iconUrl,
        iconSize: [40, 30],
        iconAnchor: [30, 30]
    });

    var markers = [];
    var closestMarkers = []; // Para almacenar los marcadores más cercanos

    <?php foreach ($distribuidores as $distribuidor) { ?>
    var popupContent = "<b><?php echo esc_js($distribuidor->nombre); ?></b><br><?php echo esc_js($distribuidor->direccion); ?><br>";
    
    // Añadir el contacto solo si existe
    <?php if (!empty($distribuidor->contacto)) { ?>
        popupContent += "<?php echo esc_js($distribuidor->contacto); ?><br>";
    <?php } ?>

    // Añadir el correo solo si existe
    <?php if (!empty($distribuidor->correo)) { ?>
        popupContent += "<?php echo esc_js($distribuidor->correo); ?><br>";
    <?php } ?>

    // Enlace de "Cómo llegar" a Google Maps
    popupContent += "<a href='https://www.google.com/maps/dir/?api=1&destination=<?php echo $distribuidor->latitud; ?>,<?php echo $distribuidor->longitud; ?>' target='_blank' class='como-llegar-link'>Cómo llegar</a>";

    var marker = L.marker([<?php echo $distribuidor->latitud; ?>, <?php echo $distribuidor->longitud; ?>], {icon: customIcon})
    .bindPopup(popupContent)
    .addTo(map);

    marker.distribuidorData = {
        latitud: "<?php echo $distribuidor->latitud; ?>",
        longitud: "<?php echo $distribuidor->longitud; ?>",
        tieneTaller: "<?php echo $distribuidor->tiene_taller; ?>"  // Agregar dato de taller
    };

    markers.push(marker);
    <?php } ?>

    window.map = map;

    // Solicitar la ubicación del usuario
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var lon = position.coords.longitude;

            // Centrar el mapa en la ubicación del usuario
            map.setView([lat, lon], 13);

            // Mostrar los distribuidores dentro de un radio de 120 km sin ocultar ninguno
            resaltarDistribuidoresCercanos(lat, lon, 120); // Aquí se usa un radio de 120 km

        }, function() {
            alert("No se pudo obtener tu ubicación.");
        });
    } else {
        alert("Tu navegador no soporta la API de geolocalización.");
    }

    // Función para calcular la distancia entre dos puntos (latitud y longitud) en kilómetros
    function calcularDistancia(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radio de la Tierra en km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = 
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        const distancia = R * c; // Distancia en km
        return distancia;
    }

    // Función para resaltar distribuidores cercanos dentro de un radio dado (no oculta los demás)
    function resaltarDistribuidoresCercanos(lat, lon, radio) {
        var itemsMapa = document.querySelectorAll('.distribuidor-item');
        var itemsLista = document.querySelectorAll('.distribuidor-item-bloque'); // En la vista "Solo Lista"

        // Limpiar los marcadores más cercanos del mapa (si ya estaban en el mapa)
        closestMarkers.forEach(function(marker) {
            if (map.hasLayer(marker)) {
                map.removeLayer(marker);
            }
        });
        closestMarkers = []; // Limpiar la lista de marcadores más cercanos

        itemsMapa.forEach(function(item) {
            var distribuidorLat = parseFloat(item.getAttribute('data-latitud'));
            var distribuidorLon = parseFloat(item.getAttribute('data-longitud'));

            var distancia = calcularDistancia(lat, lon, distribuidorLat, distribuidorLon);

            markers.forEach(function(marker) {
                if (marker.distribuidorData.latitud == distribuidorLat && marker.distribuidorData.longitud == distribuidorLon) {
                    marker.addTo(map); // Mostrar todos los marcadores
                    if (distancia <= radio) {
                        closestMarkers.push(marker); // Guardar los más cercanos
                    }
                }
            });
        });
    }

    // Función para centrar el mapa al hacer clic en un distribuidor
    var itemsMapa = document.getElementsByClassName('distribuidor-item');
    for (var i = 0; i < itemsMapa.length; i++) {
        itemsMapa[i].addEventListener('click', function() {
            var latitud = this.getAttribute('data-latitud');
            var longitud = this.getAttribute('data-longitud');

            // Remover clase 'active' de todos los distribuidores
            for (var j = 0; j < itemsMapa.length; j++) {
                itemsMapa[j].classList.remove('active');
            }

            // Añadir la clase 'active' solo al distribuidor clickeado
            this.classList.add('active');

            // Centrar el mapa en el distribuidor clickeado
            map.setView([latitud, longitud], 13);
        });
    }

    // Función para filtrar distribuidores por taller
    function filtrarPorTaller(tieneTaller) {
        var itemsMapa = document.querySelectorAll('.distribuidor-item');
        var itemsLista = document.querySelectorAll('.distribuidor-item-bloque'); // En la vista "Solo Lista"

        itemsMapa.forEach(function(item) {
            var latitud = item.getAttribute('data-latitud');
            var longitud = item.getAttribute('data-longitud');
            var taller = item.getAttribute('data-tiene-taller') === '1';

            if (tieneTaller) {
                if (taller) {
                    item.style.display = ''; // Mostrar distribuidor con taller
                } else {
                    item.style.display = 'none'; // Ocultar distribuidores sin taller
                }
            } else {
                item.style.display = ''; // Mostrar todos los distribuidores
            }

            // Mostrar en el mapa solo los distribuidores con taller
            markers.forEach(function(marker) {
                if (marker.distribuidorData.latitud === latitud && marker.distribuidorData.longitud === longitud) {
                    if (tieneTaller && !taller) {
                        map.removeLayer(marker); // Quitar del mapa los sin taller
                    } else {
                        marker.addTo(map); // Añadir los distribuidores con taller al mapa
                    }
                }
            });
        });

        // Filtrado en la lista sin mapa
        itemsLista.forEach(function(item) {
            var taller = item.getAttribute('data-tiene-taller') === '1';

            if (tieneTaller) {
                if (taller) {
                    item.style.display = ''; // Mostrar distribuidor con taller
                } else {
                    item.style.display = 'none'; // Ocultar distribuidor sin taller
                }
            } else {
                item.style.display = ''; // Mostrar todos los distribuidores
            }
        });
    }

    // Filtro de distribuidores con taller
    document.getElementById("soloTaller").addEventListener('click', function() {
        document.getElementById("todosDistribuidores").classList.remove('active');
        this.classList.add('active');
        filtrarPorTaller(true); // Filtrar solo distribuidores con taller
    });

    // Mostrar todos los distribuidores
    document.getElementById("todosDistribuidores").addEventListener('click', function() {
        document.getElementById("soloTaller").classList.remove('active');
        this.classList.add('active');
        filtrarPorTaller(false); // Mostrar todos los distribuidores
    });

    // Buscador
    function filtrarDistribuidores() {
        var input = document.getElementById('buscador').value.toLowerCase();
        var itemsMapa = document.querySelectorAll('.distribuidor-item');
        var itemsLista = document.querySelectorAll('.distribuidor-item-bloque');

        // Filtrar distribuidores en el mapa
        itemsMapa.forEach(function(item) {
            var nombre = item.getAttribute('data-nombre');
            var direccion = item.getAttribute('data-direccion');
            var latitud = item.getAttribute('data-latitud');
            var longitud = item.getAttribute('data-longitud');

            if (nombre.includes(input) || direccion.includes(input)) {
                item.style.display = '';

                markers.forEach(function(marker) {
                    if (marker.distribuidorData.latitud === latitud && marker.distribuidorData.longitud === longitud) {
                        marker.addTo(map); // Agregar marcador al mapa
                    }
                });
            } else {
                item.style.display = 'none'; // Ocultar distribuidor
            }
        });

        // Filtrar distribuidores en la vista de lista
        itemsLista.forEach(function(item) {
            var nombre = item.getAttribute('data-nombre');
            var direccion = item.getAttribute('data-direccion');

            if (nombre.includes(input) || direccion.includes(input)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    document.getElementById('buscador').addEventListener('keyup', filtrarDistribuidores);

    // Cambiar vistas entre "Mapa con Lista" y "Solo Lista"
    document.getElementById("mapaConLista").addEventListener('click', function() {
        document.getElementById("vistaMapaConLista").classList.remove('hidden');
        document.getElementById("vistaSoloLista").classList.add('hidden');
        document.getElementById("mapaConLista").classList.add('active');
        document.getElementById("soloLista").classList.remove('active');
    });

    document.getElementById("soloLista").addEventListener('click', function() {
        document.getElementById("vistaMapaConLista").classList.add('hidden');
        document.getElementById("vistaSoloLista").classList.remove('hidden');
        document.getElementById("soloLista").classList.add('active');
        document.getElementById("mapaConLista").classList.remove('active');
    });
});
</script>



<style>
   .mapa-plugin-container .filtro-distribuidores .btn-filtro.active {
    background-color: <?php echo esc_attr($color_fondo_activo); ?>;
    color: <?php echo esc_attr($color_texto_activo); ?>;
}

.mapa-plugin-container .filtro-distribuidores .btn-filtro:hover {
    background-color: <?php echo esc_attr($color_hover); ?>;
    color: white;
}

.mapa-plugin-container .filtro-distribuidores .btn-filtro {
    color: <?php echo esc_attr($color_texto_inactivo); ?>;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.mapa-plugin-container .shortcode-mapa-lista li:hover,
.mapa-plugin-container .distribuidor-item-bloque:hover {
    background-color: <?php echo esc_attr($color_hover_lista); ?>;
    color: white;
}

.mapa-plugin-container .shortcode-mapa-lista li.active {
    background-color: <?php echo esc_attr($color_hover_lista); ?>;
    color: white;
}

@media (max-width: 768px) {
    .mapa-plugin-container .shortcode-mapa-lista ul, 
    .mapa-plugin-container .shortcode-lista-bloques ul {
        max-height: 300px;
        overflow-y: auto;
    }
}

</style>
