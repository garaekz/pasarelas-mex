<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

const props = defineProps({
    shipping: 0,
    subtotal: 0,
    tax: 0,
    total: 0,
});

const form = useForm({
    payment_method: null,
});

const placeOrder = () => {
    form.post(route('orders.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AppLayout title="Checkout">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Checkout
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div
                        class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent">

                        <h1 class="text-2xl font-medium text-gray-900 dark:text-white">
                            Ejemplo de Checkout
                        </h1>

                        <p class="mt-6 text-gray-500 dark:text-gray-400 leading-relaxed">
                            Este es un ejemplo de como implementar las pasarelas de pago en tu aplicación, llegados a este
                            punto ya tienes los productos en tu carrito y puedes realizar el pago con la pasarela que
                            selecciones al momento del checkout.

                            <br><br>
                            Es importante que recuerdes que este es un ejemplo, gracias a esto puedes haber notado que
                            nos hemos saltado algunos pasos como la creación de cuentas, la autenticación de usuarios y
                            llegados a
                            a este punto la creación de direcciones tanto de facturación como de envío.
                        </p>
                    </div>
                </div>
                <div class="flex sm:flex-row flex-col gap-4 items-start mt-4">
                    <div class="w-full sm:w-2/3 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8">
                        <div class="flex flex-col">
                            <ul class="flex flex-col w-full gap-4">
                                <li>
                                    <input
                                        type="radio"
                                        id="bank_account"
                                        name="payment-type"
                                        value="bank_account"
                                        class="hidden peer"
                                        v-model="form.payment_method"
                                        required>
                                    <label for="bank_account"
                                        class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-blue-500 peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                                        <div class="block">
                                            <div class="w-full text-lg font-semibold">Transferencia Bancaria</div>
                                            <div class="w-full">SPEI</div>
                                        </div>
                                        <v-icon
                                            v-if="form.payment_method == 'bank_account'"
                                            name="bi-check2-circle" scale="2" />
                                        <v-icon
                                            v-else
                                            name="bi-circle" scale="1.6" />
                                    </label>
                                </li>
                                <li>
                                    <input
                                        type="radio"
                                        id="oxxo"
                                        name="payment-type"
                                        value="oxxo"
                                        class="hidden peer"
                                        v-model="form.payment_method"
                                        required>
                                    <label for="oxxo"
                                        class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-blue-500 peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                                        <div class="block pr-12">
                                            <div class="w-full text-lg font-semibold">Pago en OXXO / 7Eleven</div>
                                            <div class="w-full">
                                                Pago en efectivo en: OXXO, 7Eleven, Farmacias Benavides, Farmacias del ahorro, Circle K, Extra, Waldos
                                            </div>
                                        </div>
                                        <v-icon
                                            v-if="form.payment_method == 'oxxo'"
                                            name="bi-check2-circle" scale="2" />
                                        <v-icon
                                            v-else
                                            name="bi-circle" scale="1.6" />
                                    </label>
                                </li>
                                <li>
                                    <input
                                        type="radio"
                                        id="card"
                                        name="payment-type"
                                        value="card"
                                        class="hidden peer"
                                        v-model="form.payment_method"
                                        required>
                                    <label for="card"
                                        class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-blue-500 peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                                        <div class="block">
                                            <div class="w-full text-lg font-semibold">Tarjeta de Crédito / Débito</div>
                                            <div class="w-full">Tarjetas de crédito y débito</div>
                                        </div>
                                        <v-icon
                                            v-if="form.payment_method == 'card'"
                                            name="bi-check2-circle" scale="2" />
                                        <v-icon
                                            v-else
                                            name="bi-circle" scale="1.6" />
                                    </label>
                                </li>
                            </ul>
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
                            <button
                                @click="placeOrder"
                                :disabled="!form.payment_method"
                                :class="{'bg-blue-500 hover:bg-blue-700': form.payment_method, 'bg-gray-500 cursor-not-allowed': !form.payment_method}"
                                class="flex justify-center text-white font-bold py-2 px-4 rounded w-full">
                                Pagar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</AppLayout></template>
