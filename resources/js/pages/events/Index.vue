<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Calendar,
    CalendarPlus,
    Search,
    MapPin,
    Clock,
    Users,
    Droplets,
    ArrowRight,
    CheckCircle2,
    Building,
    Phone,
} from '@lucide/vue';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Jadwal Donor', href: '/events' },
        ],
    },
});

interface EventItem {
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
    registered_count: number;
    present_count: number;
    passed_count: number;
}

const props = defineProps<{
    events: {
        data: EventItem[];
        current_page: number;
        last_page: number;
        total: number;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: {
        search?: string;
        status?: string;
    };
    counts: {
        total: number;
        upcoming: number;
        ongoing: number;
        completed: number;
    };
}>();

const searchQuery = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || '');

function applyFilters() {
    router.get(
        '/events',
        {
            search: searchQuery.value || undefined,
            status: selectedStatus.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}

function setStatusFilter(status: string) {
    selectedStatus.value = status;
    applyFilters();
}

// Modal State: Create Event
const isCreateOpen = ref(false);
const createForm = useForm({
    title: '',
    organizer: 'PMI & Mitra',
    location: '',
    address: '',
    event_date: new Date(Date.now() + 86400000 * 3).toISOString().slice(0, 10),
    start_time: '08:30',
    end_time: '14:00',
    quota: 100,
    target_bags: 80,
    pic_name: '',
    pic_phone: '',
    description: '',
});

function submitCreate() {
    createForm.post('/events', {
        onSuccess: () => {
            isCreateOpen.value = false;
            createForm.reset();
        },
    });
}
</script>

<template>
    <Head title="Jadwal Donor - DarahKitaa" />

    <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 p-4 md:p-6">
        <!-- Header -->
        <div
            class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
        >
            <div>
                <div class="flex items-center gap-2">
                    <span
                        class="rounded-lg bg-teal-500/10 p-2 text-teal-600 dark:text-teal-400"
                    >
                        <Calendar class="size-5" />
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1
                                class="text-xl font-extrabold tracking-tight text-foreground md:text-2xl"
                            >
                                Jadwal Donor Darah
                            </h1>
                            <span
                                class="rounded border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400"
                            >
                                Aksi Admin: Absensi Donor
                            </span>
                        </div>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            Kelola jadwal donor darah mobile unit, target kuota
                            kantong, dan lakukan absensi kehadiran peserta.
                        </p>
                    </div>
                </div>
            </div>

            <button
                @click="isCreateOpen = true"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-teal-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-teal-700"
            >
                <CalendarPlus class="size-4" /> Jadwalkan Event Baru
            </button>
        </div>

        <!-- Filter Cards -->
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
                        Semua Event
                    </div>
                    <div class="text-lg font-bold text-foreground">
                        {{ counts.total }}
                    </div>
                </div>
                <Calendar class="size-4 text-muted-foreground" />
            </button>

            <button
                @click="setStatusFilter('ongoing')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition"
                :class="
                    selectedStatus === 'ongoing'
                        ? 'border-emerald-500 bg-emerald-500/10 ring-1 ring-emerald-500'
                        : 'border-border bg-card hover:border-border/80'
                "
            >
                <div>
                    <div class="text-[11px] font-medium text-emerald-600">
                        Sedang Berlangsung
                    </div>
                    <div class="text-lg font-bold text-foreground">
                        {{ counts.ongoing }}
                    </div>
                </div>
                <Clock class="size-4 animate-pulse text-emerald-500" />
            </button>

            <button
                @click="setStatusFilter('upcoming')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition"
                :class="
                    selectedStatus === 'upcoming'
                        ? 'border-blue-500 bg-blue-500/10 ring-1 ring-blue-500'
                        : 'border-border bg-card hover:border-border/80'
                "
            >
                <div>
                    <div class="text-[11px] font-medium text-blue-600">
                        Mendatang
                    </div>
                    <div class="text-lg font-bold text-foreground">
                        {{ counts.upcoming }}
                    </div>
                </div>
                <Calendar class="size-4 text-blue-500" />
            </button>

            <button
                @click="setStatusFilter('completed')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition"
                :class="
                    selectedStatus === 'completed'
                        ? 'border-zinc-500 bg-zinc-500/10 ring-1 ring-zinc-500'
                        : 'border-border bg-card hover:border-border/80'
                "
            >
                <div>
                    <div class="text-[11px] font-medium text-zinc-500">
                        Selesai
                    </div>
                    <div class="text-lg font-bold text-foreground">
                        {{ counts.completed }}
                    </div>
                </div>
                <CheckCircle2 class="size-4 text-zinc-500" />
            </button>
        </div>

        <!-- Search Bar -->
        <div class="flex gap-3">
            <div class="relative flex-1">
                <Search
                    class="absolute top-3 left-3 size-4 text-muted-foreground"
                />
                <input
                    v-model="searchQuery"
                    @keyup.enter="applyFilters"
                    type="text"
                    placeholder="Cari event berdasarkan judul, lokasi, atau penyelenggara..."
                    class="w-full rounded-xl border border-input bg-card py-2.5 pr-4 pl-9 text-xs text-foreground shadow-sm focus:ring-2 focus:ring-teal-500 focus:outline-none"
                />
            </div>
            <button
                @click="applyFilters"
                class="rounded-xl bg-zinc-800 px-4 py-2.5 text-xs font-medium text-white transition hover:bg-zinc-700"
            >
                Cari
            </button>
        </div>

        <!-- Grid of Event Cards -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div
                v-for="evt in events.data"
                :key="evt.id"
                class="flex flex-col justify-between space-y-4 rounded-2xl border border-border bg-card p-5 transition hover:shadow-md"
            >
                <div>
                    <!-- Top row: Status & Code -->
                    <div class="flex items-center justify-between">
                        <span
                            class="font-mono text-[10px] text-muted-foreground"
                            >{{ evt.event_code }}</span
                        >
                        <span
                            class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase"
                            :class="{
                                'border border-emerald-500/20 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400':
                                    evt.status === 'ongoing',
                                'border border-blue-500/20 bg-blue-500/10 text-blue-700 dark:text-blue-400':
                                    evt.status === 'upcoming',
                                'bg-zinc-500/10 text-zinc-600 dark:text-zinc-400':
                                    evt.status === 'completed',
                            }"
                        >
                            {{
                                evt.status === 'ongoing'
                                    ? '● Sedang Berlangsung'
                                    : evt.status === 'upcoming'
                                      ? 'Mendatang'
                                      : 'Selesai'
                            }}
                        </span>
                    </div>

                    <!-- Title & Organizer -->
                    <h3 class="mt-2 text-base font-bold text-foreground">
                        {{ evt.title }}
                    </h3>
                    <p
                        class="mt-0.5 flex items-center gap-1.5 text-xs text-muted-foreground"
                    >
                        <Building class="size-3.5 text-muted-foreground" />
                        {{ evt.organizer }}
                    </p>

                    <!-- Details: Location & Date Time -->
                    <div class="mt-3 space-y-1 text-xs text-muted-foreground">
                        <div class="flex items-center gap-1.5">
                            <MapPin class="size-3.5 shrink-0 text-red-500" />
                            <span
                                class="truncate font-medium text-foreground"
                                >{{ evt.location }}</span
                            >
                        </div>
                        <div class="flex items-center gap-1.5">
                            <Clock class="size-3.5 shrink-0 text-amber-500" />
                            <span
                                >{{ evt.event_date }} • Pukul
                                {{ evt.start_time.slice(0, 5) }} -
                                {{ evt.end_time.slice(0, 5) }} WIB</span
                            >
                        </div>
                    </div>

                    <!-- Quota & Target Bags Progress Bar -->
                    <div
                        class="mt-4 space-y-2 rounded-xl border border-border bg-muted/40 p-3"
                    >
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-muted-foreground"
                                >Target Kantong Darah:</span
                            >
                            <span class="font-bold text-foreground"
                                >{{ evt.passed_count }} /
                                {{ evt.target_bags }} kantong</span
                            >
                        </div>
                        <div
                            class="h-2 w-full overflow-hidden rounded-full bg-border"
                        >
                            <div
                                class="h-full rounded-full bg-red-600 transition-all"
                                :style="{
                                    width: `${Math.min(100, (evt.passed_count / (evt.target_bags || 1)) * 100)}%`,
                                }"
                            ></div>
                        </div>

                        <div
                            class="flex items-center justify-between pt-1 text-[11px] text-muted-foreground"
                        >
                            <span
                                >Kuota:
                                <strong>{{ evt.quota }}</strong> orang</span
                            >
                            <span
                                >Terdaftar:
                                <strong>{{
                                    evt.registered_count
                                }}</strong></span
                            >
                            <span
                                >Hadir:
                                <strong class="text-emerald-600">{{
                                    evt.present_count
                                }}</strong></span
                            >
                        </div>
                    </div>
                </div>

                <!-- Footer: PIC & Action Button -->
                <div
                    class="flex items-center justify-between border-t border-border pt-3"
                >
                    <div class="text-[11px] text-muted-foreground">
                        PIC:
                        <span class="font-medium text-foreground">{{
                            evt.pic_name
                        }}</span>
                        ({{ evt.pic_phone }})
                    </div>

                    <!-- Aksi Admin: Absensi Donor Link -->
                    <Link
                        :href="`/events/${evt.id}`"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-teal-600 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-teal-700"
                    >
                        <span>Absensi Donor</span>
                        <ArrowRight class="size-3.5" />
                    </Link>
                </div>
            </div>

            <div
                v-if="events.data.length === 0"
                class="col-span-full py-12 text-center text-sm text-muted-foreground"
            >
                Tidak ada kegiatan donor darah yang ditemukan.
            </div>
        </div>

        <!-- Modal Buat Jadwal Event Baru -->
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
                        <CalendarPlus class="size-5 text-teal-600" />
                        <h3 class="text-base font-bold text-foreground">
                            Jadwalkan Aksi Donor Darah Baru
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
                            >Nama / Judul Event
                            <span class="text-red-500">*</span></label
                        >
                        <input
                            v-model="createForm.title"
                            type="text"
                            required
                            placeholder="Contoh: Donor Darah Bersama BEM UI"
                            class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-[11px] font-medium text-foreground"
                            >Instansi / Penyelenggara
                            <span class="text-red-500">*</span></label
                        >
                        <input
                            v-model="createForm.organizer"
                            type="text"
                            required
                            placeholder="Contoh: PMI Jakarta Selatan & PT Pertamina"
                            class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Nama Tempat / Lokasi
                                <span class="text-red-500">*</span></label
                            >
                            <input
                                v-model="createForm.location"
                                type="text"
                                required
                                placeholder="Contoh: Balairung UI / Mall Senayan City"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Tanggal Kegiatan
                                <span class="text-red-500">*</span></label
                            >
                            <input
                                v-model="createForm.event_date"
                                type="date"
                                required
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Jam Mulai</label
                            >
                            <input
                                v-model="createForm.start_time"
                                type="time"
                                required
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Jam Selesai</label
                            >
                            <input
                                v-model="createForm.end_time"
                                type="time"
                                required
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Kuota Maksimal Peserta</label
                            >
                            <input
                                v-model="createForm.quota"
                                type="number"
                                required
                                placeholder="100"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Target Kantong Darah</label
                            >
                            <input
                                v-model="createForm.target_bags"
                                type="number"
                                required
                                placeholder="80"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Nama Petugas PIC PMI</label
                            >
                            <input
                                v-model="createForm.pic_name"
                                type="text"
                                required
                                placeholder="dr. Sarah Amelia"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Nomor Kontak PIC</label
                            >
                            <input
                                v-model="createForm.pic_phone"
                                type="text"
                                required
                                placeholder="0812xxxxxxxx"
                                class="w-full rounded-lg border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-[11px] font-medium text-foreground"
                            >Alamat Lengkap & Keterangan</label
                        >
                        <textarea
                            v-model="createForm.address"
                            rows="2"
                            placeholder="Alamat jalan detail dan petunjuk ruangan..."
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
                            class="rounded-xl bg-teal-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-teal-700"
                        >
                            Simpan Jadwal Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
