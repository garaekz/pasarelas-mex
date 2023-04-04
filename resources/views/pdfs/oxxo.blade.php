<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    </head>
    <body class="font-sans antialiased">
        <div class="max-w-3xl flex justify-center mx-auto">
            <div class="flex flex-col justify-center w-full mx-auto border border-gray-200">
                <div class="bg-black text-white uppercase text-center w-full py-2">
                    Ficha digital. No es necesario imprimir.
                </div>
                <div class="bg-blue-900 flex justify-center py-4 text-white font-bold text-2xl">
                    Pago en efectivo
                </div>
                <div class="flex p-8">
                    <div class="w-1/2">
                        <img
                            src="https://upload.wikimedia.org/wikipedia/commons/6/66/Oxxo_Logo.svg"
                            class="w-32 mx-auto"
                            alt="logo oxxo">
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold uppercase text-gray-600">
                            Monto a pagar
                        </h2>
                        <p class="text-5xl font-bold text-gray-900 flex items-start">
                            ${{ $order->total }} <span class="text-sm ml-2">MXN</span>
                        </p>
                        <p class="text-gray-600 text-sm mt-3">
                            OXXO cobrar&aacute; una comisi&oacute;n adicional al momento de realizar el pago.
                        </p>
                    </div>
                </div>
                <div class="flex flex-col p-8 font-bold">
                    <h2 class="uppercase text-xl text-gray-600 mb-3">Referencia</h2>
                    <p class="text-3xl py-2 flex justify-center font-bold text-gray-900 border-2 border-gray-400">
                        {{ substr(chunk_split($order->reference, 4, "-"), 0, -1) }}
                    </p>
                </div>
            </div>
        </div>
        @vite(['resources/js/app.js'])
    </body>
</html>
