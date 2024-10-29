jQuery(document).ready(function($) {
    // Verifica si Leaflet está disponible
    if (typeof L === 'undefined') {
        console.log("Leaflet no está cargado.");
        return;
    }

    // Inicializa el mapa
    var map = L.map('map').setView([23.6345, -102.5528], 5);

    // Añade la capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Icono personalizado usando la URL pasada desde PHP
    var customIcon = new L.Icon({
        iconUrl: distribuidoresIcono, // Variable global pasada desde PHP
      iconSize: [40, 30],
        iconAnchor: [30, 30]
    });

    // Verifica que los datos de distribuidores existan
    if (typeof distribuidoresData !== 'undefined' && distribuidoresData.length > 0) {
        distribuidoresData.forEach(function(distribuidor) {
            L.marker([distribuidor.latitud, distribuidor.longitud], {icon: customIcon})
                .bindPopup("<b>" + distribuidor.nombre + "</b><br>" + distribuidor.direccion + "<br>" + distribuidor.contacto)
                .addTo(map);
        });
    } else {
        console.log("No hay distribuidores disponibles para mostrar en el mapa.");
    }
});
