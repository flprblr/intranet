<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import JetAuthenticationCard from '@/Components/AuthenticationCard.vue';
import JetAuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import JetButton from '@/Components/Button.vue';
import JetInput from '@/Components/Input.vue';
import JetCheckbox from '@/Components/Checkbox.vue';
import JetLabel from '@/Components/Label.vue';
import JetValidationErrors from '@/Components/ValidationErrors.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />
    <JetAuthenticationCard>
        <template #logo>
            <JetAuthenticationCardLogo />
        </template>
        <JetValidationErrors class="mb-4" />
        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>
        <form @submit.prevent="submit">
            <div>
                <JetLabel for="email" value="Correo Electrónico" class="uppercase text-xs" />
                <JetInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                />
            </div>
            <div class="mt-4">
                <JetLabel for="password" value="Contraseña" class="uppercase text-xs" />
                <JetInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
            </div>
            <div class="cf-turnstile my-3" data-sitekey="0x4AAAAAAABFz_PWmEJPV2TR" align="center"></div>
            <div class="grid items-center justify-center">
                <JetButton class="text-center mx-auto" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    <span>Iniciar Sesión</span>
                </JetButton>
                <Link v-if="canResetPassword" :href="route('password.request')" class="uppercase text-xs font-medium text-gray-600 hover:text-gray-900 mt-3">
                    Olvidaste tu contraseña?
                </Link>
            </div>
        </form>
    </JetAuthenticationCard>
</template>


