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
                        Register an account
                    </h1>
                    <form
                        class="space-y-4 md:space-y-6"
                        @submit.prevent="submit"
                    >
                        <div>
                            <label
                                for="name"
                                class="block mb-2 text-md font-medium text-gray-700"
                                >Name</label
                            >
                            <input
                                type="name"
                                name="name"
                                id="name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 transition focus:outline-none focus:ring-0"
                                placeholder="jonh@doe"
                                v-model="form.name"
                            />
                        </div>
                        <div>
                            <label
                                for="phone"
                                class="block mb-2 text-md font-medium text-gray-700"
                                >Phone</label
                            >
                            <input
                                type="phone"
                                phone="phone"
                                id="phone"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 transition focus:outline-none focus:ring-0"
                                placeholder="098 765 432 1"
                                v-model="form.phone"
                            />
                        </div>
                        <div>
                            <label
                                for="email"
                                class="block mb-2 text-md font-medium text-gray-700"
                                >Email</label
                            >
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 transition focus:outline-none focus:ring-0"
                                placeholder="name@company.com"
                                v-model="form.email"
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
                                v-model="form.password"
                            />
                        </div>
                        <div>
                            <label
                                for="password"
                                class="block mb-2 text-md font-medium text-gray-700"
                                >Confirm Password</label
                            >
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                placeholder="••••••••"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 transition focus:outline-none focus:ring-0"
                                v-model="form.password_confirmation"
                            />
                        </div>

                        <button
                            type="submit"
                            :disabled="loading"
                            :class="
                                loading
                                    ? 'bg-blue-400 cursor-not-allowed'
                                    : 'bg-blue-600 hover:bg-blue-700'
                            "
                            class="w-full text-white focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                        >
                            <span v-if="!loading"> Sign Up</span>

                            <div v-else>
                                <i
                                    class="fa-solid fa-spinner animate-spin mr-2 text-white"
                                ></i>
                                Registering...
                            </div>
                        </button>

                        <p
                            class="text-sm font-light text-gray-500 dark:text-gray-400"
                        >
                            Already have an account?
                            <RouterLink
                                :to="{ name: 'Login' }"
                                class="font-medium text-primary-600 cursor-pointer dark:text-primary-500"
                                >Sign In</RouterLink
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
</template>

<script setup>
import { ref, reactive } from "vue";
import authService from "../../api/services/auth/authService"; // adjust path if your alias is configured
import { useRouter } from "vue-router";

const router = useRouter();
const loading = ref(false);
const form = reactive({
    name: "",
    phone: "",
    email: "",
    password: "",
    password_confirmation: "",
    role_id: 2,
});

async function submit() {
    loading.value = true;
    try {
        await authService.register(form);
        // on success, navigate or show message
        router.replace({ name: "Login" });
    } catch (err) {
        console.error(err);
        // handle error, e.g. display a toast
    } finally {
        loading.value = false;
    }
}
</script>
