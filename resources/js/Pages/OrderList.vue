<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
const props = defineProps({
    orders: {},
});
</script>

<template>
    <AppLayout title="Mis Ordenes">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Mis Ordenes
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div
                        class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">

                        <h1 class="text-2xl font-medium text-gray-900 dark:text-white">
                            Ejemplo de productos para la tienda
                        </h1>

                        <p class="mt-6 text-gray-500 dark:text-gray-400 leading-relaxed">
                            Este es un ejemplo de productos para la tienda, su intención es mostrar la funcionalidad de las
                            pasarelas de pago. Puedes agregar productos a tu carrito y realizar el pago con la pasarela que
                            selecciones al momento del checkout.

                            <br><br>
                            Recuerda que este es un ejemplo y por lo tanto los estilos o estructura convencional de una
                            tienda no se aplican, intentamos mantenerlo lo más simple posible para que puedas enfocarte en
                            las pasarelas.
                        </p>
                    </div>
                    <!-- List of orders (maybe table) -->
                    <div class="bg-gray-200 dark:bg-gray-800 bg-opacity-25">
                        <div class="flex flex-col p-6 lg:p-8 overflow-x-auto">
                            <span class="text-gray-500 dark:text-gray-400 text-sm">
                                Mostrando {{ orders.from }} - {{ orders.to }} de {{ orders.total }} ordenes
                            </span>

                            <table class="w-full mt-4 table-auto border-collapse border border-gray-200 dark:border-gray-700">
                                <thead
                                    class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400">
                                    <tr class="text-left">
                                        <th class="px-4 py-2">Fecha</th>
                                        <th class="px-4 py-2">Metodo de pago</th>
                                        <th class="px-4 py-2 text-center">Productos</th>
                                        <th class="px-4 py-2">Estado</th>
                                        <th class="px-4 py-2 text-center">Total</th>
                                        <th class="px-4 py-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                    <tr v-for="order in orders.data" :key="order.id"
                                        class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-2">{{ order.created_at }}</td>
                                        <td class="px-4 py-2">{{ order.payment_method_formatted }}</td>
                                        <td class="px-4 py-2 text-center">
                                            {{ order.products_count }}
                                        </td>
                                        <td class="px-4 py-2">{{ order.status }}</td>
                                        <td class="px-4 py-2 text-right">${{ order.total }}</td>
                                        <td class="px-4 py-2">
                                            <Link :href="route('orders.show', order.public_id)"
                                                class="text-indigo-600 hover:text-indigo-900">Ver</Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="p-8 flex justify-center">
                        <nav aria-label="Product navigation" v-if="orders.links.length > 3">
                            <ul class="inline-flex items-center -space-x-px">
                                <li v-for="(link, key) in orders.links">
                                    <button v-if="link.url === null" :key="key" disabled :class="{
                                        'block rounded-l-lg': key === 0,
                                        'block rounded-r-lg': key === orders.links.length - 1,
                                    }"
                                        class="px-3 py-2 leading-tight border dark:border-gray-700 text-gray-500 bg-white border-gray-300 dark:bg-gray-600 dark:text-gray-500 cursor-not-allowed"
                                        v-html="link.label" />
                                    <Link v-else :href="link.url" v-html="link.label" :class="{
                                        'block rounded-l-lg': key === 0,
                                        'block rounded-r-lg': key === orders.links.length - 1,
                                        'z-10 text-blue-600 border-blue-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:bg-gray-700 dark:text-white': link.active,
                                        'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': !link.active
                                    }" class="px-3 py-2 leading-tight border dark:border-gray-700" />
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
