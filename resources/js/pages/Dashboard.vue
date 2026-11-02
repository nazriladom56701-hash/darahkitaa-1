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
    ShieldAlert
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

    <div class="flex flex-col gap-6 p-4 md:p-6 max-w-7xl mx-auto w-full">
        
        <!-- Welcome Top Banner -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-red-950 via-zinc-900 to-zinc-900 border border-red-900/30 p-6 text-white shadow-lg">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-red-600/30 text-red-300 border border-red-500/30">
                            <Activity class="size-3 text-red-400" /> Web Admin Aktif
                        </span>
                        <span class="text-xs text-zinc-400">Unit Transfusi Darah (PMI)</span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">
                        Selamat Datang di <span class="text-red-500">DarahKitaa</span>
                    </h1>
                    <p class="text-sm text-zinc-300 mt-1 max-w-2xl">
                        Kelola stok kantong darah per golongan, jadwalkan aksi donor darah, verifikasi kelayakan pendonor, dan proses permohonan darah darurat rumah sakit secara terintegrasi.
                    </p>
                </div>

                <!-- Emergency Alert Banner if any emergency requests exist -->
                <div v-if="stats.emergency_requests_count > 0" class="shrink-0 flex items-center gap-3 p-3.5 rounded-xl bg-red-600/20 border border-red-500/50 backdrop-blur">
                    <ShieldAlert class="size-8 text-red-500 animate-bounce" />
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-red-300">Panggilan Darurat Cito!</div>
                        <div class="text-sm font-semibold text-white">{{ stats.emergency_requests_count }} Permintaan Rumah Sakit</div>
                        <Link href="/requests?urgency=emergency" class="text-xs text-red-400 hover:text-red-200 underline font-medium">
                            Tinjau Sekarang &rarr;
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1. Interactive Flowchart from image -->
        <FlowchartDiagram :stats="stats" />

        <!-- 2. Key Metric Stat Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Card 1: Total Stok Darah -->
            <Link href="/stocks" class="p-4 rounded-xl bg-card border border-border hover:border-red-500/50 transition group shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-muted-foreground">Total Stok Darah</span>
                    <div class="size-8 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center">
                        <Droplets class="size-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-foreground">{{ stats.total_stock_units }}</span>
                    <span class="text-xs text-muted-foreground">kantong</span>
                </div>
                <div class="mt-2 text-[11px] text-muted-foreground flex items-center gap-1 group-hover:text-red-600 transition">
                    <span>Lihat rincian golongan &rarr;</span>
                </div>
            </Link>

            <!-- Card 2: Pendonor Terdaftar -->
            <Link href="/donors" class="p-4 rounded-xl bg-card border border-border hover:border-amber-500/50 transition group shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-muted-foreground">Total Pendonor</span>
                    <div class="size-8 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                        <Users class="size-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-foreground">{{ stats.total_donors }}</span>
                    <span class="text-xs text-muted-foreground">orang</span>
                </div>
                <div class="mt-2 text-[11px] text-amber-600 dark:text-amber-400 flex items-center gap-1 font-medium">
                    <span v-if="stats.pending_verification_donors > 0">{{ stats.pending_verification_donors }} butuh verifikasi</span>
                    <span v-else>Semua terverifikasi</span>
                </div>
            </Link>

            <!-- Card 3: Jadwal Event Donor -->
            <Link href="/events" class="p-4 rounded-xl bg-card border border-border hover:border-teal-500/50 transition group shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-muted-foreground">Jadwal Event</span>
                    <div class="size-8 rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                        <CalendarDays class="size-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-foreground">{{ stats.active_events_count }}</span>
                    <span class="text-xs text-muted-foreground">event aktif</span>
                </div>
                <div class="mt-2 text-[11px] text-muted-foreground group-hover:text-teal-600 transition">
                    <span>Buka absensi donor &rarr;</span>
                </div>
            </Link>

            <!-- Card 4: Permintaan Rumah Sakit -->
            <Link href="/requests" class="p-4 rounded-xl bg-card border border-border hover:border-blue-500/50 transition group shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-muted-foreground">Permintaan RS</span>
                    <div class="size-8 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <Building2 class="size-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-foreground">{{ stats.pending_requests_count }}</span>
                    <span class="text-xs text-muted-foreground">menunggu</span>
                </div>
                <div class="mt-2 text-[11px] text-muted-foreground group-hover:text-blue-600 transition">
                    <span>Setujui / tolak pesanan &rarr;</span>
                </div>
            </Link>
        </div>

        <!-- 3. Modul Stok Darah: Kesiapan Stok Darah Per Golongan (Grid Visual) -->
        <div class="rounded-2xl border border-border bg-card p-5 md:p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-border/80 gap-3">
                <div>
                    <h2 class="font-bold text-lg text-foreground flex items-center gap-2">
                        <Droplets class="size-5 text-red-600" />
                        <span>Kesiapan Stok Darah per Golongan</span>
                    </h2>
                    <p class="text-xs text-muted-foreground">
                        Inventaris kantong darah terpilah berdasarkan golongan, rhesus, dan komponen (WB, PRC, TC, FFP)
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Link 
                        href="/stocks"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-medium shadow-sm transition"
                    >
                        <Plus class="size-3.5" /> Update Stok Darah
                    </Link>
                </div>
            </div>

            <!-- Blood Groups 8 Cards Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3 pt-4">
                <div 
                    v-for="group in stockOverview" 
                    :key="group.blood_type_full"
                    class="rounded-xl border p-3 flex flex-col justify-between transition hover:shadow-md"
                    :class="{
                        'border-emerald-500/30 bg-emerald-500/5': group.status === 'safe',
                        'border-amber-500/40 bg-amber-500/5': group.status === 'warning',
                        'border-red-500/50 bg-red-500/10 animate-pulse': group.status === 'critical'
                    }"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xl font-extrabold" :class="{
                            'text-emerald-700 dark:text-emerald-400': group.status === 'safe',
                            'text-amber-700 dark:text-amber-400': group.status === 'warning',
                            'text-red-700 dark:text-red-400': group.status === 'critical'
                        }">
                            {{ group.blood_type_full }}
                        </span>
                        <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded" :class="{
                            'bg-emerald-500/20 text-emerald-800 dark:text-emerald-300': group.status === 'safe',
                            'bg-amber-500/20 text-amber-800 dark:text-amber-300': group.status === 'warning',
                            'bg-red-500/30 text-red-800 dark:text-red-200': group.status === 'critical'
                        }">
                            {{ group.status === 'safe' ? 'Aman' : group.status === 'warning' ? 'Menipis' : 'Kritis' }}
                        </span>
                    </div>

                    <div class="my-3 text-center">
                        <div class="text-2xl font-black text-foreground">{{ group.total_units }}</div>
                        <div class="text-[10px] text-muted-foreground">kantong</div>
                    </div>

                    <!-- Component mini pills -->
                    <div class="grid grid-cols-2 gap-1 text-[10px] text-muted-foreground pt-2 border-t border-border/50">
                        <div class="flex justify-between">
                            <span>WB:</span>
                            <span class="font-semibold text-foreground">{{ group.components.WB ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>PRC:</span>
                            <span class="font-semibold text-foreground">{{ group.components.PRC ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>TC:</span>
                            <span class="font-semibold text-foreground">{{ group.components.TC ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>FFP:</span>
                            <span class="font-semibold text-foreground">{{ group.components.FFP ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Two Columns: Left (Permintaan RS) | Right (Jadwal Event & Aktivitas) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Left: Permintaan Rumah Sakit (Aksi Admin: Setujui / Tolak) -->
            <div class="rounded-2xl border border-border bg-card p-5 flex flex-col shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-border">
                    <div class="flex items-center gap-2">
                        <Building2 class="size-5 text-blue-600" />
                        <h3 class="font-bold text-base text-foreground">Permintaan Darah Rumah Sakit</h3>
                    </div>
                    <Link href="/requests" class="text-xs text-blue-600 hover:underline font-medium">
                        Lihat Semua &rarr;
                    </Link>
                </div>

                <div class="divide-y divide-border/60 flex-1">
                    <div 
                        v-for="req in recentRequests" 
                        :key="req.id" 
                        class="py-3 flex items-start justify-between gap-3 text-sm"
                    >
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-foreground">{{ req.hospital_name }}</span>
                                <span 
                                    class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded"
                                    :class="{
                                        'bg-red-500/20 text-red-700 dark:text-red-400 border border-red-500/30': req.urgency === 'emergency',
                                        'bg-amber-500/20 text-amber-700 dark:text-amber-400': req.urgency === 'urgent',
                                        'bg-blue-500/10 text-blue-700 dark:text-blue-400': req.urgency === 'normal'
                                    }"
                                >
                                    {{ req.urgency === 'emergency' ? '🚨 Cito!' : req.urgency === 'urgent' ? 'Mendesak' : 'Biasa' }}
                                </span>
                            </div>
                            <div class="text-xs text-muted-foreground">
                                Pasien: <span class="text-foreground font-medium">{{ req.patient_name }}</span> • 
                                Butuh: <span class="font-bold text-red-600">{{ req.units_requested }} kantong {{ req.blood_type_full }} ({{ req.component }})</span>
                            </div>
                        </div>

                        <!-- Actions based on status -->
                        <div class="shrink-0 flex items-center gap-1.5">
                            <template v-if="req.status === 'pending'">
                                <button 
                                    @click="openApproveModal(req)"
                                    class="px-2.5 py-1 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium transition shadow-sm"
                                    title="Setujui Permintaan"
                                >
                                    Setujui
                                </button>
                                <button 
                                    @click="openRejectModal(req)"
                                    class="px-2 py-1 rounded-md bg-zinc-200 hover:bg-red-50 dark:bg-zinc-800 dark:hover:bg-red-950/40 text-red-600 text-xs font-medium transition"
                                    title="Tolak Permintaan"
                                >
                                    Tolak
                                </button>
                            </template>
                            <span 
                                v-else 
                                class="text-xs px-2 py-0.5 rounded font-medium"
                                :class="{
                                    'bg-emerald-500/10 text-emerald-600': req.status === 'approved' || req.status === 'completed',
                                    'bg-zinc-500/10 text-zinc-500 line-through': req.status === 'rejected'
                                }"
                            >
                                {{ req.status === 'approved' ? 'Disetujui' : req.status === 'completed' ? 'Selesai' : 'Ditolak' }}
                            </span>
                        </div>
                    </div>

                    <div v-if="recentRequests.length === 0" class="py-8 text-center text-sm text-muted-foreground">
                        Belum ada permintaan darah dari rumah sakit.
                    </div>
                </div>
            </div>

            <!-- Right: Jadwal Event Donor & Aksi Absensi -->
            <div class="rounded-2xl border border-border bg-card p-5 flex flex-col shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-border">
                    <div class="flex items-center gap-2">
                        <CalendarDays class="size-5 text-amber-600" />
                        <h3 class="font-bold text-base text-foreground">Jadwal Event Donor Darah</h3>
                    </div>
                    <Link href="/events" class="text-xs text-amber-600 hover:underline font-medium">
                        Semua Event &rarr;
                    </Link>
                </div>

                <div class="divide-y divide-border/60 flex-1">
                    <div 
                        v-for="evt in recentEvents" 
                        :key="evt.id" 
                        class="py-3 flex items-start justify-between gap-3 text-sm"
                    >
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-foreground">{{ evt.title }}</span>
                                <span 
                                    class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded"
                                    :class="{
                                        'bg-emerald-500/20 text-emerald-700': evt.status === 'ongoing',
                                        'bg-blue-500/10 text-blue-700': evt.status === 'upcoming',
                                        'bg-zinc-500/10 text-zinc-600': evt.status === 'completed'
                                    }"
                                >
                                    {{ evt.status === 'ongoing' ? 'Sedang Berlangsung' : evt.status === 'upcoming' ? 'Mendatang' : 'Selesai' }}
                                </span>
                            </div>
                            <div class="text-xs text-muted-foreground">
                                📍 {{ evt.location }} • 🗓️ {{ evt.event_date }} ({{ evt.start_time.slice(0, 5) }} - {{ evt.end_time.slice(0, 5) }})
                            </div>
                        </div>

                        <!-- Action: Direct to Absensi -->
                        <Link 
                            :href="`/events/${evt.id}`"
                            class="shrink-0 px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-medium transition shadow-sm flex items-center gap-1"
                        >
                            <span>Absensi Donor</span>
                            <ArrowUpRight class="size-3" />
                        </Link>
                    </div>

                    <div v-if="recentEvents.length === 0" class="py-8 text-center text-sm text-muted-foreground">
                        Belum ada event donor darah yang dijadwalkan.
                    </div>
                </div>

                <!-- Donor Recall Fast Banner -->
                <div class="mt-4 p-3.5 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-blue-700 dark:text-blue-300">Pengingat Donor Kembali (> 60 Hari)</div>
                        <div class="text-xs text-muted-foreground mt-0.5">
                            Ada <span class="font-bold text-foreground">{{ stats.eligible_recall_count }} pendonor</span> yang sudah memenuhi syarat waktu donor kembali.
                        </div>
                    </div>
                    <Link 
                        href="/reports"
                        class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium transition shadow-sm"
                    >
                        <Send class="size-3" /> Kirim Pengingat
                    </Link>
                </div>
            </div>

        </div>

        <!-- Approve Modal -->
        <div v-if="actionType === 'approve' && selectedRequest" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
            <div class="bg-card border border-border rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                        <CheckCircle2 class="size-6" />
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-foreground">Setujui Permintaan Darah</h3>
                        <p class="text-xs text-muted-foreground">{{ selectedRequest.request_code }} - {{ selectedRequest.hospital_name }}</p>
                    </div>
                </div>

                <div class="p-3 rounded-lg bg-muted/60 text-xs space-y-1">
                    <div><strong>Pasien:</strong> {{ selectedRequest.patient_name }}</div>
                    <div><strong>Kebutuhan:</strong> {{ selectedRequest.units_requested }} kantong {{ selectedRequest.blood_type_full }} ({{ selectedRequest.component }})</div>
                    <div class="text-emerald-600 dark:text-emerald-400 font-medium pt-1">
                        ✓ Stok darah akan dipotong otomatis setelah persetujuan dikonfirmasi.
                    </div>
                </div>

                <form @submit.prevent="submitApprove" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-foreground mb-1">Penerima Serah Terima / Kurir Medis RS</label>
                        <input 
                            v-model="approveForm.handover_recipient"
                            type="text" 
                            placeholder="Contoh: Bpk. Joko - Tim Ambulans RS" 
                            class="w-full text-xs rounded-lg border border-input bg-background px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-foreground mb-1">Catatan Tambahan (Opsional)</label>
                        <textarea 
                            v-model="approveForm.notes"
                            rows="2" 
                            placeholder="Catatan penyerahan atau kondisi darah..." 
                            class="w-full text-xs rounded-lg border border-input bg-background px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button 
                            type="button" 
                            @click="closeActionModal"
                            class="px-4 py-2 rounded-lg border border-input text-xs font-medium hover:bg-muted transition"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            :disabled="approveForm.processing"
                            class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium transition"
                        >
                            Konfirmasi & Setujui
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reject Modal -->
        <div v-if="actionType === 'reject' && selectedRequest" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
            <div class="bg-card border border-border rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-full bg-red-500/10 text-red-600 flex items-center justify-center">
                        <XCircle class="size-6" />
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-foreground">Tolak Permintaan Darah</h3>
                        <p class="text-xs text-muted-foreground">{{ selectedRequest.request_code }} - {{ selectedRequest.hospital_name }}</p>
                    </div>
                </div>

                <form @submit.prevent="submitReject" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-foreground mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                        <textarea 
                            v-model="rejectForm.rejection_reason"
                            rows="3" 
                            required
                            placeholder="Contoh: Stok darah golongan terkait sedang kritis atau dokumen rujukan RS belum lengkap..." 
                            class="w-full text-xs rounded-lg border border-input bg-background px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-red-500"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button 
                            type="button" 
                            @click="closeActionModal"
                            class="px-4 py-2 rounded-lg border border-input text-xs font-medium hover:bg-muted transition"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            :disabled="rejectForm.processing"
                            class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-medium transition"
                        >
                            Simpan Penolakan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>
