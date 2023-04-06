<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

defineProps({
    products: {},
    success: {
        type: String,
        default: null,
    },
});

const form = useForm({
    product_id: null,
    // quantity: 1, still not implemented
});

const handleAddToCart = (productId) => {
    form.product_id = productId;
    form.post(route('cart.add'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AppLayout title="Inicio">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Inicio
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

                    <div
                        class="bg-gray-200 dark:bg-gray-800 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 p-6 lg:p-8">
                        <div v-for="product in products.data" :key="product.id"
                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex flex-col h-full">
                                <div class="flex-shrink-0">
                                    <img :src="product.image" alt="" class="h-48 w-full object-cover rounded-t-lg">
                                </div>
                                <div class="flex-1 flex flex-col justify-between p-6">
                                    <div>
                                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                                            {{ product.name }}
                                        </h3>
                                        <p class="mt-1 text-gray-500 dark:text-gray-400">
                                            {{ product.description }}
                                        </p>
                                    </div>
                                    <div class="mt-3 flex justify-between items-center">
                                        <span class="text-gray-900 text-xl dark:text-white">
                                            ${{ product.price }}
                                        </span>
                                        <button @click="handleAddToCart(product.id)"
                                            :class="{ 'opacity-25': form.processing && form.product_id === product.id }"
                                            :disabled="form.processing && form.product_id === product.id"
                                            class="px-3 py-2 bg-gray-800 dark:bg-gray-700 text-white dark:text-gray-200 rounded-md">
                                            Agregar al carrito
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-8 flex justify-center">
                        <nav aria-label="Product navigation">
                            <ul class="inline-flex items-center -space-x-px">
                                <li v-for="(link, key) in products.links">
                                    <button v-if="link.url === null" :key="key" disabled :class="{
                                        'block rounded-l-lg': key === 0,
                                        'block rounded-r-lg': key === products.links.length - 1,
                                    }"
                                        class="px-3 py-2 leading-tight border dark:border-gray-700 text-gray-500 bg-white border-gray-300 dark:bg-gray-600 dark:text-gray-500 cursor-not-allowed"
                                        v-html="link.label" />
                                    <Link v-else :href="link.url" v-html="link.label" :class="{
                                        'block rounded-l-lg': key === 0,
                                        'block rounded-r-lg': key === products.links.length - 1,
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
