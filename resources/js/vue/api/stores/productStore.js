import { defineStore } from "pinia";
import productService from "../services/product/productService";
import { toFormData } from "../../utils/formData";

export const useProductStore = defineStore("product", {
    state: () => ({
        products: [],
        categories: [],
        pagination: {},
        loading: false,
        error: null,
        filters: {
            page: 1,
            per_page: 12,
            category: null,
            search: null,
            sort: null,
        },
        lastFetched: null,
    }),

    getters: {
        allProducts: (state) => state.products,
        allCategories: (state) => state.categories,
        inStock: (state) => state.products.filter((p) => p.stock > 10),
        lowStock: (state) => state.products.filter((p) => p.stock < 10),
        outOfStock: (state) => state.products.filter(p => p.stock === 0),
    },

    actions: {
        async fetchProducts(force = false) {
            const now = Date.now();
            if (!force && this.products.length > 0 && this.lastFetched && (now - this.lastFetched < 30000)) {
                return;
            }

            this.loading = true;
            try {
                const response = await productService.getProducts(this.filters);
                this.products = response.data.data;
                this.pagination = response.data;
                this.lastFetched = Date.now();
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        },

        async fetchLowStockProducts() {
            this.loading = true;
            try {
                const response = await productService.getLowStockProducts(this.filters);
                this.products = response.data.data;
                this.pagination = response.data;
                // console.log(this.products);
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        },

        async fetchCategories() {
            try {
                const response = await productService.getCategories();
                this.categories = response.data.data;
            } catch (err) {
                this.error = err.message;
            }
        },

        async createProduct(payload) {
            this.loading = true;
            try {
                const formData = toFormData(payload);
                const response = await productService.createProduct(formData);
                await this.fetchProducts();
                return response;
            } catch (err) {
                this.error = err.message;
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async updateProduct(id, payload) {
            this.loading = true;
            try {
                const formData = toFormData(payload);
                formData.append("_method", "PUT");

                const response = await productService.updateProduct(id, formData);
                await this.fetchProducts();
                return response;
            } catch (err) {
                this.error = err.message;
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async deleteProduct(id) {
            this.loading = true;
            this.error = null;
            try {
                await productService.deleteProduct(id);
                this.products = this.products.filter((p) => p.id !== id);
            } catch (err) {
                this.error = err.message;
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async addStock(data){
            this.loading = true;
            try {
                const res = await productService.addStock(data);
                this.fetchProducts()
                return res.data;
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        }
    },
});
