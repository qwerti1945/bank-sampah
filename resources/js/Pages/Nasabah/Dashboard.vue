<script setup>
import { ref } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import NasabahLayout from "@/Layouts/NasabahLayout.vue";

// Definisikan props dengan default value aman
defineProps({
    nasabah: {
        type: Object,
        default: () => ({ name: "Nasabah", balance: 0 }),
    },
    deposits: {
        type: Array,
        default: () => [],
    },
    withdrawals: {
        type: Array,
        default: () => [],
    },
    wastes: {
        type: Array,
        default: () => [],
    },
});

const activeTab = ref("setor"); // 'setor', 'tarik', atau 'harga'

// ==========================================
// LOGIKA MODAL DETAIL TRANSAKSI
// ==========================================
const isModalOpen = ref(false);
const selectedTx = ref(null);
const txType = ref(""); // 'setor' atau 'tarik'

const openDetail = (tx, type) => {
    selectedTx.value = tx;
    txType.value = type;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    selectedTx.value = null;
    txType.value = "";
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Fungsi Cerdas menghitung total harga per baris item sampah (Anti Rp 0)
const getItemTotal = (item) => {
    if (item.subtotal && Number(item.subtotal) > 0)
        return Number(item.subtotal).toLocaleString("id-ID");
    if (item.total_amount && Number(item.total_amount) > 0)
        return Number(item.total_amount).toLocaleString("id-ID");

    const hargaSatuan =
        item.price || item.harga || item.waste?.current_price || 0;
    const kalkulasi = Number(item.qty || 0) * Number(hargaSatuan);

    if (kalkulasi > 0) {
        return kalkulasi.toLocaleString("id-ID");
    } else if (selectedTx.value && selectedTx.value.total_amount) {
        const hitungRata =
            Number(selectedTx.value.total_amount) /
            (selectedTx.value.details?.length || 1);
        return hitungRata.toLocaleString("id-ID");
    }

    return "0";
};
</script>

<template>
    <Head title="Beranda Nasabah" />

    <NasabahLayout>
        <div class="bg-emerald-600 px-6 pt-8 pb-20 rounded-b-3xl">
            <div class="flex justify-between items-center text-white">
                <div>
                    <p class="text-emerald-100 text-sm">Selamat datang,</p>
                    <h1 class="text-xl font-bold truncate max-w-[220px]">
                        {{ nasabah.name }}
                    </h1>
                </div>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="p-2.5 bg-emerald-700/50 rounded-full hover:bg-emerald-800 transition"
                >
                    <svg
                        class="w-5 h-5 text-white"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                        ></path>
                    </svg>
                </Link>
            </div>
        </div>

        <div class="px-6 -mt-12 relative z-10">
            <div
                class="bg-white rounded-2xl p-6 shadow-md border border-gray-100 flex flex-col justify-center items-center text-center"
            >
                <p
                    class="text-xs text-gray-500 font-bold uppercase tracking-widest mb-2"
                >
                    Saldo Simpanan
                </p>
                <h2
                    class="text-4xl font-black text-gray-800 font-mono tracking-tight"
                >
                    Rp {{ Number(nasabah.balance).toLocaleString("id-ID") }}
                </h2>
            </div>
        </div>

        <div class="px-6 mt-8">
            <div
                class="flex gap-4 border-b border-gray-100 overflow-x-auto whitespace-nowrap hide-scrollbar pb-1"
            >
                <button
                    @click="activeTab = 'setor'"
                    :class="[
                        'pb-2 px-1 text-sm font-bold border-b-2 transition-all cursor-pointer',
                        activeTab === 'setor'
                            ? 'border-emerald-600 text-emerald-600'
                            : 'border-transparent text-gray-400 hover:text-gray-600',
                    ]"
                >
                    Riwayat Setoran
                </button>
                <button
                    @click="activeTab = 'tarik'"
                    :class="[
                        'pb-2 px-1 text-sm font-bold border-b-2 transition-all cursor-pointer',
                        activeTab === 'tarik'
                            ? 'border-rose-600 text-rose-600'
                            : 'border-transparent text-gray-400 hover:text-gray-600',
                    ]"
                >
                    Penarikan
                </button>
                <button
                    @click="activeTab = 'harga'"
                    :class="[
                        'pb-2 px-1 text-sm font-bold border-b-2 transition-all cursor-pointer',
                        activeTab === 'harga'
                            ? 'border-blue-600 text-blue-600'
                            : 'border-transparent text-gray-400 hover:text-gray-600',
                    ]"
                >
                    Harga Sampah
                </button>
            </div>
        </div>

        <div class="px-6 mt-4">
            <div v-show="activeTab === 'setor'" class="space-y-1">
                <div
                    v-for="dep in deposits"
                    :key="dep.id"
                    @click="openDetail(dep, 'setor')"
                    class="flex justify-between items-center py-4 border-b border-gray-50 last:border-0 cursor-pointer hover:bg-gray-50/50 px-1 rounded-lg transition-colors"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"
                                ></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">
                                Setor Sampah
                            </p>
                            <p class="text-[10px] text-gray-400">
                                {{ formatDate(dep.created_at) }}
                            </p>
                        </div>
                    </div>
                    <p class="text-sm font-black text-emerald-600 font-mono">
                        + Rp{{
                            Number(dep.total_amount).toLocaleString("id-ID")
                        }}
                    </p>
                </div>

                <div
                    v-if="!deposits.length"
                    class="text-center py-10 text-gray-400 text-sm"
                >
                    Belum ada riwayat setoran.
                </div>
            </div>

            <div v-show="activeTab === 'tarik'" class="space-y-1">
                <div
                    v-for="wit in withdrawals"
                    :key="wit.id"
                    @click="openDetail(wit, 'tarik')"
                    class="flex justify-between items-center py-4 border-b border-gray-50 last:border-0 cursor-pointer hover:bg-gray-50/50 px-1 rounded-lg transition-colors"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center shrink-0"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18"
                                ></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">
                                Tarik Saldo
                            </p>
                            <p class="text-[10px] text-gray-400">
                                {{ formatDate(wit.created_at) }}
                            </p>
                        </div>
                    </div>
                    <p class="text-sm font-black text-rose-600 font-mono">
                        - Rp{{ Number(wit.amount).toLocaleString("id-ID") }}
                    </p>
                </div>

                <div
                    v-if="!withdrawals.length"
                    class="text-center py-10 text-gray-400 text-sm"
                >
                    Belum ada riwayat penarikan.
                </div>
            </div>

            <div v-show="activeTab === 'harga'" class="space-y-1 pt-2">
                <div
                    v-for="waste in wastes"
                    :key="waste.id"
                    class="flex justify-between items-center py-3 border-b border-gray-50 last:border-0"
                >
                    <div>
                        <p class="text-sm font-bold text-gray-800">
                            {{ waste.name }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-blue-600 font-mono">
                            Rp
                            {{
                                Number(waste.current_price || 0).toLocaleString(
                                    "id-ID",
                                )
                            }}
                        </p>
                        <p class="text-[10px] text-gray-400">
                            per {{ waste.unit || "kg" }}
                        </p>
                    </div>
                </div>

                <div
                    v-if="wastes.length === 0"
                    class="text-center py-10 text-gray-400 text-sm"
                >
                    Katalog harga sampah belum tersedia.
                </div>
            </div>
        </div>

        <div
            v-if="isModalOpen && selectedTx"
            class="fixed inset-0 bg-black/60 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 animate-in fade-in duration-200"
        >
            <div class="absolute inset-0" @click="closeModal"></div>

            <div
                class="bg-white w-full max-w-md rounded-t-3xl sm:rounded-2xl p-6 relative z-10 max-h-[90vh] overflow-y-auto shadow-2xl flex flex-col animate-in slide-in-from-bottom-8 duration-300"
            >
                <div
                    class="text-center pb-4 border-b-2 border-dashed border-gray-200 relative"
                >
                    <button
                        @click="closeModal"
                        class="absolute right-0 top-0 text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer"
                    >
                        ✕
                    </button>
                    <h3
                        class="font-black text-gray-800 tracking-wide uppercase text-sm"
                    >
                        Nota Transaksi Digital
                    </h3>
                    <p class="text-xs text-gray-400 font-mono mt-1">
                        ID-{{ txType.toUpperCase() }}-{{ selectedTx.id }}
                    </p>
                </div>

                <div class="py-4 space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Jenis Transaksi</span
                        ><span
                            :class="[
                                'font-bold uppercase',
                                txType === 'setor'
                                    ? 'text-emerald-600'
                                    : 'text-rose-600',
                            ]"
                            >{{
                                txType === "setor"
                                    ? "Setor Sampah"
                                    : "Penarikan Dana"
                            }}</span
                        >
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Waktu</span
                        ><span class="text-gray-700 font-medium">{{
                            formatDate(selectedTx.created_at)
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Status</span
                        ><span
                            class="text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded text-[10px]"
                            >SUKSES</span
                        >
                    </div>

                    <div
                        class="border-t border-dashed border-gray-200 my-3"
                    ></div>

                    <div
                        v-if="
                            txType === 'setor' &&
                            selectedTx.details &&
                            selectedTx.details.length > 0
                        "
                        class="space-y-2"
                    >
                        <p
                            class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1"
                        >
                            Rincian Komoditas
                        </p>
                        <div
                            v-for="item in selectedTx.details"
                            :key="item.id"
                            class="flex justify-between text-gray-600 font-mono"
                        >
                            <span
                                >{{ item.waste?.name || "Sampah" }} ({{
                                    Number(item.qty).toLocaleString("id-ID")
                                }}
                                {{ item.waste?.unit || "kg" }})</span
                            >
                            <span>Rp {{ getItemTotal(item) }}</span>
                        </div>
                        <div
                            class="border-t border-dashed border-gray-200 my-3"
                        ></div>
                    </div>

                    <div class="flex justify-between items-center pt-2">
                        <span class="text-sm font-bold text-gray-800"
                            >Total Jumlah</span
                        >
                        <span
                            :class="[
                                'text-xl font-black font-mono',
                                txType === 'setor'
                                    ? 'text-emerald-600'
                                    : 'text-rose-600',
                            ]"
                        >
                            {{ txType === "setor" ? "+" : "-" }} Rp
                            {{
                                Number(
                                    txType === "setor"
                                        ? selectedTx.total_amount
                                        : selectedTx.amount,
                                ).toLocaleString("id-ID")
                            }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t-2 border-dashed border-gray-200">
                    <p
                        class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2"
                    >
                        Bukti Foto / Lampiran Fisik
                    </p>
                    <div
                        class="w-full h-44 bg-gray-100 rounded-xl overflow-hidden border border-gray-100 flex items-center justify-center relative shadow-inner"
                    >
                        <img
                            v-if="
                                selectedTx.image_path ||
                                selectedTx.proof_photo ||
                                selectedTx.foto ||
                                selectedTx.photo_proof
                            "
                            :src="
                                '/storage/' +
                                (selectedTx.image_path ||
                                    selectedTx.proof_photo ||
                                    selectedTx.foto ||
                                    selectedTx.photo_proof)
                            "
                            class="w-full h-full object-cover"
                            alt="Bukti Foto Transaksi"
                        />
                        <div v-else class="text-center p-4 text-gray-400">
                            <svg
                                class="w-10 h-10 mx-auto text-gray-300 mb-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"
                                ></path>
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"
                                ></path>
                            </svg>
                            <p class="text-[10px] text-gray-400">
                                Foto nota fisik belum diunggah oleh admin loket
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-6">
                    <p class="text-[10px] text-gray-400 italic">
                        ~ Terimakasih telah menjaga kebersihan lingkungan ~
                    </p>
                </div>
            </div>
        </div>
    </NasabahLayout>
</template>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
