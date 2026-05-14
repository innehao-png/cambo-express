<template>
    <section class="min-h-screen bg-gray-50 py-10 antialiased">

        <div class="mx-auto max-w-5xl px-4">

            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
            </div>

                <!-- Pagination -->
            <div class="mx-auto max-w-5xl px-4 mb-4">
                <PaginationLinks
                    v-if="paginationLinks.length"
                    :links="paginationLinks"
                    @page-change="onPageChange"
                />
            </div>

            <!-- Loading Skeleton -->
            <div v-if="loading" class="space-y-4">
                <div
                    v-for="n in 3"
                    :key="n"
                    class="rounded-2xl bg-white p-6 shadow-sm"
                >
                    <div class="flex items-center justify-between">
                        <Skeleton width="8rem" height="1rem" />
                        <Skeleton width="5rem" height="1.5rem" rounded="rounded-full" />
                    </div>
                    <div class="mt-4 flex gap-4">
                        <Skeleton width="4rem" height="4rem" rounded="rounded-xl" />
                        <div class="flex-1 space-y-2">
                            <Skeleton width="75%" height="1rem" />
                            <Skeleton width="50%" height="0.75rem" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error -->
            <div
                v-else-if="error"
                class="rounded-2xl bg-red-50 p-8 text-center text-red-600"
            >
                <i class="fa-solid fa-circle-xmark mb-3 text-4xl"></i>
                <p class="font-semibold">Failed to load orders</p>
                <p class="mt-1 text-sm">{{ error }}</p>
                <button
                    @click="load"
                    class="mt-4 rounded-lg bg-red-600 px-5 py-2 text-sm font-medium text-white transition hover:bg-red-700"
                >
                    Retry
                </button>
            </div>

            <!-- Empty State -->
            <div
                v-else-if="!orders.length"
                class="rounded-2xl bg-white py-16 text-center shadow-sm"
            >
                <i class="fa-solid fa-box-open mb-4 text-5xl text-gray-300"></i>
                <h2 class="text-xl font-semibold text-gray-700">No orders yet</h2>
                <p class="mt-1 text-sm text-gray-400">
                    When you place an order, it will appear here.
                </p>
                <router-link
                    :to="{ name: 'Home' }"
                    class="mt-6 inline-block rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700"
                >
                    Start Shopping
                </router-link>
            </div>

            <!-- Order List -->
            <div v-else class="space-y-5">
                <div
                    v-for="order in orders"
                    :key="order.id"
                    class="overflow-hidden rounded-2xl bg-white shadow-sm transition hover:shadow-md"
                >
                    <!-- Order Header -->
                    <div
                        class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 bg-gray-50/60 px-6 py-4"
                    >
                        <div class="flex items-center gap-4">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                                    Order
                                </p>
                                <p class="font-semibold text-gray-800">#{{ order.id }}</p>
                            </div>
                            <div class="h-8 w-px bg-gray-200"></div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                                    Date
                                </p>
                                <p class="text-sm text-gray-700">
                                    {{ formatDate(order.created_at) }}
                                </p>
                            </div>
                            <div class="h-8 w-px bg-gray-200"></div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                                    Payment
                                </p>
                                <p class="text-sm capitalize text-gray-700">
                                    {{ order.payment_method ?? "KHQR" }}
                                </p>
                            </div>
                        </div>

                        <span
                            :class="statusClass(order.status)"
                            class="rounded-full px-3 py-1 text-xs font-semibold capitalize"
                        >
                            {{ order.status }}
                        </span>
                    </div>

                    <!-- Order Items -->
                    <div class="divide-y divide-gray-50 px-6">
                        <div
                            v-for="item in order.order_items"
                            :key="item.id"
                            class="flex items-center gap-4 py-4"
                        >
                            <img
                                v-if="item.product?.image"
                                :src="item.product.image"
                                :alt="item.product?.name"
                                class="h-14 w-14 rounded-xl object-contain bg-gray-50 border border-gray-100"
                            />
                            <div
                                v-else
                                class="flex h-14 w-14 items-center justify-center rounded-xl bg-gray-100 text-gray-300"
                            >
                                <i class="fa-solid fa-image text-xl"></i>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="truncate font-medium text-gray-800">
                                    {{ item.product?.name ?? "Unknown Product" }}
                                </p>
                                <p class="text-sm text-gray-400">Qty: {{ item.quantity }}</p>
                            </div>

                            <p class="font-semibold text-gray-900">
                                {{ formatCurrency(item.price * item.quantity) }}
                            </p>
                        </div>
                    </div>

                    <!-- Order Footer -->
                    <div
                        class="flex items-center justify-between border-t border-gray-100 bg-gray-50/60 px-6 py-4"
                    >
                        <p class="text-sm text-gray-500">
                            {{ order.order_items?.length ?? 0 }}
                            {{ order.order_items?.length === 1 ? "item" : "items" }}
                        </p>
                        <div class="flex items-center gap-4">
                            <p class="text-sm text-gray-500">Total</p>
                            <p class="text-lg font-bold text-gray-900">
                                {{ formatCurrency(order.total_amount) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useOrderStore } from "../../api/stores/orderStore";
import { formatCurrency, formatDate } from "../../utils/formatters";
import PaginationLinks from "../../components/PaginationLinks.vue";
import Skeleton from "../../components/ui/Skeleton.vue";

const orderStore = useOrderStore();

const orders         = computed(() => orderStore.orders);
const loading        = computed(() => orderStore.loading);
const error          = computed(() => orderStore.error);
const paginationLinks = computed(() => orderStore.paginationLinks ?? []);

function load(page = 1) {
    orderStore.fetchOrders(page);
}

function onPageChange(page) {
    load(page);
    window.scrollTo({ top: 0, behavior: "smooth" });
}

onMounted(() => load());

function statusClass(status) {
    const map = {
        pending:    "bg-yellow-100 text-yellow-700",
        processing: "bg-blue-100 text-blue-700",
        paid:       "bg-green-100 text-green-700",
        cancelled:  "bg-red-100 text-red-700",
        failed:     "bg-red-100 text-red-700",
        shipped:    "bg-purple-100 text-purple-700",
        delivered:  "bg-teal-100 text-teal-700",
    };
    return map[status?.toLowerCase()] ?? "bg-gray-100 text-gray-600";
}
</script>
