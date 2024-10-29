<div class="admin-page-distribuidores max-w-7xl mx-auto p-6 bg-gray-100 rounded-lg shadow-lg">
    <h1 class="text-3xl font-bold text-center mb-8 text-blue-700">Editar Distribuidores</h1>

    <!-- Sección de la lista de distribuidores -->
    <div id="distribuidores-lista" class="mt-8 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Distribuidores Registrados</h2>

        <!-- Buscador de distribuidores -->
        <div class="mb-6">
            <input type="text" id="buscarDistribuidor" placeholder="Buscar distribuidor..." class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" onkeyup="filtrarDistribuidores()">
        </div>

        <!-- Tabla de distribuidores con scroll -->
        <div class="overflow-auto max-h-96">
            <?php
            global $wpdb;
            $distribuidores = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}distribuidores");

            if ($distribuidores) {
                echo '<table class="min-w-full table-auto border-collapse border border-gray-300">';
                echo '<thead class="bg-blue-100 text-blue-700"><tr><th class="border border-gray-300 px-4 py-2 text-left">Nombre</th><th class="border border-gray-300 px-4 py-2 text-left">Dirección</th><th class="border border-gray-300 px-4 py-2 text-left">Contacto</th><th class="border border-gray-300 px-4 py-2 text-left">Acción</th></tr></thead>';
                echo '<tbody id="listaDistribuidores">';
                foreach ($distribuidores as $distribuidor) {
                  echo '<tr class="hover:bg-gray-100">';
                  echo '<td class="border border-gray-300 px-4 py-2 text-gray-700">' . esc_html($distribuidor->nombre) . '</td>';
                  echo '<td class="border border-gray-300 px-4 py-2 text-gray-700">' . esc_html($distribuidor->direccion) . '</td>';
                  echo '<td class="border border-gray-300 px-4 py-2 text-gray-700">' . esc_html($distribuidor->contacto) . '</td>';
                  echo '<td class="border border-gray-300 px-4 py-2 flex space-x-2">';

                    // Botón de eliminar
                    echo '<form method="post" action="admin-post.php?action=manejar_formulario_distribuidores" class="inline">';
                    echo '<input type="hidden" name="distribuidor_id" value="' . esc_attr($distribuidor->id) . '">';
                    echo '<input type="hidden" name="eliminar_distribuidor" value="1">';
                    echo '<input type="submit" value="Eliminar" class="py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-300">';
                    echo '</form>';

                    // Botón de editar, incluyendo los nuevos datos

                    echo '<button class="py-2 px-4 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition duration-300" ';
                    echo 'onclick="mostrarPopupEdicion(' . esc_js($distribuidor->id) . ', \'' . esc_js($distribuidor->nombre) . '\', \'' . esc_js($distribuidor->direccion) . '\', \'' . esc_js($distribuidor->contacto) . '\', \'' . esc_js($distribuidor->latitud) . '\', \'' . esc_js($distribuidor->longitud) . '\', \'' . esc_js($distribuidor->correo) . '\', ' . esc_js($distribuidor->tiene_taller) . ', \'' . esc_js($distribuidor->telefono_agencia) . '\');">Editar</button>';

    echo '</td>';
    echo '</tr>';
}
                echo '</tbody></table>';
            } else {
                echo '<p class="text-gray-600">No hay distribuidores registrados actualmente.</p>';
            }
            ?>
        </div>
    </div>

    <!-- Popup de edición -->
  <div id="popup-editar" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-lg w-full">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Editar Distribuidor</h2>
        <form id="editarDistribuidorForm" method="post" action="admin-post.php?action=editar_distribuidor">
            <input type="hidden" name="distribuidor_id" id="distribuidor_id">

            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre:</label>
            <input type="text" name="nombre" id="nombre" class="block w-full px-4 py-2 mb-4 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>

            <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">Dirección:</label>
            <input type="text" name="direccion" id="direccion" class="block w-full px-4 py-2 mb-4 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>

            <label for="latitud" class="block text-sm font-medium text-gray-700 mb-2">Latitud:</label>
            <input type="text" name="latitud" id="latitud" class="block w-full px-4 py-2 mb-4 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>

            <label for="longitud" class="block text-sm font-medium text-gray-700 mb-2">Longitud:</label>
            <input type="text" name="longitud" id="longitud" class="block w-full px-4 py-2 mb-4 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>

            <label for="contacto" class="block text-sm font-medium text-gray-700 mb-2">Contacto:</label>
            <input type="text" name="contacto" id="contacto" class="block w-full px-4 py-2 mb-4 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" >
            
            <label for="correo" class="block text-sm font-medium text-gray-700 mb-2">Correo:</label>
            <input type="email" name="correo" id="correo" class="block w-full px-4 py-2 mb-4 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">

            <label for="telefono_agencia" class="block text-sm font-medium text-gray-700 mb-2">Teléfono de Agencia:</label>
            <input type="text" name="telefono_agencia" id="telefono_agencia" class="block w-full px-4 py-2 mb-4 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">

            <div class="flex items-center mb-4">
                <label for="tiene_taller" class="mr-2 text-sm font-medium text-gray-700">¿Tiene taller?</label>
                <input type="checkbox" name="tiene_taller" id="tiene_taller" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            </div>

            <div class="flex justify-between">
                <button type="button" onclick="cerrarPopupEdicion()" class="py-2 px-4 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-300">Cancelar</button>
                <input type="submit" value="Guardar Cambios" class="py-2 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-300">
            </div>
        </form>
    </div>
</div>


</div>

<script>
    function mostrarPopupEdicion(distribuidorId, nombre, direccion, contacto, latitud, longitud, correo, tieneTaller, telefono_agencia) {
    document.getElementById('distribuidor_id').value = distribuidorId;
    document.getElementById('nombre').value = nombre || '';
    document.getElementById('direccion').value = direccion || '';
    document.getElementById('contacto').value = contacto || '';
    document.getElementById('latitud').value = latitud || '';
    document.getElementById('longitud').value = longitud || '';
    document.getElementById('correo').value = correo || '';
    document.getElementById('telefono_agencia').value = telefono_agencia || '';  // Asignar el valor del teléfono de agencia
    
    // Manejar el valor del checkbox "tiene_taller"
    document.getElementById('tiene_taller').checked = tieneTaller == 1;

    // Mostrar el popup
    document.getElementById('popup-editar').classList.remove('hidden');
}


    function cerrarPopupEdicion() {
        // Ocultar el popup
        document.getElementById('popup-editar').classList.add('hidden');
    }

    // Función para buscar distribuidores
    function filtrarDistribuidores() {
        const buscar = document.getElementById("buscarDistribuidor").value.toLowerCase();
        const filas = document.querySelectorAll("#listaDistribuidores tr");

        filas.forEach((fila) => {
            const nombreDistribuidor = fila.querySelector("td").textContent.toLowerCase();
            fila.style.display = nombreDistribuidor.includes(buscar) ? "" : "none";
        });
    }
</script>


