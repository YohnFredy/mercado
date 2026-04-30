@auth
    <flux:dropdown position="bottom" align="end">
        <!-- Botón Disparador (Logueado) -->
        <button class="cursor-pointer bg-white/15 hover:bg-white/25 active:scale-95 px-3 py-2 rounded-xl flex items-center text-white font-bold text-[15px] tracking-wide transition-all duration-200 select-none backdrop-blur-sm border border-white/10 h-[42px]">
            <flux:avatar size="xs" :name="auth()->user()->name" :initials="auth()->user()->initials()" class="mr-2.5 border border-white/20 shadow-sm" />
            <span class="truncate max-w-[0px] md:max-w-[150px]">{{ auth()->user()->name }}</span>
            <flux:icon.chevron-down class="size-4 ml-2 opacity-70" />
        </button>

        <flux:menu class="w-64">
            <div class="flex items-center gap-3 px-2 py-2.5 text-start text-sm border-b border-gray-100 mb-1">
                <flux:avatar size="sm" :name="auth()->user()->name" :initials="auth()->user()->initials()" />
                <div class="grid flex-1 text-start text-sm leading-tight">
                    <flux:heading class="truncate font-bold text-gray-900">{{ auth()->user()->name }}</flux:heading>
                    <flux:text class="truncate text-xs text-gray-500">{{ auth()->user()->email }}</flux:text>
                </div>
            </div>
            
            <flux:menu.group class="pt-1">
                <flux:menu.item :href="route('dashboard')" icon="squares-2x2" wire:navigate class="hover:bg-primary/5 hover:text-primary transition-colors">
                    {{ __('Panel de control') }}
                </flux:menu.item>
                <flux:menu.item :href="route('customer.orders')" icon="shopping-bag" wire:navigate class="hover:bg-primary/5 hover:text-primary transition-colors">
                    {{ __('Mis Pedidos') }}
                </flux:menu.item>
                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate class="hover:bg-primary/5 hover:text-primary transition-colors">
                    {{ __('Configuración') }}
                </flux:menu.item>
            </flux:menu.group>
            
            <flux:menu.separator />
            
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer text-red-600 hover:text-red-700 hover:bg-red-50 transition-colors" data-test="logout-button">
                    {{ __('Cerrar sesión') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
@else
    <flux:dropdown position="bottom" align="end">
        <!-- Botón Disparador (Invitado) -->
        <button class="cursor-pointer bg-white/15 hover:bg-white/25 active:scale-95 px-3 py-2 rounded-xl flex items-center text-white font-bold text-sm tracking-wide transition-all duration-200 select-none backdrop-blur-sm border border-white/10 h-[42px]">
            <flux:icon.user class="size-5 mr-2" stroke-width="2.5" />
            <span class=" hidden md:block">Mi Cuenta</span>
            <flux:icon.chevron-down class="size-4 ml-2 opacity-70" />
        </button>

        <flux:menu class="w-64">
            <div class="px-4 py-4 text-center border-b border-gray-100 mb-1">
                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-3">
                    <flux:icon.user class="size-6 text-primary" stroke-width="2" />
                </div>
                <h3 class="font-extrabold text-gray-900 text-base tracking-tight">¡Bienvenido!</h3>
                <p class="text-[13px] text-gray-500 mt-1 leading-snug">Ingresa a tu cuenta para ver tus pedidos o administrar tu carrito.</p>
            </div>
            
            <flux:menu.group class="pt-1 space-y-1 px-1">
                <flux:menu.item :href="route('login')" icon="arrow-right-end-on-rectangle" wire:navigate class="text-primary font-bold hover:bg-primary/10 hover:text-primary transition-colors rounded-lg">
                    {{ __('Iniciar Sesión') }}
                </flux:menu.item>
                <flux:menu.item :href="route('register')" icon="user-plus" wire:navigate class="hover:bg-primary/5 hover:text-primary transition-colors rounded-lg">
                    {{ __('Crear Cuenta nueva') }}
                </flux:menu.item>
            </flux:menu.group>
        </flux:menu>
    </flux:dropdown>
@endauth
