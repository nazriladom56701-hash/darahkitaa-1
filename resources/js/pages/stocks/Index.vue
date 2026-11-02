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
    SlidersHorizontal
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

    <div class="flex flex-col gap-6 p-4 md:p-6 max-w-7xl mx-auto w-full">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400">
                        <Droplets class="size-5" />
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl md:text-2xl font-extrabold text-foreground tracking-tight">Inventaris Stok Darah</h1>
                            <span class="text-xs bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-semibold px-2 py-0.5 rounded border border-emerald-500/20">
                                Aksi Admin: Update Stok
                            </span>
                        </div>
                        <p class="text-xs text-muted-foreground mt-0.5">
                            Monitoring persediaan kantong darah per golongan dan komponen (WB, PRC, TC, FFP) secara real-time.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Core Aksi Admin Trigger -->
            <button
                @click="openUpdateModal()"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow-sm transition"
            >
                <Plus class="size-4" /> Update Stok Darah
            </button>
        </div>

        <!-- Metric Badges -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="p-3.5 rounded-xl bg-card border border-border">
                <div class="text-[11px] text-muted-foreground">Total Kantong Darah</div>
                <div class="text-2xl font-black text-foreground mt-1">{{ stats.total_units }} <span class="text-xs font-normal text-muted-foreground">kantong</span></div>
                <div class="text-[10px] text-muted-foreground mt-0.5">Semua golongan & komponen</div>
            </div>

            <div class="p-3.5 rounded-xl bg-emerald-500/5 border border-emerald-500/20">
                <div class="text-[11px] text-emerald-700 dark:text-emerald-400 font-medium">Stok Aman (> Ambang Batas)</div>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ stats.safe_count }} <span class="text-xs font-normal">golongan</span></div>
                <div class="text-[10px] text-emerald-600/80 mt-0.5">Kebutuhan tercukupi</div>
            </div>

            <div class="p-3.5 rounded-xl bg-amber-500/5 border border-amber-500/20">
                <div class="text-[11px] text-amber-700 dark:text-amber-400 font-medium">Stok Menipis (Warning)</div>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ stats.warning_count }} <span class="text-xs font-normal">golongan</span></div>
                <div class="text-[10px] text-amber-600/80 mt-0.5">Perlu dorongan donor</div>
            </div>

            <div class="p-3.5 rounded-xl bg-red-500/5 border border-red-500/30">
                <div class="text-[11px] text-red-700 dark:text-red-400 font-bold flex items-center gap-1">
                    <ShieldAlert class="size-3.5 text-red-600" />
                    <span>Stok Kritis!</span>
                </div>
                <div class="text-2xl font-black text-red-600 mt-1">{{ stats.critical_count }} <span class="text-xs font-normal">golongan</span></div>
                <div class="text-[10px] text-red-600/80 mt-0.5 font-medium">Butuh panggilan darurat</div>
            </div>
        </div>

        <!-- 8 Blood Groups Cards Grid -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-base text-foreground flex items-center gap-2">
                    <Droplet class="size-4 text-red-600" />
                    <span>Ringkasan Stok Berdasarkan 8 Golongan Darah</span>
                </h3>
                <span class="text-xs text-muted-foreground">Klik "+ Update" pada kartu untuk mutasi cepat</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div
                    v-for="grp in groupedStocks"
                    :key="grp.blood_type_full"
                    class="rounded-2xl border bg-card p-4 flex flex-col justify-between hover:shadow-md transition space-y-3"
                    :class="{
                        'border-emerald-500/30 bg-emerald-500/[0.02]': grp.total_units > grp.min_threshold,
                        'border-amber-500/40 bg-amber-500/[0.04]': grp.total_units <= grp.min_threshold && grp.total_units > (grp.min_threshold / 2),
                        'border-red-500/50 bg-red-500/[0.06] ring-1 ring-red-500/30': grp.total_units <= (grp.min_threshold / 2)
                    }"
                >
                    <!-- Top: Blood group name & level badge -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-black" :class="{
                                'text-emerald-600': grp.total_units > grp.min_threshold,
                                'text-amber-600': grp.total_units <= grp.min_threshold && grp.total_units > (grp.min_threshold / 2),
                                'text-red-600': grp.total_units <= (grp.min_threshold / 2)
                            }">
                                {{ grp.blood_type_full }}
                            </span>
                            <span class="text-xs text-muted-foreground font-mono">
                                (Rhesus {{ grp.rhesus === '+' ? 'Positif' : 'Negatif' }})
                            </span>
                        </div>

                        <span
                            class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-md"
                            :class="{
                                'bg-emerald-500/10 text-emerald-700': grp.total_units > grp.min_threshold,
                                'bg-amber-500/15 text-amber-700': grp.total_units <= grp.min_threshold && grp.total_units > (grp.min_threshold / 2),
                                'bg-red-500/20 text-red-700 font-extrabold animate-pulse': grp.total_units <= (grp.min_threshold / 2)
                            }"
                        >
                            {{ grp.total_units > grp.min_threshold ? 'Aman' : grp.total_units > (grp.min_threshold / 2) ? 'Menipis' : 'Kritis' }}
                        </span>
                    </div>

                    <!-- Total number big -->
                    <div class="flex items-baseline justify-between border-y border-border/50 py-2.5">
                        <span class="text-xs text-muted-foreground">Total Persediaan:</span>
                        <div>
                            <span class="text-3xl font-black text-foreground">{{ grp.total_units }}</span>
                            <span class="text-xs text-muted-foreground ml-1">kantong</span>
                        </div>
                    </div>

                    <!-- Components breakdown grid -->
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2 rounded-lg bg-muted/40 flex justify-between items-center">
                            <span class="text-[10px] text-muted-foreground font-semibold">WB</span>
                            <span class="font-bold text-foreground">{{ grp.components.WB ?? 0 }}</span>
                        </div>
                        <div class="p-2 rounded-lg bg-muted/40 flex justify-between items-center">
                            <span class="text-[10px] text-muted-foreground font-semibold">PRC</span>
                            <span class="font-bold text-foreground">{{ grp.components.PRC ?? 0 }}</span>
                        </div>
                        <div class="p-2 rounded-lg bg-muted/40 flex justify-between items-center">
                            <span class="text-[10px] text-muted-foreground font-semibold">TC</span>
                            <span class="font-bold text-foreground">{{ grp.components.TC ?? 0 }}</span>
                        </div>
                        <div class="p-2 rounded-lg bg-muted/40 flex justify-between items-center">
                            <span class="text-[10px] text-muted-foreground font-semibold">FFP</span>
                            <span class="font-bold text-foreground">{{ grp.components.FFP ?? 0 }}</span>
                        </div>
                    </div>

                    <!-- Quick update trigger for this card -->
                    <button
                        @click="openUpdateModal(grp.blood_type, grp.rhesus)"
                        class="w-full py-1.5 rounded-lg border border-border hover:border-red-500/50 hover:bg-red-500/5 text-xs font-semibold text-foreground hover:text-red-600 transition flex items-center justify-center gap-1.5"
                    >
                        <RefreshCw class="size-3" />
                        <span>Update Stok {{ grp.blood_type_full }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Riwayat Mutasi Stok Darah (Audit Trail) -->
        <div class="rounded-2xl border border-border bg-card overflow-hidden shadow-sm">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <History class="size-5 text-muted-foreground" />
                    <div>
                        <h3 class="font-bold text-base text-foreground">Log Mutasi & Riwayat Perubahan Stok</h3>
                        <p class="text-xs text-muted-foreground">Catatan otomatis setiap kali stok bertambah (donasi) atau berkurang (distribusi RS / expired)</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-muted/40 border-b border-border text-muted-foreground uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Waktu Mutasi</th>
                            <th class="px-4 py-3 font-semibold">Gol. Darah & Komponen</th>
                            <th class="px-4 py-3 font-semibold">Tipe Mutasi</th>
                            <th class="px-4 py-3 font-semibold">Jumlah (Kantong)</th>
                            <th class="px-4 py-3 font-semibold">Kode Kantong</th>
                            <th class="px-4 py-3 font-semibold">Keterangan / Catatan</th>
                            <th class="px-4 py-3 font-semibold">Petugas Bertanggung Jawab</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        <tr v-for="mut in mutations.data" :key="mut.id" class="hover:bg-muted/20 transition">
                            <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">
                                {{ new Date(mut.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="font-bold text-foreground">
                                    {{ mut.blood_type }}{{ mut.rhesus }}
                                </span>
                                <span class="text-muted-foreground ml-1 font-mono">({{ mut.component }})</span>
                            </td>

                            <td class="px-4 py-3">
                                <span 
                                    class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full"
                                    :class="{
                                        'bg-emerald-500/10 text-emerald-700': mut.type === 'in',
                                        'bg-blue-500/10 text-blue-700': mut.type === 'out',
                                        'bg-red-500/10 text-red-700': mut.type === 'expired',
                                        'bg-amber-500/10 text-amber-700': mut.type === 'adjustment'
                                    }"
                                >
                                    <TrendingUp v-if="mut.type === 'in'" class="size-3 text-emerald-600" />
                                    <TrendingDown v-else-if="mut.type === 'out'" class="size-3 text-blue-600" />
                                    <Trash2 v-else-if="mut.type === 'expired'" class="size-3 text-red-600" />
                                    <SlidersHorizontal v-else class="size-3 text-amber-600" />
                                    <span>
                                        {{ mut.type === 'in' ? 'Stok Masuk' : mut.type === 'out' ? 'Distribusi RS' : mut.type === 'expired' ? 'Pemusnahan' : 'Opname Fisik' }}
                                    </span>
                                </span>
                            </td>

                            <td class="px-4 py-3 font-bold" :class="mut.quantity > 0 ? 'text-emerald-600' : 'text-red-600'">
                                {{ mut.quantity > 0 ? `+${mut.quantity}` : mut.quantity }} kantong
                            </td>

                            <td class="px-4 py-3 font-mono text-[11px] text-muted-foreground">
                                {{ mut.bag_code || '-' }}
                            </td>

                            <td class="px-4 py-3 text-foreground max-w-xs truncate" :title="mut.notes">
                                {{ mut.notes || '-' }}
                            </td>

                            <td class="px-4 py-3 text-muted-foreground">
                                {{ mut.officer ? mut.officer.name : 'Sistem Otomatis' }}
                            </td>
                        </tr>

                        <tr v-if="mutations.data.length === 0">
                            <td colspan="7" class="py-10 text-center text-muted-foreground text-xs">
                                Belum ada catatan mutasi stok darah.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal: Aksi Admin: Update Stok Darah -->
        <div v-if="isUpdateModalOpen" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
            <div class="bg-card border border-border rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-border">
                    <div class="flex items-center gap-2.5">
                        <div class="size-9 rounded-lg bg-red-500/10 text-red-600 flex items-center justify-center">
                            <RefreshCw class="size-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-foreground">Aksi Admin: Update Stok Darah</h3>
                            <p class="text-xs text-muted-foreground">Penyesuaian stok masuk, keluar, atau koreksi opname fisik</p>
                        </div>
                    </div>
                    <button @click="isUpdateModalOpen = false" class="text-muted-foreground hover:text-foreground">✕</button>
                </div>

                <form @submit.prevent="submitUpdate" class="space-y-4">
                    <!-- Tipe Mutasi -->
                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1.5">Tipe Perubahan Stok</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer text-xs transition" :class="updateForm.mutation_type === 'in' ? 'border-emerald-500 bg-emerald-500/10 font-bold text-emerald-700' : 'border-border'">
                                <input type="radio" v-model="updateForm.mutation_type" value="in" />
                                <span>Stok Masuk (Donasi)</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer text-xs transition" :class="updateForm.mutation_type === 'out' ? 'border-blue-500 bg-blue-500/10 font-bold text-blue-700' : 'border-border'">
                                <input type="radio" v-model="updateForm.mutation_type" value="out" />
                                <span>Stok Keluar (Pengeluaran)</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer text-xs transition" :class="updateForm.mutation_type === 'expired' ? 'border-red-500 bg-red-500/10 font-bold text-red-700' : 'border-border'">
                                <input type="radio" v-model="updateForm.mutation_type" value="expired" />
                                <span>Pemusnahan (Kadaluarsa)</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer text-xs transition" :class="updateForm.mutation_type === 'adjustment' ? 'border-amber-500 bg-amber-500/10 font-bold text-amber-700' : 'border-border'">
                                <input type="radio" v-model="updateForm.mutation_type" value="adjustment" />
                                <span>Opname Fisik (Set Total)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Blood type, rhesus, component -->
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Golongan</label>
                            <select v-model="updateForm.blood_type" class="w-full text-xs rounded-xl border border-input bg-card px-3 py-2">
                                <option value="A">Gol. A</option>
                                <option value="B">Gol. B</option>
                                <option value="AB">Gol. AB</option>
                                <option value="O">Gol. O</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Rhesus</label>
                            <select v-model="updateForm.rhesus" class="w-full text-xs rounded-xl border border-input bg-card px-3 py-2">
                                <option value="+">Positif (+)</option>
                                <option value="-">Negatif (-)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Komponen</label>
                            <select v-model="updateForm.component" class="w-full text-xs rounded-xl border border-input bg-card px-3 py-2">
                                <option value="WB">Whole Blood (WB)</option>
                                <option value="PRC">Packed Red (PRC)</option>
                                <option value="TC">Thrombocyte (TC)</option>
                                <option value="FFP">Fresh Plasma (FFP)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Jumlah Kantong</label>
                            <input v-model="updateForm.quantity" type="number" min="1" required class="w-full text-xs rounded-xl border border-input bg-card px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-foreground mb-1">Nomor Kantong / Batch (Opsional)</label>
                            <input v-model="updateForm.bag_code" type="text" placeholder="Contoh: BAG-O-202611" class="w-full text-xs rounded-xl border border-input bg-card px-3 py-2" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1">Alasan / Catatan Penyesuaian <span class="text-red-500">*</span></label>
                        <textarea
                            v-model="updateForm.notes"
                            rows="2"
                            required
                            placeholder="Tuliskan catatan mutasi stok..."
                            class="w-full text-xs rounded-xl border border-input bg-background p-3 text-foreground"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="isUpdateModalOpen = false" class="px-4 py-2 rounded-xl border border-input text-xs font-medium hover:bg-muted">Batal</button>
                        <button type="submit" :disabled="updateForm.processing" class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-sm">
                            Simpan Perubahan Stok
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>
