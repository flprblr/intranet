<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { reactive, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';
import { validateRut, formatRut } from '@fdograph/rut-utilities';


const props = defineProps({
    errors: {
        data: Object,
    }
});

const form = reactive({
    rut: null,
    name: null,
    phone: null,
    email: null,

    xblnr: null,
    sold_by: null,
    sellername: null,
    fecha_emision: null,
    type: null,
    matnr: null,
    description: null,
    serial: null,

    xblnr_gext: null,
    matnr_gext: null,
    description_gext: null,
    valor_gext: null,


    valid_from: null,
    valid_to: null,

    comment: null,
});

let valid = false;

function submit() {
    if (valid) {
        Inertia.post('/extguarantee', form)
    }
};

const res = ref(null);


function fetchData() {
    console.log("El valor de xblnr es: ", form.xblnr)
    axios.get(`/getdatainvoice/` + form.xblnr, {
    }).then(response => {
        if (response.data && response.data.length > 0) {
            res.value = response.data;
            form.valid_from = sumarUnAno(response.data[0].fecha_emision, 1);
            form.valid_to = sumarUnAno(response.data[0].fecha_emision, 2);
            form.fecha_emision = response.data[0].fecha_emision;
        } else {
            form.tipo_material_sap = '';
            form.description = '';
            form.nro_serie_apple = '';
            form.fecha_emision = '';
            res.value = null;
        }
    }).catch(error => {
        console.log(error);
    });
};

function updateDataLine(event) {
    const selectedSku = event.target.value;
    const selectedProduct = res.value.find(item => item.sku === selectedSku);
    if (selectedProduct) {
        form.tipo_material_sap = selectedProduct.tipo_material_sap;
        form.description = selectedProduct.description;
        form.nro_serie_apple = selectedProduct.nro_serie_apple;
    }
}

const resGext = ref(null);

function fetchDataGext() {
    console.log("El valor de xblnrGext es: ", form.xblnr_gext)
    axios.get(`/getdatainvoicegext/` + form.xblnr_gext, {
    }).then(response => {
        if (response.data && response.data.length > 0) {
            resGext.value = response.data;
        } else {
            form.description_gext = '';
            form.valor_gext = '';

            resGext.value = null;
        }
    }).catch(error => {
        console.log(error);
    });
};

const res_sellerName = ref(null);

function fetchDatasellerName() {
    axios.get(`/getsellername/` + form.sold_by, {
    }).then(response => {
        if (response.data && response.data.length > 0) {

            res_sellerName.value = response.data[0].name;
            form.sellername = response.data[0].name;
        } else {
            res_sellerName.value = null;
            form.sellername = '';

        }
    }).catch(error => {
        console.log(error);
    });
};

function updateDataLineGext(event) {
    const selectedSku = event.target.value;
    const selectedProduct = resGext.value.find(item => item.sku === selectedSku);
    if (selectedProduct) {
        form.description_gext = selectedProduct.description;
        form.valor_gext = selectedProduct.valor_total;
    }
}

function sumarUnAno(fecha, anos) {
    // Convertir la fecha en un objeto Date
    console.log(fecha);

    if (!fecha) {
        console.error("La fecha proporcionada es undefined o nula");
        return null;
    }

    // Convertir la fecha en un objeto Date
    const partesDeLaFecha = fecha.split('-');
    if (partesDeLaFecha.length !== 3) {
        console.error("El formato de la fecha no es válido");
        return null;
    }
    const ano = parseInt(partesDeLaFecha[0], 10);
    const mes = parseInt(partesDeLaFecha[1], 10) - 1; // Los meses en JavaScript son de 0-11
    const dia = parseInt(partesDeLaFecha[2], 10);

    const fechaActual = new Date(ano, mes, dia);
    const nuevoAno = ano + anos;

    const nuevaFecha = new Date(fechaActual);
    nuevaFecha.setFullYear(nuevoAno);

    const formattedDate = `${nuevaFecha.getFullYear()}-${String(nuevaFecha.getMonth() + 1).padStart(2, '0')}-${String(nuevaFecha.getDate()).padStart(2, '0')}`;

    // Ahora, `formattedDate` contiene la nueva fecha en formato YYYY-MM-DD.
    return formattedDate;
}


const validateGoal = () => {
    form.xblnr = form.xblnr.replace(/[^0-9]/g, '');
};
const validateGoalGext = () => {
    form.xblnr_gext = form.xblnr_gext.replace(/[^0-9]/g, '');
};

const valRut = () => {
    if (validateRut(form.rut)) {
        form.rut = formatRut(form.rut);
        valid = true;
    } else {
        valid = false;
    }
};

</script>


<template>
    <AppLayout title="Crear Garantia Extendida">
        <template #header>
            <div class="grid grid-cols-2 gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Garantia Extendida</h2>
            </div>
        </template>

        <div class="mx-auto py-10 sm:px-10 lg:px-8">
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="mt-5 md:mt-0 md:col-span-10">
                        <form @submit.prevent="submit">
                            <!--Datos Cliente-->
                            <div class="px-4 py-5 bg-gray-800 sm:p-6 shadow">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-white">
                                            <span>Datos Cliente</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-gray-100"></div>
                            <div class="px-4 py-5 bg-white sm:p-6 shadow">
                                <div class="grid grid-cols-8 gap-6">
                                    <!--Rut-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="rut">
                                            <span>Rut</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="textoInput" type="text" autocomplete="rut" v-model="form.rut" @blur="valRut"
                                            required>
                                    </div>

                                    <!--Nombre-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="full_name">
                                            <span>Nombre</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="full_name" type="text" autocomplete="name" v-model="form.full_name"
                                            required>
                                    </div>
                                    <!--Télefono-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="phone">
                                            <span>Teléfono</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="phone" type="text" autocomplete="phone" v-model="form.phone" required>
                                    </div>
                                    <!--Correo-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="email">
                                            <span>Correo</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="email" type="email" autocomplete="email" v-model="form.email" required>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-100"></div>
                            <!--Producto Garantizado-->
                            <div class="px-4 py-5 bg-gray-800 sm:p-6 shadow">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-white">
                                            <span>Producto Garantizado</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-gray-100"></div>
                            <div class="px-4 py-5 bg-white sm:p-6 shadow">
                                <div class="grid grid-cols-8 gap-6">
                                    <!--Dte-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="xblnr">
                                            <span>DTE</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="xblnr" type="text" autocomplete="xblnr" v-model="form.xblnr" required
                                            @blur="fetchData" @keyup.enter="fetchData" @input="validateGoal">
                                    </div>

                                    <!--Sold By-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="sold_by">
                                            <span>Código Vendedor</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="sold_by" type="text" autocomplete="sold_by" v-model="form.sold_by"
                                            @blur="fetchDatasellerName" @keyup.enter="fetchDatasellerName" required>
                                    </div>


                                    <!--sellername-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="sellername">
                                            <span>Nombre Vendedor</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="sellername" type="text" autocomplete="sellername" v-model="form.sellername"
                                            readonly required>
                                    </div>


                                    <!--Fecha de Compra-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="sold_date">
                                            <span>Fecha de Compra</span>
                                        </label>
                                        <input v-if="res && res[0] && res[0].fecha_emision !== undefined"
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="sold_date" type="date" autocomplete="sold_date" v-model="form.fecha_emision"
                                            readonly required>
                                        <input v-else
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="sold_date" type="date" autocomplete="sold_date" v-model="form.fecha_emision"
                                            readonly required>
                                    </div>

                                    <!--Código Producto -->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="matnr">
                                            <span>Código Producto</span>
                                        </label>
                                        <select v-if="res && res[0] && res[0].sku !== undefined" @change="updateDataLine"
                                            v-model="form.matnr"
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                            id="matnr">
                                            <option v-for="(item, index) in res" :key="index">
                                                {{ item.sku }}
                                            </option>
                                        </select>
                                        <select v-else
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                            id="matnr">
                                            <option value="" disabled selected>Selecciona Producto</option>
                                        </select>
                                    </div>

                                    <!--Tipo Producto-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="type">
                                            <span>Tipo Producto</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="type" type="text" autocomplete="type" v-model="form.tipo_material_sap"
                                            readonly required>
                                    </div>

                                    <!--Descripción-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="description">
                                            <span>Descripción</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="description" type="text" autocomplete="description"
                                            v-model="form.description" readonly required>
                                    </div>

                                    <!--Número de Serie-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="serial">
                                            <span>Número de Serie</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="serial" type="text" autocomplete="serial" v-model="form.nro_serie_apple"
                                            readonly required>
                                    </div>
                                </div>
                            </div>

                            <!--Garantia Extendida-->
                            <div class="px-4 py-5 bg-gray-800 sm:p-6 shadow ">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-white">
                                            <span>Garantia Extendida</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-gray-100"></div>
                            <div class="px-4 py-5 bg-white sm:p-6 shadow">
                                <div class="grid grid-cols-8 gap-6">
                                    <!--Dte GEXT-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="xblnr_gext">
                                            <span>DTE</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="xblnr_gext" type="text" autocomplete="xblnr_gext" v-model="form.xblnr_gext"
                                            @blur="fetchDataGext" @keyup.enter="fetchDataGext" @input="validateGoalGext"
                                            required>
                                    </div>

                                    <!--Código Producto GEXT-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="matnr">
                                            <span>Código Producto</span>
                                        </label>
                                        <select v-if="resGext && resGext[0] && resGext[0].sku !== undefined"
                                            @change="updateDataLineGext" v-model="form.matnr_gext"
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                            id="matnr_gext">
                                            <option v-for="(item, index) in resGext" :key="index">
                                                {{ item.sku }}
                                            </option>
                                        </select>
                                        <select v-else
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                            id="matnr_gext">
                                            <option value="" disabled selected>Selecciona Producto</option>
                                        </select>
                                    </div>

                                    <!--Descripción GEXT-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="description_gext">
                                            <span>Descripción</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="description_gext" type="tel" autocomplete="description_gext"
                                            v-model="form.description_gext" required readonly>
                                    </div>

                                    <!--Valor GEXT-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="matnr">
                                            <span>Valor</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="valor_gext" type="text" autocomplete="valor_gext" v-model="form.valor_gext"
                                            required readonly>
                                    </div>

                                    <!--Fecha de Inicio Cobertura-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="valid_from">
                                            <span>Fecha de Inicio Cobertura</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="valid_from" type="date" autocomplete="valid_from" v-model="form.valid_from"
                                            required readonly>
                                    </div>

                                    <!--Fecha de Termino Cobertura-->
                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700" for="valid_to">
                                            <span>Fecha de Termino Cobertura</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="valid_to" type="date" autocomplete="valid_to" v-model="form.valid_to"
                                            required readonly>
                                    </div>
                                </div>
                            </div>

                            <!--Comment-->
                            <div class="px-4 py-5 bg-white sm:p-6 shadow">
                                <div class="grid grid-cols-8 gap-6">
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="comment">
                                            <span>Observaciones</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="comment" type="text" autocomplete="comment" v-model="form.comment">
                                    </div>
                                </div>
                            </div>
                            <!--Errors-->
                            <div class="px-4 py-5 bg-white sm:p-6">
                                <span style="color:red;">{{ errors }}</span>
                            </div>
                            <!--Boton-->
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
</template >
