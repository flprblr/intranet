<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Inertia } from "@inertiajs/inertia";
import { Link, useForm } from "@inertiajs/inertia-vue3";
import { ref, watch } from "vue";

const props = defineProps({
    extguarantees: {
        Object,
    },
    extcustomers: {
        Object,
    },
});

const form = useForm();

let search = ref("");

watch(search, (value) => {
    Inertia.get(
        "/extguarantee",
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
    <AppLayout title="Garantia Extendida">
        <div class="w-full md:w-3/4 mx-auto p-8">
            <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg">
                <div class="bg-white p-8">
                    <div class="grid grid-cols-2 p-1">
                        <div class="text-left">
                            <img src="/img/ynk-logo.svg" class="block h-16 w-auto">
                        </div>
                        <div class="text-left">
                            <h1 class="text-2xl text-right font-bold">Garantía Extendida</h1>
                        </div>
                    </div>

                    <!--<div class="grid grid-cols-2 p-1 gap-5 text-sm text-gray-900" v-for="extcustomer in extcustomers">
                        <div>
                            <h1 class="text-lg font-bold">Datos Cliente</h1>
                            <p><b>Rut</b>: {{ extcustomer.rut }}</p>
                            <p><b>Nombre</b>: {{ extcustomer.full_name }}</p>
                            <p><b>Teléfono</b>: {{ extcustomer.phone }}</p>
                            <p><b>Mail</b>: {{ extcustomer.email }}</p>
                        </div>
                    </div>-->

                    <!-- Datos Cliente-->

                    <div class="grid grid-cols-1 py-5">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <tbody class="font-medium" v-for="extguarantee in extguarantees">
                                    <tr class="text-black">
                                        <td class="px-2 py-4">
                                        </td>
                                        <td class="text-black text-left">
                                            <div class="flex justify-between">
                                                <b>Folio:</b>
                                                <b> <span>{{ extguarantee.id }}</span></b>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="bg-black text-white">
                                        <td class="px-2 py-4">
                                            <b>Datos Cliente</b>
                                        </td>
                                        <td class="bg-white">
                                        </td>
                                    </tr>
                                    <tr class="bg-white">
                                        <td class="px-2 py-4">
                                            <b>Rut</b>:
                                        </td>
                                        <td class="px-2 py-4  text-right bg-gray-200">
                                            {{ extguarantee.rut }}
                                        </td>
                                    </tr>
                                    <tr class="bg-white">
                                        <td class="px-2 py-4">
                                            <b>Nombre</b>:
                                        </td>
                                        <td class="px-2 py-4  text-right bg-gray-200">
                                            {{ extguarantee.full_name }}
                                        </td>
                                    </tr>
                                    <tr class="bg-white">
                                        <td class="px-2 py-4">
                                            <b>Teléfono</b>:
                                        </td>
                                        <td class="px-2 py-4  text-right bg-gray-200">
                                            {{ extguarantee.phone }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-4 ">
                                            <b>Mail</b>:
                                        </td>
                                        <td class="px-2 py-4  text-right bg-gray-200">
                                            {{ extguarantee.email }}
                                        </td>
                                    </tr>
                                    <tr class="bg-black text-white">
                                        <td class="px-2 py-4">
                                            <b>Producto Garantizado</b>
                                        </td>
                                        <td class="bg-white">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-4 ">
                                            <b>Tipo Producto</b>:
                                        </td>
                                        <td class="px-2 py-4 text-right bg-gray-200">
                                            {{ extguarantee.type }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-4 ">
                                            <b>Código Producto</b>:
                                        </td>
                                        <td class="px-2 py-4 text-right bg-gray-200">
                                            {{ extguarantee.matnr }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-4 ">
                                            <b>Descripción del producto</b>:
                                        </td>
                                        <td class="px-2 py-4 text-right bg-gray-200">
                                            {{ extguarantee.description }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-4 ">
                                            <b>Número de Serie</b>:
                                        </td>
                                        <td class="px-2 py-4  text-right bg-gray-200">
                                            {{ extguarantee.serial }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-4 ">
                                            <b>Fecha de Compra</b>:
                                        </td>
                                        <td class="px-2 py-4  text-right bg-gray-200">
                                            {{ extguarantee.sold_date }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-4 ">
                                            <b>Código Vendedor </b>:
                                        </td>
                                        <td class="px-2 py-4  text-right bg-gray-200">
                                            {{ extguarantee.sold_by }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-4 ">
                                            <b>Nombre Vendedor </b>:
                                        </td>
                                        <td class="px-2 py-4  text-right bg-gray-200">
                                            {{ extguarantee.sellername }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-4 ">
                                            <b>N° Boleta </b>:
                                        </td>
                                        <td class="px-2 py-4  text-right bg-gray-200">
                                            {{ extguarantee.xblnr }}
                                        </td>
                                    </tr>
                                    <tr class="bg-black text-white">
                                        <td class="px-2 py-4">
                                            <b>Garantia Extendida</b>
                                        </td>
                                        <td class="bg-white">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-4 ">
                                            <b>N° Boleta </b>:
                                        </td>
                                        <td class="px-2 py-4 text-right bg-gray-200">
                                            {{ extguarantee.xblnr_gext }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-4 ">
                                            <b>Detalle</b>:
                                        </td>
                                        <td class="px-2 py-4 text-right bg-gray-200">
                                            {{ extguarantee.description_gext }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-4 ">
                                            <b>Valor</b>:
                                        </td>
                                        <td class="px-2 py-4 text-right bg-gray-200">
                                            {{ extguarantee.valor }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-4 ">
                                            <b>Fecha de Inicio Cobertura </b>:
                                        </td>
                                        <td class="px-2 py-4 text-right bg-gray-200">
                                            {{ extguarantee.valid_from }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="px-2 py-4 ">
                                            <b>Fecha de Termino Cobertura </b>:
                                        </td>
                                        <td class="px-2 py-4 text-right bg-gray-200">
                                            {{ extguarantee.valid_to }}
                                        </td>
                                    </tr>
                                    <tr class="border-t border-l border-r border-black mt-1">
                                        <td class="px-2 py-4">
                                            <b>Observaciones</b>
                                        </td>
                                        <td class="bg-white">
                                        </td>
                                    </tr>
                                    <tr class="px-2 py-4  text-center border-b border-l border-r border-black">
                                        <td colspan='2' class="px-2 py-4">
                                            {{ extguarantee.comment }}
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