jQuery(document).ready(function($) {

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

    // Filtrar distribuidores y sus marcadores en el mapa
    $('#buscador').on('input', function() {
        var filter = $(this).val().toLowerCase();

        // Filtrar lista de distribuidores
        for (var i = 0; i < items.length; i++) {
            var nombre = items[i].getAttribute('data-nombre');
            var direccion = items[i].getAttribute('data-direccion');
            if (nombre.includes(filter) || direccion.includes(filter)) {
                items[i].style.display = "";  // Mostrar el distribuidor en la lista
            } else {
                items[i].style.display = "none";  // Ocultar el distribuidor en la lista
            }
        }

        // Filtrar marcadores en el mapa
        markers.forEach(function(markerObj) {
            var distribuidor = markerObj.distribuidor;
            if (distribuidor.nombre.toLowerCase().includes(filter) || distribuidor.direccion.toLowerCase().includes(filter)) {
                markerObj.marker.addTo(window.map); // Mostrar marcador
            } else {
                window.map.removeLayer(markerObj.marker); // Ocultar marcador
            }
        });
    });
});
