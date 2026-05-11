import { defineStore } from "pinia";
import orderService from "../services/order/orderService";

export const useOrderStore = defineStore("order", {
    state: () => ({
        orders: [],
        order: null,
        userOrders: [],
        pagination: null,
        paginationLinks: [],
        loading: false,
        error: null,
    }),

    actions: {
        async fetchOrders(page = 1) {
            this.loading = true;
            this.error = null;
            try {
                const response = await orderService.getOrders(page);
                this.orders = response.data.data;
                this.pagination = {
                    current_page: response.data.current_page,
                    last_page:    response.data.last_page,
                    per_page:     response.data.per_page,
                    total:        response.data.total,
                };
                this.paginationLinks = response.data.links;
            } catch (err) {
                this.error = err.response?.data?.message ?? err.message;
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async fetchOrderByID(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await orderService.getOrderByID(id);
                this.order = response.data;
            } catch (err) {
                this.error = err.response?.data?.message ?? err.message;
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async fetchOrdersByUserID(user_id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await orderService.getOrdersByUserID(user_id);
                this.userOrders = response.data.data;
                this.pagination = {
                    current_page: response.data.current_page,
                    last_page: response.data.last_page,
                    per_page: response.data.per_page,
                    total: response.data.total,
                };
            } catch (err) {
                this.error = err.response?.data?.message ?? err.message;
                throw err;
            } finally {
                this.loading = false;
            }
        },
    },
});
