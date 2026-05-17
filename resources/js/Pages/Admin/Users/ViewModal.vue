<script setup>
defineProps({ show: Boolean, user: Object });
defineEmits(["close"]);
</script>
<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div
            class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs"
            @click="$emit('close')"
        ></div>
        <div
            class="bg-white rounded-xl shadow-xl border max-w-sm w-full relative z-10 overflow-hidden p-6 space-y-4"
        >
            <div
                class="flex items-center gap-3 bg-gray-50 p-4 rounded-xl border border-gray-100"
            >
                <div
                    class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center font-bold text-base uppercase"
                >
                    {{ user.name.charAt(0) }}
                </div>
                <div>
                    <p class="font-bold text-gray-800 text-sm">
                        {{ user.name }}
                    </p>
                    <p
                        class="text-[10px] uppercase font-mono tracking-wider font-bold text-green-600 mt-0.5"
                    >
                        {{ user.role.replace("_", " ") }}
                    </p>
                </div>
            </div>
            <div class="space-y-3 text-xs text-gray-600">
                <div>
                    <span class="text-gray-400 font-medium"
                        >Alamat Email Resmi:</span
                    >
                    <p
                        class="font-mono text-gray-800 mt-0.5 bg-gray-50 px-2.5 py-1.5 rounded border border-gray-100"
                    >
                        {{ user.email }}
                    </p>
                </div>
                <div>
                    <span class="text-gray-400 font-medium"
                        >No. Telepon Kontak:</span
                    >
                    <p class="text-gray-800 mt-0.5 font-medium">
                        {{ user.phone || "Belum diatur" }}
                    </p>
                </div>
                <div>
                    <span class="text-gray-400 font-medium">Alamat Rumah:</span>
                    <p
                        class="text-gray-800 mt-0.5 leading-relaxed bg-gray-50 p-2 rounded border border-gray-100"
                    >
                        {{ user.address || "Belum diatur" }}
                    </p>
                </div>
            </div>
            <div class="flex justify-end border-t border-gray-50 pt-3">
                <button
                    type="button"
                    @click="$emit('close')"
                    class="px-4 py-2 bg-gray-100 text-gray-600 font-semibold text-xs rounded-lg hover:bg-gray-200 cursor-pointer"
                >
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>
</template>
