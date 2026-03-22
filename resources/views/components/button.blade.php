@props(['color' => 'primary'])

@php
    // Definimos las clases de diseño base para el botón
    $baseClasses =
        'text-center px-4 py-2 rounded-lg text-sm disabled:opacity-70 focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer';

    // Definimos las clases de color y efecto hover basadas en el color proporcionado
    $colorClasses = match ($color) {
        'primary' => 'bg-primary hover:bg-primary/80 text-white',
        'secondary' => 'bg-secondary hover:bg-secondary/80 text-white',
        'acento' => 'bg-acento hover:bg-acento/80 text-white',
        'fondo' => 'bg-fondo hover:bg-gray-200 text-gray-900 border border-gray-400',
    };
@endphp

<a {{ $attributes->merge(['type' => 'button', 'class' => "$baseClasses $colorClasses"]) }}>
    {{ $slot }}
</a>
