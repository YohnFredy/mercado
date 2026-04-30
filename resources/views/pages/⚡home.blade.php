<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

{{-- ============================================================
     HOME PAGE
     ============================================================ --}}
<div class="">

    {{-- ============================================================
         HERO SECTION
         Layout de dos columnas: texto | imagen
         En mobile (< sm): solo columna de texto, imagen oculta
         ============================================================ --}}
    <section class="relative sm:bg-white overflow-hidden">

        {{-- Orbs decorativos de fondo --}}
        <div class=" hidden sm:block absolute inset-0 pointer-events-none z-0 overflow-hidden">
            <div class="absolute -top-1/4 -left-1/4 w-72 h-72 sm:w-[28rem] sm:h-[28rem] bg-primary/15 rounded-full blur-3xl animate-blob"></div>
            <div class="absolute top-1/4 -right-1/4 w-72 h-72 sm:w-[28rem] sm:h-[28rem] bg-secondary/15 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-1/4 left-1/4 w-72 h-72 sm:w-[28rem] sm:h-[28rem] bg-acento/10 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
        </div>

        <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 min-h-[85vh] md:min-h-[80vh]">

            {{-- Columna izquierda: Texto y CTA --}}
            <div class="flex items-center sm:px-10 lg:px-16 pb-6 sm:py-14">
                <div class="w-full max-w-xl">

                    {{-- Badge animado --}}
                    <div class="inline-flex items-center gap-3 bg-white shadow-md shadow-gray-300 rounded-full py-2 px-5 mb-6 sm:mb-8">
                        <span class="relative flex size-2.5">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-primary opacity-75"></span>
                            <span class="relative inline-flex size-2.5 rounded-full bg-primary"></span>
                        </span>
                        <span class="text-xs sm:text-sm text-gray-700 font-bold tracking-widest uppercase">Revolucionamos tus compras</span>
                    </div>

                    {{-- Titular principal --}}
                    <div class="mb-4 sm:mb-6">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl text-gray-900 font-black tracking-tighter leading-[1.05]">
                            Tu mercado
                        </h1>
                        <div class="relative inline-block mt-1">
                            <span class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl text-primary font-black tracking-tighter leading-[1.05]">
                                fácil, rápido
                            </span>
                            <svg class="absolute w-full h-2 sm:h-3 -bottom-1 left-0 text-secondary" viewBox="0 0 100 10" preserveAspectRatio="none">
                                <path d="M0 5 Q 50 15 100 5" stroke="currentColor" stroke-width="4" fill="transparent" />
                            </svg>
                        </div>
                        <p class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl text-gray-400 font-light italic tracking-tighter leading-[1.05] mt-1">
                            y sin filas.
                        </p>
                    </div>

                    {{-- Descripción --}}
                    <p class="text-sm sm:text-base md:text-lg text-gray-600 font-medium leading-relaxed border-l-4 border-primary pl-4 mb-8 sm:mb-10 max-w-md">
                        Arma tu lista a tu gusto. Seleccionamos lo más fresco y lo entregamos directo en tu puerta. Envío gratis según tu compra. 🚚
                    </p>

                    {{-- CTA Group --}}
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6">
                        <a href="{{ route('tienda') }}" wire:navigate
                            class="cta-btn relative inline-flex items-center justify-center w-full sm:w-auto gap-2 sm:gap-3 px-7 sm:px-10 py-4 bg-primary text-white font-black text-sm sm:text-base rounded-2xl shadow-xl shadow-primary/30 overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                            <span class="absolute inset-0 bg-secondary -z-10"></span>
                            <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-gray-900 rounded-full group-hover:w-80 group-hover:h-80 -z-[5]"></span>
                            <flux:icon.shopping-bag class="size-5 sm:size-6 relative z-10 group-hover:scale-110 transition-transform shrink-0" />
                            <span class="relative z-10">Comprar Ahora</span>
                        </a>

                        <div class="flex items-center gap-2 text-xs font-bold text-gray-500 uppercase tracking-widest">
                            <flux:icon.star class="size-4 text-secondary shrink-0" variant="solid" />
                            Calidad 100%
                        </div>
                    </div>

                </div>
            </div>

            {{-- Columna derecha: Imagen hero --}}
            {{-- Mobile: franja full-width panorámica debajo del texto --}}
            {{-- md+: columna lateral con imagen y floating badges --}}
            <div class="md:flex md:items-center md:justify-center md:px-6 lg:px-10 md:py-10 lg:py-16">

                {{-- Mobile: imagen full-width (sin padding, panorámica) --}}
                <div class="relative w-full overflow-hidden md:hidden" style="height: 52vw; min-height: 280px; max-height: 280px;">
                    <img
                        src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1000&q=80"
                        alt="Verduras frescas premium"
                        class="w-full h-full object-cover object-center"
                        loading="eager">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900/70 via-transparent to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <div class="bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl p-2.5 text-white">
                            <p class="font-black text-xs">Calidad de exportación</p>
                            <p class="text-[0.65rem] font-medium opacity-85">Seleccionado a mano para ti.</p>
                        </div>
                    </div>



                    {{-- Floating badge: Envíos --}}
                    <div class="absolute -left-4 top-5 bg-white px-3 py-2  rounded-3xl shadow-lg shadow-gray-400/30 flex items-center gap-3 animate-float">
                        <div class="size-11 rounded-full bg-green-50 flex items-center justify-center text-primary shrink-0">
                            <flux:icon.truck variant="solid" class="size-5" />
                        </div>
                        <div>
                            <p class="font-black text-gray-900 text-sm">Envíos al</p>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Valle del Cauca</p>
                        </div>
                    </div>
                </div>

                {{-- Desktop (md+): imagen con bordes, rotación y floating badges --}}
                <div class="hidden md:block relative w-full max-w-lg">

                    <div class="relative rounded-[2rem] lg:rounded-[2.5rem] overflow-hidden border-8 border-white shadow-2xl shadow-primary/20 aspect-[4/5] transform lg:rotate-2 hover:rotate-0 transition-transform duration-700">
                        <img
                            src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1000&q=80"
                            alt="Verduras frescas premium"
                            class="w-full h-full object-cover"
                            loading="eager">
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/60 via-transparent to-transparent"></div>

                        {{-- Caption de imagen --}}
                        <div class="absolute bottom-5 left-5 right-5">
                            <div class="bg-white/25 backdrop-blur-md border border-white/40 rounded-2xl p-3 lg:p-4 text-white">
                                <p class="font-black text-sm lg:text-base">Calidad de exportación</p>
                                <p class="text-xs lg:text-sm font-medium opacity-90">Seleccionado a mano para ti.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Floating badge: Envíos --}}
                    <div class="absolute -left-6 top-16 bg-white p-3 lg:p-4 rounded-3xl shadow-lg shadow-gray-400/30 flex items-center gap-3 animate-float">
                        <div class="size-11 lg:size-12 rounded-full bg-green-50 flex items-center justify-center text-primary shrink-0">
                            <flux:icon.truck variant="solid" class="size-5 lg:size-6" />
                        </div>
                        <div>
                            <p class="font-black text-gray-900 text-sm">Envíos al</p>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Valle del Cauca</p>
                        </div>
                    </div>

                    {{-- Floating badge: Puntos --}}
                    <div class="absolute -right-4 bottom-24 bg-gray-900 p-3 lg:p-4 rounded-3xl shadow-lg shadow-gray-900/30 flex items-center gap-3 animate-float-delayed">
                        <div class="size-11 lg:size-12 rounded-full bg-white/10 flex items-center justify-center text-secondary shrink-0">
                            <flux:icon.star variant="solid" class="size-5 lg:size-6" />
                        </div>
                        <div class="pr-1">
                            <p class="font-black text-white text-sm">+ Puntos</p>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Alianza Fornuvi</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>


    {{-- ============================================================
         BENTO GRID — BENEFICIOS
         Tarjetas de ventajas y características del servicio
         ============================================================ --}}
    <section id="beneficios" class="relative py-6 sm:py-24 sm:bg-white overflow-hidden">

        {{-- Divisor SVG superior --}}
        <div class="hidden sm:block absolute top-0 left-0 w-full overflow-hidden leading-none rotate-180">
            <svg class="relative block w-full h-10 sm:h-20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="fill-primary/10"></path>
            </svg>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto  sm:px-6 lg:px-8">

            {{-- Encabezado de sección --}}
            <div class="text-center max-w-2xl mx-auto mb-6 sm:mb-16">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white sm:bg-primary/8 text-primary font-black text-[0.65rem] sm:text-xs uppercase tracking-widest shadow-sm mb-4 sm:mb-5">
                    <flux:icon.sparkles class="size-3 sm:size-3.5" />
                    Beneficios Exclusivos
                </div>
                <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black text-gray-900 tracking-tight mb-3 sm:mb-4">
                    Pensado <span class="text-primary italic">para ti</span>
                </h2>
                <p class="text-sm sm:text-base lg:text-lg text-gray-600 font-medium leading-relaxed">
                    Olvídate de hacer filas. Disfruta de la comodidad de tener el supermercado en tu casa con políticas de ahorro increíbles.
                </p>
            </div>

            {{-- Bento Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 lg:gap-6 auto-rows-auto lg:auto-rows-[260px]">

                {{-- Tarjeta grande: Pedidos Seguros --}}
                <div class="sm:col-span-2 relative rounded-3xl overflow-hidden group bg-gray-50 flex flex-col justify-end border border-gray-100 shadow-lg hover:shadow-xl transition-all duration-500 min-h-[220px] lg:min-h-0">
                    <img
                        src="https://images.unsplash.com/photo-1590779033100-9f60a05a013d?auto=format&fit=crop&w=1200&q=80"
                        alt="Mercado empacado en caja"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/30 to-transparent"></div>
                    <div class="relative z-10 p-6 sm:p-8 max-w-lg">
                        <div class="size-11 sm:size-14 rounded-2xl bg-white text-primary flex items-center justify-center mb-3 shadow-md group-hover:-translate-y-2 transition-transform duration-300">
                            <flux:icon.shield-check variant="solid" class="size-5 sm:size-7" />
                        </div>
                        <h3 class="text-xl sm:text-2xl font-black text-white mb-1">Pedidos Seguros e Inocuos</h3>
                        <p class="text-gray-200 font-medium text-sm sm:text-base">Empacamos al vacío y con la máxima higiene, cuidando de ti y tu familia en cada entrega.</p>
                    </div>
                </div>

                {{-- Tarjeta: Envío Gratis --}}
                <div class="relative rounded-3xl bg-gradient-to-br from-primary to-green-600 p-6 sm:p-8 flex flex-col justify-between text-white overflow-hidden shadow-lg shadow-primary/25 group min-h-[180px] lg:min-h-0">
                    <div class="absolute -right-8 -top-8 size-44 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="size-11 sm:size-13 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <flux:icon.truck class="size-5 sm:size-7 text-white" />
                    </div>
                    <div>
                        <h3 class="text-xl sm:text-2xl font-black mb-1.5">Envío Gratis</h3>
                        <p class="text-green-50 font-medium text-sm sm:text-base leading-snug">
                            Compras superiores a <span class="font-black bg-white/20 px-1.5 py-0.5 rounded">$150.000</span> tienen envío gratis.
                        </p>
                    </div>
                </div>

                {{-- Tarjeta: Ciudades Aliadas --}}
                <div class="relative rounded-3xl bg-white border border-gray-100 p-6 sm:p-8 flex flex-col justify-between shadow-lg shadow-gray-100 group hover:-translate-y-1 transition-transform duration-300 min-h-[180px] lg:min-h-0">
                    <div class="size-11 sm:size-13 rounded-2xl bg-secondary/10 flex items-center justify-center">
                        <flux:icon.map-pin class="size-5 sm:size-7 text-secondary" />
                    </div>
                    <div>
                        <h3 class="text-xl sm:text-2xl font-black text-gray-900 mb-1.5">Ciudades Aliadas</h3>
                        <p class="text-gray-600 font-medium text-sm sm:text-base leading-snug">
                            ¡Envío gratis desde <span class="font-black text-secondary">$60.000</span> en ciudades seleccionadas!
                        </p>
                    </div>
                </div>

                {{-- Tarjeta grande: Pagos Subsidiados --}}
                <div class="sm:col-span-2 relative rounded-3xl bg-gray-900 p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center gap-6 shadow-xl overflow-hidden group min-h-[180px] lg:min-h-0">
                    <div class="absolute inset-0 bg-center bg-cover opacity-50 mix-blend-luminosity group-hover:scale-105 transition-transform duration-1000"
                        style="background-image: url('https://images.unsplash.com/photo-1608686207856-001b95cf60ca?auto=format&fit=crop&w=1200&q=80')">
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-900/90 to-transparent"></div>

                    <div class="relative z-10 flex-1">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-800 border border-gray-600 text-gray-100 text-xs font-black uppercase tracking-widest mb-3">
                            <flux:icon.currency-dollar class="size-3.5" />
                            Costos Justos
                        </div>
                        <h3 class="text-xl sm:text-3xl font-black text-white mb-2">Pagos Subsidiados</h3>
                        <p class="text-gray-300 font-medium text-sm sm:text-base max-w-md">
                            Si tu compra no alcanza el monto mínimo, nosotros subsidiamos una parte para que el domicilio sea más accesible.
                        </p>
                    </div>

                    {{-- Decorativo: spinner — visible desde sm --}}
                    <div class="hidden sm:flex relative z-10 shrink-0 size-28 md:size-36 rounded-full border border-dashed border-gray-400/50 items-center justify-center animate-[spin_20s_linear_infinite]">
                        <div class="size-20 md:size-28 bg-gray-900 rounded-full flex items-center justify-center border border-gray-600">
                            <flux:icon.shopping-cart class="size-8 sm:size-10 text-gray-300" />
                        </div>
                    </div>
                </div>

            </div>

            {{-- Banner de alerta: Días especiales --}}
            <div class="mt-5 sm:mt-6 bg-gradient-to-r from-acento to-secondary rounded-2xl sm:rounded-3xl p-px shadow-md hover:scale-[1.01] transition-transform duration-300">
                <div class="bg-white rounded-[calc(1rem+1px)] sm:rounded-[calc(1.25rem+1px)] p-4 sm:p-6 flex items-start gap-4 sm:gap-5">
                    <div class="shrink-0 size-11 sm:size-14 bg-acento/8 text-acento rounded-xl sm:rounded-2xl flex items-center justify-center">
                        <flux:icon.calendar class="size-5 sm:size-7 stroke-2" />
                    </div>
                    <div>
                        <h4 class="text-sm sm:text-lg font-black text-gray-900 mb-0.5">Días Especiales de Entrega</h4>
                        <p class="text-gray-600 font-medium text-xs sm:text-sm leading-relaxed">
                            Atención: Para ciertas rutas y ciudades específicas del Valle del Cauca, programamos días semanales especiales de entrega.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </section>


    {{-- ============================================================
         FORNUVI ECOSYSTEM — FIDELIZACIÓN
         Sección oscura con la propuesta de puntos y comisiones
         ============================================================ --}}
    <section class="relative sm:py-24 bg-gray-900 overflow-hidden">

        {{-- Orbs de fondo neo-glow --}}
        <div class="absolute -top-1/3 -left-1/4 w-72 h-72 sm:w-[32rem] sm:h-[32rem] bg-secondary rounded-full mix-blend-screen blur-[100px] opacity-15 animate-blob pointer-events-none"></div>
        <div class="absolute -bottom-1/3 -right-1/4 w-72 h-72 sm:w-[32rem] sm:h-[32rem] bg-primary rounded-full mix-blend-screen blur-[100px] opacity-15 animate-blob animation-delay-4000 pointer-events-none"></div>

        <div class="relative z-10 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/60 border border-gray-700/40 rounded-3xl sm:rounded-[2.5rem] p-6 sm:p-10 md:p-14 backdrop-blur-xl shadow-[0_40px_100px_rgba(0,0,0,0.5)] flex flex-col lg:flex-row items-center gap-10 lg:gap-14">

                {{-- Texto descriptivo --}}
                <div class="flex-1 w-full order-2 lg:order-1">

                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-secondary/10 border border-secondary/20 text-secondary text-xs font-black tracking-widest uppercase mb-5 shadow-[0_0_20px_rgba(166,135,0,0.15)]">
                        <flux:icon.users class="size-3.5" />
                        Fidelización Oficial
                    </div>

                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-4 sm:mb-5 leading-tight tracking-tight">
                        El ecosistema<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 via-secondary to-yellow-500 drop-shadow-[0_0_15px_rgba(255,215,0,0.4)] animate-pulse">
                            Fornuvi
                        </span>
                        &nbsp;te premia.
                    </h2>

                    <p class="text-gray-300 text-sm sm:text-base lg:text-lg leading-relaxed font-medium mb-7 sm:mb-9 max-w-xl">
                        Si te afilias, generas automáticamente <strong class="text-white">puntos y comisiones</strong> en cada compra. Queremos verte crecer junto a nosotros.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div class="bg-gray-800/50 p-4 sm:p-5 rounded-2xl border border-gray-700 hover:border-secondary/40 transition-colors">
                            <flux:icon.arrow-trending-up class="size-6 sm:size-7 text-primary mb-2.5" />
                            <h4 class="text-sm sm:text-base font-black text-white mb-1">Crecimiento Constante</h4>
                            <p class="text-gray-400 font-medium text-xs sm:text-sm">Tu mercado diario se convierte en un motor para seguir sumando dentro de tu cuenta.</p>
                        </div>
                        <div class="bg-gray-800/50 p-4 sm:p-5 rounded-2xl border border-gray-700 hover:border-secondary/40 transition-colors">
                            <flux:icon.gift class="size-6 sm:size-7 text-secondary mb-2.5" />
                            <h4 class="text-sm sm:text-base font-black text-white mb-1">Recompensas Reales</h4>
                            <p class="text-gray-400 font-medium text-xs sm:text-sm">Convierte tu consumo orgánico en gratificaciones exclusivas para toda la vida.</p>
                        </div>
                    </div>

                </div>

                {{-- Visual de anillos giratorios — reducido en mobile --}}
                <div class="w-full lg:w-5/12 order-1 lg:order-2 flex justify-center">
                    <div class="relative aspect-square w-48 sm:w-72 lg:w-full lg:max-w-xs">
                        <div class="absolute inset-4 rounded-full border overflow-hidden border-gray-600/50 animate-[spin_30s_linear_infinite]">
                            <img
                                src="https://images.unsplash.com/photo-1555529771-835f59fc5efe?auto=format&fit=crop&w=600&q=80"
                                alt="Alimentos frescos"
                                class="w-full h-full object-cover mix-blend-luminosity opacity-40"
                                loading="lazy">
                        </div>
                        <div class="absolute inset-8 rounded-full border-t border-l border-primary/60 animate-[spin_10s_linear_infinite_reverse]"></div>
                        <div class="absolute inset-12 rounded-full border-b border-r border-secondary/60 animate-[spin_15s_linear_infinite]"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="size-20 sm:size-28 bg-gray-900 rounded-full z-10 shadow-[0_0_50px_rgba(0,0,0,0.8)] flex items-center justify-center border-4 border-gray-700">
                                <div class="flex flex-col items-center cursor-pointer group">
                                    <flux:icon.star variant="solid" class="size-7 sm:size-9 text-secondary mb-0.5 group-hover:scale-125 transition-transform duration-300" />
                                    <span class="font-black text-white text-xs leading-none">PTS</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- ============================================================
         WHATSAPP CTA BANNER
         Llamado a la acción para contacto directo
         ============================================================ --}}
    <section class="relative py-16 sm:py-24 bg-white overflow-hidden">

        {{-- Icono decorativo de fondo --}}
        <div class="absolute right-0 bottom-0 pointer-events-none opacity-[0.07] sm:opacity-[0.12] translate-x-1/3 translate-y-1/3">
            <svg width="320" height="320" viewBox="0 0 24 24" fill="currentColor" class="text-green-500 sm:w-[440px] sm:h-[440px]">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.59 12.42L10 14.17l-2.59-2.58L6 13l4 4 8-8z" />
            </svg>
        </div>

        <div class="relative z-10 max-w-3xl mx-auto px-4 text-center flex flex-col items-center">

            {{-- Ícono de WhatsApp --}}
            <div class="relative mb-7 sm:mb-9">
                <div class="absolute inset-0 bg-green-400 blur-2xl rounded-full opacity-25 animate-pulse"></div>
                <div class="relative size-18 sm:size-24 bg-gradient-to-br from-[#128C7E] to-[#25D366] rounded-[1.5rem] sm:rounded-[2rem] rotate-3 flex items-center justify-center shadow-xl text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="-rotate-3 w-9 h-9 sm:w-12 sm:h-12">
                        <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" />
                    </svg>
                </div>
            </div>

            <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black text-gray-900 mb-3 sm:mb-5 tracking-tight">
                Estamos a un clic de distancia
            </h2>
            <p class="text-sm sm:text-base lg:text-lg text-gray-600 font-medium max-w-xl mb-8 sm:mb-10">
                Consulta productos especiales, revisa tus pedidos o pide soporte técnico directamente con un humano en WhatsApp.
            </p>

            <a href="https://wa.me/573168906749" target="_blank" rel="noopener noreferrer"
                class="group relative inline-flex items-center justify-center gap-3 w-full sm:w-auto px-8 sm:px-12 py-4 sm:py-5 bg-primary text-white font-black text-sm sm:text-base rounded-full overflow-hidden shadow-xl shadow-primary/25 hover:scale-105 transition-transform duration-300">
                <span class="absolute inset-0 bg-gray-800 -translate-x-[110%] skew-x-[30deg] group-hover:translate-x-0 transition-transform duration-500 z-0"></span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="relative z-10 w-5 h-5 sm:w-6 sm:h-6 shrink-0">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                </svg>
                <span class="relative z-10">Escríbenos al WhatsApp</span>
            </a>

        </div>
    </section>

</div>