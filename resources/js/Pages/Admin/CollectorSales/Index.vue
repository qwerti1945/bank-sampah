<script setup>
import { ref, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";

const props = defineProps({
    sales: Object,
    wasteList: Array,
});

const showCreateModal = ref(false);
const showConfirmModal = ref(false);
const selectedSale = ref(null);

const form = useForm({
    sold_at: new Date().toISOString().substr(0, 10),
    photo_proof: null,
    items: [{ waste_id: "", qty: "", price_per_unit: "" }],
});

const addRow = () => {
    form.items.push({ waste_id: "", qty: "", price_per_unit: "" });
};
const removeRow = (index) => {
    if (form.items.length > 1) form.items.splice(index, 1);
};

const getWasteUnit = (wasteId) => {
    const waste = props.wasteList.find((w) => w.id === wasteId);
    return waste ? waste.unit : "Kg";
};

const formTotalInvoice = computed(() => {
    return form.items.reduce((sum, item) => {
        const qty = Number(item.qty) || 0;
        const price = Number(item.price_per_unit) || 0;
        return sum + qty * price;
    }, 0);
});

const closeSaleModal = () => {
    form.reset();
    form.items = [{ waste_id: "", qty: "", price_per_unit: "" }];
    showCreateModal.value = false;
};

const submitSale = () => {
    form.post(route("admin.collector-sales.store"), {
        onSuccess: () => closeSaleModal(),
    });
};

const triggerDeleteConfirm = (item) => {
    selectedSale.value = item;
    showConfirmModal.value = true;
};

const executeDelete = () => {
    router.delete(
        route("admin.collector-sales.destroy", selectedSale.value.id),
        {
            preserveScroll: true,
            onSuccess: () => (showConfirmModal.value = false),
        },
    );
};

const isToastVisible = ref(false);
const toastMessage = ref("");
router.on("success", (event) => {
    const msg = event.detail.page.props.flash?.success;
    if (msg) {
        toastMessage.value = msg;
        isToastVisible.value = true;
        setTimeout(() => (isToastVisible.value = false), 4000);
    }
});
</script>

<template>
    <Head title="Jual Ke Pengepul" />

    <AuthenticatedLayout>
        <template #header> Penjualan Pengepul Besar </template>

        <div
            class="fixed top-6 right-6 z-[99] pointer-events-none max-w-sm w-full px-4 sm:px-0"
        >
            <Transition
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-2 opacity-0 sm:translate-x-4"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="isToastVisible"
                    class="bg-white border border-gray-100 p-4 rounded-xl shadow-xl pointer-events-auto flex items-start gap-3"
                >
                    <div
                        class="bg-blue-50 text-blue-500 p-1.5 rounded-lg flex-shrink-0"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            ></path>
                        </svg>
                    </div>
                    <div class="flex-1 pt-0.5">
                        <p class="text-xs font-bold text-gray-800">
                            Pembukuan Sukses
                        </p>
                        <p
                            class="text-[11px] text-gray-500 mt-0.5 leading-relaxed"
                        >
                            {{ toastMessage }}
                        </p>
                    </div>
                </div>
            </Transition>
        </div>

        <div
            class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100"
        >
            <div class="p-4 sm:p-6">
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6"
                >
                    <div>
                        <h3
                            class="text-base sm:text-lg font-bold text-gray-800"
                        >
                            Log Niaga Kas Pengepul
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Catat hasil konversi tonase komoditas sampah gudang
                            langsung ke pihak bandar pengepul besar.
                        </p>
                    </div>
                    <button
                        @click="showCreateModal = true"
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2 cursor-pointer shadow-xs"
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
                                d="M12 4v16m8-8H4"
                            ></path>
                        </svg>
                        Rekam Penjualan Baru
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
                                        Tanggal Jual
                                    </th>
                                    <th
                                        class="p-4 font-semibold text-center w-24"
                                    >
                                        Nota Fisik
                                    </th>
                                    <th class="p-4 font-semibold">
                                        Rincian Barang yang Dilepas
                                    </th>
                                    <th class="p-4 font-semibold text-right">
                                        Kas Masuk Gudang
                                    </th>
                                    <th
                                        class="p-4 font-semibold text-center w-24"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="text-sm divide-y divide-gray-50 text-gray-700"
                            >
                                <tr
                                    v-for="(item, index) in sales.data"
                                    :key="item.id"
                                    class="hover:bg-gray-50/40 transition-colors whitespace-nowrap"
                                >
                                    <td class="p-4 text-gray-500 text-center">
                                        {{ sales.from + index }}
                                    </td>
                                    <td
                                        class="p-4 text-xs font-mono font-bold text-gray-700"
                                    >
                                        {{
                                            new Date(
                                                item.sold_at,
                                            ).toLocaleDateString("id-ID", {
                                                day: "2-digit",
                                                month: "short",
                                                year: "numeric",
                                            })
                                        }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <div v-if="item.photo_proof">
                                            <a
                                                :href="
                                                    '/storage/' +
                                                    item.photo_proof
                                                "
                                                target="_blank"
                                            >
                                                <img
                                                    :src="
                                                        '/storage/' +
                                                        item.photo_proof
                                                    "
                                                    class="w-10 h-10 object-cover rounded-md mx-auto border border-gray-200 hover:scale-105 transition-transform"
                                                />
                                            </a>
                                        </div>
                                        <span
                                            v-else
                                            class="text-xs text-gray-400 font-mono"
                                            >-</span
                                        >
                                    </td>
                                    <td class="p-4 whitespace-normal max-w-md">
                                        <div class="flex flex-col gap-1">
                                            <div
                                                v-for="det in item.details"
                                                :key="det.id"
                                                class="text-xs text-gray-600"
                                            >
                                                🔹
                                                <span
                                                    class="font-semibold text-gray-800"
                                                    >{{ det.waste?.name }}</span
                                                >:
                                                <span
                                                    class="font-mono text-gray-500"
                                                    >{{ det.qty }}
                                                    {{ det.waste?.unit }}</span
                                                >
                                                x
                                                <span
                                                    class="font-mono text-gray-500"
                                                    >Rp
                                                    {{
                                                        Number(
                                                            det.price_per_unit,
                                                        ).toLocaleString(
                                                            "id-ID",
                                                        )
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="p-4 text-right font-extrabold text-blue-600 font-mono"
                                    >
                                        Rp
                                        {{
                                            Number(
                                                item.total_amount,
                                            ).toLocaleString("id-ID")
                                        }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <button
                                            @click="triggerDeleteConfirm(item)"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all cursor-pointer"
                                        >
                                            <svg
                                                class="w-4 h-4"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-16v1a3 3 0 003 3h10M4 7h16"
                                                />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="sales.data.length === 0">
                                    <td
                                        colspan="6"
                                        class="p-12 text-center text-gray-400"
                                    >
                                        Belum ada log data niaga penjualan
                                        komoditas gudang.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div
                    v-if="sales.links.length > 3"
                    class="mt-6 flex flex-wrap justify-center gap-1 border-t border-gray-50 pt-4"
                >
                    <template v-for="(link, k) in sales.links" :key="k">
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
                                    ? 'bg-blue-600 text-white border-blue-600 font-semibold'
                                    : 'text-gray-600 bg-white border-gray-200 hover:bg-gray-50'
                            "
                            :href="link.url"
                            v-html="link.label"
                        ></Link>
                    </template>
                </div>
            </div>
        </div>

        <div
            v-if="showCreateModal"
            class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-0 sm:p-4"
        >
            <div
                class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs"
                @click="closeSaleModal"
            ></div>

            <div
                class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl border border-gray-100 max-w-2xl w-full relative z-10 max-h-[92vh] sm:max-h-none flex flex-col overflow-hidden animate-in slide-in-from-bottom-10 sm:zoom-in-95 duration-200"
            >
                <div
                    class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center flex-shrink-0"
                >
                    <h3
                        class="text-sm font-bold text-gray-800 uppercase tracking-wide"
                    >
                        Pencatatan Penjualan Komersial
                    </h3>
                    <button
                        @click="closeSaleModal"
                        class="sm:hidden text-gray-400 hover:text-gray-600 text-lg p-1"
                    >
                        ✕
                    </button>
                </div>

                <form
                    @submit.prevent="submitSale"
                    class="p-5 sm:p-6 space-y-5 overflow-y-auto flex-1"
                >
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1"
                                >Tanggal Transaksi Jual</label
                            >
                            <input
                                v-model="form.sold_at"
                                type="date"
                                class="w-full text-base sm:text-sm border-gray-200 rounded-lg p-3 sm:p-2.5 focus:ring-blue-500/20 focus:border-blue-500 bg-white"
                                :class="{
                                    'border-red-300': form.errors.sold_at,
                                }"
                            />
                            <p
                                v-if="form.errors.sold_at"
                                class="text-xs text-red-500 mt-1"
                            >
                                {{ form.errors.sold_at }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1"
                                >Upload Berkas Nota Pengepul (Opsional)</label
                            >
                            <input
                                type="file"
                                @input="
                                    form.photo_proof = $event.target.files[0]
                                "
                                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 bg-white focus:outline-hidden focus:ring-blue-500/20 focus:border-blue-500"
                                accept="image/*"
                            />
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label
                                class="block text-xs font-bold text-gray-400 uppercase tracking-wider"
                                >Arsip Daftar Komoditas Gudang yang
                                Dijual</label
                            >
                            <button
                                type="button"
                                @click="addRow"
                                class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 cursor-pointer bg-blue-50 sm:bg-transparent px-2.5 py-1.5 sm:p-0 rounded-md"
                            >
                                + Tambah Baris
                            </button>
                        </div>

                        <div
                            class="space-y-3 max-h-60 sm:max-h-52 overflow-y-auto pr-0.5"
                        >
                            <div
                                v-for="(row, idx) in form.items"
                                :key="idx"
                                class="flex flex-col sm:flex-row gap-3 sm:gap-2 bg-gray-50 p-3 sm:p-2 rounded-xl border border-gray-100 shadow-xs relative"
                            >
                                <div class="flex-1 w-full">
                                    <select
                                        v-model="row.waste_id"
                                        class="w-full text-base sm:text-xs border-gray-200 rounded-lg sm:rounded-md p-2.5 sm:p-2 bg-white focus:outline-hidden"
                                    >
                                        <option value="" disabled>
                                            -- Pilih Barang Komoditas --
                                        </option>
                                        <option
                                            v-for="wst in wasteList"
                                            :key="wst.id"
                                            :value="wst.id"
                                        >
                                            {{ wst.name }} (Beli Awal: Rp
                                            {{
                                                Number(
                                                    wst.current_price,
                                                ).toLocaleString("id-ID")
                                            }})
                                        </option>
                                    </select>
                                </div>

                                <div
                                    class="flex flex-row items-center gap-2 w-full sm:w-auto"
                                >
                                    <div class="relative flex-1 sm:w-24">
                                        <input
                                            v-model="row.qty"
                                            type="text"
                                            inputmode="decimal"
                                            pattern="[0-9]*[.,]?[0-9]*"
                                            placeholder="Berat"
                                            class="w-full text-base sm:text-xs border-gray-200 rounded-lg sm:rounded-md py-2.5 sm:py-2 pl-3 sm:pl-2.5 pr-12 sm:pr-11 bg-white focus:outline-hidden font-medium text-gray-800"
                                        />
                                        <div
                                            class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                        >
                                            <span
                                                class="text-xs sm:text-[10px] text-gray-400 font-bold"
                                                >{{
                                                    getWasteUnit(row.waste_id)
                                                }}</span
                                            >
                                        </div>
                                    </div>

                                    <div class="relative flex-1 sm:w-34">
                                        <input
                                            v-model="row.price_per_unit"
                                            type="text"
                                            inputmode="numeric"
                                            pattern="[0-9]*"
                                            placeholder="Harga Bandar"
                                            class="w-full text-base sm:text-xs border-gray-200 rounded-lg sm:rounded-md py-2.5 sm:py-2 pr-3 sm:pr-2.5 pl-9 sm:pl-8 bg-white focus:outline-hidden font-semibold text-gray-800"
                                        />
                                        <div
                                            class="absolute inset-y-0 left-3 flex items-center pointer-events-none"
                                        >
                                            <span
                                                class="text-xs sm:text-[10px] text-gray-400 font-black"
                                                >Rp</span
                                            >
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        @click="removeRow(idx)"
                                        :disabled="form.items.length === 1"
                                        class="text-gray-400 hover:text-red-500 disabled:opacity-20 cursor-pointer p-2.5 sm:p-1 bg-white sm:bg-transparent border border-gray-200 sm:border-0 rounded-lg flex-shrink-0"
                                    >
                                        <svg
                                            class="w-5 h-5 sm:w-4 sm:h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-16v1a3 3 0 003 3h10M4 7h16"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-blue-600 text-white rounded-xl p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-1 shadow-sm"
                    >
                        <div>
                            <p
                                class="text-[10px] uppercase font-bold tracking-wider opacity-80"
                            >
                                Total Pendapatan Kas Masuk
                            </p>
                            <p class="text-[11px] opacity-90 mt-0.5">
                                Kalkulasi kumulatif
                                {{ form.items.length }} jenis komoditas
                            </p>
                        </div>
                        <div
                            class="text-left sm:text-right w-full sm:w-auto border-t border-white/10 sm:border-0 pt-2 sm:pt-0 mt-1 sm:mt-0"
                        >
                            <p class="text-xl sm:text-2xl font-mono font-black">
                                Rp
                                {{
                                    Number(formTotalInvoice).toLocaleString(
                                        "id-ID",
                                    )
                                }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 border-t border-gray-50 pt-4 flex-shrink-0"
                    >
                        <button
                            type="button"
                            @click="closeSaleModal"
                            class="px-4 py-3 sm:py-2 text-sm sm:text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg cursor-pointer text-center"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-3 sm:py-2 text-sm sm:text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg disabled:opacity-50 cursor-pointer text-center shadow-xs"
                        >
                            Simpan Pembukuan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div
            v-if="showConfirmModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
            <div
                class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs"
                @click="showConfirmModal = false"
            ></div>
            <div
                class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden max-w-sm w-full relative z-10 p-6 text-center animate-in fade-in zoom-in-95 duration-200"
            >
                <div
                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-600 mb-4"
                >
                    <svg
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"
                        />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1">
                    Batalkan Arsip Log Niaga?
                </h3>
                <p class="text-xs text-gray-500 leading-relaxed mb-6 px-2">
                    Aksi ini akan menghapus rekaman pembukuan kas masuk dari
                    penjualan **#CSALE-{{ selectedSale?.id }}** secara permanen.
                </p>
                <div class="flex items-center justify-end gap-2">
                    <button
                        type="button"
                        @click="showConfirmModal = false"
                        class="px-4 py-2 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg cursor-pointer"
                    >
                        Kembali
                    </button>
                    <button
                        type="button"
                        @click="executeDelete"
                        class="px-4 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg cursor-pointer shadow-xs"
                    >
                        Ya, Hapus Record
                    </button>
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
