<?php

use App\Models\Category;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    #[Computed]
    public function categories()
    {
        return Category::query()
            ->with([
                'children' => function ($query) {
                    $query->where('is_active', true)->with([
                        'children' => function ($q) {
                            $q->where('is_active', true);
                        },
                    ]);
                },
            ])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
};

?>

<div x-data="{ open: false, activeCat: null }" @keydown.escape.window="open = false; activeCat = null"
    @open-mega-menu.window="open = true"
    x-effect="document.body.style.overflow = open ? 'hidden' : ''" class="relative">

    <!-- Botón Disparador -->
    <button @click="open = !open"
        class="cursor-pointer bg-white/10 hover:bg-white/20 px-4 py-2 rounded-xl flex items-center text-white font-bold text-[13px] uppercase tracking-widest transition-all duration-300 select-none backdrop-blur-md border border-white/20 group shadow-lg active:scale-95"
        :class="open ? 'bg-white/30 border-white/40 shadow-inner' : ''">
        <div class="relative w-5 h-5 mr-3">
            <flux:icon.bars-3 class="absolute inset-0 size-5 text-white transition-all duration-500 transform"
                x-bind:class="open ? 'opacity-0 scale-50 rotate-90' : 'opacity-100 scale-100 rotate-0'" />
            <flux:icon.x-mark class="absolute inset-0 size-5 text-white transition-all duration-500 transform"
                x-bind:class="open ? 'opacity-100 scale-100 rotate-0' : 'opacity-0 scale-50 -rotate-90'" />
        </div>
        Categorías
    </button>

    <!-- Backdrop con Glassmorphism -->
    <div x-show="open" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click="open = false; activeCat = null" class="fixed inset-0 bg-gray-900/60 backdrop-blur-[1px] z-40"
        style="display: none;"></div>

    <!-- Mega Menú Premium Dropdown -->
    <div x-show="open" x-transition:enter="transition ease-out duration-500 cubic-bezier(0.16, 1, 0.3, 1)"
        x-transition:enter-start="opacity-0 -translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 -translate-y-4" @mouseenter="open = true" @mouseleave="activeCat = null"
        class="fixed left-0 top-16 bg-white rounded-r-[2.5rem] shadow-[20px_25px_80px_-15px_rgba(0,0,0,0.3)] z-50 overflow-hidden flex border-y border-r border-gray-200/50 transition-all duration-500 ease-in-out"
        x-bind:class="activeCat ? 'w-[98vw] max-w-[1400px]' : 'w-85'" style="display: none; height: 85vh;">

        <!-- Panel Izquierdo: Categorías (Dark Sidebar) -->
        <aside
            class="w-85 bg-gray-50 border-r border-gray-300 overflow-y-auto custom-scrollbar p-4 space-y-2 shrink-0 pt-8">

            {{--   <div x-show="activeCat === null" class=" text-right pr-3">

                <flux:button @click="open = false" variant="ghost" icon="x-mark" size="sm"
                    aria-label="Close modal"
                    class="text-gray 400! bg-gray-100! hover:bg-gray-200!  hover:text-gray-800! cursor-pointer">
                </flux:button>
            </div> --}}

            @foreach ($this->categories as $category)
                <div @mouseenter="activeCat = {{ $category->id }}" class="relative">
                    <a href="{{ route('category', $category->slug) }}" wire:navigate
                        class="group flex items-center gap-3 px-6 py-2 rounded-2xl transition-all duration-300 relative overflow-hidden"
                        x-bind:class="activeCat === {{ $category->id }} ? 'bg-white shadow-md shadow-gray-700 text-primary' :
                            'text-gray-600 hover:bg-gray-200/50 hover:text-gray-900'">

                        <!-- Borde  Mercado Green -->
                        <div
                            class="absolute left-2 top-3 bottom-3 w-1.5 bg-primary rounded-r-full transition-all duration-500 transform">
                        </div>

                        <div class="absolute left-2 top-3 bottom-3 w-1.5 bg-secondary rounded-r-full transition-all duration-500 transform"
                            x-show="activeCat === {{ $category->id }}"
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="-translate-x-full opacity-0"
                            x-transition:enter-end="translate-x-0 opacity-100"></div>

                        <span
                            class="font-black text-[15px] text-gray-900 tracking-tight uppercase transition-all duration-300">
                            {{ $category->name }}
                        </span>

                        <flux:icon.chevron-right
                            class="size-4 ml-auto text-primary  group-hover:opacity-100 transition-all duration-300"
                            x-bind:class="activeCat === {{ $category->id }} ? 'opacity-100 text-secondary translate-x-1' : ''" />
                    </a>
                </div>
            @endforeach

            <!-- Botón Cerrar (Solo visible en modo lateral) -->
            <div x-show="activeCat === null" class="pt-4 border-t border-gray-300 mt-4 px-4">

                <flux:button @click="open = false" icon="x-mark" class=" w-full">
                    Cerrar
                </flux:button>
            </div>
        </aside>

        <!-- Panel Derecho: Subcategorías (Se muestra solo al activar categoría) -->
        <main x-show="activeCat !== null" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0"
            class="flex-1 bg-white overflow-y-auto custom-scrollbar relative flex flex-col" style="display: none;">

            <!-- Header Unificado -->
            <div
                class="bg-white/90 backdrop-blur-md sticky top-0 z-10 px-12 py-6 border-b border-gray-50 flex items-center justify-between">
                <div>
                    @foreach ($this->categories as $category)
                        <div x-show="activeCat === {{ $category->id }}" class="flex items-center gap-4">
                            <h2 class="text-3xl font-black text-primary tracking-tighter">{{ $category->name }}</h2>
                            <a href="{{ route('category', $category->slug) }}" wire:navigate
                                class="flex items-center gap-2 text-xs font-black text-gray-700 hover:underline uppercase tracking-widest pl-4 border-l border-gray-200">
                                Ver Todo <flux:icon.arrow-right class="size-3" />
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Botón Cerrar Consistente -->
                <button @click="open = false; activeCat = null"
                    class="p-3 rounded-2xl bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all duration-300 group/close shadow-sm">
                    <flux:icon.x-mark class="size-6 group-hover/close:rotate-90 transition-transform duration-500" />
                </button>
            </div>

            <div class="flex-1 px-12 pb-12">
                @foreach ($this->categories as $category)
                    <div x-show="activeCat === {{ $category->id }}"
                        x-transition:enter="transition ease-out duration-500 delay-100"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">

                        @if ($category->children->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-16 gap-y-10">
                                @foreach ($category->children as $subcategory)
                                    <div
                                        class="group/section transform transition-all duration-300 hover:translate-x-2">
                                        <div class="flex items-center gap-3 mb-6">
                                            <span class="w-1.5 h-7 bg-primary/30 rounded-full "></span>
                                            <a href="{{ route('category', $subcategory->slug) }}" wire:navigate
                                                class="text-lg font-black text-gray-800 hover:text-primary transition-colors underline decoration-secondary/20 decoration-4 underline-offset-4 tracking-tighter uppercase">
                                                {{ $subcategory->name }}
                                            </a>
                                        </div>

                                        @if ($subcategory->children->count() > 0)
                                            <ul class="space-y-2 pl-4 border-l-2 border-gray-200 ml-1">
                                                @foreach ($subcategory->children as $item)
                                                    <li>
                                                        <a href="{{ route('category', $item->slug) }}" wire:navigate
                                                            class="flex items-center gap-4 text-[15px] font-medium text-gray-700 hover:text-primary transition-all group/item">
                                                            <flux:icon.chevron-right
                                                                class="size-3 text-gray-500 group-hover/item:text-primary group-hover/item:translate-x-1 transition-all" />
                                                            <span
                                                                class="group-hover/item:font-bold transition-all">{{ $item->name }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div
                                class="h-[40vh] flex flex-col items-center justify-center text-center opacity-30 space-y-6">
                                <flux:icon.truck class="size-24 text-gray-200" />
                                <p class="text-xl font-black text-gray-400 uppercase tracking-widest">Abasteciendo esta
                                    sección...</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </main>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e5e7eb;
        border-radius: 20px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #00A63D;
        opacity: 0.5;
    }
</style>
