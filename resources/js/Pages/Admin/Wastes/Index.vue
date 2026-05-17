<script setup>
import { ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import CreateModal from "./CreateModal.vue";
import EditModal from "./EditModal.vue";

defineProps({
    wastes: Object,
});

// State untuk manajemen Modal SPA
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedWaste = ref(null);

const openEditModal = (waste) => {
    selectedWaste.value = null; // reset cache data lama
    selectedWaste.value = waste;
    showEditModal.value = true;
};

// ==========================================
// LOGIKA REAKTIF FLOATING TOAST NOTIFICATION
// ==========================================
const page = usePage();
const isToastVisible = ref(false);
const toastMessage = ref("");

// Pantau setiap ada kiriman session 'success' baru dari Laravel
watch(
    () => page.props.flash?.success,
    (newMessage) => {
        if (newMessage) {
            toastMessage.value = newMessage;
            isToastVisible.value = true;

            // Toast otomatis menghilang setelah 3 detik
            setTimeout(() => {
                isToastVisible.value = false;
            }, 3000);
        }
    },
    { immediate: true },
);
</script>

<template>
    <Head title="Data Sampah" />

    <AuthenticatedLayout>
        <template #header> Data Master Sampah </template>

        <!-- ========================================================================= -->
        <!-- ELEMEN INTERAKTIF FLOATING TOAST (Melayang di Pojok Kanan Atas) -->
        <!-- ========================================================================= -->
        <div
            class="fixed top-6 right-6 z-[99] pointer-events-none space-y-3 max-w-sm w-full px-4 sm:px-0"
        >
            <Transition
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="isToastVisible"
                    class="bg-white border border-gray-100 p-4 rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.08)] pointer-events-auto flex items-start gap-3"
                >
                    <div
                        class="bg-green-50 text-green-500 p-1.5 rounded-lg flex-shrink-0"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            ></path>
                        </svg>
                    </div>
                    <div class="flex-1 pt-0.5">
                        <p class="text-xs font-bold text-gray-800">
                            Aksi Berhasil
                        </p>
                        <p
                            class="text-[11px] text-gray-500 mt-0.5 leading-relaxed"
                        >
                            {{ toastMessage }}
                        </p>
                    </div>
                    <button
                        @click="isToastVisible = false"
                        class="text-gray-400 hover:text-gray-500 cursor-pointer"
                    >
                        <svg
                            class="w-3.5 h-3.5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12"
                            ></path>
                        </svg>
                    </button>
                </div>
            </Transition>
        </div>

        <!-- Konten Utama Card Tabel -->
        <div
            class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100"
        >
            <div class="p-4 sm:p-6">
                <!-- Header Tabel -->
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6"
                >
                    <div>
                        <h3
                            class="text-base sm:text-lg font-bold text-gray-800"
                        >
                            Daftar Harga Sampah
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Kelola acuan kategori sampah dan harga beli dari
                            nasabah.
                        </p>
                    </div>

                    <button
                        @click="showCreateModal = true"
                        class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2 shadow-sm shadow-green-100 cursor-pointer"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v16m8-8H4"
                            ></path>
                        </svg>
                        Tambah Data
                    </button>
                </div>

                <!-- Pembungkus Tabel Responsif Mobile-Friendly -->
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <div
                        class="inline-block min-w-full align-middle px-4 sm:px-0"
                    >
                        <table class="min-w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider whitespace-nowrap"
                                >
                                    <th
                                        class="p-4 font-semibold w-16 text-center"
                                    >
                                        No
                                    </th>
                                    <th class="p-4 font-semibold">
                                        Nama Sampah
                                    </th>
                                    <th class="p-4 font-semibold w-24">
                                        Satuan
                                    </th>
                                    <th class="p-4 font-semibold">
                                        Harga Saat Ini
                                    </th>
                                    <th
                                        class="p-4 font-semibold text-center w-28"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-50">
                                <tr
                                    v-for="(waste, index) in wastes.data"
                                    :key="waste.id"
                                    class="hover:bg-gray-50/50 transition-colors whitespace-nowrap"
                                >
                                    <td class="p-4 text-gray-500 text-center">
                                        {{ wastes.from + index }}
                                    </td>
                                    <td
                                        class="p-4 font-medium text-gray-800 whitespace-normal min-w-[180px] sm:min-w-[240px]"
                                    >
                                        {{ waste.name }}
                                    </td>
                                    <td class="p-4 text-gray-600">
                                        <span
                                            class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded-md text-xs uppercase font-medium tracking-wide"
                                        >
                                            {{ waste.unit }}
                                        </span>
                                    </td>
                                    <td
                                        class="p-4 font-semibold text-green-600"
                                    >
                                        Rp
                                        {{
                                            Number(
                                                waste.current_price,
                                            ).toLocaleString("id-ID")
                                        }}
                                    </td>
                                    <td class="p-4">
                                        <!-- Aksi Tombol Menggunakan Icon Estetik -->
                                        <div
                                            class="flex items-center justify-center gap-1"
                                        >
                                            <!-- Tombol Edit -->
                                            <button
                                                @click="openEditModal(waste)"
                                                class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all cursor-pointer"
                                                title="Edit Kategori"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"
                                                    />
                                                </svg>
                                            </button>

                                            <!-- Tombol Hapus -->
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.wastes.destroy',
                                                        waste.id,
                                                    )
                                                "
                                                method="delete"
                                                as="button"
                                                preserve-scroll
                                                class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all cursor-pointer"
                                                title="Hapus Kategori"
                                                onbefore="return confirm('Apakah Anda yakin ingin menghapus data sampah ini?')"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                                                    />
                                                </svg>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="wastes.data.length === 0">
                                    <td
                                        colspan="5"
                                        class="p-12 text-center text-gray-400"
                                    >
                                        Belum ada data sampah.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Paginasi -->
                <div
                    v-if="wastes.links.length > 3"
                    class="mt-6 flex flex-wrap justify-center gap-1 border-t border-gray-50 pt-4"
                >
                    <template v-for="(link, k) in wastes.links" :key="k">
                        <div
                            v-if="link.url === null"
                            class="mr-1 mb-1 px-3 py-1.5 text-xs text-gray-400 border border-gray-100 rounded-lg select-none"
                            v-html="link.label"
                        ></div>
                        <Link
                            v-else
                            class="mr-1 mb-1 px-3 py-1.5 text-xs border rounded-lg transition-all"
                            :class="
                                link.active
                                    ? 'bg-green-600 text-white border-green-600 font-semibold shadow-xs'
                                    : 'text-gray-600 bg-white border-gray-200 hover:bg-gray-50'
                            "
                            :href="link.url"
                            v-html="link.label"
                        ></Link>
                    </template>
                </div>
            </div>
        </div>

        <!-- Pemanggilan Sub-Komponen Form Modal SPA -->
        <CreateModal
            v-if="showCreateModal"
            :show="showCreateModal"
            @close="showCreateModal = false"
        />
        <EditModal
            v-if="showEditModal"
            :show="showEditModal"
            :waste="selectedWaste"
            @close="showEditModal = false"
        />
    </AuthenticatedLayout>
</template>
