<?php

use App\Models\Category;
use App\Models\Image;
use App\Services\ImageOptimizer;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin')] class extends Component {
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    
    // Form fields
    public $categoryId = null;
    public $parent_id = null;
    public $name = '';
    public $slug = '';
    public $description = '';
    public $is_active = true;
    public $image;
    public $categoryPath = [''];

    public $isModalOpen = false;

    public function rules() {
        return [
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $this->categoryId,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function updatedName() {
        if (!$this->categoryId) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function openModal($id = null) {
        $this->resetValidation();
        $this->reset(['categoryId', 'parent_id', 'name', 'slug', 'description', 'is_active', 'image', 'categoryPath']);
        $this->categoryPath = [''];
        
        if ($id) {
            $category = Category::findOrFail($id);
            $this->categoryId = $category->id;
            $this->parent_id = $category->parent_id;
            $this->name = $category->name;
            $this->slug = $category->slug;
            $this->description = $category->description;
            $this->is_active = $category->is_active;

            $path = [];
            $current = $category->parent;
            while ($current) {
                array_unshift($path, $current->id);
                $current = $current->parent;
            }
            $this->categoryPath = $path;
            if (empty($this->categoryPath) || count($this->getChildrenOptions(end($this->categoryPath))) > 0) {
                $this->categoryPath[] = '';
            }
        }

        $this->isModalOpen = true;
    }

    public function save() {
        Gate::authorize($this->categoryId ? 'categories:edit' : 'categories:create');
        
        $this->validate();

        // Prevent a category from being its own parent or descending from itself
        if ($this->categoryId && $this->parent_id == $this->categoryId) {
            $this->addError('parent_id', __('No puede ser padre de sí misma.'));
            return;
        }

        $category = Category::updateOrCreate(
            ['id' => $this->categoryId],
            [
                'parent_id' => empty($this->parent_id) ? null : $this->parent_id,
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]
        );

        if ($this->image) {
            $optimizer = app(ImageOptimizer::class);
            $path = $optimizer->optimize($this->image, 'images/categories');
            
            if ($category->images()->exists()) {
                $category->images()->delete();
            }

            $category->images()->create([
                'path' => $path,
                'filename' => $this->image->getClientOriginalName(),
                'alt_text' => $category->name,
                'is_primary' => true,
            ]);
        }

        $this->isModalOpen = false;
        $this->dispatch('category-saved');
    }

    public function delete($id) {
        Gate::authorize('categories:delete');
        
        $category = Category::findOrFail($id);
        
        // Prevent deletion if it has children
        if ($category->children()->count() > 0) {
            $this->addError('delete', __('No se puede eliminar una categoría con subcategorías.'));
            return;
        }

        $category->images()->delete();
        $category->delete();
    }

    public function updatedCategoryPath($value, $key) {
        $this->categoryPath = array_slice($this->categoryPath, 0, $key + 1);
        if ($value && count($this->getChildrenOptions($value)) > 0) {
            $this->categoryPath[] = '';
        }
        $validPath = array_filter($this->categoryPath);
        $this->parent_id = empty($validPath) ? null : end($validPath);
    }

    public function getRootCategoriesProperty() {
        return Category::whereNull('parent_id')
            ->whereNotIn('id', $this->getInvalidParentIds())
            ->orderBy('name')->get();
    }

    public function getChildrenOptions($parentId) {
        if (!$parentId) return [];
        return Category::where('parent_id', $parentId)
            ->whereNotIn('id', $this->getInvalidParentIds())
            ->orderBy('name')->get();
    }

    private function getInvalidParentIds() {
        if (!$this->categoryId) return [];
        $invalidIds = [$this->categoryId];
        $this->getDescendantIds($this->categoryId, $invalidIds);
        return $invalidIds;
    }

    private function getDescendantIds($parentId, &$invalidIds) {
        $children = Category::where('parent_id', $parentId)->pluck('id')->toArray();
        foreach ($children as $childId) {
            $invalidIds[] = $childId;
            $this->getDescendantIds($childId, $invalidIds);
        }
    }

    public function with() {
        return [
            'categories' => Category::with(['images', 'parent'])
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
                <flux:heading size="xl">{{ __('Categorías') }}</flux:heading>
                <flux:subheading>{{ __('Administra las categorías de la tienda y su jerarquía.') }}</flux:subheading>
            </div>
            @can('categories:create')
            <flux:button variant="primary" icon="plus" wire:click="openModal">{{ __('Añadir Categoría') }}</flux:button>
            @endcan
        </div>

        @error('delete')
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                {{ $message }}
            </div>
        @enderror

        <div class="flex gap-4">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Buscar categoría...') }}" class="max-w-sm" />
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Imagen') }}</flux:table.column>
                <flux:table.column>{{ __('Nombre') }}</flux:table.column>
                <flux:table.column>{{ __('Padre') }}</flux:table.column>
                <flux:table.column>{{ __('Estado') }}</flux:table.column>
                <flux:table.column>{{ __('Acciones') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($categories as $category)
                    <flux:table.row :key="$category->id">
                        <flux:table.cell>
                            @if($category->images->first())
                                <img src="{{ asset('storage/' . $category->images->first()->path) }}" alt="{{ $category->name }}" class="size-12 object-cover rounded-md border border-gray-200 dark:border-white/10" />
                            @else
                                <div class="size-12 bg-gray-100 dark:bg-white/10 rounded-md flex items-center justify-center text-gray-400 dark:text-white/40">
                                    <flux:icon.photo class="size-6" />
                                </div>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $category->name }}</div>
                            <div class="text-sm text-gray-500 dark:text-zinc-400">{{ $category->slug }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($category->parent)
                                <div class="text-gray-900 dark:text-zinc-100">{{ $category->parent->name }}</div>
                            @else
                                <span class="text-gray-400 dark:text-zinc-500 italic">-- Ninguno --</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($category->is_active)
                                <flux:badge color="success">{{ __('Activo') }}</flux:badge>
                            @else
                                <flux:badge color="zinc">{{ __('Inactivo') }}</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                @can('categories:edit')
                                <flux:button size="sm" variant="subtle" icon="pencil" wire:click="openModal({{ $category->id }})" />
                                @endcan
                                @can('categories:delete')
                                <flux:button size="sm" variant="danger" icon="trash" wire:confirm="{{ __('¿Seguro que deseas eliminar esta categoría?') }}" wire:click="delete({{ $category->id }})" />
                                @endcan
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div>
            {{ $categories->links() }}
        </div>

        <flux:modal wire:model="isModalOpen" class="max-w-xl">
            <form wire:submit.prevent="save" class="flex flex-col gap-6">
                <div>
                    <flux:heading size="lg">{{ $categoryId ? __('Editar Categoría') : __('Nueva Categoría') }}</flux:heading>
                    <flux:subheading>{{ __('Rellena la información de la categoría y selecciona su jerarquía.') }}</flux:subheading>
                </div>

                <flux:field>
                    <flux:label>{{ __('Ruta de la Categoría Padre') }}</flux:label>
                    <div class="flex flex-col gap-2 border border-gray-200 dark:border-white/10 rounded-lg p-3 bg-gray-50 dark:bg-white/5">
                        @foreach($categoryPath as $index => $selectedId)
                            <flux:select wire:model.live="categoryPath.{{ $index }}" placeholder="Nivel {{ $index + 1 }}">
                                <flux:select.option value="">-- {{ $index === 0 ? 'Ninguno (Categoría Principal)' : 'Seleccionar Subcategoría' }} --</flux:select.option>
                                @php
                                    $options = $index === 0 ? $this->rootCategories : $this->getChildrenOptions($categoryPath[$index - 1]);
                                @endphp
                                @foreach($options as $cat)
                                    <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        @endforeach
                    </div>
                    @error('parent_id') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model.blur="name" label="{{ __('Nombre') }}" />
                    <flux:input wire:model="slug" label="{{ __('Slug') }}" placeholder="mi-categoria" />
                </div>
                
                <flux:textarea wire:model="description" label="{{ __('Descripción') }}" rows="3" />
                <flux:checkbox wire:model="is_active" label="{{ __('Activa') }}" />
                
                <flux:field>
                    <flux:label>{{ __('Imagen Representativa') }}</flux:label>
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
                    @elseif ($categoryId && \App\Models\Category::find($categoryId)->images->first())
                        <div class="mt-4">
                            <img src="{{ asset('storage/' . \App\Models\Category::find($categoryId)->images->first()->path) }}" class="size-32 object-cover rounded-md" />
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
