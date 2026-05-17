<script setup>
import { useForm } from "@inertiajs/vue3";
defineProps({ show: Boolean });
const emit = defineEmits(["close"]);

const form = useForm({
    name: "",
    email: "",
    password: "",
    role: "admin",
    phone: "",
    address: "",
});
const submit = () => {
    form.post(route("admin.users.store"), { onSuccess: () => emit("close") });
};
</script>
<template>
    <div
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
    >
        <div
            class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs"
            @click="$emit('close')"
        ></div>
        <div
            class="bg-white rounded-xl shadow-xl border max-w-md w-full relative z-10 overflow-hidden animate-in fade-in zoom-in-95 duration-200"
        >
            <div
                class="px-6 py-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50"
            >
                <h3 class="text-base font-bold text-gray-800">
                    Registrasi Staf Pengelola
                </h3>
            </div>
            <form @submit.prevent="submit" class="p-6 space-y-4">
                <div>
                    <label
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1"
                        >Nama Lengkap</label
                    ><input
                        v-model="form.name"
                        type="text"
                        class="w-full text-sm border-gray-200 rounded-lg p-2.5"
                        :class="{ 'border-red-300': form.errors.name }"
                    />
                    <p
                        v-if="form.errors.name"
                        class="text-xs text-red-500 mt-1"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>
                <div>
                    <label
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1"
                        >Alamat Email</label
                    ><input
                        v-model="form.email"
                        type="email"
                        class="w-full text-sm border-gray-200 rounded-lg p-2.5"
                        :class="{ 'border-red-300': form.errors.email }"
                    />
                    <p
                        v-if="form.errors.email"
                        class="text-xs text-red-500 mt-1"
                    >
                        {{ form.errors.email }}
                    </p>
                </div>
                <div>
                    <label
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1"
                        >Kata Sandi</label
                    ><input
                        v-model="form.password"
                        type="password"
                        class="w-full text-sm border-gray-200 rounded-lg p-2.5"
                        :class="{ 'border-red-300': form.errors.password }"
                    />
                    <p
                        v-if="form.errors.password"
                        class="text-xs text-red-500 mt-1"
                    >
                        {{ form.errors.password }}
                    </p>
                </div>
                <div>
                    <label
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1"
                        >Level Otoritas</label
                    ><select
                        v-model="form.role"
                        class="w-full text-sm border-gray-200 rounded-lg p-2.5"
                    >
                        <option value="admin">Staf Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                <div
                    class="flex items-center justify-end gap-2 border-t border-gray-50 pt-4 mt-6"
                >
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border rounded-lg cursor-pointer"
                    >
                        Batal</button
                    ><button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg disabled:opacity-50 cursor-pointer"
                    >
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
