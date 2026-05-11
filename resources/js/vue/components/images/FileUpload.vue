<template>
    <div class="">
        <!-- Image Upload Section -->
        <div class="mb-6 flex justify-center">
            <div class="flex flex-col items-center">
                <div class="relative">
                    <div
                        v-if="previewUrl"
                        class="w-32 h-32 rounded-full overflow-hidden border-4 border-blue-500 shadow-md"
                    >
                        <img
                            alt="Preview"
                            :src="previewUrl"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div
                        v-else
                        class="w-32 h-32 rounded-full bg-gray-100 border-4 border-dashed border-gray-300 flex items-center justify-center"
                    >
                        <i class="fa-solid fa-user text-4xl text-gray-400"></i>
                    </div>
                    <label
                        class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-2 cursor-pointer shadow-lg transition"
                    >
                        <i class="fa-solid fa-camera"></i>
                        <input
                            @change="onFileChange"
                            ref="fileInput"
                            type="file"
                            accept=".jpg, .jpeg, .png, image/jpeg, image/png"
                            class="hidden"
                        />
                    </label>
                </div>
                <p class="mt-2 text-sm text-gray-500">
                    {{ $t("form.choose_img") }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    avatarUrl: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["update:file"]);

const selectedFile = ref(null);
const previewUrl = ref(null);

watch(
    () => props.avatarUrl,
    (val) => {
        if (val) {
            previewUrl.value = val;
        }
    },
    { immediate: true }
);

function onFileChange(event) {
    const file = event.target.files[0];
    if (!file) return;

    const validTypes = ["image/jpeg", "image/png", "image/jpg"];
    if (!validTypes.includes(file.type)) {
        alert("Only JPG/PNG files are allowed.");
        return;
    }

    selectedFile.value = file;
    emit("update:file", file);

    const reader = new FileReader();
    reader.onload = (e) => {
        previewUrl.value = e.target.result;
    };
    reader.readAsDataURL(file);
}
</script>
