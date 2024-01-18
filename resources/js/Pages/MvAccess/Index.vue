<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Inertia } from '@inertiajs/inertia';
import { Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    mvAccess: Object
});

const form = useForm();

let search = ref('');

watch(search, (value) => {
    Inertia.get(
        "/mvaccess",
        { search: value },
        {
            preserveState: true,
        }
    );
});

function destroy(code, id) {
    if (confirm("Estas seguro que deseas eliminar la conexión " + code + "?")) {
        form.delete(route('mvaccess.destroy', id));
    }
};

</script>

<template>
    <AppLayout title="Conexión Multivende">
        <template #header>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h2 class="mt-1 font-semibold text-xl text-gray-800 float-left">Conexión</h2>
                </div>
                <div class="float-right">
                    <!--<Link
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition float-right"
                        :href="route('mvaccess.create')">Crear</Link>-->
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
                    <table v-if="mvAccess.total != 0" class="w-full text-sm text-left text-gray-900">
                        <thead class="text-xs text-gray-900 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">ID</th>
                                <th scope="col" class="px-6 py-3">URL</th>
                                <th scope="col" class="px-6 py-3">ID Client</th>
                                <th scope="col" class="px-6 py-3">Secret Client</th>
                                <th scope="col" class="px-6 py-3">Code</th>
                                <th scope="col" class="px-6 py-3">Expires Token</th>
                                <th scope="col" class="px-6 py-3 float-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white border-b border-gray-300 hover:bg-gray-50" v-for="access in mvAccess.data">
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    <Link :href="route('mvaccess.show', access.id)">{{ access.id }}</Link>
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ access.base_url }}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ access.client_id }}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ access.client_secret }}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ access.code }}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ access.expires_token }}
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap float-right">
                                    <Link :href="route('mvaccess.edit', access.id)"
                                        class="uppercase px-3 py-1 bg-white border-l border-t border-b border-gray-300 rounded-l-md text-xs text-gray-900 tracking-widest hover:bg-gray-100 active:bg-gray-900 focus:outline-none focus:border-gray-200 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">
                                    Editar
                                    </Link>
                                    <Link
                                        class="uppercase px-3 py-1 bg-white border border-gray-300 rounded-r-md text-xs text-gray-900 tracking-widest hover:bg-gray-100 active:bg-gray-900 focus:outline-none focus:border-gray-200 focus:ring focus:ring-gray-200 disabled:opacity-25 transition"
                                        @click="destroy(access.name, access.id)">
                                    Eliminar
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div>
                        <div class="bg-white px-4 py-4 flex items-center justify-between sm:px-6">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <div v-if="mvAccess.links.length > 3" v-for="(link, index) in mvAccess.links">
                                    <Link v-if="index == 0"
                                        class="uppercase px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                                        v-html="link.label" :href="link.url" />
                                    <Link v-else-if="index == mvAccess.links.length - 1"
                                        class="uppercase px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                                        v-html="link.label" :href="link.url" />
                                </div>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p v-if="mvAccess.total == 0" class="uppercase text-xs text-gray-900">No se encontraron
                                        resultados</p>
                                    <p v-else class="uppercase text-xs text-gray-900">Mostrando {{ mvAccess.from }} al {{
                                        mvAccess.to }} de {{ mvAccess.total }} resultados</p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md">
                                        <div v-if="mvAccess.links.length > 3" v-for="(link, index) in mvAccess.links">
                                            <Link v-if="index == 0"
                                                class="focus:z-20 uppercase relative inline-flex items-center px-2 py-1 rounded-l-md border-l border-t border-b border-gray-300 bg-white text-xs font-medium text-gray-900 hover:bg-gray-50"
                                                v-html="link.label" :href="link.url" />
                                            <Link v-else-if="index == mvAccess.links.length - 1"
                                                class="uppercase relative inline-flex items-center px-2 py-1 rounded-r-md border-r border-t border-b border-gray-300 bg-white text-xs font-medium text-gray-900 hover:bg-gray-50"
                                                v-html="link.label" :href="link.url" />
                                            <Link v-else-if="index == mvAccess.links.length - 2"
                                                class="uppercase bg-white border-gray-300 text-gray-900 hover:bg-gray-50 hidden md:inline-flex relative items-center px-3 py-1 border text-xs font-medium"
                                                v-html="link.label" :href="link.url" />
                                            <Link v-else
                                                class="uppercase bg-white border-gray-300 text-gray-900 hover:bg-gray-50 hidden md:inline-flex relative items-center px-3 py-1 border-l border-t border-b text-xs font-medium"
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