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
    CheckCheck,
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
    router.get(
        '/requests',
        {
            search: searchQuery.value || undefined,
            status: selectedStatus.value || undefined,
            urgency: selectedUrgency.value || undefined,
        },
        { preserveState: true, replace: true },
    );
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
    if (
        confirm(
            `Konfirmasi serah terima darah untuk permohonan ${req.request_code} (${req.hospital_name})?`,
        )
    ) {
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

    <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 p-4 md:p-6">
        <!-- Header -->
        <div
            class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
        >
            <div>
                <div class="flex items-center gap-2">
                    <span
                        class="rounded-lg bg-blue-500/10 p-2 text-blue-600 dark:text-blue-400"
                    >
                        <Building2 class="size-5" />
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1
                                class="text-xl font-extrabold tracking-tight text-foreground md:text-2xl"
                            >
                                Permintaan Rumah Sakit
                            </h1>
                            <span
                                class="rounded border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400"
                            >
                                Aksi Admin: Setujui / Tolak
                            </span>
                        </div>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            Kelola permintaan pasokan darah dari RS dan Faskes,
                            verifikasi ketersediaan stok darah, dan alokasikan
                            kantong darah.
                        </p>
                    </div>
                </div>
            </div>

            <button
                @click="isCreateOpen = true"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700"
            >
                <PlusCircle class="size-4" /> Input Permintaan Baru
            </button>
        </div>

        <!-- Filter Status Tabs -->
        <div class="grid grid-cols-2 gap-3 md:grid-cols-5">
            <button
                @click="setStatusFilter('')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition"
                :class="
                    selectedStatus === ''
                        ? 'border-primary bg-primary/5 ring-1 ring-primary'
                        : 'border-border bg-card hover:border-border/80'
                "
            >
                <div>
                    <div class="text-[11px] text-muted-foreground">
                        Semua Permintaan
                    </div>
                    <div class="text-lg font-bold text-foreground">
                        {{ counts.total }}
                    </div>
                </div>
                <Building2 class="size-4 text-muted-foreground" />
            </button>

            <button
                @click="setStatusFilter('pending')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition"
                :class="
                    selectedStatus === 'pending'
                        ? 'border-amber-500 bg-amber-500/10 ring-1 ring-amber-500'
                        : 'border-border bg-card hover:border-border/80'
                "
            >
                <div>
                    <div class="text-[11px] font-medium text-amber-600">
                        Menunggu Persetujuan
                    </div>
                    <div class="text-lg font-bold text-foreground">
                        {{ counts.pending }}
                    </div>
                </div>
                <Clock class="size-4 text-amber-500" />
            </button>

            <button
                @click="setStatusFilter('approved')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition"
                :class="
                    selectedStatus === 'approved'
                        ? 'border-emerald-500 bg-emerald-500/10 ring-1 ring-emerald-500'
                        : 'border-border bg-card hover:border-border/80'
                "
            >
                <div>
                    <div class="text-[11px] font-medium text-emerald-600">
                        Disetujui (Siap Kirim)
                    </div>
                    <div class="text-lg font-bold text-foreground">
                        {{ counts.approved }}
                    </div>
                </div>
                <CheckCircle2 class="size-4 text-emerald-500" />
            </button>

            <button
                @click="setStatusFilter('completed')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition"
                :class="
                    selectedStatus === 'completed'
                        ? 'border-blue-500 bg-blue-500/10 ring-1 ring-blue-500'
                        : 'border-border bg-card hover:border-border/80'
                "
            >
                <div>
                    <div class="text-[11px] font-medium text-blue-600">
                        Selesai / Terkirim
                    </div>
                    <div class="text-lg font-bold text-foreground">
                        {{ counts.completed }}
                    </div>
                </div>
                <Truck class="size-4 text-blue-500" />
            </button>

            <button
                @click="setStatusFilter('rejected')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition"
                :class="
                    selectedStatus === 'rejected'
                        ? 'border-red-500 bg-red-500/10 ring-1 ring-red-500'
                        : 'border-border bg-card hover:border-border/80'
                "
            >
                <div>
                    <div class="text-[11px] font-medium text-red-600">
                        Ditolak
                    </div>
                    <div class="text-lg font-bold text-foreground">
                        {{ counts.rejected }}
                    </div>
                </div>
                <XCircle class="size-4 text-red-500" />
            </button>
        </div>

        <!-- Filter bar -->
        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <Search
                    class="absolute top-3 left-3 size-4 text-muted-foreground"
                />
                <input
                    v-model="searchQuery"
                    @keyup.enter="applyFilters"
                    type="text"
                    placeholder="Cari berdasarkan nama rumah sakit, nomor RM, kode permintaan, atau nama pasien..."
                    class="w-full rounded-xl border border-input bg-card py-2.5 pr-4 pl-9 text-xs text-foreground shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                />
            </div>

            <select
                v-model="selectedUrgency"
                @change="applyFilters"
                class="rounded-xl border border-input bg-card px-3 py-2.5 text-xs text-foreground shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
            >
                <option value="">Semua Tingkat Urgensi</option>
                <option value="emergency">🚨 Darurat Cito! (Emergency)</option>
                <option value="urgent">⚠️ Mendesak (Urgent)</option>
                <option value="normal">Normal (Elektif)</option>
            </select>

            <button
                @click="applyFilters"
                class="rounded-xl bg-zinc-800 px-4 py-2.5 text-xs font-medium text-white transition hover:bg-zinc-700"
            >
                Terapkan
            </button>
        </div>

        <!-- Table of Requests -->
        <div
            class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead
                        class="border-b border-border bg-muted/40 text-[10px] tracking-wider text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-4 py-3 font-semibold">
                                Kode & Rumah Sakit
                            </th>
                            <th class="px-4 py-3 font-semibold">
                                Data Pasien & RM
                            </th>
                            <th class="px-4 py-3 font-semibold">
                                Kebutuhan Darah
                            </th>
                            <th class="px-4 py-3 font-semibold">
                                Ketersediaan Stok Saat Ini
                            </th>
                            <th class="px-4 py-3 font-semibold">Urgensi</th>
                            <th class="px-4 py-3 font-semibold">
                                Status Permintaan
                            </th>
                            <th class="px-4 py-3 text-right font-semibold">
                                Aksi Admin
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        <tr
                            v-for="req in requests.data"
                            :key="req.id"
                            class="transition hover:bg-muted/20"
                        >
                            <!-- Column 1: Hospital -->
                            <td class="px-4 py-3.5">
                                <div class="text-sm font-bold text-foreground">
                                    {{ req.hospital_name }}
                                </div>
                                <div
                                    class="font-mono text-[10px] text-muted-foreground"
                                >
                                    {{ req.request_code }}
                                </div>
                                <div
                                    class="mt-0.5 flex items-center gap-1 text-[10px] text-muted-foreground"
                                >
                                    <Phone
                                        class="size-3 text-muted-foreground"
                                    />
                                    {{ req.contact_phone }} (Dokter:
                                    {{ req.doctor_in_charge }})
                                </div>
                            </td>

                            <!-- Column 2: Patient -->
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-foreground">
                                    {{ req.patient_name }}
                                </div>
                                <div
                                    class="font-mono text-[10px] text-muted-foreground"
                                >
                                    RM: {{ req.medical_record_number }}
                                </div>
                                <div
                                    v-if="req.diagnosis_notes"
                                    class="mt-0.5 max-w-xs truncate text-[10px] text-muted-foreground"
                                    :title="req.diagnosis_notes"
                                >
                                    "{{ req.diagnosis_notes }}"
                                </div>
                            </td>

                            <!-- Column 3: Blood Need -->
                            <td class="px-4 py-3.5">
                                <div class="space-y-0.5">
                                    <span
                                        class="inline-block rounded border border-red-500/20 bg-red-500/10 px-2 py-0.5 text-sm font-black text-red-600"
                                    >
                                        {{ req.blood_type_full }}
                                    </span>
                                    <div
                                        class="text-[11px] font-bold text-foreground"
                                    >
                                        {{ req.units_requested }} kantong ({{
                                            req.component
                                        }})
                                    </div>
                                </div>
                            </td>

                            <!-- Column 4: Available stock check -->
                            <td class="px-4 py-3.5">
                                <div class="space-y-0.5">
                                    <div
                                        class="flex items-center gap-1 text-[11px] font-semibold"
                                        :class="
                                            getAvailableStock(req) >=
                                            req.units_requested
                                                ? 'text-emerald-600'
                                                : 'text-red-600'
                                        "
                                    >
                                        <span
                                            v-if="
                                                getAvailableStock(req) >=
                                                req.units_requested
                                            "
                                            >✓ Stok Cukup:</span
                                        >
                                        <span v-else>⚠️ Stok Tidak Cukup:</span>
                                        <span
                                            >{{
                                                getAvailableStock(req)
                                            }}
                                            kantong</span
                                        >
                                    </div>
                                    <div
                                        class="text-[10px] text-muted-foreground"
                                    >
                                        Persediaan {{ req.blood_type_full }} ({{
                                            req.component
                                        }})
                                    </div>
                                </div>
                            </td>

                            <!-- Column 5: Urgency -->
                            <td class="px-4 py-3.5">
                                <span
                                    class="inline-block rounded-md px-2 py-0.5 text-[10px] font-bold uppercase"
                                    :class="{
                                        'animate-pulse border border-red-500/40 bg-red-500/20 font-extrabold text-red-700 dark:text-red-400':
                                            req.urgency === 'emergency',
                                        'bg-amber-500/20 text-amber-700 dark:text-amber-400':
                                            req.urgency === 'urgent',
                                        'bg-blue-500/10 text-blue-700 dark:text-blue-400':
                                            req.urgency === 'normal',
                                    }"
                                >
                                    {{
                                        req.urgency === 'emergency'
                                            ? '🚨 Cito! (Darurat)'
                                            : req.urgency === 'urgent'
                                              ? '⚠️ Mendesak'
                                              : 'Biasa'
                                    }}
                                </span>
                            </td>

                            <!-- Column 6: Status -->
                            <td class="px-4 py-3.5">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                                    :class="{
                                        'border border-amber-500/20 bg-amber-500/10 text-amber-700':
                                            req.status === 'pending',
                                        'border border-emerald-500/20 bg-emerald-500/10 text-emerald-700':
                                            req.status === 'approved',
                                        'border border-blue-500/20 bg-blue-500/10 text-blue-700':
                                            req.status === 'completed',
                                        'border border-red-500/20 bg-red-500/10 text-red-700':
                                            req.status === 'rejected',
                                    }"
                                >
                                    <Clock
                                        v-if="req.status === 'pending'"
                                        class="size-3"
                                    />
                                    <CheckCircle2
                                        v-else-if="req.status === 'approved'"
                                        class="size-3"
                                    />
                                    <Truck
                                        v-else-if="req.status === 'completed'"
                                        class="size-3"
                                    />
                                    <XCircle v-else class="size-3" />
                                    <span>
                                        {{
                                            req.status === 'pending'
                                                ? 'Menunggu Admin'
                                                : req.status === 'approved'
                                                  ? 'Disetujui'
                                                  : req.status === 'completed'
                                                    ? 'Selesai Terkirim'
                                                    : 'Ditolak'
                                        }}
                                    </span>
                                </span>
                                <div
                                    v-if="req.rejection_reason"
                                    class="mt-1 max-w-xs truncate text-[10px] text-red-600"
                                    :title="req.rejection_reason"
                                >
                                    Alasan: {{ req.rejection_reason }}
                                </div>
                                <div
                                    v-if="req.handover_recipient"
                                    class="mt-1 max-w-xs truncate text-[10px] text-muted-foreground"
                                >
                                    Kurir: {{ req.handover_recipient }}
                                </div>
                            </td>

                            <!-- Column 7: Aksi Admin: Setujui / Tolak -->
                            <td class="px-4 py-3.5 text-right">
                                <div
                                    class="flex items-center justify-end gap-1.5"
                                >
                                    <!-- Aksi saat status pending: Setujui / Tolak -->
                                    <template v-if="req.status === 'pending'">
                                        <button
                                            @click="openApproveModal(req)"
                                            class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700"
                                            title="Setujui dan Alokasikan Kantong Darah"
                                        >
                                            <Check class="size-3.5" />
                                            <span>Setujui</span>
                                        </button>

                                        <button
                                            @click="openRejectModal(req)"
                                            class="inline-flex items-center gap-1 rounded-lg border border-border bg-card px-2.5 py-1.5 text-xs font-medium text-red-600 transition hover:border-red-500 hover:bg-red-500/10"
                                            title="Tolak Permintaan dengan Alasan"
                                        >
                                            <XCircle class="size-3.5" />
                                            <span>Tolak</span>
                                        </button>
                                    </template>

                                    <!-- Aksi saat status approved: Serah Terima / Selesai -->
                                    <template
                                        v-else-if="req.status === 'approved'"
                                    >
                                        <button
                                            @click="completeRequest(req)"
                                            class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700"
                                            title="Selesaikan Penyerahan Darah"
                                        >
                                            <Truck class="size-3.5" />
                                            <span>Serah Terima</span>
                                        </button>
                                    </template>

                                    <span
                                        v-else
                                        class="text-xs text-muted-foreground"
                                    >
                                        -
                                    </span>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="requests.data.length === 0">
                            <td
                                colspan="7"
                                class="py-12 text-center text-sm text-muted-foreground"
                            >
                                Tidak ada data permohonan darah yang sesuai.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="requests.last_page > 1"
                class="flex items-center justify-between border-t border-border p-3 text-xs text-muted-foreground"
            >
                <div>
                    Menampilkan {{ requests.data.length }} dari
                    {{ requests.total }} data
                </div>
                <div class="flex gap-1">
                    <Link
                        v-for="link in requests.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        class="rounded border px-2.5 py-1 text-xs"
                        :class="
                            link.active
                                ? 'border-primary bg-primary font-bold text-primary-foreground'
                                : 'border-border hover:bg-muted'
                        "
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>

        <!-- Modal Aksi Admin: Setujui Permintaan -->
        <div
            v-if="isApproveModalOpen && activeRequestForAction"
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
                            Setujui Permintaan Darah RS
                        </h3>
                        <p class="text-xs text-muted-foreground">
                            {{ activeRequestForAction.request_code }} •
                            {{ activeRequestForAction.hospital_name }}
                        </p>
                    </div>
                </div>

                <div
                    class="space-y-1.5 rounded-xl border border-border bg-muted/40 p-3 text-xs"
                >
                    <div>
                        <strong>Pasien:</strong>
                        {{ activeRequestForAction.patient_name }} (RM:
                        {{ activeRequestForAction.medical_record_number }})
                    </div>
                    <div>
                        <strong>Dibutuhkan:</strong>
                        {{ activeRequestForAction.units_requested }} kantong
                        {{ activeRequestForAction.blood_type_full }} ({{
                            activeRequestForAction.component
                        }})
                    </div>
                    <div>
                        <strong>Ketersediaan Stok UDD:</strong>
                        <span class="font-bold text-emerald-600"
                            >{{
                                getAvailableStock(activeRequestForAction)
                            }}
                            kantong</span
                        >
                    </div>
                    <div
                        class="border-t border-border/50 pt-1 font-medium text-emerald-700 dark:text-emerald-300"
                    >
                        ✓ Setelah disetujui, inventaris kantong darah akan
                        otomatis terpotong
                        {{ activeRequestForAction.units_requested }} unit.
                    </div>
                </div>

                <form @submit.prevent="submitApprove" class="space-y-3">
                    <div>
                        <label
                            class="mb-1 block text-xs font-semibold text-foreground"
                            >Nama Penerima / Kurir Medis RS
                            <span class="text-red-500">*</span></label
                        >
                        <input
                            v-model="approveForm.handover_recipient"
                            type="text"
                            required
                            placeholder="Contoh: Bpk. Joko - Tim Ambulans RS Cipto"
                            class="w-full rounded-xl border border-input bg-card px-3 py-2 text-xs text-foreground"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-xs font-semibold text-foreground"
                            >Catatan Serah Terima / Nomor Boks Darah</label
                        >
                        <textarea
                            v-model="approveForm.notes"
                            rows="2"
                            placeholder="Catatan suhu coolbox, es batu gel, atau kondisi kemasan..."
                            class="w-full rounded-xl border border-input bg-background p-2.5 text-xs text-foreground"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="isApproveModalOpen = false"
                            class="rounded-xl border border-input px-4 py-2 text-xs font-medium hover:bg-muted"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="approveForm.processing"
                            class="rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700"
                        >
                            Konfirmasi & Setujui Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Aksi Admin: Tolak Permintaan -->
        <div
            v-if="isRejectModalOpen && activeRequestForAction"
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
                            Tolak Permintaan Darah RS
                        </h3>
                        <p class="text-xs text-muted-foreground">
                            {{ activeRequestForAction.request_code }} •
                            {{ activeRequestForAction.hospital_name }}
                        </p>
                    </div>
                </div>

                <form @submit.prevent="submitReject" class="space-y-3">
                    <div>
                        <label
                            class="mb-1 block text-xs font-semibold text-foreground"
                            >Alasan Penolakan Permintaan
                            <span class="text-red-500">*</span></label
                        >
                        <textarea
                            v-model="rejectForm.rejection_reason"
                            rows="3"
                            required
                            placeholder="Contoh: Stok darah golongan terkait sedang kritis (< 2 unit) sehingga diprioritaskan untuk penanganan IGD darurat lokal, atau dokumen rujukan RS belum lengkap..."
                            class="w-full rounded-xl border border-input bg-background p-3 text-xs text-foreground focus:ring-2 focus:ring-red-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="isRejectModalOpen = false"
                            class="rounded-xl border border-input px-4 py-2 text-xs font-medium hover:bg-muted"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="rejectForm.processing"
                            class="rounded-xl bg-red-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-red-700"
                        >
                            Simpan Penolakan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Input Permintaan Baru -->
        <div
            v-if="isCreateOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        >
            <div
                class="max-h-[90vh] w-full max-w-lg space-y-4 overflow-y-auto rounded-2xl border border-border bg-card p-6 shadow-2xl"
            >
                <div
                    class="flex items-center justify-between border-b border-border pb-3"
                >
                    <div class="flex items-center gap-2">
                        <Building2 class="size-5 text-blue-600" />
                        <h3 class="text-base font-bold text-foreground">
                            Formulir Permintaan Darah Rumah Sakit
                        </h3>
                    </div>
                    <button
                        @click="isCreateOpen = false"
                        class="text-muted-foreground hover:text-foreground"
                    >
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitCreate" class="space-y-3">
                    <div>
                        <label
                            class="mb-1 block text-[11px] font-medium text-foreground"
                            >Nama Rumah Sakit / Faskes
                            <span class="text-red-500">*</span></label
                        >
                        <input
                            v-model="createForm.hospital_name"
                            type="text"
                            required
                            placeholder="Contoh: RSUP Fatmawati / RS Jantung Harapan Kita"
                            class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Dokter Penanggung Jawab</label
                            >
                            <input
                                v-model="createForm.doctor_in_charge"
                                type="text"
                                required
                                placeholder="dr. Sp.BTKV"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Nomor Telepon / Hotline RS</label
                            >
                            <input
                                v-model="createForm.contact_phone"
                                type="text"
                                required
                                placeholder="021-xxxx / 0812xxxx"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Nama Pasien
                                <span class="text-red-500">*</span></label
                            >
                            <input
                                v-model="createForm.patient_name"
                                type="text"
                                required
                                placeholder="Nama lengkap pasien"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Nomor Rekam Medis (No RM)
                                <span class="text-red-500">*</span></label
                            >
                            <input
                                v-model="createForm.medical_record_number"
                                type="text"
                                required
                                placeholder="RM-xxxxx"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Golongan Darah</label
                            >
                            <select
                                v-model="createForm.blood_type"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            >
                                <option value="A">Gol. A</option>
                                <option value="B">Gol. B</option>
                                <option value="AB">Gol. AB</option>
                                <option value="O">Gol. O</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Rhesus</label
                            >
                            <select
                                v-model="createForm.rhesus"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            >
                                <option value="+">Positif (+)</option>
                                <option value="-">Negatif (-)</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Komponen Darah</label
                            >
                            <select
                                v-model="createForm.component"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            >
                                <option value="WB">Whole Blood (WB)</option>
                                <option value="PRC">Packed Red (PRC)</option>
                                <option value="TC">Thrombocyte (TC)</option>
                                <option value="FFP">Fresh Plasma (FFP)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Jumlah Kantong Diminta
                                <span class="text-red-500">*</span></label
                            >
                            <input
                                v-model="createForm.units_requested"
                                type="number"
                                min="1"
                                required
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Tingkat Urgensi</label
                            >
                            <select
                                v-model="createForm.urgency"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs font-bold"
                            >
                                <option value="emergency">
                                    🚨 Darurat Cito! (Operasi Segera)
                                </option>
                                <option value="urgent">
                                    ⚠️ Mendesak (Dalam 24 Jam)
                                </option>
                                <option value="normal">
                                    Normal (Operasi Terjadwal)
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-[11px] font-medium text-foreground"
                            >Indikasi Klinis / Diagnosis Dokter</label
                        >
                        <textarea
                            v-model="createForm.diagnosis_notes"
                            rows="2"
                            placeholder="Contoh: Pendarahan pasca partum, operasi bedah toraks, trombositopenia..."
                            class="w-full rounded-lg border border-input bg-card p-2 text-xs"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="isCreateOpen = false"
                            class="rounded-xl border border-input px-4 py-2 text-xs font-medium hover:bg-muted"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="createForm.processing"
                            class="rounded-xl bg-blue-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700"
                        >
                            Simpan Permintaan RS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
