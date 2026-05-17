<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    show: Boolean,
    nasabah: Object,
});

const emit = defineEmits(["close"]);

const form = useForm({
    name: props.nasabah.name,
    email: props.nasabah.email,
    password: "", // dikosongkan kecuali ingin mengganti sandi baru
    phone: props.nasabah.phone,
    address: props.nasabah.address,
});

const submit = () => {
    form.put(route("admin.nasabah.update", props.nasabah.id), {
        onSuccess: () => emit("close"),
    });
};
</script>

<template>
    <div
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
    >
        <div
            class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity"
            @click="$emit('close')"
        ></div>

        <div
            class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden max-w-md w-full relative z-10 transition-all transform animate-in fade-in zoom-in-95 duration-200"
        >
            <div
                class="px-6 py-4 border-b border-gray-50 flex justify-between items-center"
            >
                <h3 class="text-base font-bold text-gray-800">
                    Ubah Profil Nasabah
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

            <form @submit.prevent="submit" class="p-6 space-y-4">
                <div>
                    <label
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5"
                        >Nama Nasabah</label
                    >
                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full text-sm border-gray-200 focus:border-green-500 focus:ring-green-500/10 rounded-lg p-2.5"
                        :class="{ 'border-red-300': form.errors.name }"
                    />
                    <p
                        v-if="form.errors.name"
                        class="mt-1 text-xs text-red-500 font-medium"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5"
                        >Alamat Email</label
                    >
                    <input
                        v-model="form.email"
                        type="email"
                        class="w-full text-sm border-gray-200 focus:border-green-500 focus:ring-green-500/10 rounded-lg p-2.5"
                        :class="{ 'border-red-300': form.errors.email }"
                    />
                    <p
                        v-if="form.errors.email"
                        class="mt-1 text-xs text-red-500 font-medium"
                    >
                        {{ form.errors.email }}
                    </p>
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5"
                        >Ganti Password Baru (Opsional)</label
                    >
                    <input
                        v-model="form.password"
                        type="password"
                        placeholder="Biarkan kosong jika tidak ingin diubah"
                        class="w-full text-sm border-gray-200 focus:border-green-500 focus:ring-green-500/10 rounded-lg p-2.5"
                        :class="{ 'border-red-300': form.errors.password }"
                    />
                    <p
                        v-if="form.errors.password"
                        class="mt-1 text-xs text-red-500 font-medium"
                    >
                        {{ form.errors.password }}
                    </p>
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5"
                        >Nomor Telepon</label
                    >
                    <input
                        v-model="form.phone"
                        type="text"
                        class="w-full text-sm border-gray-200 focus:border-green-500 focus:ring-green-500/10 rounded-lg p-2.5"
                        :class="{ 'border-red-300': form.errors.phone }"
                    />
                    <p
                        v-if="form.errors.phone"
                        class="mt-1 text-xs text-red-500 font-medium"
                    >
                        {{ form.errors.phone }}
                    </p>
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5"
                        >Alamat Tinggal</label
                    >
                    <textarea
                        v-model="form.address"
                        rows="2"
                        class="w-full text-sm border-gray-200 focus:border-green-500 focus:ring-green-500/10 rounded-lg p-2.5"
                        :class="{ 'border-red-300': form.errors.address }"
                    ></textarea>
                    <p
                        v-if="form.errors.address"
                        class="mt-1 text-xs text-red-500 font-medium"
                    >
                        {{ form.errors.address }}
                    </p>
                </div>

                <div
                    class="flex items-center justify-end gap-2 border-t border-gray-50 pt-4 mt-6"
                >
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg border border-gray-200 cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm disabled:opacity-50 cursor-pointer"
                    >
                        {{
                            form.processing
                                ? "Memperbarui..."
                                : "Simpan Perubahan"
                        }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
