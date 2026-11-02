<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { request } from '@/routes/password';
import { Droplet, ShieldCheck, UserCheck } from '@lucide/vue';

defineOptions({
    layout: {
        title: 'Login Admin DarahKitaa',
        description: 'Sistem Manajemen Donor Darah PMI & Rumah Sakit',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: 'admin@darahkita.id',
    password: 'password',
    remember: true,
});

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
}

function fillCredentials(role: 'admin' | 'staff') {
    if (role === 'admin') {
        form.email = 'admin@darahkita.id';
        form.password = 'password';
    } else {
        form.email = 'petugas@darahkita.id';
        form.password = 'password';
    }
}
</script>

<template>
    <Head title="Login Admin - DarahKitaa" />

    <div
        v-if="status"
        class="mb-4 rounded-lg border border-emerald-500/20 bg-emerald-500/10 p-2.5 text-center text-sm font-medium text-emerald-600 dark:text-emerald-400"
    >
        {{ status }}
    </div>

    <!-- Demo Accounts Pill -->
    <div
        class="mb-4 rounded-xl border border-red-500/20 bg-red-500/5 p-3 dark:bg-red-950/20"
    >
        <div
            class="mb-2 flex items-center gap-1.5 text-xs font-semibold text-red-600 dark:text-red-400"
        >
            <ShieldCheck class="size-4" />
            <span>Akun Demo Cepat (Klik untuk Isi):</span>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <button
                type="button"
                @click="fillCredentials('admin')"
                class="flex flex-col rounded-lg border border-zinc-200 bg-white p-2 text-left text-xs transition hover:border-red-500/50 hover:bg-red-500/5 dark:border-zinc-800 dark:bg-zinc-900"
            >
                <span
                    class="flex items-center gap-1 font-medium text-zinc-900 dark:text-zinc-100"
                >
                    <Droplet class="size-3 text-red-500" /> Admin PMI
                </span>
                <span class="truncate text-[10px] text-muted-foreground"
                    >admin@darahkita.id</span
                >
            </button>

            <button
                type="button"
                @click="fillCredentials('staff')"
                class="flex flex-col rounded-lg border border-zinc-200 bg-white p-2 text-left text-xs transition hover:border-red-500/50 hover:bg-red-500/5 dark:border-zinc-800 dark:bg-zinc-900"
            >
                <span
                    class="flex items-center gap-1 font-medium text-zinc-900 dark:text-zinc-100"
                >
                    <UserCheck class="size-3 text-emerald-500" /> Petugas
                    Lapangan
                </span>
                <span class="truncate text-[10px] text-muted-foreground"
                    >petugas@darahkita.id</span
                >
            </button>
        </div>
    </div>

    <form @submit.prevent="submit" class="flex flex-col gap-5">
        <div class="grid gap-4">
            <div class="grid gap-2">
                <Label for="email">Email Admin / Petugas</Label>
                <Input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    v-focus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="admin@darahkita.id"
                />
                <InputError :message="form.errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password">Kata Sandi</Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-xs"
                        :tabindex="5"
                    >
                        Lupa sandi?
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    v-model="form.password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Kata Sandi"
                />
                <InputError :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <Label
                    for="remember"
                    class="flex items-center space-x-2 text-sm"
                >
                    <Checkbox
                        id="remember"
                        v-model:checked="form.remember"
                        :tabindex="3"
                    />
                    <span>Ingat sesi masuk</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-2 w-full bg-red-600 font-medium text-white hover:bg-red-700"
                :tabindex="4"
                :disabled="form.processing"
            >
                <Spinner v-if="form.processing" />
                <span v-else>Masuk ke Web Admin</span>
            </Button>
        </div>

        <div class="text-center text-xs text-muted-foreground">
            Belum punya akun admin? Hubungi koordinator UDD PMI atau
            <TextLink :href="register()" :tabindex="5"
                >Daftar Akun Baru</TextLink
            >
        </div>
    </form>
</template>
