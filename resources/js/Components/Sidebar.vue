<script setup>
import { ref, onMounted, watch } from "vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    showMobileMenu: {
        type: Boolean,
        default: false,
    },
});

defineEmits(["closeMobile"]);

const isCollapsed = ref(false);

// Saat halaman dimuat, cek ingatan browser apakah sebelumnya sidebar dikecilkan
onMounted(() => {
    const savedState = localStorage.getItem("sidebar_collapsed");
    if (savedState !== null) {
        isCollapsed.value = savedState === "true";
    }
});

// Setiap kali ukuran berubah, simpan status terbarunya ke local storage
watch(isCollapsed, (newValue) => {
    localStorage.setItem("sidebar_collapsed", newValue);
});
</script>

<template>
    <!-- Overlay Gelap Khusus Mobile -->
    <div
        v-if="showMobileMenu"
        @click="$emit('closeMobile')"
        class="fixed inset-0 bg-gray-900/40 z-40 md:hidden backdrop-blur-sm transition-opacity"
    ></div>

    <div
        :class="[
            'bg-white border-r border-gray-100 flex flex-col transition-all duration-300 ease-in-out fixed md:relative z-50 h-screen',
            isCollapsed ? 'w-20' : 'w-64',
            showMobileMenu
                ? 'translate-x-0'
                : '-translate-x-full md:translate-x-0',
        ]"
    >
        <!-- Tombol Toggle Collapse (Desktop Only) -->
        <button
            @click="isCollapsed = !isCollapsed"
            class="hidden md:flex absolute -right-3.5 top-7 bg-white border border-gray-200 text-gray-400 hover:text-green-600 rounded-full p-1 shadow-sm transition-transform duration-300 focus:outline-none"
            :class="isCollapsed ? 'rotate-180' : ''"
        >
            <svg
                class="w-3.5 h-3.5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2.5"
                    d="M15 19l-7-7 7-7"
                />
            </svg>
        </button>

        <!-- Logo Area -->
        <div
            class="flex items-center justify-center h-20 border-b border-gray-50 flex-shrink-0"
        >
            <div
                class="flex items-center gap-3 overflow-hidden whitespace-nowrap"
            >
                <div class="bg-green-500 text-white p-1.5 rounded-lg">
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
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                        ></path>
                    </svg>
                </div>
                <span
                    v-show="!isCollapsed"
                    class="text-base font-semibold text-gray-800 tracking-wide transition-opacity duration-300"
                >
                    Bank Sampah
                </span>
            </div>
        </div>

        <!-- Menu Navigasi -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto hide-scrollbar">
            <!-- Dashboard Menu -->
            <Link
                :href="route('admin.dashboard')"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group"
                :class="
                    route().current('admin.dashboard')
                        ? 'bg-green-50 text-green-600'
                        : 'text-gray-600 hover:bg-green-50 hover:text-green-600'
                "
            >
                <svg
                    class="w-4 h-4 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2.5"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                    ></path>
                </svg>
                <span
                    v-show="!isCollapsed"
                    class="ml-3 text-sm font-medium whitespace-nowrap"
                    >Dashboard</span
                >
            </Link>

            <!-- ========================================================================= -->
            <!-- KATEGORI: MASTER DATA -->
            <!-- ========================================================================= -->
            <div
                v-show="!isCollapsed"
                class="text-[11px] font-bold text-gray-400 uppercase tracking-widest pt-5 pb-2 pl-3"
            >
                Master Data
            </div>
            <div v-show="isCollapsed" class="h-4"></div>

            <!-- 1. Data Sampah -->
            <Link
                :href="route('admin.wastes.index')"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group"
                :class="
                    route().current('admin.wastes.*')
                        ? 'bg-green-50 text-green-600'
                        : 'text-gray-600 hover:bg-green-50 hover:text-green-600'
                "
            >
                <svg
                    class="w-4 h-4 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2.5"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                    ></path>
                </svg>
                <span
                    v-show="!isCollapsed"
                    class="ml-3 text-sm font-medium whitespace-nowrap"
                    >Data Sampah</span
                >
            </Link>

            <!-- 2. Data Nasabah -->
            <Link
                :href="route('admin.nasabah.index')"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group"
                :class="
                    route().current('admin.nasabah.*')
                        ? 'bg-green-50 text-green-600'
                        : 'text-gray-600 hover:bg-green-50 hover:text-green-600'
                "
            >
                <svg
                    class="w-4 h-4 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2.5"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                    ></path>
                </svg>
                <span
                    v-show="!isCollapsed"
                    class="ml-3 text-sm font-medium whitespace-nowrap"
                    >Data Nasabah</span
                >
            </Link>

            <!-- 3. Kelola Tim Admin -->
            <Link
                :href="route('admin.users.index')"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group"
                :class="
                    route().current('admin.users.*')
                        ? 'bg-green-50 text-green-600'
                        : 'text-gray-600 hover:bg-green-50 hover:text-green-600'
                "
            >
                <svg
                    class="w-4 h-4 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2.5"
                        d="M9 12a4 4 0 100-8 4 4 0 000 8zm5.586 6.586a1 1 0 011.414 0L22 24H12l2.586-5.414zM2 22a8 8 0 0110.46-7.53"
                    ></path>
                </svg>
                <span
                    v-show="!isCollapsed"
                    class="ml-3 text-sm font-medium whitespace-nowrap"
                    >Data User Admin</span
                >
            </Link>

            <!-- 4. Stok Gudang (MENU BARU) -->
            <Link
                :href="route('admin.gudang.index')"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group"
                :class="
                    route().current('admin.gudang.*')
                        ? 'bg-indigo-50 text-indigo-600'
                        : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600'
                "
            >
                <svg
                    class="w-4 h-4 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                    ></path>
                </svg>
                <span
                    v-show="!isCollapsed"
                    class="ml-3 text-sm font-medium whitespace-nowrap"
                    >Stok Gudang</span
                >
            </Link>

            <!-- KATEGORI: TRANSAKSI -->
            <div
                v-show="!isCollapsed"
                class="text-[11px] font-bold text-gray-400 uppercase tracking-widest pt-5 pb-2 pl-3"
            >
                Transaksi
            </div>
            <div v-show="isCollapsed" class="h-4"></div>

            <!-- Setor Sampah -->
            <Link
                :href="route('admin.deposits.index')"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group"
                :class="
                    route().current('admin.deposits.*')
                        ? 'bg-green-50 text-green-600'
                        : 'text-gray-600 hover:bg-green-50 hover:text-green-600'
                "
            >
                <svg
                    class="w-4 h-4 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2.5"
                        d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"
                    ></path>
                </svg>
                <span
                    v-show="!isCollapsed"
                    class="ml-3 text-sm font-medium whitespace-nowrap"
                    >Setor Sampah</span
                >
            </Link>

            <!-- Tarik Saldo -->
            <Link
                :href="route('admin.withdrawals.index')"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group"
                :class="
                    route().current('admin.withdrawals.*')
                        ? 'bg-green-50 text-green-600'
                        : 'text-gray-600 hover:bg-green-50 hover:text-green-600'
                "
            >
                <svg
                    class="w-4 h-4 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2.5"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                </svg>
                <span
                    v-show="!isCollapsed"
                    class="ml-3 text-sm font-medium whitespace-nowrap"
                    >Tarik Saldo</span
                >
            </Link>

            <!-- Jual ke Pengepul -->
            <Link
                :href="route('admin.collector-sales.index')"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group"
                :class="
                    route().current('admin.collector-sales.*')
                        ? 'bg-green-50 text-green-600'
                        : 'text-gray-600 hover:bg-green-50 hover:text-green-600'
                "
            >
                <svg
                    class="w-4 h-4 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2.5"
                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"
                    ></path>
                </svg>
                <span
                    v-show="!isCollapsed"
                    class="ml-3 text-sm font-medium whitespace-nowrap"
                    >Jual ke Pengepul</span
                >
            </Link>

            <!-- KATEGORI: KELOLA WEBSITE -->
            <div
                v-show="!isCollapsed"
                class="text-[11px] font-bold text-gray-400 uppercase tracking-widest pt-5 pb-2 pl-3"
            >
                Kelola Website
            </div>
            <div v-show="isCollapsed" class="h-4"></div>

            <!-- Berita & Artikel -->
            <Link
                href="#"
                class="flex items-center px-3 py-2.5 text-gray-600 hover:bg-green-50 hover:text-green-600 rounded-lg transition-all duration-200 group"
            >
                <svg
                    class="w-4 h-4 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2.5"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"
                    ></path>
                </svg>
                <span
                    v-show="!isCollapsed"
                    class="ml-3 text-sm font-medium whitespace-nowrap"
                    >Berita & Artikel</span
                >
            </Link>

            <!-- Halaman Dinamis -->
            <Link
                href="#"
                class="flex items-center px-3 py-2.5 text-gray-600 hover:bg-green-50 hover:text-green-600 rounded-lg transition-all duration-200 group"
            >
                <svg
                    class="w-4 h-4 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2.5"
                        d="M4 6a2 2 0 012-2h8.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V18a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"
                    ></path>
                </svg>
                <span
                    v-show="!isCollapsed"
                    class="ml-3 text-sm font-medium whitespace-nowrap"
                    >Halaman Dinamis</span
                >
            </Link>

            <!-- Pengaturan Web -->
            <Link
                href="#"
                class="flex items-center px-3 py-2.5 text-gray-600 hover:bg-green-50 hover:text-green-600 rounded-lg transition-all duration-200 group"
            >
                <svg
                    class="w-4 h-4 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2.5"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                    ></path>
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2.5"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                    ></path>
                </svg>
                <span
                    v-show="!isCollapsed"
                    class="ml-3 text-sm font-medium whitespace-nowrap"
                    >Pengaturan Web</span
                >
            </Link>
        </nav>

        <!-- User Profile Minimalis Area Bawah -->
        <div class="p-4 border-t border-gray-50 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-sm font-bold flex-shrink-0"
                >
                    {{ $page.props.auth.user.name.charAt(0) }}
                </div>
                <div v-show="!isCollapsed" class="overflow-hidden">
                    <p class="text-[13px] font-medium text-gray-700 truncate">
                        {{ $page.props.auth.user.name }}
                    </p>
                    <p class="text-[11px] text-gray-400 truncate capitalize">
                        {{ $page.props.auth.user.role }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
