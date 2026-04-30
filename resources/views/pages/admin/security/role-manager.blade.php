<?php

use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;

new #[Layout('layouts.admin')] class extends Component {
    public $roles;
    public $permissions;
    public $selectedRole = null;

    // new role
    public $newRoleName = '';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->roles = Role::with('permissions')->get();
        // Group permissions by module to make it professional
        // e.g. "orders:manage" -> "orders"
        $this->permissions = collect(Permission::all())->groupBy(function ($item) {
            $parts = explode(':', $item->name);
            return $parts[0] ?? 'general';
        });
    }

    public function createRole()
    {
        Gate::authorize('roles:create');

        $this->validate(['newRoleName' => 'required|unique:roles,name']);
        // Sanitize name for consistency (lowercase)
        Role::create(['name' => strtolower($this->newRoleName)]);
        $this->newRoleName = '';
        $this->loadData();
        \Flux::modal('create-role-modal')->close();
    }

    public function selectRole($id)
    {
        $this->selectedRole = Role::findById($id);
    }

    public function togglePermission($permissionName)
    {
        Gate::authorize('roles:edit');

        if (!$this->selectedRole) return;

        if ($this->selectedRole->hasPermissionTo($permissionName)) {
            $this->selectedRole->revokePermissionTo($permissionName);
        } else {
            $this->selectedRole->givePermissionTo($permissionName);
        }
        $this->loadData();
        $this->selectedRole = Role::findById($this->selectedRole->id); // refresh
    }

    public function deleteRole($id)
    {
        Gate::authorize('roles:delete');

        $role = Role::findById($id);
        if ($role->name !== 'admin') { // prevent deleting super admin
            $role->delete();
            $this->selectedRole = null;
            $this->loadData();
        }
    }
}; ?>

<div class="max-w-7xl mx-auto py-6 space-y-6">
    <flux:heading size="xl" class="mb-6">Seguridad del Sistema</flux:heading>

    <div class="flex flex-col gap-6 md:flex-row">
        <!-- Sidebar with roles list -->
        <div class="w-full md:w-1/3 bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 dark:border-gray-700">
                <flux:heading size="lg">Roles Activos</flux:heading>
                @can('roles:create')
                <flux:modal.trigger name="create-role-modal">
                    <flux:button size="sm" icon="plus" variant="primary">Nuevo</flux:button>
                </flux:modal.trigger>
                @endcan
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($roles as $role)
                <div
                    wire:click="selectRole({{ $role->id }})"
                    class="p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ $selectedRole && $selectedRole->id === $role->id ? 'bg-indigo-50/50 border-l-4 border-indigo-500 dark:bg-indigo-900/20' : 'border-l-4 border-transparent' }}">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white capitalize">{{ $role->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $role->permissions->count() }} permisos asignados</p>
                        </div>
                        @if($role->name === 'admin')
                        <flux:badge color="red" size="sm">Super Admin</flux:badge>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Main content: Permissions matrix -->
        <div class="w-full md:w-2/3">
            @if($selectedRole)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 dark:bg-gray-800 dark:border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <flux:heading size="xl" class="capitalize">Configuración de: {{ $selectedRole->name }}</flux:heading>
                        @if($selectedRole->name === 'admin')
                        <p class="text-sm text-gray-500 mt-1">El modo "Dios" del sistema. No requiere asignación individual.</p>
                        @else
                        <p class="text-sm text-gray-500 mt-1">Habilita o deshabilita los permisos por módulo.</p>
                        @endif
                    </div>

                    @if($selectedRole->name !== 'admin')
                        @can('roles:delete')
                        <flux:button wire:click="deleteRole({{ $selectedRole->id }})" wire:confirm="¿Estás seguro de eliminar este rol del sistema?" icon="trash" variant="danger" size="sm">Eliminar Rol</flux:button>
                        @endcan
                    @endif
                </div>

                @if($selectedRole->name === 'admin')
                <div class="p-8 bg-green-50 rounded-xl border border-green-200 text-center text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400">
                    <flux:icon.shield-check class="mx-auto size-16 mb-4 opacity-75" />
                    <h3 class="text-xl font-bold mb-2">Acceso Total Garantizado</h3>
                    <p class="text-sm md:text-base">El Gate global de seguridad otorga todos los permisos automáticamente a los usuarios con este rol. Tienen control absoluto sobre el panel.</p>
                </div>
                @else
                <div class="space-y-6">
                    @foreach($permissions as $module => $modulePermissions)
                    <div class="bg-gray-50 rounded-lg border border-gray-100 overflow-hidden dark:bg-gray-900/50 dark:border-gray-700">
                        <div class="bg-gray-100 px-4 py-2 border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                            <h4 class="font-bold text-gray-700 dark:text-gray-300 uppercase text-xs tracking-wider flex items-center gap-2">
                                <flux:icon.folder class="size-4" /> {{ $module }}
                            </h4>
                        </div>
                        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach($modulePermissions as $permission)
                            @php
                                $actionName = explode(':', $permission->name)[1] ?? $permission->name;
                                $colorClass = match($actionName) {
                                    'view' => 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/30 border-blue-200 dark:border-blue-800',
                                    'create' => 'text-green-600 bg-green-50 dark:text-green-400 dark:bg-green-900/30 border-green-200 dark:border-green-800',
                                    'edit' => 'text-amber-600 bg-amber-50 dark:text-amber-400 dark:bg-amber-900/30 border-amber-200 dark:border-amber-800',
                                    'delete' => 'text-red-600 bg-red-50 dark:text-red-400 dark:bg-red-900/30 border-red-200 dark:border-red-800',
                                    'approve' => 'text-purple-600 bg-purple-50 dark:text-purple-400 dark:bg-purple-900/30 border-purple-200 dark:border-purple-800',
                                    default => 'text-gray-800 bg-white dark:text-gray-200 dark:bg-gray-800 border-gray-100 dark:border-gray-700'
                                };
                            @endphp
                            <div class="flex justify-between items-center p-3 rounded-lg border shadow-sm transition-all hover:shadow {{ $colorClass }}">
                                <span class="text-sm font-bold uppercase tracking-wider truncate pr-2">
                                    {{ $actionName == 'view' ? 'Ver' : ($actionName == 'create' ? 'Crear' : ($actionName == 'edit' ? 'Editar' : ($actionName == 'delete' ? 'Eliminar' : ($actionName == 'approve' ? 'Aprobar' : $actionName)))) }}
                                </span>
                                @can('roles:edit')
                                <flux:switch
                                    wire:click="togglePermission('{{ $permission->name }}')"
                                    :checked="$selectedRole->hasPermissionTo($permission->name)" />
                                @else
                                <flux:switch
                                    :checked="$selectedRole->hasPermissionTo($permission->name)"
                                    disabled />
                                @endcan
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @else
            <div class="flex flex-col items-center justify-center h-full min-h-[400px] border-2 border-dashed border-gray-200 rounded-xl bg-gray-50/50 dark:bg-gray-800/50 dark:border-gray-700">
                <flux:icon.shield-exclamation class="size-16 text-gray-300 dark:text-gray-600 mb-4" />
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">Panel de Permisos</h3>
                <p class="text-gray-500 max-w-sm text-center mt-2">Selecciona un rol en la barra lateral para inspeccionar y modificar sus capacidades de acceso al sistema.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Create Role Modal -->
    <flux:modal name="create-role-modal" class="min-w-[400px]">
        <div class="p-6">
            <flux:heading size="lg" class="mb-4">Añadir Nuevo Nivel de Acceso</flux:heading>

            <form wire:submit.prevent="createRole">
                <div class="mb-6">
                    <flux:input wire:model="newRoleName" label="Nombre del Rol" placeholder="Ej: operario" required autofocus autocomplete="off" />
                    <p class="text-xs text-gray-500 mt-2">El nombre será guardado en minúsculas automticamente.</p>
                </div>

                <div class="flex justify-end gap-3 border-t pt-4 border-gray-100 dark:border-gray-700 mt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancelar</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">Guardar Rol</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>