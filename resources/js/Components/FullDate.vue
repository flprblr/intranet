<script setup>
let meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
let dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

let fechaActual = new Date();
let diaSemana = dias[fechaActual.getDay()];
let diaMes = fechaActual.getDate();
let mes = meses[fechaActual.getMonth()];
let ano = fechaActual.getFullYear();

let fechaFormateada = `${diaSemana} ${diaMes} de ${mes} de ${ano}`;
</script>
<template>
    <p class="text-md font-medium leading-8 text-gray-800">
        {{ fechaFormateada }}
    </p>
</template>