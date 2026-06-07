<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head', ['disableDarkMode' => true])
</head>

<body class="min-h-screen bg-gray-100 flex flex-col">


    <div class=" bg-primary ">

        <div class="mx-auto w-full max-w-7xl h-16 px-6 lg:px-8 flex justify-between items-center z-40">

            <a href="{{ route('home') }}" class="hidden sm:block shrink-0">
                <div class=" pr-2 py-1 flex">
                    <img src="{{ asset('storage/images/logos/logo.png') }}" alt="" class=" h-12">

                    <div class=" relative flex items-center ml-1 mr-3">
                        <p class=" text-3xl font-extrabold text-white -mt-4 italic">ipermerca</p>
                        <p
                            class=" absolute top-7 left-4 text-sm  font-bold text-secondary-500 text-shadow-sm text-shadow-green-900">
                            Donde sí se gana</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('home') }}" class="shrink-0 sm:hidden">
                <div class="flex flex-col items-center ">
                    <img src="{{ asset('storage/images/logos/logo.png') }}" alt="" class=" h-8 ">
                    <p class="font-extrabold text-white italic">ipermerca</p>
                </div>
            </a>

            <flux:navbar class="flex-1 space-x-3 hidden lg:flex min-w-0">
                <livewire:category.menu />
                <div class="flex-1 mr-3">
                    <form action="{{ route('tienda') }}" method="GET" class="w-full">
                        <flux:input name="search" value="{{ request('search') }}" class="w-full"
                            icon="magnifying-glass" placeholder="buscar Producto" />
                    </form>
                </div>
            </flux:navbar>

            <div class=" flex items-center">
                <x-desktop-user-menu />

                <livewire:cart-button />
            </div>
        </div>

    </div>

    <div class=" lg:hidden bg-secondary">
        <div class="mx-auto w-full max-w-7xl h-13 px-6 lg:px-8 flex items-center z-30">
            <livewire:category.mobile-menu />
            <div class="flex-1 min-w-0">
                <form action="{{ route('tienda') }}" method="GET" class="w-full">
                    <flux:input name="search" value="{{ request('search') }}" class="w-full" icon="magnifying-glass"
                        placeholder="Buscar productos..." />
                </form>
            </div>
        </div>
    </div>

    <div class=" z-10 flex-1">
        {{ $slot }}
    </div>

    <!-- Global Footer -->
    <footer class="bg-gray-900 border-t border-gray-800 pt-16 pb-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 lg:gap-8 mb-12">
                <div class="md:col-span-1">


                    <a href="{{ route('home') }}" class="hidden sm:block shrink-0 mb-4">
                        <div class=" pr-2 py-1 flex">
                            <img src="{{ asset('storage/images/logos/logo.png') }}" alt="" class=" h-12">

                            <div class=" relative flex items-center ml-1 mr-3">
                                <p class=" text-3xl font-extrabold text-white -mt-4 italic">ipermerca</p>
                                <p
                                    class=" absolute top-7 left-4 text-sm  font-bold text-secondary-500 text-shadow-sm text-shadow-green-900">
                                    Donde sí se gana</p>
                            </div>
                        </div>
                    </a>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">
                        Arma tu mercado a tu gusto y recíbelo directamente en la puerta de tu casa. Envíos a todo el
                        Valle del Cauca.
                    </p>
                    <div class="flex items-center gap-4">
                        <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                            <svg class="size-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                            <svg class="size-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-white font-bold mb-6 tracking-wide uppercase text-sm">Enlaces Rápidos</h3>
                    <ul class="space-y-4">
                        <li><a href="{{ route('home') }}" wire:navigate
                                class="text-gray-400 hover:text-white transition-colors text-sm">Inicio</a></li>
                        <li><a href="{{ route('tienda') }}" wire:navigate
                                class="text-gray-400 hover:text-white transition-colors text-sm">Comprar Novedades</a>
                        </li>
                        <li><a href="/login" wire:navigate
                                class="text-gray-400 hover:text-white transition-colors text-sm">Mi Cuenta</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-bold mb-6 tracking-wide uppercase text-sm">Información</h3>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Términos
                                y Condiciones</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Política
                                de Privacidad</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Políticas
                                de Envío</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-bold mb-6 tracking-wide uppercase text-sm">Contáctanos</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <flux:icon.map-pin class="size-5 text-primary shrink-0" />
                            <span class="text-gray-400 text-sm">Valle del Cauca, Colombia</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <flux:icon.envelope class="size-5 text-primary shrink-0" />
                            <a href="mailto:info@mercado.com"
                                class="text-gray-400 hover:text-white transition-colors text-sm">info@ipermerca.com</a>
                        </li>
                        <li class="flex items-center gap-3">
                            <flux:icon.device-phone-mobile class="size-5 text-primary shrink-0" />
                            <a href="https://wa.me/573206296235" target="_blank"
                                class="text-gray-400 hover:text-white transition-colors text-sm"> 320 6296235</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-sm">
                    &copy; {{ date('Y') }} Ipermerca. Todos los derechos reservados.
                </p>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    Aliado oficial de <span class="text-secondary font-black">Fornuvi</span>
                </div>
            </div>
        </div>
    </footer>

    <livewire:cart-notification />
    @fluxScripts
</body>

</html>