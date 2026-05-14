<!-- SuccessPage.vue -->
<script setup>
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import Skeleton from "../../components/ui/Skeleton.vue";

const route = useRoute();
const order = ref(null);
const loading = ref(true);

onMounted(async () => {
    try {
        const { data } = await axios.get(
            `/api/v1/orders/tran/${route.query.tran_id}`,
        );
        order.value = data;
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div class="max-w-md mx-auto text-center py-20 px-4">
        <div v-if="loading" class="space-y-4">
            <Skeleton className="mx-auto" width="4rem" height="4rem" circle />
            <Skeleton className="mx-auto" width="80%" height="2rem" />
            <Skeleton className="mx-auto" width="60%" height="1.5rem" />
            <Skeleton className="mx-auto" width="100%" height="3rem" />
        </div>
        <div v-else>
            <div class="mb-6">
                <i class="fa-solid fa-circle-check text-green-500 text-6xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">
                Payment Successful!
            </h1>
            <p v-if="order" class="mt-4 text-gray-600">
                Order #{{ order.tran_id }} — ${{ order.total_amount }}
            </p>
            <router-link :to="{ name: 'Orders' }" 
                class="inline-block bg-green-600 hover:bg-green-700 text-white font-medium px-8 py-3 rounded-xl transition mt-8 w-full">
                View My Orders
            </router-link>
        </div>
    </div>
</template>
