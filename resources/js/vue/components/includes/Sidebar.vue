<script setup>
import { useUiStore } from "../../api/stores/uiStore.js";

const ui = useUiStore();
</script>

<template>
    <aside :class="['fixed top-0 left-0 z-40 w-64 h-screen bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out',
        ui.sidebarOpen ? 'translate-x-0 shadow-xl' : '-translate-x-full']" aria-label="Sidebar">
        <div class="h-full px-4 py-6 flex flex-col">
            <!-- Logo -->
            <div class="mb-10 text-center">
                <h1 class="text-2xl font-bold text-blue-600">Cambo Express</h1>
                <p class="text-md text-gray-400">Shopping Online System</p>
            </div>

            <!-- Menu -->
            <ul class="space-y-2 font-medium text-gray-700">
                <li>
                    <RouterLink :to="{ name: 'Dashboard' }"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all hover:bg-blue-50 hover:text-blue-600"
                        exact-active-class="bg-blue-500 text-white">
                        <i class="fa-solid fa-gauge"></i>
                        <span>Dashboard</span>
                    </RouterLink>
                </li>

                <li>
                    <RouterLink :to="{ name: 'ProductList' }"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all hover:bg-blue-50 hover:text-blue-600"
                        exact-active-class="bg-blue-500 text-white">
                        <i class="fa-solid fa-box"></i>
                        <span>Product</span>
                    </RouterLink>
                </li>
                <li>
                    <RouterLink :to="{ name: 'UserList' }"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all hover:bg-blue-50 hover:text-blue-600"
                        exact-active-class="bg-blue-500 text-white">
                        <i class="fa-solid fa-user"></i>
                        <span>User</span>
                    </RouterLink>
                </li>
            </ul>

            <!-- Footer -->
            <div class="mt-auto text-center text-sm text-gray-400">
                © 2026 Cambo Express
            </div>
        </div>
    </aside>
</template>
