<script setup>
    import  AppLayout  from '@/Layouts/AppLayout.vue';
    import { Inertia } from "@inertiajs/inertia";
    import { Link, useForm} from "@inertiajs/inertia-vue3";
   
    const props = defineProps({
        boletas: {
            data: Object,
        },
        stores: {
            data: Object,
        },
    });
    
    const form = useForm({
        id: props.stores.id,
        name: props.stores.name,
        store_id:props.stores.id,
        fecha_inicial: null,
        fecha_final: null,
        monto: null,
        pedido: null,
        folio: null,
       
    });
    function submit() {
      Inertia.post(route("invoice.store"), form);
      return { form, submit };
    }
   

 

</script>
<template>
    <AppLayout title="Buscar Boleta">
        <template #header>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h2 class="mt-1 font-semibold text-xl text-gray-800 float-left">Boletas</h2>
                </div>
                <div class="float-right">
                  
                </div>
            </div>
        </template>
        <div class="py-8">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg">
                    <div class="bg-white px-4 py-4 flex items-center justify-between border-gray-200 sm:px-6">
                        <form @submit.prevent="submit" style="margin:5px;">
                            <select class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm" id="role" v-model="form.store_id">
                             <option v-for="store in stores" :value="store.m_warehouse_id">{{ store.name }}</option>
                            </select>
                            <input type="date" style="margin:5px;" v-model="form.fecha_inicial" placeholder="Seleccionar Fecha Inicial"/>
                            <input type="date" style="margin:5px;" v-model="form.fecha_final" placeholder="Seleccionar Fecha Final"/>
                            <input type="text" style="margin:5px;" size="10" v-model="form.monto" placeholder="Ingrese Monto"/>
                            <input type="text" style="margin:5px;" size="20" v-model="form.pedido" placeholder="Ingresa Pedido (opcional)"/>
                            <input type="text" style="margin:5px;" size="15" v-model="form.folio" placeholder="Ingresa Folio"/>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition" style="margin:5px;">Buscar</button>
                        </form>                    
                    </div>
                    <table v-if="boletas.total!=0" class="w-full text-sm text-left">
                <thead class="text-xs text-gray-900 uppercase bg-gray-50">
                    <tr>
                    
                    <th scope="col" class="px-6 py-3">Folio</th>
                    <th scope="col" class="px-6 py-3">Pedido</th>
                    <th scope="col" class="px-6 py-3">Fecha Emision</th>
                    <th scope="col" class="px-6 py-3">Pagado Neto</th>
                    <th scope="col" class="px-6 py-3">Producto</th>
                    <th scope="col" class="px-6 py-3">Cantidad</th>
                    <th scope="col" class="px-6 py-3">Valor Producto</th>
                    <th scope="col" class="px-6 py-3">Ver Boleta</th>
                    </tr>
                </thead>
                <tbody>
                
                        <tr class="bg-white border-b border-gray-300 hover:bg-gray-50" v-for="boleta in boletas">
                            
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{boleta.nro_documento}}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{boleta.nro_consigment_s4}}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{boleta.fecha_emision}}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">${{boleta.monto_neto | toCurrency}}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{boleta.descripcion}}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{boleta.cantidad}}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">${{boleta.valor_unitario | toCurrency }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                <Link
                                        v-if="can('invoice.show')" :href="route('invoice.show', boleta.nro_documento)"
                                        class="uppercase px-3 py-1 bg-white border-l border-t border-b border-gray-300 rounded-l-md text-xs text-gray-900 tracking-widest hover:bg-gray-100 active:bg-gray-900 focus:outline-none focus:border-gray-200 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">
                                    Boleta
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
                    <div>
                        
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>