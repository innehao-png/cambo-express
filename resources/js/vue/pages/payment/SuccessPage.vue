<!-- SuccessPage.vue -->
<script setup>
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

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
    <div class="text-center py-20">
        <div v-if="loading">Loading...</div>
        <div v-else>
            <h1 class="text-3xl font-bold text-green-600">
                🎉 Payment Successful!
            </h1>
            <p v-if="order" class="mt-4 text-gray-600">
                Order #{{ order.tran_id }} — ${{ order.total_amount }}
            </p>
            <router-link to="/orders" class="btn-primary mt-6">
                View My Orders
            </router-link>
        </div>
    </div>
</template>
