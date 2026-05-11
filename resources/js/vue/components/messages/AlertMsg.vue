<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform translate-x-full opacity-0"
        enter-to-class="transform translate-x-0 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="transform translate-x-0 opacity-100"
        leave-to-class="transform translate-x-full opacity-0"
    >
        <div
            v-if="visible"
            :class="[
                'flex items-center p-4 rounded-lg shadow-lg min-w-75 max-w-md',
                alertClasses,
            ]"
            role="alert"
        >
            <!-- Icon -->
            <div class="shrink-0 mr-3">
                <i :class="['text-xl', iconClass]"></i>
            </div>

            <!-- Content -->
            <div class="flex-1">
                <p v-if="title" class="font-semibold text-sm mb-0.5">
                    {{ title }}
                </p>
                <p class="text-sm">{{ message }}</p>
            </div>

            <!-- Close Button -->
            <button
                @click="close"
                class="shrink-0 ml-3 text-lg font-bold opacity-70 hover:opacity-100 transition"
                aria-label="Close"
            >
                ×
            </button>
        </div>
    </Transition>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from "vue";

const props = defineProps({
    type: {
        type: String,
        default: "create",
    },
    title: {
        type: String,
        default: "",
    },
    message: {
        type: String,
        required: true,
    },
    show: {
        type: Boolean,
        default: true,
    },
    duration: {
        type: Number,
        default: 3000,
    },
});

const emit = defineEmits(["close"]);

const visible = ref(props.show);
let timer = null;

const close = () => {
    visible.value = false;
    clearTimer();
    emit("close");
};

const clearTimer = () => {
    if (timer) {
        clearTimeout(timer);
        timer = null;
    }
};

const startTimer = () => {
    clearTimer();
    if (props.duration > 0) {
        timer = setTimeout(close, props.duration);
    }
};

watch(
    () => props.show,
    (newVal) => {
        visible.value = newVal;
        if (newVal) {
            startTimer();
        } else {
            clearTimer();
        }
    },
);

onMounted(() => {
    if (props.show) startTimer();
});

onUnmounted(() => {
    clearTimer();
});

const alertClasses = computed(() => {
    const classes = {
        create: "text-green-800 bg-green-50 border-l-4 border-green-500",
        delete: "text-red-800 bg-red-50 border-l-4 border-red-500",
        update: "text-yellow-800 bg-yellow-50 border-l-4 border-yellow-500",
        info: "text-blue-800 bg-blue-50 border-l-4 border-blue-500",
        error: "text-red-800 bg-red-50 border-l-4 border-red-500",
    };
    return classes[props.type] || classes.info;
});

const iconClass = computed(() => {
    const icons = {
        create: "fa-solid fa-circle-check text-green-600",
        delete: "fa-solid fa-trash text-red-600",
        update: "fa-solid fa-wrench text-yellow-600",
        info: "fa-solid fa-circle-info text-blue-600",
        error: "fa-solid fa-circle-xmark text-red-600",
    };
    return icons[props.type] || icons.info;
});
</script>
