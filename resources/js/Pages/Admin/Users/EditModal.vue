<script setup>
import { useForm } from "@inertiajs/vue3";
const props = defineProps({ show: Boolean, user: Object });
const emit = defineEmits(["close"]);

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: "",
    role: props.user.role,
    phone: props.user.phone,
    address: props.user.address,
});
const submit = () => {
    form.put(route("admin.users.update", props.user.id), {
        onSuccess: () => emit("close"),
    });
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
                    Ubah Otoritas Pengguna
                </h3>
            </div>
            <form @submit.prevent="submit" class="p-6 space-y-4">
                <div>
                    <label
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1"
                        >Nama Pengguna</label
                    ><input
                        v-model="form.name"
                        type="text"
                        class="w-full text-sm border-gray-200 rounded-lg p-2.5"
                    />
                </div>
                <div>
                    <label
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1"
                        >Email</label
                    ><input
                        v-model="form.email"
                        type="email"
                        class="w-full text-sm border-gray-200 rounded-lg p-2.5"
                    />
                </div>
                <div>
                    <label
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1"
                        >Password Baru (Opsional)</label
                    ><input
                        v-model="form.password"
                        type="password"
                        placeholder="Biarkan kosong jika tetap"
                        class="w-full text-sm border-gray-200 rounded-lg p-2.5"
                    />
                </div>
                <div>
                    <label
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1"
                        >Hak Akses Sistem</label
                    >
                    <select
                        v-model="form.role"
                        class="w-full text-sm border-gray-200 rounded-lg p-2.5"
                    >
                        <option value="admin">Staf Admin</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="nasabah">
                            Turunkan Pangkat: Nasabah Luar
                        </option>
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
                        class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg disabled:opacity-50 cursor-pointer"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
