<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import FullDate from "@/Components/FullDate.vue";
import SalesRanking from "@/Components/SalesRanking.vue";
import SalesChartMonthsBanner from "@/Components/SalesChartMonthsBanner.vue";
import SalesChartDaysBanner from "@/Components/SalesChartDaysBanner.vue";
import SalesCardBanner from "@/Components/SalesCardBanner.vue";

</script>
<template>
    <AppLayout title="Reporte de Ventas">

        <div class="text-center pt-5">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Reporte de Ventas</h1>
            <FullDate></FullDate>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-1 gap-5 px-5 pt-5">
            <SalesCardBanner title="Drops: General" society="BELS" salesOrg="5000" channel="ALL" />
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 px-5 pt-5">
            <SalesCardBanner title="Drops: Retail" society="BELS" salesOrg="5000" channel="01" />
            <div class="max-w pt-2 bg-white rounded-lg shadow-md">
                <div class="h-4/6 px-5">
                    <SalesChartDaysBanner title="Retail: Análisis por Día" society="BELS" salesOrg="5000" channel="01"></SalesChartDaysBanner>
                </div>
            </div>
            <div class="max-w py-2 bg-white rounded-lg shadow-md">
                <div class="h-4/6 px-5">
                    <SalesChartMonthsBanner title="Retail: Análisis por Mes" society="BELS" salesOrg="5000" channel="01"></SalesChartMonthsBanner>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 px-5 pt-5">
            <SalesCardBanner title="Drops: E-Commerce" society="BELS" salesOrg="5000" channel="02"/>
            <div class="max-w pt-2 bg-white rounded-lg shadow-md">
                <div class="h-4/6 px-5">
                    <SalesChartDaysBanner title="E-Commerce: Análisis por Día" society="BELS" salesOrg="5000" channel="02"></SalesChartDaysBanner>
                </div>
            </div>
            <div class="max-w py-2 bg-white rounded-lg shadow-md">
                <div class="h-4/6 px-5">
                    <SalesChartMonthsBanner title="E-Commerce: Análisis por Mes" society="BELS" salesOrg="5000" channel="02"></SalesChartMonthsBanner>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 px-5 pt-5">
            <SalesCardBanner title="Drops: Marketplace" society="BELS" salesOrg="5000" channel="05" />
            <div class="max-w pt-2 bg-white rounded-lg shadow-md">
                <div class="h-4/6 px-5">
                    <SalesChartDaysBanner title="Marketplace: Análisis por Día" society="BELS" salesOrg="5000" channel="05"></SalesChartDaysBanner>
                </div>
            </div>
            <div class="max-w py-2 bg-white rounded-lg shadow-md">
                <div class="h-4/6 px-5">
                    <SalesChartMonthsBanner title="Marketplace: Análisis por Mes" society="BELS" salesOrg="5000" channel="05"></SalesChartMonthsBanner>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 px-5 py-5">
            <SalesCardBanner title="Drops: Wholesale" society="BELS" salesOrg="5000" channel="03" />
            <div class="max-w pt-2 bg-white rounded-lg shadow-md">  
                <div class="h-4/6 px-5">
                    <SalesChartDaysBanner title="Wholesale: Análisis por Día" society="BELS" salesOrg="5000" channel="03"></SalesChartDaysBanner>
                </div>
            </div>
            <div class="max-w py-2 bg-white rounded-lg shadow-md">
                <div class="h-4/6 px-5">
                    <SalesChartMonthsBanner title="Wholesale: Análisis por Mes" society="BELS" salesOrg="5000" channel="03"></SalesChartMonthsBanner>
                </div>
            </div>
        </div>
        
    </AppLayout>
</template>

