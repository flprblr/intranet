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
            <SalesCardBanner title="YNK: E-Commerce" society="ALL" salesOrg="ALL" channel="02" url="E-Commerce"/>
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

        <hr class="h-px my-5 bg-gray-200 border-0 dark:bg-gray-70 mx-6">

        <div class="pb-5 px-5 grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 gap-5">
            <SalesCardBanner title="Belsport" society="BELS" salesOrg="1000" channel="02" url="belsport" />
            <SalesCardBanner title="Bold" society="BELS" salesOrg="2000" channel="02" url="bold" />
            <SalesCardBanner title="K1" society="BELS" salesOrg="3000" channel="02" url="k1" />
            <SalesCardBanner title="Drops" society="BELS" salesOrg="5000" channel="02" url="drops" />
            <SalesCardBanner title="Outlets" society="BELS" salesOrg="6000" channel="02" url="outlets" />
            <SalesCardBanner title="Locker" society="BELS" salesOrg="6100" channel="02" url="locker" />
            <SalesCardBanner title="Qs Rx" society="BELS" salesOrg="7000" channel="02" url="qsrx" />
            <SalesCardBanner title="Antihuman" society="BELS" salesOrg="7300" channel="02" url="antihuman" />
            <SalesCardBanner title="Saucony" society="BELS" salesOrg="7400" channel="02" url="saucony" />
            <SalesCardBanner title="Aufbau" society="BELS" salesOrg="8000" channel="02" url="aufbau" />
            <SalesCardBanner title="Bamers" society="BAME" salesOrg="B000" channel="02" url="bamers" />
            <SalesCardBanner title="Crocs" society="CROC" salesOrg="C000" channel="02" url="crocs" />
            <SalesCardBanner title="Oakley" society="OAK1" salesOrg="D000" channel="02" url="oakley" />
            <SalesCardBanner title="The Lab" society="OAK1" salesOrg="E000" channel="02" url="thelab" />
            <SalesCardBanner title="Hoka" society="BELS" salesOrg="7500" channel="02" url="hoka" />
        </div>
    </AppLayout>
</template>

