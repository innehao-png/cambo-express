import { defineStore } from "pinia";
import authService from "../services/auth/authService";

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem("token") || null,
    }),
    actions: {
        async login(payload) {
            const res = await authService.login(payload);
            this.user = res.data.user;
            this.token = res.data.token;
        },
        async logout() {
            await authService.logout();
            this.user = null;
            this.token = null;
        },
        async getProfile() {
            this.user = await authService.getProfile();
            console.log("Auth User", this.user);
        },
    },
    getters: {
        isAuthenticated: (state) => !!state.token,
        userRole: (state) => state.user?.role?.name,
        isAdmin: (state) => state.user?.role?.name === 'admin',
    },
});
