<script setup>
import { ref } from "vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Sidebar from "@/Components/Sidebar.vue";

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="flex h-screen bg-[#F8FAFC] font-sans">
        <!-- Panggil Sidebar dan hubungkan statenya -->
        <Sidebar
            :showMobileMenu="showingNavigationDropdown"
            @closeMobile="showingNavigationDropdown = false"
        />

        <!-- HAPUS "overflow-hidden" dari div ini -->
        <div class="flex-1 flex flex-col relative w-full">
            <header
                class="flex items-center justify-between h-20 px-4 md:px-8 bg-[#F8FAFC] relative z-40 border-b border-gray-100/50"
            >
                <div class="flex items-center">
                    <!-- Tombol ini sekarang memicu nilai showingNavigationDropdown = true -->
                    <button
                        @click="showingNavigationDropdown = true"
                        class="text-gray-500 bg-white p-2 rounded-lg shadow-sm border border-gray-100 focus:outline-none md:hidden"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>

                    <h2
                        class="text-lg font-semibold text-gray-700 ml-3 md:ml-0 tracking-tight"
                    >
                        <slot name="header" />
                    </h2>
                </div>

                <div class="flex items-center">
                    <!-- Dropdown -->
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                type="button"
                                class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg shadow-sm border border-gray-100 hover:bg-gray-50 transition-all focus:outline-none cursor-pointer"
                            >
                                <span
                                    class="text-[13px] font-medium text-gray-600"
                                    >{{ $page.props.auth.user.name }}</span
                                >
                                <svg
                                    class="fill-current h-3.5 w-3.5 text-gray-400"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink
                                :href="route('profile.edit')"
                                class="text-sm"
                                >Pengaturan Akun</DropdownLink
                            >
                            <DropdownLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="text-sm"
                                >Keluar</DropdownLink
                            >
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Area utama bisa di scroll -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
