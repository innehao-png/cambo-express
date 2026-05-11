<!-- FailedPage.vue -->
<template>
    <div class="text-center py-20">
        <h1 class="text-3xl font-bold text-red-600">❌ Payment Failed</h1>
        <p class="mt-4 text-gray-600">
            Something went wrong. Please try again.
        </p>
        <router-link to="/cart" class="btn-primary mt-6">
            Back to Cart
        </router-link>
    </div>
</template>
