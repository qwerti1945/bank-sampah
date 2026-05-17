<script setup>
defineProps({
    show: Boolean,
    nasabah: Object,
});

defineEmits(["close"]);

// Fungsi format tanggal bawaan JS
const formatDate = (dateString) => {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};
</script>

<template>
    <div
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
    >
        <!-- Backdrop -->
        <div
            class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity"
            @click="$emit('close')"
        ></div>

        <!-- Box Modal -->
        <div
            class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden max-w-md w-full relative z-10 transition-all transform animate-in fade-in zoom-in-95 duration-200"
        >
            <!-- Header Modal -->
            <div
                class="px-6 py-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50"
            >
                <h3 class="text-base font-bold text-gray-800">
                    Detail Informasi Nasabah
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

            <!-- Konten Data -->
            <div class="p-6 space-y-6">
                <!-- Avatar & Saldo Utama -->
                <div
                    class="flex items-center gap-4 bg-green-50/50 p-4 rounded-xl border border-green-100/50"
                >
                    <div
                        class="w-12 h-12 rounded-full bg-green-500 text-white flex items-center justify-center text-lg font-bold shadow-xs"
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
                                Number(nasabah.balance).toLocaleString("id-ID")
                            }}
                        </p>
                    </div>
                </div>

                <!-- Detail List -->
                <div class="space-y-4 text-sm">
                    <div>
                        <span
                            class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1"
                            >Nama Lengkap</span
                        >
                        <p
                            class="text-gray-800 font-medium bg-gray-50 px-3 py-2 rounded-lg border border-gray-100"
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
                            class="text-gray-800 font-medium bg-gray-50 px-3 py-2 rounded-lg border border-gray-100"
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
                            class="text-gray-800 font-medium bg-gray-50 px-3 py-2 rounded-lg border border-gray-100"
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
                            class="text-gray-800 font-medium bg-gray-50 px-3 py-2 rounded-lg border border-gray-100 whitespace-pre-line leading-relaxed"
                        >
                            {{ nasabah.address || "-" }}
                        </p>
                    </div>

                    <div
                        class="grid grid-cols-2 gap-4 border-t border-gray-50 pt-4 text-xs text-gray-400 font-medium"
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

                <!-- Tombol Tutup -->
                <div
                    class="flex items-center justify-end border-t border-gray-50 pt-4"
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
    </div>
</template>
