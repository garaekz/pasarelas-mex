<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

const props = defineProps({
    order: {},
});

const splitByHyphens = (string) => {
    let formatted = '';
    for (let i = 0; i < string.length; i++) {
        if (i > 0 && i % 4 === 0) {
            formatted += '-';
        }
        formatted += string.charAt(i);
    }
    return formatted;
};
</script>

<template>
    <AppLayout title="Checkout">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Orden #{{ order.public_id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex sm:flex-row flex-col gap-4 items-start mt-4">
                    <div class="w-full sm:w-2/3 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8">
                        <div class="flex flex-col">
                            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">
                                Detalles de la orden
                            </h1>
                            <p class="text-gray-500 dark:text-gray-400" v-if="order.status === 'pending'">
                                Tu orden ha sido recibida y está siendo procesada. Una vez que se haya completado el pago,
                                se
                                enviará un correo electrónico de confirmación.
                            </p>
                            <p class="text-gray-500 dark:text-gray-400" v-if="order.status === 'paid'">
                                Tu orden ha sido pagada y está siendo procesada. Una vez que se haya enviado el producto, se
                                enviará un correo electrónico de confirmación.
                            </p>
                            <div class="flex gap-2 mt-4">
                                <span class="text-gray-500 dark:text-gray-400">Fecha de creación:</span>
                                <span class="text-gray-800 dark:text-white">{{ order.created_at }}</span>
                            </div>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-gray-500 dark:text-gray-400">Estado:</span>
                                <span :class="{
                                    'bg-green-100 dark:bg-green-700 text-green-800 dark:text-green-100': order.status === 'paid',
                                    'bg-red-100 dark:bg-red-700 text-red-800 dark:text-red-100': order.status === 'pending',
                                    'bg-blue-100 dark:bg-blue-700 text-blue-800 dark:text-blue-100': order.status === 'shipped',
                                    'bg-yellow-100 dark:bg-yellow-700 text-yellow-800 dark:text-yellow-100': order.status === 'delivered',
                                    'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100': order.status === 'canceled',
                                }" class="px-4 py-1 rounded-full font-semibold">
                                    {{ order.status_formatted }}
                                </span>
                            </div>
                            <div class="flex gap-2 mt-4" v-if="order.pdf_url">
                                <span class="text-gray-500 dark:text-gray-400">Hoja de pago:</span>
                                <a :href="order.pdf_url" target="_blank"
                                    class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-500">Descargar</a>
                            </div>
                            <div class="w-full overflow-x-auto">
                                <table class="mt-4 w-full">
                                    <thead>
                                        <tr>
                                            <th class="text-left text-gray-500 dark:text-gray-400">Producto</th>
                                            <th class="text-center text-gray-500 dark:text-gray-400">Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="product in order.products" :key="product.id">
                                            <td class="text-gray-800 dark:text-white py-2">
                                                <div class="flex">
                                                    <div class="w-[110px]">
                                                        <img :src="product.image" class="h-full w-auto">
                                                    </div>
                                                    <div class="flex flex-col ml-4">
                                                        <span class="text-gray-800 dark:text-white">{{ product.name
                                                        }}</span>
                                                        <span class="text-gray-500 dark:text-gray-400">$ {{
                                                            product.pivot.price }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-gray-800 dark:text-white text-center">
                                                {{ product.pivot.quantity }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                            <div class="flex flex-col gap-2 mt-4" v-if="order.payment_method === 'oxxo'">
                                <h4 class="text-gray-800 dark:text-white font-semibold text-xl">
                                    Instrucciones de pago
                                </h4>
                                <p class="text-gray-500 dark:text-gray-400">
                                    Paga en cualquier tienda OXXO, y usa el siguiente código para realizar el pago:
                                </p>
                                <p class=" flex justify-center text-gray-800 dark:text-white font-semibold text-2xl">
                                    {{ splitByHyphens(order.reference) }}
                                </p>
                                <ol class="list-decimal list-inside text-gray-500 dark:text-gray-400">
                                    <li>Acude a la tienda OXXO más cercana.</li>
                                    <li>Indica en caja que quieres realizar un pago de servicio.</li>
                                    <li>Dicta al cajero el número de referencia en esta ficha para que tecleé directamete en
                                        la
                                        pantalla de venta.</li>
                                    <li>Realiza el pago correspondiente con dinero en efectivo.</li>
                                    <li>Al confirmar tu pago, el cajero te entregará un comprobante impreso. <strong>En el
                                            podrás
                                            verificar que se haya realizado correctamente.</strong> Conserva este
                                        comprobante de pago.</li>
                                </ol>
                                <p class="text-gray-500 dark:text-gray-400">
                                    Al completar el proceso, recibirás un correo electrónico con la confirmación de tu pago.
                                    Podrás regresar a esta pantalla y notarás que el estatus de tu orden ha cambiado a
                                    <strong>Pagado</strong>.
                                </p>

                                <p class="bg-red-400 border border-red-600 text-red-800 rounded p-4 mt-8">
                                    <strong>Importante:</strong> Recuerda que todo este sitio es de prueba, por lo que no se
                                    realizará ningún cobro, no es necesario que realices ningún pago y no se enviará ningún
                                    producto.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="w-full sm:w-1/3 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-4">
                        <div class="flex flex-col">
                            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">
                                Resumen
                            </h1>
                            <div class="flex flex-row justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                                <span class="text-gray-800 dark:text-white">$ {{ order.subtotal }} </span>
                            </div>
                            <div class="flex flex-row justify-between">
                                <span class="text-gray-500 dark:text-gray-400">IVA 16%</span>
                                <span class="text-gray-800 dark:text-white">$ {{ order.tax }}</span>
                            </div>
                            <div class="flex flex-row justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Envío (cobramos una taza fija)</span>
                                <span class="text-gray-800 dark:text-white">$ {{ order.shipping }}</span>
                            </div>
                            <hr class="my-4 border-gray-300 dark:border-gray-700">
                            <div class="flex flex-row justify-between">
                                <span class="text-gray-800 dark:text-white font-semibold text-xl">Total</span>
                                <span class="text-gray-800 dark:text-white font-semibold text-xl">$ {{ order.total }}</span>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</AppLayout></template>
