<script setup>
import { ref } from 'vue'

const props = defineProps({
    title: String,
    society: String,
    salesOrg: String,
    channel: String,
})

const res = ref(null)

axios.get(`/api/salesranking/${props.society}/${props.salesOrg}/${props.channel}`)
    .then(response => {
        res.value = response.data
        sortData();
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

</script>
<template>
    <div class="max-w pt-2 bg-white rounded-lg shadow-md text-center">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight text-gray-900 uppercase py-6">{{ props.title }}</h1>
        </div>
        <div class="relative overflow-y-scroll custom-scrollbar h-48">
            <table class="w-full md:text-sm text-xs text-gray-900 font-medium">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-3 py-3 sticky top-0 bg-gray-50">#</th>
                        <th scope="col" class="text-sm px-3 py-3 sticky top-0 bg-gray-50">Tienda</th>
                        <th scope="col" class="text-sm px-3 py-3 sticky top-0 bg-gray-50">Día</th>
                        <th scope="col" class="text-sm px-3 py-3 sticky top-0 bg-gray-50">30 Días</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b" v-for="(store, index) in res">
                        <td class="px-1 py-2 text-xs">{{ index+1 }}</td>
                        <td class="px-1 py-2 text-xs">{{ store.STORE }}</td>
                        <td class="px-1 py-2 text-xs">{{ moneyFormat(store.SALES_TODAY) }}</td>
                        <td class="px-1 py-2 text-xs">{{ moneyFormat(store.SALES_LAST_30_DAYS) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<style scoped>

.custom-scrollbar::-webkit-scrollbar {
    width: 0px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: transparent;
    border: none;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background-color: transparent;
}

</style>