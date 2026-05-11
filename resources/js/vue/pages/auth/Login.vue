<template>
    <section class="bg-gray-50">
        <div
            class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0"
        >
            <div
                class="w-full bg-white rounded-lg shadow-md md:mt-0 sm:max-w-md xl:p-0"
            >
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1
                        class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl"
                    >
                        Sign in to your account
                    </h1>
                    <form
                        class="space-y-4 md:space-y-6"
                        @submit.prevent="submit"
                    >
                        <div>
                            <label
                                for="phone"
                                class="block mb-2 text-md font-medium text-gray-700"
                                >Phone</label
                            >
                            <input
                                type="text"
                                name="phone"
                                id="phone"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 transition focus:outline-none focus:ring-0"
                                placeholder="name@company.com"
                                v-model="phone"
                            />
                        </div>
                        <div>
                            <label
                                for="password"
                                class="block mb-2 text-md font-medium text-gray-700"
                                >Password</label
                            >
                            <input
                                type="password"
                                name="password"
                                id="password"
                                placeholder="••••••••"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 transition focus:outline-none focus:ring-0"
                                v-model="password"
                            />
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input
                                        id="remember"
                                        :value="1"
                                        aria-describedby="remember"
                                        type="checkbox"
                                        class="w-4 h-4 border border-gray-300 rounded"
                                    />
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="remember" class="text-gray-700"
                                        >Remember me</label
                                    >
                                </div>
                            </div>
                            <RouterLink
                                :to="{ name: 'ForgotPassword' }"
                                class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500"
                                >Forgot password?
                        </RouterLink>
                        </div>
                        <button
                            type="submit"
                            :disabled="loading"
                            class="cursor-pointer w-full text-white bg-blue-600 hover:bg-blue-700 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                        >
                            <span v-if="!loading">Sign in</span>
                            <div v-else>
                                <i
                                    class="fa-solid fa-spinner animate-spin mr-2 text-white"
                                ></i>
                                Siging in...
                            </div>
                        </button>
                        <p
                            class="text-sm font-light text-gray-500 dark:text-gray-400"
                        >
                            Don't have an account yet?
                            <RouterLink
                                :to="{ name: 'Register' }"
                                class="font-medium text-primary-600 cursor-pointer dark:text-primary-500"
                                >Sign up</RouterLink
                            >
                            or back
                            <RouterLink
                                :to="{ name: 'Home' }"
                                class="font-medium text-primary-600 cursor-pointer dark:text-primary-500"
                                >Home</RouterLink
                            >
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <div class="fixed top-6 left-1/2 -translate-x-1/2">
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
import { ref } from "vue";
import authService from "../../api/services/auth/authService";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../api/stores/authStore";
import AlertMsg from "../../components/messages/AlertMsg.vue";
import { useAlert } from "../../composables/useAlert";

const authStore = useAuthStore();
const router = useRouter();
const phone = ref("");
const password = ref("");
const loading = ref(false);

const { message, alertType, title, showAlert, showMessage, hideMessage } = useAlert();

async function submit() {
    loading.value = true;
    try {
        await authStore.login({
            phone: phone.value,
            password: password.value,
        });
        // redirect after login


        // console.log("isAdmin", authStore.isAdmin);

        // console.log(authStore.isAdmin);


        const intendedUrl = localStorage.getItem("intended_url");
        localStorage.removeItem("intended_url");

        if (authStore.isAdmin) {
            router.replace({ name: "Dashboard" });
        } else {
            router.replace(intendedUrl || "/");
        }
    } catch (err) {
        console.error(err);
        showMessage(err.response?.data?.message || err.message || "Login failed", "error", "Error!");
    } finally {
        loading.value = false;
    }
}
</script>
