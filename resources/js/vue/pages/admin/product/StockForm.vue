<template>
    <!-- Modal -->
    <div v-if="show" class="fixed inset-0 bg-opacity-50 z-50 flex justify-center items-center w-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-md max-h-full">
                <!-- Modal content -->
                <div class="relative border border-zinc-200 rounded-3xl shadow-sm p-4 md:p-6 bg-white">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between border-b border-zinc-200 pb-4 md:pb-5">
                        <h3 class="text-lg font-medium text-zinc-900">
                            Update Product Stock
                        </h3>
                        <button @click="emit('close')" type="button"
                            class="text-zinc-500 bg-transparent hover:bg-zinc-100 hover:text-zinc-900 rounded-lg text-sm w-9 h-9 ms-auto inline-flex justify-center items-center transition-colors">
                            <i class="fas fa-x"></i>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <form @submit.prevent="handleAddStock">
                        <div class="grid gap-4 grid-cols-2 py-4 md:py-6">
                            <!-- Product -->
                            <div class="col-span-2">
                                <label for="product" class="block mb-2.5 text-sm font-medium text-zinc-900">
                                    Product
                                </label>
                                <input
                                    :value="props.product?.name"
                                    id="product"
                                    readonly
                                    class="block w-full px-3 py-2.5 bg-zinc-50 outline-none border border-zinc-200 text-zinc-500 text-sm rounded-xl shadow-xs focus:ring-2 focus:ring-blue-500 transition-shadow cursor-not-allowed"
                                />
                            </div>
                            <!-- Quantity -->
                            <div class="col-span-2 sm:col-span-1">
                                <label for="quantity" class="block mb-2.5 text-sm font-medium text-zinc-900">
                                    Quantity
                                </label>
                                <input v-model="form.quantity" type="number" name="quantity" id="quantity" min="1"
                                    class="block w-full px-3 py-2.5 bg-zinc-100 outline-none border border-zinc-200 text-zinc-900 text-sm rounded-xl shadow-xs placeholder:text-zinc-400 focus:ring-2 focus:ring-blue-500 transition-shadow"
                                    placeholder="1" />
                            </div>
                            <!-- Type -->
                            <div class="col-span-2 sm:col-span-1">
                                <label for="type" class="block mb-2.5 text-sm font-medium text-zinc-900">
                                    Type
                                </label>
                                <select v-model="form.type" id="type"
                                    class="block w-full px-3 py-2.5 bg-zinc-100 outline-none border border-zinc-200 text-zinc-900 text-sm rounded-xl shadow-xs placeholder:text-zinc-400 focus:ring-2 focus:ring-blue-500 transition-shadow">
                                    <option selected value="">
                                        Select type
                                    </option>
                                    <option value="in">In</option>
                                    <option value="out">Out</option>
                                </select>
                            </div>
                            <!-- Description -->
                            <div class="col-span-2">
                                <label for="description" class="block mb-2.5 text-sm font-medium text-zinc-900">
                                    Description
                                </label>
                                <textarea v-model="form.note" id="description" rows="4"
                                    class="block w-full px-3 py-2.5 bg-zinc-100 outline-none border border-zinc-200 text-zinc-900 text-sm rounded-xl shadow-xs placeholder:text-zinc-400 focus:ring-2 focus:ring-blue-500 transition-shadow resize-none"
                                    placeholder="Write some notes here or leave it empty"></textarea>
                            </div>
                        </div>
                        <!-- Footer actions -->
                        <div class="flex items-center gap-3 border-t border-zinc-200 pt-4 md:pt-6">
                            <button :disabled="productStore.loading" type="submit"
                                class="inline-flex items-center text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 border border-transparent focus:ring-4 focus:ring-blue-200 shadow-xs font-medium leading-5 rounded-lg text-sm px-4 py-2.5 focus:outline-none transition-colors">
                                {{ productStore.loading ? "Adding" : "Add" }}
                            </button>
                            <button type="button" @click="emit('close')"
                                class="inline-flex items-center text-zinc-600 bg-zinc-100 border border-zinc-200 hover:bg-zinc-200 hover:text-zinc-900 focus:ring-4 focus:ring-zinc-100 shadow-xs font-medium leading-5 rounded-lg text-sm px-4 py-2.5 focus:outline-none transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <!-- Alert Messages (Persists independently of the modal) -->
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
import { useProductStore } from "../../../api/stores/productStore";
import { reactive, watch } from "vue";
import AlertMsg from "../../../components/messages/AlertMsg.vue";
import { useAlert } from "../../../composables/useAlert";

//====== Props Emit ====== \\
const props = defineProps({
    show: Boolean,
    product: {
        type: Object,
        default: () => ({})
    }
});
const emit = defineEmits(["close", "submit"]);

//====== State ====== \\
const productStore = useProductStore();
const form = reactive({
    product_id: null,
    quantity: 1,
    type: "",
    note: "",
});

const { message, alertType, title, showAlert, showMessage, hideMessage } = useAlert();

//======Helper Function ====== \\
const resetForm = () => {
    form.product_id = null;
    form.quantity = 1;
    form.type = "";
    form.note = "";
}

// watch the changes of show values
watch(() => props.show, (val) => {
    if (val) {
        if(props.product){
            form.product_id = props.product.id
        }
    }else {
        resetForm();
    }
}, { immediate: true });


//====== API Function ====== \\
async function handleAddStock() {
    try {
        const res = await productStore.addStock(form);

        showMessage(res.message, "create", "Success!");
        emit("close");
    } catch (error) {
        showMessage(error?.response?.data?.message || "Something went wrong!", "error", "Error!");
    }
}

</script>
