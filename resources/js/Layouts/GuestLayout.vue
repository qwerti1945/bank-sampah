<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-b from-green-50/60 to-gray-100 p-4 sm:p-6"
    >
        <div class="mb-5 animate-in fade-in slide-in-from-top-4 duration-300">
            <Link
                href="/"
                class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl text-green-600 shadow-md border border-gray-100/70 hover:scale-105 transition-transform"
                title="Kembali ke Beranda"
            >
                <svg
                    class="w-9 h-9"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path
                        d="M2 22c1.25-3.12 4.17-5.42 7-6.5 3-1.1 6.5-.5 9-2.5 2.5-2 3.5-5.5 3.5-9 0 0-3.5 1-5.5 3.5-2 2.5-1.4 6-2.5 9-1.08 2.83-3.38 5.75-6.5 7z"
                    ></path>
                    <path d="M12 12L2 22"></path>
                </svg>
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md bg-white px-5 py-6 sm:px-8 sm:py-8 shadow-xl rounded-2xl border border-gray-100 animate-in fade-in zoom-in-95 duration-200"
        >
            <slot />
        </div>
    </div>
</template>
