<template>
    <nav class="bg-white fixed w-full z-30 top-0 left-0 border-b border-gray-200">
        <div :class="['max-w-full flex flex-wrap items-center justify-between mx-auto p-4 pr-6 transition-all duration-300', ui.sidebarOpen ? 'pl-72' : 'pl-6']">

            <button type="button" @click="ui.toggleSidebar()" class="cursor-pointer text-xl">
                <i class="fa-solid fa-bars text-gray-700"></i>
            </button>

            <!-- <form class="flex items-center max-w-sm mx-auto gap-2">
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>

                    <input type="text" id="simple-search"
                        class="w-full px-3 py-2.5 pl-9 text-sm border border-gray-300 rounded-full focus:border-blue-500 focus:outline-none placeholder-gray-400"
                        placeholder="Search something..." />
                </div>
            </form> -->

            <div class="flex items-center gap-3.5">
                <!-- <RouterLink
                    :to="{ name: 'CartList' }"
                    class="relative p-2 hover:bg-gray-100 rounded-full transition-all"
                >
                    <i
                        class="fa-solid fa-cart-shopping text-xl text-gray-700"
                    ></i>
                    <div
                        v-if="cartCount > 0"
                        class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-2 -end-2"
                    >
                        {{ cartCount }}
                    </div>
                </RouterLink> -->
                <button type="button" @click="showConfirm = true" class="cursor-pointer text-xl">
                    <i class="fa-solid fa-right-from-bracket text-gray-700"></i>
                </button>

                <div role="status" class="max-w-sm animate-pulse space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                        <div class="flex flex-col text-left gap-1.5">
                            <div class="h-2.5 w-20 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                            <div class="h-2 w-15 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                        </div>
                    </div>
                </div>

                <!-- <button
                    type="button"
                    id="user-menu-button"
                    class="flex items-center gap-3 px-3 py-1.5 rounded-full hover:bg-gray-50 transition-all cursor-pointer peer"
                    aria-expanded="false"
                >
                    <img
                        src=""
                        alt="User photo"
                        class="w-10 h-10 rounded-full object-cover ring-2 ring-blue-500 ring-offset-2 ring-offset-white"
                    />

                    <div
                        class="hidden sm:flex flex-col text-left leading-tight"
                    >
                        <span class="text-sm font-semibold text-gray-900">
                           Ehao
                        </span>
                        <span class="text-xs text-gray-600">
                            Admin
                        </span>
                    </div>
                </button> -->
            </div>
        </div>
    </nav>

    <ConfirmModal :show="showConfirm" title="Logout" message="Are you sure to logout?" confirm-text="Yes"
        confirm-btn="bg-red-600 hover:bg-red-700" icon-bg="bg-red-100" icon-color="text-red-600" @confirm="logout"
        @cancel="showConfirm = false" />
</template>

<script setup>
import ConfirmModal from "../messages/ConfirmModal.vue";
import { ref } from "vue";
import { useRouter } from "vue-router";
import authService from "../../api/services/auth/authService.js";
import { useUiStore } from "../../api/stores/uiStore.js";

const ui = useUiStore();
const router = useRouter();
const showConfirm = ref(false);

const logout = async () => {
    await authService.logout();
    router.replace({ name: "Login" });
};

</script>
