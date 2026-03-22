<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head', ['disableDarkMode' => true])
</head>

<body class="min-h-screen bg-white">

    <flux:header container class="bg-primary flex items-center justify-between">

        <x-app-logo href="{{ route('home') }}" wire:navigate />

        <flux:navbar class=" w-full space-x-3 hidden md:flex">
            <livewire:category.menu />
            <div class=" w-full mr-3">
                <flux:input class=" w-full" icon="magnifying-glass" placeholder="buscar Producto" />
            </div>
        </flux:navbar>

        <flux:spacer />

       <x-desktop-user-menu />

        <div class="ml-3">
            <div class=" text-3xl relative px-3">
                <i class="fas fa-cart-arrow-down text-gray-900 pt-2"></i>
                <div
                    class=" absolute -right-2 -top-1 bg-acento border-2 border-secondary w-7 h-7 rounded-full text-xs flex items-center justify-center text-white">
                    100
                </div>
            </div>
        </div>
    </flux:header>

    <div class="md:hidden w-full bg-secondary h-14 px-4 flex items-center gap-2 relative z-30">
        <livewire:category.mobile-menu />
        <div class="flex-1 min-w-0">
            <flux:input class="w-full" icon="magnifying-glass" placeholder="Buscar producto..." />
        </div>
    </div>

    <div>
        {{ $slot }}
    </div>

    @fluxScripts
</body>

</html>
