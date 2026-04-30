<?php

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;

new #[Layout('layouts.admin')] class extends Component {
    public $search = '';
    public $selectedUser = null;
    public $userRoles = [];
    public $allRoles = [];

    public function mount()
    {
        $this->allRoles = Role::all();
    }

    // We use computed property for search to keep it reactive easily
    public function with()
    {
        return [
            'users' => User::with('roles')
                ->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%')
                ->paginate(15),
        ];
    }

    public function openRoleModal($userId)
    {
        $this->selectedUser = User::with('roles')->findOrFail($userId);
        // Pluck the names of roles currently assigned
        $this->userRoles = $this->selectedUser->roles->pluck('name')->toArray();
        \Flux::modal('edit-user-roles')->show();
    }

    public function saveRoles()
    {
        Gate::authorize('users:edit');

        if ($this->selectedUser) {
            // Prevent removing admin role from the master admin
            if ($this->selectedUser->email === 'fredy.guapacha@gmail.com') {
                if (!in_array('admin', $this->userRoles)) {
                    $this->userRoles[] = 'admin'; // force admin
                }
            }

            // Sync the roles array directly
            $this->selectedUser->syncRoles($this->userRoles);
        }

        $this->selectedUser = null;
        $this->userRoles = [];
        \Flux::modal('edit-user-roles')->close();
    }
}; ?>

<div class="max-w-7xl mx-auto py-6 space-y-6">
    <div class="flex justify-between items-center mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
        <div>
            <flux:heading size="xl">Usuarios del Sistema</flux:heading>
            <p class="text-gray-500 text-sm mt-1">Busca usuarios y gestiona sus niveles de acceso al panel de administración.</p>
        </div>
        <div class="w-72">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Buscar por nombre o correo..." />
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Usuario</flux:table.column>
                <flux:table.column>Correo Electrónico</flux:table.column>
                <flux:table.column>Roles Asignados</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($users as $user)
                <flux:table.row>
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <flux:avatar size="sm" :src="'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random'" />
                            <span class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex flex-wrap gap-1">
                            @forelse ($user->roles as $role)
                            <flux:badge size="sm" :color="$role->name === 'admin' ? 'red' : 'indigo'" class="capitalize">
                                {{ $role->name }}
                            </flux:badge>
                            @empty
                            <span class="text-xs text-gray-400 italic">Sin acceso</span>
                            @endforelse
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        @can('users:edit')
                        <flux:button wire:click="openRoleModal({{ $user->id }})" size="sm" variant="ghost" icon="shield-check">
                            Gestionar Acceso
                        </flux:button>
                        @endcan
                    </flux:table.cell>
                </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Edit Roles Modal -->
    <flux:modal name="edit-user-roles" class="min-w-[400px]">
        @if($selectedUser)
        <div class="p-6">
            <div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">
                <flux:avatar size="lg" :src="'https://ui-avatars.com/api/?name='.urlencode($selectedUser->name).'&background=random'" />
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $selectedUser->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $selectedUser->email }}</p>
                </div>
            </div>

            <flux:heading size="md" class="mb-4">Asignar Roles</flux:heading>

            <div class="space-y-3 mb-8 bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
                @foreach($allRoles as $role)
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-medium text-gray-800 dark:text-gray-200 capitalize">{{ $role->name }}</span>
                        @if($role->name === 'admin')
                        <p class="text-xs text-red-500 font-bold">¡Atención! Este rol otorga control absoluto.</p>
                        @endif
                    </div>
                    <flux:switch
                        wire:model="userRoles"
                        value="{{ $role->name }}"
                        :disabled="$selectedUser->email === 'fredy.guapacha@gmail.com' && $role->name === 'admin'" />
                </div>
                @endforeach
            </div>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button wire:click="saveRoles" variant="primary">Guardar Cambios</flux:button>
            </div>
        </div>
        @endif
    </flux:modal>
</div>