<?php

use App\Models\Category;
use Livewire\Component;

new class extends Component {
    public $categories = [];

    public function mount(): void
    {
        $this->categories = Category::with('children.children')->whereNull('parent_id')->get()->toArray();
    }
};
?>

<div x-data="{ open: false }" class="relative z-50">

    <!-- Botón Disparador -->
    <button x-on:click="open = true"
        class="cursor-pointer bg-primary/50 hover:bg-white/25 active:scale-95 px-3 py-2 rounded-xl flex items-center gap-2 font-bold mr-3 transition-all duration-200 backdrop-blur-sm">
        <flux:icon.bars-3 class="text-white size-6" x-bind:class="open && 'rotate-90'" style="transition: transform 0.3s ease;" />
    </button>

    <!-- Overlay con Backdrop Blur -->
    <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;"
        @click="open = false" class="fixed inset-0 bg-gray-900/20 {{-- backdrop-blur-sm  --}}z-40">
    </div>

    <!-- Panel Deslizante -->
    <div x-show="open" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-250" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full" style="display: none;"
        class="fixed inset-y-0 left-0 w-[85vw] max-w-sm bg-white z-50 flex flex-col shadow-2xl">

        <!-- Header del Panel -->
        <div class="flex items-center justify-between px-5 py-4 bg-primary">
            <div class="flex items-center gap-3">
                <flux:icon.squares-2x2 class="text-white/80 size-6" />
                <h2 class="text-lg font-bold text-white tracking-wide">Categorías</h2>
            </div>
            <button @click="open = false"
                class="cursor-pointer p-1.5 rounded-lg bg-white/15 hover:bg-white/25 transition-colors duration-200">
                <flux:icon.x-mark class="text-white size-5" />
            </button>
        </div>

        <!-- Contenido Scrollable -->
        <div class="flex-1 overflow-y-auto overscroll-contain">

            <nav class="py-3">
                @foreach ($categories as $category)
                    <div wire:key="mobile-cat-{{ $category['id'] }}" x-data="{ expanded: false }" class="border-b border-gray-100 last:border-b-0">

                        <!-- Categoría Principal (Nivel 1) -->
                        <button x-on:click="expanded = !expanded"
                            class="cursor-pointer w-full flex items-center justify-between px-5 py-3.5 text-left transition-all duration-200"
                            x-bind:class="expanded ? 'bg-primary/5' : 'hover:bg-gray-50'">

                            <div class="flex items-center gap-3">
                                <span class="w-1 h-6 rounded-full transition-colors duration-200"
                                    x-bind:class="expanded ? 'bg-primary' : 'bg-gray-200'"></span>
                                <span class="font-semibold text-[15px] transition-colors duration-200"
                                    x-bind:class="expanded ? 'text-primary' : 'text-gray-800'">
                                    {{ $category['name'] }}
                                </span>
                            </div>

                            <flux:icon.chevron-right class="size-4 text-gray-400 transition-transform duration-300"
                                x-bind:class="expanded && 'rotate-90 !text-primary'" />
                        </button>

                        <!-- Subcategorías (Nivel 2) -->
                        <div x-show="expanded" x-collapse x-cloak>
                            <div class="pb-2">
                                @foreach ($category['children'] as $subcategory)
                                    <div wire:key="mobile-sub-{{ $subcategory['id'] }}" x-data="{ subExpanded: false }" class="ml-5 mr-3">

                                        <button x-on:click="subExpanded = !subExpanded"
                                            class="cursor-pointer w-full flex items-center justify-between pl-4 pr-3 py-2.5 rounded-lg text-left transition-all duration-200 border-l-2"
                                            x-bind:class="subExpanded ? 'border-secondary bg-secondary/5' : 'border-transparent hover:border-gray-200 hover:bg-gray-50'">

                                            <span class="font-medium text-sm transition-colors duration-200"
                                                x-bind:class="subExpanded ? 'text-gray-900' : 'text-gray-600'">
                                                {{ $subcategory['name'] }}
                                            </span>

                                            @if (count($subcategory['children']) > 0)
                                                <flux:icon.chevron-right
                                                    class="size-3.5 text-gray-400 transition-transform duration-300 shrink-0 ml-2"
                                                    x-bind:class="subExpanded && 'rotate-90 !text-secondary'" />
                                            @endif
                                        </button>

                                        <!-- Items (Nivel 3) -->
                                        @if (count($subcategory['children']) > 0)
                                            <div x-show="subExpanded" x-collapse x-cloak>
                                                <ul class="pl-6 pr-2 py-1.5 space-y-0.5">
                                                    @foreach ($subcategory['children'] as $item)
                                                        <li wire:key="mobile-item-{{ $item['id'] }}">
                                                            <a href="#"
                                                                class="flex items-center gap-2 py-2 px-3 rounded-md text-sm text-gray-500 hover:text-primary hover:bg-primary/5 transition-all duration-200 group">
                                                                <span
                                                                    class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-primary transition-colors duration-200 shrink-0"></span>
                                                                {{ $item['name'] }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </nav>
        </div>

        <!-- Footer del Panel -->
        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/80">
            <a href="#"
                class="flex items-center justify-center gap-2 py-2.5 rounded-xl bg-primary text-white font-semibold text-sm hover:bg-primary/90 transition-colors duration-200">
                <flux:icon.squares-2x2 class="size-4" />
                Ver todas las categorías
            </a>
        </div>
    </div>
</div>
