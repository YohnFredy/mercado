<?php

use App\Models\City;
use App\Models\Department;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] class extends Component {
    use WithPagination;

    public string $search = '';

    /** ID del departamento seleccionado para ver sus ciudades */
    public ?int $selectedDepartmentId = null;

    /** Búsqueda dentro de las ciudades del departamento */
    public string $citySearch = '';

    /** Modal de edición de costo */
    public bool $isCostModalOpen = false;
    public ?int $editingCityId    = null;
    public string $editingCityName = '';
    public string $editingCost    = '0';

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selectedDepartmentId = null;
    }

    /**
     * Toggle the is_active flag of a department.
     */
    public function toggleDepartment(int $id): void
    {
        Gate::authorize('shipping:edit');

        $department            = Department::findOrFail($id);
        $department->is_active = ! $department->is_active;
        $department->save();
    }

    /**
     * Select a department to manage its cities.
     */
    public function selectDepartment(int $id): void
    {
        $this->selectedDepartmentId = ($this->selectedDepartmentId === $id) ? null : $id;
        $this->citySearch           = '';
    }

    /**
     * Toggle the is_active flag of a city.
     */
    public function toggleCity(int $id): void
    {
        Gate::authorize('shipping:edit');

        $city            = City::findOrFail($id);
        $city->is_active = ! $city->is_active;
        $city->save();
    }

    /**
     * Open the shipping cost editor for a city.
     */
    public function openCostModal(int $cityId): void
    {
        Gate::authorize('shipping:edit');

        $city                   = City::findOrFail($cityId);
        $this->editingCityId    = $city->id;
        $this->editingCityName  = $city->name;
        $this->editingCost      = (string) $city->cost;
        $this->isCostModalOpen  = true;
    }

    /**
     * Persist the updated shipping cost.
     */
    public function saveCost(): void
    {
        Gate::authorize('shipping:edit');

        $this->validate(['editingCost' => 'required|numeric|min:0']);

        City::findOrFail($this->editingCityId)->update(['cost' => $this->editingCost]);

        $this->isCostModalOpen = false;
        $this->editingCityId   = null;
    }

    /**
     * Departments list with optional search, paginated.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator<Department>
     */
    #[Computed]
    public function departments()
    {
        return Department::when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->withCount(['cities', 'cities as active_cities_count' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->paginate(15);
    }

    /**
     * Cities of the selected department, with optional search.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, City>
     */
    #[Computed]
    public function selectedDepartmentCities()
    {
        if (! $this->selectedDepartmentId) {
            return collect();
        }

        return City::where('department_id', $this->selectedDepartmentId)
            ->when($this->citySearch, fn ($q) => $q->where('name', 'like', '%' . $this->citySearch . '%'))
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function selectedDepartment(): ?Department
    {
        if (! $this->selectedDepartmentId) {
            return null;
        }

        return Department::find($this->selectedDepartmentId);
    }

    public function with(): array
    {
        return [];
    }
}
?>

<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Zonas de Entrega</flux:heading>
            <flux:subheading>Activa o desactiva departamentos y ciudades para controlar la cobertura de envíos.</flux:subheading>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @php
            $totalDepts  = \App\Models\Department::count();
            $activeDepts = \App\Models\Department::where('is_active', true)->count();
            $totalCities = \App\Models\City::count();
            $activeCities= \App\Models\City::where('is_active', true)->count();
        @endphp
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
            <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-widest font-semibold">Departamentos activos</p>
            <p class="text-2xl font-black text-primary mt-1">{{ $activeDepts }} <span class="text-sm font-normal text-zinc-400">/ {{ $totalDepts }}</span></p>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
            <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-widest font-semibold">Ciudades activas</p>
            <p class="text-2xl font-black text-primary mt-1">{{ $activeCities }} <span class="text-sm font-normal text-zinc-400">/ {{ $totalCities }}</span></p>
        </div>
        <div class="col-span-2 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-700/50 p-4 flex items-center gap-3">
            <flux:icon.information-circle class="size-5 text-amber-500 shrink-0" />
            <p class="text-xs text-amber-700 dark:text-amber-300">Solo las ciudades <strong>activas</strong> aparecerán en el checkout. Si desactivas un departamento, sus ciudades tampoco estarán disponibles.</p>
        </div>
    </div>

    {{-- Buscador de departamentos --}}
    <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
        placeholder="Buscar departamento..." class="max-w-sm" />

    {{-- Tabla de departamentos --}}
    <flux:table>
        <flux:table.columns>
            <flux:table.column>Departamento</flux:table.column>
            <flux:table.column>Ciudades</flux:table.column>
            <flux:table.column>Estado</flux:table.column>
            <flux:table.column>Acciones</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->departments as $department)
                <flux:table.row :key="$department->id">
                    <flux:table.cell>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-50">{{ $department->name }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-1.5 text-sm">
                            <span class="font-bold text-primary">{{ $department->active_cities_count }}</span>
                            <span class="text-zinc-400">/</span>
                            <span class="text-zinc-500">{{ $department->cities_count }}</span>
                            <span class="text-zinc-400 text-xs">ciudades activas</span>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($department->is_active)
                            <flux:badge color="green">Activo</flux:badge>
                        @else
                            <flux:badge color="zinc">Inactivo</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            @can('shipping:edit')
                            {{-- Toggle activo/inactivo --}}
                            <flux:button
                                size="sm"
                                :variant="$department->is_active ? 'danger' : 'primary'"
                                :icon="$department->is_active ? 'x-circle' : 'check-circle'"
                                wire:click="toggleDepartment({{ $department->id }})"
                                wire:confirm="{{ $department->is_active ? '¿Desactivar este departamento? Los clientes no podrán elegir ninguna de sus ciudades.' : '¿Activar este departamento?' }}"
                            />
                            @endcan
                            {{-- Ver ciudades --}}
                            <flux:button
                                size="sm"
                                variant="subtle"
                                icon="{{ $selectedDepartmentId === $department->id ? 'chevron-up' : 'chevron-down' }}"
                                wire:click="selectDepartment({{ $department->id }})"
                            />
                        </div>
                    </flux:table.cell>
                </flux:table.row>

                {{-- Ciudades expandidas --}}
                @if ($selectedDepartmentId === $department->id)
                <flux:table.row :key="'cities-' . $department->id">
                    <flux:table.cell colspan="4" class="p-0">
                        <div class="bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-200 dark:border-zinc-700 px-6 py-4">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-sm font-bold text-zinc-700 dark:text-zinc-200">
                                    Ciudades de {{ $department->name }}
                                </p>
                                <flux:input
                                    wire:model.live.debounce.200ms="citySearch"
                                    icon="magnifying-glass"
                                    placeholder="Buscar ciudad..."
                                    size="sm"
                                    class="max-w-xs"
                                />
                            </div>

                            @if ($this->selectedDepartmentCities->isEmpty())
                                <p class="text-sm text-zinc-400 italic py-2">No se encontraron ciudades.</p>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                    @foreach ($this->selectedDepartmentCities as $city)
                                    <div class="flex items-center justify-between bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 px-3 py-2 gap-2">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <div class="size-2 rounded-full shrink-0 {{ $city->is_active ? 'bg-green-500' : 'bg-zinc-300' }}"></div>
                                            <span class="text-sm font-medium text-zinc-800 dark:text-zinc-100 truncate">{{ $city->name }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <span class="text-xs text-zinc-500 font-mono">
                                                ${{ number_format($city->cost, 0, ',', '.') }}
                                            </span>
                                            @can('shipping:edit')
                                            <flux:button
                                                size="xs"
                                                variant="subtle"
                                                icon="pencil-square"
                                                wire:click="openCostModal({{ $city->id }})"
                                            />
                                            <flux:button
                                                size="xs"
                                                :variant="$city->is_active ? 'danger' : 'primary'"
                                                :icon="$city->is_active ? 'x-mark' : 'check'"
                                                wire:click="toggleCity({{ $city->id }})"
                                            />
                                            @endcan
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @endif

            @empty
                <flux:table.row>
                    <flux:table.cell colspan="4">
                        <p class="text-sm text-zinc-400 italic text-center py-4">No se encontraron departamentos.</p>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{ $this->departments->links() }}

    {{-- Modal: editar costo de envío --}}
    <flux:modal wire:model="isCostModalOpen" class="max-w-sm">
        <form wire:submit.prevent="saveCost" class="flex flex-col gap-5">
            <div>
                <flux:heading size="lg">Costo de Envío</flux:heading>
                <flux:subheading>Ciudad: <strong>{{ $editingCityName }}</strong></flux:subheading>
            </div>

            <flux:field>
                <flux:label>Costo de envío (COP)</flux:label>
                <flux:input
                    wire:model="editingCost"
                    type="number"
                    min="0"
                    step="100"
                    placeholder="0"
                    icon-leading="currency-dollar"
                />
                <flux:error name="editingCost" />
                <flux:description>Ingresa 0 para envío gratis.</flux:description>
            </flux:field>

            <div class="flex justify-end gap-2">
                <flux:button variant="subtle" wire:click="$set('isCostModalOpen', false)">Cancelar</flux:button>
                <flux:button variant="primary" type="submit">Guardar</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
