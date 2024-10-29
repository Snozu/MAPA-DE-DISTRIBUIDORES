jQuery(document).ready(function($) {
    // Verificar si Leaflet está disponible
    if (typeof L === 'undefined') {
        console.log("Leaflet no está cargado.");
        return;
    }

    // Inicializar el mapa
    var map = L.map('map').setView([23.6345, -102.5528], 5);

    // Añadir la capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Usar el ícono predeterminado pasado desde PHP
    var customIcon = new L.Icon({
        iconUrl: mapaDistribuidores.icono_default,
    iconSize: [40, 30],
        iconAnchor: [30, 30]
    });

    // Verificar que los datos de distribuidores existan
    if (typeof mapaDistribuidores.distribuidores !== 'undefined' && mapaDistribuidores.distribuidores.length > 0) {
        mapaDistribuidores.distribuidores.forEach(function(distribuidor) {
            // Agregar cada distribuidor como un marcador
            L.marker([distribuidor.latitud, distribuidor.longitud], {icon: customIcon})
                .bindPopup("<b>" + distribuidor.nombre + "</b><br>" + distribuidor.direccion + "<br>" + distribuidor.contacto)
                .addTo(map);
        });
    } else {
        console.log("No hay distribuidores disponibles para mostrar en el mapa.");
    }

    // Manejo del clic en los elementos de la lista
    var items = document.getElementsByClassName('distribuidor-item');
    for (var i = 0; i < items.length; i++) {
        items[i].addEventListener('click', function() {
            // Remover la clase 'active' de todos los elementos
            for (var j = 0; j < items.length; j++) {
                items[j].classList.remove('active');
            }

            // Añadir la clase 'active' al elemento clickeado
            this.classList.add('active');

            // Centrar el mapa en la ubicación del distribuidor clickeado
            var latitud = this.getAttribute('data-latitud');
            var longitud = this.getAttribute('data-longitud');
            window.map.setView([latitud, longitud], 13);
        });
    }



// Filtrar distribuidores en la lista y en el mapa
function filtrarDistribuidores() {
    var input = document.getElementById('buscador');
    var filter = input.value.toLowerCase();
    var items = document.getElementsByClassName('distribuidor-item');

    // Quitar todos los marcadores del mapa
    markers.forEach(function(marker) {
        window.map.removeLayer(marker);
    });

    // Recorrer los items (distribuidores)
    for (var i = 0; i < items.length; i++) {
        var nombre = items[i].getAttribute('data-nombre');
        var direccion = items[i].getAttribute('data-direccion');
        var lat = items[i].getAttribute('data-latitud');
        var lng = items[i].getAttribute('data-longitud');

        if (nombre.includes(filter) || direccion.includes(filter)) {
            items[i].style.display = ""; // Mostrar el distribuidor en la lista

            // Filtrar marcadores coincidentes
            markers.forEach((function(marker, lat, lng) {
                return function(marker) {
                    if (marker.distribuidorData.latitud == lat && marker.distribuidorData.longitud == lng) {
                        marker.addTo(window.map); // Añadir los marcadores coincidentes al mapa
                    }
                };
            })(marker, lat, lng));
        } else {
            items[i].style.display = "none"; // Ocultar el distribuidor de la lista
        }
    }
}


    // Asociar el evento keyup al buscador
    $('#buscador').on('keyup', filtrarDistribuidores);
});
