import { defineStore } from "pinia";
import cartService from "../services/cart/cartService";

export const useCartStore = defineStore("cart", {
    state: () => ({
        carts: [],
        loading: false,
        error: null,
        message: ""
    }),

    actions: {
        async fetchCarts() {
            this.loading = true;
            try {
                const response = await cartService.getCarts();
                this.carts = response.data;
                // console.log(this.carts);
            } catch (err) {
                this.error = err.message;

            } finally {
                this.loading = false;
            }
        },

        async addItem(productId, quantity = 1) {
            this.loading = true;
            try {
                const payload = {
                    product_id: productId,
                    quantity,
                };
                const res = await cartService.createCart(payload);
                this.message = res.data.message
                await this.fetchCarts();

                return res.data
            } catch (err) {
                this.error = err.message;
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async updateItem(id, quantity) {
            this.loading = true;
            try {
                const payload = { quantity };
                await cartService.updateCart(payload, id);
                await this.fetchCarts();
            } catch (err) {
                this.error = err.response?.data?.message;
                // console.log("Error IN Store",this.error);

                throw err;
            } finally {
                this.loading = false;
            }
        },

        async removeItem(id) {
            this.loading = true;
            try {
                await cartService.deleteCart(id);
                // remove locally to avoid refetch, or just fetch again
                this.carts = this.carts.filter((item) => item.id !== id);
            } catch (err) {
                this.error = err.message;
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async clearCart() {
            this.loading = true;
            try {
                await cartService.clearCarts();
                this.carts = [];
            } catch (err) {
                this.error = err.message;
                throw err;
            } finally {
                this.loading = false;
            }
        },
    },
});
