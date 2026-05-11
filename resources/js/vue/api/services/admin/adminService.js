// import apiClient from "../axios.js";

const adminService = {
    // Dashboard endpoints
    getDashboardOverview() {
        return apiClient.get("/admin/dashboard/overview");
    },

    getSalesStats(period = 30) {
        return apiClient.get("/admin/dashboard/sales", { params: { period } });
    },

    getTopProducts(limit = 10, period = 30) {
        return apiClient.get("/admin/dashboard/top-products", {
            params: { limit, period },
        });
    },

    getLowStockProducts(threshold = 10, perPage = 15) {
        return apiClient.get("/admin/dashboard/low-stock", {
            params: { threshold, per_page: perPage },
        });
    },

    getRecentOrders(limit = 10, status = null) {
        return apiClient.get("/admin/dashboard/recent-orders", {
            params: { limit, status },
        });
    },

    getOrderStats() {
        return apiClient.get("/admin/dashboard/order-stats");
    },

    getPaymentStats() {
        return apiClient.get("/admin/dashboard/payment-stats");
    },

    getStockMovement(days = 30) {
        return apiClient.get("/admin/dashboard/stock-movement", {
            params: { days },
        });
    },

    // User management
    getUsers(params = {}) {
        return apiClient.get("/admin/users", { params });
    },

    getUser(id) {
        return apiClient.get(`/admin/users/${id}`);
    },

    createUser(data) {
        return apiClient.post("/admin/users", data);
    },

    updateUser(id, data) {
        return apiClient.put(`/admin/users/${id}`, data);
    },

    deleteUser(id) {
        return apiClient.delete(`/admin/users/${id}`);
    },

    updateUserRole(id, roleId) {
        return apiClient.put(`/admin/users/${id}/role`, { role_id: roleId });
    },

    updateUserStatus(id, status) {
        return apiClient.put(`/admin/users/${id}/status`, { status });
    },

    resetUserPassword(id, password, passwordConfirmation) {
        return apiClient.put(`/admin/users/${id}/reset-password`, {
            password,
            password_confirmation: passwordConfirmation,
        });
    },
};

export default adminService;
