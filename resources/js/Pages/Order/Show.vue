<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Inertia } from "@inertiajs/inertia";
import { Link, useForm } from "@inertiajs/inertia-vue3";
import { ref, watch } from "vue";

const props = defineProps({
    orders: {
        Object,
    },
    rows: {
        Object,
    },
    payments: {
        Object,
    },
});

const form = useForm();

let search = ref("");

watch(search, (value) => {
    Inertia.get(
        "/orders",
        {
            search: value,
        },
        {
            preserveState: true,
        }
    );
});

function moneyFormat(value) {
    let money = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return "$" + money;
}

function dateFormat(value) {
    let parts = value.split("-");
    let formattedDate = `${parts[2]}/${parts[1]}/${parts[0]}`;
    return formattedDate;
}
</script>

<template>
	<AppLayout title="Pedidos E-Commerce">
		<div class="w-full md:w-3/4 mx-auto p-8">
				<div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg">
					<div class="bg-white p-8" v-for="order in orders.data">
						<div class="grid grid-cols-2 p-1">
                            <div class="text-left">
                                <img v-if="order.prefix=='HOKA'" src="/img/hoka-logo.svg" class="block h-16 w-auto">
                                <img v-if="order.prefix=='SAUCONY'" src="/img/saucony-logo.svg" class="block h-16 w-auto">
                                <img v-if="order.prefix=='LOCKER'" src="/img/locker-logo.svg" class="block h-16 w-auto">
                            </div>
                            <div class="text-right">
                                <h1 class="text-xl font-medium text-gray-900"><b>Pedido</b>: {{ order.prefix + '-' + order.order_number}}</h1>
                                <h2 class="text-xl font-medium text-gray-900"><b>Documento</b>: <a :href="order.invoice_url" target="_blank">{{ order.invoice_number }}</a></h2>
                                <h3 class="text-base font-medium text-gray-900"><b>Fecha</b>: {{ dateFormat(order.date_created) }}</h3>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 p-1 gap-5 text-sm text-gray-900">
                            <div>
                                <h1 class="text-lg font-bold">Cliente</h1>
                                <p><b>Nombre</b>: {{ order.billing_first_name }} {{ order.billing_last_name }}</p>
                                <p><b>RUT</b>: {{ order.billing_rut }}</p>
                                <p><b>Correo Electrónico</b>: {{ order.billing_email }}</p>
                                <p><b>Teléfono</b>: {{ order.billing_phone }}</p>
                                <p><b>Dirección</b>: {{ order.billing_address_1 }} {{ order.billing_city }} {{ order.billing_state }}</p>
                            </div>
                            <div v-for="payment in payments">
                                <h1 class="text-lg font-bold">Pago</h1>
                                <p><b>Método</b>: {{ payment.method }}</p>
                                <p><b>Tipo</b>: {{ payment.type }}</p>
                                <p><b>Tarjeta</b>: {{ payment.last_digits }}</p>
                                <p><b>Monto</b>: {{ moneyFormat(payment.amount) }}</p>
                                <p><b>Referencia {{ payment.method }}</b>: {{ payment.external_id }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 py-5">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="">
                                        <tr class="border-b ">
                                            <th scope="col" class="px-2 py-4">
                                                #
                                            </th>
                                            <th scope="col" class="px-2 py-4">
                                                SKU
                                            </th>
                                            <th scope="col" class="px-2 py-4">
                                                DESCRIPCIÓN
                                            </th>
                                            <th scope="col" class="px-2 py-4 text-center">
                                                PRECIO
                                            </th>
                                            <th scope="col" class="px-2 py-4 text-center">
                                                CANTIDAD
                                            </th>
                                            <th scope="col" class="px-2 py-4 text-center">
                                                SUBTOTAL
                                            </th>
                                            <th scope="col" class="px-2 py-4 text-center">
                                                DESCUENTO
                                            </th>
                                            <th scope="col" class="px-2 py-4 text-center">
                                                TOTAL
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-medium">
                                        <tr class="bg-white border-b hover:bg-gray-50" v-for="(row, index) in rows">
                                            <th scope="row" class="px-2 py-4">
                                                {{ index+1 }}
                                            </th>
                                            <td class="px-2 py-4">
                                                {{ row.sku.toUpperCase() }}
                                            </td>
                                            <td class="px-2 py-4">
                                                {{ row.description.toUpperCase() }}
                                            </td>
                                            <td class="px-2 py-4 text-center">
                                                {{ moneyFormat(row.unit_price) }}
                                            </td>
                                            <td class="px-2 py-4 text-center">
                                                {{ row.quantity }}
                                            </td>
                                            <td class="px-2 py-4 text-center">
                                                {{ moneyFormat(row.subtotal) }}
                                            </td>
                                            <td class="px-2 py-4 text-center">
                                                {{ moneyFormat(row.discount) }}
                                            </td>
                                            <td class="px-2 py-4 text-center">
                                                {{ moneyFormat(row.total) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
					</div>
				</div>
			</div>
	</AppLayout>
</template>