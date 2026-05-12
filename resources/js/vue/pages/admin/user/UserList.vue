<template>
    <div class="mt-16 p-6 bg-gray-50 min-h-screen transition-all duration-300"
        :class="ui.sidebarOpen ? 'ml-64' : 'ml-0'">

        <!-- Error Alert -->
        <div v-if="error" class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4 flex items-center gap-3">
            <div class="w-5 h-5 flex-shrink-0 text-red-600">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-red-800">{{ error }}</p>
            </div>
        </div>

        <!-- Success Alert -->
        <div v-if="success" class="mb-6 bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center gap-3">
            <div class="w-5 h-5 flex-shrink-0 text-green-600">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-green-800">{{ success }}</p>
        </div>

        <nav class="flex py-3 mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center px-4 py-2 space-x-1 md:space-x-2 bg-white rounded-full shadow-sm border border-slate-100 transition-all hover:shadow-md">
                <li class="inline-flex items-center">
                    <a href="#" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors duration-200 group">
                        <i class="fa-solid fa-house text-slate-400 group-hover:text-indigo-500 transition-colors mr-2"></i>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <span class="mx-1 text-slate-300">/</span>
                        <a href="#" class="ml-1 inline-flex items-center text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors duration-200">
                            Admin
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <span class="mx-1 text-slate-300">/</span>
                        <span class="ml-1 inline-flex items-center text-sm font-semibold text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-full">
                            Users
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="shadow-md rounded-lg p-6">
            <div class="relative overflow-x-auto shadow-xs rounded">
                <!-- Header Section -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
                        <p class="text-slate-500 text-sm mt-1">Manage users, roles, and permissions</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="refreshData" class="h-10 px-4 rounded-lg bg-slate-500 text-white hover:bg-slate-600 transition-colors font-medium text-sm flex items-center gap-2">
                            <i class="fas fa-sync" :class="loading && 'animate-spin'"></i>
                            Refresh
                        </button>
                        <button @click="openCreateModal" class="h-10 px-4 rounded-lg bg-green-500 text-white hover:bg-green-600 transition-colors font-medium text-sm flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Add User
                        </button>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <input v-model="searchQuery" type="text" placeholder="Search by name, email, or phone..."
                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>
                    <div>
                        <select v-model="filterRole" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <div>
                        <select v-model="filterStatus" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Status</option>
                            <option value="enable">Active</option>
                            <option value="disable">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <button @click="applyFilters" class="w-full h-10 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                            Apply Filters
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="flex justify-center py-12">
                    <div class="text-center">
                        <div class="w-12 h-12 border-4 border-slate-200 border-t-indigo-500 rounded-full animate-spin mx-auto mb-4"></div>
                        <p class="text-slate-600 font-semibold">Loading users...</p>
                    </div>
                </div>

                <!-- Users Table -->
                <table v-else class="w-full text-sm text-left rtl:text-right text-slate-500">
                    <thead class="text-sm text-slate-600 bg-slate-100 rounded">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">
                                #
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                User
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Phone
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Role
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Joined
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Empty State -->
                        <tr v-if="users.length === 0">
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fas fa-users text-4xl text-slate-300"></i>
                                    <p class="text-slate-500 font-medium">No users found</p>
                                </div>
                            </td>
                        </tr>

                        <!-- User Rows -->
                        <tr v-for="(user, index) in users" :key="user.id" class="bg-white border-b border-slate-200 hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900">
                                {{ ((currentPage - 1) * perPage) + index + 1 }}
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-slate-900 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <span>{{ user.name }}</span>
                                </div>
                            </th>
                            <td class="px-6 py-4">
                                {{ user.email }}
                            </td>
                            <td class="px-6 py-4">
                                {{ user.phone || "N/A" }}
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'px-3 py-1 rounded-full text-xs font-medium',
                                    user.role?.name === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'
                                ]">
                                    {{ user.role?.name || "N/A" }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'px-3 py-1 rounded-full text-xs font-medium',
                                    user.status === 'enable' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                ]">
                                    {{ user.status === 'enable' ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                {{ formatDate(user.created_at) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button @click="openEditModal(user)"
                                        class="px-3 py-1 rounded-lg text-sm font-medium text-indigo-600 hover:bg-indigo-50 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="confirmDelete(user)"
                                        class="px-3 py-1 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div v-if="users.length > 0 && !loading" class="mt-6 flex items-center justify-between">
                    <div class="text-sm text-slate-600">
                        Showing <span class="font-semibold">{{ ((currentPage - 1) * perPage) + 1 }}</span> to
                        <span class="font-semibold">{{ Math.min(currentPage * perPage, totalUsers) }}</span> of
                        <span class="font-semibold">{{ totalUsers }}</span> users
                    </div>
                    <div class="flex gap-2">
                        <button @click="previousPage" :disabled="currentPage === 1"
                            class="px-4 py-2 rounded-lg border border-slate-200 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            Previous
                        </button>
                        <div class="flex items-center gap-1">
                            <button v-for="page in totalPages" :key="page" @click="currentPage = page"
                                :class="[
                                    'px-3 py-2 rounded-lg border',
                                    currentPage === page ? 'bg-indigo-600 text-white border-indigo-600' : 'border-slate-200 hover:bg-slate-50'
                                ]">
                                {{ page }}
                            </button>
                        </div>
                        <button @click="nextPage" :disabled="currentPage === totalPages"
                            class="px-4 py-2 rounded-lg border border-slate-200 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-screen overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-slate-200 p-6 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-slate-900">
                        {{ editingUser ? 'Edit User' : 'Create New User' }}
                    </h2>
                    <button @click="closeModal" class="text-slate-500 hover:text-slate-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form @submit.prevent="saveUser" class="p-6 space-y-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Name *</label>
                        <input v-model="formData.name" type="text" required
                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email *</label>
                        <input v-model="formData.email" type="email" required
                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                        <input v-model="formData.phone" type="text"
                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Address</label>
                        <textarea v-model="formData.address" rows="2"
                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Role *</label>
                        <select v-model="formData.role_id" required
                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select Role</option>
                            <option value="1">Admin</option>
                            <option value="2">Customer</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Status *</label>
                        <select v-model="formData.status" required
                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="enable">Active</option>
                            <option value="disable">Inactive</option>
                        </select>
                    </div>

                    <!-- Password (only for create) -->
                    <div v-if="!editingUser">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Password *</label>
                        <input v-model="formData.password" type="password" required
                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <!-- Form Actions -->
                    <div class="pt-4 flex gap-2">
                        <button type="button" @click="closeModal"
                            class="flex-1 px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 font-medium transition-colors">
                            Cancel
                        </button>
                        <button type="submit" :disabled="submitting"
                            class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-colors">
                            {{ submitting ? 'Saving...' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteConfirm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                            <i class="fas fa-exclamation text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Delete User?</h3>
                            <p class="text-slate-600">Are you sure you want to delete <span class="font-semibold">{{ userToDelete?.name }}</span>?</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600 mb-6">This action cannot be undone.</p>
                    <div class="flex gap-3">
                        <button @click="showDeleteConfirm = false"
                            class="flex-1 px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 font-medium">
                            Cancel
                        </button>
                        <button @click="deleteUser" :disabled="submitting"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 font-medium">
                            {{ submitting ? 'Deleting...' : 'Delete' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useUiStore } from "../../../api/stores/uiStore.js";
import adminService from "../../../api/services/admin/adminService.js";

const ui = useUiStore();

// Data
const users = ref([]);
const loading = ref(false);
const submitting = ref(false);
const error = ref(null);
const success = ref(null);

// Pagination
const currentPage = ref(1);
const perPage = ref(15);
const totalUsers = ref(0);

// Search and Filter
const searchQuery = ref("");
const filterRole = ref("");
const filterStatus = ref("");

// Modal States
const showModal = ref(false);
const showDeleteConfirm = ref(false);
const editingUser = ref(null);
const userToDelete = ref(null);

// Form Data
const formData = ref({
    name: "",
    email: "",
    phone: "",
    address: "",
    role_id: "",
    status: "enable",
    password: "",
});

// Computed
const totalPages = computed(() => Math.ceil(totalUsers.value / perPage.value));

// Format date
const formatDate = (date) => {
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

// Fetch users
const fetchUsers = async () => {
    try {
        loading.value = true;
        error.value = null;

        const response = await adminService.getUsers({
            search: searchQuery.value,
            role: filterRole.value,
            status: filterStatus.value,
            page: currentPage.value,
            per_page: perPage.value,
        });

        if (response.data?.data?.data) {
            users.value = response.data.data.data;
            totalUsers.value = response.data.data.total || 0;
        }
    } catch (err) {
        console.error("Error fetching users:", err);
        error.value = err.response?.data?.message || "Failed to fetch users";
    } finally {
        loading.value = false;
    }
};

// Apply filters
const applyFilters = () => {
    currentPage.value = 1;
    fetchUsers();
};

// Refresh data
const refreshData = () => {
    fetchUsers();
};

// Pagination
const previousPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
        fetchUsers();
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        fetchUsers();
    }
};

// Watch currentPage
const watchPage = () => {
    fetchUsers();
};

// Modal functions
const openCreateModal = () => {
    editingUser.value = null;
    formData.value = {
        name: "",
        email: "",
        phone: "",
        address: "",
        role_id: "",
        status: "enable",
        password: "",
    };
    showModal.value = true;
};

const openEditModal = (user) => {
    editingUser.value = user;
    formData.value = {
        name: user.name,
        email: user.email,
        phone: user.phone || "",
        address: user.address || "",
        role_id: user.role?.id || "",
        status: user.status || "enable",
        password: "",
    };
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingUser.value = null;
    formData.value = {
        name: "",
        email: "",
        phone: "",
        address: "",
        role_id: "",
        status: "enable",
        password: "",
    };
};

// Save user
const saveUser = async () => {
    try {
        submitting.value = true;
        error.value = null;

        if (editingUser.value) {
            // Update user
            await adminService.updateUser(editingUser.value.id, formData.value);
            success.value = "User updated successfully!";
        } else {
            // Create user
            await adminService.createUser(formData.value);
            success.value = "User created successfully!";
        }

        setTimeout(() => {
            success.value = null;
        }, 3000);

        closeModal();
        fetchUsers();
    } catch (err) {
        console.error("Error saving user:", err);
        error.value = err.response?.data?.message || "Failed to save user";
    } finally {
        submitting.value = false;
    }
};

// Delete functions
const confirmDelete = (user) => {
    userToDelete.value = user;
    showDeleteConfirm.value = true;
};

const deleteUser = async () => {
    try {
        submitting.value = true;
        error.value = null;

        await adminService.deleteUser(userToDelete.value.id);
        success.value = "User deleted successfully!";

        setTimeout(() => {
            success.value = null;
        }, 3000);

        showDeleteConfirm.value = false;
        userToDelete.value = null;
        fetchUsers();
    } catch (err) {
        console.error("Error deleting user:", err);
        error.value = err.response?.data?.message || "Failed to delete user";
        showDeleteConfirm.value = false;
    } finally {
        submitting.value = false;
    }
};

// Lifecycle
onMounted(() => {
    fetchUsers();
});
</script>
