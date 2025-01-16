// store/user/order.js
import apiClient from "../../axios";

export default {
    namespaced: true,
    state: {
        order: null // Store as a single object, not an array
    },
    mutations: {
        setOrder(state, order) {
            state.order = order;
        }
    },
    actions: {
        async fetchUserOrders({ commit }) {
            try {
                const response = await apiClient.get('/user/orders');
                commit('setOrder', response.data || null);
            } catch (error) {
                console.error("Failed to fetch user orders:", error);
            }
        }
    },
    getters: {
        getUserOrders: (state) => state.order
    }
};
