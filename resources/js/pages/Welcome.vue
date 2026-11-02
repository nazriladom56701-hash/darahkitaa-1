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
    Lock
} from '@lucide/vue';
</script>

<template>
    <Head title="DarahKitaa - Sistem Admin Donor Darah PMI" />

    <div class="min-h-screen bg-zinc-950 text-zinc-100 flex flex-col justify-between selection:bg-red-500 selection:text-white">
        <!-- Top Navbar -->
        <header class="border-b border-zinc-800/80 bg-zinc-900/60 backdrop-blur sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="size-9 rounded-xl bg-red-600 flex items-center justify-center text-white shadow-lg shadow-red-600/30">
                        <Droplets class="size-5" />
                    </div>
                    <div>
                        <span class="font-extrabold text-lg tracking-tight text-white">Darah<span class="text-red-500">Kitaa</span></span>
                        <span class="hidden sm:inline-block text-[11px] text-zinc-400 ml-2 font-mono border-l border-zinc-700 pl-2">Sistem Admin Donor PMI</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        href="/dashboard"
                        class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-red-600/20"
                    >
                        <span>Buka Dashboard</span>
                        <ArrowRight class="size-3.5" />
                    </Link>
                    <template v-else>
                        <Link
                            href="/login"
                            class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-red-600/20"
                        >
                            <Lock class="size-3.5" />
                            <span>Login Admin</span>
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Main Hero Section -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 flex-1 flex flex-col justify-center">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Left text -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-semibold">
                        <Activity class="size-3.5" />
                        <span>Platform Terintegrasi Unit Transfusi Darah (PMI)</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight text-white">
                        Sistem Informasi Manajemen Donor Darah <span class="text-red-500">DarahKitaa</span>
                    </h1>

                    <p class="text-zinc-400 text-sm sm:text-base max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        Solusi digital pengelolaan data donor, penjadwalan event, pemantauan stok kantong darah per golongan, serta verifikasi dan alokasi permohonan pasokan darah rumah sakit secara cepat dan aman.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3">
                        <Link
                            href="/login"
                            class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold shadow-lg shadow-red-600/30 transition flex items-center justify-center gap-2"
                        >
                            <ShieldCheck class="size-4" />
                            <span>Masuk ke Web Admin</span>
                        </Link>

                        <a
                            href="#alur"
                            class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 text-zinc-300 text-sm font-semibold transition flex items-center justify-center gap-2"
                        >
                            <span>Pelajari Alur Sistem &rarr;</span>
                        </a>
                    </div>

                    <!-- Flow Feature Badges -->
                    <div class="pt-6 grid grid-cols-2 sm:grid-cols-4 gap-3 text-left">
                        <div class="p-3 rounded-xl bg-zinc-900/60 border border-zinc-800/80">
                            <Users class="size-4 text-amber-500 mb-1" />
                            <div class="font-bold text-xs text-zinc-200">Data Donor</div>
                            <div class="text-[10px] text-zinc-400">Profil & Verifikasi</div>
                        </div>

                        <div class="p-3 rounded-xl bg-zinc-900/60 border border-zinc-800/80">
                            <Calendar class="size-4 text-teal-400 mb-1" />
                            <div class="font-bold text-xs text-zinc-200">Jadwal Donor</div>
                            <div class="text-[10px] text-zinc-400">Event & Absensi</div>
                        </div>

                        <div class="p-3 rounded-xl bg-zinc-900/60 border border-zinc-800/80">
                            <Droplet class="size-4 text-red-500 mb-1" />
                            <div class="font-bold text-xs text-zinc-200">Stok Darah</div>
                            <div class="text-[10px] text-zinc-400">8 Gol. & Update Stok</div>
                        </div>

                        <div class="p-3 rounded-xl bg-zinc-900/60 border border-zinc-800/80">
                            <Building2 class="size-4 text-blue-400 mb-1" />
                            <div class="font-bold text-xs text-zinc-200">Permintaan RS</div>
                            <div class="text-[10px] text-zinc-400">Setujui & Tolak</div>
                        </div>
                    </div>
                </div>

                <!-- Right visual: Mini Dashboard Flow Mockup -->
                <div class="lg:col-span-5" id="alur">
                    <div class="rounded-2xl border border-zinc-800 bg-zinc-900/90 p-5 shadow-2xl relative overflow-hidden space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
                            <div class="flex items-center gap-2">
                                <span class="size-2 rounded-full bg-emerald-500 animate-ping"></span>
                                <span class="text-xs font-bold text-zinc-200">Alur Web Admin DarahKitaa</span>
                            </div>
                            <span class="text-[10px] font-mono text-zinc-400 bg-zinc-800 px-2 py-0.5 rounded">PMI UDD Portal</span>
                        </div>

                        <!-- Step Cards from user flow -->
                        <div class="space-y-2.5 text-xs">
                            <div class="p-3 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-between">
                                <div class="flex items-center gap-2 font-semibold text-zinc-200">
                                    <span class="size-2 rounded-full bg-zinc-400"></span>
                                    <span>Login Admin & Dashboard</span>
                                </div>
                                <span class="text-[10px] text-zinc-400">Umum</span>
                            </div>

                            <div class="p-3 rounded-xl bg-[#4A1E14]/80 border border-[#6E2C1E] flex items-center justify-between">
                                <div class="flex items-center gap-2 font-semibold text-amber-100">
                                    <span class="size-2 rounded-full bg-amber-400"></span>
                                    <span>Data Donor &rarr; Aksi: Verifikasi</span>
                                </div>
                                <span class="text-[10px] text-teal-300 bg-teal-950/80 px-2 py-0.5 rounded border border-teal-800">Aksi Admin</span>
                            </div>

                            <div class="p-3 rounded-xl bg-[#4A1E14]/80 border border-[#6E2C1E] flex items-center justify-between">
                                <div class="flex items-center gap-2 font-semibold text-amber-100">
                                    <span class="size-2 rounded-full bg-amber-400"></span>
                                    <span>Jadwal Donor &rarr; Aksi: Absensi</span>
                                </div>
                                <span class="text-[10px] text-teal-300 bg-teal-950/80 px-2 py-0.5 rounded border border-teal-800">Aksi Admin</span>
                            </div>

                            <div class="p-3 rounded-xl bg-[#4A1E14]/80 border border-[#6E2C1E] flex items-center justify-between">
                                <div class="flex items-center gap-2 font-semibold text-amber-100">
                                    <span class="size-2 rounded-full bg-amber-400"></span>
                                    <span>Stok Darah &rarr; Aksi: Update Stok</span>
                                </div>
                                <span class="text-[10px] text-teal-300 bg-teal-950/80 px-2 py-0.5 rounded border border-teal-800">Aksi Admin</span>
                            </div>

                            <div class="p-3 rounded-xl bg-[#4A1E14]/80 border border-[#6E2C1E] flex items-center justify-between">
                                <div class="flex items-center gap-2 font-semibold text-amber-100">
                                    <span class="size-2 rounded-full bg-amber-400"></span>
                                    <span>Permintaan RS &rarr; Aksi: Setujui/Tolak</span>
                                </div>
                                <span class="text-[10px] text-teal-300 bg-teal-950/80 px-2 py-0.5 rounded border border-teal-800">Aksi Admin</span>
                            </div>

                            <div class="p-3 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-between">
                                <div class="flex items-center gap-2 font-semibold text-zinc-200">
                                    <span class="size-2 rounded-full bg-blue-400"></span>
                                    <span>Laporan & Notifikasi (Ekspor, Pengingat)</span>
                                </div>
                                <span class="text-[10px] text-blue-300">Ekspor CSV</span>
                            </div>
                        </div>

                        <div class="pt-2 text-center">
                            <Link
                                href="/login"
                                class="w-full py-2 rounded-xl bg-red-600/20 hover:bg-red-600/30 text-red-400 text-xs font-semibold block transition border border-red-500/30"
                            >
                                Buka Panel Admin DarahKitaa &rarr;
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-zinc-800/80 bg-zinc-900/40 py-6 text-center text-xs text-zinc-500">
            <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
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
