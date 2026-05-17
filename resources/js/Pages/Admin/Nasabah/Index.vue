<script setup>
import { ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import CreateModal from "./CreateModal.vue";
import EditModal from "./EditModal.vue";
import ViewModal from "./ViewModal.vue";

const props = defineProps({
    nasabah: Object,
    filters: Object,
});

// State Manajemen Form Modal SPA
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const selectedNasabah = ref(null);

// =========================================================================
// STATE & LOGIKA REAKTIF PURE MODAL KONFIRMASI HAPUS (MENGGANTIKAN WINDOW.CONFIRM)
// =========================================================================
const showConfirmModal = ref(false);
const confirmTitle = ref("");
const confirmMessage = ref("");
const confirmAction = ref(null); // Menyimpan fungsi yang akan dieksekusi jika disetujui

// Pemicu Modal Konfirmasi Hapus Nasabah
const triggerDeleteConfirm = (item) => {
    selectedNasabah.value = item;
    confirmTitle.value = "Hapus Akun Nasabah";
    confirmMessage.value = `Apakah Anda yakin ingin menghapus akun nasabah "${item.name}"? Seluruh saldo riwayat transaksi akan ikut terhapus permanen dari sistem.`;

    confirmAction.value = () => {
        router.delete(route("admin.nasabah.destroy", item.id), {
            preserveScroll: true,
        });
        showConfirmModal.value = false;
    };
    showConfirmModal.value = true;
};

const openEditModal = (item) => {
    selectedNasabah.value = item;
    showEditModal.value = true;
};

const openViewModal = (item) => {
    selectedNasabah.value = item;
    showViewModal.value = true;
};

// Fitur Pencarian Debounce
const search = ref(props.filters?.search || "");
let searchTimer = null;

watch(search, (newValue) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(
            route("admin.nasabah.index"),
            { search: newValue },
            { preserveState: true, replace: true },
        );
    }, 400);
});

// Logika Toast Notification SPA
const isToastVisible = ref(false);
const toastMessage = ref("");

router.on("success", (event) => {
    const message = event.detail.page.props.flash?.success;
    if (message) {
        toastMessage.value = message;
        isToastVisible.value = true;
        setTimeout(() => {
            isToastVisible.value = false;
        }, 3000);
    }
});
</script>

<template>
    <Head title="Data Nasabah" />

    <AuthenticatedLayout>
        <template #header> Manajemen Data Nasabah </template>

        <!-- Floating Toast Notification -->
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

        <div
            class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100"
        >
            <div class="p-4 sm:p-6">
                <!-- Header Daftar -->
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6"
                >
                    <div>
                        <h3
                            class="text-base sm:text-lg font-bold text-gray-800"
                        >
                            Daftar Nasabah Bank Sampah
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Kelola data keanggotaan nasabah, peninjauan kontak,
                            serta akumulasi tabungan.
                        </p>
                    </div>

                    <button
                        @click="showCreateModal = true"
                        class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2 shadow-sm cursor-pointer"
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
                        Registrasi Nasabah
                    </button>
                </div>

                <!-- Bar Input Search -->
                <div class="mb-5 max-w-md">
                    <div class="relative rounded-lg shadow-2xs">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <svg
                                class="h-4 w-4 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </div>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari nama, email, atau no. telepon nasabah..."
                            class="block w-full rounded-lg border-gray-200 pl-9 text-xs sm:text-sm focus:border-green-500 focus:ring-green-500/10 p-2.5 transition-all"
                        />
                    </div>
                </div>

                <!-- Responsif Layout Table -->
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
                                        Nama Nasabah
                                    </th>
                                    <th class="p-4 font-semibold">
                                        Kontak & Alamat
                                    </th>
                                    <th class="p-4 font-semibold">
                                        Saldo Tabungan
                                    </th>
                                    <th
                                        class="p-4 font-semibold text-center w-32"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-50">
                                <tr
                                    v-for="(item, index) in nasabah.data"
                                    :key="item.id"
                                    class="hover:bg-gray-50/50 transition-colors whitespace-nowrap"
                                >
                                    <td class="p-4 text-gray-500 text-center">
                                        {{ nasabah.from + index }}
                                    </td>
                                    <td
                                        class="p-4 font-medium text-gray-800 whitespace-normal min-w-[140px]"
                                    >
                                        {{ item.name }}
                                    </td>
                                    <td
                                        class="p-4 text-gray-600 whitespace-normal min-w-[200px]"
                                    >
                                        <p
                                            class="font-medium text-gray-700 text-xs"
                                        >
                                            {{ item.email }}
                                        </p>
                                        <p
                                            class="text-gray-400 text-[11px] mt-0.5"
                                        >
                                            {{ item.phone }}
                                        </p>
                                    </td>
                                    <td
                                        class="p-4 font-semibold text-green-600"
                                    >
                                        Rp
                                        {{
                                            Number(item.balance).toLocaleString(
                                                "id-ID",
                                            )
                                        }}
                                    </td>
                                    <td class="p-4">
                                        <div
                                            class="flex items-center justify-center gap-0.5"
                                        >
                                            <!-- Tombol Detail -->
                                            <button
                                                @click="openViewModal(item)"
                                                class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all cursor-pointer"
                                                title="Lihat Detail Lengkap"
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
                                                        d="M2.036 12.322a1.012 1.012 0 010-.644M7.543 6.932a10.116 10.116 0 0110.914 0M21.964 11.678a1.012 1.012 0 010 .644M16.457 17.068a10.114 10.114 0 01-10.914 0M12 15a3 3 0 100-6 3 3 0 000 6z"
                                                    />
                                                </svg>
                                            </button>

                                            <!-- Tombol Edit -->
                                            <button
                                                @click="openEditModal(item)"
                                                class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all cursor-pointer"
                                                title="Edit Profil"
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
                                            <button
                                                @click="
                                                    triggerDeleteConfirm(item)
                                                "
                                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all cursor-pointer"
                                                title="Hapus Akun"
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
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="nasabah.data.length === 0">
                                    <td
                                        colspan="5"
                                        class="p-12 text-center text-gray-400"
                                    >
                                        Tidak ditemukan hasil data nasabah yang
                                        cocok.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination Links -->
                <div
                    v-if="nasabah.links.length > 3"
                    class="mt-6 flex flex-wrap justify-center gap-1 border-t border-gray-50 pt-4"
                >
                    <template v-for="(link, k) in nasabah.links" :key="k">
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
                                    ? 'bg-green-600 text-white border-green-600 font-semibold'
                                    : 'text-gray-600 bg-white border-gray-200 hover:bg-gray-50'
                            "
                            :href="link.url"
                            v-html="link.label"
                        ></Link>
                    </template>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- LAYER MODAL SPA: GLOBAL DELETION CONFIRMATION MODAL (PURE DANGER SHIELD) -->
        <!-- ========================================================================= -->
        <div
            v-if="showConfirmModal"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        >
            <div
                class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity"
                @click="showConfirmModal = false"
            ></div>

            <div
                class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden max-w-sm w-full relative z-10 transition-all transform animate-in fade-in zoom-in-95 duration-200"
            >
                <div class="p-6 text-center">
                    <div
                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-full mb-4 bg-red-50 text-red-600"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.74c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"
                            />
                        </svg>
                    </div>

                    <h3 class="text-base font-bold text-gray-900 mb-2">
                        {{ confirmTitle }}
                    </h3>
                    <p class="text-xs text-gray-500 leading-relaxed px-2">
                        {{ confirmMessage }}
                    </p>
                </div>

                <!-- Footer Tombol Pilihan Konfirmasi -->
                <div
                    class="bg-gray-50 px-6 py-3 flex items-center justify-end gap-2 border-t border-gray-100"
                >
                    <button
                        type="button"
                        @click="showConfirmModal = false"
                        class="px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100 bg-white border border-gray-200 rounded-lg cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="confirmAction"
                        class="px-4 py-2 text-xs font-semibold text-white rounded-lg cursor-pointer shadow-sm bg-red-600 hover:bg-red-700"
                    >
                        Ya, Hapus Akun
                    </button>
                </div>
            </div>
        </div>

        <!-- Render Form Modal Component -->
        <CreateModal
            v-if="showCreateModal"
            :show="showCreateModal"
            @close="showCreateModal = false"
        />
        <EditModal
            v-if="showEditModal"
            :show="showEditModal"
            :nasabah="selectedNasabah"
            @close="showEditModal = false"
        />
        <ViewModal
            v-if="showViewModal"
            :show="showViewModal"
            :nasabah="selectedNasabah"
            @close="showViewModal = false"
        />
    </AuthenticatedLayout>
</template>
