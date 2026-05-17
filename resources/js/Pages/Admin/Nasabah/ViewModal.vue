<script setup>
import { ref } from "vue";

defineProps({
    show: Boolean,
    nasabah: Object,
});

defineEmits(["close"]);

const activeTab = ref("profil"); // Pilihan: 'profil', 'setor', 'tarik'

// Fungsi format tanggal bawaan JS
const formatDate = (dateString) => {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
    >
        <!-- Backdrop -->
        <div
            class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity"
            @click="$emit('close')"
        ></div>

        <!-- Box Modal -->
        <div
            class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden max-w-lg w-full relative z-10 transition-all transform animate-in fade-in zoom-in-95 duration-200 flex flex-col max-h-[90vh]"
        >
            <!-- Header Modal -->
            <div
                class="px-6 py-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50 flex-shrink-0"
            >
                <h3 class="text-base font-bold text-gray-800">
                    Buku Rekening Nasabah
                </h3>
                <button
                    @click="$emit('close')"
                    class="text-gray-400 hover:text-gray-500 focus:outline-none cursor-pointer"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        ></path>
                    </svg>
                </button>
            </div>

            <!-- Tab Navigasi -->
            <div
                class="flex border-b border-gray-100 bg-white flex-shrink-0 px-4"
            >
                <button
                    @click="activeTab = 'profil'"
                    :class="[
                        'px-4 py-3 text-xs font-bold border-b-2 transition-colors',
                        activeTab === 'profil'
                            ? 'border-green-500 text-green-600'
                            : 'border-transparent text-gray-400 hover:text-gray-600',
                    ]"
                >
                    Profil Utama
                </button>
                <button
                    @click="activeTab = 'setor'"
                    :class="[
                        'px-4 py-3 text-xs font-bold border-b-2 transition-colors',
                        activeTab === 'setor'
                            ? 'border-green-500 text-green-600'
                            : 'border-transparent text-gray-400 hover:text-gray-600',
                    ]"
                >
                    Riwayat Setor
                </button>
                <button
                    @click="activeTab = 'tarik'"
                    :class="[
                        'px-4 py-3 text-xs font-bold border-b-2 transition-colors',
                        activeTab === 'tarik'
                            ? 'border-amber-500 text-amber-600'
                            : 'border-transparent text-gray-400 hover:text-gray-600',
                    ]"
                >
                    Riwayat Tarik
                </button>
            </div>

            <!-- Konten Data (Scrollable Area) -->
            <div class="p-6 overflow-y-auto flex-1 bg-gray-50/20">
                <!-- TAB 1: PROFIL -->
                <div v-show="activeTab === 'profil'" class="space-y-6">
                    <div
                        class="flex items-center gap-4 bg-green-50/50 p-4 rounded-xl border border-green-100/50"
                    >
                        <div
                            class="w-12 h-12 rounded-full bg-green-500 text-white flex items-center justify-center text-lg font-bold shadow-xs flex-shrink-0"
                        >
                            {{ nasabah.name.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <p
                                class="text-xs text-gray-400 font-medium uppercase tracking-wider"
                            >
                                Total Saldo Saat Ini
                            </p>
                            <p class="text-xl font-black text-green-600 mt-0.5">
                                Rp
                                {{
                                    Number(nasabah.balance).toLocaleString(
                                        "id-ID",
                                    )
                                }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4 text-sm">
                        <div>
                            <span
                                class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1"
                                >Nama Lengkap</span
                            >
                            <p
                                class="text-gray-800 font-medium bg-white px-3 py-2 rounded-lg border border-gray-100"
                            >
                                {{ nasabah.name }}
                            </p>
                        </div>
                        <div>
                            <span
                                class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1"
                                >Alamat Email</span
                            >
                            <p
                                class="text-gray-800 font-medium bg-white px-3 py-2 rounded-lg border border-gray-100"
                            >
                                {{ nasabah.email }}
                            </p>
                        </div>
                        <div>
                            <span
                                class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1"
                                >Nomor Telepon / WhatsApp</span
                            >
                            <p
                                class="text-gray-800 font-medium bg-white px-3 py-2 rounded-lg border border-gray-100"
                            >
                                {{ nasabah.phone || "-" }}
                            </p>
                        </div>
                        <div>
                            <span
                                class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1"
                                >Alamat Domisili</span
                            >
                            <p
                                class="text-gray-800 font-medium bg-white px-3 py-2 rounded-lg border border-gray-100 whitespace-pre-line leading-relaxed"
                            >
                                {{ nasabah.address || "-" }}
                            </p>
                        </div>
                        <div
                            class="grid grid-cols-2 gap-4 border-t border-gray-100 pt-4 text-xs text-gray-400 font-medium"
                        >
                            <div>
                                <span>Terdaftar Sejak:</span>
                                <p class="text-gray-600 font-semibold mt-0.5">
                                    {{ formatDate(nasabah.created_at) }}
                                </p>
                            </div>
                            <div>
                                <span>Pembaruan Data:</span>
                                <p class="text-gray-600 font-semibold mt-0.5">
                                    {{ formatDate(nasabah.updated_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: RIWAYAT SETOR -->
                <div v-show="activeTab === 'setor'" class="space-y-3">
                    <template
                        v-if="nasabah.deposits && nasabah.deposits.length > 0"
                    >
                        <div
                            v-for="dep in nasabah.deposits"
                            :key="dep.id"
                            class="bg-white p-3.5 rounded-xl border border-gray-100 shadow-2xs"
                        >
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span
                                        class="text-[10px] font-bold text-gray-400 bg-gray-50 px-2 py-0.5 rounded border border-gray-100"
                                        >#DEP-{{ dep.id }}</span
                                    >
                                    <p class="text-xs text-gray-500 mt-1.5">
                                        {{ formatDate(dep.created_at) }}
                                    </p>
                                </div>
                                <span
                                    class="text-sm font-black text-green-600 font-mono"
                                    >+ Rp
                                    {{
                                        Number(dep.total_amount).toLocaleString(
                                            "id-ID",
                                        )
                                    }}</span
                                >
                            </div>
                            <!-- Rincian Barang -->
                            <div
                                class="mt-2 pt-2 border-t border-gray-50 flex flex-wrap gap-1"
                            >
                                <span
                                    v-for="det in dep.details"
                                    :key="det.id"
                                    class="text-[10px] bg-green-50 text-green-700 px-1.5 py-0.5 rounded"
                                >
                                    {{ det.waste?.name }} ({{ det.qty }} kg)
                                </span>
                            </div>
                        </div>
                    </template>
                    <div v-else class="text-center py-8">
                        <p class="text-xs text-gray-400">
                            Belum ada riwayat setoran sampah.
                        </p>
                    </div>
                </div>

                <!-- TAB 3: RIWAYAT TARIK -->
                <div v-show="activeTab === 'tarik'" class="space-y-3">
                    <template
                        v-if="
                            nasabah.withdrawals &&
                            nasabah.withdrawals.length > 0
                        "
                    >
                        <div
                            v-for="wit in nasabah.withdrawals"
                            :key="wit.id"
                            class="bg-white p-3.5 rounded-xl border border-gray-100 shadow-2xs flex justify-between items-center"
                        >
                            <div>
                                <span
                                    class="text-[10px] font-bold text-gray-400 bg-gray-50 px-2 py-0.5 rounded border border-gray-100"
                                    >#WIT-{{ wit.id }}</span
                                >
                                <p class="text-xs text-gray-500 mt-1.5">
                                    {{ formatDate(wit.created_at) }}
                                </p>
                            </div>
                            <span
                                class="text-sm font-black text-amber-600 font-mono"
                                >- Rp
                                {{
                                    Number(wit.amount).toLocaleString("id-ID")
                                }}</span
                            >
                        </div>
                    </template>
                    <div v-else class="text-center py-8">
                        <p class="text-xs text-gray-400">
                            Belum ada riwayat penarikan dana.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tombol Tutup Footer -->
            <div
                class="flex items-center justify-end px-6 py-4 border-t border-gray-50 bg-white flex-shrink-0"
            >
                <button
                    type="button"
                    @click="$emit('close')"
                    class="px-5 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors cursor-pointer"
                >
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>
</template>
