<script setup>
import { ref, watchEffect } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Head, Link, usePage } from '@inertiajs/inertia-vue3';
import JetApplicationMark from '@/Components/ApplicationMark.vue';
import JetBanner from '@/Components/Banner.vue';
import JetDropdown from '@/Components/Dropdown.vue';
import JetDropdownLink from '@/Components/DropdownLink.vue';
import JetNavLink from '@/Components/NavLink.vue';
import JetResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
});

const { props } = usePage()

watchEffect(() => {
    window.Laravel = window.Laravel || {}
    if (props.value && props.value.permissions) {
        window.Laravel.jsPermissions = JSON.parse(props.value.permissions);
    }
})

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    Inertia.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    Inertia.post(route('logout'));
};

</script>

<template>
    <div>

        <Head :title="title" />

        <JetBanner />

        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                <JetApplicationMark class="block h-8 w-auto" />
                                </Link>
                            </div>
                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 xl:flex">
                                <JetNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </JetNavLink>
                                <JetNavLink v-if="can('order.index')" :href="route('order.index')"
                                    :active="route().current('order.index')">
                                    E-Commerce
                                </JetNavLink>
                                <JetNavLink v-if="can('mvorder.index')" :href="route('mvorder.index')"
                                    :active="route().current('mvorder.index')">
                                    Marketplace
                                </JetNavLink>
                                <JetNavLink v-if="can('store.index')" :href="route('store.index')"
                                    :active="route().current('store.index')">
                                    Tiendas
                                </JetNavLink>
                                <JetNavLink v-if="can('station.index')" :href="route('station.index')"
                                    :active="route().current('station.index')">
                                    Cajas
                                </JetNavLink>
                                <JetNavLink v-if="can('service.index')" :href="route('service.index')"
                                    :active="route().current('service.index')">
                                    Servicio Técnico
                                </JetNavLink>
                                <JetNavLink v-if="can('customer.index')" :href="route('report-pos')"
                                    :active="route().current('report-pos')">
                                    Customer Intelligence
                                </JetNavLink>

                                <JetDropdown v-if="can('extguarantee.index') || can('assurant.index')" class="mt-4"
                                    align="left">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos"
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover"
                                                :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name">
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                                Aufbau

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <JetDropdownLink v-if="can('assurant.index')" :href="route('assurant.index')"
                                            :active="route().current('assurant.index')">
                                            Assurant
                                        </JetDropdownLink>
                                        <JetDropdownLink v-if="can('extguarantee.index')"
                                            :href="route('extguarantee.index')"
                                            :active="route().current('extguarantee.index')">
                                            Garantía Extendida
                                        </JetDropdownLink>

                                    </template>
                                </JetDropdown>
                                <JetDropdown v-if="can('sales.index')" class="mt-4" align="left">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos"
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover"
                                                :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name">
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                                Ventas

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>

                                        <JetDropdownLink :href="route('sales')">
                                            YNK
                                        </JetDropdownLink>

                                        <div class="border-t border-gray-100" />

                                        <JetDropdownLink :href="route('sales-retail')">
                                            RETAIL
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-ecommerce')">
                                            E-COMMERCE
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-marketplace')">
                                            MARKETPLACE
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-wholesale')">
                                            WHOLESALE
                                        </JetDropdownLink>

                                        <div class="border-t border-gray-100" />

                                        <JetDropdownLink :href="route('sales-belsport')">
                                            Belsport
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-bold')">
                                            Bold
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-k1')">
                                            K1
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-drops')">
                                            Drops
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-outlets')">
                                            Outlets
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-locker')">
                                            Locker
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-qsrx')">
                                            Qs Rx
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-antihuman')">
                                            Antihuman
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-saucony')">
                                            Saucony
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-aufbau')">
                                            Aufbau
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-bamers')">
                                            Bamers
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-crocs')">
                                            Crocs
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-oakley')">
                                            Oakley
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-thelab')">
                                            The Lab
                                        </JetDropdownLink>

                                        <JetDropdownLink :href="route('sales-hoka')">
                                            Hoka
                                        </JetDropdownLink>

                                    </template>
                                </JetDropdown>
                            </div>
                        </div>
                        <div class="hidden xl:flex sm:items-center sm:ml-6">
                            <div class="ml-3 relative">
                                <!-- Teams Dropdown -->
                                <JetDropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                                {{ $page.props.user.current_team.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Manage Team
                                                </div>

                                                <!-- Team Settings -->
                                                <JetDropdownLink :href="route('teams.show', $page.props.user.current_team)">
                                                    Team Settings
                                                </JetDropdownLink>

                                                <JetDropdownLink v-if="$page.props.jetstream.canCreateTeams"
                                                    :href="route('teams.create')">
                                                    Create New Team
                                                </JetDropdownLink>

                                                <div class="border-t border-gray-100" />

                                                <!-- Team Switcher -->
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Switch Teams
                                                </div>

                                                <template v-for="team in $page.props.user.all_teams" :key="team.id">
                                                    <form @submit.prevent="switchToTeam(team)">
                                                        <JetDropdownLink as="button">
                                                            <div class="flex items-center">
                                                                <svg v-if="team.id == $page.props.user.current_team_id"
                                                                    class="mr-2 h-5 w-5 text-green-400" fill="none"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                <div>{{ team.name }}</div>
                                                            </div>
                                                        </JetDropdownLink>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </JetDropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <JetDropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos"
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover"
                                                :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name">
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                                {{ $page.props.user.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="uppercase block px-4 py-2 text-xs text-gray-400">
                                            Cuenta
                                        </div>

                                        <div class="border-t border-gray-100" />

                                        <JetDropdownLink :href="route('profile.show')">
                                            Perfil
                                        </JetDropdownLink>

                                        <div v-if="is('Administrador')">

                                            <JetDropdownLink v-if="$page.props.jetstream.hasApiFeatures"
                                                :href="route('api-tokens.index')">
                                                API Tokens
                                            </JetDropdownLink>

                                            <div class="border-t border-gray-100" />
                                            <div class="uppercase block px-4 py-2 text-xs text-gray-400">
                                                Administración
                                            </div>
                                            <div class="border-t border-gray-100" />

                                            <JetDropdownLink :href="route('user.index')">
                                                Usuarios
                                            </JetDropdownLink>

                                            <JetDropdownLink :href="route('manager.index')">
                                                Supervisores
                                            </JetDropdownLink>

                                            <JetDropdownLink :href="route('role.index')">
                                                Roles
                                            </JetDropdownLink>

                                            <JetDropdownLink :href="route('permission.index')">
                                                Permisos
                                            </JetDropdownLink>
                                        </div>

                                        <div v-if="is('Administrador | Consultoría')">
                                            <div class="border-t border-gray-100" />
                                            <div class="uppercase block px-4 py-2 text-xs text-gray-400">
                                                Mantenedores
                                            </div>
                                            <div class="border-t border-gray-100" />
                                        </div>


                                        <JetDropdownLink v-if="is('Administrador')" :href="route('state.index')">
                                            Regiones
                                        </JetDropdownLink>

                                        <JetDropdownLink v-if="is('Administrador')" :href="route('city.index')">
                                            Comunas
                                        </JetDropdownLink>

                                        <JetDropdownLink v-if="is('Administrador | Consultoría')"
                                            :href="route('company.index')">
                                            Empresas
                                        </JetDropdownLink>

                                        <JetDropdownLink v-if="is('Administrador | Consultoría')"
                                            :href="route('ecommerce.index')">
                                            E-Commerce
                                        </JetDropdownLink>

                                        <JetDropdownLink v-if="is('Administrador | Consultoría')"
                                            :href="route('mvaccess.index')">
                                            Mulltivende Conexión
                                        </JetDropdownLink>

                                        <JetDropdownLink v-if="is('Administrador | Consultoría')"
                                            :href="route('mvwarehouse.index')">
                                            Mulltivende Bodegas
                                        </JetDropdownLink>
                                        <JetDropdownLink v-if="is('Administrador | Consultoría')"
                                            :href="route('mvstore.index')">
                                            Mulltivende Tiendas
                                        </JetDropdownLink>

                                        <div class="border-t border-gray-100" />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <JetDropdownLink as="button">
                                                Cerrar Sesión
                                            </JetDropdownLink>
                                        </form>
                                    </template>
                                </JetDropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center xl:hidden">
                            <button
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition"
                                @click="showingNavigationDropdown = !showingNavigationDropdown">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path
                                        :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                    class="lg:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <JetResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </JetResponsiveNavLink>

                        <JetResponsiveNavLink v-if="can('store.index')" :href="route('store.index')"
                            :active="route().current('store.index')">
                            Tiendas
                        </JetResponsiveNavLink>
                        <JetResponsiveNavLink v-if="can('station.index')" :href="route('station.index')"
                            :active="route().current('station.index')">
                            Cajas
                        </JetResponsiveNavLink>
                        <JetResponsiveNavLink v-if="can('service.index')" :href="route('service.index')"
                            :active="route().current('service.index')">
                            Servicio Técnico
                        </JetResponsiveNavLink>
                        <JetResponsiveNavLink v-if="can('assurant.index')" :href="route('assurant.index')"
                            :active="route().current('assurant.index')">
                            Assurant
                        </JetResponsiveNavLink>
                        <JetResponsiveNavLink v-if="can('customer.index')" :href="route('report-pos')"
                            :active="route().current('report-pos')">
                            Customer Intelligence
                        </JetResponsiveNavLink>

                        <JetResponsiveNavLink v-if="can('extguarantee.index')" :href="route('extguarantee.index')"
                            :active="route().current('extguarantee.index')">
                            Garantía Extendida
                        </JetResponsiveNavLink>

                        <JetResponsiveNavLink v-if="can('sales.index')" :href="route('sales')"
                            :active="route().current('sales')">
                            Ventas
                        </JetResponsiveNavLink>
                    </div>
                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
                                <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.user.profile_photo_url"
                                    :alt="$page.props.user.name">
                            </div>

                            <div>
                                <div class="font-medium text-base text-gray-800">
                                    {{ $page.props.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <JetResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                Perfil
                            </JetResponsiveNavLink>

                            <JetResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures"
                                :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                                API Tokens
                            </JetResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <JetResponsiveNavLink as="button">
                                    Cerrar Sesión
                                </JetResponsiveNavLink>
                            </form>

                            <!-- Team Management -->
                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                <div class="border-t border-gray-200" />

                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Manage Team
                                </div>

                                <!-- Team Settings -->
                                <JetResponsiveNavLink :href="route('teams.show', $page.props.user.current_team)"
                                    :active="route().current('teams.show')">
                                    Team Settings
                                </JetResponsiveNavLink>

                                <JetResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams"
                                    :href="route('teams.create')" :active="route().current('teams.create')">
                                    Create New Team
                                </JetResponsiveNavLink>

                                <div class="border-t border-gray-200" />

                                <!-- Team Switcher -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Switch Teams
                                </div>

                                <template v-for="team in $page.props.user.all_teams" :key="team.id">
                                    <form @submit.prevent="switchToTeam(team)">
                                        <JetResponsiveNavLink as="button">
                                            <div class="flex items-center">
                                                <svg v-if="team.id == $page.props.user.current_team_id"
                                                    class="mr-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <div>{{ team.name }}</div>
                                            </div>
                                        </JetResponsiveNavLink>
                                    </form>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>


