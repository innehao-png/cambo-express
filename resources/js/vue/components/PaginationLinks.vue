<template>
    <div
        v-if="links.length > 3"
        class="flex justify-center items-center gap-1 mt-6"
    >
        <button
            v-for="(link, index) in links"
            :key="index"
            :disabled="!link.url || link.active"
            @click="changePage(link.url)"
            class="min-w-10 px-3 py-2 text-sm font-medium rounded-lg transition-colors"
            :class="getLinkClass(link)"
        >
            <span v-html="link.label"></span>
        </button>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    links: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const emit = defineEmits(["page-change"]);

const getPageNumber = (url) => {
    if (!url) return null;
    try {
        return new URL(url).searchParams.get("page");
    } catch (error) {
        console.error("Invalid URL:", url);
        return null;
    }
};

const changePage = (url) => {
    const page = getPageNumber(url);
    if (page) {
        emit("page-change", page);
    }
};

const getLinkClass = (link) => {
    if (link.active) {
        return "bg-blue-600 text-white cursor-default";
    }
    if (!link.url) {
        return "bg-gray-100 text-gray-400 cursor-not-allowed";
    }
    return "bg-white text-gray-700 hover:bg-gray-100 border border-gray-300 hover:border-gray-400";
};
</script>
