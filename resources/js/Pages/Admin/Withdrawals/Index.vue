<script setup>
import { ref, watch, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";

const props = defineProps({
    withdrawals: Object,
    nasabahList: Array,
    filters: Object,
});

const showCreateModal = ref(false);
const showConfirmModal = ref(false);
const selectedWithdrawal = ref(null);

const form = useForm({
    nasabah_id: "",
    amount: "",
    photo_proof: null,
});

// ==========================================
// LOGIKA DROPDOWN PENCARIAN NASABAH (BARU)
// ==========================================
const searchNasabahTerm = ref("");
const showNasabahDropdown = ref(false);

const filteredNasabahList = computed(() => {
    if (!searchNasabahTerm.value) return props.nasabahList;
    return props.nasabahList.filter((n) =>
        n.name.toLowerCase().includes(searchNasabahTerm.value.toLowerCase()),
    );
});

const selectNasabah = (nsb) => {
    form.nasabah_id = nsb.id;
    searchNasabahTerm.value = `${nsb.name} (Maks Saldo: Rp ${Number(nsb.balance).toLocaleString("id-ID")})`;
    showNasabahDropdown.value = false;
};

const handleNasabahBlur = () => {
    setTimeout(() => {
        showNasabahDropdown.value = false;
        if (!form.nasabah_id) {
            searchNasabahTerm.value = "";
        }
    }, 200);
};
// ==========================================

const closeWithdrawalModal = () => {
    form.reset();
    searchNasabahTerm.value = "";
    showNasabahDropdown.value = false;
    showCreateModal.value = false;
};

const submitWithdrawal = () => {
    form.post(route("admin.withdrawals.store"), {
        onSuccess: () => closeWithdrawalModal(),
    });
};

const triggerDeleteConfirm = (item) => {
    selectedWithdrawal.value = item;
    showConfirmModal.value = true;
};

const executeDelete = () => {
    router.delete(
        route("admin.withdrawals.destroy", selectedWithdrawal.value.id),
        {
            preserveScroll: true,
            onSuccess: () => (showConfirmModal.value = false),
        },
    );
};

const search = ref(props.filters?.search || "");
let searchTimer = null;
watch(search, (newValue) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(
            route("admin.withdrawals.index"),
            { search: newValue },
            { preserveState: true, replace: true },
        );
    }, 400);
});

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
    <Head title="Tarik Saldo" />

    <AuthenticatedLayout>
        <template #header> Loket Penarikan Tunai </template>

        <!-- Toast Floating -->
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
                        class="bg-amber-50 text-amber-500 p-1.5 rounded-lg flex-shrink-0"
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
                            Transaksi Sukses
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
                            Log Penarikan Saldo Nasabah
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Catat debit keluar dari tabungan nasabah serta
                            arsipkan berkas jaminan serah terima dana.
                        </p>
                    </div>
                    <button
                        @click="showCreateModal = true"
                        class="w-full sm:w-auto bg-amber-600 hover:bg-amber-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2 cursor-pointer shadow-xs"
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
                        Proses Tarik Tunai Baru
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
                            class="block w-full rounded-lg border-gray-200 pl-9 text-xs sm:text-sm focus:border-amber-500 focus:ring-amber-500/10 p-2.5 transition-all"
                        />
                    </div>
                </div>

                <!-- Tabel Utama Log Debet -->
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
                                        No. Kwitansi / Waktu
                                    </th>
                                    <th class="p-4 font-semibold">Nasabah</th>
                                    <th
                                        class="p-4 font-semibold text-center w-24"
                                    >
                                        Bukti Fisik
                                    </th>
                                    <th class="p-4 font-semibold text-right">
                                        Debet Keluar
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
                                    v-for="(item, index) in withdrawals.data"
                                    :key="item.id"
                                    class="hover:bg-gray-50/40 transition-colors whitespace-nowrap"
                                >
                                    <td class="p-4 text-gray-500 text-center">
                                        {{ withdrawals.from + index }}
                                    </td>
                                    <td
                                        class="p-4 text-xs font-mono text-gray-500"
                                    >
                                        <span class="font-bold text-gray-700"
                                            >#WIT-{{ item.id }}</span
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
                                                    class="w-10 h-10 object-cover rounded-md mx-auto border border-gray-200 shadow-2xs hover:scale-105 transition-transform"
                                                />
                                            </a>
                                        </div>
                                        <span
                                            v-else
                                            class="text-xs text-gray-400 font-mono"
                                            >-</span
                                        >
                                    </td>

                                    <td
                                        class="p-4 text-right font-extrabold text-red-600 font-mono"
                                    >
                                        Rp
                                        {{
                                            Number(item.amount).toLocaleString(
                                                "id-ID",
                                            )
                                        }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <button
                                            @click="triggerDeleteConfirm(item)"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all cursor-pointer"
                                            title="Batalkan Penarikan / Ambil Saldo"
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
                                <tr v-if="withdrawals.data.length === 0">
                                    <td
                                        colspan="6"
                                        class="p-12 text-center text-gray-400"
                                    >
                                        Belum ada riwayat penarikan dana
                                        dicatat.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div
                    v-if="withdrawals.links.length > 3"
                    class="mt-6 flex flex-wrap justify-center gap-1 border-t border-gray-50 pt-4"
                >
                    <template v-for="(link, k) in withdrawals.links" :key="k">
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
                                    ? 'bg-amber-600 text-white border-amber-600 font-semibold'
                                    : 'text-gray-600 bg-white border-gray-200 hover:bg-gray-50'
                            "
                            :href="link.url"
                            v-html="link.label"
                        ></Link>
                    </template>
                </div>
            </div>
        </div>

        <!-- MODAL JENDELA INPUT TARIK TUNAI -->
        <div
            v-if="showCreateModal"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        >
            <div
                class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs"
                @click="closeWithdrawalModal"
            ></div>
            <div
                class="bg-white rounded-xl shadow-xl border border-gray-100 max-w-md w-full relative z-10 overflow-hidden animate-in fade-in zoom-in-95 duration-200"
            >
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                    <h3
                        class="text-sm font-bold text-gray-800 uppercase tracking-wide"
                    >
                        Form Penarikan Saldo Tabungan
                    </h3>
                </div>
                <form @submit.prevent="submitWithdrawal" class="p-6 space-y-4">
                    <!-- FITUR PENCARIAN NASABAH (AUTOCOMPLETE DROPDOWN) -->
                    <div class="relative">
                        <label
                            class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1"
                            >Akun Nasabah</label
                        >
                        <div class="relative">
                            <input
                                v-model="searchNasabahTerm"
                                @focus="showNasabahDropdown = true"
                                @blur="handleNasabahBlur"
                                @input="form.nasabah_id = ''"
                                type="text"
                                placeholder="Ketik nama nasabah untuk mencari..."
                                class="w-full text-sm border-gray-200 rounded-lg p-2.5 pl-9 focus:ring-amber-500/20 focus:border-amber-500 bg-white shadow-xs transition-all"
                                :class="{
                                    'border-red-300': form.errors.nasabah_id,
                                }"
                            />
                            <!-- Icon Kaca Pembesar Kecil di dalam Input -->
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
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

                        <!-- Dropdown List Hasil Pencarian -->
                        <div
                            v-if="showNasabahDropdown"
                            class="absolute z-50 w-full mt-1 bg-white border border-gray-100 rounded-xl shadow-xl max-h-48 overflow-y-auto"
                        >
                            <ul
                                class="py-1 text-sm text-gray-700 divide-y divide-gray-50"
                            >
                                <!-- Pakai mousedown.prevent agar input tidak kehilangan fokus seketika saat di klik -->
                                <li
                                    v-for="nsb in filteredNasabahList"
                                    :key="nsb.id"
                                    @mousedown.prevent="selectNasabah(nsb)"
                                    class="px-4 py-2.5 hover:bg-amber-50 hover:text-amber-700 cursor-pointer transition-colors flex justify-between items-center"
                                >
                                    <div>
                                        <span class="block font-bold">{{
                                            nsb.name
                                        }}</span>
                                        <span class="text-[10px] text-gray-400"
                                            >ID: {{ nsb.id }}</span
                                        >
                                    </div>
                                    <span
                                        class="text-xs font-mono font-bold text-gray-500"
                                        >Maks: Rp
                                        {{
                                            Number(nsb.balance).toLocaleString(
                                                "id-ID",
                                            )
                                        }}</span
                                    >
                                </li>
                                <li
                                    v-if="filteredNasabahList.length === 0"
                                    class="px-4 py-4 text-xs text-gray-400 text-center"
                                >
                                    Nasabah tidak ditemukan. Pastikan nama
                                    sesuai.
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

                    <!-- Input Nominal Dana Keluar -->
                    <div>
                        <label
                            class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1"
                            >Nominal yang Diambil</label
                        >
                        <div class="relative rounded-lg shadow-2xs">
                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                            >
                                <span class="text-xs text-gray-400 font-bold"
                                    >Rp</span
                                >
                            </div>
                            <input
                                v-model="form.amount"
                                type="number"
                                min="100"
                                placeholder="0"
                                class="block w-full text-sm border-gray-200 rounded-lg p-2.5 pl-9 focus:ring-amber-500/20 focus:border-amber-500"
                                :class="{
                                    'border-red-300': form.errors.amount,
                                }"
                            />
                        </div>
                        <p
                            v-if="form.errors.amount"
                            class="text-xs text-red-500 mt-1"
                        >
                            {{ form.errors.amount }}
                        </p>
                    </div>

                    <!-- Bukti Unggah Foto Serah Terima Kas -->
                    <div>
                        <label
                            class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1"
                            >Foto Bukti Penyerahan Uang Tunai</label
                        >
                        <input
                            type="file"
                            @input="form.photo_proof = $event.target.files[0]"
                            class="w-full text-xs border border-gray-200 rounded-lg p-2 bg-white focus:outline-hidden"
                            accept="image/*"
                        />
                        <p
                            v-if="form.errors.photo_proof"
                            class="text-xs text-red-500 mt-1"
                        >
                            {{ form.errors.photo_proof }}
                        </p>
                    </div>

                    <div
                        class="flex items-center justify-end gap-2 border-t border-gray-50 pt-4 mt-6"
                    >
                        <button
                            type="button"
                            @click="closeWithdrawalModal"
                            class="px-4 py-2 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-lg disabled:opacity-50 cursor-pointer shadow-xs"
                        >
                            Cairkan Dana Tunai
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL KONFIRMASI PEMBATALAN PENARIKAN -->
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
                    Batalkan Kwitansi Penarikan?
                </h3>
                <p class="text-xs text-gray-500 leading-relaxed mb-6 px-2">
                    Membatalkan kwitansi **#WIT-{{ selectedWithdrawal?.id }}**
                    berarti membatalkan penyerahan tunai dan akan
                    **mengembalikan otomatis (increment)** saldo sebesar **Rp
                    {{
                        Number(selectedWithdrawal?.amount).toLocaleString(
                            "id-ID",
                        )
                    }}** ke dalam akun nasabah terkait.
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
                        class="px-4 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg cursor-pointer"
                    >
                        Ya, Batalkan Transaksi
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
