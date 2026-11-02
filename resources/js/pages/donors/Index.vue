<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Users,
    UserPlus,
    Search,
    CheckCircle2,
    AlertCircle,
    Clock,
    FileText,
    Phone,
    ShieldCheck,
    XCircle,
    Eye,
    Edit2,
    Trash2,
    Check,
    HeartPulse,
} from '@lucide/vue';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Data Donor', href: '/donors' },
        ],
    },
});

interface DonorHistoryItem {
    id: number;
    donation_date: string;
    blood_type: string;
    rhesus: string;
    component_type: string;
    volume_ml: number;
    blood_bag_code?: string;
    hemoglobin?: number;
    blood_pressure?: string;
    screening_result: string;
    notes?: string;
}

interface DonorItem {
    id: number;
    donor_code: string;
    nik: string;
    name: string;
    email?: string;
    phone: string;
    blood_type: string;
    rhesus: string;
    blood_type_full: string;
    gender: 'L' | 'P';
    birth_date: string;
    age: number;
    address?: string;
    occupation?: string;
    weight?: number;
    systolic?: number;
    diastolic?: number;
    hemoglobin?: number;
    last_donation_date?: string;
    total_donations: number;
    days_since_last_donation?: number;
    is_eligible_to_donate: boolean;
    verification_status: 'pending' | 'verified' | 'suspended';
    verification_notes?: string;
    verified_at?: string;
    verifier?: { name: string };
    histories?: DonorHistoryItem[];
}

const props = defineProps<{
    donors: {
        data: DonorItem[];
        current_page: number;
        last_page: number;
        total: number;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: {
        search?: string;
        blood_type?: string;
        status?: string;
    };
    counts: {
        total: number;
        verified: number;
        pending: number;
        suspended: number;
    };
}>();

// Search & Filter state
const searchQuery = ref(props.filters.search || '');
const selectedBloodType = ref(props.filters.blood_type || '');
const selectedStatus = ref(props.filters.status || '');

function applyFilters() {
    router.get(
        '/donors',
        {
            search: searchQuery.value || undefined,
            blood_type: selectedBloodType.value || undefined,
            status: selectedStatus.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}

function setStatusFilter(status: string) {
    selectedStatus.value = status;
    applyFilters();
}

// Modal State: Create Donor
const isCreateOpen = ref(false);
const createForm = useForm({
    nik: '',
    name: '',
    email: '',
    phone: '',
    blood_type: 'O',
    rhesus: '+',
    gender: 'L',
    birth_date: '1998-01-01',
    address: '',
    occupation: '',
    weight: 65,
    systolic: 120,
    diastolic: 80,
    hemoglobin: 13.5,
});

function submitCreate() {
    createForm.post('/donors', {
        onSuccess: () => {
            isCreateOpen.value = false;
            createForm.reset();
        },
    });
}

// Modal State: Detail Donor
const selectedDonorDetail = ref<DonorItem | null>(null);
function openDetail(donor: DonorItem) {
    selectedDonorDetail.value = donor;
}

// Modal State: Aksi Admin - Verifikasi Donor
const isVerifyOpen = ref(false);
const verifyingDonor = ref<DonorItem | null>(null);
const verifyForm = useForm({
    status: 'verified',
    notes: '',
    systolic: 120,
    diastolic: 80,
    hemoglobin: 13.5,
    weight: 65,
});

function openVerifyModal(donor: DonorItem) {
    verifyingDonor.value = donor;
    verifyForm.status =
        donor.verification_status === 'suspended' ? 'suspended' : 'verified';
    verifyForm.notes = donor.verification_notes || '';
    verifyForm.systolic = donor.systolic || 120;
    verifyForm.diastolic = donor.diastolic || 80;
    verifyForm.hemoglobin = donor.hemoglobin || 13.5;
    verifyForm.weight = donor.weight || 60;
    isVerifyOpen.value = true;
}

function submitVerification() {
    if (!verifyingDonor.value) return;
    verifyForm.post(`/donors/${verifyingDonor.value.id}/verify`, {
        onSuccess: () => {
            isVerifyOpen.value = false;
            verifyingDonor.value = null;
        },
    });
}

// Delete Donor
function deleteDonor(donor: DonorItem) {
    if (
        confirm(`Apakah Anda yakin ingin menghapus data donor ${donor.name}?`)
    ) {
        router.delete(`/donors/${donor.id}`);
    }
}
</script>

<template>
    <Head title="Data Donor - DarahKitaa" />

    <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 p-4 md:p-6">
        <!-- Header -->
        <div
            class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
        >
            <div>
                <div class="flex items-center gap-2">
                    <span
                        class="rounded-lg bg-amber-500/10 p-2 text-amber-600 dark:text-amber-400"
                    >
                        <Users class="size-5" />
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1
                                class="text-xl font-extrabold tracking-tight text-foreground md:text-2xl"
                            >
                                Data Donor
                            </h1>
                            <span
                                class="rounded border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400"
                            >
                                Aksi Admin: Verifikasi
                            </span>
                        </div>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            Kelola profil calon dan pendonor tetap, verifikasi
                            kelayakan medis, dan tinjau riwayat donasi.
                        </p>
                    </div>
                </div>
            </div>

            <button
                @click="isCreateOpen = true"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-red-700"
            >
                <UserPlus class="size-4" /> Tambah Calon Donor
            </button>
        </div>

        <!-- Filter Cards Status -->
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
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
                        Semua Donor
                    </div>
                    <div class="text-lg font-bold text-foreground">
                        {{ counts.total }}
                    </div>
                </div>
                <Users class="size-4 text-muted-foreground" />
            </button>

            <button
                @click="setStatusFilter('verified')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition"
                :class="
                    selectedStatus === 'verified'
                        ? 'border-emerald-500 bg-emerald-500/10 ring-1 ring-emerald-500'
                        : 'border-border bg-card hover:border-border/80'
                "
            >
                <div>
                    <div
                        class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400"
                    >
                        Terverifikasi (Layak)
                    </div>
                    <div class="text-lg font-bold text-foreground">
                        {{ counts.verified }}
                    </div>
                </div>
                <CheckCircle2 class="size-4 text-emerald-500" />
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
                    <div
                        class="text-[11px] font-medium text-amber-600 dark:text-amber-400"
                    >
                        Menunggu Verifikasi
                    </div>
                    <div class="text-lg font-bold text-foreground">
                        {{ counts.pending }}
                    </div>
                </div>
                <Clock class="size-4 text-amber-500" />
            </button>

            <button
                @click="setStatusFilter('suspended')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition"
                :class="
                    selectedStatus === 'suspended'
                        ? 'border-red-500 bg-red-500/10 ring-1 ring-red-500'
                        : 'border-border bg-card hover:border-border/80'
                "
            >
                <div>
                    <div
                        class="text-[11px] font-medium text-red-600 dark:text-red-400"
                    >
                        Ditangguhkan
                    </div>
                    <div class="text-lg font-bold text-foreground">
                        {{ counts.suspended }}
                    </div>
                </div>
                <XCircle class="size-4 text-red-500" />
            </button>
        </div>

        <!-- Search and Blood Type Bar -->
        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <Search
                    class="absolute top-3 left-3 size-4 text-muted-foreground"
                />
                <input
                    v-model="searchQuery"
                    @keyup.enter="applyFilters"
                    type="text"
                    placeholder="Cari berdasarkan nama, kode donor, NIK, atau no WhatsApp..."
                    class="w-full rounded-xl border border-input bg-card py-2.5 pr-4 pl-9 text-xs text-foreground shadow-sm focus:ring-2 focus:ring-red-500 focus:outline-none"
                />
            </div>

            <select
                v-model="selectedBloodType"
                @change="applyFilters"
                class="rounded-xl border border-input bg-card px-3 py-2.5 text-xs text-foreground shadow-sm focus:ring-2 focus:ring-red-500 focus:outline-none"
            >
                <option value="">Semua Gol. Darah</option>
                <option value="A">Golongan A</option>
                <option value="B">Golongan B</option>
                <option value="AB">Golongan AB</option>
                <option value="O">Golongan O</option>
            </select>

            <button
                @click="applyFilters"
                class="rounded-xl bg-zinc-800 px-4 py-2.5 text-xs font-medium text-white transition hover:bg-zinc-700"
            >
                Terapkan
            </button>
        </div>

        <!-- Table of Donors -->
        <div
            class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead
                        class="border-b border-border bg-muted/50 text-[10px] tracking-wider text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-4 py-3 font-semibold">Donor & NIK</th>
                            <th class="px-4 py-3 font-semibold">Gol. Darah</th>
                            <th class="px-4 py-3 font-semibold">Kontak</th>
                            <th class="px-4 py-3 font-semibold">
                                Kondisi Fisik / Skrining
                            </th>
                            <th class="px-4 py-3 font-semibold">
                                Riwayat Donasi
                            </th>
                            <th class="px-4 py-3 font-semibold">
                                Status Verifikasi
                            </th>
                            <th class="px-4 py-3 text-right font-semibold">
                                Aksi Admin
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        <tr
                            v-for="donor in donors.data"
                            :key="donor.id"
                            class="transition hover:bg-muted/30"
                        >
                            <!-- Column 1: Info -->
                            <td class="px-4 py-3.5">
                                <div class="text-sm font-bold text-foreground">
                                    {{ donor.name }}
                                </div>
                                <div
                                    class="font-mono text-[11px] text-muted-foreground"
                                >
                                    {{ donor.donor_code }}
                                </div>
                                <div class="text-[10px] text-muted-foreground">
                                    NIK: {{ donor.nik }}
                                </div>
                            </td>

                            <!-- Column 2: Blood Type -->
                            <td class="px-4 py-3.5">
                                <span
                                    class="inline-flex items-center justify-center rounded-lg border border-red-500/20 bg-red-500/10 px-2.5 py-1 text-sm font-extrabold text-red-600 dark:text-red-400"
                                >
                                    {{ donor.blood_type_full }}
                                </span>
                            </td>

                            <!-- Column 3: Contact -->
                            <td class="px-4 py-3.5">
                                <a
                                    :href="`https://wa.me/${donor.phone.replace(/^0/, '62')}`"
                                    target="_blank"
                                    class="inline-flex items-center gap-1 font-medium text-emerald-600 hover:underline"
                                >
                                    <Phone class="size-3" /> {{ donor.phone }}
                                </a>
                                <div
                                    v-if="donor.email"
                                    class="max-w-[140px] truncate text-[10px] text-muted-foreground"
                                >
                                    {{ donor.email }}
                                </div>
                            </td>

                            <!-- Column 4: Physical check -->
                            <td class="px-4 py-3.5">
                                <div class="space-y-0.5 text-[11px]">
                                    <div>
                                        Usia:
                                        <span
                                            class="font-medium text-foreground"
                                            >{{ donor.age }} th</span
                                        >
                                        ({{
                                            donor.gender === 'L'
                                                ? 'Laki-laki'
                                                : 'Perempuan'
                                        }})
                                    </div>
                                    <div>
                                        Tensi:
                                        <span
                                            class="font-medium text-foreground"
                                            >{{
                                                donor.systolic
                                                    ? `${donor.systolic}/${donor.diastolic} mmHg`
                                                    : '-'
                                            }}</span
                                        >
                                    </div>
                                    <div>
                                        Hb:
                                        <span
                                            class="font-medium text-foreground"
                                            >{{
                                                donor.hemoglobin
                                                    ? `${donor.hemoglobin} g/dL`
                                                    : '-'
                                            }}</span
                                        >
                                    </div>
                                </div>
                            </td>

                            <!-- Column 5: Donation History -->
                            <td class="px-4 py-3.5">
                                <div class="space-y-0.5 text-[11px]">
                                    <div class="font-medium text-foreground">
                                        Total: {{ donor.total_donations }} kali
                                    </div>
                                    <div
                                        class="text-[10px] text-muted-foreground"
                                    >
                                        Terakhir:
                                        {{
                                            donor.last_donation_date
                                                ? donor.last_donation_date
                                                : 'Belum pernah'
                                        }}
                                    </div>
                                    <div
                                        v-if="donor.is_eligible_to_donate"
                                        class="text-[10px] font-semibold text-emerald-600"
                                    >
                                        ✓ Siap donor kembali
                                    </div>
                                    <div
                                        v-else-if="
                                            donor.days_since_last_donation
                                        "
                                        class="text-[10px] font-medium text-amber-600"
                                    >
                                        Tunggu
                                        {{
                                            60 - donor.days_since_last_donation
                                        }}
                                        hari lagi
                                    </div>
                                </div>
                            </td>

                            <!-- Column 6: Status Verifikasi -->
                            <td class="px-4 py-3.5">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                                    :class="{
                                        'border border-emerald-500/20 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400':
                                            donor.verification_status ===
                                            'verified',
                                        'border border-amber-500/20 bg-amber-500/10 text-amber-700 dark:text-amber-400':
                                            donor.verification_status ===
                                            'pending',
                                        'border border-red-500/20 bg-red-500/10 text-red-700 dark:text-red-400':
                                            donor.verification_status ===
                                            'suspended',
                                    }"
                                >
                                    <CheckCircle2
                                        v-if="
                                            donor.verification_status ===
                                            'verified'
                                        "
                                        class="size-3"
                                    />
                                    <Clock
                                        v-else-if="
                                            donor.verification_status ===
                                            'pending'
                                        "
                                        class="size-3"
                                    />
                                    <XCircle v-else class="size-3" />
                                    <span>
                                        {{
                                            donor.verification_status ===
                                            'verified'
                                                ? 'Terverifikasi'
                                                : donor.verification_status ===
                                                    'pending'
                                                  ? 'Menunggu'
                                                  : 'Ditangguhkan'
                                        }}
                                    </span>
                                </span>
                                <div
                                    v-if="donor.verification_notes"
                                    class="mt-1 max-w-[160px] truncate text-[10px] text-muted-foreground"
                                    :title="donor.verification_notes"
                                >
                                    "{{ donor.verification_notes }}"
                                </div>
                            </td>

                            <!-- Column 7: Aksi Admin -->
                            <td class="px-4 py-3.5 text-right">
                                <div
                                    class="flex items-center justify-end gap-1.5"
                                >
                                    <!-- Aksi Utama: Verifikasi -->
                                    <button
                                        @click="openVerifyModal(donor)"
                                        class="inline-flex items-center gap-1 rounded-md bg-teal-600 px-2.5 py-1 text-xs font-semibold text-white shadow-sm transition hover:bg-teal-700"
                                        title="Lakukan Verifikasi Status Donor"
                                    >
                                        <ShieldCheck class="size-3" />
                                        <span>Verifikasi</span>
                                    </button>

                                    <!-- Detail -->
                                    <button
                                        @click="openDetail(donor)"
                                        class="rounded-md p-1 text-muted-foreground transition hover:bg-muted hover:text-foreground"
                                        title="Lihat Detail Profil & Riwayat"
                                    >
                                        <Eye class="size-4" />
                                    </button>

                                    <!-- Delete -->
                                    <button
                                        @click="deleteDonor(donor)"
                                        class="rounded-md p-1 text-red-500 transition hover:bg-red-500/10 hover:text-red-700"
                                        title="Hapus Data Donor"
                                    >
                                        <Trash2 class="size-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="donors.data.length === 0">
                            <td
                                colspan="7"
                                class="py-12 text-center text-sm text-muted-foreground"
                            >
                                Tidak ada data donor yang cocok dengan kriteria
                                pencarian.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="donors.last_page > 1"
                class="flex items-center justify-between border-t border-border p-3 text-xs text-muted-foreground"
            >
                <div>
                    Menampilkan {{ donors.data.length }} dari
                    {{ donors.total }} donor
                </div>
                <div class="flex gap-1">
                    <Link
                        v-for="link in donors.links"
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

        <!-- Modal 1: Aksi Admin: Verifikasi Donor -->
        <div
            v-if="isVerifyOpen && verifyingDonor"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        >
            <div
                class="w-full max-w-lg space-y-4 rounded-2xl border border-border bg-card p-6 shadow-2xl"
            >
                <div
                    class="flex items-center justify-between border-b border-border pb-3"
                >
                    <div class="flex items-center gap-2.5">
                        <div
                            class="flex size-9 items-center justify-center rounded-lg bg-teal-500/10 text-teal-600"
                        >
                            <ShieldCheck class="size-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-foreground">
                                Aksi Admin: Verifikasi Kelayakan Donor
                            </h3>
                            <p class="text-xs text-muted-foreground">
                                {{ verifyingDonor.name }} ({{
                                    verifyingDonor.donor_code
                                }})
                            </p>
                        </div>
                    </div>
                    <button
                        @click="isVerifyOpen = false"
                        class="text-muted-foreground hover:text-foreground"
                    >
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitVerification" class="space-y-4">
                    <!-- Status Selection -->
                    <div>
                        <label
                            class="mb-1.5 block text-xs font-semibold text-foreground"
                            >Pilih Status Verifikasi</label
                        >
                        <div class="grid grid-cols-2 gap-3">
                            <label
                                class="flex cursor-pointer items-center gap-2 rounded-xl border p-3 transition"
                                :class="
                                    verifyForm.status === 'verified'
                                        ? 'border-emerald-500 bg-emerald-500/10 font-bold text-emerald-700 dark:text-emerald-300'
                                        : 'border-border'
                                "
                            >
                                <input
                                    type="radio"
                                    v-model="verifyForm.status"
                                    value="verified"
                                    class="text-emerald-600"
                                />
                                <div>
                                    <div class="text-xs">
                                        Terverifikasi (Layak)
                                    </div>
                                    <div
                                        class="text-[10px] font-normal text-muted-foreground"
                                    >
                                        Memenuhi semua syarat donor
                                    </div>
                                </div>
                            </label>

                            <label
                                class="flex cursor-pointer items-center gap-2 rounded-xl border p-3 transition"
                                :class="
                                    verifyForm.status === 'suspended'
                                        ? 'border-red-500 bg-red-500/10 font-bold text-red-700 dark:text-red-300'
                                        : 'border-border'
                                "
                            >
                                <input
                                    type="radio"
                                    v-model="verifyForm.status"
                                    value="suspended"
                                    class="text-red-600"
                                />
                                <div>
                                    <div class="text-xs">
                                        Tangguhkan (Tidak Layak)
                                    </div>
                                    <div
                                        class="text-[10px] font-normal text-muted-foreground"
                                    >
                                        Hb rendah / tensi / medis
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Medical parameters check -->
                    <div
                        class="grid grid-cols-3 gap-3 rounded-xl border border-border bg-muted/40 p-3"
                    >
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Tensi Sistolik</label
                            >
                            <input
                                v-model="verifyForm.systolic"
                                type="number"
                                placeholder="120"
                                class="w-full rounded-lg border border-input bg-card px-2.5 py-1.5 text-xs"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Tensi Diastolik</label
                            >
                            <input
                                v-model="verifyForm.diastolic"
                                type="number"
                                placeholder="80"
                                class="w-full rounded-lg border border-input bg-card px-2.5 py-1.5 text-xs"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Kadar Hb (g/dL)</label
                            >
                            <input
                                v-model="verifyForm.hemoglobin"
                                type="number"
                                step="0.1"
                                placeholder="13.5"
                                class="w-full rounded-lg border border-input bg-card px-2.5 py-1.5 text-xs"
                            />
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label
                            class="mb-1 block text-xs font-semibold text-foreground"
                            >Catatan Hasil Verifikasi & Skrining</label
                        >
                        <textarea
                            v-model="verifyForm.notes"
                            rows="3"
                            placeholder="Tuliskan catatan kelayakan donor (misal: Hb stabil di atas 12.5, bebas konsumsi antibiotik 7 hari terakhir)..."
                            class="w-full rounded-xl border border-input bg-background p-3 text-xs text-foreground focus:ring-2 focus:ring-teal-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="isVerifyOpen = false"
                            class="rounded-xl border border-input px-4 py-2 text-xs font-medium transition hover:bg-muted"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="verifyForm.processing"
                            class="rounded-xl bg-teal-600 px-5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-teal-700"
                        >
                            Simpan Hasil Verifikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal 2: Tambah Calon Donor Baru -->
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
                        <UserPlus class="size-5 text-red-600" />
                        <h3 class="text-base font-bold text-foreground">
                            Pendaftaran Calon Pendonor
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
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >NIK (16 Digit)
                                <span class="text-red-500">*</span></label
                            >
                            <input
                                v-model="createForm.nik"
                                type="text"
                                maxlength="16"
                                required
                                placeholder="3171xxxxxxxxxxxx"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Nama Lengkap
                                <span class="text-red-500">*</span></label
                            >
                            <input
                                v-model="createForm.name"
                                type="text"
                                required
                                placeholder="Nama sesuai KTP"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Golongan Darah</label
                            >
                            <select
                                v-model="createForm.blood_type"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            >
                                <option value="A">Golongan A</option>
                                <option value="B">Golongan B</option>
                                <option value="AB">Golongan AB</option>
                                <option value="O">Golongan O</option>
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
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Nomor WhatsApp / HP
                                <span class="text-red-500">*</span></label
                            >
                            <input
                                v-model="createForm.phone"
                                type="text"
                                required
                                placeholder="08xxxxxxxxxx"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Email (Opsional)</label
                            >
                            <input
                                v-model="createForm.email"
                                type="email"
                                placeholder="donor@email.com"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Jenis Kelamin</label
                            >
                            <select
                                v-model="createForm.gender"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            >
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Tanggal Lahir</label
                            >
                            <input
                                v-model="createForm.birth_date"
                                type="date"
                                required
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Berat Badan (kg)</label
                            >
                            <input
                                v-model="createForm.weight"
                                type="number"
                                step="0.5"
                                placeholder="60"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-[11px] font-medium text-foreground"
                            >Alamat Tinggal</label
                        >
                        <textarea
                            v-model="createForm.address"
                            rows="2"
                            placeholder="Alamat domisili pendonor..."
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
                            class="rounded-xl bg-red-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-red-700"
                        >
                            Daftarkan Calon Donor
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal 3: Detail Profil & Riwayat Donor -->
        <div
            v-if="selectedDonorDetail"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        >
            <div
                class="max-h-[90vh] w-full max-w-lg space-y-4 overflow-y-auto rounded-2xl border border-border bg-card p-6 shadow-2xl"
            >
                <div
                    class="flex items-center justify-between border-b border-border pb-3"
                >
                    <div class="flex items-center gap-2.5">
                        <span
                            class="inline-flex size-10 items-center justify-center rounded-xl border border-red-500/20 bg-red-500/10 text-sm font-black text-red-600"
                        >
                            {{ selectedDonorDetail.blood_type_full }}
                        </span>
                        <div>
                            <h3 class="text-base font-bold text-foreground">
                                {{ selectedDonorDetail.name }}
                            </h3>
                            <p class="text-xs text-muted-foreground">
                                {{ selectedDonorDetail.donor_code }} • NIK:
                                {{ selectedDonorDetail.nik }}
                            </p>
                        </div>
                    </div>
                    <button
                        @click="selectedDonorDetail = null"
                        class="text-muted-foreground hover:text-foreground"
                    >
                        ✕
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div
                        class="grid grid-cols-2 gap-2 rounded-xl bg-muted/40 p-3"
                    >
                        <div>
                            <strong>Kontak WhatsApp:</strong>
                            {{ selectedDonorDetail.phone }}
                        </div>
                        <div>
                            <strong>Usia:</strong>
                            {{ selectedDonorDetail.age }} tahun ({{
                                selectedDonorDetail.gender === 'L'
                                    ? 'Pria'
                                    : 'Wanita'
                            }})
                        </div>
                        <div>
                            <strong>Berat Badan:</strong>
                            {{
                                selectedDonorDetail.weight
                                    ? `${selectedDonorDetail.weight} kg`
                                    : '-'
                            }}
                        </div>
                        <div>
                            <strong>Tekanan Darah:</strong>
                            {{
                                selectedDonorDetail.systolic
                                    ? `${selectedDonorDetail.systolic}/${selectedDonorDetail.diastolic} mmHg`
                                    : '-'
                            }}
                        </div>
                        <div>
                            <strong>Kadar Hb:</strong>
                            {{
                                selectedDonorDetail.hemoglobin
                                    ? `${selectedDonorDetail.hemoglobin} g/dL`
                                    : '-'
                            }}
                        </div>
                        <div>
                            <strong>Total Donasi:</strong>
                            {{ selectedDonorDetail.total_donations }} kali
                        </div>
                    </div>

                    <div>
                        <strong>Alamat Domisili:</strong>
                        <p class="mt-0.5 text-muted-foreground">
                            {{ selectedDonorDetail.address || '-' }}
                        </p>
                    </div>

                    <!-- Riwayat Donasi -->
                    <div class="pt-2">
                        <div
                            class="mb-2 flex items-center gap-1.5 font-bold text-foreground"
                        >
                            <HeartPulse class="size-4 text-red-500" />
                            <span>Riwayat Donor Darah Sebelumnya</span>
                        </div>
                        <div
                            v-if="
                                selectedDonorDetail.histories &&
                                selectedDonorDetail.histories.length > 0
                            "
                            class="divide-y divide-border overflow-hidden rounded-xl border"
                        >
                            <div
                                v-for="h in selectedDonorDetail.histories"
                                :key="h.id"
                                class="flex items-center justify-between bg-card p-2.5"
                            >
                                <div>
                                    <div class="font-semibold text-foreground">
                                        🗓️ {{ h.donation_date }} ({{
                                            h.component_type
                                        }}
                                        {{ h.volume_ml }}ml)
                                    </div>
                                    <div
                                        class="text-[10px] text-muted-foreground"
                                    >
                                        Kode Kantong:
                                        {{ h.blood_bag_code || '-' }} • Tensi:
                                        {{ h.blood_pressure }}
                                    </div>
                                </div>
                                <span
                                    class="rounded bg-emerald-500/10 px-2 py-0.5 text-[10px] font-medium text-emerald-600"
                                    >Lolos Donasi</span
                                >
                            </div>
                        </div>
                        <div
                            v-else
                            class="rounded-xl bg-muted/20 py-4 text-center text-xs text-muted-foreground"
                        >
                            Belum ada riwayat donasi darah yang tercatat.
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button
                        @click="selectedDonorDetail = null"
                        class="rounded-xl bg-zinc-800 px-4 py-2 text-xs font-medium text-white"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
