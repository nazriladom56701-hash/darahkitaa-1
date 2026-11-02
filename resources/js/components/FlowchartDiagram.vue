<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    LogIn,
    LayoutDashboard,
    Users,
    Calendar,
    Droplets,
    Building2,
    FileText,
    CheckCircle2,
    UserCheck,
    RefreshCw,
    CheckCheck,
    ArrowDown,
} from '@lucide/vue';

defineProps<{
    stats?: {
        total_stock_units: number;
        total_donors: number;
        pending_verification_donors: number;
        active_events_count: number;
        pending_requests_count: number;
        emergency_requests_count: number;
    };
}>();
</script>

<template>
    <div
        class="rounded-2xl border border-zinc-800 bg-zinc-950/90 p-5 text-zinc-100 shadow-xl md:p-6"
    >
        <!-- Header -->
        <div
            class="flex flex-col justify-between gap-3 border-b border-zinc-800/80 pb-5 sm:flex-row sm:items-center"
        >
            <div>
                <div class="flex items-center gap-2">
                    <span
                        class="inline-block size-2.5 animate-pulse rounded-full bg-red-500"
                    ></span>
                    <h3
                        class="text-base font-bold tracking-tight text-white md:text-lg"
                    >
                        Alur Sistem DarahKitaa (Flow Diagram)
                    </h3>
                </div>
                <p class="mt-0.5 text-xs text-zinc-400">
                    Representasi alur kerja web admin: klik modul untuk menuju
                    halaman dan menjalankan aksi admin
                </p>
            </div>

            <!-- Legend matching the user's diagram -->
            <div class="flex flex-wrap items-center gap-4 text-xs">
                <div class="flex items-center gap-1.5">
                    <span
                        class="size-3 rounded border border-zinc-500 bg-zinc-600"
                    ></span>
                    <span class="text-zinc-300">Umum</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span
                        class="size-3 rounded border border-amber-700 bg-amber-900"
                    ></span>
                    <span class="text-zinc-300">Modul utama</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span
                        class="size-3 rounded border border-teal-600 bg-teal-800"
                    ></span>
                    <span class="text-zinc-300">Aksi admin</span>
                </div>
            </div>
        </div>

        <!-- Interactive Diagram Layout -->
        <div class="flex flex-col items-center pt-6">
            <!-- Step 1: Login Admin (Umum) -->
            <div class="flex flex-col items-center">
                <div
                    class="w-64 rounded-xl border border-zinc-700 bg-zinc-800/90 px-4 py-2.5 text-center shadow-md transition hover:border-zinc-500"
                >
                    <div
                        class="flex items-center justify-center gap-2 text-sm font-semibold text-zinc-200"
                    >
                        <LogIn class="size-4 text-zinc-400" />
                        <span>Login admin</span>
                    </div>
                    <p class="mt-0.5 text-[11px] text-zinc-400">
                        Autentikasi & hak akses UDD PMI
                    </p>
                </div>

                <!-- Down connector line -->
                <div class="h-6 w-0.5 bg-zinc-700"></div>
                <ArrowDown class="-mt-1.5 size-3 text-zinc-500" />
            </div>

            <!-- Step 2: Dashboard (Umum) -->
            <div class="flex w-full max-w-sm flex-col items-center">
                <Link
                    href="/dashboard"
                    class="group w-full rounded-xl border border-zinc-700 bg-zinc-800/90 px-5 py-3 text-center shadow-lg transition hover:border-red-500/60"
                >
                    <div
                        class="flex items-center justify-center gap-2 text-sm font-bold text-white transition group-hover:text-red-400"
                    >
                        <LayoutDashboard class="size-4 text-red-500" />
                        <span>Dashboard</span>
                    </div>
                    <p class="mt-1 text-xs text-zinc-300">
                        Ringkasan stok dan aktivitas
                    </p>
                    <div
                        v-if="stats"
                        class="mt-2 inline-flex items-center gap-2 rounded-full border border-zinc-800 bg-zinc-900/80 px-2.5 py-0.5 text-[11px] text-zinc-400"
                    >
                        <span>{{ stats.total_stock_units }} Kantong Darah</span>
                        <span>•</span>
                        <span>{{ stats.total_donors }} Donor</span>
                    </div>
                </Link>

                <!-- Stem down to horizontal branch -->
                <div class="h-6 w-0.5 bg-zinc-700"></div>
            </div>

            <!-- Horizontal Trunk Line -->
            <div class="relative hidden w-full max-w-4xl md:block">
                <div class="mx-auto h-0.5 w-[84%] bg-zinc-700"></div>
            </div>

            <!-- 4 Branches: Modul Utama & Aksi Admin -->
            <div
                class="mt-3 grid w-full max-w-4xl grid-cols-1 gap-4 sm:grid-cols-2 md:mt-0 md:grid-cols-4"
            >
                <!-- Branch 1: Data Donor -> Verifikasi -->
                <div class="flex flex-col items-center">
                    <!-- Stem from horizontal trunk -->
                    <div class="hidden h-4 w-0.5 bg-zinc-700 md:block"></div>
                    <ArrowDown
                        class="-mt-1 hidden size-3 text-zinc-500 md:block"
                    />

                    <!-- Modul Utama Card -->
                    <Link
                        href="/donors"
                        class="group flex min-h-[92px] w-full flex-col justify-between rounded-xl border border-[#6E2C1E] bg-[#4A1E14] p-3.5 text-center shadow-md transition hover:border-amber-500/60"
                    >
                        <div
                            class="flex items-center justify-center gap-1.5 text-sm font-bold text-amber-100 group-hover:text-white"
                        >
                            <Users class="size-4 text-amber-400" />
                            <span>Data donor</span>
                        </div>
                        <p class="mt-1 text-xs text-amber-200/80">
                            Profil, riwayat
                        </p>
                        <div
                            v-if="stats"
                            class="mt-1.5 text-[10px] text-amber-300/70"
                        >
                            {{ stats.total_donors }} Donor Terdaftar
                        </div>
                    </Link>

                    <!-- Down arrow to action -->
                    <div class="h-4 w-0.5 bg-zinc-700"></div>
                    <ArrowDown class="-mt-1 size-3 text-zinc-500" />

                    <!-- Aksi Admin Card -->
                    <Link
                        href="/donors?status=pending"
                        class="group w-full rounded-xl border border-[#175C53] bg-[#0F3A34] px-3 py-2.5 text-center shadow-md transition hover:border-teal-400/80 hover:bg-[#144740]"
                    >
                        <div
                            class="flex items-center justify-center gap-1.5 text-xs font-bold text-teal-200 group-hover:text-white"
                        >
                            <CheckCircle2 class="size-3.5 text-teal-400" />
                            <span>Verifikasi</span>
                        </div>
                        <span
                            v-if="
                                stats && stats.pending_verification_donors > 0
                            "
                            class="mt-1 inline-block rounded-full border border-amber-500/30 bg-amber-500/20 px-2 py-0.5 text-[10px] font-semibold text-amber-300"
                        >
                            {{ stats.pending_verification_donors }} Perlu
                            Ditinjau
                        </span>
                        <span
                            v-else
                            class="mt-0.5 block text-[10px] text-teal-300/70"
                        >
                            Kelayakan & Skrining
                        </span>
                    </Link>
                </div>

                <!-- Branch 2: Jadwal Donor -> Absensi donor -->
                <div class="flex flex-col items-center">
                    <div class="hidden h-4 w-0.5 bg-zinc-700 md:block"></div>
                    <ArrowDown
                        class="-mt-1 hidden size-3 text-zinc-500 md:block"
                    />

                    <!-- Modul Utama Card -->
                    <Link
                        href="/events"
                        class="group flex min-h-[92px] w-full flex-col justify-between rounded-xl border border-[#6E2C1E] bg-[#4A1E14] p-3.5 text-center shadow-md transition hover:border-amber-500/60"
                    >
                        <div
                            class="flex items-center justify-center gap-1.5 text-sm font-bold text-amber-100 group-hover:text-white"
                        >
                            <Calendar class="size-4 text-amber-400" />
                            <span>Jadwal donor</span>
                        </div>
                        <p class="mt-1 text-xs text-amber-200/80">
                            Event, kuota
                        </p>
                        <div
                            v-if="stats"
                            class="mt-1.5 text-[10px] text-amber-300/70"
                        >
                            {{ stats.active_events_count }} Event Aktif
                        </div>
                    </Link>

                    <div class="h-4 w-0.5 bg-zinc-700"></div>
                    <ArrowDown class="-mt-1 size-3 text-zinc-500" />

                    <!-- Aksi Admin Card -->
                    <Link
                        href="/events"
                        class="group w-full rounded-xl border border-[#175C53] bg-[#0F3A34] px-3 py-2.5 text-center shadow-md transition hover:border-teal-400/80 hover:bg-[#144740]"
                    >
                        <div
                            class="flex items-center justify-center gap-1.5 text-xs font-bold text-teal-200 group-hover:text-white"
                        >
                            <UserCheck class="size-3.5 text-teal-400" />
                            <span>Absensi donor</span>
                        </div>
                        <span class="mt-0.5 block text-[10px] text-teal-300/70">
                            Presensi & Skrining Fisik
                        </span>
                    </Link>
                </div>

                <!-- Branch 3: Stok Darah -> Update stok -->
                <div class="flex flex-col items-center">
                    <div class="hidden h-4 w-0.5 bg-zinc-700 md:block"></div>
                    <ArrowDown
                        class="-mt-1 hidden size-3 text-zinc-500 md:block"
                    />

                    <!-- Modul Utama Card -->
                    <Link
                        href="/stocks"
                        class="group flex min-h-[92px] w-full flex-col justify-between rounded-xl border border-[#6E2C1E] bg-[#4A1E14] p-3.5 text-center shadow-md transition hover:border-amber-500/60"
                    >
                        <div
                            class="flex items-center justify-center gap-1.5 text-sm font-bold text-amber-100 group-hover:text-white"
                        >
                            <Droplets class="size-4 text-amber-400" />
                            <span>Stok darah</span>
                        </div>
                        <p class="mt-1 text-xs text-amber-200/80">
                            Per golongan
                        </p>
                        <div
                            v-if="stats"
                            class="mt-1.5 text-[10px] text-amber-300/70"
                        >
                            {{ stats.total_stock_units }} Total Kantong
                        </div>
                    </Link>

                    <div class="h-4 w-0.5 bg-zinc-700"></div>
                    <ArrowDown class="-mt-1 size-3 text-zinc-500" />

                    <!-- Aksi Admin Card -->
                    <Link
                        href="/stocks"
                        class="group w-full rounded-xl border border-[#175C53] bg-[#0F3A34] px-3 py-2.5 text-center shadow-md transition hover:border-teal-400/80 hover:bg-[#144740]"
                    >
                        <div
                            class="flex items-center justify-center gap-1.5 text-xs font-bold text-teal-200 group-hover:text-white"
                        >
                            <RefreshCw class="size-3.5 text-teal-400" />
                            <span>Update stok</span>
                        </div>
                        <span class="mt-0.5 block text-[10px] text-teal-300/70">
                            Masuk, Keluar, Mutasi
                        </span>
                    </Link>
                </div>

                <!-- Branch 4: Permintaan -> Setujui / tolak -->
                <div class="flex flex-col items-center">
                    <div class="hidden h-4 w-0.5 bg-zinc-700 md:block"></div>
                    <ArrowDown
                        class="-mt-1 hidden size-3 text-zinc-500 md:block"
                    />

                    <!-- Modul Utama Card -->
                    <Link
                        href="/requests"
                        class="group flex min-h-[92px] w-full flex-col justify-between rounded-xl border border-[#6E2C1E] bg-[#4A1E14] p-3.5 text-center shadow-md transition hover:border-amber-500/60"
                    >
                        <div
                            class="flex items-center justify-center gap-1.5 text-sm font-bold text-amber-100 group-hover:text-white"
                        >
                            <Building2 class="size-4 text-amber-400" />
                            <span>Permintaan</span>
                        </div>
                        <p class="mt-1 text-xs text-amber-200/80">
                            Rumah sakit
                        </p>
                        <div
                            v-if="stats && stats.emergency_requests_count > 0"
                            class="mt-1.5 animate-pulse text-[10px] font-semibold text-red-400"
                        >
                            ⚠️ {{ stats.emergency_requests_count }} Darurat
                            Cito!
                        </div>
                        <div
                            v-else-if="stats"
                            class="mt-1.5 text-[10px] text-amber-300/70"
                        >
                            {{ stats.pending_requests_count }} Menunggu
                        </div>
                    </Link>

                    <div class="h-4 w-0.5 bg-zinc-700"></div>
                    <ArrowDown class="-mt-1 size-3 text-zinc-500" />

                    <!-- Aksi Admin Card -->
                    <Link
                        href="/requests?status=pending"
                        class="group w-full rounded-xl border border-[#175C53] bg-[#0F3A34] px-3 py-2.5 text-center shadow-md transition hover:border-teal-400/80 hover:bg-[#144740]"
                    >
                        <div
                            class="flex items-center justify-center gap-1.5 text-xs font-bold text-teal-200 group-hover:text-white"
                        >
                            <CheckCheck class="size-3.5 text-teal-400" />
                            <span>Setujui / tolak</span>
                        </div>
                        <span class="mt-0.5 block text-[10px] text-teal-300/70">
                            Validasi & Alokasi Stok
                        </span>
                    </Link>
                </div>
            </div>

            <!-- Merging lines down to Laporan & Notifikasi -->
            <div class="relative mt-2 hidden w-full max-w-4xl md:block">
                <div class="mx-auto h-0.5 w-[84%] bg-zinc-700"></div>
                <div class="mx-auto h-6 w-0.5 bg-zinc-700"></div>
                <ArrowDown class="mx-auto -mt-1.5 size-3 text-zinc-500" />
            </div>

            <!-- Mobile down arrow -->
            <div class="mt-2 h-6 w-0.5 bg-zinc-700 md:hidden"></div>
            <ArrowDown class="-mt-1.5 size-3 text-zinc-500 md:hidden" />

            <!-- Bottom Node: Laporan dan notifikasi (Umum) -->
            <div class="mt-2 w-full max-w-md">
                <Link
                    href="/reports"
                    class="group block w-full rounded-xl border border-zinc-700 bg-zinc-800/90 p-4 text-center shadow-lg transition hover:border-blue-500/60"
                >
                    <div
                        class="flex items-center justify-center gap-2 text-sm font-bold text-white transition group-hover:text-blue-400"
                    >
                        <FileText class="size-4 text-blue-400" />
                        <span>Laporan dan notifikasi</span>
                    </div>
                    <p class="mt-1 text-xs text-zinc-300">
                        Ekspor data, kirim pengingat
                    </p>
                    <div
                        class="mt-2 flex items-center justify-center gap-3 text-[11px] text-zinc-400"
                    >
                        <span
                            class="rounded border border-zinc-800 bg-zinc-900/90 px-2 py-0.5"
                            >Ekspor CSV & Cetak</span
                        >
                        <span
                            class="rounded border border-zinc-800 bg-zinc-900/90 px-2 py-0.5"
                            >Broadcast WhatsApp</span
                        >
                    </div>
                </Link>
            </div>
        </div>
    </div>
</template>
