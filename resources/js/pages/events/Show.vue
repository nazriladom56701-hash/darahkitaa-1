<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    CalendarDays,
    ArrowLeft,
    UserCheck,
    UserPlus,
    CheckCircle2,
    XCircle,
    Clock,
    Phone,
    MapPin,
    Droplet,
    Sparkles,
    ShieldCheck,
} from '@lucide/vue';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Jadwal Donor', href: '/events' },
            { title: 'Absensi Donor', href: '#' },
        ],
    },
});

interface DonorItem {
    id: number;
    name: string;
    donor_code: string;
    blood_type: string;
    rhesus: string;
    phone: string;
    weight?: number;
    systolic?: number;
    diastolic?: number;
    hemoglobin?: number;
}

interface AttendanceItem {
    id: number;
    registration_code: string;
    attendance_status: 'registered' | 'present' | 'absent';
    screening_status: 'pending' | 'passed' | 'failed';
    blood_pressure?: string;
    hemoglobin?: number;
    blood_bag_code?: string;
    notes?: string;
    attended_at?: string;
    donor: DonorItem;
    officer?: { name: string };
}

interface EventDetail {
    id: number;
    event_code: string;
    title: string;
    organizer: string;
    location: string;
    address?: string;
    event_date: string;
    start_time: string;
    end_time: string;
    quota: number;
    target_bags: number;
    status: 'upcoming' | 'ongoing' | 'completed' | 'cancelled';
    pic_name: string;
    pic_phone: string;
    description?: string;
    attendances: AttendanceItem[];
}

const props = defineProps<{
    event: EventDetail;
    availableDonors: DonorItem[];
}>();

// Filter for attendance list
const filterStatus = ref<'all' | 'present' | 'pending' | 'absent'>('all');

function filteredAttendances() {
    if (filterStatus.value === 'present') {
        return props.event.attendances.filter(
            (a) => a.attendance_status === 'present',
        );
    }
    if (filterStatus.value === 'pending') {
        return props.event.attendances.filter(
            (a) => a.attendance_status === 'registered',
        );
    }
    if (filterStatus.value === 'absent') {
        return props.event.attendances.filter(
            (a) => a.attendance_status === 'absent',
        );
    }
    return props.event.attendances;
}

// Modal State: Aksi Admin: Absensi Donor
const isAttendanceModalOpen = ref(false);
const activeAttendance = ref<AttendanceItem | null>(null);

const attendanceForm = useForm({
    attendance_status: 'present',
    screening_status: 'passed',
    blood_pressure: '120/80',
    hemoglobin: 13.5,
    component_type: 'WB',
    notes: '',
});

function openAttendanceModal(item: AttendanceItem) {
    activeAttendance.value = item;
    attendanceForm.attendance_status =
        item.attendance_status === 'registered'
            ? 'present'
            : item.attendance_status;
    attendanceForm.screening_status =
        item.screening_status === 'pending' ? 'passed' : item.screening_status;
    attendanceForm.blood_pressure =
        item.blood_pressure ||
        (item.donor.systolic
            ? `${item.donor.systolic}/${item.donor.diastolic}`
            : '120/80');
    attendanceForm.hemoglobin =
        item.hemoglobin || item.donor.hemoglobin || 13.5;
    attendanceForm.component_type = 'WB';
    attendanceForm.notes = item.notes || '';
    isAttendanceModalOpen.value = true;
}

function submitAttendance() {
    if (!activeAttendance.value) return;
    attendanceForm.post(`/events/attendances/${activeAttendance.value.id}`, {
        onSuccess: () => {
            isAttendanceModalOpen.value = false;
            activeAttendance.value = null;
        },
    });
}

// Modal State: Tambah Peserta Baru
const isAddParticipantOpen = ref(false);
const participantForm = useForm({
    donor_id: '',
    notes: 'Pendaftaran walk-in langsung di lokasi event',
});

function submitAddParticipant() {
    participantForm.post(`/events/${props.event.id}/participants`, {
        onSuccess: () => {
            isAddParticipantOpen.value = false;
            participantForm.reset();
        },
    });
}
</script>

<template>
    <Head :title="`Absensi Donor - ${event.title}`" />

    <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 p-4 md:p-6">
        <!-- Navigation Back & Header -->
        <div
            class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
        >
            <div>
                <Link
                    href="/events"
                    class="mb-2 inline-flex items-center gap-1.5 text-xs text-muted-foreground hover:text-foreground"
                >
                    <ArrowLeft class="size-3.5" /> Kembali ke Daftar Jadwal
                </Link>
                <div class="flex items-center gap-2">
                    <h1
                        class="text-xl font-extrabold tracking-tight text-foreground md:text-2xl"
                    >
                        {{ event.title }}
                    </h1>
                    <span
                        class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase"
                        :class="{
                            'bg-emerald-500/20 text-emerald-700':
                                event.status === 'ongoing',
                            'bg-blue-500/10 text-blue-700':
                                event.status === 'upcoming',
                            'bg-zinc-500/10 text-zinc-600':
                                event.status === 'completed',
                        }"
                    >
                        {{
                            event.status === 'ongoing'
                                ? '● Berlangsung'
                                : event.status === 'upcoming'
                                  ? 'Mendatang'
                                  : 'Selesai'
                        }}
                    </span>
                </div>
                <p class="mt-0.5 text-xs text-muted-foreground">
                    📍 {{ event.location }} ({{ event.address || '-' }}) • 🗓️
                    {{ event.event_date }} ({{ event.start_time.slice(0, 5) }} -
                    {{ event.end_time.slice(0, 5) }} WIB)
                </p>
            </div>

            <button
                @click="isAddParticipantOpen = true"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-teal-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-teal-700"
            >
                <UserPlus class="size-4" /> Daftarkan Peserta Walk-in
            </button>
        </div>

        <!-- Event Progress Summary Cards -->
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
            <div class="rounded-xl border border-border bg-card p-3.5">
                <div class="text-[11px] text-muted-foreground">
                    Target Kantong Darah
                </div>
                <div class="mt-1 text-xl font-bold text-foreground">
                    {{
                        event.attendances.filter(
                            (a) => a.screening_status === 'passed',
                        ).length
                    }}
                    / {{ event.target_bags }}
                </div>
                <div class="mt-1 text-[10px] font-medium text-emerald-600">
                    {{
                        Math.round(
                            (event.attendances.filter(
                                (a) => a.screening_status === 'passed',
                            ).length /
                                (event.target_bags || 1)) *
                                100,
                        )
                    }}% tercapai
                </div>
            </div>

            <div class="rounded-xl border border-border bg-card p-3.5">
                <div class="text-[11px] text-muted-foreground">
                    Total Pendaftar
                </div>
                <div class="mt-1 text-xl font-bold text-foreground">
                    {{ event.attendances.length }} orang
                </div>
                <div class="mt-1 text-[10px] text-muted-foreground">
                    Kuota: {{ event.quota }} orang
                </div>
            </div>

            <div class="rounded-xl border border-border bg-card p-3.5">
                <div class="text-[11px] text-muted-foreground">
                    Kehadiran (Presensi)
                </div>
                <div class="mt-1 text-xl font-bold text-emerald-600">
                    {{
                        event.attendances.filter(
                            (a) => a.attendance_status === 'present',
                        ).length
                    }}
                    hadir
                </div>
                <div class="mt-1 text-[10px] text-amber-600">
                    {{
                        event.attendances.filter(
                            (a) => a.attendance_status === 'registered',
                        ).length
                    }}
                    belum absensi
                </div>
            </div>

            <div class="rounded-xl border border-border bg-card p-3.5">
                <div class="text-[11px] text-muted-foreground">
                    PIC Petugas PMI
                </div>
                <div class="mt-1 truncate text-sm font-bold text-foreground">
                    {{ event.pic_name }}
                </div>
                <div
                    class="mt-1 flex items-center gap-1 text-[10px] text-muted-foreground"
                >
                    <Phone class="size-3" /> {{ event.pic_phone }}
                </div>
            </div>
        </div>

        <!-- Absensi Table Header & Filter Tabs -->
        <div
            class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"
        >
            <div
                class="flex flex-col justify-between gap-3 border-b border-border p-4 sm:flex-row sm:items-center"
            >
                <div class="flex items-center gap-2">
                    <UserCheck class="size-5 text-teal-600" />
                    <div>
                        <h3 class="text-base font-bold text-foreground">
                            Daftar Absensi & Skrining Donor
                        </h3>
                        <p class="text-xs text-muted-foreground">
                            Admin mencatat kehadiran fisik dan kelayakan donor
                            peserta di lokasi
                        </p>
                    </div>
                </div>

                <!-- Status Filter Pills -->
                <div
                    class="flex items-center gap-1.5 rounded-xl bg-muted/60 p-1 text-xs"
                >
                    <button
                        @click="filterStatus = 'all'"
                        class="rounded-lg px-2.5 py-1 transition"
                        :class="
                            filterStatus === 'all'
                                ? 'bg-card font-bold text-foreground shadow-sm'
                                : 'text-muted-foreground hover:text-foreground'
                        "
                    >
                        Semua ({{ event.attendances.length }})
                    </button>
                    <button
                        @click="filterStatus = 'pending'"
                        class="rounded-lg px-2.5 py-1 transition"
                        :class="
                            filterStatus === 'pending'
                                ? 'bg-card font-bold text-amber-600 shadow-sm'
                                : 'text-muted-foreground hover:text-foreground'
                        "
                    >
                        Belum Absen ({{
                            event.attendances.filter(
                                (a) => a.attendance_status === 'registered',
                            ).length
                        }})
                    </button>
                    <button
                        @click="filterStatus = 'present'"
                        class="rounded-lg px-2.5 py-1 transition"
                        :class="
                            filterStatus === 'present'
                                ? 'bg-card font-bold text-emerald-600 shadow-sm'
                                : 'text-muted-foreground hover:text-foreground'
                        "
                    >
                        Hadir ({{
                            event.attendances.filter(
                                (a) => a.attendance_status === 'present',
                            ).length
                        }})
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead
                        class="border-b border-border bg-muted/40 text-[10px] tracking-wider text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-4 py-3 font-semibold">
                                No. Registrasi
                            </th>
                            <th class="px-4 py-3 font-semibold">
                                Data Pendonor
                            </th>
                            <th class="px-4 py-3 font-semibold">Gol. Darah</th>
                            <th class="px-4 py-3 font-semibold">
                                Status Presensi
                            </th>
                            <th class="px-4 py-3 font-semibold">
                                Hasil Skrining Medis
                            </th>
                            <th class="px-4 py-3 font-semibold">
                                Kantong Darah
                            </th>
                            <th class="px-4 py-3 text-right font-semibold">
                                Aksi Admin
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        <tr
                            v-for="att in filteredAttendances()"
                            :key="att.id"
                            class="transition hover:bg-muted/20"
                        >
                            <td
                                class="px-4 py-3.5 font-mono text-[11px] font-semibold text-foreground"
                            >
                                {{ att.registration_code }}
                            </td>

                            <td class="px-4 py-3.5">
                                <div class="text-sm font-bold text-foreground">
                                    {{ att.donor.name }}
                                </div>
                                <div class="text-[10px] text-muted-foreground">
                                    {{ att.donor.donor_code }} • WA:
                                    {{ att.donor.phone }}
                                </div>
                            </td>

                            <td class="px-4 py-3.5">
                                <span
                                    class="rounded border border-red-500/20 bg-red-500/10 px-2 py-0.5 font-bold text-red-600"
                                >
                                    {{ att.donor.blood_type
                                    }}{{ att.donor.rhesus }}
                                </span>
                            </td>

                            <td class="px-4 py-3.5">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                                    :class="{
                                        'border border-emerald-500/20 bg-emerald-500/10 text-emerald-700':
                                            att.attendance_status === 'present',
                                        'border border-amber-500/20 bg-amber-500/10 text-amber-700':
                                            att.attendance_status ===
                                            'registered',
                                        'bg-zinc-500/10 text-zinc-600':
                                            att.attendance_status === 'absent',
                                    }"
                                >
                                    <CheckCircle2
                                        v-if="
                                            att.attendance_status === 'present'
                                        "
                                        class="size-3"
                                    />
                                    <Clock
                                        v-else-if="
                                            att.attendance_status ===
                                            'registered'
                                        "
                                        class="size-3"
                                    />
                                    <XCircle v-else class="size-3" />
                                    {{
                                        att.attendance_status === 'present'
                                            ? 'Hadir'
                                            : att.attendance_status ===
                                                'registered'
                                              ? 'Menunggu Kehadiran'
                                              : 'Tidak Hadir'
                                    }}
                                </span>
                            </td>

                            <td class="px-4 py-3.5">
                                <div
                                    v-if="att.screening_status === 'passed'"
                                    class="space-y-0.5"
                                >
                                    <span
                                        class="text-xs font-semibold text-emerald-600"
                                        >✓ Lolos Skrining (Donor Sukses)</span
                                    >
                                    <div
                                        class="text-[10px] text-muted-foreground"
                                    >
                                        Tensi: {{ att.blood_pressure || '-' }} •
                                        Hb: {{ att.hemoglobin || '-' }}
                                    </div>
                                </div>
                                <div
                                    v-else-if="
                                        att.screening_status === 'failed'
                                    "
                                    class="space-y-0.5"
                                >
                                    <span
                                        class="text-xs font-semibold text-red-600"
                                        >✕ Gagal Skrining</span
                                    >
                                    <div
                                        class="text-[10px] text-muted-foreground"
                                    >
                                        {{
                                            att.notes ||
                                            'Kondisi belum memenuhi syarat donor'
                                        }}
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="text-[11px] text-muted-foreground"
                                >
                                    Belum diskrining di lokasi
                                </div>
                            </td>

                            <td class="px-4 py-3.5">
                                <span
                                    v-if="att.blood_bag_code"
                                    class="rounded border border-red-500/20 bg-red-500/10 px-2 py-0.5 font-mono text-[11px] font-bold text-red-700"
                                >
                                    {{ att.blood_bag_code }}
                                </span>
                                <span
                                    v-else
                                    class="text-[11px] text-muted-foreground"
                                    >-</span
                                >
                            </td>

                            <td class="px-4 py-3.5 text-right">
                                <!-- Core Aksi Admin: Absensi Donor -->
                                <button
                                    @click="openAttendanceModal(att)"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-teal-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-teal-700"
                                >
                                    <UserCheck class="size-3.5" />
                                    <span>Catat Absensi</span>
                                </button>
                            </td>
                        </tr>

                        <tr v-if="filteredAttendances().length === 0">
                            <td
                                colspan="7"
                                class="py-10 text-center text-xs text-muted-foreground"
                            >
                                Tidak ada peserta yang terdaftar pada kategori
                                filter ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal: Aksi Admin: Absensi Donor -->
        <div
            v-if="isAttendanceModalOpen && activeAttendance"
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
                            <UserCheck class="size-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-foreground">
                                Aksi Admin: Absensi & Skrining Donor
                            </h3>
                            <p class="text-xs text-muted-foreground">
                                {{ activeAttendance.donor.name }} • Gol:
                                {{ activeAttendance.donor.blood_type
                                }}{{ activeAttendance.donor.rhesus }}
                            </p>
                        </div>
                    </div>
                    <button
                        @click="isAttendanceModalOpen = false"
                        class="text-muted-foreground hover:text-foreground"
                    >
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitAttendance" class="space-y-4">
                    <!-- Status Kehadiran -->
                    <div>
                        <label
                            class="mb-1 block text-xs font-semibold text-foreground"
                            >Status Kehadiran di Tempat</label
                        >
                        <select
                            v-model="attendanceForm.attendance_status"
                            class="w-full rounded-xl border border-input bg-card px-3 py-2 text-xs"
                        >
                            <option value="present">
                                Hadir di Lokasi Event
                            </option>
                            <option value="registered">
                                Belum Hadir (Menunggu)
                            </option>
                            <option value="absent">Tidak Hadir (Batal)</option>
                        </select>
                    </div>

                    <!-- Hasil Skrining Medis -->
                    <div v-if="attendanceForm.attendance_status === 'present'">
                        <label
                            class="mb-1.5 block text-xs font-semibold text-foreground"
                            >Hasil Skrining Medis Hari H</label
                        >
                        <div class="grid grid-cols-2 gap-3">
                            <label
                                class="flex cursor-pointer items-center gap-2 rounded-xl border p-3 transition"
                                :class="
                                    attendanceForm.screening_status === 'passed'
                                        ? 'border-emerald-500 bg-emerald-500/10 font-bold text-emerald-700 dark:text-emerald-300'
                                        : 'border-border'
                                "
                            >
                                <input
                                    type="radio"
                                    v-model="attendanceForm.screening_status"
                                    value="passed"
                                />
                                <div>
                                    <div class="text-xs">
                                        Lolos Skrining (Donor)
                                    </div>
                                    <div
                                        class="text-[10px] font-normal text-muted-foreground"
                                    >
                                        +1 Stok darah otomatis
                                    </div>
                                </div>
                            </label>

                            <label
                                class="flex cursor-pointer items-center gap-2 rounded-xl border p-3 transition"
                                :class="
                                    attendanceForm.screening_status === 'failed'
                                        ? 'border-red-500 bg-red-500/10 font-bold text-red-700 dark:text-red-300'
                                        : 'border-border'
                                "
                            >
                                <input
                                    type="radio"
                                    v-model="attendanceForm.screening_status"
                                    value="failed"
                                />
                                <div>
                                    <div class="text-xs">
                                        Gagal Skrining Medis
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

                    <!-- Medical measurements if present -->
                    <div
                        v-if="attendanceForm.attendance_status === 'present'"
                        class="grid grid-cols-3 gap-3 rounded-xl border border-border bg-muted/40 p-3"
                    >
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Tekanan Darah</label
                            >
                            <input
                                v-model="attendanceForm.blood_pressure"
                                type="text"
                                placeholder="120/80"
                                class="w-full rounded-lg border border-input bg-card px-2.5 py-1.5 text-xs"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Kadar Hb (g/dL)</label
                            >
                            <input
                                v-model="attendanceForm.hemoglobin"
                                type="number"
                                step="0.1"
                                placeholder="13.5"
                                class="w-full rounded-lg border border-input bg-card px-2.5 py-1.5 text-xs"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Komponen Darah</label
                            >
                            <select
                                v-model="attendanceForm.component_type"
                                class="w-full rounded-lg border border-input bg-card px-2.5 py-1.5 text-xs"
                            >
                                <option value="WB">Whole Blood (WB)</option>
                                <option value="PRC">Packed Red (PRC)</option>
                                <option value="TC">Thrombocyte (TC)</option>
                                <option value="FFP">Fresh Plasma (FFP)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Auto stock notice -->
                    <div
                        v-if="
                            attendanceForm.attendance_status === 'present' &&
                            attendanceForm.screening_status === 'passed'
                        "
                        class="flex items-start gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/10 p-3 text-xs text-emerald-700 dark:text-emerald-300"
                    >
                        <Sparkles
                            class="mt-0.5 size-4 shrink-0 text-emerald-600"
                        />
                        <div>
                            <strong>Pembaruan Inventaris Otomatis:</strong>
                            <p class="mt-0.5 text-[11px]">
                                Sistem akan otomatis mengenerate kode kantong
                                darah baru, menambahkan stok +1 ke golongan
                                {{ activeAttendance.donor.blood_type
                                }}{{ activeAttendance.donor.rhesus }}, dan
                                memperbarui riwayat tanggal donor terakhir milik
                                {{ activeAttendance.donor.name }}.
                            </p>
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-xs font-semibold text-foreground"
                            >Catatan Tambahan Petugas</label
                        >
                        <textarea
                            v-model="attendanceForm.notes"
                            rows="2"
                            placeholder="Catatan skrining atau alasan tidak lolos..."
                            class="w-full rounded-xl border border-input bg-background p-2.5 text-xs"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="isAttendanceModalOpen = false"
                            class="rounded-xl border border-input px-4 py-2 text-xs font-medium hover:bg-muted"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="attendanceForm.processing"
                            class="rounded-xl bg-teal-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-teal-700"
                        >
                            Simpan Hasil Absensi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Tambah Peserta Baru -->
        <div
            v-if="isAddParticipantOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        >
            <div
                class="w-full max-w-md space-y-4 rounded-2xl border border-border bg-card p-6 shadow-2xl"
            >
                <div
                    class="flex items-center justify-between border-b border-border pb-3"
                >
                    <h3 class="text-base font-bold text-foreground">
                        Daftarkan Peserta ke Event Ini
                    </h3>
                    <button
                        @click="isAddParticipantOpen = false"
                        class="text-muted-foreground hover:text-foreground"
                    >
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitAddParticipant" class="space-y-3">
                    <div>
                        <label
                            class="mb-1 block text-xs font-medium text-foreground"
                            >Pilih Calon Donor Terdaftar</label
                        >
                        <select
                            v-model="participantForm.donor_id"
                            required
                            class="w-full rounded-xl border border-input bg-card px-3 py-2 text-xs"
                        >
                            <option value="" disabled>-- Pilih Donor --</option>
                            <option
                                v-for="d in availableDonors"
                                :key="d.id"
                                :value="d.id"
                            >
                                {{ d.name }} (Gol. {{ d.blood_type
                                }}{{ d.rhesus }} - {{ d.donor_code }})
                            </option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-xs font-medium text-foreground"
                            >Keterangan / Catatan Pendaftaran</label
                        >
                        <input
                            v-model="participantForm.notes"
                            type="text"
                            placeholder="Contoh: Datang langsung ke meja registrasi..."
                            class="w-full rounded-xl border border-input bg-card px-3 py-2 text-xs"
                        />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="isAddParticipantOpen = false"
                            class="rounded-xl border border-input px-4 py-2 text-xs font-medium hover:bg-muted"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="participantForm.processing"
                            class="rounded-xl bg-teal-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-teal-700"
                        >
                            Tambahkan Peserta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
