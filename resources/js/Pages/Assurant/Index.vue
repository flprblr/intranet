<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Inertia } from "@inertiajs/inertia";
import { useForm } from "@inertiajs/inertia-vue3";

const props = defineProps({
    ventas: {
        data: Object,
    },

});

const form = useForm({
    fecha_inicial: null,
    fecha_final: null,

});
function submit() {
    Inertia.post(route("assurant.index"), form);
    return { form, submit };
}

function exportTableToExcel(tableID, filename = '') {
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

    // Specify file name
    filename = filename ? filename + '.xls' : 'excel_data.xls';

    // Create download link element
    downloadLink = document.createElement("a");

    document.body.appendChild(downloadLink);

    if (navigator.msSaveOrOpenBlob) {
        var blob = new Blob(['ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        // Create a link to the file
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

        // Setting the file name
        downloadLink.download = filename;

        //triggering the function
        downloadLink.click();
    }
}
</script>
<template>
    <AppLayout title="Reporte Assurant">
        <template #header>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h2 class="mt-1 font-semibold text-xl text-gray-800 float-left">Reporte Assurant</h2>
                </div>
            </div>
        </template>
        <div class="py-8">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg">
                    <div class="bg-white px-4 pt-4 pb-7">
                        <form @submit.prevent="submit">
                            <div class="flex flex-wrap gap-3 items-end">
                                <div>
                                    <label class="block font-medium text-sm text-gray-700">
                                        <span>Fecha Inicial</span>
                                    </label>
                                    <input v-model="form.fecha_inicial" type="date"
                                        class="bg-gray-50 border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-gray-700">
                                        <span>Fecha Final</span>
                                    </label>
                                    <input v-model="form.fecha_final" type="date"
                                        class="bg-gray-50 border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full">
                                </div>
                                <div>
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Buscar</button>
                                </div>
                                <div>
                                    <a v-on:click="exportTableToExcel('signups', 'Assurant')"
                                        class="inline-flex items-center px-4 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Excel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <table v-if="ventas.length>1" id="signups" class="w-full text-sm text-left text-gray-900" border="1">
                        <thead class="text-xs text-gray-900 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Tipo</th>
                                <th scope="col" class="px-6 py-3">Nº Boleta</th>
                                <th scope="col" class="px-6 py-3">Nº Caja</th>
                                <th scope="col" class="px-6 py-3">Nº Tienda</th>
                                <th scope="col" class="px-6 py-3">Fecha</th>
                                <th scope="col" class="px-6 py-3">RUT</th>
                                <th scope="col" class="px-6 py-3">SKU</th>
                                <th scope="col" class="px-6 py-3">Nº Serie</th>
                                <th scope="col" class="px-6 py-3">Unidades</th>
                                <th scope="col" class="px-6 py-3">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="venta in ventas" class="bg-white border-b border-gray-300 hover:bg-gray-50" >
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ venta.tipo }}</td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ venta.nro_boleta }}</td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ venta.nro_caja }}</td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ venta.nro_tienda }}</td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ venta.fecha }}</td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ venta.rut }}</td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ venta.sku }}</td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ venta.nro_serie }}</td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ venta.cantidad }}</td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">${{ venta.venta }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
