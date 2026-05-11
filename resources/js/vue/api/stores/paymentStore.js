import { defineStore } from "pinia";
import paymentService from "../services/payment/paymentService";

export const usePaymentStore = defineStore("payment", {
    state: () => ({
        error: null,
        loading: false,
        paying: false,
        order: null,
        payment: null,
        shipping_address: "",
    }),

    getters: {
        isLoading: (state) => state.loading,
        isPaying: (state) => state.paying,
        getError: (state) => state.error,
        getOrder: (state) => state.order,
        getPayment: (state) => state.payment,
    },

    actions: {
        async proceedCheckout(payload) {
            this.loading = true;
            this.error = null;

            try {
                const res = await paymentService.checkout(payload);

                if (res.status === 200 || res.status === 201) {
                    this.order = res.data?.order_id;
                    this.payment = res.data;
                    this.shipping_address = payload.shipping_address;
                    return res.data;
                } else {
                    throw new Error(res.data?.message || "Checkout failed");
                }
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    error.message ||
                    "Checkout error";
                console.error("Checkout error:", error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async verifyPayment(paymentID) {
            this.paying = true;
            this.error = null;

            try {
                const res = await paymentService.checkPaymentStatus(paymentID);

                if (res.status === 200) {
                    if (res.data?.status === "paid") {
                        return {
                            status: "success",
                            data: res.data,
                        };
                    } else {
                        throw new Error("Payment not confirmed yet");
                    }
                } else {
                    throw new Error(
                        res.data?.message || "Payment verification failed",
                    );
                }
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    error.message ||
                    "Payment verification error";
                console.error("Payment verification error:", error);
                throw error;
            } finally {
                this.paying = false;
            }
        },

        clearPayment() {
            this.order = null;
            this.payment = null;
            this.error = null;
            this.shipping_address = "";
            this.loading = false;
            this.paying = false;
        },

        setError(error) {
            this.error = error;
        },
    },
});
