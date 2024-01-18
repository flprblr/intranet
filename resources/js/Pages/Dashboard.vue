<script setup>

import AppLayout from "@/Layouts/AppLayout.vue";
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/inertia-vue3';

const { props } = usePage()

if (JSON.parse(props.value.permissions).roles[0] === 'Gerencia y Sub-Gerencia' && window.location.pathname !== '/sales') {
    Inertia.visit('/sales');
}
else if (JSON.parse(props.value.permissions).roles[0] === 'Comercial' && window.location.pathname !== '/sales') {
    Inertia.visit('/sales');
}
else if (JSON.parse(props.value.permissions).roles[0] === 'Tiendas' && window.location.pathname !== '/store') {
    Inertia.visit('/store');
}
else if (JSON.parse(props.value.permissions).roles[0] === 'Inventario' && window.location.pathname !== '/store') {
    Inertia.visit('/store');
}
else if (JSON.parse(props.value.permissions).roles[0] === 'Supervisor(a)' && window.location.pathname !== '/store') {
    Inertia.visit('/store');
}
else if (JSON.parse(props.value.permissions).roles[0] === 'Recepción' && window.location.pathname !== '/store') {
    Inertia.visit('/store');
}
else if (JSON.parse(props.value.permissions).roles[0] === 'Contabilidad' && window.location.pathname !== '/store') {
    Inertia.visit('/store');
}
else if (JSON.parse(props.value.permissions).roles[0] === 'Apple / Aufbau' && window.location.pathname !== '/assurant') {
    Inertia.visit('/assurant');
}
else if (JSON.parse(props.value.permissions).roles[0] === 'Servicio al Cliente' && window.location.pathname !== '/order') {
    Inertia.visit('/order');
}
else if (JSON.parse(props.value.permissions).roles[0] === 'E-Commerce' && window.location.pathname !== '/order') {
    Inertia.visit('/order');
}

</script>
<template>
    <AppLayout title="Dashboard">
        
    </AppLayout>
</template>
