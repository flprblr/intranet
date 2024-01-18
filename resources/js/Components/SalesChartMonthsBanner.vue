<script setup>
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend } from 'chart.js';
import { Line } from 'vue-chartjs';
import { onMounted, ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    society: String,
    salesOrg: String,
    channel: String,
    title: String
});

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend);

const data = ref({ labels: [], datasets: [] });
const sales = ref([]);

const options = {
    pointRadius: 4,
    pointHoverRadius: 5,
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        mode: 'index'
    },
    scales: {
        y: {
            min: 0,
            ticks: {
                callback: function (value) {
                    if (value > 1e9) {
                        return '$' + (value / 1e9) + 'B';
                    } else {
                        return '$' + (value / 1e6) + 'M';
                    }
                }
            }
        }
    }
};

onMounted(() => {
    const date = new Date();
    const year = date.getFullYear();
    const lastYear = date.getFullYear() - 1;
    const twoYearsAgo = date.getFullYear() - 2;
    const labels = [];
    axios.get(`/api/saleschartmonth/${props.society}/${props.salesOrg}/${props.channel}`)
        .then(response => {
            sales.value = response.data;
            data.value = {
                labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                datasets: [
                    {
                        label: year,
                        borderColor: '#ea5b0c',
                        backgroundColor: '#ea5b0c',
                        data: sales.value[0]
                    },
                    {
                        label: lastYear,
                        borderColor: '#666666',
                        backgroundColor: '#666666',
                        data: sales.value[1]
                    },
                    {
                        label: twoYearsAgo,
                        borderColor: '#999999',
                        backgroundColor: '#999999',
                        data: sales.value[2],
                        hidden: true
                    }
                ]
            }
        })
        .catch(error => {
            console.log(error);
        });
})
</script>
<template>
    <h1 class="mb-1 text-xl font-bold tracking-tight text-gray-900 text-center uppercase py-6">{{ title }}</h1>
    <Line :data="data" :options="options" />
</template>