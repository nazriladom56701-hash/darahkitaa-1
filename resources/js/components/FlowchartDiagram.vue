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
    ArrowDown
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
    <div class="rounded-2xl border border-zinc-800 bg-zinc-950/90 p-5 md:p-6 text-zinc-100 shadow-xl">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 border-b border-zinc-800/80 gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-block size-2.5 rounded-full bg-red-500 animate-pulse"></span>
                    <h3 class="font-bold text-base md:text-lg tracking-tight text-white">Alur Sistem DarahKitaa (Flow Diagram)</h3>
                </div>
                <p class="text-xs text-zinc-400 mt-0.5">
                    Representasi alur kerja web admin: klik modul untuk menuju halaman dan menjalankan aksi admin
                </p>
            </div>

            <!-- Legend matching the user's diagram -->
            <div class="flex flex-wrap items-center gap-4 text-xs">
                <div class="flex items-center gap-1.5">
                    <span class="size-3 rounded bg-zinc-600 border border-zinc-500"></span>
                    <span class="text-zinc-300">Umum</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="size-3 rounded bg-amber-900 border border-amber-700"></span>
                    <span class="text-zinc-300">Modul utama</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="size-3 rounded bg-teal-800 border border-teal-600"></span>
                    <span class="text-zinc-300">Aksi admin</span>
                </div>
            </div>
        </div>

        <!-- Interactive Diagram Layout -->
        <div class="pt-6 flex flex-col items-center">
            
            <!-- Step 1: Login Admin (Umum) -->
            <div class="flex flex-col items-center">
                <div class="w-64 px-4 py-2.5 rounded-xl bg-zinc-800/90 border border-zinc-700 shadow-md text-center transition hover:border-zinc-500">
                    <div class="flex items-center justify-center gap-2 font-semibold text-sm text-zinc-200">
                        <LogIn class="size-4 text-zinc-400" />
                        <span>Login admin</span>
                    </div>
                    <p class="text-[11px] text-zinc-400 mt-0.5">Autentikasi & hak akses UDD PMI</p>
                </div>

                <!-- Down connector line -->
                <div class="w-0.5 h-6 bg-zinc-700"></div>
                <ArrowDown class="size-3 text-zinc-500 -mt-1.5" />
            </div>

            <!-- Step 2: Dashboard (Umum) -->
            <div class="flex flex-col items-center w-full max-w-sm">
                <Link 
                    href="/dashboard"
                    class="w-full px-5 py-3 rounded-xl bg-zinc-800/90 border border-zinc-700 hover:border-red-500/60 shadow-lg text-center transition group"
                >
                    <div class="flex items-center justify-center gap-2 font-bold text-sm text-white group-hover:text-red-400 transition">
                        <LayoutDashboard class="size-4 text-red-500" />
                        <span>Dashboard</span>
                    </div>
                    <p class="text-xs text-zinc-300 mt-1">Ringkasan stok dan aktivitas</p>
                    <div v-if="stats" class="mt-2 inline-flex items-center gap-2 text-[11px] bg-zinc-900/80 px-2.5 py-0.5 rounded-full text-zinc-400 border border-zinc-800">
                        <span>{{ stats.total_stock_units }} Kantong Darah</span>
                        <span>•</span>
                        <span>{{ stats.total_donors }} Donor</span>
                    </div>
                </Link>

                <!-- Stem down to horizontal branch -->
                <div class="w-0.5 h-6 bg-zinc-700"></div>
            </div>

            <!-- Horizontal Trunk Line -->
            <div class="relative w-full max-w-4xl hidden md:block">
                <div class="h-0.5 w-[84%] mx-auto bg-zinc-700"></div>
            </div>

            <!-- 4 Branches: Modul Utama & Aksi Admin -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 w-full max-w-4xl mt-3 md:mt-0">
                
                <!-- Branch 1: Data Donor -> Verifikasi -->
                <div class="flex flex-col items-center">
                    <!-- Stem from horizontal trunk -->
                    <div class="w-0.5 h-4 bg-zinc-700 hidden md:block"></div>
                    <ArrowDown class="size-3 text-zinc-500 -mt-1 hidden md:block" />

                    <!-- Modul Utama Card -->
                    <Link 
                        href="/donors" 
                        class="w-full p-3.5 rounded-xl bg-[#4A1E14] border border-[#6E2C1E] hover:border-amber-500/60 shadow-md text-center transition group flex flex-col justify-between min-h-[92px]"
                    >
                        <div class="flex items-center justify-center gap-1.5 font-bold text-sm text-amber-100 group-hover:text-white">
                            <Users class="size-4 text-amber-400" />
                            <span>Data donor</span>
                        </div>
                        <p class="text-xs text-amber-200/80 mt-1">Profil, riwayat</p>
                        <div v-if="stats" class="mt-1.5 text-[10px] text-amber-300/70">
                            {{ stats.total_donors }} Donor Terdaftar
                        </div>
                    </Link>

                    <!-- Down arrow to action -->
                    <div class="w-0.5 h-4 bg-zinc-700"></div>
                    <ArrowDown class="size-3 text-zinc-500 -mt-1" />

                    <!-- Aksi Admin Card -->
                    <Link 
                        href="/donors?status=pending"
                        class="w-full py-2.5 px-3 rounded-xl bg-[#0F3A34] border border-[#175C53] hover:border-teal-400/80 hover:bg-[#144740] shadow-md text-center transition group"
                    >
                        <div class="flex items-center justify-center gap-1.5 font-bold text-xs text-teal-200 group-hover:text-white">
                            <CheckCircle2 class="size-3.5 text-teal-400" />
                            <span>Verifikasi</span>
                        </div>
                        <span v-if="stats && stats.pending_verification_donors > 0" class="inline-block mt-1 text-[10px] font-semibold text-amber-300 bg-amber-500/20 border border-amber-500/30 px-2 py-0.5 rounded-full">
                            {{ stats.pending_verification_donors }} Perlu Ditinjau
                        </span>
                        <span v-else class="text-[10px] text-teal-300/70 mt-0.5 block">
                            Kelayakan & Skrining
                        </span>
                    </Link>
                </div>

                <!-- Branch 2: Jadwal Donor -> Absensi donor -->
                <div class="flex flex-col items-center">
                    <div class="w-0.5 h-4 bg-zinc-700 hidden md:block"></div>
                    <ArrowDown class="size-3 text-zinc-500 -mt-1 hidden md:block" />

                    <!-- Modul Utama Card -->
                    <Link 
                        href="/events" 
                        class="w-full p-3.5 rounded-xl bg-[#4A1E14] border border-[#6E2C1E] hover:border-amber-500/60 shadow-md text-center transition group flex flex-col justify-between min-h-[92px]"
                    >
                        <div class="flex items-center justify-center gap-1.5 font-bold text-sm text-amber-100 group-hover:text-white">
                            <Calendar class="size-4 text-amber-400" />
                            <span>Jadwal donor</span>
                        </div>
                        <p class="text-xs text-amber-200/80 mt-1">Event, kuota</p>
                        <div v-if="stats" class="mt-1.5 text-[10px] text-amber-300/70">
                            {{ stats.active_events_count }} Event Aktif
                        </div>
                    </Link>

                    <div class="w-0.5 h-4 bg-zinc-700"></div>
                    <ArrowDown class="size-3 text-zinc-500 -mt-1" />

                    <!-- Aksi Admin Card -->
                    <Link 
                        href="/events"
                        class="w-full py-2.5 px-3 rounded-xl bg-[#0F3A34] border border-[#175C53] hover:border-teal-400/80 hover:bg-[#144740] shadow-md text-center transition group"
                    >
                        <div class="flex items-center justify-center gap-1.5 font-bold text-xs text-teal-200 group-hover:text-white">
                            <UserCheck class="size-3.5 text-teal-400" />
                            <span>Absensi donor</span>
                        </div>
                        <span class="text-[10px] text-teal-300/70 mt-0.5 block">
                            Presensi & Skrining Fisik
                        </span>
                    </Link>
                </div>

                <!-- Branch 3: Stok Darah -> Update stok -->
                <div class="flex flex-col items-center">
                    <div class="w-0.5 h-4 bg-zinc-700 hidden md:block"></div>
                    <ArrowDown class="size-3 text-zinc-500 -mt-1 hidden md:block" />

                    <!-- Modul Utama Card -->
                    <Link 
                        href="/stocks" 
                        class="w-full p-3.5 rounded-xl bg-[#4A1E14] border border-[#6E2C1E] hover:border-amber-500/60 shadow-md text-center transition group flex flex-col justify-between min-h-[92px]"
                    >
                        <div class="flex items-center justify-center gap-1.5 font-bold text-sm text-amber-100 group-hover:text-white">
                            <Droplets class="size-4 text-amber-400" />
                            <span>Stok darah</span>
                        </div>
                        <p class="text-xs text-amber-200/80 mt-1">Per golongan</p>
                        <div v-if="stats" class="mt-1.5 text-[10px] text-amber-300/70">
                            {{ stats.total_stock_units }} Total Kantong
                        </div>
                    </Link>

                    <div class="w-0.5 h-4 bg-zinc-700"></div>
                    <ArrowDown class="size-3 text-zinc-500 -mt-1" />

                    <!-- Aksi Admin Card -->
                    <Link 
                        href="/stocks"
                        class="w-full py-2.5 px-3 rounded-xl bg-[#0F3A34] border border-[#175C53] hover:border-teal-400/80 hover:bg-[#144740] shadow-md text-center transition group"
                    >
                        <div class="flex items-center justify-center gap-1.5 font-bold text-xs text-teal-200 group-hover:text-white">
                            <RefreshCw class="size-3.5 text-teal-400" />
                            <span>Update stok</span>
                        </div>
                        <span class="text-[10px] text-teal-300/70 mt-0.5 block">
                            Masuk, Keluar, Mutasi
                        </span>
                    </Link>
                </div>

                <!-- Branch 4: Permintaan -> Setujui / tolak -->
                <div class="flex flex-col items-center">
                    <div class="w-0.5 h-4 bg-zinc-700 hidden md:block"></div>
                    <ArrowDown class="size-3 text-zinc-500 -mt-1 hidden md:block" />

                    <!-- Modul Utama Card -->
                    <Link 
                        href="/requests" 
                        class="w-full p-3.5 rounded-xl bg-[#4A1E14] border border-[#6E2C1E] hover:border-amber-500/60 shadow-md text-center transition group flex flex-col justify-between min-h-[92px]"
                    >
                        <div class="flex items-center justify-center gap-1.5 font-bold text-sm text-amber-100 group-hover:text-white">
                            <Building2 class="size-4 text-amber-400" />
                            <span>Permintaan</span>
                        </div>
                        <p class="text-xs text-amber-200/80 mt-1">Rumah sakit</p>
                        <div v-if="stats && stats.emergency_requests_count > 0" class="mt-1.5 text-[10px] font-semibold text-red-400 animate-pulse">
                            ⚠️ {{ stats.emergency_requests_count }} Darurat Cito!
                        </div>
                        <div v-else-if="stats" class="mt-1.5 text-[10px] text-amber-300/70">
                            {{ stats.pending_requests_count }} Menunggu
                        </div>
                    </Link>

                    <div class="w-0.5 h-4 bg-zinc-700"></div>
                    <ArrowDown class="size-3 text-zinc-500 -mt-1" />

                    <!-- Aksi Admin Card -->
                    <Link 
                        href="/requests?status=pending"
                        class="w-full py-2.5 px-3 rounded-xl bg-[#0F3A34] border border-[#175C53] hover:border-teal-400/80 hover:bg-[#144740] shadow-md text-center transition group"
                    >
                        <div class="flex items-center justify-center gap-1.5 font-bold text-xs text-teal-200 group-hover:text-white">
                            <CheckCheck class="size-3.5 text-teal-400" />
                            <span>Setujui / tolak</span>
                        </div>
                        <span class="text-[10px] text-teal-300/70 mt-0.5 block">
                            Validasi & Alokasi Stok
                        </span>
                    </Link>
                </div>
            </div>

            <!-- Merging lines down to Laporan & Notifikasi -->
            <div class="relative w-full max-w-4xl hidden md:block mt-2">
                <div class="h-0.5 w-[84%] mx-auto bg-zinc-700"></div>
                <div class="w-0.5 h-6 mx-auto bg-zinc-700"></div>
                <ArrowDown class="size-3 text-zinc-500 mx-auto -mt-1.5" />
            </div>

            <!-- Mobile down arrow -->
            <div class="w-0.5 h-6 bg-zinc-700 md:hidden mt-2"></div>
            <ArrowDown class="size-3 text-zinc-500 -mt-1.5 md:hidden" />

            <!-- Bottom Node: Laporan dan notifikasi (Umum) -->
            <div class="w-full max-w-md mt-2">
                <Link 
                    href="/reports" 
                    class="block w-full p-4 rounded-xl bg-zinc-800/90 border border-zinc-700 hover:border-blue-500/60 shadow-lg text-center transition group"
                >
                    <div class="flex items-center justify-center gap-2 font-bold text-sm text-white group-hover:text-blue-400 transition">
                        <FileText class="size-4 text-blue-400" />
                        <span>Laporan dan notifikasi</span>
                    </div>
                    <p class="text-xs text-zinc-300 mt-1">Ekspor data, kirim pengingat</p>
                    <div class="mt-2 flex items-center justify-center gap-3 text-[11px] text-zinc-400">
                        <span class="bg-zinc-900/90 px-2 py-0.5 rounded border border-zinc-800">Ekspor CSV & Cetak</span>
                        <span class="bg-zinc-900/90 px-2 py-0.5 rounded border border-zinc-800">Broadcast WhatsApp</span>
                    </div>
                </Link>
            </div>

        </div>
    </div>
</template>
