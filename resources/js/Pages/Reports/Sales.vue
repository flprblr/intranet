<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import FullDate from "@/Components/FullDate.vue";
import SalesChartMonthsBanner from "@/Components/SalesChartMonthsBanner.vue";
import SalesChartDaysBanner from "@/Components/SalesChartDaysBanner.vue";
import SalesCardBanner from "@/Components/SalesCardBanner.vue";

</script>
<template>
    <AppLayout title="Reporte de Ventas">
        <div class="text-center pt-5">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-3xl">Reporte de Ventas</h2>
            <FullDate></FullDate>
        </div>
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 px-5 pt-5">
            <SalesCardBanner title="YNK: General" society="ALL" salesOrg="ALL" channel="ALL"/>
            <div class="max-w pt-2 bg-white rounded-lg shadow-md">
                <div class="h-4/6 px-5">
                    <SalesChartDaysBanner title="YNK: Análisis por Día" society="ALL" salesOrg="ALL" channel="ALL"></SalesChartDaysBanner>
                </div>
            </div>
            <div class="max-w py-2 bg-white rounded-lg shadow-md">
                <div class="h-4/6 px-5">
                    <SalesChartMonthsBanner title="YNK: Análisis por Mes" society="ALL" salesOrg="ALL" channel="ALL"></SalesChartMonthsBanner>
                </div>
            </div>
        </div>

        <hr class="h-px mt-5 bg-gray-200 border-0 dark:bg-gray-70 mx-6">

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 px-5 pt-5">
            <SalesCardBanner title="YNK: Retail" society="ALL" salesOrg="ALL" channel="01" url="retail"/>
            <div class="max-w pt-2 bg-white rounded-lg shadow-md">
                <div class="h-4/6 px-5">
                    <SalesChartDaysBanner title="Retail: Análisis por Día" society="ALL" salesOrg="ALL" channel="01"></SalesChartDaysBanner>
                </div>
            </div>
            <div class="max-w py-2 bg-white rounded-lg shadow-md">
                <div class="h-4/6 px-5">
                    <SalesChartMonthsBanner title="Retail: Análisis por Mes" society="ALL" salesOrg="ALL" channel="01"></SalesChartMonthsBanner>
                </div>
            </div>
        </div>

        <hr class="h-px mt-5 bg-gray-200 border-0 dark:bg-gray-70 mx-6">

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 px-5 pt-5">
            <SalesCardBanner title="YNK: E-Commerce" society="ALL" salesOrg="ALL" channel="02" url="ecommerce"/>
            <div class="max-w pt-2 bg-white rounded-lg shadow-md">
                <div class="h-4/6 px-5">
                    <SalesChartDaysBanner title="E-Commerce: Análisis por Día" society="ALL" salesOrg="ALL" channel="02"></SalesChartDaysBanner>
                </div>
            </div>
            <div class="max-w py-2 bg-white rounded-lg shadow-md">
                <div class="h-4/6 px-5">
                    <SalesChartMonthsBanner title="E-Commerce: Análisis por Mes" society="ALL" salesOrg="ALL" channel="02"></SalesChartMonthsBanner>
                </div>
            </div>
        </div>

        <hr class="h-px mt-5 bg-gray-200 border-0 dark:bg-gray-70 mx-6">

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 px-5 pt-5">
            <SalesCardBanner title="YNK: Marketplace" society="ALL" salesOrg="ALL" channel="05" url="marketplace"/>
            <div class="max-w pt-2 bg-white rounded-lg shadow-md">
                <div class="h-4/6 px-5">
                    <SalesChartDaysBanner title="Marketplace: Análisis por Día" society="ALL" salesOrg="ALL" channel="05"></SalesChartDaysBanner>
                </div>
            </div>
            <div class="max-w py-2 bg-white rounded-lg shadow-md">
                <div class="h-4/6 px-5">
                    <SalesChartMonthsBanner title="Marketplace: Análisis por Mes" society="ALL" salesOrg="ALL" channel="05"></SalesChartMonthsBanner>
                </div>
            </div>
        </div>

        <hr class="h-px mt-5 bg-gray-200 border-0 dark:bg-gray-70 mx-6">

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 px-5 pt-5">
            <SalesCardBanner title="YNK: Wholesale" society="ALL" salesOrg="ALL" channel="03" url="wholesale"/>
            <div class="max-w pt-2 bg-white rounded-lg shadow-md">  
                <div class="h-4/6 px-5">
                    <SalesChartDaysBanner title="Wholesale: Análisis por Día" society="ALL" salesOrg="ALL" channel="03"></SalesChartDaysBanner>
                </div>
            </div>
            <div class="max-w py-2 bg-white rounded-lg shadow-md">
                <div class="h-4/6 px-5">
                    <SalesChartMonthsBanner title="Wholesale: Análisis por Mes" society="ALL" salesOrg="ALL" channel="03"></SalesChartMonthsBanner>
                </div>
            </div>
        </div>

        <hr class="h-px my-5 bg-gray-200 border-0 dark:bg-gray-70 mx-6">

        <div class="pb-5 px-5 grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 gap-5">
            <SalesCardBanner title="Belsport" society="BELS" salesOrg="1000" channel="ALL" url="belsport" />
            <SalesCardBanner title="Bold" society="BELS" salesOrg="2000" channel="ALL" url="bold" />
            <SalesCardBanner title="K1" society="BELS" salesOrg="3000" channel="ALL" url="k1" />
            <SalesCardBanner title="Drops" society="BELS" salesOrg="5000" channel="ALL" url="drops" />
            <SalesCardBanner title="Outlets" society="BELS" salesOrg="6000" channel="ALL" url="outlets" />
            <SalesCardBanner title="Locker" society="BELS" salesOrg="6100" channel="ALL" url="locker" />
            <SalesCardBanner title="Qs Rx" society="BELS" salesOrg="7000" channel="ALL" url="qsrx" />
            <SalesCardBanner title="Antihuman" society="BELS" salesOrg="7300" channel="ALL" url="antihuman" />
            <SalesCardBanner title="Saucony" society="BELS" salesOrg="7400" channel="ALL" url="saucony" />
            <SalesCardBanner title="Aufbau" society="BELS" salesOrg="8000" channel="ALL" url="aufbau" />
            <SalesCardBanner title="Bamers" society="BAME" salesOrg="B000" channel="ALL" url="bamers" />
            <SalesCardBanner title="Crocs" society="CROC" salesOrg="C000" channel="ALL" url="crocs" />
            <SalesCardBanner title="Oakley" society="OAK1" salesOrg="D000" channel="ALL" url="oakley" />
            <SalesCardBanner title="The Lab" society="OAK1" salesOrg="E000" channel="ALL" url="thelab" />
            <SalesCardBanner title="Hoka" society="BELS" salesOrg="7500" channel="ALL" url="hoka" />
        </div>
    </AppLayout>
</template>

