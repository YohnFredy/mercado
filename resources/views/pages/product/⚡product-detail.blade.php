<?php

use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Str;

new #[Title('Detalle del Producto')] class extends Component {
    public Product $product;
    public int $quantity = 1;
    public ?string $selectedImage = null;

    public function mount(Product $product)
    {
        $this->product = $product->load(['brand', 'images', 'categories.parent']);
        $firstImage = $this->product->images->first();
        $this->selectedImage = $firstImage ? asset('storage/' . $firstImage->path) : 'https://exitocol.vtexassets.com/arquivos/ids/28955924/Arroz-Diana-500-gr-445117_a.jpg?v=638864002494600000';
    }

    public function selectImage(string $path): void
    {
        $this->selectedImage = asset('storage/' . $path);
    }

    public function addToCart(\App\Services\CartService $cart)
    {
        $cart->add($this->product, $this->quantity);

        $this->dispatch('add-to-cart', [
            'productId' => $this->product->id,
            'quantity' => $this->quantity,
            'name' => $this->product->title,
            'image' => $this->product->images->first() ? asset('storage/' . $this->product->images->first()->path) : null,
            'price' => $this->product->selling_price_incl_vat,
        ]);

        // Dispatching event to update global cart button and flyout
        $this->dispatch('cart-updated');
    }
}; ?>

<div class="min-h-screen bg-gray-50 shadow-sm shadow-gray-300 pb-28 lg:pb-16 md:px-10 lg:px-20 pt-3 rounded-2xl text-gray-900"
    x-data="{ quantity: @entangle('quantity') }">
    <div class="max-w-7xl mx-auto w-full">
        <!-- Desktop Breadcrumbs -->
        <div class="hidden md:block px-6 lg:px-8 py-4">
            <nav class="overflow-hidden">
                <flux:breadcrumbs class="flex-wrap gap-y-1">
                    <flux:breadcrumbs.item href="{{ route('home') }}" icon="home" wire:navigate
                        class="text-gray-500 hover:text-primary transition-colors">Inicio</flux:breadcrumbs.item>

                    @if ($product->categories->first())
                        @php
                            $cat = $product->categories->first();
                            $path = [];
                            $curr = $cat;
                            while ($curr) {
                                $path[] = $curr;
                                $curr = $curr->parent;
                            }
                            $path = array_reverse($path);
                        @endphp
                        @foreach ($path as $p)
                            <flux:breadcrumbs.item href="{{ route('category', $p->slug) }}" wire:navigate
                                class="text-gray-500 hover:text-primary transition-colors">{{ $p->name }}
                            </flux:breadcrumbs.item>
                        @endforeach
                    @endif

                    <flux:breadcrumbs.item class="font-black truncate max-w-[200px] text-primary">{{ $product->title }}
                    </flux:breadcrumbs.item>
                </flux:breadcrumbs>
            </nav>
        </div>

        <main class="grid grid-cols-1 lg:grid-cols-12 gap-0 lg:gap-8 w-full">

            <!-- Mobile "Back" Header (Absolute / Sticky) -->
            <div
                class="lg:hidden sticky top-0 z-40 bg-fondo/90 backdrop-blur-xl border-b border-gray-100 flex items-center justify-between px-4 py-3">
                <a href="javascript:history.back()"
                    class="size-10 flex items-center justify-center bg-gray-50 rounded-full text-gray-800 hover:bg-gray-100 transition-colors">
                    <flux:icon.chevron-left class="size-5" />
                </a>
                <span class="text-sm font-black text-gray-900 truncate max-w-[60%]">{{ $product->title }}</span>
                <div class="w-10"></div> <!-- Spacer for centering -->
            </div>

            <!-- Left: Images -->
            <section
                class="lg:col-span-5 relative w-full lg:rounded-3xl lg:border lg:border-gray-100 lg:p-6 lg:shadow-md shadow-gray-700 lg:bg-white">
                <div class="lg:sticky lg:top-24 space-y-4">

                    <!-- Main Image Frame -->
                    <div
                        class="relative w-full aspect-square md:aspect-[4/3] lg:aspect-square flex items-center justify-center bg-fondo group overflow-hidden lg:rounded-2xl lg:bg-transparent">

                        <!-- Discount Badge Overlaid -->
                        @if ($product->discount_percentage > 0)
                            <div
                                class="absolute top-4 left-4 xl:top-6 xl:left-6 z-20 bg-acento text-fondo text-xs font-black px-3 py-1.5 rounded-full shadow-lg shadow-acento/30 tracking-tight">
                                -{{ number_format($product->discount_percentage, 0) }}% OFF
                            </div>
                        @endif

                        <img src="{{ $selectedImage }}"
                            class="absolute inset-0 w-full h-full object-contain p-4 lg:p-8 transition-transform duration-700 ease-out group-hover:scale-[1.03]"
                            alt="{{ $product->title }}">

                        <!-- Subtle studio lighting effect -->
                        <div
                            class="absolute inset-0 bg-radial-[at_50%_50%] from-primary/5 to-transparent pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-700 hidden lg:block">
                        </div>
                    </div>

                    <!-- Thumbnails Gallery -->
                    @if ($product->images->count() > 1)
                        <div
                            class="flex items-center gap-3 overflow-x-auto pb-4 px-4 pt-2 lg:px-0 hide-scrollbar snap-x snap-mandatory">
                            @foreach ($product->images as $image)
                                @php $assetUrl = asset('storage/' . $image->path); @endphp
                                <button wire:click="selectImage('{{ $image->path }}')"
                                    class="relative size-16 md:size-20 shrink-0 rounded-xl border-2 transition-all duration-300 overflow-hidden bg-white snap-center cursor-pointer {{ $selectedImage === $assetUrl ? 'border-primary ring-2 ring-primary/20 scale-100' : 'border-gray-100 hover:border-gray-200 opacity-70 hover:opacity-100 scale-95' }}">
                                    <img src="{{ $assetUrl }}" class="w-full h-full object-contain p-1.5"
                                        alt="Miniatura">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>

            <!-- Right: Product Info & Buy Action -->
            <section class="lg:col-span-6 lg:col-start-7 px-5 py-6 lg:py-4 lg:pl-6 w-full flex flex-col justify-between h-full bg-fondo lg:bg-transparent">

                <div class="space-y-8 flex-grow">

                    <!-- Headers & Price -->
                    <div class="space-y-5">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-xs font-black text-secondary tracking-[0.2em] uppercase shrink-0 truncate">
                                {{ $product->brand?->name ?? 'Exclusivo Mercado' }}
                            </span>
                            <div class="flex items-center gap-1.5 bg-green-50/80 px-2.5 py-1 rounded-md shrink-0 border border-green-100/50">
                                <flux:icon.sparkles variant="solid" class="size-3.5 text-primary" />
                                <span class="text-[10px] font-black text-primary uppercase tracking-widest">Stock Disponible</span>
                            </div>
                        </div>

                        <h1 class="text-xl sm:text-2xl font-black text-gray-800 leading-[1.1] tracking-tight text-balance">
                            {{ $product->title }}
                        </h1>

                        <div class="flex flex-col gap-1 pt-2">
                            @if ($product->discount_percentage > 0)
                                <span class=" text-gray-400 line-through font-bold">
                                    ${{ number_format($product->selling_price_incl_vat / (1 - $product->discount_percentage / 100), 0, ',', '.') }}
                                </span>
                            @endif
                            <div class="flex items-baseline gap-3 flex-wrap">
                                <span class="text-lg sm:text-xl font-black text-gray-500 tracking-tighter shrink-0">
                                    ${{ number_format($product->selling_price_incl_vat, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Trust Bar (Subtle) -->
                    <div class="flex flex-wrap items-center gap-y-3 gap-x-6 py-5 border-y border-gray-200/60">
                        <div class="flex items-center gap-2.5">
                            <div class="bg-gray-100/80 text-gray-600 p-1.5 rounded-lg shrink-0">
                                <flux:icon.truck variant="solid" class="size-4.5" />
                            </div>
                            <span class="text-sm font-bold text-gray-700 tracking-tight">Envío solo Valle del Cauca</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="bg-gray-100/80 text-gray-600 p-1.5 rounded-lg shrink-0">
                                <flux:icon.shield-check variant="solid" class="size-4.5" />
                            </div>
                            <span class="text-sm font-bold text-gray-700 tracking-tight">Compra Segura</span>
                        </div>
                    </div>

                    <!-- Brief Description -->
                    @if ($product->description)
                        <div class="pt-1">
                            <p class="text-base text-gray-600 leading-relaxed font-medium line-clamp-3">
                                {{ Str::limit(strip_tags($product->description), 200) }}
                            </p>
                            <a href="#detalles-tecnicos"
                                class="text-primary text-xs font-black uppercase tracking-widest hover:text-primary/80 transition-colors mt-3 inline-block">Ver
                                todos los detalles →</a>
                        </div>
                    @endif

                    <!-- Desktop Add to cart logic -->
                    <div class="hidden lg:block pt-4">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center bg-white rounded-xl p-1 shrink-0 border border-gray-200 shadow-sm shadow-gray-200/40">
                                <button @click="quantity = Math.max(1, quantity - 1)"
                                    class="size-11 flex items-center justify-center bg-gray-50/50 hover:bg-gray-100 rounded-lg transition-colors text-gray-700 font-bold cursor-pointer">
                                    <flux:icon.minus class="size-4" />
                                </button>

                                <input type="number" x-model.number="quantity" min="1" max="999"
                                    @input="quantity = Math.max(1, Math.min(999, quantity))"
                                    class="w-12 text-center text-lg font-black text-gray-900 bg-transparent border-none focus:ring-0 [-moz-appearance:_textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">

                                <button @click="quantity = Math.min(999, quantity + 1)"
                                    class="size-11 flex items-center justify-center bg-gray-50/50 hover:bg-gray-100 rounded-lg transition-colors text-gray-700 font-bold cursor-pointer">
                                    <flux:icon.plus class="size-4" />
                                </button>
                            </div>

                            <button wire:click="addToCart"
                                class="flex-1 bg-primary hover:bg-primary/95 text-white h-[3.25rem] rounded-xl font-black text-sm transition-all shadow-lg shadow-primary/25 active:scale-[0.98] flex items-center justify-center gap-2.5 cursor-pointer group">
                                <flux:icon.shopping-cart variant="solid" class="size-5 group-hover:-translate-y-0.5 transition-transform" />
                                Añadir al Carrito
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Deep Dive Information (Specs, Details) Below CTA -->
                <div id="detalles-tecnicos" class="mt-12 lg:mt-16 space-y-10 lg:space-y-12">

                    @if ($product->description)
                        <section class="space-y-4">
                            <h3
                                class="text-lg font-black text-gray-800 uppercase tracking-widest border-b border-gray-300 pb-2">
                                Historia del Producto</h3>
                            <div
                                class="prose prose-sm sm:prose-base max-w-none text-gray-600 prose-p:leading-relaxed prose-p:font-medium">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                        </section>
                    @endif

                    @if ($product->specifications && count($product->specifications) > 0)
                        <section class="space-y-4">
                            <h3
                                class="text-lg font-black text-gray-800 uppercase tracking-widest border-b border-gray-300 pb-2">
                                Especificaciones</h3>
                            <div class="grid grid-cols-1 gap-2">
                                @foreach ($product->specifications as $label => $value)
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white rounded-2xl border border-gray-100 shadow-md shadow-gray-700 gap-1 sm:gap-4">
                                        <span
                                            class="text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] shrink-0">{{ $label }}</span>
                                        <span
                                            class="text-sm font-bold text-gray-900 sm:text-right w-full sm:w-auto overflow-hidden text-ellipsis">{{ $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($product->additional_information)
                        <section class="p-5 sm:p-6 bg-white border border-gray-300 rounded-3xl">
                            <div class="flex items-start gap-3">
                                <flux:icon.information-circle variant="solid"
                                    class="size-5 text-secondary shrink-0 mt-0.5" />
                                <p class="text-sm font-medium text-gray-700 italic leading-relaxed">
                                    {{ $product->additional_information }}
                                </p>
                            </div>
                        </section>
                    @endif

                </div>
            </section>
        </main>
    </div>

    <!-- Mobile Sticky Add to Cart Bar -->
    <div
        class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-fondo/90 backdrop-blur-2xl border-t border-gray-100 p-4 pb-safe-offset-4 shadow-[0_-10px_40px_-5px_var(--color-primary)]/10">
        <div class="max-w-7xl mx-auto flex items-center gap-2 sm:gap-3">
            <!-- Quantity Selector -->
            <div
                class="flex items-center bg-white rounded-2xl p-1 border border-gray-100 shadow-md shadow-gray-700 shrink-0">
                <button x-on:click="if(quantity > 1) quantity--"
                    class="size-11 sm:size-12 flex items-center justify-center bg-gray-50 rounded-xl text-gray-900 active:bg-gray-200 cursor-pointer">
                    <flux:icon.minus class="size-4" />
                </button>
                <div class="w-8 sm:w-10 text-center font-black text-base md:text-lg text-gray-900 pointer-events-none select-none"
                    x-text="quantity">1</div>
                <button x-on:click="if(quantity < 99) quantity++"
                    class="size-11 sm:size-12 flex items-center justify-center bg-gray-50 rounded-xl text-gray-900 active:bg-gray-200 cursor-pointer">
                    <flux:icon.plus class="size-4" />
                </button>
            </div>

            <!-- Add Button -->
            <button wire:click="addToCart"
                class="flex-1 bg-primary hover:bg-primary/95 text-fondo h-14 rounded-2xl font-black text-[11px] sm:text-xs md:text-sm uppercase tracking-widest transition-all shadow-xl shadow-primary/25 active:scale-95 flex items-center justify-center gap-1 sm:gap-2 cursor-pointer relative overflow-hidden group">
                <div
                    class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 active:translate-y-0 transition-transform duration-300">
                </div>
                <flux:icon.shopping-cart variant="solid" class="size-4 sm:size-5 relative z-10 shrink-0" />
                <span class="relative z-10 truncate lg:w-auto">Al Carrito</span>
            </button>
        </div>
    </div>

    <!-- Safari CSS safe area padding support -->
    <style>
        .pb-safe-offset-4 {
            padding-bottom: calc(1rem + env(safe-area-inset-bottom));
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</div>
