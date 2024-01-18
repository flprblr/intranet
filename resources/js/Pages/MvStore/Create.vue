<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    marketplaces: {
        data: Object,
    },
});

const form = reactive({
    connection: null,
    active: false
});

function submit() {
    Inertia.post('/mvstore', form)
};

</script>

<template>
    <AppLayout title="Crear Tienda">
        <template #header>
            <div class="grid grid-cols-2 gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tienda</h2>
            </div>
        </template>
        <div class="mx-auto py-10 sm:px-6 lg:px-8">
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1 flex justify-between">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium text-gray-900">Crear Tienda</h3>
                            <p class="mt-1 text-sm text-gray-600">Ingresa los datos para crear una Tienda.</p>
                        </div>
                        <div class="px-4 sm:px-0"></div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form @submit.prevent="submit">
                            <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="prefix">
                                            <span>Tienda</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="prefix" type="text" v-model="form.connection" maxlength="255" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="marketplace_id">
                                            <span>Marketplace</span>
                                        </label>
                                        <select
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                            id="marketplace_id" v-model="form.marketplace_id">
                                            <option v-for="market in marketplaces" :value="market.id">{{ market.marketplace
                                            }}</option>
                                        </select>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="active">
                                            <span>Estado</span>
                                        </label>
                                        <select
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="active" v-model="form.active" required>
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Crear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </div>
</AppLayout></template>
