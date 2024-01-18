<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { reactive } from 'vue';
    import { Inertia } from '@inertiajs/inertia';

    const props = defineProps({
        roles: {
            data: Object,
        },
    });

    const form = reactive({
        name: null,
        email: null,
        password: null,
    });

    function submit() {
      Inertia.post('/user', form)
    };
    
</script>

<template>
    <AppLayout title="Crear Usuario">
        <template #header>
            <div class="grid grid-cols-2 gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Usuarios</h2>
            </div>
        </template>
        <div class="mx-auto py-10 sm:px-6 lg:px-8">
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1 flex justify-between">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium text-gray-900">Crear Usuario</h3>
                            <p class="mt-1 text-sm text-gray-600">Ingresa nombre, correo electrónico y contraseña para
                                crear un nuevo usuario.</p>
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
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="email">
                                            <span>Correo Electrónico</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="email" type="email" v-model="form.email">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="email">
                                            <span>Rol</span>
                                        </label>
                                        <select class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm" id="role" v-model="form.role">
                                            <option v-for="role in roles" :value="role.name">{{ role.name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="email">
                                            <span>Contraseña</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            id="password" type="password" v-model="form.password">
                                    </div>
                                </div>
                            </div>
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
</template>
