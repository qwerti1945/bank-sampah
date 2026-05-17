<script setup>
import { ref, watch, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import VueApexCharts from "vue3-apexcharts";

const props = defineProps({
    stats: Object,
    chart: Object,
    recentDeposits: Array,
    recentWithdrawals: Array,
    recentSales: Array,
    topInventories: Array,
    filters: Object,
});

// ==========================================
// STATE TAB NAVIGASI
// ==========================================
const activeTab = ref("grafik"); // Pilihan: 'grafik', 'transaksi'

// ==========================================
// FILTER LOGIC & EXPORT
// ==========================================
const filterMonth = ref(props.filters?.month || "all");
const filterYear = ref(props.filters?.year || "all");
const currentYear = new Date().getFullYear();
const years = Array.from({ length: 5 }, (_, i) => currentYear - i);

// Saat dropdown diubah, tembak ulang rute inertia
watch([filterMonth, filterYear], ([newMonth, newYear]) => {
    router.get(
        route(route().current()),
        { month: newMonth, year: newYear },
        { preserveState: true, replace: true },
    );
});

// Link Ekspor yang dinamis mengikuti nilai filter yang sedang aktif
const exportUrl = computed(() => {
    return route("admin.dashboard.export", {
        month: filterMonth.value,
        year: filterYear.value,
    });
});

// ==========================================
// KESEHATAN KAS
// ==========================================
const isFinanciallyUnhealthy = computed(() => {
    return props.stats.kas_pengelola < props.stats.total_saldo;
});

// ==========================================
// CHART 1: SETORAN MASUK
// ==========================================
const chartDepositOptions = computed(() => ({
    chart: { type: "bar", toolbar: { show: false }, fontFamily: "inherit" },
    colors: ["#10b981"],
    plotOptions: { bar: { borderRadius: 4, columnWidth: "45%" } },
    dataLabels: { enabled: false },
    xaxis: {
        categories: props.chart?.dates || [],
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: { style: { colors: "#9ca3af", fontSize: "11px" } },
    },
    yaxis: { labels: { style: { colors: "#9ca3af" } } },
    grid: { borderColor: "#f3f4f6", strokeDashArray: 4 },
    tooltip: { y: { formatter: (val) => val + " Kg" } },
}));

const chartDepositSeries = computed(() => [
    {
        name: "Volume Masuk",
        data: props.chart?.seriesDeposit || [],
    },
]);

// ==========================================
// CHART 2: PENARIKAN DANA
// ==========================================
const chartWithdrawalOptions = computed(() => ({
    chart: { type: "area", toolbar: { show: false }, fontFamily: "inherit" },
    colors: ["#f43f5e"],
    dataLabels: { enabled: false },
    stroke: { curve: "smooth", width: 2 },
    fill: {
        type: "gradient",
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0.05,
            stops: [0, 90, 100],
        },
    },
    xaxis: {
        categories: props.chart?.dates || [],
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: { style: { colors: "#9ca3af", fontSize: "11px" } },
    },
    yaxis: {
        labels: {
            style: { colors: "#9ca3af", fontSize: "10px" },
            formatter: (val) => {
                if (val >= 1000000) return (val / 1000000).toFixed(1) + "Jt";
                if (val >= 1000) return (val / 1000).toFixed(0) + "Rb";
                return val;
            },
        },
    },
    grid: { borderColor: "#f3f4f6", strokeDashArray: 4 },
    tooltip: {
        y: { formatter: (val) => "Rp " + Number(val).toLocaleString("id-ID") },
    },
}));

const chartWithdrawalSeries = computed(() => [
    {
        name: "Penarikan Dana",
        data: props.chart?.seriesWithdrawal || [],
    },
]);
</script>

<template>
    <Head title="Dashboard Admin" />

    <AuthenticatedLayout>
        <template #header> Pusat Kendali Operasional </template>

        <div class="space-y-6">
            <!-- TOMBOL AKSI CEPAT (STATIS DI ATAS) -->
            <div class="flex flex-wrap gap-3">
                <Link
                    :href="route('admin.deposits.index')"
                    class="flex-1 min-w-[150px] bg-white border border-gray-100 hover:border-emerald-300 hover:shadow-md p-3 rounded-xl flex items-center justify-center gap-2 text-sm font-bold text-gray-700 transition-all group cursor-pointer"
                >
                    <span
                        class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform"
                        >↓</span
                    >
                    Setor Sampah
                </Link>
                <Link
                    :href="route('admin.withdrawals.index')"
                    class="flex-1 min-w-[150px] bg-white border border-gray-100 hover:border-rose-300 hover:shadow-md p-3 rounded-xl flex items-center justify-center gap-2 text-sm font-bold text-gray-700 transition-all group cursor-pointer"
                >
                    <span
                        class="w-7 h-7 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center group-hover:scale-110 transition-transform"
                        >↑</span
                    >
                    Tarik Tunai
                </Link>
                <Link
                    :href="route('admin.collector-sales.index')"
                    class="flex-1 min-w-[150px] bg-white border border-gray-100 hover:border-blue-300 hover:shadow-md p-3 rounded-xl flex items-center justify-center gap-2 text-sm font-bold text-gray-700 transition-all group cursor-pointer"
                >
                    <span
                        class="w-7 h-7 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform"
                        >🚚</span
                    >
                    Jual Pengepul
                </Link>
                <Link
                    :href="route('admin.nasabah.index')"
                    class="flex-1 min-w-[150px] bg-white border border-gray-100 hover:border-indigo-300 hover:shadow-md p-3 rounded-xl flex items-center justify-center gap-2 text-sm font-bold text-gray-700 transition-all group cursor-pointer"
                >
                    <span
                        class="w-7 h-7 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform"
                        >+</span
                    >
                    Nasabah Baru
                </Link>
            </div>

            <!-- PERINGATAN KESEHATAN KAS (STATIS DI ATAS) -->
            <div
                v-if="isFinanciallyUnhealthy"
                class="bg-rose-50 border border-rose-200 rounded-xl p-4 flex items-start gap-4 shadow-xs animate-pulse"
            >
                <div class="bg-white rounded-full p-2 text-rose-500 shadow-xs">
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        ></path>
                    </svg>
                </div>
                <div class="pt-0.5">
                    <h4
                        class="text-sm font-bold text-rose-800 uppercase tracking-wide"
                    >
                        Peringatan Likuiditas Kas!
                    </h4>
                    <p class="text-xs text-rose-600 mt-1 leading-relaxed">
                        Estimasi Uang Kas Pengelola (<span
                            class="font-bold font-mono"
                            >Rp
                            {{
                                Number(stats.kas_pengelola).toLocaleString(
                                    "id-ID",
                                )
                            }}</span
                        >) lebih kecil dari total Hutang Saldo Nasabah (<span
                            class="font-bold font-mono"
                            >Rp
                            {{
                                Number(stats.total_saldo).toLocaleString(
                                    "id-ID",
                                )
                            }}</span
                        >). Jika terjadi penarikan massal, dana tidak akan
                        cukup. Segera jual stok gudang!
                    </p>
                </div>
            </div>

            <!-- HEADER FILTER & METRIK (STATIS DI ATAS) -->
            <div class="bg-gray-50/50 p-5 rounded-2xl border border-gray-100">
                <div
                    class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6"
                >
                    <div>
                        <h2 class="text-base font-bold text-gray-800">
                            Ringkasan Statistik Global
                        </h2>
                    </div>

                    <!-- KELOMPOK FILTER & UNDUH -->
                    <div
                        class="flex flex-wrap items-center gap-2 w-full md:w-auto"
                    >
                        <!-- TOMBOL UNDUH EXCEL -->
                        <a
                            :href="exportUrl"
                            target="_blank"
                            class="flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-sm font-bold shadow-xs transition-colors cursor-pointer w-full sm:w-auto"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                ></path>
                            </svg>
                            <span class="hidden sm:inline">Unduh Excel</span>
                            <span class="sm:hidden">Ekspor Data</span>
                        </a>

                        <!-- FILTER BULAN -->
                        <select
                            v-model="filterMonth"
                            class="block flex-1 sm:w-auto text-sm border-gray-200 rounded-lg focus:ring-emerald-500/20 focus:border-emerald-500 bg-white py-2.5 shadow-xs cursor-pointer"
                        >
                            <option value="all">Semua Bulan</option>
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>

                        <!-- FILTER TAHUN -->
                        <select
                            v-model="filterYear"
                            class="block flex-1 sm:w-auto text-sm border-gray-200 rounded-lg focus:ring-emerald-500/20 focus:border-emerald-500 bg-white py-2.5 shadow-xs cursor-pointer"
                        >
                            <option value="all">Semua Tahun</option>
                            <option v-for="y in years" :key="y" :value="y">
                                {{ y }}
                            </option>
                        </select>
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4"
                >
                    <!-- Metrik Cards -->
                    <div
                        class="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
                    >
                        <div class="flex items-center gap-3 mb-2">
                            <div
                                class="w-7 h-7 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                    ></path>
                                </svg>
                            </div>
                            <p
                                class="text-[10px] uppercase tracking-wider font-bold text-gray-400"
                            >
                                Total Nasabah
                            </p>
                        </div>
                        <h4 class="text-xl font-black text-gray-800">
                            {{ stats.total_nasabah || 0 }}
                        </h4>
                    </div>

                    <div
                        class="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
                    >
                        <div class="flex items-center gap-3 mb-2">
                            <div
                                class="w-7 h-7 rounded-md bg-rose-50 text-rose-600 flex items-center justify-center"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                                    ></path>
                                </svg>
                            </div>
                            <p
                                class="text-[10px] uppercase tracking-wider font-bold text-gray-400"
                            >
                                Hutang Saldo
                            </p>
                        </div>
                        <h4
                            class="text-base font-black text-gray-800 font-mono"
                        >
                            Rp
                            {{
                                Number(stats.total_saldo || 0).toLocaleString(
                                    "id-ID",
                                )
                            }}
                        </h4>
                    </div>

                    <div
                        class="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
                    >
                        <div class="flex items-center gap-3 mb-2">
                            <div
                                class="w-7 h-7 rounded-md bg-amber-50 text-amber-600 flex items-center justify-center"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                    ></path>
                                </svg>
                            </div>
                            <p
                                class="text-[10px] uppercase tracking-wider font-bold text-gray-400"
                            >
                                Volume Gudang
                            </p>
                        </div>
                        <h4
                            class="text-base font-black text-gray-800 font-mono"
                        >
                            {{
                                Number(stats.total_stok || 0).toLocaleString(
                                    "id-ID",
                                )
                            }}
                            <span class="text-[10px] text-gray-400 font-sans"
                                >Kg</span
                            >
                        </h4>
                    </div>

                    <div
                        class="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
                    >
                        <div class="flex items-center gap-3 mb-2">
                            <div
                                class="w-7 h-7 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                            </div>
                            <p
                                class="text-[10px] uppercase tracking-wider font-bold text-gray-400"
                            >
                                Omzet Pengepul
                            </p>
                        </div>
                        <h4
                            class="text-base font-black text-emerald-600 font-mono"
                        >
                            Rp
                            {{
                                Number(
                                    stats.total_pendapatan || 0,
                                ).toLocaleString("id-ID")
                            }}
                        </h4>
                    </div>

                    <div
                        class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-xl p-4 shadow-sm text-white relative overflow-hidden"
                    >
                        <div class="absolute -right-4 -top-4 opacity-10">
                            <svg
                                class="w-20 h-20"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="relative z-10">
                            <p
                                class="text-[10px] uppercase tracking-widest font-bold opacity-80 text-blue-100 mb-2"
                            >
                                Untung Bersih
                            </p>
                            <h4 class="text-lg font-black font-mono">
                                Rp
                                {{
                                    Number(
                                        stats.untung_bersih || 0,
                                    ).toLocaleString("id-ID")
                                }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB NAVIGASI -->
            <!-- ========================================== -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex gap-6">
                    <button
                        @click="activeTab = 'grafik'"
                        :class="[
                            'whitespace-nowrap pb-4 px-1 border-b-2 font-bold text-sm transition-colors cursor-pointer',
                            activeTab === 'grafik'
                                ? 'border-emerald-500 text-emerald-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                        ]"
                    >
                        📈 Visualisasi Grafik Tren
                    </button>
                    <button
                        @click="activeTab = 'transaksi'"
                        :class="[
                            'whitespace-nowrap pb-4 px-1 border-b-2 font-bold text-sm transition-colors cursor-pointer',
                            activeTab === 'transaksi'
                                ? 'border-emerald-500 text-emerald-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                        ]"
                    >
                        📋 Log Aktivitas Terakhir
                    </button>
                </nav>
            </div>

            <!-- KONTEN TAB -->
            <div class="pt-2">
                <!-- TAB 1: GRAFIK TREN -->
                <div
                    v-show="activeTab === 'grafik'"
                    class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-in fade-in duration-300"
                >
                    <div
                        class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-2xl flex flex-col"
                    >
                        <div
                            class="p-5 border-b border-gray-50 flex items-center justify-between"
                        >
                            <div>
                                <h3 class="text-sm font-bold text-gray-800">
                                    Sampah Masuk: {{ chart.title }}
                                </h3>
                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    {{ chart.subtitle }}
                                </p>
                            </div>
                            <div
                                class="w-2 h-2 rounded-full bg-emerald-500"
                            ></div>
                        </div>
                        <div class="p-4 flex-1">
                            <VueApexCharts
                                type="bar"
                                height="300"
                                :options="chartDepositOptions"
                                :series="chartDepositSeries"
                            />
                        </div>
                    </div>

                    <div
                        class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-2xl flex flex-col"
                    >
                        <div
                            class="p-5 border-b border-gray-50 flex items-center justify-between"
                        >
                            <div>
                                <h3 class="text-sm font-bold text-gray-800">
                                    Penarikan Tunai: {{ chart.title }}
                                </h3>
                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    {{ chart.subtitle }}
                                </p>
                            </div>
                            <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                        </div>
                        <div class="p-4 flex-1">
                            <VueApexCharts
                                type="area"
                                height="300"
                                :options="chartWithdrawalOptions"
                                :series="chartWithdrawalSeries"
                            />
                        </div>
                    </div>
                </div>

                <!-- TAB 2: LOG TRANSAKSI -->
                <div
                    v-show="activeTab === 'transaksi'"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-in fade-in duration-300"
                >
                    <!-- 1. Gudang Aktif -->
                    <div
                        class="bg-white rounded-2xl border border-gray-100 shadow-xs flex flex-col"
                    >
                        <div
                            class="p-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50"
                        >
                            <h3 class="text-xs font-bold text-gray-800">
                                Gudang Aktif
                            </h3>
                            <Link
                                :href="route('admin.gudang.index')"
                                class="text-[10px] text-emerald-600 font-bold hover:underline"
                                >Semua</Link
                            >
                        </div>
                        <div class="p-0 flex-1">
                            <div
                                v-if="topInventories.length === 0"
                                class="text-center py-8"
                            >
                                <p class="text-[11px] text-gray-400">
                                    Belum ada aktivitas.
                                </p>
                            </div>
                            <ul v-else class="divide-y divide-gray-50">
                                <li
                                    v-for="(item, idx) in topInventories"
                                    :key="idx"
                                    class="p-4 flex items-center justify-between hover:bg-gray-50/50"
                                >
                                    <div>
                                        <p
                                            class="text-xs font-bold text-gray-800 truncate max-w-[100px]"
                                        >
                                            {{ item.name }}
                                        </p>
                                        <p class="text-[10px] text-gray-400">
                                            {{ item.category }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p
                                            class="text-[10px] text-emerald-600 font-bold"
                                        >
                                            +{{
                                                Number(
                                                    item.total_in,
                                                ).toLocaleString("id-ID")
                                            }}{{ item.unit }}
                                        </p>
                                        <span
                                            class="text-xs font-black font-mono text-gray-700"
                                            >{{ item.stock }}</span
                                        >
                                        <span
                                            class="text-[9px] text-gray-400 ml-0.5"
                                            >{{ item.unit }}</span
                                        >
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- 2. Log Setor -->
                    <div
                        class="bg-white rounded-2xl border border-gray-100 shadow-xs flex flex-col"
                    >
                        <div
                            class="p-4 border-b border-gray-50 flex items-center gap-2 bg-gray-50/50"
                        >
                            <div
                                class="w-2 h-2 rounded-full bg-emerald-500"
                            ></div>
                            <h3 class="text-xs font-bold text-gray-800">
                                Setoran Masuk
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-50">
                            <div
                                v-for="dep in recentDeposits"
                                :key="dep.id"
                                class="p-4 hover:bg-gray-50/50 flex justify-between items-center"
                            >
                                <div>
                                    <p
                                        class="text-[11px] font-bold text-gray-800 truncate max-w-[90px]"
                                    >
                                        {{ dep.nasabah_name }}
                                    </p>
                                    <p class="text-[9px] text-gray-400">
                                        {{ dep.date }}
                                    </p>
                                </div>
                                <span
                                    class="text-[11px] font-black text-emerald-600 font-mono"
                                    >+Rp{{
                                        Number(dep.amount).toLocaleString(
                                            "id-ID",
                                        )
                                    }}</span
                                >
                            </div>
                            <div
                                v-if="!recentDeposits.length"
                                class="p-8 text-center text-[11px] text-gray-400"
                            >
                                Belum ada data.
                            </div>
                        </div>
                    </div>

                    <!-- 3. Log Tarik -->
                    <div
                        class="bg-white rounded-2xl border border-gray-100 shadow-xs flex flex-col"
                    >
                        <div
                            class="p-4 border-b border-gray-50 flex items-center gap-2 bg-gray-50/50"
                        >
                            <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                            <h3 class="text-xs font-bold text-gray-800">
                                Penarikan Tunai
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-50">
                            <div
                                v-for="wit in recentWithdrawals"
                                :key="wit.id"
                                class="p-4 hover:bg-gray-50/50 flex justify-between items-center"
                            >
                                <div>
                                    <p
                                        class="text-[11px] font-bold text-gray-800 truncate max-w-[90px]"
                                    >
                                        {{ wit.nasabah_name }}
                                    </p>
                                    <p class="text-[9px] text-gray-400">
                                        {{ wit.date }}
                                    </p>
                                </div>
                                <span
                                    class="text-[11px] font-black text-rose-600 font-mono"
                                    >-Rp{{
                                        Number(wit.amount).toLocaleString(
                                            "id-ID",
                                        )
                                    }}</span
                                >
                            </div>
                            <div
                                v-if="!recentWithdrawals.length"
                                class="p-8 text-center text-[11px] text-gray-400"
                            >
                                Belum ada data.
                            </div>
                        </div>
                    </div>

                    <!-- 4. Log Jual -->
                    <div
                        class="bg-white rounded-2xl border border-gray-100 shadow-xs flex flex-col"
                    >
                        <div
                            class="p-4 border-b border-gray-50 flex items-center gap-2 bg-gray-50/50"
                        >
                            <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                            <h3 class="text-xs font-bold text-gray-800">
                                Jual Pengepul
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-50">
                            <div
                                v-for="sale in recentSales"
                                :key="sale.id"
                                class="p-4 hover:bg-gray-50/50 flex justify-between items-center"
                            >
                                <div>
                                    <p
                                        class="text-[11px] font-bold text-gray-800 truncate max-w-[90px]"
                                    >
                                        {{ sale.pengepul_name }}
                                    </p>
                                    <p class="text-[9px] text-gray-400">
                                        {{ sale.date }}
                                    </p>
                                </div>
                                <span
                                    class="text-[11px] font-black text-blue-600 font-mono"
                                    >Rp{{
                                        Number(sale.amount).toLocaleString(
                                            "id-ID",
                                        )
                                    }}</span
                                >
                            </div>
                            <div
                                v-if="!recentSales.length"
                                class="p-8 text-center text-[11px] text-gray-400"
                            >
                                Belum ada data.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
