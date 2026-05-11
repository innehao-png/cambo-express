<template>
    <nav class="flex py-3 mb-2" aria-label="Breadcrumb">
        <ol class="inline-flex items-center px-4 py-2 space-x-1 md:space-x-2 bg-white rounded-full shadow-sm border border-slate-100 transition-all hover:shadow-md">
            <li class="inline-flex items-center">
                <RouterLink
                    to="/"
                    class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors duration-200 group"
                >
                    <i class="fa-solid fa-house text-slate-400 group-hover:text-indigo-500 transition-colors mr-2"></i>
                    Home
                </RouterLink>
            </li>
            <li v-for="(item, index) in items" :key="index">
                <div class="flex items-center text-sm font-medium">
                    <span class="mx-1 text-slate-300">/</span>
                    <RouterLink
                        v-if="item.to"
                        :to="item.to"
                        class="ml-1 inline-flex items-center text-slate-500 hover:text-indigo-600 transition-colors duration-200"
                    >
                        {{ item.label }}
                    </RouterLink>
                    <span
                        v-else
                        class="ml-1 inline-flex items-center font-semibold text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-full"
                    >
                        {{ item.label }}
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</template>

<script setup>
defineProps({
    items: {
        type: Array, // [{ label: 'Admin', to: '/admin' }, { label: 'Products' }]
        required: true,
        default: () => []
    }
});
</script>
