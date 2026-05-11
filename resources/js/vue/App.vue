<template>
    <router-view name="AdminNavbar"></router-view>
    <router-view v-if="!isAdmin"  name="Navbar"></router-view>
    <router-view name="Sidebar"></router-view>
    <router-view></router-view>
</template>

<script setup>
import { useAuthStore } from "./api/stores/authStore";
import { computed } from "vue";

const authStore = useAuthStore();

const isAdmin = computed(() => authStore.isAdmin);
const isAuthenticated = computed(() => authStore.isAuthenticated);


</script>
