<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Masuk ke Sistem" />

    <div
        class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden font-sans"
    >
        <div
            class="absolute top-0 left-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"
        ></div>
        <div
            class="absolute bottom-0 right-1/4 w-96 h-96 bg-green-600/5 rounded-full blur-3xl pointer-events-none"
        ></div>

        <div class="sm:mx-auto w-full max-w-md z-10 px-4">
            <div class="text-center mb-6">
                <span
                    class="text-3xl sm:text-4xl block group-hover:scale-105 transition-transform duration-300"
                    >🌿</span
                >
                <h2
                    class="mt-3 text-xl sm:text-2xl font-black text-slate-900 tracking-tight"
                >
                    Ekosistem Bank Sampah
                </h2>
                <p class="mt-1.5 text-xs sm:text-sm text-slate-500 font-medium">
                    Silakan masuk untuk mengelola tabungan & logistik gudang
                </p>
            </div>

            <div
                class="bg-white py-6 px-4 sm:p-8 rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/50"
            >
                <div
                    v-if="status"
                    class="mb-4 font-semibold text-xs text-green-600 bg-green-50 p-3 rounded-xl border border-green-100"
                >
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-4.5">
                    <div>
                        <label
                            for="email"
                            class="block text-xs font-black text-slate-700 uppercase tracking-wider"
                        >
                            Alamat Email
                        </label>
                        <div class="mt-1.5 relative rounded-xl shadow-3xs">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm"
                            >
                                ✉️
                            </div>
                            <input
                                id="email"
                                type="email"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="nama@email.com"
                                class="block w-full pl-10 pr-3 py-3 text-sm border-slate-200 rounded-xl bg-slate-50/50 font-medium focus:bg-white focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-slate-400"
                                :class="{
                                    'border-red-300 bg-red-50/10 focus:border-red-500 focus:ring-red-500/10':
                                        form.errors.email,
                                }"
                            />
                        </div>
                        <p
                            v-if="form.errors.email"
                            class="mt-1.5 text-xs text-red-600 font-semibold flex items-center gap-1 animate-in fade-in slide-in-from-top-1 duration-200"
                        >
                            ⚠️ {{ form.errors.email }}
                        </p>
                    </div>

                    <div>
                        <div class="flex justify-between items-center">
                            <label
                                for="password"
                                class="block text-xs font-black text-slate-700 uppercase tracking-wider"
                            >
                                Kata Sandi
                            </label>
                        </div>
                        <div class="mt-1.5 relative rounded-xl shadow-3xs">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm"
                            >
                                🔒
                            </div>
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="block w-full pl-10 pr-10 py-3 text-sm border-slate-200 rounded-xl bg-slate-50/50 font-medium focus:bg-white focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-slate-400"
                                :class="{
                                    'border-red-300 bg-red-50/10 focus:border-red-500 focus:ring-red-500/10':
                                        form.errors.password,
                                }"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors cursor-pointer text-xs font-bold"
                            >
                                {{ showPassword ? "Sembunyi" : "Lihat" }}
                            </button>
                        </div>
                        <p
                            v-if="form.errors.password"
                            class="mt-1.5 text-xs text-red-600 font-semibold flex items-center gap-1 animate-in fade-in slide-in-from-top-1 duration-200"
                        >
                            ⚠️ {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between pt-0.5">
                        <div class="flex items-center">
                            <input
                                id="remember_me"
                                type="checkbox"
                                v-model="form.remember"
                                class="h-4 w-4 text-emerald-600 focus:ring-emerald-500/20 border-slate-300 rounded-md cursor-pointer shadow-3xs"
                            />
                            <label
                                for="remember_me"
                                class="ml-2 block text-xs sm:text-sm text-slate-600 font-medium cursor-pointer select-none"
                            >
                                Tetap masuk di perangkat ini
                            </label>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all cursor-pointer items-center gap-2"
                        >
                            <span
                                v-if="form.processing"
                                class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"
                            ></span>
                            {{
                                form.processing
                                    ? "Menautkan Sesi..."
                                    : "Masuk Dashboard"
                            }}
                        </button>
                    </div>
                </form>
            </div>

            <p
                class="mt-8 text-center text-[10px] text-slate-400 font-medium tracking-wide uppercase"
            >
                &copy; {{ new Date().getFullYear() }} Bank Sampah Unit —
                Universitas Mulawarman.
            </p>
        </div>
    </div>
</template>

<style scoped>
/* Ruang spasi internal kustom yang presisi */
.space-y-4\.5 > :not([hidden]) ~ :not([hidden]) {
    --tw-space-y-reverse: 0;
    margin-top: calc(1.125rem * calc(1 - var(--tw-space-y-reverse)));
    margin-bottom: calc(1.125rem * var(--tw-space-y-reverse));
}
</style>
