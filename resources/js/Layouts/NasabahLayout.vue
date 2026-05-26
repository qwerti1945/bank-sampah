<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <!-- Background abu-abu muda di desktop, tapi konten di tengah tetap putih bersih -->
    <div
        class="min-h-screen bg-gray-100 flex justify-center font-sans text-gray-900 selection:bg-emerald-500 selection:text-white"
    >
        <!-- Container seukuran HP standar -->
        <div
            class="w-full max-w-md bg-white min-h-screen shadow-sm flex flex-col"
        >
            <main class="flex-1 pb-10">
                <slot />
            </main>
        </div>
    </div>
</template>
