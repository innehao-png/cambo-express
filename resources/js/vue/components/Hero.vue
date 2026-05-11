<script setup>
import { useAuthStore } from "../api/stores/authStore";
import { computed } from "vue";

const authStore = useAuthStore();
const isAdmin = computed(() => authStore.isAdmin);

const scrollToTrending = () => {
    const el = document.getElementById("trending-products");
    if (el) el.scrollIntoView({ behavior: "smooth" });
};
</script>

<template>
    <section class="relative overflow-hidden bg-slate-50 dark:bg-slate-900 border-b border-white dark:border-slate-800">
        <!-- Abstract Background Decoration -->
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none flex justify-center items-center">
            <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] rounded-full bg-gradient-to-br from-indigo-300/40 to-purple-300/40 dark:from-indigo-600/30 dark:to-purple-800/30 blur-3xl mix-blend-multiply dark:mix-blend-lighten animate-pulse duration-[5000ms]"></div>
            <div class="absolute bottom-[-10%] right-[-5%] w-[600px] h-[600px] rounded-full bg-gradient-to-bl from-pink-200/50 to-rose-200/50 dark:from-pink-900/30 dark:to-rose-900/30 blur-3xl mix-blend-multiply dark:mix-blend-lighten animate-pulse duration-[7000ms]"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 py-20 sm:py-28 lg:py-32 text-center">
            <!-- Badge -->
            <div class="mb-8">
                <span class="inline-flex items-center gap-2.5 px-5 py-2 text-sm font-semibold rounded-full bg-white/70 dark:bg-slate-800/70 backdrop-blur-md border border-indigo-100 dark:border-indigo-900/50 text-indigo-700 dark:text-indigo-400 shadow-sm transition-all hover:scale-105 hover:bg-white dark:hover:bg-slate-800 cursor-default">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                    </span>
                    Everything You Need, One Click Away
                </span>
            </div>

            <!-- Heading -->
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-[1.15]">
                Shop Smarter with <br class="hidden sm:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400">
                    CamboExpress
                </span>
            </h1>

            <!-- Subtext -->
            <p class="mt-8 text-lg sm:text-xl md:text-2xl text-slate-600 dark:text-slate-300 max-w-3xl mx-auto font-medium leading-relaxed">
                Experience seamless shopping across Cambodia. Quality products, secure payments, and fast delivery right to your doorstep.
            </p>

            <!-- Buttons -->
            <div class="mt-12 flex flex-col sm:flex-row justify-center items-center gap-5">
                <a
                    href="#trending-products"
                    @click.prevent="scrollToTrending"
                    class="group relative inline-flex items-center justify-center gap-3 w-full sm:w-auto px-8 py-4 text-lg font-bold text-white bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-200 dark:shadow-indigo-900/20 hover:bg-indigo-700 hover:shadow-xl hover:shadow-indigo-300 hover:-translate-y-1 transition-all duration-300"
                >
                    <i class="fa-solid fa-cart-shopping group-hover:scale-110 transition-transform"></i>
                    Start Shopping
                </a>

                <a
                    href="#"
                    class="group inline-flex items-center justify-center gap-3 w-full sm:w-auto px-8 py-4 text-lg font-bold text-indigo-700 dark:text-indigo-300 bg-white/80 dark:bg-slate-800/80 backdrop-blur-md border border-indigo-100 dark:border-slate-700 rounded-2xl hover:border-indigo-600 dark:hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-slate-700 shadow-sm hover:shadow-md transition-all duration-300"
                >
                    <i class="fa-solid fa-bullhorn group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></i>
                    View Offers
                </a>

                <router-link
                    v-if="isAdmin"
                    :to="{ name: 'Dashboard' }"
                    class="group relative inline-flex items-center justify-center gap-3 w-full sm:w-auto px-8 py-4 text-lg font-bold text-white bg-slate-900 dark:bg-slate-800 rounded-2xl shadow-lg hover:bg-black dark:hover:bg-slate-700 hover:-translate-y-1 transition-all duration-300 border border-slate-700"
                >
                    <i class="fa-solid fa-gauge-high group-hover:scale-110 transition-transform"></i>
                    Dashboard
                </router-link>
            </div>

            <!-- Features -->
            <div class="mt-20 grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 max-w-5xl mx-auto text-left">
                <!-- Feature 1 -->
                <div class="relative group bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl p-8 rounded-3xl shadow-sm hover:shadow-xl hover:bg-white dark:hover:bg-slate-800 border border-slate-100 dark:border-slate-700/50 transition-all duration-500 hover:-translate-y-2">
                    <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl shadow-lg shadow-indigo-200 dark:shadow-indigo-900/50 flex items-center justify-center text-white text-2xl mb-6 transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Lightning Fast</h3>
                    <p class="mt-3 text-slate-500 dark:text-slate-400 leading-relaxed text-sm font-medium">Nationwide delivery that gets your items to you when you need them.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="relative group bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl p-8 rounded-3xl shadow-sm hover:shadow-xl hover:bg-white dark:hover:bg-slate-800 border border-slate-100 dark:border-slate-700/50 transition-all duration-500 hover:-translate-y-2">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl shadow-lg shadow-teal-200 dark:shadow-teal-900/50 flex items-center justify-center text-white text-2xl mb-6 transform group-hover:scale-110 group-hover:-rotate-6 transition-all duration-300">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">100% Secure</h3>
                    <p class="mt-3 text-slate-500 dark:text-slate-400 leading-relaxed text-sm font-medium">Bank-level encryption and trusted digital payment options for checkout.</p>
                </div>

                <!-- Feature 3 -->
                <div class="relative group bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl p-8 rounded-3xl shadow-sm hover:shadow-xl hover:bg-white dark:hover:bg-slate-800 border border-slate-100 dark:border-slate-700/50 transition-all duration-500 hover:-translate-y-2">
                    <div class="w-14 h-14 bg-gradient-to-br from-rose-400 to-pink-500 rounded-2xl shadow-lg shadow-pink-200 dark:shadow-pink-900/50 flex items-center justify-center text-white text-2xl mb-6 transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Daily Deals</h3>
                    <p class="mt-3 text-slate-500 dark:text-slate-400 leading-relaxed text-sm font-medium">Competitive prices, exclusive discounts, and flash promotions.</p>
                </div>
            </div>
        </div>
    </section>
</template>
