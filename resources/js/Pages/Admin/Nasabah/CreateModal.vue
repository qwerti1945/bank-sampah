<script setup>
import { useForm } from "@inertiajs/vue3";

defineProps({ show: Boolean });
const emit = defineEmits(["close"]);

const form = useForm({
    name: "",
    email: "",
    password: "",
    phone: "",
    address: "",
});

const submit = () => {
    form.post(route("admin.nasabah.store"), {
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
                    Registrasi Nasabah Baru
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
                        >Nama Lengkap</label
                    >
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="Masukkan nama nasabah"
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
                        placeholder="contoh@email.com"
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
                        >Kata Sandi Akun</label
                    >
                    <input
                        v-model="form.password"
                        type="password"
                        placeholder="Minimal 6 karakter"
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
                        >Nomor Telepon / WA</label
                    >
                    <input
                        v-model="form.phone"
                        type="text"
                        placeholder="Contoh: 0812345678"
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
                        >Alamat Lengkap Rumah</label
                    >
                    <textarea
                        v-model="form.address"
                        rows="2"
                        placeholder="Tuliskan alamat domisili lengkap..."
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
                        class="px-4 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-lg shadow-sm disabled:opacity-50 cursor-pointer"
                    >
                        {{
                            form.processing
                                ? "Mendaftarkan..."
                                : "Daftarkan Nasabah"
                        }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
