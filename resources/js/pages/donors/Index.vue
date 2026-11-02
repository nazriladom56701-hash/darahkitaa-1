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
    HeartPulse
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
    router.get('/donors', {
        search: searchQuery.value || undefined,
        blood_type: selectedBloodType.value || undefined,
        status: selectedStatus.value || undefined,
    }, { preserveState: true, replace: true });
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
    verifyForm.status = donor.verification_status === 'suspended' ? 'suspended' : 'verified';
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
    if (confirm(`Apakah Anda yakin ingin menghapus data donor ${donor.name}?`)) {
        router.delete(`/donors/${donor.id}`);
    }
}
</script>

<template>
    <Head title="Data Donor - DarahKitaa" />

    <div class="flex flex-col gap-6 p-4 md:p-6 max-w-7xl mx-auto w-full">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400">
                        <Users class="size-5" />
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl md:text-2xl font-extrabold text-foreground tracking-tight">Data Donor</h1>
                            <span class="text-xs bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-semibold px-2 py-0.5 rounded border border-emerald-500/20">
                                Aksi Admin: Verifikasi
                            </span>
                        </div>
                        <p class="text-xs text-muted-foreground mt-0.5">
                            Kelola profil calon dan pendonor tetap, verifikasi kelayakan medis, dan tinjau riwayat donasi.
                        </p>
                    </div>
                </div>
            </div>

            <button
                @click="isCreateOpen = true"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow-sm transition"
            >
                <UserPlus class="size-4" /> Tambah Calon Donor
            </button>
        </div>

        <!-- Filter Cards Status -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <button
                @click="setStatusFilter('')"
                class="p-3 rounded-xl border text-left transition flex items-center justify-between"
                :class="selectedStatus === '' ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-border bg-card hover:border-border/80'"
            >
                <div>
                    <div class="text-[11px] text-muted-foreground">Semua Donor</div>
                    <div class="text-lg font-bold text-foreground">{{ counts.total }}</div>
                </div>
                <Users class="size-4 text-muted-foreground" />
            </button>

            <button
                @click="setStatusFilter('verified')"
                class="p-3 rounded-xl border text-left transition flex items-center justify-between"
                :class="selectedStatus === 'verified' ? 'border-emerald-500 bg-emerald-500/10 ring-1 ring-emerald-500' : 'border-border bg-card hover:border-border/80'"
            >
                <div>
                    <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">Terverifikasi (Layak)</div>
                    <div class="text-lg font-bold text-foreground">{{ counts.verified }}</div>
                </div>
                <CheckCircle2 class="size-4 text-emerald-500" />
            </button>

            <button
                @click="setStatusFilter('pending')"
                class="p-3 rounded-xl border text-left transition flex items-center justify-between"
                :class="selectedStatus === 'pending' ? 'border-amber-500 bg-amber-500/10 ring-1 ring-amber-500' : 'border-border bg-card hover:border-border/80'"
            >
                <div>
                    <div class="text-[11px] text-amber-600 dark:text-amber-400 font-medium">Menunggu Verifikasi</div>
                    <div class="text-lg font-bold text-foreground">{{ counts.pending }}</div>
                </div>
                <Clock class="size-4 text-amber-500" />
            </button>

            <button
                @click="setStatusFilter('suspended')"
                class="p-3 rounded-xl border text-left transition flex items-center justify-between"
                :class="selectedStatus === 'suspended' ? 'border-red-500 bg-red-500/10 ring-1 ring-red-500' : 'border-border bg-card hover:border-border/80'"
            >
                <div>
                    <div class="text-[11px] text-red-600 dark:text-red-400 font-medium">Ditangguhkan</div>
                    <div class="text-lg font-bold text-foreground">{{ counts.suspended }}</div>
                </div>
                <XCircle class="size-4 text-red-500" />
            </button>
        </div>

        <!-- Search and Blood Type Bar -->
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <Search class="size-4 absolute left-3 top-3 text-muted-foreground" />
                <input
                    v-model="searchQuery"
                    @keyup.enter="applyFilters"
                    type="text"
                    placeholder="Cari berdasarkan nama, kode donor, NIK, atau no WhatsApp..."
                    class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl border border-input bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm"
                />
            </div>

            <select
                v-model="selectedBloodType"
                @change="applyFilters"
                class="text-xs rounded-xl border border-input bg-card px-3 py-2.5 text-foreground focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm"
            >
                <option value="">Semua Gol. Darah</option>
                <option value="A">Golongan A</option>
                <option value="B">Golongan B</option>
                <option value="AB">Golongan AB</option>
                <option value="O">Golongan O</option>
            </select>

            <button
                @click="applyFilters"
                class="px-4 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-medium transition"
            >
                Terapkan
            </button>
        </div>

        <!-- Table of Donors -->
        <div class="rounded-2xl border border-border bg-card overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-muted/50 border-b border-border text-muted-foreground uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Donor & NIK</th>
                            <th class="px-4 py-3 font-semibold">Gol. Darah</th>
                            <th class="px-4 py-3 font-semibold">Kontak</th>
                            <th class="px-4 py-3 font-semibold">Kondisi Fisik / Skrining</th>
                            <th class="px-4 py-3 font-semibold">Riwayat Donasi</th>
                            <th class="px-4 py-3 font-semibold">Status Verifikasi</th>
                            <th class="px-4 py-3 font-semibold text-right">Aksi Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        <tr v-for="donor in donors.data" :key="donor.id" class="hover:bg-muted/30 transition">
                            <!-- Column 1: Info -->
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-foreground text-sm">{{ donor.name }}</div>
                                <div class="text-[11px] text-muted-foreground font-mono">{{ donor.donor_code }}</div>
                                <div class="text-[10px] text-muted-foreground">NIK: {{ donor.nik }}</div>
                            </td>

                            <!-- Column 2: Blood Type -->
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center justify-center font-extrabold text-sm px-2.5 py-1 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20">
                                    {{ donor.blood_type_full }}
                                </span>
                            </td>

                            <!-- Column 3: Contact -->
                            <td class="px-4 py-3.5">
                                <a 
                                    :href="`https://wa.me/${donor.phone.replace(/^0/, '62')}`" 
                                    target="_blank"
                                    class="inline-flex items-center gap-1 text-emerald-600 hover:underline font-medium"
                                >
                                    <Phone class="size-3" /> {{ donor.phone }}
                                </a>
                                <div v-if="donor.email" class="text-[10px] text-muted-foreground truncate max-w-[140px]">{{ donor.email }}</div>
                            </td>

                            <!-- Column 4: Physical check -->
                            <td class="px-4 py-3.5">
                                <div class="space-y-0.5 text-[11px]">
                                    <div>Usia: <span class="font-medium text-foreground">{{ donor.age }} th</span> ({{ donor.gender === 'L' ? 'Laki-laki' : 'Perempuan' }})</div>
                                    <div>Tensi: <span class="font-medium text-foreground">{{ donor.systolic ? `${donor.systolic}/${donor.diastolic} mmHg` : '-' }}</span></div>
                                    <div>Hb: <span class="font-medium text-foreground">{{ donor.hemoglobin ? `${donor.hemoglobin} g/dL` : '-' }}</span></div>
                                </div>
                            </td>

                            <!-- Column 5: Donation History -->
                            <td class="px-4 py-3.5">
                                <div class="text-[11px] space-y-0.5">
                                    <div class="font-medium text-foreground">Total: {{ donor.total_donations }} kali</div>
                                    <div class="text-[10px] text-muted-foreground">
                                        Terakhir: {{ donor.last_donation_date ? donor.last_donation_date : 'Belum pernah' }}
                                    </div>
                                    <div v-if="donor.is_eligible_to_donate" class="text-[10px] text-emerald-600 font-semibold">
                                        ✓ Siap donor kembali
                                    </div>
                                    <div v-else-if="donor.days_since_last_donation" class="text-[10px] text-amber-600 font-medium">
                                        Tunggu {{ 60 - donor.days_since_last_donation }} hari lagi
                                    </div>
                                </div>
                            </td>

                            <!-- Column 6: Status Verifikasi -->
                            <td class="px-4 py-3.5">
                                <span 
                                    class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full"
                                    :class="{
                                        'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20': donor.verification_status === 'verified',
                                        'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20': donor.verification_status === 'pending',
                                        'bg-red-500/10 text-red-700 dark:text-red-400 border border-red-500/20': donor.verification_status === 'suspended'
                                    }"
                                >
                                    <CheckCircle2 v-if="donor.verification_status === 'verified'" class="size-3" />
                                    <Clock v-else-if="donor.verification_status === 'pending'" class="size-3" />
                                    <XCircle v-else class="size-3" />
                                    <span>
                                        {{ donor.verification_status === 'verified' ? 'Terverifikasi' : donor.verification_status === 'pending' ? 'Menunggu' : 'Ditangguhkan' }}
                                    </span>
                                </span>
                                <div v-if="donor.verification_notes" class="text-[10px] text-muted-foreground truncate max-w-[160px] mt-1" :title="donor.verification_notes">
                                    "{{ donor.verification_notes }}"
                                </div>
                            </td>

                            <!-- Column 7: Aksi Admin -->
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Aksi Utama: Verifikasi -->
                                    <button
                                        @click="openVerifyModal(donor)"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-teal-600 hover:bg-teal-700 text-white font-semibold text-xs shadow-sm transition"
                                        title="Lakukan Verifikasi Status Donor"
                                    >
                                        <ShieldCheck class="size-3" />
                                        <span>Verifikasi</span>
                                    </button>

                                    <!-- Detail -->
                                    <button
                                        @click="openDetail(donor)"
                                        class="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-muted transition"
                                        title="Lihat Detail Profil & Riwayat"
                                    >
                                        <Eye class="size-4" />
                                    </button>

                                    <!-- Delete -->
                                    <button
                                        @click="deleteDonor(donor)"
                                        class="p-1 rounded-md text-red-500 hover:text-red-700 hover:bg-red-500/10 transition"
                                        title="Hapus Data Donor"
                                    >
                                        <Trash2 class="size-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="donors.data.length === 0">
                            <td colspan="7" class="py-12 text-center text-sm text-muted-foreground">
                                Tidak ada data donor yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="donors.last_page > 1" class="p-3 border-t border-border flex items-center justify-between text-xs text-muted-foreground">
                <div>Menampilkan {{ donors.data.length }} dari {{ donors.total }} donor</div>
                <div class="flex gap-1">
                    <Link
                        v-for="link in donors.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        class="px-2.5 py-1 rounded border text-xs"
                        :class="link.active ? 'bg-primary text-primary-foreground border-primary font-bold' : 'border-border hover:bg-muted'"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>

        <!-- Modal 1: Aksi Admin: Verifikasi Donor -->
        <div v-if="isVerifyOpen && verifyingDonor" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
            <div class="bg-card border border-border rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-border">
                    <div class="flex items-center gap-2.5">
                        <div class="size-9 rounded-lg bg-teal-500/10 text-teal-600 flex items-center justify-center">
                            <ShieldCheck class="size-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-foreground">Aksi Admin: Verifikasi Kelayakan Donor</h3>
                            <p class="text-xs text-muted-foreground">{{ verifyingDonor.name }} ({{ verifyingDonor.donor_code }})</p>
                        </div>
                    </div>
                    <button @click="isVerifyOpen = false" class="text-muted-foreground hover:text-foreground">✕</button>
                </div>

                <form @submit.prevent="submitVerification" class="space-y-4">
                    <!-- Status Selection -->
                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1.5">Pilih Status Verifikasi</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2 p-3 rounded-xl border cursor-pointer transition" :class="verifyForm.status === 'verified' ? 'border-emerald-500 bg-emerald-500/10 font-bold text-emerald-700 dark:text-emerald-300' : 'border-border'">
                                <input type="radio" v-model="verifyForm.status" value="verified" class="text-emerald-600" />
                                <div>
                                    <div class="text-xs">Terverifikasi (Layak)</div>
                                    <div class="text-[10px] text-muted-foreground font-normal">Memenuhi semua syarat donor</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-2 p-3 rounded-xl border cursor-pointer transition" :class="verifyForm.status === 'suspended' ? 'border-red-500 bg-red-500/10 font-bold text-red-700 dark:text-red-300' : 'border-border'">
                                <input type="radio" v-model="verifyForm.status" value="suspended" class="text-red-600" />
                                <div>
                                    <div class="text-xs">Tangguhkan (Tidak Layak)</div>
                                    <div class="text-[10px] text-muted-foreground font-normal">Hb rendah / tensi / medis</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Medical parameters check -->
                    <div class="grid grid-cols-3 gap-3 p-3 rounded-xl bg-muted/40 border border-border">
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Tensi Sistolik</label>
                            <input v-model="verifyForm.systolic" type="number" placeholder="120" class="w-full text-xs rounded-lg border border-input bg-card px-2.5 py-1.5" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Tensi Diastolik</label>
                            <input v-model="verifyForm.diastolic" type="number" placeholder="80" class="w-full text-xs rounded-lg border border-input bg-card px-2.5 py-1.5" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Kadar Hb (g/dL)</label>
                            <input v-model="verifyForm.hemoglobin" type="number" step="0.1" placeholder="13.5" class="w-full text-xs rounded-lg border border-input bg-card px-2.5 py-1.5" />
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1">Catatan Hasil Verifikasi & Skrining</label>
                        <textarea
                            v-model="verifyForm.notes"
                            rows="3"
                            placeholder="Tuliskan catatan kelayakan donor (misal: Hb stabil di atas 12.5, bebas konsumsi antibiotik 7 hari terakhir)..."
                            class="w-full text-xs rounded-xl border border-input bg-background p-3 text-foreground focus:outline-none focus:ring-2 focus:ring-teal-500"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="isVerifyOpen = false"
                            class="px-4 py-2 rounded-xl border border-input text-xs font-medium hover:bg-muted transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="verifyForm.processing"
                            class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition shadow-sm"
                        >
                            Simpan Hasil Verifikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal 2: Tambah Calon Donor Baru -->
        <div v-if="isCreateOpen" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
            <div class="bg-card border border-border rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-border">
                    <div class="flex items-center gap-2">
                        <UserPlus class="size-5 text-red-600" />
                        <h3 class="font-bold text-base text-foreground">Pendaftaran Calon Pendonor</h3>
                    </div>
                    <button @click="isCreateOpen = false" class="text-muted-foreground hover:text-foreground">✕</button>
                </div>

                <form @submit.prevent="submitCreate" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">NIK (16 Digit) <span class="text-red-500">*</span></label>
                            <input v-model="createForm.nik" type="text" maxlength="16" required placeholder="3171xxxxxxxxxxxx" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input v-model="createForm.name" type="text" required placeholder="Nama sesuai KTP" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Golongan Darah</label>
                            <select v-model="createForm.blood_type" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2">
                                <option value="A">Golongan A</option>
                                <option value="B">Golongan B</option>
                                <option value="AB">Golongan AB</option>
                                <option value="O">Golongan O</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Rhesus</label>
                            <select v-model="createForm.rhesus" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2">
                                <option value="+">Positif (+)</option>
                                <option value="-">Negatif (-)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Nomor WhatsApp / HP <span class="text-red-500">*</span></label>
                            <input v-model="createForm.phone" type="text" required placeholder="08xxxxxxxxxx" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Email (Opsional)</label>
                            <input v-model="createForm.email" type="email" placeholder="donor@email.com" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Jenis Kelamin</label>
                            <select v-model="createForm.gender" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Tanggal Lahir</label>
                            <input v-model="createForm.birth_date" type="date" required class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Berat Badan (kg)</label>
                            <input v-model="createForm.weight" type="number" step="0.5" placeholder="60" class="w-full text-xs rounded-lg border border-input bg-card px-3 py-2" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-medium text-foreground mb-1">Alamat Tinggal</label>
                        <textarea v-model="createForm.address" rows="2" placeholder="Alamat domisili pendonor..." class="w-full text-xs rounded-lg border border-input bg-card p-2"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="isCreateOpen = false" class="px-4 py-2 rounded-xl border border-input text-xs font-medium hover:bg-muted">Batal</button>
                        <button type="submit" :disabled="createForm.processing" class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-sm">Daftarkan Calon Donor</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal 3: Detail Profil & Riwayat Donor -->
        <div v-if="selectedDonorDetail" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
            <div class="bg-card border border-border rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-border">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center justify-center font-black text-sm size-10 rounded-xl bg-red-500/10 text-red-600 border border-red-500/20">
                            {{ selectedDonorDetail.blood_type_full }}
                        </span>
                        <div>
                            <h3 class="font-bold text-base text-foreground">{{ selectedDonorDetail.name }}</h3>
                            <p class="text-xs text-muted-foreground">{{ selectedDonorDetail.donor_code }} • NIK: {{ selectedDonorDetail.nik }}</p>
                        </div>
                    </div>
                    <button @click="selectedDonorDetail = null" class="text-muted-foreground hover:text-foreground">✕</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-2 p-3 rounded-xl bg-muted/40">
                        <div><strong>Kontak WhatsApp:</strong> {{ selectedDonorDetail.phone }}</div>
                        <div><strong>Usia:</strong> {{ selectedDonorDetail.age }} tahun ({{ selectedDonorDetail.gender === 'L' ? 'Pria' : 'Wanita' }})</div>
                        <div><strong>Berat Badan:</strong> {{ selectedDonorDetail.weight ? `${selectedDonorDetail.weight} kg` : '-' }}</div>
                        <div><strong>Tekanan Darah:</strong> {{ selectedDonorDetail.systolic ? `${selectedDonorDetail.systolic}/${selectedDonorDetail.diastolic} mmHg` : '-' }}</div>
                        <div><strong>Kadar Hb:</strong> {{ selectedDonorDetail.hemoglobin ? `${selectedDonorDetail.hemoglobin} g/dL` : '-' }}</div>
                        <div><strong>Total Donasi:</strong> {{ selectedDonorDetail.total_donations }} kali</div>
                    </div>

                    <div>
                        <strong>Alamat Domisili:</strong>
                        <p class="text-muted-foreground mt-0.5">{{ selectedDonorDetail.address || '-' }}</p>
                    </div>

                    <!-- Riwayat Donasi -->
                    <div class="pt-2">
                        <div class="font-bold text-foreground mb-2 flex items-center gap-1.5">
                            <HeartPulse class="size-4 text-red-500" />
                            <span>Riwayat Donor Darah Sebelumnya</span>
                        </div>
                        <div v-if="selectedDonorDetail.histories && selectedDonorDetail.histories.length > 0" class="divide-y divide-border border rounded-xl overflow-hidden">
                            <div v-for="h in selectedDonorDetail.histories" :key="h.id" class="p-2.5 bg-card flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-foreground">🗓️ {{ h.donation_date }} ({{ h.component_type }} {{ h.volume_ml }}ml)</div>
                                    <div class="text-[10px] text-muted-foreground">Kode Kantong: {{ h.blood_bag_code || '-' }} • Tensi: {{ h.blood_pressure }}</div>
                                </div>
                                <span class="text-[10px] bg-emerald-500/10 text-emerald-600 px-2 py-0.5 rounded font-medium">Lolos Donasi</span>
                            </div>
                        </div>
                        <div v-else class="text-center py-4 text-muted-foreground text-xs bg-muted/20 rounded-xl">
                            Belum ada riwayat donasi darah yang tercatat.
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button @click="selectedDonorDetail = null" class="px-4 py-2 rounded-xl bg-zinc-800 text-white text-xs font-medium">Tutup</button>
                </div>
            </div>
        </div>

    </div>
</template>
