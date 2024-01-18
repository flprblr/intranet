<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import {useForm } from '@inertiajs/inertia-vue3';

    const props = defineProps({
        store: {
            data: Object,
        },
        stations: {
            data: Object,
        },
        stores: {
            data: Object,
        },
        cities: {
            data: Object,
        },
        states: {
            data: Object,
        },
        services: {
            data: Object,
        },
        manager: {
            data: Object,
        },
    });

    const form = useForm({
        store_number: props.store.store_number,
        description: props.store.description,
        werks: props.store.werks,
        extension: props.store.extension,
        phone_number: props.store.phone_number,
        gateway_ip: props.store.gateway_ip,
        router_ip: props.store.router_ip,
        phone_ip: props.store.phone_ip,
        clock_ip: props.store.clock_ip,
        clock_model: props.store.clock_model,
        wifi_password: props.store.wifi_password,
        service_code: props.store.service_code,
        address: props.store.address,
        email: props.store.email,
        condition: props.store.condition,
        number_p: props.store.number_p,
        ip: props.store.ip,
        state_id: props.store.state_id,
        city_id: props.store.city_id,
        station_ip : props.stations.station_ip,
        station_number : props.stations.station_number,
        service: props.services.service,
        service_phone: props.services.service_phone,
        service_extension: props.services.service_extension,
        service_ip: props.services.service_ip,
        manager: props.services.manager,
        manager_email: props.services.manager_email,
    });

</script>

<template>
    <AppLayout title="Ver Tienda">
        <template #header>
            <div class="grid grid-cols-2 gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tiendas</h2>
            </div>
        </template>
        <div class="mx-auto py-10 sm:px-6 lg:px-8">
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1 flex justify-between">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium text-gray-900">Ver Tienda
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">Modifica los datos para actualizar una tienda.</p>
                        </div>
                        <div class="px-4 sm:px-0"></div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form @submit.prevent="submit">
                            <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="store_number">
                                            <span>N° Tienda</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="store_number" disabled type="text" v-model="form.store_number">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="description">
                                            <span>Descripción</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="description" disabled type="text" autocomplete="description" v-model="form.description">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="description">
                                            <span>Centro</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="description" disabled type="text" autocomplete="werks" v-model="form.werks">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="extension">
                                            <span>Anexo</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="extension" disabled type="text" autocomplete="description" v-model="form.extension">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="phone_number">
                                            <span>Teléfono</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="description" disabled type="text"
                                            v-model="form.phone_number">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="router_ip">
                                            <span>IP Router</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="router_ip" disabled type="text" autocomplete="Router" v-model="form.router_ip">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="phone_ip">
                                            <span>IP Teléfono</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="phone_ip" disabled type="text" autocomplete="description" v-model="form.phone_ip">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="clock_ip">
                                            <span>IP Reloj</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="clock_ip" disabled type="text" autocomplete="clock" v-model="form.clock_ip">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="wifi_password">
                                            <span>Contraseña Wi-Fi</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="wifi_password" disabled type="text" autocomplete="wifi" v-model="form.wifi_password">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="service_code">
                                            <span>Codigo de Servicio</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="service_code" disabled type="text" autocomplete="description"
                                            v-model="form.service_code">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="address">
                                            <span>Dirección</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="address" disabled type="text" autocomplete="description" v-model="form.address">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="email">
                                            <span>Correo</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="email" disabled type="text" v-model="form.email">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="email">
                                            <span>Estado</span>
                                        </label>
                                        <input v-if="form.condition == 0"
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="email" disabled type="text" placeholder="Cerrado">
                                        <input v-else="form.condition == 1"
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="email" disabled type="text" placeholder="Abierto" >
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700">
                                            <span>Región</span>
                                        </label>
                                        <select v-model="form.state_id"
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                            id="roles" 
                                            disabled>
                                            <option v-for="state in states" :value="state.id">{{ state.name }}</option>
                                        </select>
                                        <br>
                                        <label class="block font-medium text-sm text-gray-700">
                                            <span>Ciudad</span>
                                        </label>
                                        <select v-model="form.city_id"
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                            name="cities" disabled>
                                            <option v-for="city in cities"
                                                :value="city.id">{{ city.name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="stations">
                                            <span>Supervisor</span>
                                        </label>
                                        <ul v-for="managers in manager"  class="list-disc space-y-2 pl-4 text-sm">
                                            <li class="text-gray-700 ml-2 my-2"><span class="text-gray-700">Nombre: {{ managers.manager }}</span></li>
                                            <li class="text-gray-700 ml-2 my-2"><span class="text-gray-700">Correo: {{ managers.manager_email }}</span></li>
                                        </ul>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="stations">
                                            <span>Cajas Asociadas</span>
                                        </label>
                                        <ul class="list-disc space-y-2 pl-4 text-sm">
                                            <li v-for="station in stations" class="text-gray-700 ml-2 my-2"><span class="text-gray-700">{{station.station_number}} ( {{station.station_ip}} )</span></li>
                                        </ul>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="stations">
                                            <span>Servicio Técnico Asociado</span>
                                        </label>
                                        <ul class="list-disc space-y-2 pl-4 text-sm">
                                            <li v-for="service in services" class="text-gray-700 ml-2 my-2"><span class="text-gray-700">Descripción ( {{service.service}} )  Anexo: ( {{service.service_extension}} ) Dirección IP: ( {{service.service_ip}} ) Télefono: ( {{service.service_phone}} )</span></li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
