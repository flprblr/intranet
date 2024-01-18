<script setup>
    import  AppLayout  from '@/Layouts/AppLayout.vue';
    import { Inertia } from "@inertiajs/inertia";
    import { useForm} from "@inertiajs/inertia-vue3";
   
    const props = defineProps({
        productos: {
            data: Object,
        }
    });
    
    const form = useForm({
        tienda: null,
        search: null,
        codigo: null,
      
       
    });
    function submit() {
      Inertia.post(route("product.store"), form);
      return { form, submit };
    }
   

</script>
<template>
    <AppLayout title="productos">
        <template #header>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h2 class="mt-1 font-semibold text-xl text-gray-800 float-left">Buscar productos</h2>
                </div>
                <div class="float-right"></div>
            </div>
        </template>    
        <div class="py-8">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg">
                    <div class="bg-white px-4 py-4 flex items-center justify-between border-gray-200 sm:px-6">
                       
                        <form @submit.prevent="submit" style="margin:5px;">
                            
                            <select class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm" v-model="form.tienda" id="tienda" >
                             <option value="0">Seleccionar Empresa</option>
                             <option value="1">Producto IGS</option>
                             <option value="2">Producto Belsport</option>
                            </select>
                            <select class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"  v-model="form.search" id="search" >
                             <option value="0">Seleccionar Tipo Busqueda</option>
                             <option value="1">Codigo Padre</option>
                             <option value="2">Variante</option>
                             <option value="3">Ean13</option>
                            </select>
                            <input type="text" v-model="form.codigo" style="margin:5px;" size="40"  placeholder=""/>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition" style="margin:5px;">Buscar</button>
                        </form>                    
                    </div>
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-900 uppercase bg-gray-50">
                            <tr>					
                                <th scope="col" class="px-6 py-3">Codigo Padre</th>
                                <th scope="col" class="px-6 py-3">Nombre</th>
                                <th scope="col" class="px-6 py-3">EAN13</th>
                                <th scope="col" class="px-6 py-3">Variante</th>
                                <th scope="col" class="px-6 py-3">Descripcion</th>
                                <th scope="col" class="px-6 py-3">Talla</th>
                            </tr>
                            </thead>
                            <tbody>
                
                                <tr class="bg-white border-b border-gray-300 hover:bg-gray-50" v-for="producto in productos">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{producto.value}}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{producto.name}}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{producto.upc}}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{producto.sku}}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{producto.description}}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{producto.talla}}</td>
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