<script setup>
import Hero from "@/vue/components/Hero.vue";
import ProductCard from "./ProductCard.vue";
</script>

<template>
    <!-- Hero Section -->
    <Hero />

    <!-- Category / Banner Section -->
    <section class="max-w-7xl mx-auto px-6 py-16 sm:py-24">
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
            <div>
                <h2 class="text-3xl md:text-4xl font-extrabold dark:text-slate-900 text-white tracking-tight">
                    Featured Collections
                </h2>
                <p class="mt-2 text-slate-500 dark:text-slate-400 text-lg">
                    Discover the latest trends in our premium curated categories.
                </p>
            </div>
            <a href="#" class="group inline-flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-bold hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                View All Categories
                <i class="fa-solid fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <!-- Large Featured Block (Left) -->
            <div class="md:col-span-8 group relative rounded-3xl overflow-hidden aspect-[16/9] md:aspect-auto min-h-[400px] shadow-sm hover:shadow-2xl transition-all duration-500">
                <img src="https://plus.unsplash.com/premium_photo-1721742733156-736e11c918bb?w=600&auto=format&fit=crop" alt="Fashion Trends" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-in-out"/>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/30 to-transparent"></div>
                <!-- Content -->
                <div class="absolute inset-0 p-8 sm:p-10 flex flex-col justify-end">
                    <div>
                        <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-bold uppercase tracking-wider rounded-lg mb-4">Trending Now</span>
                        <h3 class="text-3xl sm:text-4xl font-bold text-white mb-3 tracking-tight drop-shadow-md">Streetwear Collection</h3>
                        <p class="text-slate-200 max-w-md hidden sm:block font-medium drop-shadow-sm">Explore the freshest looks and elevate your everyday style with our premium urban selection.</p>
                        <button class="mt-6 px-6 py-2.5 bg-white text-slate-900 font-bold rounded-xl shadow-lg hover:bg-indigo-50 transition-colors">Shop Collection</button>
                    </div>
                </div>
            </div>

            <!-- Upper Small Block (Right) -->
            <div class="md:col-span-4 group relative rounded-3xl overflow-hidden aspect-[4/3] md:aspect-auto md:h-auto min-h-[200px] shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col">
                <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=2670&auto=format&fit=crop" alt="Electronics" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-in-out"/>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6 w-full mt-auto">
                    <h3 class="text-2xl font-bold text-white mb-1 drop-shadow-md">Gadgets & Tech</h3>
                    <p class="text-indigo-300 font-semibold text-sm drop-shadow-sm">Up to 40% Off</p>
                </div>
            </div>

            <!-- Lower Small Block 1 -->
            <div class="md:col-span-4 group relative rounded-3xl overflow-hidden min-h-[200px] shadow-sm hover:shadow-2xl transition-all duration-500">
                <img src="https://images.unsplash.com/photo-1522335789203-aabd1fc54c28?q=80&w=2687&auto=format&fit=crop" alt="Beauty Essentials" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-in-out"/>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6 w-full">
                    <h3 class="text-xl font-bold text-white mb-1 drop-shadow-md">Beauty Essentials</h3>
                </div>
            </div>

            <!-- Lower Small Block 2 -->
            <div class="md:col-span-4 group relative rounded-3xl overflow-hidden min-h-[200px] shadow-sm hover:shadow-2xl transition-all duration-500">
                <img src="https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?q=80&w=2572&auto=format&fit=crop" alt="Men's Apparel" class="absolute inset-0 w-full h-full object-cover object-top transform group-hover:scale-105 transition-transform duration-700 ease-in-out"/>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6 w-full">
                    <h3 class="text-xl font-bold text-white mb-1 drop-shadow-md">Men's Apparel</h3>
                </div>
            </div>

             <!-- Lower Small Block 3 -->
             <div class="md:col-span-4 group relative rounded-3xl overflow-hidden min-h-[200px] shadow-sm hover:shadow-2xl transition-all duration-500">
                <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=2670&auto=format&fit=crop" alt="Sneaker Drops" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-in-out"/>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6 w-full">
                    <h3 class="text-xl font-bold text-white mb-1 drop-shadow-md">Sneaker Drops</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <ProductCard />
</template>
