const productService = {
    getProducts(params) {
        return apiClient.get("/products",{ params });
    },
    createProduct(payload) {
        return apiClient.post("/products", payload, {
            headers: { "Content-Type": "multipart/form-data" },
        });
    },
    deleteProduct(id) {
        return apiClient.delete(`/products/${id}`);
    },
    updateProduct(id, payload) {
        return apiClient.post(`/products/${id}`, payload, {
            headers: { "Content-Type": "multipart/form-data" },
        });
    },
    getCategories() {
        return apiClient.get("/categories");
    },
    addStock(data){
        return apiClient.post('/stocks',data)
    }
};
export default productService;
