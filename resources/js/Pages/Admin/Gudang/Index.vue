<script setup>
import { ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

const props = defineProps({
    stocks: Object,
    filters: Object,
});

// State untuk filter
const search = ref(props.filters?.search || "");
const month = ref(props.filters?.month || "");
const year = ref(props.filters?.year || "");
let searchTimer = null;

// Daftar opsi bulan dan tahun
const months = [
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

const currentYear = new Date().getFullYear();
// Generate opsi tahun: mulai dari 2 tahun lalu s.d. tahun sekarang
const years = Array.from({ length: 3 }, (_, i) => currentYear - i);

// Watcher untuk re-fetch data ketika salah satu filter berubah
watch([search, month, year], ([newSearch, newMonth, newYear]) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(
            route("admin.gudang.index"),
            { search: newSearch, month: newMonth, year: newYear },
            { preserveState: true, replace: true },
        );
    }, 400);
});
</script>

<template>
    <Head title="Stok Gudang" />

    <AuthenticatedLayout>
        <template #header> Manajemen Inventaris Gudang </template>

        <div
            class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100"
        >
            <div class="p-4 sm:p-6">
                <div class="mb-6">
                    <h3 class="text-base sm:text-lg font-bold text-gray-800">
                        Arsip Stok Komoditas
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Pantau sirkulasi masuk (dari nasabah) dan keluar (ke
                        pengepul) per komoditas sampah berdasarkan filter waktu.
                    </p>
                </div>

                <div
                    class="mb-6 flex flex-col sm:flex-row items-stretch sm:items-center gap-3"
                >
                    <div class="relative rounded-lg shadow-2xs flex-1 max-w-md">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <svg
                                class="h-4 w-4 text-gray-400 flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </div>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari nama atau kategori..."
                            class="block w-full rounded-lg border-gray-200 py-2.5 pr-2.5 pl-10 text-xs sm:text-sm focus:border-indigo-500 focus:ring-indigo-500/10 transition-all font-medium text-gray-700"
                        />
                    </div>

                    <div
                        class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2"
                    >
                        <select
                            v-model="month"
                            class="w-full sm:w-auto text-xs sm:text-sm border-gray-200 rounded-lg py-2.5 pl-3 pr-9 bg-white focus:outline-hidden focus:ring-indigo-500/10 focus:border-indigo-500 shadow-2xs text-gray-600 font-medium cursor-pointer"
                        >
                            <option value="">Semua Bulan</option>
                            <option
                                v-for="m in months"
                                :key="m.value"
                                :value="m.value"
                            >
                                {{ m.label }}
                            </option>
                        </select>

                        <select
                            v-model="year"
                            class="w-full sm:w-auto text-xs sm:text-sm border-gray-200 rounded-lg py-2.5 pl-3 pr-9 bg-white focus:outline-hidden focus:ring-indigo-500/10 focus:border-indigo-500 shadow-2xs text-gray-600 font-medium cursor-pointer"
                        >
                            <option value="">Semua Tahun</option>
                            <option v-for="y in years" :key="y" :value="y">
                                {{ y }}
                            </option>
                        </select>
                    </div>
                </div>

                <div
                    v-if="month || year"
                    class="mb-4 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-lg px-4 py-3 flex items-start sm:items-center gap-2.5 text-xs font-medium"
                >
                    <svg
                        class="w-4 h-4 text-indigo-500 flex-shrink-0 mt-0.5 sm:mt-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        ></path>
                    </svg>
                    <div
                        class="flex-1 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2"
                    >
                        <span>
                            Menampilkan rekapitulasi data spesifik untuk
                            <span class="font-bold">
                                {{
                                    month
                                        ? months.find((m) => m.value === month)
                                              ?.label
                                        : ""
                                }}
                                {{ year ? year : "" }}
                            </span>
                        </span>
                    </div>
                    <button
                        @click="
                            month = '';
                            year = '';
                        "
                        class="ml-auto underline hover:text-indigo-900 cursor-pointer flex-shrink-0 mt-0.5 sm:mt-0"
                    >
                        Reset Filter
                    </button>
                </div>

                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <div
                        class="inline-block min-w-full align-middle px-4 sm:px-0"
                    >
                        <table class="min-w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider whitespace-nowrap"
                                >
                                    <th
                                        class="p-4 font-semibold w-16 text-center"
                                    >
                                        No
                                    </th>
                                    <th class="p-4 font-semibold">
                                        Nama Komoditas
                                    </th>
                                    <th class="p-4 font-semibold text-center">
                                        Total Masuk (Nasabah)
                                    </th>
                                    <th class="p-4 font-semibold text-center">
                                        Total Keluar (Pengepul)
                                    </th>
                                    <th
                                        class="p-4 font-semibold text-center w-40"
                                    >
                                        {{
                                            month || year
                                                ? "Perubahan Stok Periode Ini"
                                                : "Stok Tersedia Saat Ini"
                                        }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="text-sm divide-y divide-gray-50 text-gray-700"
                            >
                                <tr
                                    v-for="(item, index) in stocks.data"
                                    :key="item.id"
                                    class="hover:bg-gray-50/40 transition-colors whitespace-nowrap"
                                >
                                    <td class="p-4 text-gray-500 text-center">
                                        {{ stocks.from + index }}
                                    </td>
                                    <td
                                        class="p-4 font-bold text-gray-900 whitespace-normal"
                                    >
                                        {{ item.name }}
                                        <div
                                            class="text-[10px] text-gray-400 font-normal mt-0.5"
                                        >
                                            Kategori: {{ item.category }}
                                        </div>
                                    </td>

                                    <td
                                        class="p-4 text-center font-mono text-gray-600"
                                    >
                                        {{
                                            Number(
                                                item.total_in || 0,
                                            ).toLocaleString("id-ID")
                                        }}
                                        {{ item.unit }}
                                    </td>
                                    <td
                                        class="p-4 text-center font-mono text-gray-600"
                                    >
                                        {{
                                            Number(
                                                item.total_out || 0,
                                            ).toLocaleString("id-ID")
                                        }}
                                        {{ item.unit }}
                                    </td>

                                    <td class="p-4 text-center">
                                        <span
                                            :class="[
                                                'inline-block font-mono font-bold px-3 py-1.5 rounded-lg text-xs w-full text-center border shadow-xs',
                                                item.current_stock > 20
                                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                                                    : item.current_stock > 0
                                                      ? 'bg-amber-50 text-amber-700 border-amber-100'
                                                      : 'bg-rose-50 text-rose-600 border-rose-100',
                                            ]"
                                        >
                                            {{
                                                Number(
                                                    item.current_stock,
                                                ).toLocaleString("id-ID")
                                            }}
                                            {{ item.unit }}
                                            {{
                                                item.current_stock === 0
                                                    ? "(Kosong)"
                                                    : ""
                                            }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="stocks.data.length === 0">
                                    <td
                                        colspan="5"
                                        class="p-12 text-center text-gray-400"
                                    >
                                        Data barang komoditas tidak ditemukan
                                        pada filter tersebut.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div
                    v-if="stocks.links.length > 3"
                    class="mt-6 flex flex-wrap justify-center gap-1 border-t border-gray-50 pt-4"
                >
                    <template v-for="(link, k) in stocks.links" :key="k">
                        <div
                            v-if="link.url === null"
                            class="mr-1 mb-1 px-3 py-1.5 text-xs text-gray-400 border border-gray-100 rounded-lg select-none"
                            v-html="link.label"
                        ></div>
                        <Link
                            v-else
                            class="mr-1 mb-1 px-3 py-1.5 text-xs border rounded-lg transition-all"
                            :class="
                                link.active
                                    ? 'bg-indigo-600 text-white border-indigo-600 font-semibold shadow-xs'
                                    : 'text-gray-600 bg-white border-gray-200 hover:bg-gray-50'
                            "
                            :href="link.url"
                            v-html="link.label"
                        ></Link>
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
::-webkit-scrollbar {
    width: 5px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 99px;
}
</style>
