<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { useForm } from "@inertiajs/inertia-vue3";

    const props = defineProps({
        dbPermissions: {
            data: Object,
        },
        role: {
            data: Object,
        },
        permissionsRol: {
            data: Object,
        },
    });

    const form = useForm({
        id: props.role.id,
        name: props.role.name,
        dbPermission: []
    });

    for (let j = 0; j < props.permissionsRol.length; j++) {
        form.dbPermission.push(props.permissionsRol[j].permission_id);
    }

    const submit = () => {
        form.put(route("role.update", props.role.id));
    };

</script>

<template>
    <AppLayout title="Editar Rol">
        <template #header>
            <div class="grid grid-cols-2 gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Roles</h2>
            </div>
        </template>
        <div class="mx-auto py-10 sm:px-6 lg:px-8">
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1 flex justify-between">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium text-gray-900">Editar Rol</h3>
                            <p class="mt-1 text-sm text-gray-600">Modifica nombre para actualizar un rol.</p>
                        </div>
                        <div class="px-4 sm:px-0"></div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form @submit.prevent="submit">
                            <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="name">
                                            <span>Nombre</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="name" type="text" autocomplete="name" v-model="form.name">
                                    </div>
                                </div>
                                <div class="col-span-6 sm:col-span-4">
                                    <div class="mt-1">
                                        <label class="block font-medium text-sm text-gray-700 mt-3 mb-1">
                                            <span>Permisos</span>
                                        </label>
                                        <div v-for="dbPermission in dbPermissions">
                                            <div class="flex items-start">
                                                <div class="flex h-5 items-center">
                                                    <input type="checkbox" v-model="form.dbPermission"
                                                        :name="dbPermission.id" :value="dbPermission.id"
                                                        class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-500">
                                                </div>
                                                <div class="ml-1 text-sm">
                                                    <label class="font-medium text-gray-700">{{dbPermission.name}}</label>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
