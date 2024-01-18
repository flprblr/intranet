<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { useForm } from "@inertiajs/inertia-vue3";
    
    const props = defineProps({
        manager: {
            data: Object,
        },
        store: {
            data: Object,
        }
    });
    
    const form = useForm({
        id: props.manager.id,
        name: props.manager.name,
        email: props.manager.email,
        store_id: props.manager.store_id,
    });
    
    const submit = () => {
        form.put(route("manager.update", props.manager.id));
    };
    
</script>
    
<template>
    <AppLayout title="Editar Supervisor">
        <template #header>
            <div class="grid grid-cols-2 gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Supervisores</h2>
            </div>
        </template>
        <div class="mx-auto py-10 sm:px-6 lg:px-8">
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1 flex justify-between">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium text-gray-900">Editar Comuna</h3>
                            <p class="mt-1 text-sm text-gray-600">Modifica el nombre y la región para actualizar la comuna</p>
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
                                    <input class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full" id="name" type="text" autocomplete="name" v-model="form.name">
                                </div>
                                <div class="col-span-6 sm:col-span-4">
                                    <label class="block font-medium text-sm text-gray-700" for="name">
                                        <span>Email</span>
                                    </label>
                                    <input class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full" id="email" type="text" autocomplete="email" v-model="form.email">
                                </div>
                                <div class="col-span-6 sm:col-span-4">
                                    <label class="block font-medium text-sm text-gray-700" for="region">
                                        <span>Tienda</span>
                                    </label>
                                    <select class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm" id="role" v-model="form.store_id">
                                        <option v-for="stores in store" :value="stores.id">{{ stores.description }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Actualizar</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
    