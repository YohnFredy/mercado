<?php
use App\Models\Category;
use Livewire\Component;

new class extends Component {
    public $categories = [];

    public function mount()
    {
        $this->categories = Category::with('children.children')->whereNull('parent_id')->get()->toArray();
    }
};
?>

<div x-data="{ open: false, activeCat: null }" @mouseleave="activeCat = null">

    <!-- Botón Disparador -->
    <button @click="open = !open"
        class="cursor-pointer bg-white/15 hover:bg-white/25 px-3 py-2 rounded-xl flex items-center text-white font-bold text-[15px] tracking-wide transition-all duration-200 select-none backdrop-blur-sm border border-white/10">
        <flux:icon.bars-3 class="text-white size-6 mr-2.5 transition-transform duration-300" x-bind:class="open && 'rotate-90'" />
        Categorías
    </button>

    <!-- Fondo Oscuro del Modal (Backdrop) -->
    <div x-show="open" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        style="display: none;" @click="open = false"
        class="fixed top-[72px] inset-0 bg-gray-900/10 z-40 {{-- backdrop-blur-sm --}}"></div>

    <!-- Contenedor Principal del Mega Menú -->
    <div x-show="open" style="display: none;" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        class="absolute top-[72px] left-0 right-0 w-full bg-transparent z-50 flex shadow-2xl overflow-hidden border-t border-gray-100 h-[80vh] min-h-[500px]">

        <!-- Panel Izquierdo (Categorías Principales) -->
        <div
            class="w-80 h-full overflow-y-auto bg-white border-r border-gray-100 pointer-events-auto shadow-[4px_0_24px_rgba(0,0,0,0.02)] z-10 overscroll-contain">
            <ul class="py-4">
                @foreach ($categories as $category)
                    <li x-on:mouseenter="activeCat = {{ $category['id'] }}">
                        <button
                            class="w-full text-left px-6 py-3.5 font-semibold flex justify-between items-center transition-all duration-200 group"
                            x-bind:class="activeCat === {{ $category['id'] }} ?
                                'bg-primary/5 text-primary border-l-4 border-primary' :
                                'text-gray-700 hover:bg-gray-50 border-l-4 border-transparent'">
                            <span class="truncate text-[15px] group-hover:translate-x-1 transition-transform duration-200">{{ $category['name'] }}</span>
                            <flux:icon.chevron-right class="size-4 shrink-0 transition-all duration-200"
                                x-bind:class="activeCat === {{ $category['id'] }} ? 'translate-x-1 text-primary' : 'text-gray-400 group-hover:text-gray-600'"
                                stroke-width="2.5" />
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Panel Derecho (Subcategorías en Cuadrícula) -->
        <div class="flex-1 h-full overflow-y-auto transition-all duration-300 relative bg-white overscroll-contain"
            x-bind:class="activeCat !== null ?
                'pointer-events-auto opacity-100 translate-x-0' :
                'pointer-events-none opacity-0 -translate-x-8'">
            @foreach ($categories as $category)
                <div x-show="activeCat === {{ $category['id'] }}" style="display: none;"
                    x-transition:enter="transition ease-out duration-300 delay-75"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="p-10 lg:p-12 h-full">

                    <!-- Encabezado de la Categoría Principal -->
                    <div class="pb-8">
                        <div class="flex items-center gap-4 mb-8 pb-4 border-b border-gray-100 justify-between">
                            <h2
                                class="text-3xl font-extrabold text-gray-900 tracking-tight">
                                {{ $category['name'] }}
                            </h2>
                            <a href="#" class="inline-flex items-center gap-1.5 text-sm font-bold text-primary hover:text-primary/80 hover:underline transition-colors mt-1 px-4 py-2 rounded-lg hover:bg-primary/5">
                                Ver todo <flux:icon.arrow-right class="size-4" />
                            </a>
                        </div>
    
                        @if (count($category['children']) > 0)
                            <!-- Cuadrícula de Subcategorías -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-12 gap-y-12">
                                @foreach ($category['children'] as $subcategory)
                                    <div class="flex flex-col">
                                        <!-- Título de la Subcategoría -->
                                        <div class="mb-4">
                                            <a href="#" class="group inline-flex items-center gap-2">
                                                <h3
                                                    class="font-extrabold text-gray-900 text-[15px] tracking-wide uppercase group-hover:text-secondary transition-colors duration-200">
                                                    {{ $subcategory['name'] }}
                                                </h3>
                                            </a>
                                        </div>
    
                                        <!-- Enlaces (Hijos de la Subcategoría) -->
                                        @if (count($subcategory['children']) > 0)
                                            <ul class="space-y-2.5 flex-1">
                                                @foreach ($subcategory['children'] as $item)
                                                    <li>
                                                        <a href="#"
                                                            class="group flex items-center gap-2.5 text-[15px] text-gray-600 hover:text-primary transition-all duration-200">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-200 group-hover:bg-primary group-hover:scale-125 transition-all duration-200 shrink-0"></span>
                                                            <span class="group-hover:translate-x-1 transition-transform duration-200">{{ $item['name'] }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Estado Vacío -->
                            <div class="flex flex-col items-center justify-center pt-24 text-gray-400">
                                <div class="w-24 h-24 mb-6 rounded-full bg-gray-50 flex items-center justify-center">
                                    <flux:icon.folder-open class="size-12 text-gray-300" />
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Sección en construcción</h3>
                                <p class="text-base text-gray-500">Próximamente agregaremos productos a esta categoría.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
