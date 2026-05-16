<template>
    <div v-if="show">
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        >
            <div
                class="relative w-full max-w-2xl max-h-full overflow-y-auto bg-white rounded-lg shadow-xl"
            >
                <!-- Modal Header -->
                <div
                    class="flex items-center justify-between p-4 border-b border-gray-200"
                >
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-gray-500"
                            >INVENTORY</span
                        >
                        <h3 class="text-xl font-semibold text-gray-900">
                            {{ isEdit ? "Edit Product" : "Add New Product" }}
                        </h3>
                    </div>
                    <button
                        @click="emit('close')"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            ></path>
                        </svg>
                    </button>
                </div>
                <!-- Modal Body -->
                <div class="p-6">
                    <form id="submit-create-product"  @submit.prevent="submitForm">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label
                                    for="name"
                                    class="block mb-2 text-sm font-medium text-gray-900"
                                    >Product Name</label
                                >
                                <input
                                    v-model="form.name"
                                    type="text"
                                    name="name"
                                    id="name"
                                    class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 outline-0"
                                    placeholder="e.g. AI Robot Arm"
                                />
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label
                                    for="category_id"
                                    class="block mb-2 text-sm font-medium text-gray-900"
                                    >Category</label
                                >

                                <select v-model="form.category_id"
                                    id="category_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                >
                                    <option>Select category</option>
                                    <option v-for="category in productStore.allCategories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                </select>
                            </div>
                            <div class="col-span-6">
                                <label
                                    for="description"
                                    class="block mb-2 text-sm font-medium text-gray-900"
                                    >Description</label
                                >
                                <textarea
                                    v-model="form.description"
                                    id="description"
                                    rows="4"
                                    class="outline-0 block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Write product details here..."
                                ></textarea>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label
                                    for="price"
                                    class="block mb-2 text-sm font-medium text-gray-900"
                                    >Price ($)</label
                                >
                                <input
                                    v-model="form.price"
                                    type="number"
                                    name="price"
                                    id="price"
                                    min="0.1"
                                    class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 outline-0"
                                    placeholder="2999"
                                />
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label
                                    for="stock"
                                    class="block mb-2 text-sm font-medium text-gray-900"
                                    >Stock</label
                                >
                                <input
                                    v-model="form.stock"
                                    type="number"
                                    name="stock"
                                    id="stock"
                                    class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 outline-0"
                                    placeholder="50"
                                />
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label
                                    for="discount"
                                    class="block mb-2 text-sm font-medium text-gray-900"
                                    >Discount (%)</label
                                >
                                <input
                                    v-model="form.discount"
                                    type="number"
                                    name="discount"
                                    id="discount"
                                    class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 outline-0"
                                    placeholder="10"
                                />
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label
                                    for="status"
                                    class="block mb-2 text-sm font-medium text-gray-900"
                                    >Status</label
                                >
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <input
                                            v-model="form.status"
                                            id="status-active"
                                            type="radio"
                                            value="active"
                                            name="status"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"
                                        />
                                        <label
                                            for="status-active"
                                            class="ml-2 text-sm font-medium text-gray-900"
                                            >Active</label
                                        >
                                    </div>
                                    <div class="flex items-center">
                                        <input
                                            v-model="form.status"
                                            id="status-inactive"
                                            type="radio"
                                            value="inactive"
                                            name="status"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"
                                        />
                                        <label
                                            for="status-inactive"
                                            class="ml-2 text-sm font-medium text-gray-900"
                                            >Inactive</label
                                        >
                                    </div>
                                </div>
                            </div>
                            <div
                                class="col-span-6 sm:col-span-3 sm:flex sm:flex-col sm:items-end"
                            >
                                <label
                                    v-if="!imagePreview"
                                    class="block mb-2 text-sm font-medium text-gray-900 w-full sm:text-right"
                                    for="image"
                                >
                                    Product Image
                                </label>
                                <div class="flex flex-col sm:items-end w-full">
                                    <input
                                        hidden
                                        id="image"
                                        type="file"
                                        accept="image/*"
                                        @change="handleImageChange"
                                    />
                                    <label
                                        v-if="!imagePreview"
                                        for="image"
                                        class="inline-flex items-center justify-center text-sm text-gray-900 border border-gray-300 rounded-md px-3 py-2 cursor-pointer bg-gray-50 hover:bg-gray-100 focus:outline-none transition-colors"
                                    >
                                        <i class="fa-regular fa-image mr-2"></i>
                                        Upload image
                                    </label>
                                    <label
                                        v-else
                                        for="image"
                                        class="cursor-pointer relative w-32 h-32 rounded border border-gray-200 shadow-sm overflow-hidden shrink-0"
                                    >
                                        <img
                                            :src="imagePreview"
                                            alt="Image Preview"
                                            class="w-full h-full object-cover"
                                        />
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Modal Footer -->
                <div
                    class="flex items-center justify-end grow p-6 space-x-2 border-t border-gray-200 rounded-b "
                >
                    <button
                        form="submit-create-product"
                        :disabled="productStore.loading"
                        type="submit"
                        class="cursor-pointer text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                    >
                        {{ productStore.loading ? "Saving..." : "Save Product"}}
                    </button>
                    <button
                        type="button"
                        @click="emit('close')"
                        class="cursor-pointer text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Message -->
    <div class="fixed top-20 right-6 z-50">
        <AlertMsg
            v-if="showAlert"
            :type="alertType"
            :title="title"
            :message="message"
            @close="hideMessage"
        />
    </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from "vue";
import { useProductStore } from "../../../api/stores/productStore";
import AlertMsg from "../../../components/messages/AlertMsg.vue";
import { useAlert } from "../../../composables/useAlert";

//================ PROPS / EMITS ================//
const props = defineProps({
    show: Boolean,
    product: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["close", "submit"]);

//================ STORE ================//
const productStore = useProductStore();

//================ STATE ================//
const { message, alertType, title, showAlert, showMessage, hideMessage } = useAlert();
const imagePreview = ref(null);

const defaultForm = () => ({
    name: "",
    category_id: 1,
    description: null,
    price: 0,
    stock: 1,
    discount: 0,
    status: "active",
    image: null,
});

const form = reactive(defaultForm());

//================ COMPUTED ================//
const isEdit = computed(() => !!props.product?.id);

//================ METHODS ================//

// Handle image upload preview
const handleImageChange = (event) => {
    const file = event.target.files[0];

    form.image = file || null;
    imagePreview.value = file ? URL.createObjectURL(file) : null;
};

// Reset form
const resetForm = () => {
    Object.assign(form, defaultForm());
    imagePreview.value = null;
};

// Fill form for edit
const fillForm = (product) => {
    Object.assign(form, {
        name: product.name || "",
        category_id:
            typeof product.category_id === "object"
                ? product.category_id?.id
                : product.category_id || 1,
        description: product.description || null,
        price: product.price || 0,
        stock: product.stock || 0,
        discount: product.discount || 0,
        status: product.status || "active",
        image: null,
    });

    imagePreview.value = product.image || null;
};

// Submit form
const submitForm = async () => {
    try {
        let res;

        if (isEdit.value) {
            res = await productStore.updateProduct(props.product.id, form);
            showMessage(res?.data?.message || "Product updated successfully!", "update", "Success!");
        } else {
            res = await productStore.createProduct(form);
            showMessage(res?.data?.message || "Product created successfully!", "create", "Success!");
        }

        emit("submit");
        resetForm();
    } catch (error) {
        console.error("Failed to save product:", error);
        showMessage(error.response?.data?.message || error.message || "Failed to save product.", "error", "Error");
    }
};

//================ WATCHERS ================//
watch(
    () => [props.show, props.product],
    ([show, product]) => {
        if (!show) return;

        if (product?.id) {
            fillForm(product);
        } else {
            resetForm();
        }
    },
    { immediate: true }
);

//================ LIFECYCLE ================//
onMounted(() => {
    productStore.fetchCategories();
});
</script>
