<?php

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Image;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin')] class extends Component {
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    
    // Core fields
    public $productId = null;
    public $brand_id = null;
    public $sku = '';
    public $barcode = '';
    public $title = '';
    public $slug = '';
    public $description = '';
    
    // Pricing
    public $cost_price_excl_vat = 0.00;
    public $selling_price_excl_vat = 0.00;
    public $vat_percentage = 0.00;
    public $discount_percentage = 0.00;
    public $final_price_incl_vat = 0.00;
    
    // Additional info
    public $additional_information = '';
    public $is_active = true;
    
    // JSON & Relations
    public $specifications = []; 
    public $selectedCategories = [];
    public $categorySelectorPath = [''];
    
    // Uploads
    public $newImages = [];

    public $isModalOpen = false;

    public function rules() {
        return [
            'brand_id' => 'nullable|exists:brands,id',
            'sku' => 'required|string|max:255|unique:products,sku,' . $this->productId,
            'barcode' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $this->productId,
            'description' => 'nullable|string',
            'cost_price_excl_vat' => 'required|numeric|min:0',
            'selling_price_excl_vat' => 'required|numeric|min:0',
            'vat_percentage' => 'required|numeric|min:0',
            'discount_percentage' => 'required|numeric|min:0',
            'final_price_incl_vat' => 'required|numeric|min:0',
            'additional_information' => 'nullable|string',
            'is_active' => 'boolean',
            'selectedCategories' => 'array',
            'selectedCategories.*' => 'exists:categories,id',
            'newImages.*' => 'nullable|image|max:10240',
        ];
    }

    public function updatedTitle($value) {
        if (!$this->productId) {
            $this->slug = Str::slug($value);
            if (empty($this->sku) && !empty($value)) {
                $words = explode(' ', trim($value));
                $skuParts = [];
                foreach ($words as $word) {
                    $cleanWord = preg_replace('/[^A-Za-z0-9]/', '', Str::ascii($word));
                    if (strlen($cleanWord) > 0) {
                        if (is_numeric($cleanWord)) {
                            $skuParts[] = $cleanWord;
                        } else {
                            $skuParts[] = strtoupper(substr($cleanWord, 0, 3));
                        }
                    }
                }
                $this->sku = implode('-', $skuParts);
            }
        }
    }

    public function updatedFinalPriceInclVat() {
        $this->recalculatePrices();
    }
    public function updatedVatPercentage() {
        $this->recalculatePrices();
    }
    public function updatedCostPriceExclVat() {}
    public function updatedDiscountPercentage() {}

    private function recalculatePrices() {
        $vat = (float) $this->vat_percentage;
        $final = (float) $this->final_price_incl_vat;
        if ($vat >= 0) {
            $this->selling_price_excl_vat = round($final / (1 + ($vat / 100)), 2);
        } else {
            $this->selling_price_excl_vat = $final;
        }
    }

    public function getProfitMarginProperty() {
        $cost = (float) $this->cost_price_excl_vat;
        $selling = (float) $this->selling_price_excl_vat;
        $discount = (float) $this->discount_percentage;

        if ($cost <= 0) return 0;
        
        $actualSelling = $selling * (1 - ($discount / 100));
        $profit = $actualSelling - $cost;
        
        return round(($profit / $cost) * 100, 2);
    }

    public function openModal($id = null) {
        $this->resetValidation();
        $this->reset([
            'productId', 'brand_id', 'sku', 'barcode', 'title', 'slug', 'description',
            'cost_price_excl_vat', 'selling_price_excl_vat', 'vat_percentage', 'discount_percentage', 'final_price_incl_vat',
            'additional_information', 'is_active', 'specifications', 'selectedCategories', 'newImages', 'categorySelectorPath'
        ]);
        $this->categorySelectorPath = [''];
        
        if ($id) {
            $product = Product::with(['categories', 'images'])->findOrFail($id);
            $this->productId = $product->id;
            $this->brand_id = $product->brand_id;
            $this->sku = $product->sku;
            $this->barcode = $product->barcode;
            $this->title = $product->title;
            $this->slug = $product->slug;
            $this->description = $product->description;
            $this->cost_price_excl_vat = $product->cost_price_excl_vat;
            $this->selling_price_excl_vat = $product->selling_price_excl_vat;
            $this->vat_percentage = $product->vat_percentage;
            $this->discount_percentage = $product->discount_percentage;
            $this->final_price_incl_vat = round($product->selling_price_excl_vat * (1 + ($product->vat_percentage / 100)), 2);
            $this->additional_information = $product->additional_information;
            $this->is_active = $product->is_active;
            
            $specs = is_array($product->specifications) ? $product->specifications : json_decode((string)$product->specifications, true);
            if (is_array($specs)) {
                foreach ($specs as $key => $value) {
                    $this->specifications[] = ['name' => $key, 'value' => $value];
                }
            }
            
            $this->selectedCategories = $product->categories->pluck('id')->toArray();
        }

        $this->isModalOpen = true;
    }

    public function addSpecification() {
        $this->specifications[] = ['name' => '', 'value' => ''];
    }

    public function removeSpecification($index) {
        unset($this->specifications[$index]);
        $this->specifications = array_values($this->specifications);
    }

    public function removeImage($imageId) {
        $image = Image::findOrFail($imageId);
        $image->delete();
    }

    public function save() {
        $this->validate();

        $specsJson = [];
        foreach ($this->specifications as $spec) {
            if (!empty($spec['name'])) {
                $specsJson[$spec['name']] = $spec['value'];
            }
        }

        $product = Product::updateOrCreate(
            ['id' => $this->productId],
            [
                'brand_id' => empty($this->brand_id) ? null : $this->brand_id,
                'sku' => $this->sku,
                'barcode' => $this->barcode,
                'title' => $this->title,
                'slug' => $this->slug,
                'description' => $this->description,
                'cost_price_excl_vat' => $this->cost_price_excl_vat,
                'selling_price_excl_vat' => $this->selling_price_excl_vat,
                'vat_percentage' => $this->vat_percentage,
                'discount_percentage' => $this->discount_percentage,
                'specifications' => $specsJson, 
                'additional_information' => $this->additional_information,
                'is_active' => $this->is_active,
            ]
        );

        $product->categories()->sync($this->selectedCategories);

        if (!empty($this->newImages)) {
            foreach ($this->newImages as $image) {
                $path = $image->store('images/products', 'public');
                $product->images()->create([
                    'path' => $path,
                    'filename' => $image->getClientOriginalName(),
                    'alt_text' => $product->title,
                    'is_primary' => $product->images()->count() === 0 ? true : false,
                ]);
            }
        }

        $this->isModalOpen = false;
        $this->dispatch('product-saved');
    }

    public function delete($id) {
        $product = Product::findOrFail($id);
        $product->categories()->detach();
        $product->images()->delete(); 
        $product->delete();
    }

    public function updatedCategorySelectorPath($value, $key) {
        $this->categorySelectorPath = array_slice($this->categorySelectorPath, 0, $key + 1);
        if ($value && count($this->getChildrenOptions($value)) > 0) {
            $this->categorySelectorPath[] = '';
        }
    }

    public function addSelectedCategory() {
        $validPath = array_filter($this->categorySelectorPath);
        if (!empty($validPath)) {
            $catId = end($validPath);
            if (!in_array($catId, $this->selectedCategories)) {
                $this->selectedCategories[] = $catId;
            }
        }
        $this->categorySelectorPath = [''];
    }

    public function removeSelectedCategory($id) {
        $this->selectedCategories = array_values(array_diff($this->selectedCategories, [$id]));
    }

    public function getRootCategoriesProperty() {
        return Category::whereNull('parent_id')->orderBy('name')->get();
    }

    public function getChildrenOptions($parentId) {
        if (!$parentId) return [];
        return Category::where('parent_id', $parentId)->orderBy('name')->get();
    }

    public function getSelectedCategoryObjectsProperty() {
        if (empty($this->selectedCategories)) return collect();
        return Category::whereIn('id', $this->selectedCategories)->get();
    }

    public function with() {
        return [
            'products' => Product::with(['brand', 'categories', 'images'])
                ->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('sku', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get()
        ];
    }
}
?>

<div>
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Productos') }}</flux:heading>
                <flux:subheading>{{ __('Administra el inventario de la tienda.') }}</flux:subheading>
            </div>
            <flux:button variant="primary" icon="plus" wire:click="openModal">{{ __('Añadir Producto') }}</flux:button>
        </div>

        <div class="flex gap-4">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Buscar por título o SKU...') }}" class="max-w-sm" />
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Imagen') }}</flux:table.column>
                <flux:table.column>{{ __('Producto') }}</flux:table.column>
                <flux:table.column>{{ __('Marca') }}</flux:table.column>
                <flux:table.column>{{ __('Precio (Sin IVA)') }}</flux:table.column>
                <flux:table.column>{{ __('Estado') }}</flux:table.column>
                <flux:table.column>{{ __('Acciones') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($products as $product)
                    <flux:table.row :key="$product->id">
                        <flux:table.cell>
                            @if($product->images->first())
                                <img src="{{ asset('storage/' . $product->images->first()->path) }}" alt="{{ $product->title }}" class="size-12 object-cover rounded-md border border-gray-200 dark:border-white/10" />
                            @else
                                <div class="size-12 bg-gray-100 dark:bg-white/10 rounded-md flex items-center justify-center text-gray-400 dark:text-white/40">
                                    <flux:icon.photo class="size-6" />
                                </div>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $product->title }}</div>
                            <div class="text-sm text-gray-500 dark:text-zinc-400">SKU: {{ $product->sku }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $product->brand ? $product->brand->name : '--' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            ${{ number_format($product->selling_price_excl_vat, 2) }}
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($product->is_active)
                                <flux:badge color="success">{{ __('Activo') }}</flux:badge>
                            @else
                                <flux:badge color="zinc">{{ __('Inactivo') }}</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button size="sm" variant="subtle" icon="pencil" wire:click="openModal({{ $product->id }})" />
                                <flux:button size="sm" variant="danger" icon="trash" wire:confirm="{{ __('¿Seguro que deseas eliminar este producto?') }}" wire:click="delete({{ $product->id }})" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div>
            {{ $products->links() }}
        </div>

        <flux:modal wire:model="isModalOpen" class="w-full max-w-4xl">
            <form wire:submit.prevent="save" class="flex flex-col gap-6">
                <div>
                    <flux:heading size="lg">{{ $productId ? __('Editar Producto') : __('Nuevo Producto') }}</flux:heading>
                    <flux:subheading>{{ __('Ingresa la información detallada del producto.') }}</flux:subheading>
                </div>

                <!-- Tabs (Simulated with grid or sections) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- General Details -->
                    <div class="flex flex-col gap-4 border border-gray-200 dark:border-white/10 rounded-xl p-4 bg-white dark:bg-white/5">
                        <flux:heading size="md" class="mb-2">{{ __('Detalles Generales') }}</flux:heading>
                        
                        <flux:input wire:model.live.debounce.500ms="title" label="{{ __('Título') }}" />
                        <flux:input wire:model="slug" label="{{ __('Slug') }}" />
                        
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input wire:model="sku" label="{{ __('SKU') }}" />
                            <flux:input wire:model="barcode" label="{{ __('Código de Barras') }}" />
                        </div>

                        <flux:select wire:model="brand_id" label="{{ __('Marca') }}">
                            <flux:select.option value="">-- Ninguna Marca --</flux:select.option>
                            @foreach($brands as $brand)
                                <flux:select.option value="{{ $brand->id }}">{{ $brand->name }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:field>
                            <flux:label>{{ __('Añadir Categoría') }}</flux:label>
                            <div class="flex flex-col gap-2 border border-gray-200 dark:border-white/10 rounded-lg p-3 bg-gray-50 dark:bg-white/5">
                                @foreach($categorySelectorPath as $index => $selectedId)
                                    <flux:select wire:model.live="categorySelectorPath.{{ $index }}" placeholder="Nivel {{ $index + 1 }}">
                                        <flux:select.option value="">-- Seleccionar Nivel {{ $index + 1 }} --</flux:select.option>
                                        @php
                                            $options = $index === 0 ? $this->rootCategories : $this->getChildrenOptions($categorySelectorPath[$index - 1]);
                                        @endphp
                                        @foreach($options as $cat)
                                            <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                @endforeach
                                <flux:button size="sm" variant="subtle" icon="plus" wire:click="addSelectedCategory" class="mt-2 text-primary">{{ __('Agregar a la lista') }}</flux:button>
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Categorías Seleccionadas') }}</flux:label>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @forelse($this->selectedCategoryObjects as $cat)
                                    <flux:badge color="success" class="flex items-center gap-1 cursor-default">
                                        {{ $cat->name }}
                                        <button type="button" wire:click="removeSelectedCategory({{ $cat->id }})" class="ml-1 hover:text-red-700">
                                            <flux:icon.x-mark class="size-3" />
                                        </button>
                                    </flux:badge>
                                @empty
                                    <span class="text-sm text-gray-500 dark:text-zinc-500">Ninguna categoría seleccionada.</span>
                                @endforelse
                            </div>
                            <flux:error name="selectedCategories" />
                        </flux:field>
                    </div>

                    <!-- Pricing & Specs -->
                    <div class="flex flex-col gap-4 border border-gray-200 dark:border-white/10 rounded-xl p-4 bg-white dark:bg-white/5">
                        <flux:heading size="md" class="mb-2">{{ __('Precios y Métricas') }}</flux:heading>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input type="number" step="0.01" wire:model.live.debounce.500ms="final_price_incl_vat" label="{{ __('Precio Final de Venta (Con IVA)') }}" icon="currency-dollar" />
                            <!-- Disabled or readonly input just showing calculated price -->
                            <flux:field>
                                <flux:label>{{ __('Precio Base Autónomo (Sin IVA)') }}</flux:label>
                                <div class="w-full px-3 py-2 border rounded-lg bg-gray-50 text-gray-500 font-medium border-gray-200">
                                    ${{ number_format($selling_price_excl_vat, 2) }}
                                </div>
                            </flux:field>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input type="number" step="0.01" wire:model.live.debounce.500ms="cost_price_excl_vat" label="{{ __('Costo de Compra (Sin IVA)') }}" icon="currency-dollar" />
                            <flux:input type="number" step="0.01" wire:model.live.debounce.500ms="vat_percentage" label="{{ __('IVA (%)') }}" icon="receipt-percent" />
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input type="number" step="0.01" wire:model.live.debounce.500ms="discount_percentage" label="{{ __('Descuento de Promoción (%)') }}" icon="receipt-percent" />
                            
                        <flux:field>
                                <flux:label>{{ __('Margen de Ganancia Neto') }}</flux:label>
                                <div class="w-full px-3 py-2 border rounded-lg {{ $this->profitMargin > 0 ? 'bg-green-50 dark:bg-green-500/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-500/30' : 'bg-red-50 dark:bg-red-500/20 text-red-700 dark:text-red-400 border-red-200 dark:border-red-500/30' }} font-bold text-center">
                                    {{ $this->profitMargin }}%
                                </div>
                            </flux:field>
                        </div>
                        
                        <hr class="my-2 border-gray-200 dark:border-white/10" />
                        
                        <flux:heading size="md" class="mb-2">{{ __('Atributos y Especificaciones') }}</flux:heading>
                        
                        @foreach($specifications as $index => $spec)
                            <div class="flex items-center gap-2">
                                <flux:input wire:model="specifications.{{ $index }}.name" placeholder="Ej: Color" class="flex-1" />
                                <flux:input wire:model="specifications.{{ $index }}.value" placeholder="Ej: Rojo" class="flex-1" />
                                <flux:button variant="danger" icon="trash" size="sm" wire:click="removeSpecification({{ $index }})" />
                            </div>
                        @endforeach
                        
                        <flux:button variant="subtle" icon="plus" size="sm" wire:click="addSpecification">{{ __('Agregar Especificación') }}</flux:button>
                    </div>
                </div>

                <!-- Descriptions & Images -->
                <div class="flex flex-col gap-4 border border-gray-200 dark:border-white/10 rounded-xl p-4 bg-white dark:bg-white/5">
                    <flux:heading size="md" class="mb-2">{{ __('Contenidos Adicionales') }}</flux:heading>
                    
                    <flux:textarea wire:model="description" label="{{ __('Descripción Breve') }}" rows="3" />
                    <flux:textarea wire:model="additional_information" label="{{ __('Información Adicional (HTML Detallado)') }}" rows="4" />
                    <flux:checkbox wire:model="is_active" label="{{ __('Producto Activo y Visible en Tienda') }}" />

                    <flux:field>
                        <flux:label>{{ __('Subir Imágenes') }}</flux:label>
                        <input type="file" multiple wire:model="newImages" class="block w-full text-sm text-gray-500 dark:text-zinc-400
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-semibold
                            file:bg-primary dark:file:bg-white/10 file:text-fondo dark:file:text-white
                            hover:file:bg-secondary dark:hover:file:bg-white/20 cursor-pointer" />
                        <flux:error name="newImages.*" />
                    </flux:field>
                    
                    @if ($productId && \App\Models\Product::find($productId)->images->count() > 0)
                        <div class="mt-4">
                            <flux:heading size="sm" class="mb-2">{{ __('Imágenes Antiguas') }}</flux:heading>
                            <div class="flex gap-4 overflow-x-auto pb-2">
                                @foreach(\App\Models\Product::find($productId)->images as $existingImage)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $existingImage->path) }}" class="size-24 object-cover rounded-md border border-gray-200 dark:border-white/10" />
                                        <button type="button" wire:click="removeImage({{ $existingImage->id }})" wire:confirm="¿Borrar imagen permanentemente?" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <flux:icon.x-mark class="size-3" />
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if ($newImages)
                        <div class="mt-4">
                            <flux:heading size="sm" class="mb-2">{{ __('Nuevas Imágenes (Sin Guardar)') }}</flux:heading>
                            <div class="flex gap-4 overflow-x-auto pb-2">
                                @foreach($newImages as $tempImage)
                                    <img src="{{ $tempImage->temporaryUrl() }}" class="size-24 object-cover rounded-md border border-primary dark:border-white/30" />
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-200 dark:border-white/10 pt-4">
                    <flux:button variant="subtle" wire:click="$set('isModalOpen', false)">{{ __('Cancelar') }}</flux:button>
                    <flux:button variant="primary" type="submit">{{ __('Guardar Producto') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    </div>
</div>
