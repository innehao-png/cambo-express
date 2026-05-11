<script setup>
import { onMounted, ref } from "vue";
import { useProductStore } from "../api/stores/productStore";
import { useCartStore } from "../api/stores/cartStore";
import AlertMsg from "../components/messages/AlertMsg.vue";
import { useAlert } from "../composables/useAlert";
import { formatCurrency } from "../utils/formatters";
import { parseError } from "../utils/errors";

// ====== Store ====== \\
const productStore = useProductStore();
const cartStore = useCartStore();

const { message, alertType, title, showAlert, showMessage, hideMessage } = useAlert();

// ====== Api Function ====== \\
const addToCart = async (productId) => {
    try {
        const res = await cartStore.addItem(productId);
       showMessage(res.message, "create", "Success")
    } catch (error) {
        showMessage(parseError(error), "error", "Failed")
    }

    // console.log(res);
};

onMounted(async () => {
    await productStore.fetchProducts();
    // console.log(productStore.products);
});
</script>

<template>
    <div class="fixed top-5 right-5 z-50">
        <AlertMsg
            v-if="showAlert"
            :type="alertType"
            :title="title"
            :message="message"
            @close="hideMessage"
        />
    </div>

    <div id="trending-products" class="bg-gray-50 dark:bg-gray-900 py-15">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between">
                <h2
                    class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white"
                >
                    Trending Products
                </h2>
            </div>

            <section
                class="bg-gray-50 py-8 antialiased dark:bg-gray-900 md:py-12"
            >
                <div class="mx-auto max-w-7xl px-4 2xl:px-0">
                    <!-- Heading & Filters -->
                    <!--  -->
                    <div
                        class="mb-4 grid gap-6 sm:grid-cols-2 md:mb-8 lg:grid-cols-3 xl:grid-cols-4"
                    >
                        <div
                            class="group relative flex flex-col rounded-3xl border border-transparent bg-white shadow-sm transition-all duration-300 hover:border-indigo-100 hover:shadow-2xl dark:border-transparent dark:bg-slate-800 dark:hover:border-slate-700"
                            v-for="p in productStore.products"
                            :key="p.id"
                        >
                            <!-- Image Container -->
                            <div
                                class="relative aspect-square w-full overflow-hidden rounded-t-3xl bg-gray-50 dark:bg-slate-900/50"
                            >
                                <!-- Action Buttons (Float on Hover) -->
                                <div
                                    class="absolute right-3 top-3 z-10 flex flex-col gap-2 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                                >
                                    <button
                                        type="button"
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-white/90 text-gray-600 shadow-md backdrop-blur transition-colors hover:bg-indigo-600 hover:text-white dark:bg-slate-800/90 dark:text-gray-300 dark:hover:bg-indigo-500"
                                        title="Quick View"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-white/90 text-gray-600 shadow-md backdrop-blur transition-colors hover:bg-red-500 hover:text-white dark:bg-slate-800/90 dark:text-gray-300 dark:hover:bg-red-500"
                                        title="Add to Wishlist"
                                    >
                                        <i class="fa-regular fa-heart"></i>
                                    </button>
                                </div>

                                <a href="#">
                                    <img
                                        class="absolute inset-0 h-full w-full object-contain p-6 mix-blend-multiply transition-transform duration-500 group-hover:scale-110 dark:mix-blend-normal"
                                        :src="p.image"
                                        :alt="p.name"
                                    />
                                </a>

                                <!-- Tags/Badges -->
                                <div class="absolute left-4 top-4">
                                    <span
                                        class="inline-flex items-center rounded-lg bg-red-50 px-2.5 py-1 text-xs font-bold text-red-600 ring-1 ring-inset ring-red-500/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/30"
                                    >
                                        Hot
                                    </span>
                                </div>
                            </div>

                            <!-- Content Area -->
                            <div class="flex flex-1 flex-col p-6">
                                <!-- Category & Rating Row -->
                                <div
                                    class="mb-3 flex flex-wrap items-center justify-between gap-2"
                                >
                                    <p
                                        class="text-xs font-medium uppercase tracking-wider text-indigo-600 dark:text-indigo-400"
                                    >
                                        {{ p.category_id?.name }}
                                    </p>

                                    <div
                                        class="flex items-center gap-1.5 rounded-md bg-yellow-50 px-2 py-1 dark:bg-yellow-500/10"
                                    >
                                        <i
                                            class="fa-solid fa-star text-xs text-yellow-500 dark:text-yellow-400"
                                        ></i>
                                        <span
                                            class="text-xs font-bold text-gray-900 dark:text-white"
                                            >5.0</span
                                        >
                                        <span
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                            >(455)</span
                                        >
                                    </div>
                                </div>

                                <!-- Title -->
                                <div
                                    class="flex items-start justify-between gap-3"
                                >
                                    <!-- Product Name -->
                                    <a
                                        class="flex-1 line-clamp-2 text-lg font-bold leading-snug text-gray-900 transition-colors hover:text-indigo-600 dark:text-white dark:hover:text-indigo-400"
                                    >
                                        {{ p.name }}
                                    </a>

                                    <!-- Stock Badge -->
                                    <span
                                        class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full"
                                        :class="
                                            p.stock > 0
                                                ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                                                : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'
                                        "
                                    >
                                        {{
                                            p.stock > 0
                                                ? `${p.stock} In Stock`
                                                : "Out of stock"
                                        }}
                                    </span>
                                </div>

                                <!-- Price & Action Row (Pushed to bottom) -->
                                <div
                                    class="mt-auto pt-4 flex items-end justify-between gap-4 border-t border-gray-100 dark:border-slate-700/50"
                                >
                                    <div>
                                        <p
                                            v-if="p.discount > 0"
                                            class="text-xs text-gray-500 line-through dark:text-gray-400"
                                        >
                                            ${{ p.price }}
                                        </p>
                                        <p
                                            class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white"
                                        >
                                            {{
                                                formatCurrency(
                                                    p.discount > 0
                                                        ? (p.price *
                                                              p.discount) /
                                                              100
                                                        : p.price,
                                                )
                                            }}
                                        </p>
                                    </div>

                                    <button
                                        @click="addToCart(p.id)"
                                        type="button"
                                        class="group/btn flex h-12 w-12 items-center justify-center overflow-hidden rounded-2xl bg-indigo-600 text-white shadow-md transition-all duration-300 hover:w-32 hover:bg-indigo-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800"
                                    >
                                        <i
                                            class="fa-solid fa-cart-plus shrink-0 transition-all duration-300 group-hover/btn:mr-1.5"
                                        ></i>
                                        <span
                                            class="max-w-0 overflow-hidden whitespace-nowrap font-semibold text-sm transition-all duration-300 group-hover/btn:max-w-xs"
                                            >Add to Cart</span
                                        >
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 text-center sm:mt-12">
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-2xl bg-white px-8 py-3.5 text-sm font-bold tracking-wide text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 transition-all hover:bg-gray-50 hover:text-indigo-600 hover:shadow-md dark:bg-slate-800 dark:text-white dark:ring-slate-700 dark:hover:bg-slate-700 dark:hover:text-indigo-400"
                        >
                            Explore More Products
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
