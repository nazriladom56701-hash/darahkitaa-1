<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Droplet,
    Droplets,
    Plus,
    RefreshCw,
    AlertTriangle,
    CheckCircle2,
    Clock,
    History,
    TrendingUp,
    TrendingDown,
    ShieldAlert,
    Trash2,
    SlidersHorizontal,
} from '@lucide/vue';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Stok Darah', href: '/stocks' },
        ],
    },
});

interface StockItem {
    id: number;
    blood_type: string;
    rhesus: string;
    blood_type_full: string;
    component: string;
    total_units: number;
    min_threshold: number;
    status_level: 'safe' | 'warning' | 'critical';
}

interface GroupedStock {
    blood_type: string;
    rhesus: string;
    blood_type_full: string;
    total_units: number;
    min_threshold: number;
    components: Record<string, number>;
}

interface MutationItem {
    id: number;
    blood_type: string;
    rhesus: string;
    component: string;
    type: 'in' | 'out' | 'expired' | 'adjustment';
    quantity: number;
    bag_code?: string;
    reference_type?: string;
    notes?: string;
    created_at: string;
    officer?: { name: string };
}

const props = defineProps<{
    stocks: StockItem[];
    groupedStocks: GroupedStock[];
    mutations: {
        data: MutationItem[];
        current_page: number;
        last_page: number;
        total: number;
    };
    stats: {
        total_units: number;
        critical_count: number;
        warning_count: number;
        safe_count: number;
    };
}>();

// Modal State: Aksi Admin: Update Stok
const isUpdateModalOpen = ref(false);
const updateForm = useForm({
    blood_type: 'O',
    rhesus: '+',
    component: 'WB',
    mutation_type: 'in',
    quantity: 1,
    bag_code: '',
    notes: 'Penerimaan donasi langsung UDD PMI',
});

function openUpdateModal(defaultBloodType?: string, defaultRhesus?: string) {
    if (defaultBloodType) updateForm.blood_type = defaultBloodType;
    if (defaultRhesus) updateForm.rhesus = defaultRhesus;
    isUpdateModalOpen.value = true;
}

function submitUpdate() {
    updateForm.post('/stocks/update', {
        onSuccess: () => {
            isUpdateModalOpen.value = false;
            updateForm.reset();
        },
    });
}
</script>

<template>
    <Head title="Stok Darah - DarahKitaa" />

    <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 p-4 md:p-6">
        <!-- Header -->
        <div
            class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
        >
            <div>
                <div class="flex items-center gap-2">
                    <span
                        class="rounded-lg bg-red-500/10 p-2 text-red-600 dark:text-red-400"
                    >
                        <Droplets class="size-5" />
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1
                                class="text-xl font-extrabold tracking-tight text-foreground md:text-2xl"
                            >
                                Inventaris Stok Darah
                            </h1>
                            <span
                                class="rounded border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400"
                            >
                                Aksi Admin: Update Stok
                            </span>
                        </div>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            Monitoring persediaan kantong darah per golongan dan
                            komponen (WB, PRC, TC, FFP) secara real-time.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Core Aksi Admin Trigger -->
            <button
                @click="openUpdateModal()"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-red-700"
            >
                <Plus class="size-4" /> Update Stok Darah
            </button>
        </div>

        <!-- Metric Badges -->
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
            <div class="rounded-xl border border-border bg-card p-3.5">
                <div class="text-[11px] text-muted-foreground">
                    Total Kantong Darah
                </div>
                <div class="mt-1 text-2xl font-black text-foreground">
                    {{ stats.total_units }}
                    <span class="text-xs font-normal text-muted-foreground"
                        >kantong</span
                    >
                </div>
                <div class="mt-0.5 text-[10px] text-muted-foreground">
                    Semua golongan & komponen
                </div>
            </div>

            <div
                class="rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-3.5"
            >
                <div
                    class="text-[11px] font-medium text-emerald-700 dark:text-emerald-400"
                >
                    Stok Aman (> Ambang Batas)
                </div>
                <div class="mt-1 text-2xl font-black text-emerald-600">
                    {{ stats.safe_count }}
                    <span class="text-xs font-normal">golongan</span>
                </div>
                <div class="mt-0.5 text-[10px] text-emerald-600/80">
                    Kebutuhan tercukupi
                </div>
            </div>

            <div
                class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-3.5"
            >
                <div
                    class="text-[11px] font-medium text-amber-700 dark:text-amber-400"
                >
                    Stok Menipis (Warning)
                </div>
                <div class="mt-1 text-2xl font-black text-amber-600">
                    {{ stats.warning_count }}
                    <span class="text-xs font-normal">golongan</span>
                </div>
                <div class="mt-0.5 text-[10px] text-amber-600/80">
                    Perlu dorongan donor
                </div>
            </div>

            <div class="rounded-xl border border-red-500/30 bg-red-500/5 p-3.5">
                <div
                    class="flex items-center gap-1 text-[11px] font-bold text-red-700 dark:text-red-400"
                >
                    <ShieldAlert class="size-3.5 text-red-600" />
                    <span>Stok Kritis!</span>
                </div>
                <div class="mt-1 text-2xl font-black text-red-600">
                    {{ stats.critical_count }}
                    <span class="text-xs font-normal">golongan</span>
                </div>
                <div class="mt-0.5 text-[10px] font-medium text-red-600/80">
                    Butuh panggilan darurat
                </div>
            </div>
        </div>

        <!-- 8 Blood Groups Cards Grid -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3
                    class="flex items-center gap-2 text-base font-bold text-foreground"
                >
                    <Droplet class="size-4 text-red-600" />
                    <span>Ringkasan Stok Berdasarkan 8 Golongan Darah</span>
                </h3>
                <span class="text-xs text-muted-foreground"
                    >Klik "+ Update" pada kartu untuk mutasi cepat</span
                >
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="grp in groupedStocks"
                    :key="grp.blood_type_full"
                    class="flex flex-col justify-between space-y-3 rounded-2xl border bg-card p-4 transition hover:shadow-md"
                    :class="{
                        'border-emerald-500/30 bg-emerald-500/[0.02]':
                            grp.total_units > grp.min_threshold,
                        'border-amber-500/40 bg-amber-500/[0.04]':
                            grp.total_units <= grp.min_threshold &&
                            grp.total_units > grp.min_threshold / 2,
                        'border-red-500/50 bg-red-500/[0.06] ring-1 ring-red-500/30':
                            grp.total_units <= grp.min_threshold / 2,
                    }"
                >
                    <!-- Top: Blood group name & level badge -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span
                                class="text-2xl font-black"
                                :class="{
                                    'text-emerald-600':
                                        grp.total_units > grp.min_threshold,
                                    'text-amber-600':
                                        grp.total_units <= grp.min_threshold &&
                                        grp.total_units > grp.min_threshold / 2,
                                    'text-red-600':
                                        grp.total_units <=
                                        grp.min_threshold / 2,
                                }"
                            >
                                {{ grp.blood_type_full }}
                            </span>
                            <span
                                class="font-mono text-xs text-muted-foreground"
                            >
                                (Rhesus
                                {{
                                    grp.rhesus === '+' ? 'Positif' : 'Negatif'
                                }})
                            </span>
                        </div>

                        <span
                            class="rounded-md px-2 py-0.5 text-[10px] font-bold uppercase"
                            :class="{
                                'bg-emerald-500/10 text-emerald-700':
                                    grp.total_units > grp.min_threshold,
                                'bg-amber-500/15 text-amber-700':
                                    grp.total_units <= grp.min_threshold &&
                                    grp.total_units > grp.min_threshold / 2,
                                'animate-pulse bg-red-500/20 font-extrabold text-red-700':
                                    grp.total_units <= grp.min_threshold / 2,
                            }"
                        >
                            {{
                                grp.total_units > grp.min_threshold
                                    ? 'Aman'
                                    : grp.total_units > grp.min_threshold / 2
                                      ? 'Menipis'
                                      : 'Kritis'
                            }}
                        </span>
                    </div>

                    <!-- Total number big -->
                    <div
                        class="flex items-baseline justify-between border-y border-border/50 py-2.5"
                    >
                        <span class="text-xs text-muted-foreground"
                            >Total Persediaan:</span
                        >
                        <div>
                            <span class="text-3xl font-black text-foreground">{{
                                grp.total_units
                            }}</span>
                            <span class="ml-1 text-xs text-muted-foreground"
                                >kantong</span
                            >
                        </div>
                    </div>

                    <!-- Components breakdown grid -->
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div
                            class="flex items-center justify-between rounded-lg bg-muted/40 p-2"
                        >
                            <span
                                class="text-[10px] font-semibold text-muted-foreground"
                                >WB</span
                            >
                            <span class="font-bold text-foreground">{{
                                grp.components.WB ?? 0
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between rounded-lg bg-muted/40 p-2"
                        >
                            <span
                                class="text-[10px] font-semibold text-muted-foreground"
                                >PRC</span
                            >
                            <span class="font-bold text-foreground">{{
                                grp.components.PRC ?? 0
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between rounded-lg bg-muted/40 p-2"
                        >
                            <span
                                class="text-[10px] font-semibold text-muted-foreground"
                                >TC</span
                            >
                            <span class="font-bold text-foreground">{{
                                grp.components.TC ?? 0
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between rounded-lg bg-muted/40 p-2"
                        >
                            <span
                                class="text-[10px] font-semibold text-muted-foreground"
                                >FFP</span
                            >
                            <span class="font-bold text-foreground">{{
                                grp.components.FFP ?? 0
                            }}</span>
                        </div>
                    </div>

                    <!-- Quick update trigger for this card -->
                    <button
                        @click="openUpdateModal(grp.blood_type, grp.rhesus)"
                        class="flex w-full items-center justify-center gap-1.5 rounded-lg border border-border py-1.5 text-xs font-semibold text-foreground transition hover:border-red-500/50 hover:bg-red-500/5 hover:text-red-600"
                    >
                        <RefreshCw class="size-3" />
                        <span>Update Stok {{ grp.blood_type_full }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Riwayat Mutasi Stok Darah (Audit Trail) -->
        <div
            class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"
        >
            <div
                class="flex items-center justify-between border-b border-border p-4"
            >
                <div class="flex items-center gap-2">
                    <History class="size-5 text-muted-foreground" />
                    <div>
                        <h3 class="text-base font-bold text-foreground">
                            Log Mutasi & Riwayat Perubahan Stok
                        </h3>
                        <p class="text-xs text-muted-foreground">
                            Catatan otomatis setiap kali stok bertambah (donasi)
                            atau berkurang (distribusi RS / expired)
                        </p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead
                        class="border-b border-border bg-muted/40 text-[10px] tracking-wider text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-4 py-3 font-semibold">
                                Waktu Mutasi
                            </th>
                            <th class="px-4 py-3 font-semibold">
                                Gol. Darah & Komponen
                            </th>
                            <th class="px-4 py-3 font-semibold">Tipe Mutasi</th>
                            <th class="px-4 py-3 font-semibold">
                                Jumlah (Kantong)
                            </th>
                            <th class="px-4 py-3 font-semibold">
                                Kode Kantong
                            </th>
                            <th class="px-4 py-3 font-semibold">
                                Keterangan / Catatan
                            </th>
                            <th class="px-4 py-3 font-semibold">
                                Petugas Bertanggung Jawab
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        <tr
                            v-for="mut in mutations.data"
                            :key="mut.id"
                            class="transition hover:bg-muted/20"
                        >
                            <td
                                class="px-4 py-3 whitespace-nowrap text-muted-foreground"
                            >
                                {{
                                    new Date(mut.created_at).toLocaleString(
                                        'id-ID',
                                        {
                                            dateStyle: 'medium',
                                            timeStyle: 'short',
                                        },
                                    )
                                }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="font-bold text-foreground">
                                    {{ mut.blood_type }}{{ mut.rhesus }}
                                </span>
                                <span
                                    class="ml-1 font-mono text-muted-foreground"
                                    >({{ mut.component }})</span
                                >
                            </td>

                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                                    :class="{
                                        'bg-emerald-500/10 text-emerald-700':
                                            mut.type === 'in',
                                        'bg-blue-500/10 text-blue-700':
                                            mut.type === 'out',
                                        'bg-red-500/10 text-red-700':
                                            mut.type === 'expired',
                                        'bg-amber-500/10 text-amber-700':
                                            mut.type === 'adjustment',
                                    }"
                                >
                                    <TrendingUp
                                        v-if="mut.type === 'in'"
                                        class="size-3 text-emerald-600"
                                    />
                                    <TrendingDown
                                        v-else-if="mut.type === 'out'"
                                        class="size-3 text-blue-600"
                                    />
                                    <Trash2
                                        v-else-if="mut.type === 'expired'"
                                        class="size-3 text-red-600"
                                    />
                                    <SlidersHorizontal
                                        v-else
                                        class="size-3 text-amber-600"
                                    />
                                    <span>
                                        {{
                                            mut.type === 'in'
                                                ? 'Stok Masuk'
                                                : mut.type === 'out'
                                                  ? 'Distribusi RS'
                                                  : mut.type === 'expired'
                                                    ? 'Pemusnahan'
                                                    : 'Opname Fisik'
                                        }}
                                    </span>
                                </span>
                            </td>

                            <td
                                class="px-4 py-3 font-bold"
                                :class="
                                    mut.quantity > 0
                                        ? 'text-emerald-600'
                                        : 'text-red-600'
                                "
                            >
                                {{
                                    mut.quantity > 0
                                        ? `+${mut.quantity}`
                                        : mut.quantity
                                }}
                                kantong
                            </td>

                            <td
                                class="px-4 py-3 font-mono text-[11px] text-muted-foreground"
                            >
                                {{ mut.bag_code || '-' }}
                            </td>

                            <td
                                class="max-w-xs truncate px-4 py-3 text-foreground"
                                :title="mut.notes"
                            >
                                {{ mut.notes || '-' }}
                            </td>

                            <td class="px-4 py-3 text-muted-foreground">
                                {{
                                    mut.officer
                                        ? mut.officer.name
                                        : 'Sistem Otomatis'
                                }}
                            </td>
                        </tr>

                        <tr v-if="mutations.data.length === 0">
                            <td
                                colspan="7"
                                class="py-10 text-center text-xs text-muted-foreground"
                            >
                                Belum ada catatan mutasi stok darah.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal: Aksi Admin: Update Stok Darah -->
        <div
            v-if="isUpdateModalOpen"
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
                            class="flex size-9 items-center justify-center rounded-lg bg-red-500/10 text-red-600"
                        >
                            <RefreshCw class="size-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-foreground">
                                Aksi Admin: Update Stok Darah
                            </h3>
                            <p class="text-xs text-muted-foreground">
                                Penyesuaian stok masuk, keluar, atau koreksi
                                opname fisik
                            </p>
                        </div>
                    </div>
                    <button
                        @click="isUpdateModalOpen = false"
                        class="text-muted-foreground hover:text-foreground"
                    >
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitUpdate" class="space-y-4">
                    <!-- Tipe Mutasi -->
                    <div>
                        <label
                            class="mb-1.5 block text-xs font-semibold text-foreground"
                            >Tipe Perubahan Stok</label
                        >
                        <div class="grid grid-cols-2 gap-2">
                            <label
                                class="flex cursor-pointer items-center gap-2 rounded-xl border p-2.5 text-xs transition"
                                :class="
                                    updateForm.mutation_type === 'in'
                                        ? 'border-emerald-500 bg-emerald-500/10 font-bold text-emerald-700'
                                        : 'border-border'
                                "
                            >
                                <input
                                    type="radio"
                                    v-model="updateForm.mutation_type"
                                    value="in"
                                />
                                <span>Stok Masuk (Donasi)</span>
                            </label>
                            <label
                                class="flex cursor-pointer items-center gap-2 rounded-xl border p-2.5 text-xs transition"
                                :class="
                                    updateForm.mutation_type === 'out'
                                        ? 'border-blue-500 bg-blue-500/10 font-bold text-blue-700'
                                        : 'border-border'
                                "
                            >
                                <input
                                    type="radio"
                                    v-model="updateForm.mutation_type"
                                    value="out"
                                />
                                <span>Stok Keluar (Pengeluaran)</span>
                            </label>
                            <label
                                class="flex cursor-pointer items-center gap-2 rounded-xl border p-2.5 text-xs transition"
                                :class="
                                    updateForm.mutation_type === 'expired'
                                        ? 'border-red-500 bg-red-500/10 font-bold text-red-700'
                                        : 'border-border'
                                "
                            >
                                <input
                                    type="radio"
                                    v-model="updateForm.mutation_type"
                                    value="expired"
                                />
                                <span>Pemusnahan (Kadaluarsa)</span>
                            </label>
                            <label
                                class="flex cursor-pointer items-center gap-2 rounded-xl border p-2.5 text-xs transition"
                                :class="
                                    updateForm.mutation_type === 'adjustment'
                                        ? 'border-amber-500 bg-amber-500/10 font-bold text-amber-700'
                                        : 'border-border'
                                "
                            >
                                <input
                                    type="radio"
                                    v-model="updateForm.mutation_type"
                                    value="adjustment"
                                />
                                <span>Opname Fisik (Set Total)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Blood type, rhesus, component -->
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Golongan</label
                            >
                            <select
                                v-model="updateForm.blood_type"
                                class="w-full rounded-xl border border-input bg-card px-3 py-2 text-xs"
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
                                v-model="updateForm.rhesus"
                                class="w-full rounded-xl border border-input bg-card px-3 py-2 text-xs"
                            >
                                <option value="+">Positif (+)</option>
                                <option value="-">Negatif (-)</option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Komponen</label
                            >
                            <select
                                v-model="updateForm.component"
                                class="w-full rounded-xl border border-input bg-card px-3 py-2 text-xs"
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
                                >Jumlah Kantong</label
                            >
                            <input
                                v-model="updateForm.quantity"
                                type="number"
                                min="1"
                                required
                                class="w-full rounded-xl border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-foreground"
                                >Nomor Kantong / Batch (Opsional)</label
                            >
                            <input
                                v-model="updateForm.bag_code"
                                type="text"
                                placeholder="Contoh: BAG-O-202611"
                                class="w-full rounded-xl border border-input bg-card px-3 py-2 text-xs"
                            />
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-xs font-semibold text-foreground"
                            >Alasan / Catatan Penyesuaian
                            <span class="text-red-500">*</span></label
                        >
                        <textarea
                            v-model="updateForm.notes"
                            rows="2"
                            required
                            placeholder="Tuliskan catatan mutasi stok..."
                            class="w-full rounded-xl border border-input bg-background p-3 text-xs text-foreground"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="isUpdateModalOpen = false"
                            class="rounded-xl border border-input px-4 py-2 text-xs font-medium hover:bg-muted"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="updateForm.processing"
                            class="rounded-xl bg-red-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-red-700"
                        >
                            Simpan Perubahan Stok
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
