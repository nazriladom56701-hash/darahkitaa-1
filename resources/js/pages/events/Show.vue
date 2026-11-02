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
    ShieldCheck
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
        return props.event.attendances.filter(a => a.attendance_status === 'present');
    }
    if (filterStatus.value === 'pending') {
        return props.event.attendances.filter(a => a.attendance_status === 'registered');
    }
    if (filterStatus.value === 'absent') {
        return props.event.attendances.filter(a => a.attendance_status === 'absent');
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
    attendanceForm.attendance_status = item.attendance_status === 'registered' ? 'present' : item.attendance_status;
    attendanceForm.screening_status = item.screening_status === 'pending' ? 'passed' : item.screening_status;
    attendanceForm.blood_pressure = item.blood_pressure || (item.donor.systolic ? `${item.donor.systolic}/${item.donor.diastolic}` : '120/80');
    attendanceForm.hemoglobin = item.hemoglobin || item.donor.hemoglobin || 13.5;
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

    <div class="flex flex-col gap-6 p-4 md:p-6 max-w-7xl mx-auto w-full">
        
        <!-- Navigation Back & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <Link href="/events" class="inline-flex items-center gap-1.5 text-xs text-muted-foreground hover:text-foreground mb-2">
                    <ArrowLeft class="size-3.5" /> Kembali ke Daftar Jadwal
                </Link>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl md:text-2xl font-extrabold text-foreground tracking-tight">{{ event.title }}</h1>
                    <span 
                        class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full"
                        :class="{
                            'bg-emerald-500/20 text-emerald-700': event.status === 'ongoing',
                            'bg-blue-500/10 text-blue-700': event.status === 'upcoming',
                            'bg-zinc-500/10 text-zinc-600': event.status === 'completed'
                        }"
                    >
                        {{ event.status === 'ongoing' ? '● Berlangsung' : event.status === 'upcoming' ? 'Mendatang' : 'Selesai' }}
                    </span>
                </div>
                <p class="text-xs text-muted-foreground mt-0.5">
                    📍 {{ event.location }} ({{ event.address || '-' }}) • 🗓️ {{ event.event_date }} ({{ event.start_time.slice(0, 5) }} - {{ event.end_time.slice(0, 5) }} WIB)
                </p>
            </div>

            <button
                @click="isAddParticipantOpen = true"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-sm transition"
            >
                <UserPlus class="size-4" /> Daftarkan Peserta Walk-in
            </button>
        </div>

        <!-- Event Progress Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="p-3.5 rounded-xl bg-card border border-border">
                <div class="text-[11px] text-muted-foreground">Target Kantong Darah</div>
                <div class="text-xl font-bold text-foreground mt-1">
                    {{ event.attendances.filter(a => a.screening_status === 'passed').length }} / {{ event.target_bags }}
                </div>
                <div class="text-[10px] text-emerald-600 font-medium mt-1">
                    {{ Math.round((event.attendances.filter(a => a.screening_status === 'passed').length / (event.target_bags || 1)) * 100) }}% tercapai
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-card border border-border">
                <div class="text-[11px] text-muted-foreground">Total Pendaftar</div>
                <div class="text-xl font-bold text-foreground mt-1">{{ event.attendances.length }} orang</div>
                <div class="text-[10px] text-muted-foreground mt-1">Kuota: {{ event.quota }} orang</div>
            </div>

            <div class="p-3.5 rounded-xl bg-card border border-border">
                <div class="text-[11px] text-muted-foreground">Kehadiran (Presensi)</div>
                <div class="text-xl font-bold text-emerald-600 mt-1">
                    {{ event.attendances.filter(a => a.attendance_status === 'present').length }} hadir
                </div>
                <div class="text-[10px] text-amber-600 mt-1">
                    {{ event.attendances.filter(a => a.attendance_status === 'registered').length }} belum absensi
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-card border border-border">
                <div class="text-[11px] text-muted-foreground">PIC Petugas PMI</div>
                <div class="text-sm font-bold text-foreground mt-1 truncate">{{ event.pic_name }}</div>
                <div class="text-[10px] text-muted-foreground mt-1 flex items-center gap-1">
                    <Phone class="size-3" /> {{ event.pic_phone }}
                </div>
            </div>
        </div>

        <!-- Absensi Table Header & Filter Tabs -->
        <div class="rounded-2xl border border-border bg-card overflow-hidden shadow-sm">
            <div class="p-4 border-b border-border flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <UserCheck class="size-5 text-teal-600" />
                    <div>
                        <h3 class="font-bold text-base text-foreground">Daftar Absensi & Skrining Donor</h3>
                        <p class="text-xs text-muted-foreground">Admin mencatat kehadiran fisik dan kelayakan donor peserta di lokasi</p>
                    </div>
                </div>

                <!-- Status Filter Pills -->
                <div class="flex items-center gap-1.5 text-xs bg-muted/60 p-1 rounded-xl">
                    <button
                        @click="filterStatus = 'all'"
                        class="px-2.5 py-1 rounded-lg transition"
                        :class="filterStatus === 'all' ? 'bg-card font-bold text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                    >
                        Semua ({{ event.attendances.length }})
                    </button>
                    <button
                        @click="filterStatus = 'pending'"
                        class="px-2.5 py-1 rounded-lg transition"
                        :class="filterStatus === 'pending' ? 'bg-card font-bold text-amber-600 shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                    >
                        Belum Absen ({{ event.attendances.filter(a => a.attendance_status === 'registered').length }})
                    </button>
                    <button
                        @click="filterStatus = 'present'"
                        class="px-2.5 py-1 rounded-lg transition"
                        :class="filterStatus === 'present' ? 'bg-card font-bold text-emerald-600 shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                    >
                        Hadir ({{ event.attendances.filter(a => a.attendance_status === 'present').length }})
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-muted/40 border-b border-border text-muted-foreground uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No. Registrasi</th>
                            <th class="px-4 py-3 font-semibold">Data Pendonor</th>
                            <th class="px-4 py-3 font-semibold">Gol. Darah</th>
                            <th class="px-4 py-3 font-semibold">Status Presensi</th>
                            <th class="px-4 py-3 font-semibold">Hasil Skrining Medis</th>
                            <th class="px-4 py-3 font-semibold">Kantong Darah</th>
                            <th class="px-4 py-3 font-semibold text-right">Aksi Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        <tr v-for="att in filteredAttendances()" :key="att.id" class="hover:bg-muted/20 transition">
                            <td class="px-4 py-3.5 font-mono text-[11px] font-semibold text-foreground">
                                {{ att.registration_code }}
                            </td>

                            <td class="px-4 py-3.5">
                                <div class="font-bold text-foreground text-sm">{{ att.donor.name }}</div>
                                <div class="text-[10px] text-muted-foreground">{{ att.donor.donor_code }} • WA: {{ att.donor.phone }}</div>
                            </td>

                            <td class="px-4 py-3.5">
                                <span class="font-bold text-red-600 bg-red-500/10 px-2 py-0.5 rounded border border-red-500/20">
                                    {{ att.donor.blood_type }}{{ att.donor.rhesus }}
                                </span>
                            </td>

                            <td class="px-4 py-3.5">
                                <span 
                                    class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full"
                                    :class="{
                                        'bg-emerald-500/10 text-emerald-700 border border-emerald-500/20': att.attendance_status === 'present',
                                        'bg-amber-500/10 text-amber-700 border border-amber-500/20': att.attendance_status === 'registered',
                                        'bg-zinc-500/10 text-zinc-600': att.attendance_status === 'absent'
                                    }"
                                >
                                    <CheckCircle2 v-if="att.attendance_status === 'present'" class="size-3" />
                                    <Clock v-else-if="att.attendance_status === 'registered'" class="size-3" />
                                    <XCircle v-else class="size-3" />
                                    {{ att.attendance_status === 'present' ? 'Hadir' : att.attendance_status === 'registered' ? 'Menunggu Kehadiran' : 'Tidak Hadir' }}
                                </span>
                            </td>

                            <td class="px-4 py-3.5">
                                <div v-if="att.screening_status === 'passed'" class="space-y-0.5">
                                    <span class="text-xs font-semibold text-emerald-600">✓ Lolos Skrining (Donor Sukses)</span>
                                    <div class="text-[10px] text-muted-foreground">Tensi: {{ att.blood_pressure || '-' }} • Hb: {{ att.hemoglobin || '-' }}</div>
                                </div>
                                <div v-else-if="att.screening_status === 'failed'" class="space-y-0.5">
                                    <span class="text-xs font-semibold text-red-600">✕ Gagal Skrining</span>
                                    <div class="text-[10px] text-muted-foreground">{{ att.notes || 'Kondisi belum memenuhi syarat donor' }}</div>
                                </div>
                                <div v-else class="text-[11px] text-muted-foreground">
                                    Belum diskrining di lokasi
                                </div>
                            </td>

                            <td class="px-4 py-3.5">
                                <span v-if="att.blood_bag_code" class="font-mono text-[11px] bg-red-500/10 text-red-700 px-2 py-0.5 rounded font-bold border border-red-500/20">
                                    {{ att.blood_bag_code }}
                                </span>
                                <span v-else class="text-muted-foreground text-[11px]">-</span>
                            </td>

                            <td class="px-4 py-3.5 text-right">
                                <!-- Core Aksi Admin: Absensi Donor -->
                                <button
                                    @click="openAttendanceModal(att)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-sm transition"
                                >
                                    <UserCheck class="size-3.5" />
                                    <span>Catat Absensi</span>
                                </button>
                            </td>
                        </tr>

                        <tr v-if="filteredAttendances().length === 0">
                            <td colspan="7" class="py-10 text-center text-muted-foreground text-xs">
                                Tidak ada peserta yang terdaftar pada kategori filter ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal: Aksi Admin: Absensi Donor -->
        <div v-if="isAttendanceModalOpen && activeAttendance" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
            <div class="bg-card border border-border rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-border">
                    <div class="flex items-center gap-2.5">
                        <div class="size-9 rounded-lg bg-teal-500/10 text-teal-600 flex items-center justify-center">
                            <UserCheck class="size-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-foreground">Aksi Admin: Absensi & Skrining Donor</h3>
                            <p class="text-xs text-muted-foreground">{{ activeAttendance.donor.name }} • Gol: {{ activeAttendance.donor.blood_type }}{{ activeAttendance.donor.rhesus }}</p>
                        </div>
                    </div>
                    <button @click="isAttendanceModalOpen = false" class="text-muted-foreground hover:text-foreground">✕</button>
                </div>

                <form @submit.prevent="submitAttendance" class="space-y-4">
                    <!-- Status Kehadiran -->
                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1">Status Kehadiran di Tempat</label>
                        <select v-model="attendanceForm.attendance_status" class="w-full text-xs rounded-xl border border-input bg-card px-3 py-2">
                            <option value="present">Hadir di Lokasi Event</option>
                            <option value="registered">Belum Hadir (Menunggu)</option>
                            <option value="absent">Tidak Hadir (Batal)</option>
                        </select>
                    </div>

                    <!-- Hasil Skrining Medis -->
                    <div v-if="attendanceForm.attendance_status === 'present'">
                        <label class="block text-xs font-semibold text-foreground mb-1.5">Hasil Skrining Medis Hari H</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2 p-3 rounded-xl border cursor-pointer transition" :class="attendanceForm.screening_status === 'passed' ? 'border-emerald-500 bg-emerald-500/10 font-bold text-emerald-700 dark:text-emerald-300' : 'border-border'">
                                <input type="radio" v-model="attendanceForm.screening_status" value="passed" />
                                <div>
                                    <div class="text-xs">Lolos Skrining (Donor)</div>
                                    <div class="text-[10px] text-muted-foreground font-normal">+1 Stok darah otomatis</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-2 p-3 rounded-xl border cursor-pointer transition" :class="attendanceForm.screening_status === 'failed' ? 'border-red-500 bg-red-500/10 font-bold text-red-700 dark:text-red-300' : 'border-border'">
                                <input type="radio" v-model="attendanceForm.screening_status" value="failed" />
                                <div>
                                    <div class="text-xs">Gagal Skrining Medis</div>
                                    <div class="text-[10px] text-muted-foreground font-normal">Hb rendah / tensi / medis</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Medical measurements if present -->
                    <div v-if="attendanceForm.attendance_status === 'present'" class="grid grid-cols-3 gap-3 p-3 rounded-xl bg-muted/40 border border-border">
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Tekanan Darah</label>
                            <input v-model="attendanceForm.blood_pressure" type="text" placeholder="120/80" class="w-full text-xs rounded-lg border border-input bg-card px-2.5 py-1.5" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Kadar Hb (g/dL)</label>
                            <input v-model="attendanceForm.hemoglobin" type="number" step="0.1" placeholder="13.5" class="w-full text-xs rounded-lg border border-input bg-card px-2.5 py-1.5" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Komponen Darah</label>
                            <select v-model="attendanceForm.component_type" class="w-full text-xs rounded-lg border border-input bg-card px-2.5 py-1.5">
                                <option value="WB">Whole Blood (WB)</option>
                                <option value="PRC">Packed Red (PRC)</option>
                                <option value="TC">Thrombocyte (TC)</option>
                                <option value="FFP">Fresh Plasma (FFP)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Auto stock notice -->
                    <div v-if="attendanceForm.attendance_status === 'present' && attendanceForm.screening_status === 'passed'" class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-700 dark:text-emerald-300 flex items-start gap-2">
                        <Sparkles class="size-4 shrink-0 mt-0.5 text-emerald-600" />
                        <div>
                            <strong>Pembaruan Inventaris Otomatis:</strong>
                            <p class="text-[11px] mt-0.5">
                                Sistem akan otomatis mengenerate kode kantong darah baru, menambahkan stok +1 ke golongan {{ activeAttendance.donor.blood_type }}{{ activeAttendance.donor.rhesus }}, dan memperbarui riwayat tanggal donor terakhir milik {{ activeAttendance.donor.name }}.
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1">Catatan Tambahan Petugas</label>
                        <textarea v-model="attendanceForm.notes" rows="2" placeholder="Catatan skrining atau alasan tidak lolos..." class="w-full text-xs rounded-xl border border-input bg-background p-2.5"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="isAttendanceModalOpen = false" class="px-4 py-2 rounded-xl border border-input text-xs font-medium hover:bg-muted">Batal</button>
                        <button type="submit" :disabled="attendanceForm.processing" class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-sm">
                            Simpan Hasil Absensi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Tambah Peserta Baru -->
        <div v-if="isAddParticipantOpen" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
            <div class="bg-card border border-border rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-border">
                    <h3 class="font-bold text-base text-foreground">Daftarkan Peserta ke Event Ini</h3>
                    <button @click="isAddParticipantOpen = false" class="text-muted-foreground hover:text-foreground">✕</button>
                </div>

                <form @submit.prevent="submitAddParticipant" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-foreground mb-1">Pilih Calon Donor Terdaftar</label>
                        <select v-model="participantForm.donor_id" required class="w-full text-xs rounded-xl border border-input bg-card px-3 py-2">
                            <option value="" disabled>-- Pilih Donor --</option>
                            <option v-for="d in availableDonors" :key="d.id" :value="d.id">
                                {{ d.name }} (Gol. {{ d.blood_type }}{{ d.rhesus }} - {{ d.donor_code }})
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-foreground mb-1">Keterangan / Catatan Pendaftaran</label>
                        <input v-model="participantForm.notes" type="text" placeholder="Contoh: Datang langsung ke meja registrasi..." class="w-full text-xs rounded-xl border border-input bg-card px-3 py-2" />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="isAddParticipantOpen = false" class="px-4 py-2 rounded-xl border border-input text-xs font-medium hover:bg-muted">Batal</button>
                        <button type="submit" :disabled="participantForm.processing" class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-sm">Tambahkan Peserta</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>
