<template>
    <nav
        class="sticky top-0 z-1 backdrop-blur-xl shadow-md bg-linear-to-br from-white to-gray-50 border-b border-gray-100 antialiased animate-slideDown"
    >
        <!-- Decorative gradient overlay -->
        <div
            class="absolute top-0 right-0 w-96 h-full bg-gradient-radial from-amber-50/30 via-transparent to-transparent pointer-events-none"
        ></div>

        <div class="max-w-7xl px-4 mx-auto 2xl:px-0 py-5 relative z-10">
            <div class="flex items-center justify-between">
                <!-- Logo and Navigation Links -->
                <div class="flex items-center space-x-12">
                    <!-- Logo -->
                    <div class="shrink-0">
                        <a
                            href="#"
                            class="text-3xl font-bold tracking-tight bg-linear-to-r from-gray-900 to-amber-600 bg-clip-text text-transparent font-serif relative inline-block animate-fadeIn"
                        >
                            CamboExpress
                            <span
                                class="absolute -bottom-1 left-0 w-10 h-0.5 bg-amber-500 animate-expandWidth"
                            ></span>
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <ul class="hidden lg:flex items-center gap-8">
                        <li
                            v-for="(link, index) in navLinks"
                            :key="link.route"
                            :style="`animation-delay: ${0.1 + index * 0.05}s`"
                        >
                            <router-link
                                :to="{ name: link.route }"
                                class="relative py-2 text-sm font-medium text-gray-900 transition-all duration-300 hover:text-amber-600 hover:-translate-y-0.5 animate-fadeInUp before:absolute before:bottom-0 before:left-1/2 before:-translate-x-1/2 before:w-0 before:h-0.5 before:bg-amber-500 before:transition-all before:duration-300 hover:before:w-full"
                                active-class="text-amber-600 before:w-full"
                                exact-active-class="text-amber-600 before:w-full"
                            >
                                {{ link.label }}
                            </router-link>
                        </li>
                    </ul>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-3">
                    <!-- Cart Button -->
                    <div class="relative">
                        <router-link
                            :to="{ name: 'Cart' }"
                            class="group relative flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 bg-transparent transition-all duration-300 hover:border-amber-500 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-amber-500/10 overflow-visible animate-fadeIn"
                            style="animation-delay: 0.35s"
                        >
                            <span
                                class="absolute inset-0 bg-amber-50 scale-0 rounded-xl transition-transform duration-500 group-hover:scale-100"
                            ></span>
                            <svg
                                class="w-5 h-5 relative z-10 text-gray-600 transition-all duration-300 group-hover:text-amber-600 group-hover:scale-110"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                />
                            </svg>
                            <span
                                class="absolute -top-1.5 -right-1.5 z-20 min-w-4.5 h-4.5 flex items-center justify-center bg-amber-500 text-white text-[10px] font-bold px-1 rounded-full"
                            >
                                {{ cartStore.carts.total || 0 }}
                            </span>
                        </router-link>
                    </div>

                    <!-- User Account Button -->
                    <div class="relative">
                        <button
                            @click.stop="toggleUser"
                            class="group relative p-3 rounded-xl border border-gray-200 bg-transparent transition-all duration-300 hover:border-amber-500 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-amber-500/10 overflow-hidden animate-fadeIn"
                            style="animation-delay: 0.4s"
                        >
                            <span
                                class="absolute inset-0 bg-amber-50 scale-0 rounded-xl transition-transform duration-500 group-hover:scale-100"
                            ></span>
                            <svg
                                class="w-5 h-5 relative z-10 transition-all duration-300 group-hover:text-amber-600 group-hover:scale-110"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                />
                            </svg>
                        </button>

                        <!-- User Dropdown -->
                        <Transition
                            enter-active-class="transition duration-300 ease-out"
                            enter-from-class="opacity-0 -translate-y-2 scale-95"
                            enter-to-class="opacity-100 translate-y-0 scale-100"
                            leave-active-class="transition duration-200 ease-in"
                            leave-from-class="opacity-100 translate-y-0 scale-100"
                            leave-to-class="opacity-0 -translate-y-2 scale-95"
                        >
                            <div
                                v-if="isUserOpen"
                                class="absolute top-full right-0 mt-4 w-64 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50"
                            >
                                <!-- Accent bar -->
                                <div
                                    class="h-1 bg-linear-to-r from-amber-500 to-gray-900"
                                ></div>

                                <div class="py-2">
                                    <div v-if="!profile">
                                        <router-link
                                            :to="{ name: 'Login' }"
                                            class="block px-4 py-3 mx-2 my-1 text-sm font-medium text-gray-900 rounded-lg transition-all duration-200 hover:bg-amber-50 hover:text-gray-900 hover:translate-x-1 animate-slideInRight"
                                            style="animation-delay: 0.3s"
                                        >
                                            Sign In
                                        </router-link>
                                        <router-link
                                            :to="{ name: 'Register' }"
                                            class="block px-4 py-3 mx-2 my-1 text-sm font-medium text-gray-900 rounded-lg transition-all duration-200 hover:bg-amber-50 hover:text-gray-900 hover:translate-x-1 animate-slideInRight"
                                            style="animation-delay: 0.3s"
                                        >
                                            Sign Up
                                        </router-link>
                                    </div>
                                    <div v-else>
                                        <router-link
                                            v-for="(
                                                item, index
                                            ) in userMenuItems"
                                            :key="item.route"
                                            :to="{ name: item.route }"
                                            class="block px-4 py-3 mx-2 my-1 text-sm font-medium text-gray-900 rounded-lg transition-all duration-200 hover:bg-amber-50 hover:text-gray-900 hover:translate-x-1 animate-slideInRight"
                                            active-class="bg-amber-300 text-amber-700"
                                            exact-active-class="bg-amber-300 text-amber-700"
                                            :style="`animation-delay: ${index * 0.05}s`"
                                        >
                                            {{ item.label }}
                                        </router-link>
                                        <div
                                            class="border-t border-gray-100 my-2"
                                        ></div>
                                        <a
                                            role="button"
                                            @click="showConfirm = true"
                                            class="block px-4 py-3 mx-2 my-1 text-sm font-medium text-gray-900 rounded-lg transition-all duration-200 hover:bg-amber-50 hover:text-gray-900 hover:translate-x-1 animate-slideInRight"
                                            style="animation-delay: 0.3s"
                                        >
                                            Sign Out
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- Mobile Menu Toggle -->
                    <button
                        @click="toggleMobileMenu"
                        class="group lg:hidden relative p-3 rounded-xl border border-gray-200 bg-transparent transition-all duration-300 hover:border-amber-500 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-amber-500/10 overflow-hidden animate-fadeIn"
                        style="animation-delay: 0.45s"
                    >
                        <span
                            class="absolute inset-0 bg-amber-50 scale-0 rounded-xl transition-transform duration-500 group-hover:scale-100"
                        ></span>
                        <svg
                            class="w-5 h-5 relative z-10 transition-all duration-300 group-hover:text-amber-600 group-hover:scale-110"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <Transition
                enter-active-class="transition duration-300 ease-out origin-top"
                enter-from-class="opacity-0 scale-y-0"
                enter-to-class="opacity-100 scale-y-100"
                leave-active-class="transition duration-200 ease-in origin-top"
                leave-from-class="opacity-100 scale-y-100"
                leave-to-class="opacity-0 scale-y-0"
            >
                <div
                    v-if="isMobileMenuOpen"
                    class="lg:hidden mt-4 bg-white rounded-xl border border-gray-200 overflow-hidden"
                >
                    <a
                        v-for="(link, index) in mobileLinks"
                        :key="link"
                        href="#"
                        class="block px-4 py-3.5 text-sm font-medium text-gray-900 border-b border-gray-100 last:border-b-0 transition-all duration-200 hover:bg-amber-50 hover:pl-6 animate-fadeInUp"
                        :style="`animation-delay: ${index * 0.05}s`"
                    >
                        {{ link }}
                    </a>
                </div>
            </Transition>
        </div>
    </nav>

    <ConfirmModal
        :show="showConfirm"
        title="Logout"
        message="Are you sure to logout?"
        confirm-text="Yes"
        confirm-btn="bg-red-600 hover:bg-red-700"
        icon-bg="bg-red-100"
        icon-color="text-red-600"
        @confirm="logout"
        @cancel="showConfirm = false"
    />
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../api/stores/authStore";
import ConfirmModal from "../messages/ConfirmModal.vue";
import { useCartStore } from "../../api/stores/cartStore";

// ====== State ====== \\
const authStore = useAuthStore();
const profile = computed(() => authStore.user);
const cartStore = useCartStore();
const navLinks = [
    { label: "Home", route: "Home" },
    { label: "My Orders", route: "Orders" },
    // { label: "Gift Ideas", route: "GiftIdeas" },
    // { label: "Today's Deals", route: "TodaysDeals" },
    // { label: "Sell", route: "Sell" },
];
const mobileLinks = [
    "Home",
    "Best Sellers",
    "Gift Ideas",
    "Games",
    "Electronics",
    "Home & Garden",
];
const userMenuItems = [
    { label: "My Account", route: "MyAccount" },
    { label: "My Orders", route: "Orders" },
    // { label: "Settings", route: "Settings" },
    // { label: "Favourites", route: "Favourites" },
];

const isUserOpen = ref(false);
const isMobileMenuOpen = ref(false);
const showConfirm = ref(false);
const router = useRouter();

// ====== Function ====== \\
const toggleUser = () => {
    isUserOpen.value = !isUserOpen.value;
};

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};
const closeAllDropdowns = () => {
    isUserOpen.value = false;
};

// ====== Api Function ====== \\

const logout = async () => {
    await authStore.logout();
    router.replace({ name: "Login" });
};

// ====== OnMounted ====== \\
onMounted(async () => {
    try {
        if (authStore.token && !authStore.user) {
            await authStore.getProfile();
        }
        await cartStore.fetchCarts();
    } catch (error) {
        console.error("Failed to fetch data:", error);
    }

    document.addEventListener("click", closeAllDropdowns);
});

onUnmounted(() => {
    document.removeEventListener("click", closeAllDropdowns);
});
</script>

<style scoped>
/* Font imports */
@import url("https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@400;500;600&display=swap");

/* Custom font classes */
.font-serif {
    font-family: "Playfair Display", serif;
}

/* Radial gradient background */
.bg-gradient-radial {
    background: radial-gradient(circle at 100% 50%, var(--tw-gradient-stops));
}

/* Custom animations */
@keyframes slideDown {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes expandWidth {
    from {
        width: 0;
    }
    to {
        width: 2.5rem;
    }
}

.animate-slideDown {
    animation: slideDown 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.animate-fadeIn {
    animation: fadeIn 0.6s ease-out both;
}

.animate-fadeInUp {
    animation: fadeInUp 0.6s ease-out both;
}

.animate-slideInRight {
    animation: slideInRight 0.4s ease-out both;
}

.animate-expandWidth {
    animation: expandWidth 0.8s ease-out 0.6s both;
}
</style>
