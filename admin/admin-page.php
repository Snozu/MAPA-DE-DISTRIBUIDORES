<div class="admin-page-distribuidores max-w-4xl mx-auto p-6 ">
    <h1 class="text-3xl font-bold text-center mb-8">Agregar Distribuidores</h1>

    <!-- Contenedor para el formulario y el mapa -->
    <div id="formulario-mapa-container" class="flex flex-col gap-6">
        <!-- Sección del formulario -->
        <div id="distribuidores-form" class="w-full bg-white p-6 rounded-lg shadow-md">
            <form method="post" action="admin-post.php?action=manejar_formulario_distribuidores">
                <!-- Tabla estilo formulario -->
                <table class="min-w-full divide-y divide-gray-200 table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Nombre del distribuidor</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <input type="text" name="nombre" required class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </td>
                        </tr>

                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Dirección</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <input type="text" name="direccion" required class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </td>
                        </tr>

                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Latitud</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <input type="text" name="latitud" required class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </td>
                        </tr>

                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Longitud</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <input type="text" name="longitud" required class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </td>
                        </tr>

                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Correo</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <input type="email" name="correo" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </td>
                        </tr>

                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Contacto (WhatsApp)</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <input type="text" name="contacto" required class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </td>
                        </tr>

                        <!-- Nuevo campo para Teléfono de Agencia -->
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Teléfono de Agencia</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <input type="text" name="telefono_agencia" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Opcional">
                            </td>
                        </tr>

                        <!-- Nuevo checkbox para preguntar si tiene taller -->
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">¿Tiene taller?</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <input type="checkbox" name="tiene_taller" id="tiene_taller" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="text-sm text-gray-600 ml-2">Selecciona si este distribuidor tiene taller</span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Botón de envío -->
                <input type="submit" name="submit_distribuidor" value="Añadir Distribuidor" class="w-full py-3 mt-6 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300">
            </form>
        </div>

        <!-- Sección del mapa -->
        <div id="map-container" class="w-full bg-white p-6 rounded-lg shadow-md">
            <div id="map" class="h-96 w-full rounded-lg border border-gray-300"></div>
        </div>
    </div>
</div>
