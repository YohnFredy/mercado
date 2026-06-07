<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remisión - Pedido {{ $order->order_number }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css'])

    <style>
        /* Base page sizes and print configuration */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
            }
        }

        @page {
            margin: 8mm;
        }

        @media print {
            @page {
                @if($format === 'ticket')
                    size: 80mm auto;
                    margin: 0;
                @else
                    size: letter;
                    margin: 8mm;
                @endif
            }
        }

        /* Prevent table row breaking across pages */
        tr {
            page-break-inside: avoid;
        }
        .keep-together {
            page-break-inside: avoid;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased font-sans min-h-screen flex flex-col justify-between">

    <!-- Action Bar for Screen view -->
    <div class="no-print bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-900 flex items-center gap-1">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Volver a Pedidos
            </a>
            <span class="text-gray-300">|</span>
            <span class="text-sm font-bold text-gray-700">Vista Previa - Formato {{ $format === 'ticket' ? 'Ticket POS' : 'Carta (Letter)' }}</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="?format={{ $format === 'ticket' ? 'letter' : 'ticket' }}" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Cambiar a Formato {{ $format === 'ticket' ? 'Carta' : 'Ticket' }}
            </a>
            <button onclick="window.print()" class="px-4 py-2 text-xs font-bold text-white bg-[#00A63D] rounded-lg hover:bg-[#008f33] transition flex items-center gap-2">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.617 0-1.11-.481-1.12-1.099L5.82 18m11.84 0a41.036 41.036 0 0 0-11.84 0m14.456-4.17A9.19 9.19 0 0 0 18 9V6a3 3 0 0 0-3-3H9a3 3 0 0 0-3 3v3c0 1.293.27 2.522.756 3.633M18 9h2.25A2.25 2.25 0 0 1 22 11.25v3a2.25 2.25 0 0 1-2.25 2.25H18M6 9H3.75A2.25 2.25 0 0 0 1.5 11.25v3a2.25 2.25 0 0 0 2.25 2.25H6" />
                </svg>
                Imprimir Remisión
            </button>
        </div>
    </div>

    <!-- Main Printable Content Area -->
    <div class="flex-1 py-6 px-4 flex justify-center items-start">

        @if($format === 'ticket')
            <!-- ================= TICKET FORMAT ================= -->
            <div class="w-[80mm] max-w-full bg-white p-4 shadow-sm border border-gray-200 print:border-none print:shadow-none print:p-0 text-black text-[11px] leading-tight">
                <!-- Header -->
                <div class="text-center space-y-1 mb-3">
                    <img src="{{ asset('storage/images/logos/logo.png') }}" alt="Ipermerca" class="h-10 mx-auto object-contain">
                    <h1 class="text-base font-extrabold tracking-wider italic text-[#00A63D]">ipermerca</h1>
                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Soporte de Entrega</p>
                    <p class="text-[9px] text-gray-500">Cel: 320 6296235 | Valle del Cauca</p>
                    <p class="text-[9px] text-gray-500">Documento Soporte (No Fiscal)</p>
                </div>

                <!-- Divider -->
                <div class="border-t border-dashed border-gray-400 my-2"></div>

                <!-- Info Info -->
                <div class="space-y-1 text-[10px]">
                    <div class="flex justify-between">
                        <span class="font-bold">N° Pedido:</span>
                        <span class="font-mono font-bold text-right">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Fecha:</span>
                        <span class="text-right">{{ $order->created_at->format('d/m/Y h:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Cliente:</span>
                        <span class="font-bold text-right truncate max-w-[120px]">{{ $order->customer_name }}</span>
                    </div>
                    @if($order->customer_phone)
                    <div class="flex justify-between">
                        <span>Teléfono:</span>
                        <span class="text-right">{{ $order->customer_phone }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-start">
                        <span>Dirección:</span>
                        <span class="text-right break-words max-w-[140px]">{{ $order->shipping_address }}, {{ $order->shipping_city }}</span>
                    </div>
                </div>

                <!-- Divider -->
                <div class="border-t border-dashed border-gray-400 my-2"></div>

                <!-- Table Header -->
                <div class="grid grid-cols-12 font-bold mb-1 text-[9px] uppercase tracking-wider text-gray-600">
                    <span class="col-span-2 text-center">Cant</span>
                    <span class="col-span-7">Producto</span>
                    <span class="col-span-3 text-right">Subtotal</span>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-200 mb-1.5"></div>

                <!-- Product Items -->
                <div class="space-y-1.5">
                    @foreach($order->items as $item)
                        <div class="grid grid-cols-12 items-start text-[10px]">
                            <span class="col-span-2 text-center font-bold">{{ $item->quantity }}</span>
                            <div class="col-span-7 pr-1">
                                <span class="font-medium text-gray-900">{{ $item->product_title }}</span>
                                <span class="block text-[8px] text-gray-500">${{ number_format($item->unit_price, 0, ',', '.') }} c/u</span>
                            </div>
                            <span class="col-span-3 text-right font-semibold">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Divider -->
                <div class="border-t border-dashed border-gray-400 my-2"></div>

                <!-- Totals -->
                <div class="space-y-1 text-[10px] pl-8">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>${{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Costo Envío:</span>
                        <span>{{ $order->shipping_cost > 0 ? '$' . number_format($order->shipping_cost, 0, ',', '.') : 'Gratis' }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-[#00A63D] text-[12px] border-t border-gray-200 pt-1 mt-1">
                        <span>TOTAL:</span>
                        <span>${{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Divider -->
                <div class="border-t border-dashed border-gray-400 my-2"></div>

                <!-- Notes / Instructions -->
                @if($order->shipping_notes)
                    <div class="bg-gray-50 p-1.5 rounded-sm mb-2 text-[9px] text-gray-700">
                        <span class="font-bold block">Notas del envío:</span>
                        {{ $order->shipping_notes }}
                    </div>
                @endif

                <!-- Footer disclaimer -->
                <div class="text-center text-[8px] text-gray-500 mt-2 space-y-1">
                    <p class="font-bold">¡Gracias por tu compra en Ipermerca!</p>
                    <p class="italic">"Donde sí se gana"</p>
                    <p class="border-t border-gray-100 pt-1 mt-1">Este documento sirve como constancia de recibo y detalle de entrega. No posee validez de factura legal.</p>
                </div>
            </div>
        @else
            <!-- ================= LETTER FORMAT (CARTA) ================= -->
            <div class="w-[8.5in] min-h-[11in] bg-white p-8 shadow-sm border border-gray-200 print:border-none print:shadow-none print:p-0 text-black text-xs leading-normal flex flex-col justify-between">
                <div>
                    <!-- Header Section -->
                    <div class="flex justify-between items-start gap-4 mb-6">
                        <!-- Logo & Company Details -->
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('storage/images/logos/logo.png') }}" alt="Ipermerca Logo" class="h-12 object-contain">
                                <div>
                                    <h1 class="text-2xl font-black italic tracking-tight text-[#00A63D]">ipermerca</h1>
                                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest -mt-1.5">Donde sí se gana</p>
                                </div>
                            </div>
                            <div class="text-gray-500 text-[10px] space-y-0.5">
                                <p class="font-semibold text-gray-800 text-xs">REMISIÓN DE MERCANCÍAS</p>
                                <p>Valle del Cauca, Colombia</p>
                                <p>WhatsApp: 320 6296235 | Email: info@ipermerca.com</p>
                            </div>
                        </div>

                        <!-- Order Metadata Box -->
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 text-right min-w-[200px] space-y-1">
                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Número de Pedido</div>
                            <div class="text-lg font-black font-mono text-[#00A63D]">{{ $order->order_number }}</div>
                            <div class="text-[10px] text-gray-500 mt-1">
                                <span class="font-bold">Fecha:</span> {{ $order->created_at->format('d M Y, h:i A') }}
                            </div>
                            <div class="text-[10px] text-gray-500">
                                <span class="font-bold">Estado:</span>
                                <span class="uppercase font-semibold">
                                    {{ $order->status === 'paid' ? 'PAGADO' : 'PENDIENTE' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Customer & Shipping Grid -->
                    <div class="grid grid-cols-2 gap-6 border border-gray-200 rounded-lg p-4 bg-gray-50/50 mb-6">
                        <!-- Customer Info -->
                        <div class="space-y-1.5">
                            <h4 class="text-[#00A63D] font-extrabold uppercase text-[10px] tracking-wider border-b border-gray-200 pb-1">Datos del Cliente</h4>
                            <div class="space-y-0.5 text-gray-700">
                                <p><span class="font-bold text-gray-900">Nombre:</span> {{ $order->customer_name }}</p>
                                @if($order->customer_phone)
                                    <p><span class="font-bold text-gray-900">Teléfono:</span> {{ $order->customer_phone }}</p>
                                @endif
                                @if($order->customer_email)
                                    <p><span class="font-bold text-gray-900">Email:</span> {{ $order->customer_email }}</p>
                                @endif
                               <!--  @if($order->customer_document)
                                    <p><span class="font-bold text-gray-900">Documento:</span> {{ $order->customer_document }}</p>
                                @endif
                                @if($order->pts)
                                    <p><span class="font-bold text-gray-900">Puntos Ganados:</span> {{ $order->pts }} pts</p>
                                @endif -->
                            </div>
                        </div>

                        <!-- Shipping Info -->
                        <div class="space-y-1.5">
                            <h4 class="text-[#00A63D] font-extrabold uppercase text-[10px] tracking-wider border-b border-gray-200 pb-1">Detalles de Entrega</h4>
                            <div class="space-y-0.5 text-gray-700">
                                <p><span class="font-bold text-gray-900">Dirección:</span> {{ $order->shipping_address }}</p>
                                <p><span class="font-bold text-gray-900">Ciudad:</span> {{ $order->shipping_city }}, {{ $order->shipping_department }}</p>
                                @if($order->shipping_notes)
                                    <p><span class="font-bold text-gray-900">Instrucciones:</span> {{ $order->shipping_notes }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden mb-6">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100 text-[10px] font-extrabold uppercase tracking-wider text-gray-700 border-b border-gray-200">
                                    <th class="py-2.5 px-3 text-center w-12">#</th>
                                    <th class="py-2.5 px-3">Producto / Descripción</th>
                                    <th class="py-2.5 px-3 text-center w-20">Cant.</th>
                                    <th class="py-2.5 px-3 text-right w-28">Precio Unit.</th>
                                    <th class="py-2.5 px-3 text-right w-32">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($order->items as $index => $item)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="py-0 px-3 text-center font-semibold text-gray-400 text-[10px]">{{ $index + 1 }}</td>
                                        <td class="py-0  px-3">
                                            <div class="font-bold text-gray-900">{{ $item->product_title }}</div>
                                        </td>
                                        <td class="py-0 px-3 text-center font-bold text-gray-800">{{ $item->quantity }}</td>
                                        <td class="py-0 px-3 text-right text-gray-600">${{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                        <td class="py-0 px-3 text-right font-extrabold text-gray-900">${{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Area -->
                    <div class="grid grid-cols-12 gap-4 mt-6 keep-together">
                        <!-- Shipping Notes & Terms (Left) -->
                        <div class="col-span-7 space-y-3">
                            @if($order->shipping_notes)
                                <div class="bg-yellow-50/50 border border-yellow-200 rounded-lg p-3">
                                    <span class="font-bold text-yellow-800 text-[10px] uppercase block mb-1">Notas especiales de envío:</span>
                                    <p class="text-yellow-900 text-[11px]">{{ $order->shipping_notes }}</p>
                                </div>
                            @endif
                            <div class="border border-gray-100 rounded-lg p-3 text-[10px] text-gray-400 space-y-1">
                                <span class="font-bold text-gray-500 uppercase block mb-1">Políticas de Devolución y Entrega:</span>
                                <!-- <p>• El cliente debe revisar todos los productos al momento de la entrega en compañía del transportador.</p> -->
                                <p>• <!-- La firma de este documento constituye constancia de recibido de los productos aquí relacionados.  -->Cualquier novedad, faltante o inconsistencia deberá reportarse al momento de la entrega o dentro de las 24 horas siguientes.</p>
                            </div>
                        </div>

                        <!-- Totals Box (Right) -->
                        <div class="col-span-5 border border-gray-200 rounded-lg p-4 bg-gray-50/50 space-y-2 h-fit">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-semibold">${{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Costo de Envío</span>
                                <span class="font-semibold">
                                    {{ $order->shipping_cost > 0 ? '$' . number_format($order->shipping_cost, 0, ',', '.') : 'Gratis' }}
                                </span>
                            </div>
                            <div class="border-t border-gray-200 pt-2 flex justify-between font-black text-[#00A63D] text-sm">
                                <span>Valor de los productos:</span>
                                <span class="text-base">${{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer disclaimer -->
                <div class="border-t border-gray-200 pt-4 mt-8 text-center text-[10px] text-gray-400 keep-together">
                    <p class="font-bold text-[#00A63D] text-xs">¡Gracias por depositar tu confianza en Ipermerca!</p>
                    <p class="italic text-gray-500">"El mercado del hogar directo a tu puerta, donde sí se gana"</p>
                    <p class="mt-2 text-[9px] text-gray-400 leading-relaxed">
                        Documento de remisión de mercancías. No constituye factura de venta ni documento equivalente.
                    </p>
                </div>
            </div>
        @endif

    </div>

    <!-- Screen-only print trigger script -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 600);
        };
    </script>
</body>
</html>
