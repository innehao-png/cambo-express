const authService = {
    register(payload) {
        return apiClient.post("/auth/register", payload);
    },
    login(payload) {
        return apiClient.post("/auth/login", payload).then((res) => {
            if (res.data && res.data.token) {
                localStorage.setItem("token", res.data.token);
            }
            return res;
        });
    },
    logout() {
        return apiClient.post("/auth/logout").then((res) => {
            localStorage.removeItem("token");
            return res;
        });
    },
    getProfile() {
        return apiClient.get("/auth/me").then((res) => {
            const authUser = res.data.data
            return authUser;
        });
    },
    // getAuthUserRole() {
    //     return apiClient.get("/auth/me").then((res) => {
    //         const authUser = res.data.data
    //         return authUser.role.name;
    //     });
    // },
    forgotPassword(email){
        return apiClient.post('/auth/forgot-password',{email})
    },
    resetPassword(payload){
        return apiClient.post('/auth/reset-password',payload)
    }
};

export default authService;
