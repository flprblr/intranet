<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Inertia } from '@inertiajs/inertia';
import { Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    orders: {
        data: Object,
    },
});

const form = useForm();

let search = ref('');

watch(search, (value) => {
    Inertia.get(
        "/order",
        {
            search: value
        },
        {
            preserveState: true,
        },
    );
});

function destroy(name, id) {
    if (confirm("Estas seguro que deseas eliminar la tienda " + name + "?")) {
        form.delete(route('order.destroy', id));
    }
}

function moneyFormat(value) {
    let money = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
    return '$' + money
}

function dateFormat(value) {
    let parts = value.split('-');
    let formattedDate = `${parts[2]}/${parts[1]}/${parts[0]}`;
    return formattedDate;
}

</script>

<template>
    <AppLayout title="Pedidos E-Commerce">
        <template #header>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h2 class="mt-1 font-semibold text-xl text-gray-800 float-left">Pedidos E-Commerce</h2>
                </div>
                <div>
                    <Link
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition float-right"
                        v-if="can('order.create')" :href="route('order.create')">Crear</Link>
                </div>
            </div>
        </template>
        <div class="py-8">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg">
                    <div class="bg-white px-4 py-4 flex items-center justify-between border-gray-200 sm:px-6">
                        <input type="text" v-model="search"
                            class="uppercase text-xs border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                            placeholder="Buscar...">
                    </div>
                    <table v-if="orders.total != 0" class="w-full text-sm text-left text-gray-900">
                        <thead class="text-xs text-gray-900 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Tienda</th>
                                <th scope="col" class="px-6 py-3">Pedido</th>
                                <th scope="col" class="px-6 py-3">Boleta</th>
                                <th scope="col" class="px-6 py-3">Nota de Crédito</th>
                                <th scope="col" class="px-6 py-3">Fecha</th>
                                <th scope="col" class="px-6 py-3">Total</th>
                                <th scope="col" class="px-6 py-3">Estado</th>
                                <th scope="col" class="px-6 py-3">Cliente</th>
                                <th scope="col" class="px-6 py-3">RUT</th>
                                <th scope="col" class="px-6 py-3">Correo Electrónico</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white border-b border-gray-300 hover:bg-gray-50" v-for="order in orders.data">
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{
                                    order.prefix }}</td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    <a :href="`/order/${order.id}`" type="button"
                                        class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg px-3 py-2 text-xs mr-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                                        target="_blank">{{ order.order_number }}</a>
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    <a v-if="order.invoice_number != NULL"
                                        class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg px-3 py-2 text-xs mr-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                                        :href="order.invoice_url" target="_blank">{{ order.invoice_number }}</a>
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    <a v-if="order.invoice_number_rev != NULL"
                                        class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg px-3 py-2 text-xs mr-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                                        :href="order.invoice_url_rev" target="_blank">{{ order.invoice_number_rev }}</a>
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{
                                    dateFormat(order.date_created) }}</td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{
                                    moneyFormat(order.total) }}</td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ order.name
                                }}</td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{
                                    order.billing_first_name }} {{ order.billing_last_name }}</td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{
                                    order.billing_rut }}</td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{
                                    order.billing_email }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div>
                        <div class="bg-white px-4 py-4 flex items-center justify-between sm:px-6">

                            <div class="flex-1 flex justify-between sm:hidden">
                                <div v-if="orders.links.length > 3" v-for="(link, index) in orders.links">
                                    <Link v-if="index == 0"
                                        class="uppercase px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                                        v-html="link.label" :href="link.url" />
                                    <Link v-else-if="index == orders.links.length - 1"
                                        class="uppercase px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                                        v-html="link.label" :href="link.url" />
                                </div>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p v-if="orders.total == 0" class="uppercase text-xs text-gray-900">No se encontraron
                                        resultados</p>
                                    <p v-else class="uppercase text-xs text-gray-900">Mostrando {{ orders.from }} al {{
                                        orders.to }} de {{ orders.total }} resultados</p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md">
                                        <div v-if="orders.links.length > 3" v-for="(link, index) in orders.links">
                                            <Link v-if="index == 0"
                                                class="uppercase relative inline-flex items-center px-3 py-2 rounded-l-md border-l border-t border-b border-gray-300 bg-white text-xs font-medium text-gray-900 hover:bg-gray-50"
                                                v-html="link.label" :href="link.url" />
                                            <Link v-else-if="index == orders.links.length - 1"
                                                class="uppercase relative inline-flex items-center px-3 py-2 rounded-r-md border-r border-t border-b border-gray-300 bg-white text-xs font-medium text-gray-900 hover:bg-gray-50"
                                                v-html="link.label" :href="link.url" />
                                            <Link v-else-if="index == orders.links.length - 2"
                                                class="uppercase bg-white border-gray-300 text-gray-900 hover:bg-gray-50 hidden md:inline-flex relative items-center px-4 py-2 border text-xs font-medium"
                                                v-html="link.label" :href="link.url" />
                                            <Link v-else
                                                class="uppercase bg-white border-gray-300 text-gray-900 hover:bg-gray-50 hidden md:inline-flex relative items-center px-4 py-2 border-l border-t border-b text-xs font-medium"
                                                :class="{ 'bg-white': link.active }" v-html="link.label" :href="link.url" />
                                        </div>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
