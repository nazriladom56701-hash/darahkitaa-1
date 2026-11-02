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
    MessageSquareShare,
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
    message:
        'Salam kemanusiaan dari DarahKitaa PMI! Saat ini kebutuhan stok darah di wilayah kita meningkat. Bagi para pendonor setia yang sudah memenuhi syarat waktu donor kembali (>60 hari), kami mengundang Anda untuk berdonor di markas PMI terdekat atau mobile unit hari ini.',
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

    <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 p-4 md:p-6">
        <!-- Header -->
        <div
            class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
        >
            <div>
                <div class="flex items-center gap-2">
                    <span class="rounded-lg bg-zinc-800 p-2 text-zinc-100">
                        <FileText class="size-5 text-blue-400" />
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1
                                class="text-xl font-extrabold tracking-tight text-foreground md:text-2xl"
                            >
                                Laporan dan Notifikasi
                            </h1>
                            <span
                                class="rounded border border-border bg-zinc-500/10 px-2 py-0.5 text-xs font-semibold text-zinc-600 dark:text-zinc-400"
                            >
                                Umum: Ekspor Data, Kirim Pengingat
                            </span>
                        </div>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            Ekspor rekapan data ke CSV/Cetak dan kirimkan
                            pengingat donor kembali (recall) via WhatsApp ke
                            pendonor yang telah memenuhi syarat.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tab Switcher -->
            <div class="flex items-center rounded-xl bg-muted/60 p-1 text-xs">
                <button
                    @click="activeTab = 'reminders'"
                    class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 font-semibold transition"
                    :class="
                        activeTab === 'reminders'
                            ? 'bg-card text-foreground shadow-sm'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                >
                    <Send class="size-3.5 text-blue-500" />
                    <span>Kirim Pengingat (Recall)</span>
                </button>

                <button
                    @click="activeTab = 'reports'"
                    class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 font-semibold transition"
                    :class="
                        activeTab === 'reports'
                            ? 'bg-card text-foreground shadow-sm'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                >
                    <Download class="size-3.5 text-emerald-500" />
                    <span>Ekspor Data & Laporan</span>
                </button>
            </div>
        </div>

        <!-- ================= TAB 1: KIRIM PENGINGAT (RECALL) ================= -->
        <div v-if="activeTab === 'reminders'" class="space-y-6">
            <!-- Recall Notice Banner -->
            <div
                class="flex flex-col justify-between gap-4 rounded-2xl border border-blue-500/30 bg-gradient-to-r from-blue-950/40 via-card to-card p-5 sm:flex-row sm:items-center"
            >
                <div class="flex items-start gap-3">
                    <div
                        class="mt-0.5 flex size-10 shrink-0 items-center justify-center rounded-xl bg-blue-500/10 text-blue-500"
                    >
                        <Radio class="size-5" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-foreground">
                            Pengingat Waktu Donor Kembali (> 60 Hari)
                        </h3>
                        <p
                            class="mt-0.5 max-w-xl text-xs text-muted-foreground"
                        >
                            Daftar di bawah menyaring pendonor terverifikasi
                            yang telah melewati jeda minimum 60 hari sejak
                            donasi terakhir. Anda dapat mengirimkan pengingat
                            WhatsApp secara individual atau broadcast massal.
                        </p>
                    </div>
                </div>

                <button
                    @click="isBroadcastOpen = true"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700"
                >
                    <MessageSquareShare class="size-4" /> Broadcast Pengingat
                    Massal
                </button>
            </div>

            <!-- Table of Eligible Donors for Recall -->
            <div
                class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"
            >
                <div
                    class="flex items-center justify-between border-b border-border p-4"
                >
                    <div>
                        <h3 class="text-sm font-bold text-foreground">
                            Pendonor Siap Donor Kembali
                        </h3>
                        <p class="text-xs text-muted-foreground">
                            {{ eligibleDonors.length }} pendonor terdaftar siap
                            dipanggil
                        </p>
                    </div>
                    <span
                        class="rounded-lg bg-blue-500/10 px-2.5 py-1 font-mono text-xs font-semibold text-blue-600"
                    >
                        Total Siap: {{ reportStats.donors_ready_recall }} orang
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead
                            class="border-b border-border bg-muted/40 text-[10px] tracking-wider text-muted-foreground uppercase"
                        >
                            <tr>
                                <th class="px-4 py-3 font-semibold">
                                    Pendonor & Kode
                                </th>
                                <th class="px-4 py-3 font-semibold">
                                    Golongan Darah
                                </th>
                                <th class="px-4 py-3 font-semibold">
                                    Kontak WhatsApp
                                </th>
                                <th class="px-4 py-3 font-semibold">
                                    Donasi Terakhir
                                </th>
                                <th class="px-4 py-3 font-semibold">
                                    Status Kesiapan
                                </th>
                                <th class="px-4 py-3 text-right font-semibold">
                                    Aksi Admin
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <tr
                                v-for="d in eligibleDonors"
                                :key="d.id"
                                class="transition hover:bg-muted/20"
                            >
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-foreground">
                                        {{ d.name }}
                                    </div>
                                    <div
                                        class="font-mono text-[10px] text-muted-foreground"
                                    >
                                        {{ d.donor_code }}
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span
                                        class="rounded border border-red-500/20 bg-red-500/10 px-2 py-0.5 font-bold text-red-600"
                                    >
                                        {{ d.blood_type_full }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span
                                        class="font-mono text-muted-foreground"
                                        >{{ d.phone }}</span
                                    >
                                </td>

                                <td class="px-4 py-3.5 text-muted-foreground">
                                    {{
                                        d.last_donation_date ||
                                        'Belum pernah donor'
                                    }}
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="space-y-0.5">
                                        <span
                                            class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600"
                                        >
                                            <CheckCircle2 class="size-3" />
                                            Memenuhi Syarat
                                        </span>
                                        <div
                                            v-if="d.days_since_last_donation"
                                            class="text-[10px] text-muted-foreground"
                                        >
                                            Sudah
                                            {{ d.days_since_last_donation }}
                                            hari sejak donor lalu
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-right">
                                    <!-- Aksi Admin: Kirim Pengingat -->
                                    <button
                                        @click="openSingleReminder(d)"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700"
                                    >
                                        <Send class="size-3" />
                                        <span>Kirim WhatsApp</span>
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="eligibleDonors.length === 0">
                                <td
                                    colspan="6"
                                    class="py-10 text-center text-xs text-muted-foreground"
                                >
                                    Tidak ada pendonor yang perlu dikirimi
                                    pengingat saat ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Log Notifikasi yang Pernah Dikirim -->
            <div
                class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"
            >
                <div
                    class="flex items-center justify-between border-b border-border p-4"
                >
                    <div class="flex items-center gap-2">
                        <Bell class="size-4 text-muted-foreground" />
                        <h3 class="text-sm font-bold text-foreground">
                            Riwayat Log Notifikasi & Pengingat Terkirim
                        </h3>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead
                            class="border-b border-border bg-muted/40 text-[10px] tracking-wider text-muted-foreground uppercase"
                        >
                            <tr>
                                <th class="px-4 py-3 font-semibold">
                                    Waktu Kirim
                                </th>
                                <th class="px-4 py-3 font-semibold">
                                    Penerima
                                </th>
                                <th class="px-4 py-3 font-semibold">Kontak</th>
                                <th class="px-4 py-3 font-semibold">Saluran</th>
                                <th class="px-4 py-3 font-semibold">
                                    Pesan Pengingat
                                </th>
                                <th class="px-4 py-3 font-semibold">
                                    Pengirim
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <tr
                                v-for="notif in notifications.data"
                                :key="notif.id"
                                class="transition hover:bg-muted/20"
                            >
                                <td
                                    class="px-4 py-3 whitespace-nowrap text-muted-foreground"
                                >
                                    {{
                                        new Date(
                                            notif.created_at,
                                        ).toLocaleString('id-ID', {
                                            dateStyle: 'medium',
                                            timeStyle: 'short',
                                        })
                                    }}
                                </td>
                                <td class="px-4 py-3 font-bold text-foreground">
                                    {{ notif.recipient_name }}
                                </td>
                                <td
                                    class="px-4 py-3 font-mono text-muted-foreground"
                                >
                                    {{ notif.recipient_contact }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-block rounded border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-[10px] font-semibold text-emerald-600 uppercase"
                                    >
                                        {{ notif.channel }}
                                    </span>
                                </td>
                                <td
                                    class="max-w-md truncate px-4 py-3 text-muted-foreground"
                                    :title="notif.message"
                                >
                                    {{ notif.message }}
                                </td>
                                <td
                                    class="px-4 py-3 font-medium text-foreground"
                                >
                                    {{
                                        notif.sender
                                            ? notif.sender.name
                                            : 'Sistem'
                                    }}
                                </td>
                            </tr>

                            <tr v-if="notifications.data.length === 0">
                                <td
                                    colspan="6"
                                    class="py-8 text-center text-xs text-muted-foreground"
                                >
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
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <a
                    href="/reports/export-csv?type=donors"
                    class="group flex items-center justify-between rounded-2xl border border-border bg-card p-4 shadow-sm transition hover:border-red-500/50 hover:bg-red-500/5"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-red-500/10 text-red-600"
                        >
                            <Users class="size-5" />
                        </div>
                        <div>
                            <div class="text-sm font-bold text-foreground">
                                Ekspor Data Pendonor
                            </div>
                            <div class="text-xs text-muted-foreground">
                                Format CSV Spreadsheet
                            </div>
                        </div>
                    </div>
                    <Download
                        class="size-4 text-muted-foreground transition group-hover:text-red-600"
                    />
                </a>

                <a
                    href="/reports/export-csv?type=stocks"
                    class="group flex items-center justify-between rounded-2xl border border-border bg-card p-4 shadow-sm transition hover:border-amber-500/50 hover:bg-amber-500/5"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600"
                        >
                            <Droplets class="size-5" />
                        </div>
                        <div>
                            <div class="text-sm font-bold text-foreground">
                                Ekspor Stok Darah
                            </div>
                            <div class="text-xs text-muted-foreground">
                                Rekap kantong per golongan
                            </div>
                        </div>
                    </div>
                    <Download
                        class="size-4 text-muted-foreground transition group-hover:text-amber-600"
                    />
                </a>

                <a
                    href="/reports/export-csv?type=requests"
                    class="group flex items-center justify-between rounded-2xl border border-border bg-card p-4 shadow-sm transition hover:border-blue-500/50 hover:bg-blue-500/5"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"
                        >
                            <Building2 class="size-5" />
                        </div>
                        <div>
                            <div class="text-sm font-bold text-foreground">
                                Ekspor Permintaan RS
                            </div>
                            <div class="text-xs text-muted-foreground">
                                Rekap distribusi ke rumah sakit
                            </div>
                        </div>
                    </div>
                    <Download
                        class="size-4 text-muted-foreground transition group-hover:text-blue-600"
                    />
                </a>
            </div>

            <!-- Printable Formal Report Document (Ready for Print / PDF) -->
            <div
                class="rounded-2xl border border-border bg-card p-6 shadow-sm md:p-8 print:border-none print:p-0 print:shadow-none"
            >
                <div
                    class="mb-6 flex items-center justify-between border-b border-border pb-6 print:hidden"
                >
                    <div>
                        <h3 class="text-lg font-bold text-foreground">
                            Lembar Cetak Laporan Resmi UDD DarahKitaa
                        </h3>
                        <p class="text-xs text-muted-foreground">
                            Dapat langsung dicetak ke printer atau disimpan
                            sebagai dokumen PDF
                        </p>
                    </div>
                    <button
                        @click="printReport"
                        class="inline-flex items-center gap-2 rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-zinc-800"
                    >
                        <Printer class="size-4" /> Cetak Laporan / Simpan PDF
                    </button>
                </div>

                <!-- Printable Document Content with PMI Kop Surat -->
                <div class="space-y-6 text-foreground">
                    <!-- Letterhead / Kop Surat -->
                    <div
                        class="space-y-1 border-b-2 border-foreground/80 pb-4 text-center"
                    >
                        <div
                            class="text-xs font-extrabold tracking-widest text-red-600 uppercase"
                        >
                            Palang Merah Indonesia • Unit Transfusi Darah
                        </div>
                        <h2 class="text-2xl font-black tracking-tight">
                            DARAHKITAA SISTEM MANAJEMEN DONOR
                        </h2>
                        <p class="text-xs text-muted-foreground">
                            Jl. Kramat Raya No. 47, Senen, Jakarta Pusat • Telp:
                            (021) 3907722 • Email: udd@darahkita.id
                        </p>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <div>
                            <div>
                                <strong>Jenis Laporan:</strong> Rekapitulasi
                                Operasional Donor & Permintaan Darah
                            </div>
                            <div>
                                <strong>Periode:</strong> Per Tanggal
                                {{
                                    new Date().toLocaleDateString('id-ID', {
                                        dateStyle: 'full',
                                    })
                                }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div>
                                <strong>Dokumen ID:</strong> LAP-DK-{{
                                    new Date().toISOString().slice(0, 10)
                                }}
                            </div>
                            <div>
                                <strong>Status:</strong> Resmi & Tervalidasi
                            </div>
                        </div>
                    </div>

                    <!-- Summary Stats Grid -->
                    <div class="grid grid-cols-2 gap-4 pt-2 md:grid-cols-4">
                        <div
                            class="rounded-xl border border-border p-4 text-center"
                        >
                            <div class="text-xs text-muted-foreground">
                                Total Pendonor Terdaftar
                            </div>
                            <div class="mt-1 text-2xl font-bold">
                                {{ reportStats.total_donors }} orang
                            </div>
                        </div>
                        <div
                            class="rounded-xl border border-border p-4 text-center"
                        >
                            <div class="text-xs text-muted-foreground">
                                Total Stok Kantong Darah
                            </div>
                            <div class="mt-1 text-2xl font-bold text-red-600">
                                {{ reportStats.total_stock_bags }} kantong
                            </div>
                        </div>
                        <div
                            class="rounded-xl border border-border p-4 text-center"
                        >
                            <div class="text-xs text-muted-foreground">
                                Permintaan RS Disetujui
                            </div>
                            <div
                                class="mt-1 text-2xl font-bold text-emerald-600"
                            >
                                {{ reportStats.approved_requests }} berkas
                            </div>
                        </div>
                        <div
                            class="rounded-xl border border-border p-4 text-center"
                        >
                            <div class="text-xs text-muted-foreground">
                                Kegiatan Donor Terlaksana
                            </div>
                            <div class="mt-1 text-2xl font-bold">
                                {{ reportStats.completed_events }} event
                            </div>
                        </div>
                    </div>

                    <!-- Signature Area for Official Print -->
                    <div class="flex items-end justify-between pt-8 text-xs">
                        <div>
                            <p class="text-muted-foreground">
                                Dicetak secara otomatis melalui portal
                                DarahKitaa
                            </p>
                        </div>
                        <div class="w-56 space-y-14 text-center">
                            <p>Penanggung Jawab UDD PMI,</p>
                            <div>
                                <p class="font-bold underline">
                                    dr. Sarah Amelia
                                </p>
                                <p class="text-[10px] text-muted-foreground">
                                    NIP. 19850412 201001 2 004
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Kirim Pengingat Individual -->
        <div
            v-if="isSingleReminderOpen && activeDonorForReminder"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        >
            <div
                class="w-full max-w-md space-y-4 rounded-2xl border border-border bg-card p-6 shadow-2xl"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-10 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-600"
                    >
                        <Send class="size-5" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-foreground">
                            Kirim Pengingat Donor Kembali
                        </h3>
                        <p class="text-xs text-muted-foreground">
                            {{ activeDonorForReminder.name }} ({{
                                activeDonorForReminder.blood_type_full
                            }})
                        </p>
                    </div>
                </div>

                <form @submit.prevent="submitSingleReminder" class="space-y-3">
                    <div>
                        <label
                            class="mb-1 block text-xs font-medium text-foreground"
                            >Nomor WhatsApp Tujuan</label
                        >
                        <input
                            :value="activeDonorForReminder.phone"
                            disabled
                            class="w-full rounded-xl border border-input bg-muted/50 px-3 py-2 font-mono text-xs text-muted-foreground"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-xs font-semibold text-foreground"
                            >Teks Pesan WhatsApp</label
                        >
                        <textarea
                            v-model="singleReminderForm.custom_message"
                            rows="4"
                            required
                            class="w-full rounded-xl border border-input bg-background p-3 text-xs text-foreground"
                        ></textarea>
                    </div>

                    <div
                        class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 p-3 text-xs text-emerald-700 dark:text-emerald-300"
                    >
                        ✓ Setelah dikonfirmasi, sistem akan mencatat riwayat
                        notifikasi dan membuka tautan WhatsApp resmi dengan
                        pesan yang sudah terisi.
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="isSingleReminderOpen = false"
                            class="rounded-xl border border-input px-4 py-2 text-xs font-medium hover:bg-muted"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="singleReminderForm.processing"
                            class="rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700"
                        >
                            Kirim WhatsApp Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Broadcast Pengingat Massal -->
        <div
            v-if="isBroadcastOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        >
            <div
                class="w-full max-w-lg space-y-4 rounded-2xl border border-border bg-card p-6 shadow-2xl"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-10 items-center justify-center rounded-full bg-blue-500/10 text-blue-600"
                    >
                        <MessageSquareShare class="size-5" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-foreground">
                            Broadcast Pengingat Donor Massal
                        </h3>
                        <p class="text-xs text-muted-foreground">
                            Kirim pesan serentak ke semua pendonor yang sudah
                            memenuhi syarat waktu (>60 hari)
                        </p>
                    </div>
                </div>

                <form @submit.prevent="submitBroadcast" class="space-y-4">
                    <div>
                        <label
                            class="mb-1 block text-xs font-semibold text-foreground"
                            >Target Golongan Darah (Opsional)</label
                        >
                        <select
                            v-model="broadcastForm.blood_type"
                            class="w-full rounded-xl border border-input bg-card px-3 py-2 text-xs"
                        >
                            <option value="">
                                Semua Golongan Darah (A, B, AB, O)
                            </option>
                            <option value="A">Khusus Golongan A</option>
                            <option value="B">Khusus Golongan B</option>
                            <option value="AB">Khusus Golongan AB</option>
                            <option value="O">Khusus Golongan O</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-xs font-semibold text-foreground"
                            >Pesan Siaran Massal
                            <span class="text-red-500">*</span></label
                        >
                        <textarea
                            v-model="broadcastForm.message"
                            rows="4"
                            required
                            placeholder="Tuliskan pesan ajakan atau panggilan donor darurat..."
                            class="w-full rounded-xl border border-input bg-background p-3 text-xs text-foreground"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="isBroadcastOpen = false"
                            class="rounded-xl border border-input px-4 py-2 text-xs font-medium hover:bg-muted"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="broadcastForm.processing"
                            class="rounded-xl bg-blue-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700"
                        >
                            Kirim Broadcast Pengingat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
