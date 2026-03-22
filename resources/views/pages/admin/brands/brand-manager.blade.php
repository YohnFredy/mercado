<?php

use App\Models\Brand;
use App\Models\Image;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin')] class extends Component {
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    
    // Form fields
    public $brandId = null;
    public $name = '';
    public $slug = '';
    public $description = '';
    public $is_active = true;
    public $image;

    public $isModalOpen = false;

    public function rules() {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:brands,slug,' . $this->brandId,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function updatedName() {
        if (!$this->brandId) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function openModal($id = null) {
        $this->resetValidation();
        $this->reset(['brandId', 'name', 'slug', 'description', 'is_active', 'image']);
        
        if ($id) {
            $brand = Brand::findOrFail($id);
            $this->brandId = $brand->id;
            $this->name = $brand->name;
            $this->slug = $brand->slug;
            $this->description = $brand->description;
            $this->is_active = $brand->is_active;
        }

        $this->isModalOpen = true;
    }

    public function save() {
        $this->validate();

        $brand = Brand::updateOrCreate(
            ['id' => $this->brandId],
            [
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]
        );

        if ($this->image) {
            $path = $this->image->store('images/brands', 'public');
            
            if ($brand->images()->exists()) {
                $brand->images()->delete();
            }

            $brand->images()->create([
                'path' => $path,
                'filename' => $this->image->getClientOriginalName(),
                'alt_text' => $brand->name,
                'is_primary' => true,
            ]);
        }

        $this->isModalOpen = false;
        $this->dispatch('brand-saved');
    }

    public function delete($id) {
        $brand = Brand::findOrFail($id);
        $brand->images()->delete();
        $brand->delete();
    }

    public function with() {
        return [
            'brands' => Brand::with('images')
                ->where('name', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10)
        ];
    }
}
?>

<div>
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Marcas') }}</flux:heading>
                <flux:subheading>{{ __('Administra las marcas de la tienda.') }}</flux:subheading>
            </div>
            <flux:button variant="primary" icon="plus" wire:click="openModal">{{ __('Añadir Marca') }}</flux:button>
        </div>

        <div class="flex gap-4">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Buscar por nombre...') }}" class="max-w-sm" />
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Imagen') }}</flux:table.column>
                <flux:table.column>{{ __('Nombre') }}</flux:table.column>
                <flux:table.column>{{ __('Estado') }}</flux:table.column>
                <flux:table.column>{{ __('Acciones') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($brands as $brand)
                    <flux:table.row :key="$brand->id">
                        <flux:table.cell>
                            @if($brand->images->first())
                                <img src="{{ asset('storage/' . $brand->images->first()->path) }}" alt="{{ $brand->name }}" class="size-12 object-cover rounded-md" />
                            @else
                                <div class="size-12 bg-gray-100 dark:bg-white/10 rounded-md flex items-center justify-center text-gray-400 dark:text-white/40">
                                    <flux:icon.photo class="size-6" />
                                </div>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="font-medium text-gray-900 dark:text-zinc-50">{{ $brand->name }}</div>
                            <div class="text-sm text-gray-500 dark:text-zinc-400">{{ $brand->slug }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($brand->is_active)
                                <flux:badge color="success">{{ __('Activo') }}</flux:badge>
                            @else
                                <flux:badge color="zinc">{{ __('Inactivo') }}</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button size="sm" variant="subtle" icon="pencil" wire:click="openModal({{ $brand->id }})" />
                                <flux:button size="sm" variant="danger" icon="trash" wire:confirm="{{ __('¿Seguro que deseas eliminar esta marca?') }}" wire:click="delete({{ $brand->id }})" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div>
            {{ $brands->links() }}
        </div>

        <flux:modal wire:model="isModalOpen" class="max-w-lg">
            <form wire:submit.prevent="save" class="flex flex-col gap-6">
                <div>
                    <flux:heading size="lg">{{ $brandId ? __('Editar Marca') : __('Nueva Marca') }}</flux:heading>
                    <flux:subheading>{{ __('Rellena la información de la marca.') }}</flux:subheading>
                </div>

                <flux:input wire:model.blur="name" label="{{ __('Nombre') }}" />
                <flux:input wire:model="slug" label="{{ __('Slug') }}" placeholder="mi-marca-genial" />
                <flux:textarea wire:model="description" label="{{ __('Descripción') }}" rows="3" />
                <flux:checkbox wire:model="is_active" label="{{ __('Activa') }}" />
                
                <flux:field>
                    <flux:label>{{ __('Imagen de la Marca') }}</flux:label>
                    <input type="file" wire:model="image" class="block w-full text-sm text-gray-500 dark:text-zinc-400
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-primary dark:file:bg-white/10 file:text-fondo dark:file:text-white
                        hover:file:bg-secondary dark:hover:file:bg-white/20 cursor-pointer" />
                    <flux:error name="image" />
                    
                    @if ($image)
                        <div class="mt-4">
                            <img src="{{ $image->temporaryUrl() }}" class="size-32 object-cover rounded-md" />
                        </div>
                    @elseif ($brandId && \App\Models\Brand::find($brandId)->images->first())
                        <div class="mt-4">
                            <img src="{{ asset('storage/' . \App\Models\Brand::find($brandId)->images->first()->path) }}" class="size-32 object-cover rounded-md" />
                        </div>
                    @endif
                </flux:field>

                <div class="flex justify-end gap-2">
                    <flux:button variant="subtle" wire:click="$set('isModalOpen', false)">{{ __('Cancelar') }}</flux:button>
                    <flux:button variant="primary" type="submit">{{ __('Guardar') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    </div>
</div>
