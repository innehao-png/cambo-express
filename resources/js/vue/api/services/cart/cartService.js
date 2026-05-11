const cartService = {
    getCarts() {
        return apiClient.get("/cart");
    },
    createCart(payload) {
        return apiClient.post("/cart", payload);
    },
    updateCart(payload, id) {
        return apiClient.put(`/cart/${id}`, payload);
    },
    deleteCart(id) {
        return apiClient.delete(`/cart/${id}`);
    },
    clearCarts() {
        return apiClient.delete(`/clear/cart`);
    },
};

export default cartService;
