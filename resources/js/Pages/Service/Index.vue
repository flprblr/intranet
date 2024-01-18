<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { Inertia } from '@inertiajs/inertia';
    import { Link, useForm } from '@inertiajs/inertia-vue3';
    import { ref, watch} from 'vue';
    
    const props = defineProps({
        services: {
            data: Object,
        },
    });

    const form = useForm();

    let search = ref('');

    watch(search, (value) => {
        Inertia.get(
            "/station",
            { search: value },
            {
                preserveState: true,
            }
        );
    }); 

    function destroy(description, id) {
        if (confirm("Estas seguro que deseas eliminar el servicio " + description + "?")) {
            form.delete(route('service.destroy', id));
        }
    };

</script>

<template>
    <AppLayout title="Servicio Técnico">
        <template #header>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h2 class="mt-1 font-semibold text-xl text-gray-800 float-left">Servicio Técnico</h2>
                </div>
                <div class="float-right">
                    <Link
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition float-right"
                        :href="route('service.create')">Crear</Link>
                </div>
            </div>
        </template>
        <div class="py-8">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg">
                    <div class="bg-white px-4 py-4 flex items-center justify-between border-gray-200 sm:px-6">
                        <input type="text" v-model="search" class="uppercase text-xs border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full" placeholder="Buscar...">
                    </div>
                    <table v-if="services.total!=0" class="w-full text-sm text-left text-gray-900">
                        <thead class="text-xs text-gray-900 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">ID</th>
                                <th scope="col" class="px-6 py-3">Descripción</th>
                                <th scope="col" class="px-6 py-3">Tienda</th>
                                <th scope="col" class="px-6 py-3">Anexo</th>
                                <th scope="col" class="px-6 py-3">Dirección IP</th>
                                <th scope="col" class="px-6 py-3">N° de Télefono</th>
                                <th scope="col" class="px-6 py-3 float-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white border-b border-gray-300 hover:bg-gray-50" v-for="service in services.data">
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ service.id }}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ service.service}}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ service.store}}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ service.service_extension }}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ service.service_ip }}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ service.service_phone }}
                                </td>
                                <td scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap float-right">
                                    <Link :href="route('service.edit', service.id)"
                                        class="uppercase px-3 py-1 bg-white border-l border-t border-b border-gray-300 rounded-l-md text-xs text-gray-900 tracking-widest hover:bg-gray-100 active:bg-gray-900 focus:outline-none focus:border-gray-200 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">
                                    Editar
                                    </Link>
                                    <Link
                                        class="uppercase px-3 py-1 bg-white border border-gray-300 rounded-r-md text-xs text-gray-900 tracking-widest hover:bg-gray-100 active:bg-gray-900 focus:outline-none focus:border-gray-200 focus:ring focus:ring-gray-200 disabled:opacity-25 transition"
                                        @click="destroy(service.service, service.id)">
                                    Eliminar
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div>
                        <div class="bg-white px-4 py-4 flex items-center justify-between sm:px-6">
                            
                            <div class="flex-1 flex justify-between sm:hidden">
                                <div v-if="services.links.length > 3" v-for="(link, index) in services.links">
                                    <Link v-if="index==0" class="uppercase px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50" v-html="link.label" :href="link.url" />
                                    <Link v-else-if="index ==services.links.length-1" class="uppercase px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50" v-html="link.label" :href="link.url" />
                                </div>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p v-if="services.total==0" class="uppercase text-xs text-gray-900">No se encontraron resultados</p>
                                    <p v-else class="uppercase text-xs text-gray-900">Mostrando {{ services.from }} al {{ services.to }} de {{ services.total }} resultados</p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md">
                                        <div v-if="services.links.length > 3" v-for="(link, index) in services.links">
                                            <Link v-if="index==0" class="uppercase relative inline-flex items-center px-2 py-1 rounded-l-md border-l border-t border-b border-gray-300 bg-white text-xs font-medium text-gray-900 hover:bg-gray-50" v-html="link.label" :href="link.url" />
                                            <Link v-else-if="index ==services.links.length-1" class="uppercase relative inline-flex items-center px-2 py-1 rounded-r-md border-r border-t border-b border-gray-300 bg-white text-xs font-medium text-gray-900 hover:bg-gray-50" v-html="link.label" :href="link.url" />
                                            <Link v-else-if="index ==services.links.length-2" class="uppercase bg-white border-gray-300 text-gray-900 hover:bg-gray-50 hidden md:inline-flex relative items-center px-3 py-1 border text-xs font-medium" v-html="link.label" :href="link.url" />
                                            <Link v-else class="uppercase bg-white border-gray-300 text-gray-900 hover:bg-gray-50 hidden md:inline-flex relative items-center px-3 py-1 border-l border-t border-b text-xs font-medium" :class="{ 'bg-white': link.active }" v-html="link.label" :href="link.url" />
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