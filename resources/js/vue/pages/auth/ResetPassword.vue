<!-- ResetPassword.vue -->
<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg">
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
        Reset Password
      </h2>

      <form @submit.prevent="handleSubmit" class="space-y-5">
        <!-- Email -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Email Address
          </label>
          <input
            type="email"
            v-model="form.email"
            required
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
            placeholder="Enter your email"
          />
        </div>

        <!-- Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            New Password
          </label>
          <input
            type="password"
            v-model="form.password"
            required
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
            placeholder="Enter new password"
          />
        </div>

        <!-- Password Confirmation -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Confirm Password
          </label>
          <input
            type="password"
            v-model="form.password_confirmation"
            required
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
            placeholder="Confirm new password"
          />
        </div>

        <!-- Hidden Token Field -->
        <input type="hidden" v-model="form.token" />

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
        >
          {{ loading ? "Resetting..." : "Reset Password" }}
        </button>

        <!-- Success -->
        <p v-if="successMessage" class="text-green-600 text-sm text-center">
          {{ successMessage }}
        </p>

        <!-- Error -->
        <p v-if="errorMessage" class="text-red-600 text-sm text-center">
          {{ errorMessage }}
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import authService from "../../api/services/auth/authService";

const route = useRoute();
const router = useRouter();

const loading = ref(false);
const successMessage = ref("");
const errorMessage = ref("");

const form = ref({
  email: "",
  password: "",
  password_confirmation: "",
  token: "",
});

onMounted(() => {
  form.value.token = route.query.token || "";
  form.value.email = route.query.email || '';
});

const handleSubmit = async () => {
  loading.value = true;
  successMessage.value = "";
  errorMessage.value = "";

  try {
    const res = await authService.resetPassword(form.value);
    successMessage.value =
      res.data?.message || "Password reset successfully.";

    setTimeout(() => {
      router.push("/login");
    }, 2000);
  } catch (error) {
    errorMessage.value =
      error.response?.data?.message || "Password reset failed.";
  } finally {
    loading.value = false;
  }
};
</script>
