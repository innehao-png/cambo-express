<template>
    <div
        :class="[
            'mt-16 p-8 bg-[#f8fafc] min-h-screen transition-all duration-300',
            ui.sidebarOpen ? 'ml-64' : 'ml-0',
        ]"
    >
        <!-- Error Alert -->
        <div v-if="error" class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4 flex items-center gap-3">
            <div class="w-5 h-5 flex-shrink-0 text-red-600">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-red-800">{{ error }}</p>
                <button @click="refreshData" class="text-xs font-bold text-red-600 hover:text-red-700 mt-2">Try Again</button>
            </div>
        </div>


        <!-- Header Section -->
        <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Overview</h1>
                <p class="text-slate-500 mt-1 font-medium">Welcome back, Admin! Here's what's happening today.</p>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
            <!-- Accounts Card -->
            <div class="group relative bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-5">
                        <div class="p-3 bg-blue-500 rounded-xl text-white shadow-lg shadow-blue-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">{{ percentChange > 0 ? '+' : '' }}{{ percentChange }}%</span>
                    </div>
                    <div class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Total Accounts</div>
                    <div class="text-4xl font-black text-slate-900 leading-none">
                        <Skeleton v-if="loading" width="2.5rem" class="rounded-full" height="2.5rem" />
                        <template v-else>{{ overview.users.total.toLocaleString() }}</template>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-slate-400">
                        <span class="font-semibold text-slate-500 mr-1">{{ overview.users.admins }}</span> Admin accounts
                    </div>
                </div>
            </div>

            <!-- Products Card -->
            <div class="group relative bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-purple-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-5">
                        <div class="p-3 bg-purple-500 rounded-xl text-white shadow-lg shadow-purple-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">{{ overview.products.total }}</span>
                    </div>
                    <div class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Total Products</div>
                    <div class="text-4xl font-black text-slate-900 leading-none">
                        <Skeleton v-if="loading" width="2.5rem" class="rounded-full" height="2.5rem" />
                        <template v-else>{{ overview.products.total }}</template>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-slate-400">
                        <span class="font-semibold text-slate-500 mr-1">{{ overview.products.low_stock }}</span> Low stock items
                    </div>
                </div>
            </div>

            <!-- Orders Card -->
            <div class="group relative bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-amber-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-5">
                        <div class="p-3 bg-amber-500 rounded-xl text-white shadow-lg shadow-amber-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">{{ overview.orders.active }}</span>
                    </div>
                    <div class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Total Orders</div>
                    <div class="text-4xl font-black text-slate-900 leading-none">
                        <Skeleton v-if="loading" width="2.5rem" class="rounded-full" height="2.5rem" />
                        <template v-else>{{ overview.orders.total }}</template>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-slate-400">
                        <span class="font-semibold text-slate-500 mr-1">{{ overview.orders.active }}</span> Active orders
                    </div>
                </div>
            </div>

            <!-- Sales Card -->
            <div class="group relative bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-5">
                        <div class="p-3 bg-emerald-500 rounded-xl text-white shadow-lg shadow-emerald-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+18.2%</span>
                    </div>
                    <div class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Monthly Sales</div>
                    <div class="text-4xl font-black text-slate-900 leading-none">
                        <Skeleton v-if="loading" width="2.5rem" class="rounded-full" height="2.5rem" />
                        <template v-else>{{ formatCurrency(salesStats.total_sales) }}</template>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-slate-400">
                        <span class="font-semibold text-slate-500 mr-1">{{ formatCurrency(salesStats.average_order_value) }}</span> Average order value
                    </div>
                </div>
            </div>
        </div>

        <!-- Tables Section -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <!-- Stock Movement Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                    <h2 class="text-xl font-extrabold text-slate-800">Recent Stock Movements</h2>
                    <button @click="refreshData" class="text-sm font-bold text-blue-600 hover:text-blue-700 transition-colors">Refresh</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-400 uppercase tracking-widest bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-4 font-bold">Product</th>
                                <th class="px-6 py-4 font-bold text-center">Date</th>
                                <th class="px-6 py-4 font-bold text-center">Qty</th>
                                <th class="px-6 py-4 font-bold text-center">Type</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-if="loading && stockMovement.length === 0">
                                <td colspan="4" class="px-6 py-4">
                                    <div class="space-y-4">
                                        <div v-for="i in 5" :key="i" class="flex items-center gap-4">
                                            <Skeleton width="100%" class="rounded-3xl" height="2.5rem" />
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else-if="!loading && stockMovement.length === 0" class="hover:bg-slate-50/80 transition-colors">
                                <td colspan="4" class="px-6 py-5 text-center text-slate-500">
                                    No stock movements found
                                </td>
                            </tr>
                            <tr v-for="stock in stockMovement.slice(0, 5)" :key="stock.id" class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800 text-base">{{ stock.product?.name || "N/A" }}</span>
                                        <span class="text-xs text-slate-400">{{ stock.note || "No notes" }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="bg-slate-100 px-2 py-1 rounded-lg text-xs font-bold text-slate-600 border border-slate-200">{{ formatDate(stock.created_at) }}</span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="text-base font-black text-indigo-600">{{ stock.quantity }}</span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span :class="[getStatusBadge(stock.type).bg, getStatusBadge(stock.type).text, 'font-extrabold px-2 py-1 rounded-full text-[10px] uppercase tracking-tighter shadow-sm']">
                                        {{ getStatusBadge(stock.type).label }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                    <h2 class="text-xl font-extrabold text-slate-800">Recent Orders</h2>
                    <button @click="refreshData" class="text-sm font-bold text-blue-600 hover:text-blue-700 transition-colors">View All</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-400 uppercase tracking-widest bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-4 font-bold">Order ID</th>
                                <th class="px-6 py-4 font-bold text-center">Date</th>
                                <th class="px-6 py-4 font-bold text-center">Amount</th>
                                <th class="px-6 py-4 font-bold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-if="loading && recentOrders.length === 0">
                                <td colspan="4" class="px-6 py-4">
                                    <div class="space-y-4">
                                        <div v-for="i in 5" :key="i" class="flex items-center gap-4">
                                            <Skeleton width="100%" class="rounded-3xl" height="2.5rem" />
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else-if="!loading && recentOrders.length === 0" class="hover:bg-slate-50/80 transition-colors">
                                <td colspan="4" class="px-6 py-5 text-center text-slate-500">
                                    No recent orders found
                                </td>
                            </tr>
                            <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800 text-base">#ORD-{{ String(order.id).padStart(6, "0") }}</span>
                                        <span class="text-xs text-slate-400 truncate max-w-50">{{ order.user?.name || "Customer" }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="bg-slate-100 px-3 py-1 rounded-lg text-xs font-bold text-slate-600 border border-slate-200">{{ formatDate(order.created_at) }}</span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="text-base font-black text-amber-600">{{ formatCurrency(order.total_amount) }}</span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span :class="[getStatusBadge(order.status).bg, getStatusBadge(order.status).text, 'font-extrabold px-3 py-1 rounded-full text-[10px] uppercase tracking-tighter shadow-sm']">
                                        {{ getStatusBadge(order.status).label }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useUiStore } from "../../api/stores/uiStore.js";
import adminService from "../../api/services/admin/adminService.js";
import Skeleton from "../../components/ui/Skeleton.vue";

const ui = useUiStore();

// Loading and error states
const loading = ref(true);
const error = ref(null);

// Overview data
const overview = ref({
    users: { total: 0, customers: 0, admins: 0 },
    orders: { total: 0, active: 0, total_revenue: 0 },
    products: { total: 0, low_stock: 0 },
    payments: { pending: 0 },
});

// Sales stats
const salesStats = ref({
    total_sales: 0,
    total_orders: 0,
    average_order_value: 0,
});

// Recent orders
const recentOrders = ref([]);

// Stock movement
const stockMovement = ref([]);

// Fetch dashboard data
const fetchDashboardData = async () => {
    try {
        loading.value = true;
        error.value = null;

        // Fetch overview
        const overviewResponse = await adminService.getDashboardOverview();
        if (overviewResponse.data?.data) {
            overview.value = overviewResponse.data.data;
        }

        // Fetch sales stats (last 30 days)
        const salesResponse = await adminService.getSalesStats(30);
        if (salesResponse.data?.data) {
            salesStats.value = {
                total_sales: salesResponse.data.data.total_sales,
                total_orders: salesResponse.data.data.total_orders,
                average_order_value: salesResponse.data.data.average_order_value,
            };
        }

        // Fetch recent orders (limit 5)
        const ordersResponse = await adminService.getRecentOrders(5);
        if (ordersResponse.data?.data) {
            recentOrders.value = ordersResponse.data.data;
        }

        // Fetch stock movement (last 30 days)
        const stockResponse = await adminService.getStockMovement(30);
        if (stockResponse.data?.data?.movements) {
            stockMovement.value = stockResponse.data.data.movements.data || [];
        }
    } catch (err) {
        console.error("Error fetching dashboard data:", err);
        error.value = err.response?.data?.message || "Failed to load dashboard data";
    } finally {
        loading.value = false;
    }
};

// Format currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
        minimumFractionDigits: 0,
    }).format(value || 0);
};

// Format date
const formatDate = (date) => {
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

// Get status badge styling
const getStatusBadge = (status) => {
    const statusMap = {
        "stock in": {
            bg: "bg-emerald-100",
            text: "text-emerald-700",
            label: "Stock In",
        },
        "stock out": {
            bg: "bg-red-100",
            text: "text-red-700",
            label: "Stock Out",
        },
        in: { bg: "bg-emerald-100", text: "text-emerald-700", label: "In" },
        out: { bg: "bg-red-100", text: "text-red-700", label: "Out" },
        pending: {
            bg: "bg-blue-100",
            text: "text-blue-700",
            label: "Pending",
        },
        processing: {
            bg: "bg-yellow-100",
            text: "text-yellow-700",
            label: "Processing",
        },
        paid: {
            bg: "bg-emerald-100",
            text: "text-emerald-700",
            label: "paid",
        },
        cancelled: {
            bg: "bg-red-100",
            text: "text-red-700",
            label: "Cancelled",
        },
    };
    return statusMap[status?.toLowerCase()] || statusMap.pending;
};

// Compute percentage change (mock for now)
const percentChange = computed(() => {
    return Math.floor(Math.random() * 30) - 10;
});

// Lifecycle
onMounted(() => {
    fetchDashboardData();
});

// Refresh data
const refreshData = () => {
    fetchDashboardData();
};
</script>
