<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

const props = defineProps({
    order: {},
});

const parseStatus = (status) => {
    switch (status) {
        case 'pending':
            return 'Pendiente de pago';
        case 'paid':
            return 'Pagada';
        case 'canceled':
            return 'Cancelada';
        case 'refunded':
            return 'Reembolsada';
        case 'failed':
            return 'Fallida';
        case 'shipped':
            return 'Enviada';
        case 'delivered':
            return 'Entregada';
        case 'completed':
            return 'Completada';
        default:
            return 'Error, contacta al administrador';
    }
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
                            <p class="text-gray-500 dark:text-gray-400">
                                Tu orden ha sido recibida y está siendo procesada.
                            </p>
                            <!-- Show status -->
                            <div class="flex gap-2 mt-4">
                                <span class="text-gray-500 dark:text-gray-400">Estado:</span>
                                <span class="text-gray-800 dark:text-white">{{ parseStatus(order.status) }}</span>
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
