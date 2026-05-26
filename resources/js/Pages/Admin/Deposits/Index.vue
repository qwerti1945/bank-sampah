<script setup>
import { ref, watch, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";

const props = defineProps({
    deposits: Object,
    nasabahList: Array,
    wasteList: Array,
    filters: Object,
});

const showCreateModal = ref(false);
const showConfirmModal = ref(false);
const selectedDeposit = ref(null);

// Inisialisasi State Form dengan Dukungan File Objek
const form = useForm({
    nasabah_id: "",
    photo_proof: null,
    items: [{ waste_id: "", qty: "" }],
});

// ==========================================
// LOGIKA DROPDOWN PENCARIAN NASABAH
// ==========================================
const searchNasabahTerm = ref("");
const showNasabahDropdown = ref(false);

// Filter daftar nasabah secara real-time berdasarkan input ketikan
const filteredNasabahList = computed(() => {
    if (!searchNasabahTerm.value) return props.nasabahList;
    return props.nasabahList.filter((n) =>
        n.name.toLowerCase().includes(searchNasabahTerm.value.toLowerCase()),
    );
});

// Fungsi saat admin mengklik salah satu nama di daftar pencarian
const selectNasabah = (nsb) => {
    form.nasabah_id = nsb.id;
    searchNasabahTerm.value = `${nsb.name} (Saldo: Rp ${Number(nsb.balance).toLocaleString("id-ID")})`;
    showNasabahDropdown.value = false;
};

// Fungsi saat input kehilangan fokus (klik di luar)
const handleNasabahBlur = () => {
    setTimeout(() => {
        showNasabahDropdown.value = false;
        if (!form.nasabah_id) {
            searchNasabahTerm.value = "";
        }
    }, 200);
};
// ==========================================

const addRow = () => {
    form.items.push({ waste_id: "", qty: "" });
};
const removeRow = (index) => {
    if (form.items.length > 1) form.items.splice(index, 1);
};

const getWastePrice = (wasteId) => {
    const waste = props.wasteList.find((w) => w.id === wasteId);
    return waste ? Number(waste.current_price) : 0;
};

const formTotalInvoice = computed(() => {
    return form.items.reduce((sum, item) => {
        const price = getWastePrice(item.waste_id);
        const itemQty = Number(item.qty) || 0;
        return sum + itemQty * price;
    }, 0);
});

const closeDepositModal = () => {
    form.reset();
    form.items = [{ waste_id: "", qty: "" }];
    searchNasabahTerm.value = "";
    showNasabahDropdown.value = false;
    showCreateModal.value = false;
};

const submitDeposit = () => {
    form.post(route("admin.deposits.store"), {
        onSuccess: () => closeDepositModal(),
    });
};

const triggerDeleteConfirm = (item) => {
    selectedDeposit.value = item;
    showConfirmModal.value = true;
};

const executeDelete = () => {
    router.delete(route("admin.deposits.destroy", selectedDeposit.value.id), {
        preserveScroll: true,
        onSuccess: () => (showConfirmModal.value = false),
    });
};

// Fitur Filter Halaman Utama
const search = ref(props.filters?.search || "");
let searchTimer = null;
watch(search, (newValue) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(
            route("admin.deposits.index"),
            { search: newValue },
            { preserveState: true, replace: true },
        );
    }, 400);
});

// Fitur Toast Pesan Berhasil
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
    <Head title="Setor Sampah" />

    <AuthenticatedLayout>
        <template #header> Loket Timbang & Setor </template>

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
                        class="bg-green-50 text-green-500 p-1.5 rounded-lg flex-shrink-0"
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
                            Transaksi Berhasil
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
                            Riwayat Nota Timbangan
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Pantau buku setoran masuk anggota nasabah beserta
                            rincian komoditas sampah terkait.
                        </p>
                    </div>
                    <button
                        @click="showCreateModal = true"
                        class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2 cursor-pointer shadow-xs"
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
                        Buat Nota Timbangan Baru
                    </button>
                </div>

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
                            placeholder="Cari berdasarkan nama nasabah..."
                            class="block w-full rounded-lg border-gray-200 py-2.5 pr-2.5 pl-10 text-xs sm:text-sm focus:border-green-500 focus:ring-green-500/10 transition-all"
                        />
                    </div>
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
                                        No. Nota / Waktu
                                    </th>
                                    <th class="p-4 font-semibold">Nasabah</th>
                                    <th
                                        class="p-4 font-semibold text-center w-24"
                                    >
                                        Foto Bukti
                                    </th>
                                    <th class="p-4 font-semibold">
                                        Rincian Komoditas
                                    </th>
                                    <th class="p-4 font-semibold text-right">
                                        Total Kredit
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
                                    v-for="(item, index) in deposits.data"
                                    :key="item.id"
                                    class="hover:bg-gray-50/40 transition-colors whitespace-nowrap"
                                >
                                    <td class="p-4 text-gray-500 text-center">
                                        {{ deposits.from + index }}
                                    </td>
                                    <td
                                        class="p-4 text-xs font-mono text-gray-500"
                                    >
                                        <span class="font-bold text-gray-700"
                                            >#DEP-{{ item.id }}</span
                                        >
                                        <p class="mt-0.5">
                                            {{
                                                new Date(
                                                    item.created_at,
                                                ).toLocaleString("id-ID", {
                                                    day: "2-digit",
                                                    month: "short",
                                                    hour: "2-digit",
                                                    minute: "2-digit",
                                                })
                                            }}
                                        </p>
                                    </td>
                                    <td
                                        class="p-4 font-medium text-gray-900 whitespace-normal"
                                    >
                                        {{
                                            item.nasabah?.name || "User Dihapus"
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
                                    <td class="p-4 whitespace-normal max-w-xs">
                                        <div class="flex flex-wrap gap-1">
                                            <span
                                                v-for="det in item.details"
                                                :key="det.id"
                                                class="inline-block bg-gray-50 text-gray-600 text-[11px] px-2 py-0.5 rounded border border-gray-200"
                                            >
                                                {{ det.waste?.name }} ({{
                                                    det.qty
                                                }}
                                                kg)
                                            </span>
                                        </div>
                                    </td>
                                    <td
                                        class="p-4 text-right font-extrabold text-green-600 font-mono"
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
                                <tr v-if="deposits.data.length === 0">
                                    <td
                                        colspan="7"
                                        class="p-12 text-center text-gray-400"
                                    >
                                        Belum ada riwayat nota setoran yang
                                        dicatat.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div
                    v-if="deposits.links.length > 3"
                    class="mt-6 flex flex-wrap justify-center gap-1 border-t border-gray-50 pt-4"
                >
                    <template v-for="(link, k) in deposits.links" :key="k">
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
                                    ? 'bg-green-600 text-white border-green-600 font-semibold'
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
                @click="closeDepositModal"
            ></div>

            <div
                class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl border border-gray-100 max-w-xl w-full relative z-10 max-h-[92vh] sm:max-h-none flex flex-col overflow-hidden animate-in slide-in-from-bottom-10 sm:zoom-in-95 duration-200"
            >
                <div
                    class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center flex-shrink-0"
                >
                    <h3
                        class="text-sm font-bold text-gray-800 uppercase tracking-wide"
                    >
                        Penerimaan Timbangan Sampah
                    </h3>
                    <button
                        @click="closeDepositModal"
                        class="sm:hidden text-gray-400 hover:text-gray-600 text-lg p-1"
                    >
                        ✕
                    </button>
                </div>

                <form
                    @submit.prevent="submitDeposit"
                    class="p-5 sm:p-6 space-y-5 overflow-y-auto flex-1"
                >
                    <div class="relative">
                        <label
                            class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1"
                            >Akun Anggota Nasabah</label
                        >
                        <div class="relative">
                            <input
                                v-model="searchNasabahTerm"
                                @focus="showNasabahDropdown = true"
                                @blur="handleNasabahBlur"
                                @input="form.nasabah_id = ''"
                                type="text"
                                placeholder="Ketik nama nasabah untuk mencari..."
                                class="w-full text-base sm:text-sm border-gray-200 rounded-lg py-3 sm:py-2.5 pr-3 sm:pr-2.5 pl-11 focus:ring-green-500/20 focus:border-green-500 bg-white shadow-xs transition-all font-medium"
                                :class="{
                                    'border-red-300': form.errors.nasabah_id,
                                }"
                            />
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"
                            >
                                <svg
                                    class="h-4 w-4 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    ></path>
                                </svg>
                            </div>
                        </div>

                        <div
                            v-if="showNasabahDropdown"
                            class="absolute z-50 w-full mt-1 bg-white border border-gray-100 rounded-xl shadow-xl max-h-48 overflow-y-auto"
                        >
                            <ul class="py-1 divide-y divide-gray-50">
                                <li
                                    v-for="nsb in filteredNasabahList"
                                    :key="nsb.id"
                                    @mousedown.prevent="selectNasabah(nsb)"
                                    class="px-4 py-3 sm:py-2.5 hover:bg-green-50 hover:text-green-700 cursor-pointer transition-colors flex justify-between items-center"
                                >
                                    <div>
                                        <span
                                            class="block text-sm font-bold text-gray-800"
                                            >{{ nsb.name }}</span
                                        >
                                        <span class="text-[10px] text-gray-400"
                                            >ID: {{ nsb.id }}</span
                                        >
                                    </div>
                                    <span
                                        class="text-xs font-mono font-bold text-gray-500"
                                    >
                                        Saldo: Rp
                                        {{
                                            Number(nsb.balance).toLocaleString(
                                                "id-ID",
                                            )
                                        }}
                                    </span>
                                </li>
                                <li
                                    v-if="filteredNasabahList.length === 0"
                                    class="px-4 py-5 text-xs text-gray-400 text-center"
                                >
                                    Nasabah tidak ditemukan.
                                </li>
                            </ul>
                        </div>
                        <p
                            v-if="form.errors.nasabah_id"
                            class="text-xs text-red-500 mt-1"
                        >
                            {{ form.errors.nasabah_id }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1"
                            >Foto Bukti Timbangan / Nota Fisik (Opsional)</label
                        >
                        <input
                            type="file"
                            @input="form.photo_proof = $event.target.files[0]"
                            class="w-full text-xs border border-gray-200 rounded-lg p-2.5 bg-white focus:outline-hidden focus:ring-green-500/20 focus:border-green-500 shadow-xs"
                            accept="image/*"
                        />
                        <p
                            v-if="form.errors.photo_proof"
                            class="text-xs text-red-500 mt-1"
                        >
                            {{ form.errors.photo_proof }}
                        </p>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label
                                class="block text-xs font-bold text-gray-400 uppercase tracking-wider"
                                >Daftar Timbangan Sampah</label
                            >
                            <button
                                type="button"
                                @click="addRow"
                                class="text-xs font-bold text-green-600 hover:text-green-700 flex items-center gap-1 cursor-pointer bg-green-50 sm:bg-transparent px-2.5 py-1.5 sm:p-0 rounded-md"
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
                                class="flex flex-col sm:flex-row gap-2.5 sm:gap-2 bg-gray-50 p-3 sm:p-2 rounded-xl border border-gray-100 shadow-xs relative"
                            >
                                <div class="flex-1">
                                    <select
                                        v-model="row.waste_id"
                                        class="w-full text-base sm:text-xs border-gray-200 rounded-lg sm:rounded-md p-2.5 sm:p-2 bg-white focus:outline-hidden"
                                    >
                                        <option value="" disabled>
                                            Pilih Jenis Sampah
                                        </option>
                                        <option
                                            v-for="wst in wasteList"
                                            :key="wst.id"
                                            :value="wst.id"
                                        >
                                            {{ wst.name }} (Rp
                                            {{
                                                Number(
                                                    wst.current_price,
                                                ).toLocaleString("id-ID")
                                            }}/{{ wst.unit }})
                                        </option>
                                    </select>
                                </div>

                                <div
                                    class="flex items-center gap-2 w-full sm:w-32"
                                >
                                    <div class="relative flex-1 sm:w-full">
                                        <input
                                            v-model="row.qty"
                                            type="text"
                                            inputmode="decimal"
                                            pattern="[0-9]*[.,]?[0-9]*"
                                            placeholder="Berat"
                                            class="w-full text-base sm:text-xs border-gray-200 rounded-lg sm:rounded-md p-2.5 sm:p-2 bg-white pr-11 focus:outline-hidden font-medium"
                                        />
                                        <div
                                            class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                                        >
                                            <span
                                                class="text-xs sm:text-[10px] text-gray-400 font-bold"
                                                >Kg</span
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
                        class="bg-green-600 text-white rounded-xl p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-1 shadow-xs"
                    >
                        <div>
                            <p
                                class="text-[10px] uppercase font-bold tracking-wider opacity-80"
                            >
                                Total Saldo Masuk
                            </p>
                            <p class="text-[11px] opacity-90 mt-0.5">
                                Kalkulasi dari {{ form.items.length }} baris
                                komoditas
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
                            @click="closeDepositModal"
                            class="px-4 py-3 sm:py-2 text-sm sm:text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg cursor-pointer text-center"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-3 sm:py-2 text-sm sm:text-xs font-bold text-white bg-green-600 hover:bg-green-700 rounded-lg disabled:opacity-50 cursor-pointer text-center shadow-xs"
                        >
                            Simpan Nota & Cetak Saldo
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
                    Batalkan Seluruh Nota Setoran?
                </h3>
                <p class="text-xs text-gray-500 leading-relaxed mb-6 px-2">
                    Membatalkan nota **#DEP-{{ selectedDeposit?.id }}** akan
                    otomatis menarik balik saldo sebesar **Rp
                    {{
                        Number(selectedDeposit?.total_amount).toLocaleString(
                            "id-ID",
                        )
                    }}** dari akun nasabah terkait.
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
                        Ya, Batalkan Nota
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Pengaman visual scrollbar */
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
