<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    FileText, 
    Send, 
    Download, 
    Printer, 
    Bell, 
    CheckCircle2, 
    Clock, 
    Users, 
    Droplets, 
    Building2, 
    Calendar,
    Phone,
    Radio,
    Sparkles,
    MessageSquareShare
} from '@lucide/vue';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Laporan dan Notifikasi', href: '/reports' },
        ],
    },
});

interface EligibleDonorItem {
    id: number;
    donor_code: string;
    name: string;
    phone: string;
    blood_type_full: string;
    last_donation_date?: string;
    days_since_last_donation?: number;
    total_donations: number;
}

interface NotificationItem {
    id: number;
    recipient_type: string;
    recipient_name: string;
    recipient_contact: string;
    type: string;
    title: string;
    message: string;
    channel: string;
    status: string;
    sent_at: string;
    sender?: { name: string };
}

const props = defineProps<{
    eligibleDonors: EligibleDonorItem[];
    notifications: {
        data: NotificationItem[];
        current_page: number;
        last_page: number;
        total: number;
    };
    reportStats: {
        total_donors: number;
        total_stock_bags: number;
        total_requests: number;
        approved_requests: number;
        completed_events: number;
        notifications_sent: number;
        donors_ready_recall: number;
    };
}>();

const activeTab = ref<'reports' | 'reminders'>('reminders');

// Flash props check for whatsapp redirect
const page = usePage();
const flashWaLink = computed(() => (page.props as any).flash?.waLink);

// Modal State: Kirim Pengingat Individual
const isSingleReminderOpen = ref(false);
const activeDonorForReminder = ref<EligibleDonorItem | null>(null);
const singleReminderForm = useForm({
    donor_id: 0,
    channel: 'whatsapp',
    custom_message: '',
});

function openSingleReminder(donor: EligibleDonorItem) {
    activeDonorForReminder.value = donor;
    singleReminderForm.donor_id = donor.id;
    singleReminderForm.channel = 'whatsapp';
    singleReminderForm.custom_message = `Halo ${donor.name}, salam kemanusiaan dari DarahKitaa (UDD PMI). Masa tunggu donor darah 60 hari Anda telah terlewati (terakhir donor: ${donor.last_donation_date || 'belum pernah'}). Tubuh Anda sudah siap untuk kembali menyelamatkan nyawa sesama. Kunjungi PMI terdekat untuk berdonor hari ini!`;
    isSingleReminderOpen.value = true;
}

function submitSingleReminder() {
    singleReminderForm.post('/reports/send-reminder', {
        onSuccess: () => {
            isSingleReminderOpen.value = false;
            // If waLink returned, open in new tab
            const link = (page.props as any).flash?.waLink;
            if (link) {
                window.open(link, '_blank');
            }
        },
    });
}

// Modal State: Kirim Pengingat Massal (Broadcast)
const isBroadcastOpen = ref(false);
const broadcastForm = useForm({
    blood_type: '',
    message: 'Salam kemanusiaan dari DarahKitaa PMI! Saat ini kebutuhan stok darah di wilayah kita meningkat. Bagi para pendonor setia yang sudah memenuhi syarat waktu donor kembali (>60 hari), kami mengundang Anda untuk berdonor di markas PMI terdekat atau mobile unit hari ini.',
});

function submitBroadcast() {
    broadcastForm.post('/reports/broadcast', {
        onSuccess: () => {
            isBroadcastOpen.value = false;
            broadcastForm.reset();
        },
    });
}

function printReport() {
    window.print();
}
</script>

<template>
    <Head title="Laporan & Notifikasi - DarahKitaa" />

    <div class="flex flex-col gap-6 p-4 md:p-6 max-w-7xl mx-auto w-full">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-lg bg-zinc-800 text-zinc-100">
                        <FileText class="size-5 text-blue-400" />
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl md:text-2xl font-extrabold text-foreground tracking-tight">Laporan dan Notifikasi</h1>
                            <span class="text-xs bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 font-semibold px-2 py-0.5 rounded border border-border">
                                Umum: Ekspor Data, Kirim Pengingat
                            </span>
                        </div>
                        <p class="text-xs text-muted-foreground mt-0.5">
                            Ekspor rekapan data ke CSV/Cetak dan kirimkan pengingat donor kembali (recall) via WhatsApp ke pendonor yang telah memenuhi syarat.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tab Switcher -->
            <div class="flex items-center bg-muted/60 p-1 rounded-xl text-xs">
                <button
                    @click="activeTab = 'reminders'"
                    class="px-3 py-1.5 rounded-lg font-semibold transition flex items-center gap-1.5"
                    :class="activeTab === 'reminders' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                >
                    <Send class="size-3.5 text-blue-500" />
                    <span>Kirim Pengingat (Recall)</span>
                </button>

                <button
                    @click="activeTab = 'reports'"
                    class="px-3 py-1.5 rounded-lg font-semibold transition flex items-center gap-1.5"
                    :class="activeTab === 'reports' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                >
                    <Download class="size-3.5 text-emerald-500" />
                    <span>Ekspor Data & Laporan</span>
                </button>
            </div>
        </div>

        <!-- ================= TAB 1: KIRIM PENGINGAT (RECALL) ================= -->
        <div v-if="activeTab === 'reminders'" class="space-y-6">
            
            <!-- Recall Notice Banner -->
            <div class="p-5 rounded-2xl bg-gradient-to-r from-blue-950/40 via-card to-card border border-blue-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="size-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center shrink-0 mt-0.5">
                        <Radio class="size-5" />
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-foreground">Pengingat Waktu Donor Kembali (> 60 Hari)</h3>
                        <p class="text-xs text-muted-foreground mt-0.5 max-w-xl">
                            Daftar di bawah menyaring pendonor terverifikasi yang telah melewati jeda minimum 60 hari sejak donasi terakhir. Anda dapat mengirimkan pengingat WhatsApp secara individual atau broadcast massal.
                        </p>
                    </div>
                </div>

                <button
                    @click="isBroadcastOpen = true"
                    class="shrink-0 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition"
                >
                    <MessageSquareShare class="size-4" /> Broadcast Pengingat Massal
                </button>
            </div>

            <!-- Table of Eligible Donors for Recall -->
            <div class="rounded-2xl border border-border bg-card overflow-hidden shadow-sm">
                <div class="p-4 border-b border-border flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-foreground">Pendonor Siap Donor Kembali</h3>
                        <p class="text-xs text-muted-foreground">{{ eligibleDonors.length }} pendonor terdaftar siap dipanggil</p>
                    </div>
                    <span class="text-xs font-mono text-blue-600 font-semibold bg-blue-500/10 px-2.5 py-1 rounded-lg">
                        Total Siap: {{ reportStats.donors_ready_recall }} orang
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-muted/40 border-b border-border text-muted-foreground uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Pendonor & Kode</th>
                                <th class="px-4 py-3 font-semibold">Golongan Darah</th>
                                <th class="px-4 py-3 font-semibold">Kontak WhatsApp</th>
                                <th class="px-4 py-3 font-semibold">Donasi Terakhir</th>
                                <th class="px-4 py-3 font-semibold">Status Kesiapan</th>
                                <th class="px-4 py-3 font-semibold text-right">Aksi Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <tr v-for="d in eligibleDonors" :key="d.id" class="hover:bg-muted/20 transition">
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-foreground">{{ d.name }}</div>
                                    <div class="text-[10px] text-muted-foreground font-mono">{{ d.donor_code }}</div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span class="font-bold text-red-600 bg-red-500/10 px-2 py-0.5 rounded border border-red-500/20">
                                        {{ d.blood_type_full }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span class="text-muted-foreground font-mono">{{ d.phone }}</span>
                                </td>

                                <td class="px-4 py-3.5 text-muted-foreground">
                                    {{ d.last_donation_date || 'Belum pernah donor' }}
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="space-y-0.5">
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                                            <CheckCircle2 class="size-3" /> Memenuhi Syarat
                                        </span>
                                        <div v-if="d.days_since_last_donation" class="text-[10px] text-muted-foreground">
                                            Sudah {{ d.days_since_last_donation }} hari sejak donor lalu
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-right">
                                    <!-- Aksi Admin: Kirim Pengingat -->
                                    <button
                                        @click="openSingleReminder(d)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition"
                                    >
                                        <Send class="size-3" />
                                        <span>Kirim WhatsApp</span>
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="eligibleDonors.length === 0">
                                <td colspan="6" class="py-10 text-center text-xs text-muted-foreground">
                                    Tidak ada pendonor yang perlu dikirimi pengingat saat ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Log Notifikasi yang Pernah Dikirim -->
            <div class="rounded-2xl border border-border bg-card overflow-hidden shadow-sm">
                <div class="p-4 border-b border-border flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Bell class="size-4 text-muted-foreground" />
                        <h3 class="font-bold text-sm text-foreground">Riwayat Log Notifikasi & Pengingat Terkirim</h3>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-muted/40 border-b border-border text-muted-foreground uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Waktu Kirim</th>
                                <th class="px-4 py-3 font-semibold">Penerima</th>
                                <th class="px-4 py-3 font-semibold">Kontak</th>
                                <th class="px-4 py-3 font-semibold">Saluran</th>
                                <th class="px-4 py-3 font-semibold">Pesan Pengingat</th>
                                <th class="px-4 py-3 font-semibold">Pengirim</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <tr v-for="notif in notifications.data" :key="notif.id" class="hover:bg-muted/20 transition">
                                <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">
                                    {{ new Date(notif.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) }}
                                </td>
                                <td class="px-4 py-3 font-bold text-foreground">
                                    {{ notif.recipient_name }}
                                </td>
                                <td class="px-4 py-3 font-mono text-muted-foreground">
                                    {{ notif.recipient_contact }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block text-[10px] font-semibold uppercase px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                        {{ notif.channel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground max-w-md truncate" :title="notif.message">
                                    {{ notif.message }}
                                </td>
                                <td class="px-4 py-3 text-foreground font-medium">
                                    {{ notif.sender ? notif.sender.name : 'Sistem' }}
                                </td>
                            </tr>

                            <tr v-if="notifications.data.length === 0">
                                <td colspan="6" class="py-8 text-center text-xs text-muted-foreground">
                                    Belum ada notifikasi yang tercatat.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ================= TAB 2: EKSPOR DATA & LAPORAN ================= -->
        <div v-else class="space-y-6">
            
            <!-- Quick Export Buttons -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a
                    href="/reports/export-csv?type=donors"
                    class="p-4 rounded-2xl border border-border bg-card hover:border-red-500/50 hover:bg-red-500/5 transition flex items-center justify-between group shadow-sm"
                >
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-xl bg-red-500/10 text-red-600 flex items-center justify-center">
                            <Users class="size-5" />
                        </div>
                        <div>
                            <div class="font-bold text-sm text-foreground">Ekspor Data Pendonor</div>
                            <div class="text-xs text-muted-foreground">Format CSV Spreadsheet</div>
                        </div>
                    </div>
                    <Download class="size-4 text-muted-foreground group-hover:text-red-600 transition" />
                </a>

                <a
                    href="/reports/export-csv?type=stocks"
                    class="p-4 rounded-2xl border border-border bg-card hover:border-amber-500/50 hover:bg-amber-500/5 transition flex items-center justify-between group shadow-sm"
                >
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                            <Droplets class="size-5" />
                        </div>
                        <div>
                            <div class="font-bold text-sm text-foreground">Ekspor Stok Darah</div>
                            <div class="text-xs text-muted-foreground">Rekap kantong per golongan</div>
                        </div>
                    </div>
                    <Download class="size-4 text-muted-foreground group-hover:text-amber-600 transition" />
                </a>

                <a
                    href="/reports/export-csv?type=requests"
                    class="p-4 rounded-2xl border border-border bg-card hover:border-blue-500/50 hover:bg-blue-500/5 transition flex items-center justify-between group shadow-sm"
                >
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center">
                            <Building2 class="size-5" />
                        </div>
                        <div>
                            <div class="font-bold text-sm text-foreground">Ekspor Permintaan RS</div>
                            <div class="text-xs text-muted-foreground">Rekap distribusi ke rumah sakit</div>
                        </div>
                    </div>
                    <Download class="size-4 text-muted-foreground group-hover:text-blue-600 transition" />
                </a>
            </div>

            <!-- Printable Formal Report Document (Ready for Print / PDF) -->
            <div class="rounded-2xl border border-border bg-card p-6 md:p-8 shadow-sm print:border-none print:shadow-none print:p-0">
                <div class="flex items-center justify-between pb-6 border-b border-border mb-6 print:hidden">
                    <div>
                        <h3 class="font-bold text-lg text-foreground">Lembar Cetak Laporan Resmi UDD DarahKitaa</h3>
                        <p class="text-xs text-muted-foreground">Dapat langsung dicetak ke printer atau disimpan sebagai dokumen PDF</p>
                    </div>
                    <button
                        @click="printReport"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-semibold shadow-sm transition"
                    >
                        <Printer class="size-4" /> Cetak Laporan / Simpan PDF
                    </button>
                </div>

                <!-- Printable Document Content with PMI Kop Surat -->
                <div class="space-y-6 text-foreground">
                    <!-- Letterhead / Kop Surat -->
                    <div class="text-center pb-4 border-b-2 border-foreground/80 space-y-1">
                        <div class="text-xs uppercase font-extrabold tracking-widest text-red-600">Palang Merah Indonesia • Unit Transfusi Darah</div>
                        <h2 class="text-2xl font-black tracking-tight">DARAHKITAA SISTEM MANAJEMEN DONOR</h2>
                        <p class="text-xs text-muted-foreground">Jl. Kramat Raya No. 47, Senen, Jakarta Pusat • Telp: (021) 3907722 • Email: udd@darahkita.id</p>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <div>
                            <div><strong>Jenis Laporan:</strong> Rekapitulasi Operasional Donor & Permintaan Darah</div>
                            <div><strong>Periode:</strong> Per Tanggal {{ new Date().toLocaleDateString('id-ID', { dateStyle: 'full' }) }}</div>
                        </div>
                        <div class="text-right">
                            <div><strong>Dokumen ID:</strong> LAP-DK-{{ new Date().toISOString().slice(0, 10) }}</div>
                            <div><strong>Status:</strong> Resmi & Tervalidasi</div>
                        </div>
                    </div>

                    <!-- Summary Stats Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2">
                        <div class="p-4 rounded-xl border border-border text-center">
                            <div class="text-xs text-muted-foreground">Total Pendonor Terdaftar</div>
                            <div class="text-2xl font-bold mt-1">{{ reportStats.total_donors }} orang</div>
                        </div>
                        <div class="p-4 rounded-xl border border-border text-center">
                            <div class="text-xs text-muted-foreground">Total Stok Kantong Darah</div>
                            <div class="text-2xl font-bold mt-1 text-red-600">{{ reportStats.total_stock_bags }} kantong</div>
                        </div>
                        <div class="p-4 rounded-xl border border-border text-center">
                            <div class="text-xs text-muted-foreground">Permintaan RS Disetujui</div>
                            <div class="text-2xl font-bold mt-1 text-emerald-600">{{ reportStats.approved_requests }} berkas</div>
                        </div>
                        <div class="p-4 rounded-xl border border-border text-center">
                            <div class="text-xs text-muted-foreground">Kegiatan Donor Terlaksana</div>
                            <div class="text-2xl font-bold mt-1">{{ reportStats.completed_events }} event</div>
                        </div>
                    </div>

                    <!-- Signature Area for Official Print -->
                    <div class="pt-8 flex justify-between items-end text-xs">
                        <div>
                            <p class="text-muted-foreground">Dicetak secara otomatis melalui portal DarahKitaa</p>
                        </div>
                        <div class="text-center w-56 space-y-14">
                            <p>Penanggung Jawab UDD PMI,</p>
                            <div>
                                <p class="font-bold underline">dr. Sarah Amelia</p>
                                <p class="text-[10px] text-muted-foreground">NIP. 19850412 201001 2 004</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Kirim Pengingat Individual -->
        <div v-if="isSingleReminderOpen && activeDonorForReminder" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
            <div class="bg-card border border-border rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                        <Send class="size-5" />
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-foreground">Kirim Pengingat Donor Kembali</h3>
                        <p class="text-xs text-muted-foreground">{{ activeDonorForReminder.name }} ({{ activeDonorForReminder.blood_type_full }})</p>
                    </div>
                </div>

                <form @submit.prevent="submitSingleReminder" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-foreground mb-1">Nomor WhatsApp Tujuan</label>
                        <input
                            :value="activeDonorForReminder.phone"
                            disabled
                            class="w-full text-xs rounded-xl border border-input bg-muted/50 px-3 py-2 text-muted-foreground font-mono"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1">Teks Pesan WhatsApp</label>
                        <textarea
                            v-model="singleReminderForm.custom_message"
                            rows="4"
                            required
                            class="w-full text-xs rounded-xl border border-input bg-background p-3 text-foreground"
                        ></textarea>
                    </div>

                    <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-700 dark:text-emerald-300">
                        ✓ Setelah dikonfirmasi, sistem akan mencatat riwayat notifikasi dan membuka tautan WhatsApp resmi dengan pesan yang sudah terisi.
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="isSingleReminderOpen = false" class="px-4 py-2 rounded-xl border border-input text-xs font-medium hover:bg-muted">Batal</button>
                        <button type="submit" :disabled="singleReminderForm.processing" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm">
                            Kirim WhatsApp Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Broadcast Pengingat Massal -->
        <div v-if="isBroadcastOpen" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
            <div class="bg-card border border-border rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-full bg-blue-500/10 text-blue-600 flex items-center justify-center">
                        <MessageSquareShare class="size-5" />
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-foreground">Broadcast Pengingat Donor Massal</h3>
                        <p class="text-xs text-muted-foreground">Kirim pesan serentak ke semua pendonor yang sudah memenuhi syarat waktu (>60 hari)</p>
                    </div>
                </div>

                <form @submit.prevent="submitBroadcast" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1">Target Golongan Darah (Opsional)</label>
                        <select v-model="broadcastForm.blood_type" class="w-full text-xs rounded-xl border border-input bg-card px-3 py-2">
                            <option value="">Semua Golongan Darah (A, B, AB, O)</option>
                            <option value="A">Khusus Golongan A</option>
                            <option value="B">Khusus Golongan B</option>
                            <option value="AB">Khusus Golongan AB</option>
                            <option value="O">Khusus Golongan O</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1">Pesan Siaran Massal <span class="text-red-500">*</span></label>
                        <textarea
                            v-model="broadcastForm.message"
                            rows="4"
                            required
                            placeholder="Tuliskan pesan ajakan atau panggilan donor darurat..."
                            class="w-full text-xs rounded-xl border border-input bg-background p-3 text-foreground"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="isBroadcastOpen = false" class="px-4 py-2 rounded-xl border border-input text-xs font-medium hover:bg-muted">Batal</button>
                        <button type="submit" :disabled="broadcastForm.processing" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm">
                            Kirim Broadcast Pengingat
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>
