<script setup>

import { ref } from 'vue'
import SalesCardBannerSkeleton from '@/Components/SalesCardBannerSkeleton.vue';

const props = defineProps({
    title: String,
    society: String,
    salesOrg: String,
    channel: String,
    url: String,
})

const res = ref(null)

axios.get(`/api/salescard/${props.society}/${props.salesOrg}/${props.channel}`)
    .then(response => {
        res.value = response.data
    })
    .catch(error => {
        console.log(error)
    });

const date = new Date()
let year = date.getFullYear()
let lastYear = date.getFullYear() - 1


function moneyFormat(value) {
    let money = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
    return '$' + money
}

function grow(actual, last) {
    if (last === 0 && actual === 0) {
        return '0%';
    } else if (last === 0) {
        return '100%';
    } else if (actual === 0) {
        return '-100%';
    }
    return `${((actual - last) / last * 100).toFixed(2)}%`;
}

</script>
<template>
    <div class="max-w pt-2 bg-white rounded-lg shadow-md text-center">

        <div v-if="props.url" class="grid grid-cols-2 px-5">
            <div class="text-left">
                <h1 class="mb-1 text-xl font-bold tracking-tight text-gray-900 uppercase py-6">{{ props.title }}</h1>
            </div>
            <div class="text-right pt-6">
                <a :href="`/sales/${props.url}`" class="px-3 py-2 text-gray-900 hover:text-white border border-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-xs">Ver Más</a>
            </div>
        </div>
        <div v-else>
            <h1 class="mb-1 text-xl font-bold tracking-tight text-gray-900 uppercase py-6">{{ props.title }}</h1>
        </div>
    
        <div class="relative overflow-x-auto">
            <table class="w-full md:text-sm text-xs text-gray-900 font-medium">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-3 py-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mx-auto">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                            </svg>
                        </th>
                        <th scope="col" class="text-sm px-3 py-3">{{ lastYear }}</th>
                        <th scope="col" class="text-sm px-3 py-3">{{ year }}</th>
                        <th scope="col" class="px-3 py-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mx-auto">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                            </svg>
                        </th>
                    </tr>
                </thead>
                <tbody v-if="res">
                    <tr class="border-b">
                        <th scope="row" class="px-1 py-4 font-medium text-gray-900 whitespace-nowrap">Día</th>
                        <td class="px-1 py-4">{{ moneyFormat(res.lastYearDay) }}</td>
                        <td class="px-1 py-4 font-bold">{{ moneyFormat(res.actualDay) }}</td>
                        <td class="px-1 py-4 font-bold" :class="[(res.actualDay-res.lastYearDay)/res.lastYearDay >= 0 ? 'text-green-600' : 'text-red-600']">{{ grow(res.actualDay, res.lastYearDay) }}</td>
                    </tr>
                    <tr class="border-b">
                        <th scope="row" class="px-1 py-4 font-medium text-gray-900 whitespace-nowrap">Mes</th>
                        <td class="px-1 py-4">{{ moneyFormat(res.lastYearMonth) }}</td>
                        <td class="px-1 py-4 font-bold">{{ moneyFormat(res.actualMonth) }}</td>
                        <td class="px-1 py-4 font-bold" :class="[(res.actualMonth-res.lastYearMonth)/res.lastYearMonth >= 0 ? 'text-green-600' : 'text-red-600']">{{ grow(res.actualMonth, res.lastYearMonth) }}</td>
                    </tr>
                    <tr>
                        <th scope="row" class="px-1 py-4 font-medium text-gray-900 whitespace-nowrap">Año</th>
                        <td class="px-1 py-4">{{ moneyFormat(res.lastYearYear) }}</td>
                        <td class="px-1 py-4 font-bold">{{ moneyFormat(res.actualYear) }}</td>
                        <td class="px-1 py-4 font-bold" :class="[(res.actualYear-res.lastYearYear)/res.lastYearYear >= 0 ? 'text-green-600' : 'text-red-600']">{{ grow(res.actualYear, res.lastYearYear) }}</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <SalesCardBannerSkeleton></SalesCardBannerSkeleton>
                </tbody>
            </table>
        </div>
    </div>
</template>