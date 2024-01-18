<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { reactive } from 'vue';
    import { Inertia } from '@inertiajs/inertia';

    const props = defineProps({
        companies: {
            data: Object,
        },
    });

    const form = reactive({
        prefix: null,
        url: null,
        api_key: null,
        api_secret: null,
        logo: null,
        vkorg: null,
        werks: null,
        lgort: null,
        auart: null,
        fkart: null
    });

    function submit() {
      Inertia.post('/ecommerce', form)
    };
    
</script>

<template>
    <AppLayout title="Crear E-Commerce">
        <template #header>
            <div class="grid grid-cols-2 gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">E-Commerce</h2>
            </div>
        </template>
        <div class="mx-auto py-10 sm:px-6 lg:px-8">
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1 flex justify-between">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium text-gray-900">Crear E-Commerce</h3>
                            <p class="mt-1 text-sm text-gray-600">Ingresa los datos para crear un E-Commerce.</p>
                        </div>
                        <div class="px-4 sm:px-0"></div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form @submit.prevent="submit">
                            <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="prefix">
                                            <span>Nombre</span>
                                        </label>
                                        <input class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full" id="prefix" type="text" v-model="form.prefix" maxlength="50" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="url">
                                            <span>URL</span>
                                        </label>
                                        <input class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full" id="url" type="text" v-model="form.url" maxlength="50" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="api_key">
                                            <span>API Key</span>
                                        </label>
                                        <input class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full" id="api_key" type="text" v-model="form.api_key" maxlength="43" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="api_secret">
                                            <span>API Secret</span>
                                        </label>
                                        <input class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full" id="api_secret" type="text" v-model="form.api_secret" maxlength="43" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="logo">
                                            <span>Logo</span>
                                        </label>
                                        <input class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full" id="logo" type="text" v-model="form.logo" maxlength="25" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="vkorg">
                                            <span>Organizaci贸n de Venta</span>
                                        </label>
                                        <input class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full" id="vkorg" type="text" v-model="form.vkorg" maxlength="4" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="werks">
                                            <span>Centro</span>
                                        </label>
                                        <input class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full" id="werks" type="text" v-model="form.werks" maxlength="4" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="lgort">
                                            <span>Almacen</span>
                                        </label>
                                        <input class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full" id="lolgorto" type="text" v-model="form.lgort" maxlength="4" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="auart">
                                            <span>Clase de Pedido</span>
                                        </label>
                                        <input class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full" id="auart" type="text" v-model="form.auart" maxlength="4" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="fkart">
                                            <span>Clase de Facturaci贸n</span>
                                        </label>
                                        <input class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full" id="fkart" type="text" v-model="form.fkart" maxlength="4" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="company_id">
                                            <span>Empresa</span>
                                        </label>
                                        <select class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm" id="company_id" v-model="form.company_id">
                                            <option v-for="company in companies" :value="company.id">{{ company.name }}</option>
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
    </AppLayout>
</template>
