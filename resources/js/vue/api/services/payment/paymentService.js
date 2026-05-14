import apiClient from "../../axios";
 
const paymentService = {
    generateKHQR(payload) {
        return apiClient.post('/generateQr', payload);
    },
    checkout(payload) {
        return apiClient.post("/checkout", payload);
    },
    checkTransaction(tran_id) {
        return apiClient.get(`/check-transaction/${tran_id}`);
    },

    abaCheckout(payload) {
        return apiClient.post('/aba-checkout', payload);
    },

    paywayCheckout(payload) {
        return apiClient.post('/payway-checkout', payload);
    },

    paywayCheckStatus(tran_id) {
        return apiClient.post('/payway-check-status', { tran_id });
    },
};

export default paymentService;
