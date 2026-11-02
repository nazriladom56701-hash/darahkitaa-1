<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import FlowchartDiagram from '@/components/FlowchartDiagram.vue';
import {
    Droplets,
    Users,
    CalendarDays,
    Building2,
    AlertTriangle,
    CheckCircle2,
    XCircle,
    Plus,
    Send,
    ArrowUpRight,
    Clock,
    Activity,
    ShieldAlert,
} from '@lucide/vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: '/dashboard',
            },
        ],
    },
});

interface StockGroup {
    blood_type: string;
    rhesus: string;
    blood_type_full: string;
    total_units: number;
    min_threshold: number;
    status: 'safe' | 'warning' | 'critical';
    components: Record<string, number>;
}

interface HospitalReq {
    id: number;
    request_code: string;
    hospital_name: string;
    patient_name: string;
    blood_type_full: string;
    component: string;
    units_requested: number;
    urgency: 'normal' | 'urgent' | 'emergency';
    status: 'pending' | 'approved' | 'rejected' | 'completed';
    created_at: string;
}

interface DonorEvt {
    id: number;
    event_code: string;
    title: string;
    location: string;
    event_date: string;
    start_time: string;
    end_time: string;
    quota: number;
    target_bags: number;
    status: string;
}

interface MutationItem {
    id: number;
    blood_type: string;
    rhesus: string;
    component: string;
    type: 'in' | 'out' | 'expired' | 'adjustment';
    quantity: number;
    notes: string;
    created_at: string;
}

const props = defineProps<{
    stats: {
        total_stock_units: number;
        total_donors: number;
        verified_donors: number;
        pending_verification_donors: number;
        active_events_count: number;
        pending_requests_count: number;
        emergency_requests_count: number;
        eligible_recall_count: number;
    };
    stockOverview: StockGroup[];
    recentRequests: HospitalReq[];
    recentEvents: DonorEvt[];
    recentMutations: MutationItem[];
}>();

// Fast Action Modal: Approve/Reject Request
const selectedRequest = ref<HospitalReq | null>(null);
const actionType = ref<'approve' | 'reject' | null>(null);

const approveForm = useForm({
    handover_recipient: '',
    notes: '',
});

const rejectForm = useForm({
    rejection_reason: '',
});

function openApproveModal(req: HospitalReq) {
    selectedRequest.value = req;
    actionType.value = 'approve';
    approveForm.reset();
}

function openRejectModal(req: HospitalReq) {
    selectedRequest.value = req;
    actionType.value = 'reject';
    rejectForm.reset();
}

function closeActionModal() {
    selectedRequest.value = null;
    actionType.value = null;
}

function submitApprove() {
    if (!selectedRequest.value) return;
    approveForm.post(`/requests/${selectedRequest.value.id}/approve`, {
        onSuccess: () => closeActionModal(),
    });
}

function submitReject() {
    if (!selectedRequest.value) return;
    rejectForm.post(`/requests/${selectedRequest.value.id}/reject`, {
        onSuccess: () => closeActionModal(),
    });
}
</script>

<template>
    <Head title="Dashboard - DarahKitaa" />

    <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 p-4 md:p-6">
        <!-- Welcome Top Banner -->
        <div
            class="relative overflow-hidden rounded-2xl border border-red-900/30 bg-gradient-to-r from-red-950 via-zinc-900 to-zinc-900 p-6 text-white shadow-lg"
        >
            <div
                class="relative z-10 flex flex-col justify-between gap-4 md:flex-row md:items-center"
            >
                <div>
                    <div class="mb-1.5 flex items-center gap-2">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full border border-red-500/30 bg-red-600/30 px-2.5 py-0.5 text-xs font-semibold text-red-300"
                        >
                            <Activity class="size-3 text-red-400" /> Web Admin
                            Aktif
                        </span>
                        <span class="text-xs text-zinc-400"
                            >Unit Transfusi Darah (PMI)</span
                        >
                    </div>
                    <h1
                        class="text-2xl font-extrabold tracking-tight md:text-3xl"
                    >
                        Selamat Datang di
                        <span class="text-red-500">DarahKitaa</span>
                    </h1>
                    <p class="mt-1 max-w-2xl text-sm text-zinc-300">
                        Kelola stok kantong darah per golongan, jadwalkan aksi
                        donor darah, verifikasi kelayakan pendonor, dan proses
                        permohonan darah darurat rumah sakit secara
                        terintegrasi.
                    </p>
                </div>

                <!-- Emergency Alert Banner if any emergency requests exist -->
                <div
                    v-if="stats.emergency_requests_count > 0"
                    class="flex shrink-0 items-center gap-3 rounded-xl border border-red-500/50 bg-red-600/20 p-3.5 backdrop-blur"
                >
                    <ShieldAlert class="size-8 animate-bounce text-red-500" />
                    <div>
                        <div
                            class="text-xs font-bold tracking-wider text-red-300 uppercase"
                        >
                            Panggilan Darurat Cito!
                        </div>
                        <div class="text-sm font-semibold text-white">
                            {{ stats.emergency_requests_count }} Permintaan
                            Rumah Sakit
                        </div>
                        <Link
                            href="/requests?urgency=emergency"
                            class="text-xs font-medium text-red-400 underline hover:text-red-200"
                        >
                            Tinjau Sekarang &rarr;
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1. Interactive Flowchart from image -->
        <FlowchartDiagram :stats="stats" />

        <!-- 2. Key Metric Stat Cards -->
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <!-- Card 1: Total Stok Darah -->
            <Link
                href="/stocks"
                class="group rounded-xl border border-border bg-card p-4 shadow-sm transition hover:border-red-500/50"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-muted-foreground"
                        >Total Stok Darah</span
                    >
                    <div
                        class="flex size-8 items-center justify-center rounded-lg bg-red-500/10 text-red-600 dark:text-red-400"
                    >
                        <Droplets class="size-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-foreground">{{
                        stats.total_stock_units
                    }}</span>
                    <span class="text-xs text-muted-foreground">kantong</span>
                </div>
                <div
                    class="mt-2 flex items-center gap-1 text-[11px] text-muted-foreground transition group-hover:text-red-600"
                >
                    <span>Lihat rincian golongan &rarr;</span>
                </div>
            </Link>

            <!-- Card 2: Pendonor Terdaftar -->
            <Link
                href="/donors"
                class="group rounded-xl border border-border bg-card p-4 shadow-sm transition hover:border-amber-500/50"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-muted-foreground"
                        >Total Pendonor</span
                    >
                    <div
                        class="flex size-8 items-center justify-center rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400"
                    >
                        <Users class="size-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-foreground">{{
                        stats.total_donors
                    }}</span>
                    <span class="text-xs text-muted-foreground">orang</span>
                </div>
                <div
                    class="mt-2 flex items-center gap-1 text-[11px] font-medium text-amber-600 dark:text-amber-400"
                >
                    <span v-if="stats.pending_verification_donors > 0"
                        >{{ stats.pending_verification_donors }} butuh
                        verifikasi</span
                    >
                    <span v-else>Semua terverifikasi</span>
                </div>
            </Link>

            <!-- Card 3: Jadwal Event Donor -->
            <Link
                href="/events"
                class="group rounded-xl border border-border bg-card p-4 shadow-sm transition hover:border-teal-500/50"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-muted-foreground"
                        >Jadwal Event</span
                    >
                    <div
                        class="flex size-8 items-center justify-center rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400"
                    >
                        <CalendarDays class="size-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-foreground">{{
                        stats.active_events_count
                    }}</span>
                    <span class="text-xs text-muted-foreground"
                        >event aktif</span
                    >
                </div>
                <div
                    class="mt-2 text-[11px] text-muted-foreground transition group-hover:text-teal-600"
                >
                    <span>Buka absensi donor &rarr;</span>
                </div>
            </Link>

            <!-- Card 4: Permintaan Rumah Sakit -->
            <Link
                href="/requests"
                class="group rounded-xl border border-border bg-card p-4 shadow-sm transition hover:border-blue-500/50"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-muted-foreground"
                        >Permintaan RS</span
                    >
                    <div
                        class="flex size-8 items-center justify-center rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400"
                    >
                        <Building2 class="size-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-foreground">{{
                        stats.pending_requests_count
                    }}</span>
                    <span class="text-xs text-muted-foreground">menunggu</span>
                </div>
                <div
                    class="mt-2 text-[11px] text-muted-foreground transition group-hover:text-blue-600"
                >
                    <span>Setujui / tolak pesanan &rarr;</span>
                </div>
            </Link>
        </div>

        <!-- 3. Modul Stok Darah: Kesiapan Stok Darah Per Golongan (Grid Visual) -->
        <div
            class="rounded-2xl border border-border bg-card p-5 shadow-sm md:p-6"
        >
            <div
                class="flex flex-col justify-between gap-3 border-b border-border/80 pb-4 sm:flex-row sm:items-center"
            >
                <div>
                    <h2
                        class="flex items-center gap-2 text-lg font-bold text-foreground"
                    >
                        <Droplets class="size-5 text-red-600" />
                        <span>Kesiapan Stok Darah per Golongan</span>
                    </h2>
                    <p class="text-xs text-muted-foreground">
                        Inventaris kantong darah terpilah berdasarkan golongan,
                        rhesus, dan komponen (WB, PRC, TC, FFP)
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Link
                        href="/stocks"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm transition hover:bg-red-700"
                    >
                        <Plus class="size-3.5" /> Update Stok Darah
                    </Link>
                </div>
            </div>

            <!-- Blood Groups 8 Cards Grid -->
            <div
                class="grid grid-cols-2 gap-3 pt-4 sm:grid-cols-4 lg:grid-cols-8"
            >
                <div
                    v-for="group in stockOverview"
                    :key="group.blood_type_full"
                    class="flex flex-col justify-between rounded-xl border p-3 transition hover:shadow-md"
                    :class="{
                        'border-emerald-500/30 bg-emerald-500/5':
                            group.status === 'safe',
                        'border-amber-500/40 bg-amber-500/5':
                            group.status === 'warning',
                        'animate-pulse border-red-500/50 bg-red-500/10':
                            group.status === 'critical',
                    }"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="text-xl font-extrabold"
                            :class="{
                                'text-emerald-700 dark:text-emerald-400':
                                    group.status === 'safe',
                                'text-amber-700 dark:text-amber-400':
                                    group.status === 'warning',
                                'text-red-700 dark:text-red-400':
                                    group.status === 'critical',
                            }"
                        >
                            {{ group.blood_type_full }}
                        </span>
                        <span
                            class="rounded px-1.5 py-0.5 text-[9px] font-bold uppercase"
                            :class="{
                                'bg-emerald-500/20 text-emerald-800 dark:text-emerald-300':
                                    group.status === 'safe',
                                'bg-amber-500/20 text-amber-800 dark:text-amber-300':
                                    group.status === 'warning',
                                'bg-red-500/30 text-red-800 dark:text-red-200':
                                    group.status === 'critical',
                            }"
                        >
                            {{
                                group.status === 'safe'
                                    ? 'Aman'
                                    : group.status === 'warning'
                                      ? 'Menipis'
                                      : 'Kritis'
                            }}
                        </span>
                    </div>

                    <div class="my-3 text-center">
                        <div class="text-2xl font-black text-foreground">
                            {{ group.total_units }}
                        </div>
                        <div class="text-[10px] text-muted-foreground">
                            kantong
                        </div>
                    </div>

                    <!-- Component mini pills -->
                    <div
                        class="grid grid-cols-2 gap-1 border-t border-border/50 pt-2 text-[10px] text-muted-foreground"
                    >
                        <div class="flex justify-between">
                            <span>WB:</span>
                            <span class="font-semibold text-foreground">{{
                                group.components.WB ?? 0
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>PRC:</span>
                            <span class="font-semibold text-foreground">{{
                                group.components.PRC ?? 0
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>TC:</span>
                            <span class="font-semibold text-foreground">{{
                                group.components.TC ?? 0
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>FFP:</span>
                            <span class="font-semibold text-foreground">{{
                                group.components.FFP ?? 0
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Two Columns: Left (Permintaan RS) | Right (Jadwal Event & Aktivitas) -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Left: Permintaan Rumah Sakit (Aksi Admin: Setujui / Tolak) -->
            <div
                class="flex flex-col rounded-2xl border border-border bg-card p-5 shadow-sm"
            >
                <div
                    class="flex items-center justify-between border-b border-border pb-3"
                >
                    <div class="flex items-center gap-2">
                        <Building2 class="size-5 text-blue-600" />
                        <h3 class="text-base font-bold text-foreground">
                            Permintaan Darah Rumah Sakit
                        </h3>
                    </div>
                    <Link
                        href="/requests"
                        class="text-xs font-medium text-blue-600 hover:underline"
                    >
                        Lihat Semua &rarr;
                    </Link>
                </div>

                <div class="flex-1 divide-y divide-border/60">
                    <div
                        v-for="req in recentRequests"
                        :key="req.id"
                        class="flex items-start justify-between gap-3 py-3 text-sm"
                    >
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-foreground">{{
                                    req.hospital_name
                                }}</span>
                                <span
                                    class="rounded px-1.5 py-0.5 text-[10px] font-bold uppercase"
                                    :class="{
                                        'border border-red-500/30 bg-red-500/20 text-red-700 dark:text-red-400':
                                            req.urgency === 'emergency',
                                        'bg-amber-500/20 text-amber-700 dark:text-amber-400':
                                            req.urgency === 'urgent',
                                        'bg-blue-500/10 text-blue-700 dark:text-blue-400':
                                            req.urgency === 'normal',
                                    }"
                                >
                                    {{
                                        req.urgency === 'emergency'
                                            ? '🚨 Cito!'
                                            : req.urgency === 'urgent'
                                              ? 'Mendesak'
                                              : 'Biasa'
                                    }}
                                </span>
                            </div>
                            <div class="text-xs text-muted-foreground">
                                Pasien:
                                <span class="font-medium text-foreground">{{
                                    req.patient_name
                                }}</span>
                                • Butuh:
                                <span class="font-bold text-red-600"
                                    >{{ req.units_requested }} kantong
                                    {{ req.blood_type_full }} ({{
                                        req.component
                                    }})</span
                                >
                            </div>
                        </div>

                        <!-- Actions based on status -->
                        <div class="flex shrink-0 items-center gap-1.5">
                            <template v-if="req.status === 'pending'">
                                <button
                                    @click="openApproveModal(req)"
                                    class="rounded-md bg-emerald-600 px-2.5 py-1 text-xs font-medium text-white shadow-sm transition hover:bg-emerald-700"
                                    title="Setujui Permintaan"
                                >
                                    Setujui
                                </button>
                                <button
                                    @click="openRejectModal(req)"
                                    class="rounded-md bg-zinc-200 px-2 py-1 text-xs font-medium text-red-600 transition hover:bg-red-50 dark:bg-zinc-800 dark:hover:bg-red-950/40"
                                    title="Tolak Permintaan"
                                >
                                    Tolak
                                </button>
                            </template>
                            <span
                                v-else
                                class="rounded px-2 py-0.5 text-xs font-medium"
                                :class="{
                                    'bg-emerald-500/10 text-emerald-600':
                                        req.status === 'approved' ||
                                        req.status === 'completed',
                                    'bg-zinc-500/10 text-zinc-500 line-through':
                                        req.status === 'rejected',
                                }"
                            >
                                {{
                                    req.status === 'approved'
                                        ? 'Disetujui'
                                        : req.status === 'completed'
                                          ? 'Selesai'
                                          : 'Ditolak'
                                }}
                            </span>
                        </div>
                    </div>

                    <div
                        v-if="recentRequests.length === 0"
                        class="py-8 text-center text-sm text-muted-foreground"
                    >
                        Belum ada permintaan darah dari rumah sakit.
                    </div>
                </div>
            </div>

            <!-- Right: Jadwal Event Donor & Aksi Absensi -->
            <div
                class="flex flex-col rounded-2xl border border-border bg-card p-5 shadow-sm"
            >
                <div
                    class="flex items-center justify-between border-b border-border pb-3"
                >
                    <div class="flex items-center gap-2">
                        <CalendarDays class="size-5 text-amber-600" />
                        <h3 class="text-base font-bold text-foreground">
                            Jadwal Event Donor Darah
                        </h3>
                    </div>
                    <Link
                        href="/events"
                        class="text-xs font-medium text-amber-600 hover:underline"
                    >
                        Semua Event &rarr;
                    </Link>
                </div>

                <div class="flex-1 divide-y divide-border/60">
                    <div
                        v-for="evt in recentEvents"
                        :key="evt.id"
                        class="flex items-start justify-between gap-3 py-3 text-sm"
                    >
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-foreground">{{
                                    evt.title
                                }}</span>
                                <span
                                    class="rounded px-1.5 py-0.5 text-[10px] font-bold uppercase"
                                    :class="{
                                        'bg-emerald-500/20 text-emerald-700':
                                            evt.status === 'ongoing',
                                        'bg-blue-500/10 text-blue-700':
                                            evt.status === 'upcoming',
                                        'bg-zinc-500/10 text-zinc-600':
                                            evt.status === 'completed',
                                    }"
                                >
                                    {{
                                        evt.status === 'ongoing'
                                            ? 'Sedang Berlangsung'
                                            : evt.status === 'upcoming'
                                              ? 'Mendatang'
                                              : 'Selesai'
                                    }}
                                </span>
                            </div>
                            <div class="text-xs text-muted-foreground">
                                📍 {{ evt.location }} • 🗓️
                                {{ evt.event_date }} ({{
                                    evt.start_time.slice(0, 5)
                                }}
                                - {{ evt.end_time.slice(0, 5) }})
                            </div>
                        </div>

                        <!-- Action: Direct to Absensi -->
                        <Link
                            :href="`/events/${evt.id}`"
                            class="flex shrink-0 items-center gap-1 rounded-lg bg-teal-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm transition hover:bg-teal-700"
                        >
                            <span>Absensi Donor</span>
                            <ArrowUpRight class="size-3" />
                        </Link>
                    </div>

                    <div
                        v-if="recentEvents.length === 0"
                        class="py-8 text-center text-sm text-muted-foreground"
                    >
                        Belum ada event donor darah yang dijadwalkan.
                    </div>
                </div>

                <!-- Donor Recall Fast Banner -->
                <div
                    class="mt-4 flex items-center justify-between rounded-xl border border-blue-500/20 bg-blue-500/10 p-3.5"
                >
                    <div>
                        <div
                            class="text-xs font-bold text-blue-700 dark:text-blue-300"
                        >
                            Pengingat Donor Kembali (> 60 Hari)
                        </div>
                        <div class="mt-0.5 text-xs text-muted-foreground">
                            Ada
                            <span class="font-bold text-foreground"
                                >{{
                                    stats.eligible_recall_count
                                }}
                                pendonor</span
                            >
                            yang sudah memenuhi syarat waktu donor kembali.
                        </div>
                    </div>
                    <Link
                        href="/reports"
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm transition hover:bg-blue-700"
                    >
                        <Send class="size-3" /> Kirim Pengingat
                    </Link>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <div
            v-if="actionType === 'approve' && selectedRequest"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        >
            <div
                class="w-full max-w-md space-y-4 rounded-2xl border border-border bg-card p-6 shadow-2xl"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-10 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-600"
                    >
                        <CheckCircle2 class="size-6" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-foreground">
                            Setujui Permintaan Darah
                        </h3>
                        <p class="text-xs text-muted-foreground">
                            {{ selectedRequest.request_code }} -
                            {{ selectedRequest.hospital_name }}
                        </p>
                    </div>
                </div>

                <div class="space-y-1 rounded-lg bg-muted/60 p-3 text-xs">
                    <div>
                        <strong>Pasien:</strong>
                        {{ selectedRequest.patient_name }}
                    </div>
                    <div>
                        <strong>Kebutuhan:</strong>
                        {{ selectedRequest.units_requested }} kantong
                        {{ selectedRequest.blood_type_full }} ({{
                            selectedRequest.component
                        }})
                    </div>
                    <div
                        class="pt-1 font-medium text-emerald-600 dark:text-emerald-400"
                    >
                        ✓ Stok darah akan dipotong otomatis setelah persetujuan
                        dikonfirmasi.
                    </div>
                </div>

                <form @submit.prevent="submitApprove" class="space-y-3">
                    <div>
                        <label
                            class="mb-1 block text-xs font-medium text-foreground"
                            >Penerima Serah Terima / Kurir Medis RS</label
                        >
                        <input
                            v-model="approveForm.handover_recipient"
                            type="text"
                            placeholder="Contoh: Bpk. Joko - Tim Ambulans RS"
                            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-xs text-foreground focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-xs font-medium text-foreground"
                            >Catatan Tambahan (Opsional)</label
                        >
                        <textarea
                            v-model="approveForm.notes"
                            rows="2"
                            placeholder="Catatan penyerahan atau kondisi darah..."
                            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-xs text-foreground focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="closeActionModal"
                            class="rounded-lg border border-input px-4 py-2 text-xs font-medium transition hover:bg-muted"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="approveForm.processing"
                            class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-medium text-white transition hover:bg-emerald-700"
                        >
                            Konfirmasi & Setujui
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reject Modal -->
        <div
            v-if="actionType === 'reject' && selectedRequest"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        >
            <div
                class="w-full max-w-md space-y-4 rounded-2xl border border-border bg-card p-6 shadow-2xl"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-10 items-center justify-center rounded-full bg-red-500/10 text-red-600"
                    >
                        <XCircle class="size-6" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-foreground">
                            Tolak Permintaan Darah
                        </h3>
                        <p class="text-xs text-muted-foreground">
                            {{ selectedRequest.request_code }} -
                            {{ selectedRequest.hospital_name }}
                        </p>
                    </div>
                </div>

                <form @submit.prevent="submitReject" class="space-y-3">
                    <div>
                        <label
                            class="mb-1 block text-xs font-medium text-foreground"
                            >Alasan Penolakan
                            <span class="text-red-500">*</span></label
                        >
                        <textarea
                            v-model="rejectForm.rejection_reason"
                            rows="3"
                            required
                            placeholder="Contoh: Stok darah golongan terkait sedang kritis atau dokumen rujukan RS belum lengkap..."
                            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-xs text-foreground focus:ring-2 focus:ring-red-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="closeActionModal"
                            class="rounded-lg border border-input px-4 py-2 text-xs font-medium transition hover:bg-muted"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="rejectForm.processing"
                            class="rounded-lg bg-red-600 px-4 py-2 text-xs font-medium text-white transition hover:bg-red-700"
                        >
                            Simpan Penolakan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
