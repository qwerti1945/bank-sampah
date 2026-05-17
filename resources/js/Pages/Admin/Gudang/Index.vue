<script setup>
import { ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

const props = defineProps({
    stocks: Object,
    filters: Object,
});

const search = ref(props.filters?.search || "");
let searchTimer = null;

watch(search, (newValue) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(
            route("admin.gudang.index"),
            { search: newValue },
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
                        Informasi jumlah persediaan sampah yang saat ini
                        tersimpan di dalam gudang penyimpanan utama.
                    </p>
                </div>

                <!-- Bar Pencarian -->
                <div class="mb-5 max-w-md">
                    <div class="relative rounded-lg shadow-2xs">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <svg
                                class="h-4 w-4 text-gray-400"
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
                            placeholder="Cari berdasarkan nama atau kategori sampah..."
                            class="block w-full rounded-lg border-gray-200 pl-9 text-xs sm:text-sm focus:border-indigo-500 focus:ring-indigo-500/10 p-2.5 transition-all"
                        />
                    </div>
                </div>

                <!-- Tabel Informasi Persediaan -->
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
                                        Stok Tersedia Saat Ini
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
                                    </td>

                                    <td
                                        class="p-4 text-center font-mono text-gray-600"
                                    >
                                        {{ Number(item.total_in || 0) }}
                                        {{ item.unit }}
                                    </td>
                                    <td
                                        class="p-4 text-center font-mono text-gray-600"
                                    >
                                        {{ Number(item.total_out || 0) }}
                                        {{ item.unit }}
                                    </td>

                                    <!-- BADGE INDIKATOR VOLUME STOK TERSISA -->
                                    <td class="p-4 text-center">
                                        <span
                                            :class="[
                                                'inline-block font-mono font-bold px-3 py-1 rounded-lg text-xs w-full text-center border',
                                                item.current_stock > 20
                                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                                                    : item.current_stock > 0
                                                      ? 'bg-amber-50 text-amber-700 border-amber-100'
                                                      : 'bg-rose-50 text-rose-600 border-rose-100',
                                            ]"
                                        >
                                            {{ item.current_stock }}
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
                                        colspan="6"
                                        class="p-12 text-center text-gray-400"
                                    >
                                        Data barang komoditas tidak ditemukan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Navigasi Halaman -->
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
                                    ? 'bg-indigo-600 text-white border-indigo-600 font-semibold'
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
