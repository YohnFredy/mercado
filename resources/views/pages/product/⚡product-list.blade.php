<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Services\CartService;

new #[Title('Productos')] class extends Component {
    use WithoutUrlPagination, WithPagination;

    public ?Category $category = null;

    #[Url]
    public array $selectedBrands = [];

    #[Url]
    public string $search = '';

    #[Url]
    public ?float $minPrice = null;

    #[Url]
    public ?float $maxPrice = null;

    #[Url]
    public string $sort = 'latest';

    public function mount(?Category $category = null)
    {
        $this->category = $category;
    }

    public function updated($property)
    {
        if (in_array($property, ['selectedBrands', 'search', 'minPrice', 'maxPrice', 'sort'])) {
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'minPrice', 'maxPrice', 'selectedBrands', 'sort']);
        $this->resetPage();
    }

    protected function getDescendantCategoryIds(Category $category): array
    {
        $ids = [$category->id];
        foreach ($category->children as $child) {
            $ids = array_merge($ids, $this->getDescendantCategoryIds($child));
        }

        return $ids;
    }

    #[Computed]
    public function categoryPathArray()
    {
        if (!$this->category) {
            return [];
        }

        $path = [];
        $current = $this->category;
        while ($current) {
            $path[] = ['name' => $current->name, 'slug' => $current->slug];
            $current = $current->parent;
        }

        return array_reverse($path);
    }

    #[Computed]
    public function products()
    {
        $query = Product::query()->where('is_active', true);

        if ($this->search) {
            $query->search($this->search);
        }

        if ($this->category) {
            $categoryIds = $this->getDescendantCategoryIds($this->category);
            $query->whereHas('categories', fn($q) => $q->whereIn('categories.id', $categoryIds));
        }

        if (!empty($this->selectedBrands)) {
            $query->whereIn('brand_id', $this->selectedBrands);
        }

        if (is_numeric($this->minPrice)) {
            $query->priceMin($this->minPrice);
        }

        if (is_numeric($this->maxPrice)) {
            $query->priceMax($this->maxPrice);
        }

        switch ($this->sort) {
            case 'name-az':
                $query->orderBy('title', 'asc');
                break;
            case 'name-za':
                $query->orderBy('title', 'desc');
                break;
            case 'price-low':
                $query->orderByPrice('asc');
                break;
            case 'price-high':
                $query->orderByPrice('desc');
                break;
            default:
                $query->latest();
                break;
        }

        return $query->with(['brand', 'images'])->paginate(20);
    }

    public function addToCart(Product $product, int $quantity, CartService $cart)
    {
        $cart->add($product, $quantity);

        $this->dispatch('add-to-cart', [
            'productId' => $product->id,
            'quantity' => $quantity,
            'name' => $product->title,
            'image' => $product->images->first() ? asset('storage/' . $product->images->first()->path) : null,
            'price' => $product->selling_price_incl_vat,
        ]);

        $this->dispatch('cart-updated');
    }

    #[Computed]
    public function subfamilies()
    {
        if (!$this->category) {
            return [];
        }

        return $this->category->children()->where('is_active', true)->get();
    }

    #[Computed]
    public function brands()
    {
        $categoryIds = $this->category ? $this->getDescendantCategoryIds($this->category) : [];

        return Brand::query()
            ->where('is_active', true)
            ->whereHas('products', function ($q) use ($categoryIds) {
                $q->where('is_active', true);

                if (!empty($categoryIds)) {
                    $q->whereHas('categories', fn($cq) => $cq->whereIn('categories.id', $categoryIds));
                }

                if ($this->search) {
                    $q->search($this->search);
                }

                if (is_numeric($this->minPrice)) {
                    $q->priceMin($this->minPrice);
                }

                if (is_numeric($this->maxPrice)) {
                    $q->priceMax($this->maxPrice);
                }
            })
            ->orderBy('name')
            ->get();
    }
};
?>

<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pb-8">
        <!-- Breadcrumbs Superior -->
        <div class="mb-2 md:mb-4 overflow-hidden">
            <flux:breadcrumbs class="flex-wrap gap-y-1">
                <flux:breadcrumbs.item href="{{ route('home') }}" icon="home" wire:navigate
                    class=" hidden md:flex py-1 rounded-lg transition-all duration-200">Inicio
                </flux:breadcrumbs.item>

                <flux:breadcrumbs.item
                    class="hidden md:flex cursor-pointer py-1 rounded-lg transition-all duration-200 font-bold"
                    @click.prevent="$dispatch('open-mega-menu')">
                    Categorías
                </flux:breadcrumbs.item>


                @foreach ($this->categoryPathArray as $path)
                    <flux:breadcrumbs.item href="{{ route('category', $path['slug']) }}" wire:navigate
                        class="py-1 rounded-lg transition-all duration-200">
                        {{ $path['name'] }}
                    </flux:breadcrumbs.item>
                @endforeach

                @if (!$this->category)
                    <flux:breadcrumbs.item class=" py-1 ">Catálogo</flux:breadcrumbs.item>
                @endif
            </flux:breadcrumbs>
        </div>

        <!-- Acción Móvil: Botón de Filtros -->

        <div class="md:hidden w-full mb-4">
            <flux:modal.trigger name="mobile-filters">
                <flux:button icon="funnel" class=" w-full  !text-primary !shadow-md shadow-gray-700 font-bold px-5">
                    Filtros
                </flux:button>
            </flux:modal.trigger>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filtros (Desktop) -->
            <aside class="hidden lg:block w-64 shrink-0">
                <div class="space-y-6">
                    <!-- Categorías Secundarias (Hijas) -->
                    @if (count($this->subfamilies) > 0)
                        <div class="bg-white rounded-2xl shadow-md shadow-gray-700 border border-gray-300 p-5">
                            <h3 class="font-bold text-primary mb-4 flex items-center gap-2">
                                <flux:icon.squares-2x2 class="size-8 text-secondary" />
                                Subcategorías
                            </h3>
                            <div class=" space-y-1">
                                @foreach ($this->subfamilies as $sub)
                                    <a href="{{ route('category', $sub->slug) }}" wire:navigate
                                        class="group flex py-1  px-2 items-center justify-between  rounded-lg bg-primary/2 
                                        hover:bg-primary/6 transition-colors">
                                        <span
                                            class=" text-sm  text-gray-600 font-semibold group-hover:text-primary transition-colors">{{ $sub->name }}</span>
                                        <flux:icon.chevron-right
                                            class="size-4 text-primary/80 group-hover:text-primary group-hover:translate-x-1 transition-all" />
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Filtro Marcas -->
                    @if (count($this->brands) > 0)
                        <div class="bg-white rounded-2xl shadow-md shadow-gray-700 border border-gray-300 p-5">
                            <h3 class="font-bold text-primary mb-4 flex items-center gap-2">
                                <flux:icon.tag variant="solid" class="size-6 text-secondary" />
                                Marcas
                            </h3>
                            <div
                                class="space-y-1 divide-y divide-gray-300 max-h-60 overflow-y-auto custom-scrollbar pr-2">
                                @foreach ($this->brands as $brand)
                                    <label class="flex items-center gap-2  cursor-pointer group pb-1">
                                        <input type="checkbox" wire:model.live="selectedBrands"
                                            value="{{ $brand->id }}">
                                        <span
                                            class="text-sm font-semibold text-gray-600 group-hover:text-primary transition-colors">{{ $brand->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Filtro Precio -->
                    <div class="bg-white rounded-2xl shadow-md shadow-gray-700 border border-gray-300 p-5">
                        <h3 class="font-bold text-primary mb-4 flex items-center gap-2">
                            <flux:icon.banknotes class="size-8 text-secondary" />
                            Rango de Precio
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label
                                    class="text-[10px] font-bold text-gray-600 uppercase tracking-wider ml-1">Mín</label>
                                <flux:input wire:model.live.debounce.500ms="minPrice" placeholder="0" size="sm"
                                    class=" border border-gray-300 rounded-lg" />
                            </div>
                            <div class="space-y-1">
                                <label
                                    class="text-[10px] font-bold text-gray-600 uppercase tracking-wider ml-1">Máx</label>
                                <flux:input wire:model.live.debounce.500ms="maxPrice" placeholder="Máx" size="sm"
                                    class="border border-gray-300 rounded-lg" />
                            </div>
                        </div>
                    </div>

                    <!-- Botón Limpiar Filtros -->
                    @if ($search || $minPrice || $maxPrice || !empty($selectedBrands))
                        <button wire:click="resetFilters"
                            class="w-full py-2.5 rounded-xl border-2 border-dashed border-secondary bg-white text-gray-800 text-xs font-bold hover:border-acento hover:text-acento hover:bg-acento/5 transition-all outline-none cursor-pointer">
                            LIMPIAR FILTROS
                        </button>
                    @endif
                </div>
            </aside>

            <!-- Listado Principal -->
            <main class="flex-1 space-y-6">
                <!-- Toolbar Desktop y Mobile Header -->
                <div
                    class=" hidden bg-white rounded-2xl shadow-md shadow-gray-700 border border-gray-300 p-4 md:flex items-center justify-between gap-4">
                    <div class="flex-1">
                        <h1 class="text-lg md:text-xl font-black text-primary leading-tight">
                            {{ $this->category?->name ?? 'Todos los Productos' }}
                        </h1>
                        <p class="text-xs md:text-sm text-gray-600 font-medium mt-0.5">
                            <span class="text-secondary font-bold">{{ $this->products->total() }}</span> productos
                        </p>
                    </div>

                    <!-- Acción Desktop: Ordenar -->
                    <div class="hidden lg:flex items-center gap-3">
                        <span class="text-xs font-bold text-gray-600 uppercase tracking-widest">Ordenar:</span>
                        <flux:select wire:model.live="sort" class="w-48 border !border-gray-400 ">
                            <flux:select.option value="latest">Novedades</flux:select.option>
                            <flux:select.option value="name-az">Nombre (A-Z)</flux:select.option>
                            <flux:select.option value="name-za">Nombre (Z-A)</flux:select.option>
                            <flux:select.option value="price-low">Precio más bajo</flux:select.option>
                            <flux:select.option value="price-high">Precio más alto</flux:select.option>
                        </flux:select>
                    </div>
                </div>

                <!-- Grilla de Productos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse ($this->products as $product)
                        <div x-data="{ quantity: 1 }"
                            class="bg-white rounded-3xl shadow-md shadow-gray-700 border border-gray-300 p-4 space-y-4 group  hover:shadow-gray-800 hover:border-primary/70 transition-all duration-300 relative overflow-hidden flex flex-col h-full">

                            <!-- Badge de Descuento -->
                            @if ($product->discount_percentage > 0)
                                <div
                                    class="absolute top-4 left-4 z-10 bg-acento text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-lg shadow-acento/20 animate-pulse">
                                    -{{ number_format($product->discount_percentage, 0) }}%
                                </div>
                            @endif

                            <!-- Contenedor Imagen -->
                            <a href="{{ route('product.show', $product->slug) }}" wire:navigate
                                class="relative aspect-square rounded-2xl border border-primary/50 shadow-md shadow-gray-500 flex items-center justify-center overflow-hidden shrink-0 block">
                                <img src="{{ $product->images->first() ? asset('storage/' . $product->images->first()->path) : 'https://exitocol.vtexassets.com/arquivos/ids/28955924/Arroz-Diana-500-gr-445117_a.jpg?v=638864002494600000' }}"
                                    class="max-h-[85%] max-w-[85%] object-contain group-hover:scale-110 transition-transform duration-500 ease-out"
                                    alt="{{ $product->title }}">

                                <div
                                    class="absolute inset-0 bg-primary/3 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                </div>
                            </a>

                            <!-- Información del Producto -->
                            <a href="{{ route('product.show', $product->slug) }}" wire:navigate
                                class="space-y-2 flex-grow">
                                <span
                                    class="text-[10px] font-black text-gray-500 tracking-widest uppercase truncate block">
                                    {{ $product->brand?->name ?? 'Marca Mercado' }}
                                </span>


                                <h3
                                    class="text-base text-primary-900 font-bold leading-tight line-clamp-2 h-10 group-hover:text-primary transition-colors">
                                    {{ $product->title }}
                                </h3>


                                <div class="pt-1">
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-lg font-black text-gray-700 tracking-tighter">
                                            ${{ number_format($product->selling_price_incl_vat, 0, ',', '.') }}
                                        </span>
                                        @if ($product->discount_percentage > 0)
                                            <span class="text-sm text-gray-500 line-through font-bold">
                                                ${{ number_format($product->selling_price_incl_vat / (1 - $product->discount_percentage / 100), 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                    {{-- <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">IVA Incluido</p> --}}
                                </div>
                            </a>

                            <!-- Acciones y Cantidad -->
                            <div class="space-y-3 border-t border-gray-50 shrink-0 mt-3">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="flex items-center bg-gray-200 rounded-xl p-1 shrink-0 border border-gray-300">
                                        <button @click="quantity = Math.max(1, quantity - 1)"
                                            class="size-8 flex items-center justify-center bg-white/90 hover:bg-white rounded-lg transition-all  border border-gray-300 hover:border-gray-300 text-gray-900 font-bold cursor-pointer">
                                            <flux:icon.minus class="size-4" />
                                        </button>

                                        <input type="number" x-model.number="quantity" min="1" max="999"
                                            @input="quantity = Math.max(1, Math.min(999, quantity))"
                                            class="w-10 text-center text-xs font-black text-gray-800 bg-transparent border-none focus:ring-0 [-moz-appearance:_textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">

                                        <button @click="quantity = Math.min(999, quantity + 1)"
                                            class="size-8 flex items-center justify-center bg-white/90 hover:bg-white rounded-lg transition-all  border border-gray-300 hover:border-gray-300 text-gray-900 font-bold cursor-pointer">
                                            <flux:icon.plus class="size-4" />
                                        </button>
                                    </div>

                                    <button @click="$wire.addToCart({{ $product->id }}, quantity)"
                                        class="flex-1 bg-primary hover:bg-primary/90 text-white py-2.5 rounded-xl font-black text-sm transition-all shadow-lg shadow-primary/20 active:scale-[0.98] flex items-center justify-center gap-2 cursor-pointer">
                                        <flux:icon.shopping-cart variant="solid" class="size-4 animate-bounce" />
                                        COMPRAR
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full py-24 text-center flex flex-col items-center justify-center animate-fade-in">
                            <div class="bg-gray-50 rounded-full p-10 mb-6 border-2 border-dashed border-gray-200">
                                <flux:icon.magnifying-glass class="size-20 text-gray-200" />
                            </div>
                            <h2 class="text-2xl font-black text-gray-900 mb-2 tracking-tight">¡Vaya! No encontramos
                                resultados</h2>
                            <p class="text-sm text-gray-500 max-w-xs mx-auto mb-8 font-medium">
                                Intenta con otros filtros o términos de búsqueda para encontrar lo que buscas.
                            </p>
                            <flux:button wire:click="resetFilters" variant="primary"
                                class="px-10 shadow-lg shadow-primary/20 font-black">
                                REINICIAR BÚSQUEDA
                            </flux:button>
                        </div>
                    @endforelse
                </div>

                <!-- Navegación de Páginas -->
                @if ($this->products->hasPages())
                    <div class=" bg-white p-4 shadow-md shadow-gray-700 border border-gray-300 rounded-2xl">
                        {{ $this->products->links() }}
                    </div>
                @endif
            </main>
        </div>
    </div>

    <flux:modal closable="" name="mobile-filters" flyout class="p-0! space-y-0 ">

        <div class="h-full flex flex-col ">
            <!-- Header del Modal -->
            <div class="p-4 border-b border-gray-300 flex items-center justify-between bg-primary">
                <div>
                    <flux:heading size="lg" class="text-white font-black!">Filtros y Orden</flux:heading>
                    <flux:text class="text-[10px] font-black text-gray-200 uppercase tracking-widest mt-1">Personaliza
                        tu búsqueda</flux:text>
                </div>
                <flux:modal.close>
                    <flux:button variant="ghost" icon="x-mark" size="sm" aria-label="Close modal"
                        class="text-gray-100! hover:text-white! bg-white/20! hover:bg-white/30!"></flux:button>
                </flux:modal.close>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-8 custom-scrollbar">
                <!-- Sección: Ordenar -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        <flux:icon.arrows-up-down class="size-6 text-secondary" />
                        Ordenar por
                    </h3>
                    <flux:select wire:model.live="sort" class="w-full border-gray-500! shadow-inner">
                        <flux:select.option value="latest">Novedades</flux:select.option>
                        <flux:select.option value="name-az">Nombre (A-Z)</flux:select.option>
                        <flux:select.option value="name-za">Nombre (Z-A)</flux:select.option>
                        <flux:select.option value="price-low">Precio más bajo</flux:select.option>
                        <flux:select.option value="price-high">Precio más alto</flux:select.option>
                    </flux:select>
                </div>

                <!-- Sección: Subcategorías -->
                @if (count($this->subfamilies) > 0)
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-primary flex items-center gap-2">
                            <flux:icon.squares-2x2 class="size-6 text-secondary" />
                            Subcategorías
                        </h3>
                        <div class="grid grid-cols-1 gap-2">
                            @foreach ($this->subfamilies as $sub)
                                <a href="{{ route('category', $sub->slug) }}" wire:navigate
                                    class="flex items-center justify-between py-1 px-3 rounded-xl bg-primary/5 hover:bg-primary/15 border border-transparent hover:border-primary/30 transition-all">
                                    <span class="text-sm font-medium text-gray-800">{{ $sub->name }}</span>
                                    <flux:icon.chevron-right class="size-4 text-primary" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Sección: Marcas -->
                @if (count($this->brands) > 0)
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-primary flex items-center gap-2">
                            <flux:icon.tag class="size-6  text-secondary" variant="solid" />
                            Marcas
                        </h3>
                        <div class="grid sm:grid-cols-2 gap-2 max-h-72 overflow-y-auto custom-scrollbar pr-2">
                            @foreach ($this->brands as $brand)
                                <label
                                    class="flex items-center gap-2 p-2 rounded-xl bg-primary/5 cursor-pointer border border-transparent has-[:checked]:border-primary/30 has-[:checked]:bg-primary/10 transition-all">
                                    <input type="checkbox" wire:model.live="selectedBrands"
                                        value="{{ $brand->id }}"
                                        class="rounded-md border-gray-300 text-primary focus:ring-primary/20 size-4 transition-all">
                                    <span class="text-xs font-bold text-gray-700 truncate">{{ $brand->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Sección: Precio -->
                <div class="space-y-4 pb-4">
                    <h3 class="text-sm font-bold text-primary flex items-center gap-2">
                        <flux:icon.banknotes class="size-6 text-secondary" />
                        Rango de Precio
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label
                                class="text-[10px] font-black text-gray-700 uppercase tracking-widest ml-1">Mínimo</label>
                            <flux:input wire:model.live.debounce.500ms="minPrice" placeholder="0" size="sm"
                                class="border rounded-lg border-gray-500" />
                        </div>
                        <div class="space-y-1">
                            <label
                                class="text-[10px] font-black text-gray-700 uppercase tracking-widest ml-1">Máximo</label>
                            <flux:input wire:model.live.debounce.500ms="maxPrice" placeholder="Máx" size="sm"
                                class="border rounded-lg border-gray-500" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón Limpiar en el Footer del Modal -->
            @if ($search || $minPrice || $maxPrice || !empty($selectedBrands))
                <div class="px-6 pb-12 ">
                    <flux:button wire:click="resetFilters" variant="primary" class="w-full font-black "
                        icon="trash">
                        REINICIAR FILTROS
                    </flux:button>
                </div>
            @endif
        </div>
    </flux:modal>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e5e5e5;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #00A63D;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</div>
