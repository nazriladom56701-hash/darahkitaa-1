<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLogo from '@/components/AppLogo.vue';
import {
    Droplet,
    Droplets,
    ShieldCheck,
    Users,
    Calendar,
    Building2,
    FileText,
    ArrowRight,
    Heart,
    Activity,
    Lock,
} from '@lucide/vue';
</script>

<template>
    <Head title="DarahKitaa - Sistem Admin Donor Darah PMI" />

    <div
        class="flex min-h-screen flex-col justify-between bg-zinc-950 text-zinc-100 selection:bg-red-500 selection:text-white"
    >
        <!-- Top Navbar -->
        <header
            class="sticky top-0 z-50 border-b border-zinc-800/80 bg-zinc-900/60 backdrop-blur"
        >
            <div
                class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-9 items-center justify-center rounded-xl bg-red-600 text-white shadow-lg shadow-red-600/30"
                    >
                        <Droplets class="size-5" />
                    </div>
                    <div>
                        <span
                            class="text-lg font-extrabold tracking-tight text-white"
                            >Darah<span class="text-red-500">Kitaa</span></span
                        >
                        <span
                            class="ml-2 hidden border-l border-zinc-700 pl-2 font-mono text-[11px] text-zinc-400 sm:inline-block"
                            >Sistem Admin Donor PMI</span
                        >
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        href="/dashboard"
                        class="flex items-center gap-1.5 rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-red-600/20 transition hover:bg-red-700"
                    >
                        <span>Buka Dashboard</span>
                        <ArrowRight class="size-3.5" />
                    </Link>
                    <template v-else>
                        <Link
                            href="/login"
                            class="flex items-center gap-1.5 rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-red-600/20 transition hover:bg-red-700"
                        >
                            <Lock class="size-3.5" />
                            <span>Login Admin</span>
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Main Hero Section -->
        <main
            class="mx-auto flex max-w-7xl flex-1 flex-col justify-center px-4 py-12 sm:px-6 md:py-16 lg:px-8"
        >
            <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-12">
                <!-- Left text -->
                <div class="space-y-6 text-center lg:col-span-7 lg:text-left">
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-red-500/20 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-400"
                    >
                        <Activity class="size-3.5" />
                        <span
                            >Platform Terintegrasi Unit Transfusi Darah
                            (PMI)</span
                        >
                    </div>

                    <h1
                        class="text-3xl leading-tight font-black tracking-tight text-white sm:text-5xl"
                    >
                        Sistem Informasi Manajemen Donor Darah
                        <span class="text-red-500">DarahKitaa</span>
                    </h1>

                    <p
                        class="mx-auto max-w-xl text-sm leading-relaxed text-zinc-400 sm:text-base lg:mx-0"
                    >
                        Solusi digital pengelolaan data donor, penjadwalan
                        event, pemantauan stok kantong darah per golongan, serta
                        verifikasi dan alokasi permohonan pasokan darah rumah
                        sakit secara cepat dan aman.
                    </p>

                    <!-- CTA Buttons -->
                    <div
                        class="flex flex-col items-center justify-center gap-3 pt-2 sm:flex-row lg:justify-start"
                    >
                        <Link
                            href="/login"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-red-600/30 transition hover:bg-red-700 sm:w-auto"
                        >
                            <ShieldCheck class="size-4" />
                            <span>Masuk ke Web Admin</span>
                        </Link>

                        <a
                            href="#alur"
                            class="flex w-full items-center justify-center gap-2 rounded-xl border border-zinc-800 bg-zinc-900 px-6 py-3.5 text-sm font-semibold text-zinc-300 transition hover:bg-zinc-800 sm:w-auto"
                        >
                            <span>Pelajari Alur Sistem &rarr;</span>
                        </a>
                    </div>

                    <!-- Flow Feature Badges -->
                    <div
                        class="grid grid-cols-2 gap-3 pt-6 text-left sm:grid-cols-4"
                    >
                        <div
                            class="rounded-xl border border-zinc-800/80 bg-zinc-900/60 p-3"
                        >
                            <Users class="mb-1 size-4 text-amber-500" />
                            <div class="text-xs font-bold text-zinc-200">
                                Data Donor
                            </div>
                            <div class="text-[10px] text-zinc-400">
                                Profil & Verifikasi
                            </div>
                        </div>

                        <div
                            class="rounded-xl border border-zinc-800/80 bg-zinc-900/60 p-3"
                        >
                            <Calendar class="mb-1 size-4 text-teal-400" />
                            <div class="text-xs font-bold text-zinc-200">
                                Jadwal Donor
                            </div>
                            <div class="text-[10px] text-zinc-400">
                                Event & Absensi
                            </div>
                        </div>

                        <div
                            class="rounded-xl border border-zinc-800/80 bg-zinc-900/60 p-3"
                        >
                            <Droplet class="mb-1 size-4 text-red-500" />
                            <div class="text-xs font-bold text-zinc-200">
                                Stok Darah
                            </div>
                            <div class="text-[10px] text-zinc-400">
                                8 Gol. & Update Stok
                            </div>
                        </div>

                        <div
                            class="rounded-xl border border-zinc-800/80 bg-zinc-900/60 p-3"
                        >
                            <Building2 class="mb-1 size-4 text-blue-400" />
                            <div class="text-xs font-bold text-zinc-200">
                                Permintaan RS
                            </div>
                            <div class="text-[10px] text-zinc-400">
                                Setujui & Tolak
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right visual: Mini Dashboard Flow Mockup -->
                <div class="lg:col-span-5" id="alur">
                    <div
                        class="relative space-y-4 overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900/90 p-5 shadow-2xl"
                    >
                        <div
                            class="flex items-center justify-between border-b border-zinc-800 pb-3"
                        >
                            <div class="flex items-center gap-2">
                                <span
                                    class="size-2 animate-ping rounded-full bg-emerald-500"
                                ></span>
                                <span class="text-xs font-bold text-zinc-200"
                                    >Alur Web Admin DarahKitaa</span
                                >
                            </div>
                            <span
                                class="rounded bg-zinc-800 px-2 py-0.5 font-mono text-[10px] text-zinc-400"
                                >PMI UDD Portal</span
                            >
                        </div>

                        <!-- Step Cards from user flow -->
                        <div class="space-y-2.5 text-xs">
                            <div
                                class="flex items-center justify-between rounded-xl border border-zinc-700 bg-zinc-800 p-3"
                            >
                                <div
                                    class="flex items-center gap-2 font-semibold text-zinc-200"
                                >
                                    <span
                                        class="size-2 rounded-full bg-zinc-400"
                                    ></span>
                                    <span>Login Admin & Dashboard</span>
                                </div>
                                <span class="text-[10px] text-zinc-400"
                                    >Umum</span
                                >
                            </div>

                            <div
                                class="flex items-center justify-between rounded-xl border border-[#6E2C1E] bg-[#4A1E14]/80 p-3"
                            >
                                <div
                                    class="flex items-center gap-2 font-semibold text-amber-100"
                                >
                                    <span
                                        class="size-2 rounded-full bg-amber-400"
                                    ></span>
                                    <span
                                        >Data Donor &rarr; Aksi:
                                        Verifikasi</span
                                    >
                                </div>
                                <span
                                    class="rounded border border-teal-800 bg-teal-950/80 px-2 py-0.5 text-[10px] text-teal-300"
                                    >Aksi Admin</span
                                >
                            </div>

                            <div
                                class="flex items-center justify-between rounded-xl border border-[#6E2C1E] bg-[#4A1E14]/80 p-3"
                            >
                                <div
                                    class="flex items-center gap-2 font-semibold text-amber-100"
                                >
                                    <span
                                        class="size-2 rounded-full bg-amber-400"
                                    ></span>
                                    <span
                                        >Jadwal Donor &rarr; Aksi: Absensi</span
                                    >
                                </div>
                                <span
                                    class="rounded border border-teal-800 bg-teal-950/80 px-2 py-0.5 text-[10px] text-teal-300"
                                    >Aksi Admin</span
                                >
                            </div>

                            <div
                                class="flex items-center justify-between rounded-xl border border-[#6E2C1E] bg-[#4A1E14]/80 p-3"
                            >
                                <div
                                    class="flex items-center gap-2 font-semibold text-amber-100"
                                >
                                    <span
                                        class="size-2 rounded-full bg-amber-400"
                                    ></span>
                                    <span
                                        >Stok Darah &rarr; Aksi: Update
                                        Stok</span
                                    >
                                </div>
                                <span
                                    class="rounded border border-teal-800 bg-teal-950/80 px-2 py-0.5 text-[10px] text-teal-300"
                                    >Aksi Admin</span
                                >
                            </div>

                            <div
                                class="flex items-center justify-between rounded-xl border border-[#6E2C1E] bg-[#4A1E14]/80 p-3"
                            >
                                <div
                                    class="flex items-center gap-2 font-semibold text-amber-100"
                                >
                                    <span
                                        class="size-2 rounded-full bg-amber-400"
                                    ></span>
                                    <span
                                        >Permintaan RS &rarr; Aksi:
                                        Setujui/Tolak</span
                                    >
                                </div>
                                <span
                                    class="rounded border border-teal-800 bg-teal-950/80 px-2 py-0.5 text-[10px] text-teal-300"
                                    >Aksi Admin</span
                                >
                            </div>

                            <div
                                class="flex items-center justify-between rounded-xl border border-zinc-700 bg-zinc-800 p-3"
                            >
                                <div
                                    class="flex items-center gap-2 font-semibold text-zinc-200"
                                >
                                    <span
                                        class="size-2 rounded-full bg-blue-400"
                                    ></span>
                                    <span
                                        >Laporan & Notifikasi (Ekspor,
                                        Pengingat)</span
                                    >
                                </div>
                                <span class="text-[10px] text-blue-300"
                                    >Ekspor CSV</span
                                >
                            </div>
                        </div>

                        <div class="pt-2 text-center">
                            <Link
                                href="/login"
                                class="block w-full rounded-xl border border-red-500/30 bg-red-600/20 py-2 text-xs font-semibold text-red-400 transition hover:bg-red-600/30"
                            >
                                Buka Panel Admin DarahKitaa &rarr;
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer
            class="border-t border-zinc-800/80 bg-zinc-900/40 py-6 text-center text-xs text-zinc-500"
        >
            <div
                class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 sm:flex-row"
            >
                <div class="flex items-center gap-2">
                    <Droplet class="size-4 text-red-500" />
                    <span class="font-semibold text-zinc-400">DarahKitaa</span>
                    <span>• Palang Merah Indonesia</span>
                </div>
                <div>
                    Unit Transfusi Darah (UDD) & Pelayanan Kemanusiaan Terpadu
                </div>
            </div>
        </footer>
    </div>
</template>
