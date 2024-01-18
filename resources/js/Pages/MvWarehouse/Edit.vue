<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from "@inertiajs/inertia-vue3";

const props = defineProps({
    mvWarehouses: {
        data: Object,
    },
    companies: {
        data: Object,
    },
});

const form = useForm({
    id: props.mvWarehouses.id,
    id_warehouse: props.mvWarehouses.id_warehouse,
    vkorg: props.mvWarehouses.vkorg,
    werks: props.mvWarehouses.werks,
    lgort: props.mvWarehouses.lgort,
    vtweg: props.mvWarehouses.vtweg,
    des_sovos: props.mvWarehouses.des_sovos,
    logo: props.mvWarehouses.logo,
    company_id: props.mvWarehouses.company_id,
    active: props.mvWarehouses.active,
});

const submit = () => {
    form.put(route("mvwarehouse.update", props.mvWarehouses.id));
};

</script>
    
<template>
    <AppLayout title="Editar Bodega">
        <template #header>
            <div class="grid grid-cols-2 gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Bodegas</h2>
            </div>
        </template>
        <div class="mx-auto py-10 sm:px-6 lg:px-8">
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1 flex justify-between">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium text-gray-900">Editar Bodega</h3>
                            <p class="mt-1 text-sm text-gray-600">Modifica los datos para actualizar la bodega.</p>
                        </div>
                        <div class="px-4 sm:px-0"></div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form @submit.prevent="submit">
                            <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="id_warehouse">
                                            <span>ID Warehouse MV</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="id_warehouse" type="text" autocomplete="id_warehouse"
                                            v-model="form.id_warehouse" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="vkorg">
                                            <span>Org. Ventas</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="vkorg" type="text" autocomplete="vkorg" v-model="form.vkorg" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="werks">
                                            <span>Centro</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="werks" type="text" autocomplete="werks" v-model="form.werks" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="lgort">
                                            <span>Almacén</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="lgort" type="text" autocomplete="lgort" v-model="form.lgort" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="vtweg">
                                            <span>Canal Distribución</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="vtweg" type="text" autocomplete="vtweg" v-model="form.vtweg" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="des_sovos">
                                            <span>Descripción SOVOS</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="des_sovos" type="text" autocomplete="des_sovos" v-model="form.des_sovos"
                                            required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="logo">
                                            <span>URL Logo</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="logo" type="text" autocomplete="logo" v-model="form.logo" required>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="company_id">
                                            <span>Empresa</span>
                                        </label>
                                        <select
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                            id="role" v-model="form.company_id" required>
                                            <option v-for="company in companies" :value="company.id">{{ company.name }}
                                            </option>
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
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</AppLayout></template>
    