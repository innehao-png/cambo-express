<template>
    <div class="mt-16 p-6 bg-gray-50 min-h-screen transition-all duration-300"
        :class="ui.sidebarOpen ? 'ml-64' : 'ml-0'">
        <Breadcrumb :items="[
            { label: 'Admin', to: { name: 'Dashboard' } },
            { label: 'Products' },
        ]" />

        <div class="shadow-md rounded-lg px-6 py-4">
            <div class="relative overflow-x-auto shadow-xs rounded">
                <div class="flex justify-between mb-4 items-center">
                    <h1 class="text-2xl font-bold text-gray-800">
                        Product List
                    </h1>
                    <!-- Filter and Search -->
                    <div class="relative flex items-center justify-center p-4">
                        <button @click="isDropdownOpen = !isDropdownOpen" id="dropdownDefault"
                            class="flex items-center gap-2 text-white bg-indigo-600 hover:bg-indigo-700 font-medium rounded-l-lg text-sm px-4 py-2.5 shadow-sm transition"
                            type="button">
                            Filter by category
                            <i class="fa-solid fa-angle-down text-xs"></i>
                        </button>

                        <form @submit.prevent="productStore.fetchProducts()"
                            class="flex items-center max-w-sm mx-auto space-x-2">
                            <label for="simple-search" class="sr-only">Search</label>
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <i class="fas fa-box text-slate-400"></i>
                                </div>
                                <input v-model="productStore.filters.search" type="text" id="simple-search"
                                    class="px-3 py-2.5 border border-slate-200 outline-0 rounded-r-lg ps-9 text-sm w-full"
                                    placeholder="Search product name..." />
                            </div>
                            <button type="submit"
                                class="inline-flex items-center justify-center shrink-0 text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs rounded-full w-10 h-10 transition-transform active:scale-90">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>



                        <!-- Dropdown menu -->
                        <div v-if="isDropdownOpen" id="dropdown"
                            class="absolute top-14 mt-3 left-0 z-10 w-56 p-4 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto">
                            <h6 class="mb-3 text-sm font-semibold text-gray-700">
                                Category
                            </h6>

                            <ul class="space-y-3 text-sm">
                                <li v-for="category in productStore.allCategories" :key="category.id"
                                    class="flex items-center">
                                    <input :id="'category-' + category.id" type="radio" name="category-filter"
                                        :value="category.id" v-model="productStore.filters.category"
                                        @change="productStore.fetchProducts()"
                                        class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer" />
                                    <label :for="'category-' + category.id"
                                        class="ml-2 text-gray-700 font-medium cursor-pointer">
                                        {{ category.name }}
                                        <span class="text-gray-400">({{
                                            category.products_count
                                        }})</span>
                                    </label>
                                </li>
                                <li v-if="productStore.allCategories.length > 0"
                                    class="border-t border-slate-100 pt-2 mt-2">
                                    <button @click="
                                        productStore.filters.category =
                                        null;
                                    productStore.fetchProducts();
                                    "
                                        class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold transition-colors">
                                        Clear Filter
                                    </button>
                                </li>
                                <li v-else class="text-slate-400 italic">
                                    No categories found
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <PaginationLinks v-if="productStore.pagination.meta" :links="productStore.pagination.meta.links"
                        @page-change="onPageChange" />

                    <!-- Open Product Form -->
                    <button @click="openProductForm"
                        class="h-8 whitespace-nowrap justify-center rounded-full px-3 py-1 text-sm font-medium text-green-500 hover:text-white border border-slate-200 shadow-sm hover:bg-green-600 focus-visible:outline-none focus-visible:ring focus-visible:ring-green-300 transition-colors group">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </div>

                <!-- Table Data -->
                <table class="w-full text-sm text-left rtl:text-right text-body text-slate-500">
                    <thead class="text-sm text-body bg-slate-100 rounded">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium text-slate-500">
                                #
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium text-slate-500">
                                Product name
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium text-slate-500">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium text-slate-500">
                                Category
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium text-slate-500">
                                Price
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium text-slate-500">
                                Stock
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium text-slate-500">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="productStore.loading">
                            <td colspan="7" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400 italic">
                                    <i class="fas fa-circle-notch fa-spin text-2xl mb-2 text-indigo-500"></i>
                                    <span>Loading products...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="productStore.allProducts.length === 0">
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400 italic">
                                No products found matching your criteria.
                            </td>
                        </tr>
                        <tr v-else v-for="(product, index) in productStore.allProducts" :key="product.id"
                            class="bg-neutral-primary border-b border-slate-200 hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                {{ index + 1 }}
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                <div class="flex items-center">
                                    <img :src="product.image ||
                                        'https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80'
                                        " alt=""
                                        class="w-10 h-10 object-cover rounded-full shadow-sm border border-slate-100" />
                                    <span class="ml-2 font-semibold text-slate-700 decoration-indigo-500/30">{{
                                        product.name }}</span>
                                </div>
                            </th>
                            <td class="px-6 py-4">
                                <div :class="[
                                    'inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold tracking-wide transition-all duration-200',
                                    product.status === 'active'
                                        ? 'bg-green-50 text-green-700 ring-1 ring-green-200'
                                        : 'bg-red-50 text-red-700 ring-1 ring-red-200',
                                ]">
                                    <!-- Status Dot -->
                                    <span class="relative flex h-2.5 w-2.5">
                                        <span
                                            class="absolute inline-flex h-full w-full rounded-full opacity-75 animate-ping"
                                            :class="product.status === 'active'
                                                    ? 'bg-green-400'
                                                    : 'bg-red-400'
                                                "></span>

                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5" :class="product.status === 'active'
                                                ? 'bg-green-500'
                                                : 'bg-red-500'
                                            "></span>
                                    </span>

                                    <!-- Text -->
                                    <span class="capitalize">
                                        {{ product.status }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 italic text-slate-400">
                                {{
                                    product.category_id?.name || "Uncategorized"
                                }}
                            </td>
                            <td class="px-6 py-4 font-bold text-indigo-600">
                                {{ formatCurrency(product.price) }}
                            </td>
                            <td class="px-6 py-4">
                                <span :class="product.stock > 10
                                        ? 'text-green-600 bg-green-50'
                                        : 'text-amber-600 bg-amber-50'
                                    " class="font-medium px-2 py-0.5 rounded-full text-xs">
                                    {{ product.stock }} in stock
                                </span>
                                <button v-if="product.stock < 10" @click="openStockForm(product)"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold text-green-600 bg-green-50 border border-green-200 hover:bg-green-600 hover:text-white hover:border-green-600 transition-all duration-200 active:scale-95">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                    <span>Add Stock</span>
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button @click="openProductForm(product)"
                                        class="h-8 w-8 flex items-center justify-center rounded-full text-sm font-medium text-indigo-500 hover:text-white border border-slate-200 shadow-sm hover:bg-indigo-600 transition-all active:scale-90">
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                    <button @click="confirmDelete(product)"
                                        class="h-8 w-8 flex items-center justify-center rounded-full text-sm font-medium text-red-500 hover:text-white border border-slate-200 shadow-sm hover:bg-red-600 transition-all active:scale-90">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stock Form -->
        <StockForm :show="showModal" :product="selectedProduct" @close="showModal = false" />

        <!-- Product Form -->
        <ProductForm :show="showProductModal" :product="selectedProduct" @close="showProductModal = false"
            @submit="onProductSubmit" />

        <!-- Confirm Modal -->
        <ConfirmModal :show="confirmModalState.show" title="Delete Product" @confirm="handleDeleteProduct"
            @cancel="confirmModalState.show = false" />

        <!-- Alert Message -->
        <div class="fixed top-20 right-5 z-50">
            <AlertMsg :show="showAlert" :message="message" :type="alertType" :title="title" @close="hideMessage" />
        </div>

    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useUiStore } from "../../../api/stores/uiStore.js";
import { useProductStore } from "../../../api/stores/productStore.js";
import PaginationLinks from "../../../components/PaginationLinks.vue";
import StockForm from "./StockForm.vue";
import ProductForm from "./ProductForm.vue";
import ConfirmModal from "../../../components/messages/ConfirmModal.vue";
import AlertMsg from "../../../components/messages/AlertMsg.vue";
import Breadcrumb from "../../../components/Breadcrumb.vue";
import { useAlert } from "../../../composables/useAlert";
import { formatCurrency } from "../../../utils/formatters";

// ====== Store ====== \\
const ui = useUiStore();
const productStore = useProductStore();

// const stockDate = reactive({
//     product_id: null,
//     quantity: 1,
//     type: "In"
// })

// ====== State ====== \\
const isDropdownOpen = ref(false);
const showModal = ref(false);
const showProductModal = ref(false);
const selectedProduct = ref(null);

const confirmModalState = ref({
    show: false,
    product: null,
});

const { message, alertType, title, showAlert, showMessage, hideMessage } = useAlert();

// ====== Function ====== \\
const openStockForm = (product) => {
    selectedProduct.value = product;
    showModal.value = true;
};

const openProductForm = (product = null) => {
    selectedProduct.value = product;
    showProductModal.value = true;
};

const onProductSubmit = () => {
    showProductModal.value = false;
    productStore.fetchProducts();
};

// ====== Api Function ====== \\
const onPageChange = (page) => {
    productStore.filters.page = page;
    productStore.fetchProducts();
};

const confirmDelete = (product) => {
    confirmModalState.value.product = product;
    confirmModalState.value.show = true;
};

const handleDeleteProduct = async () => {
    const product = confirmModalState.value.product;
    if (!product) return;

    confirmModalState.value.show = false;

    try {
        await productStore.deleteProduct(product.id);

        showMessage("Product deleted successfully.", "delete", "Success");

        await productStore.fetchProducts();
    } catch (err) {
        showMessage(err.response?.data?.message || err.message || "Failed to delete product.", "error", "Error");
    } finally {
        confirmModalState.value.product = null;
    }
};

// ====== Life Cycle ====== \\
onMounted(() => {
    productStore.fetchProducts();
    productStore.fetchCategories();
});
</script>
