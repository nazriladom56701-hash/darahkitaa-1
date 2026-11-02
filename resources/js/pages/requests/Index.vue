<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    Building2, 
    PlusCircle, 
    Search, 
    ShieldAlert, 
    CheckCircle2, 
    XCircle, 
    Clock, 
    Phone, 
    FileText, 
    Check, 
    AlertTriangle,
    Truck,
    CheckCheck
} from '@lucide/vue';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Permintaan RS', href: '/requests' },
        ],
    },
});

interface HospitalRequestItem {
    id: number;
    request_code: string;
    hospital_name: string;
    doctor_in_charge: string;
    contact_phone: string;
    patient_name: string;
    medical_record_number: string;
    blood_type: string;
    rhesus: string;
    blood_type_full: string;
    component: string;
    units_requested: number;
    urgency: 'normal' | 'urgent' | 'emergency';
    diagnosis_notes?: string;
    status: 'pending' | 'approved' | 'rejected' | 'completed';
    rejection_reason?: string;
    action_at?: string;
    handover_recipient?: string;
    created_at: string;
    officer?: { name: string };
}

const props = defineProps<{
    requests: {
        data: HospitalRequestItem[];
        current_page: number;
        last_page: number;
        total: number;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: {
        search?: string;
        status?: string;
        urgency?: string;
    };
    counts: {
        total: number;
        pending: number;
        approved: number;
        rejected: number;
        completed: number;
        emergency: number;
    };
    currentStocks: Record<string, number>;
}>();

const searchQuery = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || '');
const selectedUrgency = ref(props.filters.urgency || '');

function applyFilters() {
    router.get('/requests', {
        search: searchQuery.value || undefined,
        status: selectedStatus.value || undefined,
        urgency: selectedUrgency.value || undefined,
    }, { preserveState: true, replace: true });
}

function setStatusFilter(status: string) {
    selectedStatus.value = status;
    applyFilters();
}

// Check stock availability
function getAvailableStock(req: HospitalRequestItem): number {
    const key = `${req.blood_type}_${req.rhesus}_${req.component}`;
    return props.currentStocks[key] ?? 0;
}

// Modal State: Aksi Admin: Setujui Permintaan
const isApproveModalOpen = ref(false);
const activeRequestForAction = ref<HospitalRequestItem | null>(null);
const approveForm = useForm({
    handover_recipient: '',
    notes: '',
});

function openApproveModal(req: HospitalRequestItem) {
    activeRequestForAction.value = req;
    approveForm.reset();
    isApproveModalOpen.value = true;
}

function submitApprove() {
    if (!activeRequestForAction.value) return;
    approveForm.post(`/requests/${activeRequestForAction.value.id}/approve`, {
        onSuccess: () => {
            isApproveModalOpen.value = false;
            activeRequestForAction.value = null;
        },
    });
}

// Modal State: Aksi Admin: Tolak Permintaan
const isRejectModalOpen = ref(false);
const rejectForm = useForm({
    rejection_reason: '',
});

function openRejectModal(req: HospitalRequestItem) {
    activeRequestForAction.value = req;
    rejectForm.reset();
    isRejectModalOpen.value = true;
}

function submitReject() {
    if (!activeRequestForAction.value) return;
    rejectForm.post(`/requests/${activeRequestForAction.value.id}/reject`, {
        onSuccess: () => {
            isRejectModalOpen.value = false;
            activeRequestForAction.value = null;
        },
    });
}

// Complete Handover
function completeRequest(req: HospitalRequestItem) {
    if (confirm(`Konfirmasi serah terima darah untuk permohonan ${req.request_code} (${req.hospital_name})?`)) {
        router.post(`/requests/${req.id}/complete`);
    }
}

// Modal State: Input Permintaan Baru
const isCreateOpen = ref(false);
const createForm = useForm({
    hospital_name: '',
    doctor_in_charge: '',
    contact_phone: '',
    patient_name: '',
    medical_record_number: '',
    blood_type: 'O',
    rhesus: '+',
    component: 'WB',
    units_requested: 2,
    urgency: 'urgent',
    diagnosis_notes: '',
});

function submitCreate() {
    createForm.post('/requests', {
        onSuccess: () => {
            isCreateOpen.value = false;
            createForm.reset();
        },
    });
}
</script>

<template>
    <Head title="Permintaan Rumah Sakit - DarahKitaa" />

    <div class="flex flex-col gap-6 p-4 md:p-6 max-w-7xl mx-auto w-full">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400">
                        <Building2 class="size-5" />
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl md:text-2xl font-extrabold text-foreground tracking-tight">Permintaan Rumah Sakit</h1>
                            <span class="text-xs bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-semibold px-2 py-0.5 rounded border border-emerald-500/20">
                                Aksi Admin: Setujui / Tolak
                            </span>
                        </div>
                        <p class="text-xs text-muted-foreground mt-0.5">
                            Kelola permintaan pasokan darah dari RS dan Faskes, verifikasi ketersediaan stok darah, dan alokasikan kantong darah.
                        </p>
                    </div>
                </div>
            </div>

            <button
                @click="isCreateOpen = true"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition"
            >
                <PlusCircle class="size-4" /> Input Permintaan Baru
            </button>
        </div>

        <!-- Filter Status Tabs -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <button
                @click="setStatusFilter('')"
                class="p-3 rounded-xl border text-left transition flex items-center justify-between"
                :class="selectedStatus === '' ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-border bg-card hover:border-border/80'"
            >
                <div>
                    <div class="text-[11px] text-muted-foreground">Semua Permintaan</div>
                    <div class="text-lg font-bold text-foreground">{{ counts.total }}</div>
                </div>
                <Building2 class="size-4 text-muted-foreground" />
            </button>

            <button
                @click="setStatusFilter('pending')"
                class="p-3 rounded-xl border text-left transition flex items-center justify-between"
                :class="selectedStatus === 'pending' ? 'border-amber-500 bg-amber-500/10 ring-1 ring-amber-500' : 'border-border bg-card hover:border-border/80'"
            >
                <div>
                    <div class="text-[11px] text-amber-600 font-medium">Menunggu Persetujuan</div>
                    <div class="text-lg font-bold text-foreground">{{ counts.pending }}</div>
                </div>
                <Clock class="size-4 text-amber-500" />
            </button>

            <button
                @click="setStatusFilter('approved')"
                class="p-3 rounded-xl border text-left transition flex items-center justify-between"
                :class="selectedStatus === 'approved' ? 'border-emerald-500 bg-emerald-500/10 ring-1 ring-emerald-500' : 'border-border bg-card hover:border-border/80'"
            >
                <div>
                    <div class="text-[11px] text-emerald-600 font-medium">Disetujui (Siap Kirim)</div>
                    <div class="text-lg font-bold text-foreground">{{ counts.approved }}</div>
                </div>
                <CheckCircle2 class="size-4 text-emerald-500" />
            </button>

            <button
                @click="setStatusFilter('completed')"
                class="p-3 rounded-xl border text-left transition flex items-center justify-between"
                :class="selectedStatus === 'completed' ? 'border-blue-500 bg-blue-500/10 ring-1 ring-blue-500' : 'border-border bg-card hover:border-border/80'"
            >
                <div>
                    <div class="text-[11px] text-blue-600 font-medium">Selesai / Terkirim</div>
                    <div class="text-lg font-bold text-foreground">{{ counts.completed }}</div>
                </div>
                <Truck class="size-4 text-blue-500" />
            </button>

            <button
                @click="setStatusFilter('rejected')"
                class="p-3 rounded-xl border text-left transition flex items-center justify-between"
                :class="selectedStatus === 'rejected' ? 'border-red-500 bg-red-500/10 ring-1 ring-red-500' : 'border-border bg-card hover:border-border/80'"
            >
                <div>
                    <div class="text-[11px] text-red-600 font-medium">Ditolak</div>
                    <div class="text-lg font-bold text-foreground">{{ counts.rejected }}</div>
                </div>
                <XCircle class="size-4 text-red-500" />
            </button>
        </div>

        <!-- Filter bar -->
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <Search class="size-4 absolute left-3 top-3 text-muted-foreground" />
                <input
                    v-model="searchQuery"
                    @keyup.enter="applyFilters"
                    type="text"
                    placeholder="Cari berdasarkan nama rumah sakit, nomor RM, kode permintaan, atau nama pasien..."
                    class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl border border-input bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm"
                />
            </div>

            <select
                v-model="selectedUrgency"
                @change="applyFilters"
                class="text-xs rounded-xl border border-input bg-card px-3 py-2.5 text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm"
            >
                <option value="">Semua Tingkat Urgensi</option>
                <option value="emergency">🚨 Darurat Cito! (Emergency)</option>
                <option value="urgent">⚠️ Mendesak (Urgent)</option>
                <option value="normal">Normal (Elektif)</option>
            </select>

            <button
                @click="applyFilters"
                class="px-4 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-medium transition"
            >
                Terapkan
            </button>
        </div>

        <!-- Table of Requests -->
        <div class="rounded-2xl border border-border bg-card overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-muted/40 border-b border-border text-muted-foreground uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Kode & Rumah Sakit</th>
                            <th class="px-4 py-3 font-semibold">Data Pasien & RM</th>
                            <th class="px-4 py-3 font-semibold">Kebutuhan Darah</th>
                            <th class="px-4 py-3 font-semibold">Ketersediaan Stok Saat Ini</th>
                            <th class="px-4 py-3 font-semibold">Urgensi</th>
                            <th class="px-4 py-3 font-semibold">Status Permintaan</th>
                            <th class="px-4 py-3 font-semibold text-right">Aksi Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        <tr v-for="req in requests.data" :key="req.id" class="hover:bg-muted/20 transition">
                            <!-- Column 1: Hospital -->
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-foreground text-sm">{{ req.hospital_name }}</div>
                                <div class="text-[10px] font-mono text-muted-foreground">{{ req.request_code }}</div>
                                <div class="text-[10px] text-muted-foreground flex items-center gap-1 mt-0.5">
                                    <Phone class="size-3 text-muted-foreground" /> {{ req.contact_phone }} (Dokter: {{ req.doctor_in_charge }})
                                </div>
                            </td>

                            <!-- Column 2: Patient -->
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-foreground">{{ req.patient_name }}</div>
                                <div class="text-[10px] text-muted-foreground font-mono">RM: {{ req.medical_record_number }}</div>
                                <div v-if="req.diagnosis_notes" class="text-[10px] text-muted-foreground truncate max-w-xs mt-0.5" :title="req.diagnosis_notes">
                                    "{{ req.diagnosis_notes }}"
                                </div>
                            </td>

                            <!-- Column 3: Blood Need -->
                            <td class="px-4 py-3.5">
                                <div class="space-y-0.5">
                                    <span class="inline-block font-black text-sm px-2 py-0.5 rounded bg-red-500/10 text-red-600 border border-red-500/20">
                                        {{ req.blood_type_full }}
                                    </span>
                                    <div class="text-[11px] font-bold text-foreground">
                                        {{ req.units_requested }} kantong ({{ req.component }})
                                    </div>
                                </div>
                            </td>

                            <!-- Column 4: Available stock check -->
                            <td class="px-4 py-3.5">
                                <div class="space-y-0.5">
                                    <div class="text-[11px] font-semibold flex items-center gap-1" :class="getAvailableStock(req) >= req.units_requested ? 'text-emerald-600' : 'text-red-600'">
                                        <span v-if="getAvailableStock(req) >= req.units_requested">✓ Stok Cukup:</span>
                                        <span v-else>⚠️ Stok Tidak Cukup:</span>
                                        <span>{{ getAvailableStock(req) }} kantong</span>
                                    </div>
                                    <div class="text-[10px] text-muted-foreground">
                                        Persediaan {{ req.blood_type_full }} ({{ req.component }})
                                    </div>
                                </div>
                            </td>

                            <!-- Column 5: Urgency -->
                            <td class="px-4 py-3.5">
                                <span
                                    class="inline-block text-[10px] font-bold uppercase px-2 py-0.5 rounded-md"
                                    :class="{
                                        'bg-red-500/20 text-red-700 dark:text-red-400 border border-red-500/40 animate-pulse font-extrabold': req.urgency === 'emergency',
                                        'bg-amber-500/20 text-amber-700 dark:text-amber-400': req.urgency === 'urgent',
                                        'bg-blue-500/10 text-blue-700 dark:text-blue-400': req.urgency === 'normal'
                                    }"
                                >
                                    {{ req.urgency === 'emergency' ? '🚨 Cito! (Darurat)' : req.urgency === 'urgent' ? '⚠️ Mendesak' : 'Biasa' }}
                                </span>
                            </td>

                            <!-- Column 6: Status -->
                            <td class="px-4 py-3.5">
                                <span 
                                    class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full"
                                    :class="{
                                        'bg-amber-500/10 text-amber-700 border border-amber-500/20': req.status === 'pending',
                                        'bg-emerald-500/10 text-emerald-700 border border-emerald-500/20': req.status === 'approved',
                                        'bg-blue-500/10 text-blue-700 border border-blue-500/20': req.status === 'completed',
                                        'bg-red-500/10 text-red-700 border border-red-500/20': req.status === 'rejected'
                                    }"
                                >
                                    <Clock v-if="req.status === 'pending'" class="size-3" />
                                    <CheckCircle2 v-else-if="req.status === 'approved'" class="size-3" />
                                    <Truck v-else-if="req.status === 'completed'" class="size-3" />
                                    <XCircle v-else class="size-3" />
                                    <span>
                                        {{ req.status === 'pending' ? 'Menunggu Admin' : req.status === 'approved' ? 'Disetujui' : req.status === 'completed' ? 'Selesai Terkirim' : 'Ditolak' }}
                                    </span>
                                </span>
                                <div v-if="req.rejection_reason" class="text-[10px] text-red-600 mt-1 truncate max-w-xs" :title="req.rejection_reason">
                                    Alasan: {{ req.rejection_reason }}
                                </div>
                                <div v-if="req.handover_recipient" class="text-[10px] text-muted-foreground mt-1 truncate max-w-xs">
                                    Kurir: {{ req.handover_recipient }}
                                </div>
                            </td>

                            <!-- Column 7: Aksi Admin: Setujui / Tolak -->
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Aksi saat status pending: Setujui / Tolak -->
                                    <template v-if="req.status === 'pending'">
                                        <button
                                            @click="openApproveModal(req)"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition"
                                            title="Setujui dan Alokasikan Kantong Darah"
                                        >
                                            <Check class="size-3.5" />
                                            <span>Setujui</span>
                                        </button>

                                        <button
                                            @click="openRejectModal(req)"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-border bg-card hover:bg-red-500/10 hover:border-red-500 text-red-600 font-medium text-xs transition"
                                            title="Tolak Permintaan dengan Alasan"
                                        >
                                            <XCircle class="size-3.5" />
                                            <span>Tolak</span>
                                        </button>
                                    </template>

                                    <!-- Aksi saat status approved: Serah Terima / Selesai -->
                                    <template v-else-if="req.status === 'approved'">
                                        <button
                                            @click="completeRequest(req)"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition shadow-sm"
                                            title="Selesaikan Penyerahan Darah"
                                        >
                                            <Truck class="size-3.5" />
                                            <span>Serah Terima</span>
                                        </button>
                                    </template>

                                    <span v-else class="text-xs text-muted-foreground">
                                        -
                                    </span>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="requests.data.length === 0">
                            <td colspan="7" class="py-12 text-center text-sm text-muted-foreground">
                                Tidak ada data permohonan darah yang sesuai.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="requests.last_page > 1" class="p-3 border-t border-border flex items-center justify-between text-xs text-muted-foreground">
                <div>Menampilkan {{ requests.data.length }} dari {{ requests.total }} data</div>
                <div class="flex gap-1">
                    <Link
                        v-for="link in requests.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        class="px-2.5 py-1 rounded border text-xs"
                        :class="link.active ? 'bg-primary text-primary-foreground border-primary font-bold' : 'border-border hover:bg-muted'"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>

        <!-- Modal Aksi Admin: Setujui Permintaan -->
        <div v-if="isApproveModalOpen && activeRequestForAction" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
            <div class="bg-card border border-border rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                        <CheckCircle2 class="size-6" />
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-foreground">Setujui Permintaan Darah RS</h3>
                        <p class="text-xs text-muted-foreground">{{ activeRequestForAction.request_code }} • {{ activeRequestForAction.hospital_name }}</p>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-muted/40 border border-border text-xs space-y-1.5">
                    <div><strong>Pasien:</strong> {{ activeRequestForAction.patient_name }} (RM: {{ activeRequestForAction.medical_record_number }})</div>
                    <div><strong>Dibutuhkan:</strong> {{ activeRequestForAction.units_requested }} kantong {{ activeRequestForAction.blood_type_full }} ({{ activeRequestForAction.component }})</div>
                    <div><strong>Ketersediaan Stok UDD:</strong> <span class="font-bold text-emerald-600">{{ getAvailableStock(activeRequestForAction) }} kantong</span></div>
                    <div class="text-emerald-700 dark:text-emerald-300 font-medium pt-1 border-t border-border/50">
                        ✓ Setelah disetujui, inventaris kantong darah akan otomatis terpotong {{ activeRequestForAction.units_requested }} unit.
                    </div>
                </div>

                <form @submit.prevent="submitApprove" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1">Nama Penerima / Kurir Medis RS <span class="text-red-500">*</span></label>
                        <input
                            v-model="approveForm.handover_recipient"
                            type="text"
                            required
                            placeholder="Contoh: Bpk. Joko - Tim Ambulans RS Cipto"
                            class="w-full text-xs rounded-xl border border-input bg-card px-3 py-2 text-foreground"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1">Catatan Serah Terima / Nomor Boks Darah</label>
                        <textarea
                            v-model="approveForm.notes"
                            rows="2"
                            placeholder="Catatan suhu coolbox, es batu gel, atau kondisi kemasan..."
                            class="w-full text-xs rounded-xl border border-input bg-background p-2.5 text-foreground"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="isApproveModalOpen = false" class="px-4 py-2 rounded-xl border border-input text-xs font-medium hover:bg-muted">Batal</button>
                        <button type="submit" :disabled="approveForm.processing" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm">
                            Konfirmasi & Setujui Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Aksi Admin: Tolak Permintaan -->
        <div v-if="isRejectModalOpen && activeRequestForAction" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
            <div class="bg-card border border-border rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-full bg-red-500/10 text-red-600 flex items-center justify-center">
                        <XCircle class="size-6" />
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-foreground">Tolak Permintaan Darah RS</h3>
                        <p class="text-xs text-muted-foreground">{{ activeRequestForAction.request_code }} • {{ activeRequestForAction.hospital_name }}</p>
                    </div>
                </div>

                <form @submit.prevent="submitReject" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1">Alasan Penolakan Permintaan <span class="text-red-500">*</span></label>
                        <textarea
                            v-model="rejectForm.rejection_reason"
                            rows="3"
                            required
                            placeholder="Contoh: Stok darah golongan terkait sedang kritis (< 2 unit) sehingga diprioritaskan untuk penanganan IGD darurat lokal, atau dokumen rujukan RS belum lengkap..."
                            class="w-full text-xs rounded-xl border border-input bg-background p-3 text-foreground focus:outline-none focus:ring-2 focus:ring-red-500"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="isRejectModalOpen = false" class="px-4 py-2 rounded-xl border border-input text-xs font-medium hover:bg-muted">Batal</button>
                        <button type="submit" :disabled="rejectForm.processing" class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-sm">
                            Simpan Penolakan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Input Permintaan Baru -->
        <div v-if="isCreateOpen" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
            <div class="bg-card border border-border rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-border">
                    <div class="flex items-center gap-2">
                        <Building2 class="size-5 text-blue-600" />
                        <h3 class="font-bold text-base text-foreground">Formulir Permintaan Darah Rumah Sakit</h3>
                    </div>
                    <button @click="isCreateOpen = false" class="text-muted-foreground hover:text-foreground">✕</button>
                </div>

                <form @submit.prevent="submitCreate" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-medium text-foreground mb-1">Nama Rumah Sakit / Faskes <span class="text-red-500">*</span></label>
                        <input v-model="createForm.hospital_name" type="text" required placeholder="Contoh: RSUP Fatmawati / RS Jantung Harapan Kita" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Dokter Penanggung Jawab</label>
                            <input v-model="createForm.doctor_in_charge" type="text" required placeholder="dr. Sp.BTKV" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Nomor Telepon / Hotline RS</label>
                            <input v-model="createForm.contact_phone" type="text" required placeholder="021-xxxx / 0812xxxx" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Nama Pasien <span class="text-red-500">*</span></label>
                            <input v-model="createForm.patient_name" type="text" required placeholder="Nama lengkap pasien" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Nomor Rekam Medis (No RM) <span class="text-red-500">*</span></label>
                            <input v-model="createForm.medical_record_number" type="text" required placeholder="RM-xxxxx" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Golongan Darah</label>
                            <select v-model="createForm.blood_type" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2">
                                <option value="A">Gol. A</option>
                                <option value="B">Gol. B</option>
                                <option value="AB">Gol. AB</option>
                                <option value="O">Gol. O</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Rhesus</label>
                            <select v-model="createForm.rhesus" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2">
                                <option value="+">Positif (+)</option>
                                <option value="-">Negatif (-)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Komponen Darah</label>
                            <select v-model="createForm.component" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2">
                                <option value="WB">Whole Blood (WB)</option>
                                <option value="PRC">Packed Red (PRC)</option>
                                <option value="TC">Thrombocyte (TC)</option>
                                <option value="FFP">Fresh Plasma (FFP)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Jumlah Kantong Diminta <span class="text-red-500">*</span></label>
                            <input v-model="createForm.units_requested" type="number" min="1" required class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Tingkat Urgensi</label>
                            <select v-model="createForm.urgency" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2 font-bold">
                                <option value="emergency">🚨 Darurat Cito! (Operasi Segera)</option>
                                <option value="urgent">⚠️ Mendesak (Dalam 24 Jam)</option>
                                <option value="normal">Normal (Operasi Terjadwal)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-medium text-foreground mb-1">Indikasi Klinis / Diagnosis Dokter</label>
                        <textarea v-model="createForm.diagnosis_notes" rows="2" placeholder="Contoh: Pendarahan pasca partum, operasi bedah toraks, trombositopenia..." class="w-full text-xs rounded-lg border border-input bg-card p-2"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="isCreateOpen = false" class="px-4 py-2 rounded-xl border border-input text-xs font-medium hover:bg-muted">Batal</button>
                        <button type="submit" :disabled="createForm.processing" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm">Simpan Permintaan RS</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>
