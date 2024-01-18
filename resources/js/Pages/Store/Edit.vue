<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { useForm } from "@inertiajs/inertia-vue3";

    const props = defineProps({
        store: {
            data: Object,
        },
        states: {
            data: Object,
        },
        cities: {
            data: Object,
        },
        stations: {
            data: Object,
        },
        services: {
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
        store_id: props.services.store_id,
    });

    const submit = () => {
        form.put(route("store.update", props.store.id));
    };

</script>

<template>
    <AppLayout title="Editar Tienda">
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
                            <h3 class="text-lg font-medium text-gray-900">Editar Tienda
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
                                            name="store_number" type="text" v-model="form.store_number">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="description">
                                            <span>Descripción</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="description" type="text" autocomplete="description" v-model="form.description">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="description">
                                            <span>Centro</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="description" type="text" autocomplete="werks" v-model="form.werks">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="extension">
                                            <span>Anexo</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="extension" type="text" autocomplete="description" v-model="form.extension">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="phone_number">
                                            <span>Teléfono</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="description" type="text"
                                            v-model="form.phone_number">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="router_ip">
                                            <span>IP Gateway</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="gateway_ip" type="text" autocomplete="Router" v-model="form.gateway_ip">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="router_ip">
                                            <span>IP Router</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="router_ip" type="text" autocomplete="Router" v-model="form.router_ip">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="phone_ip">
                                            <span>IP Teléfono</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="phone_ip" type="text" autocomplete="description" v-model="form.phone_ip">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="clock_ip">
                                            <span>IP Reloj</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="clock_ip" type="text" autocomplete="clock" v-model="form.clock_ip">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="state_id">
                                            <span>Modelo</span>
                                            <select
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                                id="clock_model" v-model="form.clock_model">
                                                <option value="LP400">ZKTeco LP400</option>
                                                <option value="G3">ZKTeco G3</option>
                                            </select>
                                        </label>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="wifi_password">
                                            <span>Contraseña Wi-Fi</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="wifi_password" type="text" autocomplete="wifi" v-model="form.wifi_password">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="service_code">
                                            <span>Codigo de Servicio</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="service_code" type="text" autocomplete="service_code"
                                            v-model="form.service_code">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="address">
                                            <span>Dirección</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="address" type="text" autocomplete="address" v-model="form.address">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="email">
                                            <span>Email</span>
                                        </label>
                                        <input
                                            class="border-gray-300 focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                            name="email" type="text" autocomplete="email" v-model="form.email">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700" for="state_id">
                                            <span>Estado</span>
                                            <select
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                                id="store_id" v-model="form.condition">
                                                <option :value="false">Cerrada
                                                </option>
                                                <option :value="true">Abierta
                                                </option>
                                            </select>
                                        </label>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block font-medium text-sm text-gray-700">
                                            <span>Región</span>
                                        </label>
                                        <select v-model="form.state_id"
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                            id="roles">
                                            <option v-for="state in states" :value="state.id">{{ state.name }}</option>
                                        </select>
                                        <br>
                                        <label class="block font-medium text-sm text-gray-700">
                                            <span>Ciudad</span>
                                        </label>
                                        <select v-model="form.city_id"
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                            name="cities">
                                            <option v-for="city in cities"
                                                :value="city.id">{{ city.name }}</option>
                                        </select>
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
                                            <li v-for="service in services" class="text-gray-700 ml-2 my-2"><span class="text-gray-700">Descripción ( {{service.service}} )  Anexo: ( {{service.service_extension}} ) Dirección IP: ( {{service.service_ip}} ) Telefono: ( {{service.service_phone}} )</span></li>
                                        </ul>
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
    