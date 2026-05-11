import apiClient from "../../axios";

const orderService = {
    getOrders(page = 1) {
        return apiClient.get(`/orders?page=${page}`);
    },
    getOrderByID(id) {
        return apiClient.get(`/orders/${id}`);
    },
    getOrdersByUserID(user_id){
        return apiClient.get(`/users/${user_id}/orders`)
    }
};

export default orderService;
