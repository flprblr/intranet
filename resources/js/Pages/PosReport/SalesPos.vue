<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    organizacion:Object,
    result: Object,
    banners: Object,
});

const form = useForm({
    org: '',
    banner: '',
    dateStart: '',
    dateEnd: '',
    
});


const showTable = ref(false);

function toggleTable() {
  showTable.value = !showTable.value;
}

const exportToExcel = () => {
  // Obtener la tabla HTML y las filas
  const table = document.getElementById('table');
  const rows = table.querySelectorAll('tr');

  // Crear una instancia de ExcelJS
  const workbook = new ExcelJS.Workbook();
  const worksheet = workbook.addWorksheet('Hoja de Trabajo');

  // Obtener los encabezados de la tabla
  const headerRow = rows[0];
  const headers = [];
  headerRow.querySelectorAll('th').forEach((cell) => {
    headers.push(cell.textContent);
  });

  // Agregar una fila de encabezados a la hoja de trabajo
  worksheet.addRow(headers);

  // Recorrer las filas de datos y agregarlos a la hoja de trabajo
  for (let i = 1; i < rows.length; i++) {
    const rowData = [];
    rows[i].querySelectorAll('td').forEach((cell) => {
      rowData.push(cell.textContent);
    });
    worksheet.addRow(rowData);
  }

  // Escribir el archivo Excel
  workbook.xlsx.writeBuffer().then((buffer) => {
    const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'Reporte.xlsx';
    a.click();
    window.URL.revokeObjectURL(url);
  });
};

</script>
<template>
    <AppLayout title="Reporte General">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Boletas
            </h2>
        </template>
        <div class="pt-6"> 
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="relative overflow-x-auto p-5">
                        <form class="grid grid-rows-1 md:grid-flow-col gap-3" @submit.prevent="form.post('/sales/pos')">
                            <div>
                                <label for="organization" class="block text-sm font-medium text-gray-700">Organización</label>
                                <select id="organization" v-model="form.org" name="organization" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                    <option value="" disabled>Seleccionar</option>
                                    <option :value="organizaciones.acronym" v-for="organizaciones in organizacion">{{ organizaciones.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="banner" class="block text-sm font-medium text-gray-700">Banner</label>
                                <select id="banner" v-model="form.banner" name="banner" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                    <option value="" disabled>Seleccionar</option>
                                    <option :value="banner.name" v-for="banner in banners">{{ banner.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="date-start" class="block text-sm font-medium text-gray-700">Fecha Desde</label>
                                <input type="date" v-model="form.dateStart" name="date-start" id="date-start" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="date-end" class="block text-sm font-medium text-gray-700">Fecha Hasta</label>
                                <input type="date" v-model="form.dateEnd" name="date-end" id="date-end" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 invisible">&nbsp;</label>
                                <button type="submit" :disabled="form.processing" class="mt-1 w-full inline-flex justify-center my-6 rounded-md border border-transparent bg-gray-800 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-900 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Buscar</button>
                            </div>
                        </form>
                        
                        <div class="mt-1 w-full inline-flex justify-center ">
                            <button class="mt-1  justify-center  w-52 inline-flex float-right rounded-md border border-transparent bg-gray-800 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-900 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" @click="exportToExcel">Exportar a Excel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="py-8">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg">
                    <table  class="hidden-table w-full text-sm text-left text-gray-900" id="table">
                        <thead class="text-xs text-gray-900 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Fecha Documento</th>
                                <th scope="col" class="px-6 py-3">Hora</th>
                                <th scope="col" class="px-6 py-3">DTE</th>
                                <th scope="col" class="px-6 py-3">Folio</th>
                                <th scope="col" class="px-6 py-3">Centro</th>
                                <th scope="col" class="px-6 py-3">SKU</th>
                                <th scope="col" class="px-6 py-3">Descripción</th>
                                <th scope="col" class="px-6 py-3">Unidades articulo</th>
                                <th scope="col" class="px-6 py-3">Monto de venta</th>
                                <th scope="col" class="px-6 py-3">Correo cliente</th>
                                <th scope="col" class="px-6 py-3">Codigo Vendedor</th>
                                <th scope="col" class="px-6 py-3">Rut</th>
                                <th scope="col" class="px-6 py-3">Vendedor</th>
                                <th scope="col" class="px-6 py-3">Cajero</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white border-b border-gray-300 hover:bg-gray-50" v-for="results in result">
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ results.fecha_documento}}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ results.hora}}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ results.tipo_documento}}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ results.folio_boleta}}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ results.warehouse_id_sap}}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ results.codigo_articulo}}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ results.descripcion_articulo}}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ results.unidades_articulo}}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ results.monto_venta}}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ results.email_cliente}}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ results.cod_vendedor}}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ results.rut_vendedor}}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ results.vendedor}}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ results.cajero}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<style>
  .hidden-table {
    display: none;
  }
</style>