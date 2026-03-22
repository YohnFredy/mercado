<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Productos')] class extends Component
{
    //
};
?>

<div class=" flex">
 <!-- SIDEBAR -->
    <aside class="w-64 bg-white border-r p-4 space-y-6">

        <!-- Buscador -->
        <div>
            <input type="text" placeholder="Buscar producto"
                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200">
        </div>

        <!-- Filtro precio -->
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Filtrar por precio</h3>
            <div class="flex gap-2">
                <input type="text" placeholder="Min"
                    class="w-1/2 border rounded px-2 py-1 text-sm">
                <input type="text" placeholder="Max"
                    class="w-1/2 border rounded px-2 py-1 text-sm">
            </div>
        </div>

        <!-- Categorías -->
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Subfamilia</h3>
            <div class="space-y-1 text-sm text-gray-600">
                <label class="flex items-center gap-2">
                    <input type="checkbox"> Comida preparada
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox"> Lasaña
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox"> Dedos
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox"> Empanadas
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox"> Nuggets
                </label>
            </div>
        </div>

        <!-- Marcas -->
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Marca</h3>
            <div class="space-y-1 text-sm text-gray-600">
                <label class="flex items-center gap-2">
                    <input type="checkbox"> Zenu
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox"> KListo
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox"> McCain
                </label>
            </div>
        </div>

    </aside>

    <!-- MAIN -->
    <main class="flex-1 p-6 space-y-6">

        <!-- Título -->
        <div>
            <h1 class="text-xl font-bold text-gray-800">ALIMENTOS PRECOCIDOS</h1>
            <p class="text-sm text-gray-500">Productos congelados / Precocidos</p>
        </div>

        <!-- Banner -->
        <div class="relative bg-orange-100 rounded-xl overflow-hidden h-40 flex items-center">
            <img src="https://images.unsplash.com/photo-1604908176997-125f25cc6f3d"
                 class="absolute inset-0 w-full h-full object-cover opacity-70">

            <h2 class="relative text-3xl font-bold text-orange-600 ml-6">
                Alimentos Precocidos
            </h2>
        </div>

        <!-- Orden -->
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600">
                Ordenar por:
                <select class="border rounded px-2 py-1 text-sm">
                    <option>Ordenar</option>
                </select>
            </div>

            <div class="text-sm text-gray-600">
                Ver:
                <select class="border rounded px-2 py-1 text-sm">
                    <option>Ver</option>
                </select>
            </div>
        </div>

        <!-- PRODUCTOS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Producto -->
            <div class="bg-white rounded-xl shadow p-4 space-y-3">
                <img src="https://exitocol.vtexassets.com/arquivos/ids/28955924/Arroz-Diana-500-gr-445117_a.jpg?v=638864002494600000"
                     class="mx-auto h-40 object-contain">

                <div>
                    <p class="text-xs text-gray-500">Precio Ahora</p>
                    <p class="text-xl font-bold text-blue-600">$24.100</p>
                    <p class="text-xs text-gray-400">IVA incluido</p>
                </div>

                <h3 class="text-sm text-gray-700">
                    Dedos de queso KListo x10
                </h3>

                <!-- Cantidad -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center border rounded">
                        <button class="px-2">-</button>
                        <span class="px-3">1</span>
                        <button class="px-2">+</button>
                    </div>

                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-1 rounded">
                        Comprar
                    </button>
                </div>
            </div>

            <!-- Producto -->
            <div class="bg-white rounded-xl shadow p-4 space-y-3">
                <img src="https://exitocol.vtexassets.com/arquivos/ids/28955924/Arroz-Diana-500-gr-445117_a.jpg?v=638864002494600000"
                     class="mx-auto h-40 object-contain">

                <div>
                    <p class="text-xs text-gray-500">Precio Ahora</p>
                    <p class="text-xl font-bold text-blue-600">$28.650</p>
                    <p class="text-xs text-gray-400">IVA incluido</p>
                </div>

                <h3 class="text-sm text-gray-700">
                    Palitos de pollo apanado Zenú
                </h3>

                <div class="flex items-center justify-between">
                    <div class="flex items-center border rounded">
                        <button class="px-2">-</button>
                        <span class="px-3">1</span>
                        <button class="px-2">+</button>
                    </div>

                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-1 rounded">
                        Comprar
                    </button>
                </div>
            </div>

            <!-- Producto -->
            <div class="bg-white rounded-xl shadow p-4 space-y-3">
                <img src="https://exitocol.vtexassets.com/arquivos/ids/28955924/Arroz-Diana-500-gr-445117_a.jpg?v=638864002494600000"
                     class="mx-auto h-40 object-contain">

                <div>
                    <p class="text-xs text-gray-500">Precio Ahora</p>
                    <p class="text-xl font-bold text-blue-600">$27.300</p>
                    <p class="text-xs text-gray-400">IVA incluido</p>
                </div>

                <h3 class="text-sm text-gray-700">
                    Pinchos de pollo Zenú
                </h3>

                <div class="flex items-center justify-between">
                    <div class="flex items-center border rounded">
                        <button class="px-2">-</button>
                        <span class="px-3">1</span>
                        <button class="px-2">+</button>
                    </div>

                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-1 rounded">
                        Comprar
                    </button>
                </div>
            </div>

        </div>

    </main>

</div>