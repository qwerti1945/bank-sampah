<script setup>
import { ref, watch, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";

const props = defineProps({
    stats: Object,
    chart: Object,
    recentDeposits: Array,
    recentWithdrawals: Array,
    recentSales: Array,
    topInventories: Array,
    filters: Object,
    nasabahPasif: Array,
    totalDanaMengendap: Number,
    batasBulan: Number,
});

// State Tab Utama
const activeTab = ref("performa");

// State Filter Kalender
const filterYear = ref(
    props.filters?.year || new Date().getFullYear().toString(),
);
const filterMonth = ref(
    props.filters?.month ||
        (new Date().getMonth() + 1).toString().padStart(2, "0"),
);

// State Filter Akun Pasif
const filterDormantMonths = ref(props.batasBulan || 12);

// State Interaktif Real-Time Snapping Tracker
const activeDeposit = ref(null);
const activeWithdrawal = ref(null);

// Filter Reload via Inertia
const applyFilters = () => {
    router.get(
        window.location.pathname,
        {
            year: filterYear.value,
            month: filterMonth.value,
            dormant_months: filterDormantMonths.value,
        },
        { preserveState: true, replace: true },
    );
};

watch([filterYear, filterMonth, filterDormantMonths], () => {
    applyFilters();
});

const downloadExcelUrl = computed(() => {
    return `/admin/dashboard/export?year=${filterYear.value}&month=${filterMonth.value}`;
});

const listYears = computed(() => {
    const currentY = new Date().getFullYear();
    return [
        "all",
        currentY.toString(),
        (currentY - 1).toString(),
        (currentY - 2).toString(),
    ];
});

const listMonths = [
    { value: "all", label: "Semua Bulan" },
    { value: "01", label: "Januari" },
    { value: "02", label: "Februari" },
    { value: "03", label: "Maret" },
    { value: "04", label: "April" },
    { value: "05", label: "Mei" },
    { value: "06", label: "Juni" },
    { value: "07", label: "Juli" },
    { value: "08", label: "Agustus" },
    { value: "09", label: "September" },
    { value: "10", label: "Oktober" },
    { value: "11", label: "November" },
    { value: "12", label: "Desember" },
];

const listDormantOptions = [
    { value: 3, label: "⏱️ Pasif > 3 Bulan" },
    { value: 6, label: "⏱️ Pasif > 6 Bulan" },
    { value: 12, label: "⏱️ Pasif > 12 Bulan (1 Tahun)" },
];

const danaAmanDigunakan = computed(() => (props.totalDanaMengendap || 0) * 0.7);
const danaCadanganStandby = computed(
    () => (props.totalDanaMengendap || 0) * 0.3,
);

const maxDepositValue = computed(() => {
    const values = props.chart?.seriesDeposit || [];
    return values.length > 0 ? Math.max(...values, 1) : 1;
});

const maxWithdrawalValue = computed(() => {
    const values = props.chart?.seriesWithdrawal || [];
    return values.length > 0 ? Math.max(...values, 1) : 1;
});

// ==========================================================
// KALKULASI GEOMETRI KOORDINAT TREN LINE
// ==========================================================
const SVG_W = 600;
const SVG_H = 140;
const PAD_Y = 10;

const generatePoints = (series, maxValue) => {
    if (!series || series.length === 0) return [];
    const stepX = series.length > 1 ? SVG_W / (series.length - 1) : SVG_W;
    const usableHeight = SVG_H - PAD_Y * 2;

    return series.map((val, i) => {
        const x = i * stepX;
        const y = SVG_H - PAD_Y - (val / maxValue) * usableHeight;
        return { x, y, val };
    });
};

const depositPoints = computed(() =>
    generatePoints(props.chart?.seriesDeposit || [], maxDepositValue.value),
);
const withdrawalPoints = computed(() =>
    generatePoints(
        props.chart?.seriesWithdrawal || [],
        maxWithdrawalValue.value,
    ),
);

const getLinePath = (points) => {
    if (points.length === 0) return "";
    return points
        .map((p, i) => `${i === 0 ? "M" : "L"} ${p.x} ${p.y}`)
        .join(" ");
};

const getAreaPath = (points) => {
    if (points.length === 0) return "";
    const firstX = points[0].x;
    const lastX = points[points.length - 1].x;
    const bottomY = SVG_H;
    const lineStr = getLinePath(points);
    return `${lineStr} L ${lastX} ${bottomY} L ${firstX} ${bottomY} Z`;
};

// ==========================================================
// TRACKER SNAPPING (MENGIKUTI POSISI JARI/MOUSE)
// ==========================================================
const handleTrackerDeposit = (e) => {
    if (!depositPoints.value.length) return;
    const svg = e.currentTarget;
    const rect = svg.getBoundingClientRect();
    const clientX =
        e.touches && e.touches.length > 0 ? e.touches[0].clientX : e.clientX;
    const mouseX = ((clientX - rect.left) / rect.width) * SVG_W;

    let closestIdx = 0;
    let minDist = Infinity;
    depositPoints.value.forEach((p, idx) => {
        const dist = Math.abs(p.x - mouseX);
        if (dist < minDist) {
            minDist = dist;
            closestIdx = idx;
        }
    });
    activeDeposit.value = {
        ...depositPoints.value[closestIdx],
        date: props.chart?.dates[closestIdx],
    };
};

const handleTrackerWithdrawal = (e) => {
    if (!withdrawalPoints.value.length) return;
    const svg = e.currentTarget;
    const rect = svg.getBoundingClientRect();
    const clientX =
        e.touches && e.touches.length > 0 ? e.touches[0].clientX : e.clientX;
    const mouseX = ((clientX - rect.left) / rect.width) * SVG_W;

    let closestIdx = 0;
    let minDist = Infinity;
    withdrawalPoints.value.forEach((p, idx) => {
        const dist = Math.abs(p.x - mouseX);
        if (dist < minDist) {
            minDist = dist;
            closestIdx = idx;
        }
    });
    activeWithdrawal.value = {
        ...withdrawalPoints.value[closestIdx],
        date: props.chart?.dates[closestIdx],
    };
};
</script>

<template>
    <Head title="Panel Ekosistem Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-lg sm:text-xl font-black text-gray-900 leading-tight"
            >
                Dashboard Ekosistem
            </h2>
        </template>

        <div class="space-y-5 max-w-7xl mx-auto px-1 py-2 sm:py-4">
            <div
                class="bg-white p-3.5 rounded-xl border border-gray-100 shadow-2xs flex flex-col md:flex-row md:items-center md:justify-between gap-3.5"
            >
                <div>
                    <p class="text-xs text-gray-500">
                        Analisis riil sirkulasi tabungan, logistik gudang, dan
                        dana mengendap.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch gap-2">
                    <div class="grid grid-cols-2 gap-2 sm:w-64">
                        <select
                            v-model="filterMonth"
                            class="w-full text-xs sm:text-sm border-gray-200 rounded-xl p-2.5 bg-white font-medium focus:ring-green-500/20 focus:border-green-500 shadow-3xs"
                        >
                            <option
                                v-for="m in listMonths"
                                :key="m.value"
                                :value="m.value"
                            >
                                {{ m.label }}
                            </option>
                        </select>
                        <select
                            v-model="filterYear"
                            class="w-full text-xs sm:text-sm border-gray-200 rounded-xl p-2.5 bg-white font-semibold focus:ring-green-500/20 focus:border-green-500 shadow-3xs"
                        >
                            <option v-for="y in listYears" :key="y" :value="y">
                                {{ y === "all" ? "Semua Tahun" : y }}
                            </option>
                        </select>
                    </div>

                    <a
                        :href="downloadExcelUrl"
                        target="_blank"
                        class="text-center bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-3 rounded-xl transition-all shadow-xs flex items-center justify-center gap-2 cursor-pointer"
                    >
                        📊 Cetak Laporan Excel
                    </a>
                </div>
            </div>

            <div
                class="bg-white p-3.5 rounded-xl border border-gray-100 shadow-2xs"
            >
                <h3
                    class="text-xs font-black text-gray-400 uppercase tracking-wider mb-2.5"
                >
                    ⚡ Akses Pintasan Cepat
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <Link
                        href="/admin/deposits"
                        class="flex items-center gap-2.5 p-2.5 bg-gray-50 active:bg-green-50 rounded-xl border border-gray-200/50 transition-all group"
                    >
                        <span
                            class="text-lg group-hover:scale-105 transition-transform"
                            >📥</span
                        >
                        <div class="truncate">
                            <p
                                class="text-xs font-black text-gray-800 truncate"
                            >
                                Setoran Baru
                            </p>
                            <p class="text-[9px] text-gray-400 truncate">
                                Timbang sampah
                            </p>
                        </div>
                    </Link>
                    <Link
                        href="/admin/withdrawals"
                        class="flex items-center gap-2.5 p-2.5 bg-gray-50 active:bg-amber-50 rounded-xl border border-gray-200/50 transition-all group"
                    >
                        <span
                            class="text-lg group-hover:scale-105 transition-transform"
                            >📤</span
                        >
                        <div class="truncate">
                            <p
                                class="text-xs font-black text-gray-800 truncate"
                            >
                                Tarik Tabungan
                            </p>
                            <p class="text-[9px] text-gray-400 truncate">
                                Pencairan saldo
                            </p>
                        </div>
                    </Link>
                    <Link
                        href="/admin/collector-sales"
                        class="flex items-center gap-2.5 p-2.5 bg-gray-50 active:bg-blue-50 rounded-xl border border-gray-200/50 transition-all group"
                    >
                        <span
                            class="text-lg group-hover:scale-105 transition-transform"
                            >🏭</span
                        >
                        <div class="truncate">
                            <p
                                class="text-xs font-black text-gray-800 truncate"
                            >
                                Jual Pengepul
                            </p>
                            <p class="text-[9px] text-gray-400 truncate">
                                Kosongkan gudang
                            </p>
                        </div>
                    </Link>
                    <Link
                        href="/admin/wastes"
                        class="flex items-center gap-2.5 p-2.5 bg-gray-50 active:bg-purple-50 rounded-xl border border-gray-200/50 transition-all group"
                    >
                        <span
                            class="text-lg group-hover:scale-105 transition-transform"
                            >📦</span
                        >
                        <div class="truncate">
                            <p
                                class="text-xs font-black text-gray-800 truncate"
                            >
                                Katalog Harga
                            </p>
                            <p class="text-[9px] text-gray-400 truncate">
                                Tarif komoditas
                            </p>
                        </div>
                    </Link>
                </div>
            </div>

            <div
                class="border-b border-gray-100 bg-white sm:rounded-xl shadow-xs overflow-hidden"
            >
                <nav
                    class="grid grid-cols-2 sm:flex sm:space-x-8"
                    aria-label="Tabs"
                >
                    <button
                        @click="activeTab = 'performa'"
                        type="button"
                        class="py-3.5 px-1 border-b-2 font-bold text-xs sm:text-sm transition-all cursor-pointer text-center justify-center flex items-center gap-1.5"
                        :class="
                            activeTab === 'performa'
                                ? 'border-green-600 text-green-600 bg-green-50/10 sm:bg-transparent'
                                : 'border-transparent text-gray-400 hover:text-gray-600'
                        "
                    >
                        📈 Performa Gudang
                    </button>
                    <button
                        @click="activeTab = 'dormant'"
                        type="button"
                        class="py-3.5 px-1 border-b-2 font-bold text-xs sm:text-sm transition-all cursor-pointer text-center justify-center flex items-center gap-1.5"
                        :class="
                            activeTab === 'dormant'
                                ? 'border-green-600 text-green-600 bg-green-50/10 sm:bg-transparent'
                                : 'border-transparent text-gray-400 hover:text-gray-600'
                        "
                    >
                        💰 Dana Mengendap
                        <span
                            v-if="nasabahPasif?.length > 0"
                            class="text-[9px] sm:text-[10px] px-1.5 py-0.5 rounded-full font-black bg-amber-100 text-amber-800 shadow-3xs"
                        >
                            {{ nasabahPasif.length }}
                        </span>
                    </button>
                </nav>
            </div>

            <div
                v-if="activeTab === 'performa'"
                class="space-y-5 animate-in fade-in duration-150"
            >
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
                    <div
                        class="bg-white p-3.5 sm:p-5 rounded-xl border border-gray-100 shadow-2xs col-span-2 sm:col-span-1"
                    >
                        <span
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block"
                            >Kas Riil Pengelola</span
                        >
                        <p
                            class="text-base sm:text-xl font-mono font-black text-green-600 mt-0.5 truncate"
                        >
                            Rp
                            {{
                                Number(
                                    stats?.kas_pengelola || 0,
                                ).toLocaleString("id-ID")
                            }}
                        </p>
                    </div>
                    <div
                        class="bg-white p-3.5 sm:p-5 rounded-xl border border-gray-100 shadow-2xs"
                    >
                        <span
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block"
                            >Kewajiban Tabungan</span
                        >
                        <p
                            class="text-base sm:text-lg font-mono font-bold text-gray-800 mt-0.5 truncate"
                        >
                            Rp
                            {{
                                Number(stats?.total_saldo || 0).toLocaleString(
                                    "id-ID",
                                )
                            }}
                        </p>
                    </div>
                    <div
                        class="bg-white p-3.5 sm:p-5 rounded-xl border border-gray-100 shadow-2xs"
                    >
                        <span
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block"
                            >Aset Stok Gudang</span
                        >
                        <p
                            class="text-base sm:text-lg font-mono font-bold text-gray-800 mt-0.5 truncate"
                        >
                            {{
                                Number(stats?.total_stok || 0).toLocaleString(
                                    "id-ID",
                                )
                            }}
                            <span class="text-xs text-gray-400">Kg</span>
                        </p>
                    </div>
                    <div
                        class="bg-white p-3.5 sm:p-5 rounded-xl border border-gray-100 shadow-2xs"
                    >
                        <span
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block"
                            >Total Keanggotaan</span
                        >
                        <p
                            class="text-base sm:text-lg font-mono font-bold text-gray-800 mt-0.5 truncate"
                        >
                            {{ stats?.total_nasabah || 0 }}
                            <span class="text-xs text-gray-400">Jiwa</span>
                        </p>
                    </div>
                </div>

                <div
                    class="bg-white border border-gray-100 rounded-xl shadow-2xs p-4"
                >
                    <div class="mb-2">
                        <span
                            class="text-[9px] font-black text-green-600 bg-green-50 Bimbel px-2 py-0.5 rounded uppercase tracking-wider"
                            >{{ chart?.title || "Grafik Tren" }}</span
                        >
                        <h3 class="text-sm font-black text-gray-800 mt-1">
                            {{
                                chart?.subtitle || "Pergerakan Niaga Eksosistem"
                            }}
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div
                            class="bg-linear-to-b from-gray-50/20 to-white p-3.5 rounded-xl border border-gray-150/60 relative"
                        >
                            <div class="flex justify-between items-center mb-1">
                                <h4
                                    class="text-xs font-bold text-gray-400 uppercase tracking-wider"
                                >
                                    📥 Volume Masuk Sampah
                                </h4>
                                <span
                                    class="text-[10px] font-mono text-green-600 bg-green-50 px-2 py-0.5 rounded-full font-black"
                                    >Kg</span
                                >
                            </div>

                            <div class="overflow-x-auto pb-1 select-none">
                                <div class="min-w-[480px] pr-2 pt-12 relative">
                                    <svg
                                        :viewBox="`0 0 ${SVG_W} ${SVG_H}`"
                                        class="w-full h-auto overflow-visible touch-none"
                                        @mousemove="handleTrackerDeposit"
                                        @mouseleave="activeDeposit = null"
                                        @touchstart="handleTrackerDeposit"
                                        @touchmove="handleTrackerDeposit"
                                        @touchend="activeDeposit = null"
                                    >
                                        <defs>
                                            <linearGradient
                                                id="gradDeposit"
                                                x1="0"
                                                y1="0"
                                                x2="0"
                                                y2="1"
                                            >
                                                <stop
                                                    offset="0%"
                                                    stop-color="#10b981"
                                                    stop-opacity="0.18"
                                                />
                                                <stop
                                                    offset="100%"
                                                    stop-color="#10b981"
                                                    stop-opacity="0.00"
                                                />
                                            </linearGradient>
                                        </defs>

                                        <line
                                            x1="0"
                                            y1="10"
                                            :x2="SVG_W"
                                            y2="10"
                                            stroke="#f1f5f9"
                                            stroke-width="1"
                                            stroke-dasharray="4 4"
                                        />
                                        <line
                                            x1="0"
                                            y1="70"
                                            :x2="SVG_W"
                                            y2="70"
                                            stroke="#f1f5f9"
                                            stroke-width="1"
                                            stroke-dasharray="4 4"
                                        />
                                        <line
                                            x1="0"
                                            y1="130"
                                            :x2="SVG_W"
                                            y2="130"
                                            stroke="#f1f5f9"
                                            stroke-width="1"
                                            stroke-dasharray="4 4"
                                        />

                                        <path
                                            :d="getAreaPath(depositPoints)"
                                            fill="url(#gradDeposit)"
                                        />

                                        <line
                                            v-if="activeDeposit"
                                            :x1="activeDeposit.x"
                                            :y1="0"
                                            :x2="activeDeposit.x"
                                            :y2="SVG_H"
                                            stroke="#10b981"
                                            stroke-width="1.2"
                                            stroke-dasharray="3 3"
                                        />
                                        <line
                                            v-if="activeDeposit"
                                            :x1="0"
                                            :y1="activeDeposit.y"
                                            :x2="SVG_W"
                                            y2="activeDeposit.y"
                                            stroke="#10b981"
                                            stroke-width="0.8"
                                            stroke-dasharray="3 3"
                                        />

                                        <path
                                            :d="getLinePath(depositPoints)"
                                            fill="none"
                                            stroke="#10b981"
                                            stroke-width="2.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />

                                        <circle
                                            v-if="activeDeposit"
                                            :cx="activeDeposit.x"
                                            :cy="activeDeposit.y"
                                            r="8"
                                            fill="#10b981"
                                            fill-opacity="0.2"
                                        />
                                        <circle
                                            v-if="activeDeposit"
                                            :cx="activeDeposit.x"
                                            :cy="activeDeposit.y"
                                            r="4"
                                            fill="white"
                                            stroke="#10b981"
                                            stroke-width="2.5"
                                        />
                                    </svg>

                                    <div
                                        v-if="activeDeposit"
                                        class="absolute bg-slate-900/95 backdrop-blur-xs text-white px-3 py-2 rounded-xl shadow-xl border border-slate-800 text-xs pointer-events-none transition-all duration-75 min-w-[120px] z-50"
                                        :style="{
                                            left: `${(activeDeposit.x / SVG_W) * 100}%`,
                                            top: `${activeDeposit.y + 48}px`,
                                            transform: 'translate(-50%, -130%)',
                                        }"
                                    >
                                        <p
                                            class="text-[9px] text-gray-400 font-medium tracking-wide"
                                        >
                                            Periode: {{ activeDeposit.date }}
                                        </p>
                                        <p
                                            class="font-mono font-black text-emerald-400 mt-0.5 text-sm flex items-center gap-1"
                                        >
                                            <span class="text-[10px]">🟢</span>
                                            {{ activeDeposit.val }} Kg
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="flex justify-between text-[9px] font-mono font-black text-gray-400 mt-2.5 min-w-[480px] border-t border-gray-100 pt-2 px-0.5"
                                >
                                    <span
                                        v-for="(lbl, idx) in chart?.dates || []"
                                        :key="idx"
                                        class="w-6 text-center shrink-0"
                                        >{{ lbl }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-linear-to-b from-gray-50/20 to-white p-3.5 rounded-xl border border-gray-150/60 relative"
                        >
                            <div class="flex justify-between items-center mb-1">
                                <h4
                                    class="text-xs font-bold text-gray-400 uppercase tracking-wider"
                                >
                                    📤 Volume Tarik Tabungan
                                </h4>
                                <span
                                    class="text-[10px] font-mono text-red-600 bg-red-50 px-2 py-0.5 rounded-full font-black"
                                    >Rupiah</span
                                >
                            </div>

                            <div class="overflow-x-auto pb-1 select-none">
                                <div class="min-w-[480px] pr-2 pt-12 relative">
                                    <svg
                                        :viewBox="`0 0 ${SVG_W} ${SVG_H}`"
                                        class="w-full h-auto overflow-visible touch-none"
                                        @mousemove="handleTrackerWithdrawal"
                                        @mouseleave="activeWithdrawal = null"
                                        @touchstart="handleTrackerWithdrawal"
                                        @touchmove="handleTrackerWithdrawal"
                                        @touchend="activeWithdrawal = null"
                                    >
                                        <defs>
                                            <linearGradient
                                                id="gradWithdrawal"
                                                x1="0"
                                                y1="0"
                                                x2="0"
                                                y2="1"
                                            >
                                                <stop
                                                    offset="0%"
                                                    stop-color="#ef4444"
                                                    stop-opacity="0.15"
                                                />
                                                <stop
                                                    offset="100%"
                                                    stop-color="#ef4444"
                                                    stop-opacity="0.00"
                                                />
                                            </linearGradient>
                                        </defs>

                                        <line
                                            x1="0"
                                            y1="10"
                                            :x2="SVG_W"
                                            y2="10"
                                            stroke="#f1f5f9"
                                            stroke-width="1"
                                            stroke-dasharray="4 4"
                                        />
                                        <line
                                            x1="0"
                                            y1="70"
                                            :x2="SVG_W"
                                            y2="70"
                                            stroke="#f1f5f9"
                                            stroke-width="1"
                                            stroke-dasharray="4 4"
                                        />
                                        <line
                                            x1="0"
                                            y1="130"
                                            :x2="SVG_W"
                                            y2="130"
                                            stroke="#f1f5f9"
                                            stroke-width="1"
                                            stroke-dasharray="4 4"
                                        />

                                        <path
                                            :d="getAreaPath(withdrawalPoints)"
                                            fill="url(#gradWithdrawal)"
                                        />

                                        <line
                                            v-if="activeWithdrawal"
                                            :x1="activeWithdrawal.x"
                                            :y1="0"
                                            :x2="activeWithdrawal.x"
                                            :y2="SVG_H"
                                            stroke="#ef4444"
                                            stroke-width="1.2"
                                            stroke-dasharray="3 3"
                                        />
                                        <line
                                            v-if="activeWithdrawal"
                                            :x1="0"
                                            :y1="activeWithdrawal.y"
                                            :x2="SVG_W"
                                            y2="activeWithdrawal.y"
                                            stroke="#ef4444"
                                            stroke-width="0.8"
                                            stroke-dasharray="3 3"
                                        />

                                        <path
                                            :d="getLinePath(withdrawalPoints)"
                                            fill="none"
                                            stroke="#ef4444"
                                            stroke-width="2.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />

                                        <circle
                                            v-if="activeWithdrawal"
                                            :cx="activeWithdrawal.x"
                                            :cy="activeWithdrawal.y"
                                            r="8"
                                            fill="#ef4444"
                                            fill-opacity="0.2"
                                        />
                                        <circle
                                            v-if="activeWithdrawal"
                                            :cx="activeWithdrawal.x"
                                            :cy="activeWithdrawal.y"
                                            r="4"
                                            fill="white"
                                            stroke="#ef4444"
                                            stroke-width="2.5"
                                        />
                                    </svg>

                                    <div
                                        v-if="activeWithdrawal"
                                        class="absolute bg-slate-900/95 backdrop-blur-xs text-white p-2.5 rounded-xl shadow-xl border border-slate-800 text-xs pointer-events-none transition-all duration-75 min-w-[140px] z-50"
                                        :style="{
                                            left: `${(activeWithdrawal.x / SVG_W) * 100}%`,
                                            top: `${activeWithdrawal.y + 48}px`,
                                            transform: 'translate(-50%, -130%)',
                                        }"
                                    >
                                        <p
                                            class="text-[9px] text-gray-400 font-medium tracking-wide"
                                        >
                                            Periode: {{ activeWithdrawal.date }}
                                        </p>
                                        <p
                                            class="font-mono font-black text-rose-400 mt-0.5 text-xs flex items-center gap-1"
                                        >
                                            <span class="text-[10px]">🔴</span>
                                            Rp
                                            {{
                                                activeWithdrawal.val.toLocaleString(
                                                    "id-ID",
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="flex justify-between text-[9px] font-mono font-black text-gray-400 mt-2.5 min-w-[480px] border-t border-gray-100 pt-2 px-0.5"
                                >
                                    <span
                                        v-for="(lbl, idx) in chart?.dates || []"
                                        :key="idx"
                                        class="w-6 text-center shrink-0"
                                        >{{ lbl }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-r from-gray-900 to-gray-800 text-white p-4 rounded-xl shadow-xs"
                >
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3"
                    >
                        <div>
                            <h4
                                class="text-xs uppercase font-bold tracking-widest text-gray-300"
                            >
                                Laba Bersih Toko Periode Ini
                            </h4>
                            <p class="text-[10px] text-gray-400 mt-0.5">
                                Selisih nominal penjualan pengepul dengan
                                pengeluaran beli sampah nasabah.
                            </p>
                        </div>
                        <div
                            class="flex justify-between sm:justify-end gap-6 w-full sm:w-auto border-t border-gray-700 sm:border-0 pt-2.5 sm:pt-0"
                        >
                            <div>
                                <span
                                    class="text-[9px] font-bold text-gray-400 uppercase block"
                                    >Omset Toko</span
                                >
                                <span
                                    class="text-sm sm:text-base font-mono font-bold text-blue-400"
                                    >Rp
                                    {{
                                        Number(
                                            props.stats?.total_pendapatan || 0,
                                        ).toLocaleString("id-ID")
                                    }}</span
                                >
                            </div>
                            <div>
                                <span
                                    class="text-[9px] font-bold text-gray-400 uppercase block"
                                    >Keuntungan Bersih</span
                                >
                                <span
                                    class="text-sm sm:text-base font-mono font-bold"
                                    :class="
                                        (props.stats?.untung_bersih || 0) >= 0
                                            ? 'text-green-400'
                                            : 'text-red-400'
                                    "
                                >
                                    Rp
                                    {{
                                        Number(
                                            props.stats?.untung_bersih || 0,
                                        ).toLocaleString("id-ID")
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div
                        class="bg-white border border-gray-100 rounded-xl p-4 shadow-3xs"
                    >
                        <h4
                            class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-2.5 flex items-center gap-1"
                        >
                            📥 Log Setoran Terkini
                        </h4>
                        <div class="space-y-1.5">
                            <div
                                v-for="dep in recentDeposits || []"
                                :key="dep.id"
                                class="p-2.5 bg-gray-50/70 rounded-lg flex justify-between items-center text-xs"
                            >
                                <div class="truncate mr-2">
                                    <span
                                        class="font-bold text-gray-800 block truncate max-w-[10rem]"
                                        >{{ dep.nasabah_name }}</span
                                    >
                                    <span
                                        class="text-[9px] text-gray-400 font-mono block mt-0.5"
                                        >{{ dep.date }}</span
                                    >
                                </div>
                                <span
                                    class="font-mono font-bold text-green-600 shrink-0"
                                    >+Rp
                                    {{
                                        dep.amount.toLocaleString("id-ID")
                                    }}</span
                                >
                            </div>
                            <p
                                v-if="
                                    !recentDeposits ||
                                    recentDeposits.length === 0
                                "
                                class="text-center text-xs text-gray-400 py-3"
                            >
                                Tidak ada aktivitas.
                            </p>
                        </div>
                    </div>

                    <div
                        class="bg-white border border-gray-100 rounded-xl p-4 shadow-3xs"
                    >
                        <h4
                            class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-2.5 flex items-center gap-1"
                        >
                            📤 Log Tarik Tunai Terkini
                        </h4>
                        <div class="space-y-1.5">
                            <div
                                v-for="wit in recentWithdrawals || []"
                                :key="wit.id"
                                class="p-2.5 bg-gray-50/70 rounded-lg flex justify-between items-center text-xs"
                            >
                                <div class="truncate mr-2">
                                    <span
                                        class="font-bold text-gray-800 block truncate max-w-[10rem]"
                                        >{{ wit.nasabah_name }}</span
                                    >
                                    <span
                                        class="text-[9px] text-gray-400 font-mono block mt-0.5"
                                        >{{ wit.date }}</span
                                    >
                                </div>
                                <span
                                    class="font-mono font-bold text-red-600 shrink-0"
                                    >-Rp
                                    {{
                                        wit.amount.toLocaleString("id-ID")
                                    }}</span
                                >
                            </div>
                            <p
                                v-if="
                                    !recentWithdrawals ||
                                    recentWithdrawals.length === 0
                                "
                                class="text-center text-xs text-gray-400 py-3"
                            >
                                Tidak ada aktivitas.
                            </p>
                        </div>
                    </div>

                    <div
                        class="bg-white border border-gray-100 rounded-xl p-4 shadow-3xs"
                    >
                        <h4
                            class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-2.5 flex items-center gap-1"
                        >
                            🏭 Log Jual Pengepul Terkini
                        </h4>
                        <div class="space-y-1.5">
                            <div
                                v-for="sal in recentSales || []"
                                :key="sal.id"
                                class="p-2.5 bg-gray-50/70 rounded-lg flex justify-between items-center text-xs"
                            >
                                <div class="truncate">
                                    <span
                                        class="font-bold text-gray-800 block truncate"
                                        >Pelepasan Stok Gudang</span
                                    >
                                    <span
                                        class="text-[9px] text-gray-400 font-mono block mt-0.5"
                                        >{{ sal.date }}</span
                                    >
                                </div>
                                <span
                                    class="font-mono font-bold text-blue-600 shrink-0"
                                    >+Rp
                                    {{
                                        sal.amount.toLocaleString("id-ID")
                                    }}</span
                                >
                            </div>
                            <p
                                v-if="!recentSales || recentSales.length === 0"
                                class="text-center text-xs text-gray-400 py-3"
                            >
                                Tidak ada aktivitas.
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white border border-gray-100 rounded-xl p-4 shadow-3xs"
                >
                    <h3
                        class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3"
                    >
                        📦 Distribusi Komoditas Gudang Populer
                    </h3>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3"
                    >
                        <div
                            v-for="inv in topInventories || []"
                            :key="inv.name"
                            class="p-3.5 bg-gray-50/80 rounded-xl border border-gray-100 flex flex-col justify-between"
                        >
                            <div>
                                <span
                                    class="text-[9px] font-bold text-gray-400 uppercase bg-white px-2 py-0.5 rounded border border-gray-100 inline-block mb-1.5"
                                    >{{ inv.category }}</span
                                >
                                <h4
                                    class="text-xs font-black text-gray-800 truncate"
                                >
                                    {{ inv.name }}
                                </h4>
                            </div>
                            <div
                                class="mt-3 pt-2 border-t border-gray-200/60 flex justify-between items-end text-[11px]"
                            >
                                <div>
                                    <span class="text-[9px] text-gray-400 block"
                                        >Stok Sisa</span
                                    >
                                    <span
                                        class="font-mono font-bold text-gray-700"
                                        >{{ inv.stock }} {{ inv.unit }}</span
                                    >
                                </div>
                                <div class="text-right">
                                    <span class="text-[9px] text-gray-400 block"
                                        >Total Masuk</span
                                    >
                                    <span
                                        class="font-mono font-semibold text-gray-500"
                                        >{{ inv.total_in }} {{ inv.unit }}</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div
                            v-if="
                                !topInventories || topInventories.length === 0
                            "
                            class="col-span-full p-6 text-center text-xs text-gray-400"
                        >
                            Tidak ada rekam data.
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="activeTab === 'dormant'"
                class="space-y-5 animate-in fade-in duration-150"
            >
                <div
                    class="bg-amber-50 border border-amber-200 p-3.5 rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-3xs"
                >
                    <div>
                        <h4
                            class="text-xs sm:text-sm font-black text-amber-900"
                        >
                            Filter Analisis Jangka Jeda Pasif
                        </h4>
                        <p class="text-[10px] text-amber-700 mt-0.5">
                            Ubah durasi kosongnya rekam transaksi nasabah untuk
                            kalkulasi rasio kas.
                        </p>
                    </div>
                    <div class="w-full sm:w-64">
                        <select
                            v-model="filterDormantMonths"
                            class="w-full text-xs border-amber-300 text-amber-900 rounded-xl p-2.5 bg-white font-bold focus:ring-amber-500/20 focus:border-amber-500 shadow-2xs"
                        >
                            <option
                                v-for="opt in listDormantOptions"
                                :key="opt.value"
                                :value="opt.value"
                            >
                                {{ opt.label }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div
                        class="bg-amber-50/40 border border-amber-200/60 rounded-xl p-4"
                    >
                        <p
                            class="text-[10px] font-bold text-amber-700 uppercase tracking-wider"
                        >
                            Total Dana Pasif (&gt;{{ batasBulan }} Bln)
                        </p>
                        <p
                            class="text-xl sm:text-2xl font-mono font-black text-amber-900 mt-0.5 truncate"
                        >
                            Rp
                            {{
                                (totalDanaMengendap || 0).toLocaleString(
                                    "id-ID",
                                )
                            }}
                        </p>
                        <p
                            class="text-[10px] text-amber-600 mt-1.5 leading-relaxed"
                        >
                            Akumulasi tabungan dari nasabah yang nihil aktivitas
                            transaksi selama
                            <span class="font-black text-amber-800"
                                >{{ batasBulan }} bulan terakhir</span
                            >.
                        </p>
                    </div>
                    <div
                        class="bg-green-50/40 border border-green-200/60 rounded-xl p-4"
                    >
                        <p
                            class="text-[10px] font-bold text-green-700 uppercase tracking-wider"
                        >
                            Aman Diputar (Rasio 70%)
                        </p>
                        <p
                            class="text-xl sm:text-2xl font-mono font-black text-green-900 mt-0.5 truncate"
                        >
                            Rp {{ danaAmanDigunakan.toLocaleString("id-ID") }}
                        </p>
                        <p
                            class="text-[10px] text-green-600 mt-1.5 leading-relaxed"
                        >
                            Batas maksimum dana mengendap yang direkomendasikan
                            aman jika ingin diputar sementara untuk stimulus
                            operasional/internal.
                        </p>
                    </div>
                    <div
                        class="bg-gray-50 border border-gray-200/60 rounded-xl p-4"
                    >
                        <p
                            class="text-[10px] font-bold text-gray-500 uppercase tracking-wider"
                        >
                            Plafon Likuiditas (Rasio 30%)
                        </p>
                        <p
                            class="text-xl sm:text-2xl font-mono font-black text-gray-700 mt-0.5 truncate"
                        >
                            Rp {{ danaCadanganStandby.toLocaleString("id-ID") }}
                        </p>
                        <p
                            class="text-[10px] text-gray-400 mt-1.5 leading-relaxed"
                        >
                            Dana wajib standby di kas sebagai jaminan likuiditas
                            jika sewaktu-waktu nasabah pasif datang menarik
                            simpanannya.
                        </p>
                    </div>
                </div>

                <div
                    class="bg-white overflow-hidden sm:rounded-xl border border-gray-100 shadow-3xs"
                >
                    <div
                        class="p-3.5 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center"
                    >
                        <h3
                            class="text-xs font-bold text-gray-800 uppercase tracking-wide"
                        >
                            Daftar Identitas Rekening Dormant (&gt;{{
                                batasBulan
                            }}
                            Bulan)
                        </h3>
                        <span
                            class="text-[10px] px-2 py-0.5 bg-amber-100 text-amber-800 font-bold rounded"
                        >
                            Jumlah: {{ nasabahPasif?.length || 0 }} Akun
                        </span>
                    </div>

                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider"
                                >
                                    <th
                                        class="p-4 font-semibold w-14 text-center"
                                    >
                                        No
                                    </th>
                                    <th class="p-4 font-semibold">
                                        Nama Lengkap Akun
                                    </th>
                                    <th class="p-4 font-semibold">ID Nomor</th>
                                    <th class="p-4 font-semibold">
                                        Aktivitas Terakhir
                                    </th>
                                    <th class="p-4 font-semibold text-right">
                                        Saldo Idle
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="text-sm divide-y divide-gray-50 text-gray-700"
                            >
                                <tr
                                    v-for="(nsb, idx) in nasabahPasif || []"
                                    :key="nsb.id"
                                    class="hover:bg-gray-50/40 transition-colors"
                                >
                                    <td
                                        class="p-4 text-gray-400 text-center font-mono"
                                    >
                                        {{ idx + 1 }}
                                    </td>
                                    <td class="p-4 font-bold text-gray-900">
                                        {{ nsb.name }}
                                    </td>
                                    <td
                                        class="p-4 text-xs font-mono text-gray-500"
                                    >
                                        #NSB-{{ nsb.id }}
                                    </td>
                                    <td class="p-4 text-xs text-gray-500">
                                        {{
                                            new Date(
                                                nsb.updated_at,
                                            ).toLocaleDateString("id-ID", {
                                                day: "2-digit",
                                                month: "short",
                                                year: "numeric",
                                            })
                                        }}
                                    </td>
                                    <td
                                        class="p-4 text-right font-extrabold text-amber-700 font-mono"
                                    >
                                        Rp
                                        {{
                                            Number(nsb.balance).toLocaleString(
                                                "id-ID",
                                            )
                                        }}
                                    </td>
                                </tr>
                                <tr
                                    v-if="
                                        !nasabahPasif ||
                                        nasabahPasif.length === 0
                                    "
                                >
                                    <td
                                        colspan="5"
                                        class="p-10 text-center text-gray-400"
                                    >
                                        Tidak ada pemegang rekening yang pasif
                                        dalam jangka {{ batasBulan }} bulan
                                        terakhir.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="block sm:hidden divide-y divide-gray-50">
                        <div
                            v-for="(nsb, idx) in nasabahPasif || []"
                            :key="nsb.id"
                            class="p-3.5 space-y-1.5 bg-white active:bg-gray-50"
                        >
                            <div class="flex justify-between items-start gap-2">
                                <div class="truncate">
                                    <span
                                        class="text-[9px] font-mono text-gray-400 block"
                                        >NO. {{ idx + 1 }} • ID: #NSB-{{
                                            nsb.id
                                        }}</span
                                    >
                                    <h4
                                        class="text-xs font-black text-gray-800 mt-0.5 truncate"
                                    >
                                        {{ nsb.name }}
                                    </h4>
                                </div>
                                <span
                                    class="text-xs font-mono font-black text-amber-700 shrink-0"
                                >
                                    Rp
                                    {{
                                        Number(nsb.balance).toLocaleString(
                                            "id-ID",
                                        )
                                    }}
                                </span>
                            </div>
                            <div
                                class="flex justify-between items-center text-[10px] text-gray-500"
                            >
                                <span>Aktivitas Akhir:</span>
                                <span class="font-medium font-mono">
                                    {{
                                        new Date(
                                            nsb.updated_at,
                                        ).toLocaleDateString("id-ID", {
                                            day: "2-digit",
                                            month: "short",
                                            year: "numeric",
                                        })
                                    }}
                                </span>
                            </div>
                        </div>
                        <div
                            v-if="!nasabahPasif || nasabahPasif.length === 0"
                            class="p-6 text-center text-xs text-gray-400"
                        >
                            Tidak ada pemegang rekening yang pasif dalam jangka
                            {{ batasBulan }} bulan terakhir.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.overflow-x-auto::-webkit-scrollbar {
    height: 4px;
}
.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 99px;
}
</style>
