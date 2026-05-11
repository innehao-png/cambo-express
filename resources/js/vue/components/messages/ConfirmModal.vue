<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
        <!-- Modal Box -->
        <div
            class="bg-white w-full max-w-md rounded-xl shadow-lg p-6 animate-scaleIn"
        >
            <!-- Icon -->
            <div class="flex justify-center mb-4">
                <div
                    class="w-12 h-12 flex items-center justify-center rounded-full"
                    :class="iconBg"
                >
                    <i
                        :class="iconColor"
                        class="fa-solid fa-circle-exclamation text-2xl"
                    ></i>
                </div>
            </div>

            <!-- Title -->
            <h2 class="text-2xl font-semibold text-center text-gray-800">
                {{ title }}
            </h2>

            <!-- Message -->
            <p class="mt-2 text-xl text-center text-gray-600">
                {{ message }}
            </p>

            <!-- Actions -->
            <div class="mt-6 flex justify-end gap-3">
                <button
                    @click="$emit('cancel')"
                    class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition"
                >
                    {{ cancelText }}
                </button>

                <button
                    @click="$emit('confirm')"
                    class="px-4 py-2 text-sm rounded-lg text-white transition"
                    :class="confirmBtn"
                >
                    {{ confirmText }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    show: Boolean,
    title: {
        type: String,
        default: "Confirm Action",
    },
    message: {
        type: String,
        default: "Are you sure you want to delete this?",
    },
    confirmText: {
        type: String,
        default: "Yes",
    },
    cancelText: {
        type: String,
        default: "No",
    },
    confirmBtn: {
        type: String,
        default: "bg-red-500 hover:bg-red-700",
    },
    iconBg: {
        type: String,
        default: "bg-red-100",
    },
    iconColor: {
        type: String,
        default: "text-red-600",
    },
});
</script>

<style scoped>
@keyframes scaleIn {
    from {
        transform: scale(0.9);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.animate-scaleIn {
    animation: scaleIn 0.2s ease-out;
}
</style>
