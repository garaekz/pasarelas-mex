<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed, onMounted, toRef, watch } from 'vue';

const props = defineProps({
    cart: {},
    shipping: 0,
    subtotal: 0,
    tax: 0,
    total: 0,
});

const removeFromCart = (id) => {
    router.delete(route('cart.remove', id));
};

</script>

<template>
    <AppLayout title="Cart">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Carrito de compras
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.cartCount" class="flex flex-col sm:flex-row gap-4 items-start">
                    <div class="w-full sm:w-2/3 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8">
                        <div class="flex flex-col">
                            <div class="flex flex-col">
                                <div class="flex flex-row justify-between mb-4">
                                    <div class="text-gray-500 dark:text-gray-400 font-medium text-lg">Producto</div>
                                    <span class="text-gray-500 dark:text-gray-400 font-medium text-lg">Cantidad</span>
                                    <span class="text-gray-500 dark:text-gray-400 font-medium text-lg">Precio</span>
                                </div>
                                <div v-for="item in cart" :key="item.id" class="flex flex-row justify-between items-center">
                                    <div class="flex py-2">
                                        <div class="w-[110px]">
                                            <img :src="item.image" class="h-full w-auto">
                                        </div>
                                        <div class="flex flex-col ml-4">
                                            <span class="text-gray-800 dark:text-white">{{ item.name }}</span>
                                            <span class="text-gray-500 dark:text-gray-400">$ {{ item.price }}</span>
                                        </div>
                                    </div>
                                    <span class="text-gray-800 dark:text-white">{{ item.quantity }}</span>
                                    <span class="text-gray-800 dark:text-white">${{ item.price * item.quantity }}</span>
                                    <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg"
                                        @click="removeFromCart(item.id)">
                                        Eliminar
                                    </button>
                                </div>
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
                                <span class="text-gray-800 dark:text-white">$ {{ subtotal.toFixed(2) }} </span>
                            </div>
                            <div class="flex flex-row justify-between">
                                <span class="text-gray-500 dark:text-gray-400">IVA 16%</span>
                                <span class="text-gray-800 dark:text-white">$ {{ tax.toFixed(2) }}</span>
                            </div>
                            <div class="flex flex-row justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Envío (cobramos una taza fija)</span>
                                <span class="text-gray-800 dark:text-white">$ {{ shipping.toFixed(2) }}</span>
                            </div>
                            <hr class="my-4 border-gray-300 dark:border-gray-700">
                            <div class="flex flex-row justify-between">
                                <span class="text-gray-800 dark:text-white font-semibold text-xl">Total</span>
                                <span class="text-gray-800 dark:text-white font-semibold text-xl">$ {{ total.toFixed(2)
                                }}</span>
                            </div>
                            <div class="flex mt-4">
                                <Link href="/checkout"
                                    class="bg-blue-500 hover:bg-blue-700 flex justify-center text-white font-bold py-2 px-4 rounded w-full">
                                Proceder al pago
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="flex justify-center text-center w-full bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-16">
                    <h1 class="text-2xl md:text-4xl font-semibold text-gray-800 dark:text-white">
                        No hay productos en el carrito
                    </h1>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
