<template>
    <section class="py-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">
                Shopping Cart
            </h2>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Cart Items -->
                <div class="flex-1 space-y-6">
                    <div
                        v-if="!items.length"
                        class="text-center py-10 text-gray-500"
                    >
                        Your cart is empty.
                    </div>

                    <div
                        v-for="item in items"
                        :key="item.id"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700"
                    >
                        <div
                            class="flex flex-col md:flex-row gap-6 items-center"
                        >
                            <!-- Product Image -->
                            <img
                                class="w-24 h-24 object-contain"
                                :src="item.product.image"
                                :alt="item.product.name"
                            />

                            <!-- Product Info -->
                            <div class="flex-1 text-center md:text-left">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white"
                                >
                                    {{ item.product.name }}
                                </h3>

                                <div
                                    class="flex flex-wrap justify-center md:justify-start gap-4 mt-3 text-sm"
                                >
                                    <button
                                        class="text-red-600 hover:text-red-700 transition"
                                        @click="remove(item)"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </div>

                            <!-- Quantity + Price -->
                            <div class="flex items-center gap-6">
                                <!-- Quantity -->
                                <div class="flex items-center">
                                    <button
                                        type="button"
                                        class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700"
                                        @click="decrement(item)"
                                        :disabled="item.quantity <= 1"
                                    >
                                        <i
                                            class="fa-solid fa-minus text-gray-900 dark:text-white text-sm"
                                        ></i>
                                    </button>
                                    <input
                                        type="text"
                                        class="w-10 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white"
                                        :value="item.quantity"
                                        readonly
                                    />
                                    <button
                                        type="button"
                                        class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700"
                                        @click="increment(item)"
                                    >
                                        <i
                                            class="fa-solid fa-plus text-gray-900 dark:text-white text-sm"
                                        ></i>
                                    </button>
                                </div>

                                <!-- Price -->
                                <p
                                    class="text-lg font-bold text-gray-900 dark:text-white"
                                >
                                    {{
                                        formatCurrency(
                                            item.product.price * item.quantity,
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="w-full lg:w-1/3">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700"
                    >
                        <h3
                            class="text-xl font-semibold text-gray-900 dark:text-white mb-6"
                        >
                            Order Summary
                        </h3>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400"
                                    >Subtotal</span
                                >
                                <span class="font-medium text-white">
                                    {{ formatCurrency(subtotal) }}
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400"
                                    >Quantity</span
                                >
                                <span class="font-medium text-white">
                                    {{ totalQty }} items
                                </span>
                            </div>
                        </div>

                        <button
                            class="w-full mt-6 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium py-3 rounded-lg transition"
                            :disabled="!items.length"
                            @click="proceedToCheckout"
                        >
                            Proceed to Checkout
                        </button>

                        <button
                            class="w-full mt-2 text-red-600 hover:text-red-800 font-medium py-3 rounded-lg transition"
                            @click="showConfirm = true"
                            v-if="items.length"
                        >
                            Clear Cart
                        </button>

                        <p
                            class="text-center text-sm text-gray-500 dark:text-gray-400 mt-4"
                        >
                            or
                            <RouterLink
                                :to="{ name: 'Home' }"
                                class="text-blue-600 hover:underline ml-1"
                            >
                                Continue Shopping
                            </RouterLink>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="fixed top-5 right-5 z-50">
        <AlertMsg
            v-if="showAlert"
            :type="alertType"
            :title="title"
            :message="message"
            @close="hideMessage"
        />
    </div>

    <ConfirmModal
        :show="showConfirm"
        title="Confirmation"
        message="Are you sure to clear carts?"
        confirm-text="Yes"
        confirm-btn="bg-red-600 hover:bg-red-700"
        icon-bg="bg-red-100"
        icon-color="text-red-600"
        @confirm="clearCart"
        @cancel="showConfirm = false"
    />
</template>

<script setup>
import { onMounted, computed, ref } from "vue";
import { useRouter } from "vue-router";
import { useCartStore } from "../../api/stores/cartStore";
import ConfirmModal from "../../components/messages/ConfirmModal.vue";
import AlertMsg from "../../components/messages/AlertMsg.vue";
import { useAlert } from "../../composables/useAlert";
import { formatCurrency } from "../../utils/formatters";
import { parseError } from "../../utils/errors";

const router = useRouter();

const cartStore = useCartStore();

const { message, alertType, title, showAlert, showMessage, hideMessage } = useAlert();

const showConfirm = ref(false);

const items = computed(() => cartStore.carts?.data?.cart_items || []);
// console.log("Items:", items.value);

const subtotal = computed(() => cartStore.carts?.subtotal || 0);
const totalQty = computed(() => cartStore.carts?.total || 0);

const increment = async (item) => {
    try {
        await cartStore.updateItem(item.id, item.quantity + 1);
    } catch (error) {
        showMessage(parseError(error), "error", "Failed");
    }
};
const decrement = async (item) => {
    if (item.quantity > 1) {
        await cartStore.updateItem(item.id, item.quantity - 1);
    }
};
const remove = async (item) => {
    await cartStore.removeItem(item.id);
};
const clearCart = async () => {
    await cartStore.clearCart();
    showConfirm.value = false;
};

const proceedToCheckout = () => {
    if (items.value.length > 0) {
        router.push({ name: "Checkout" });
    }
};

// formatCurrency moved to utils/formatters.js

onMounted(async () => {
    await cartStore.fetchCarts();
});
</script>
