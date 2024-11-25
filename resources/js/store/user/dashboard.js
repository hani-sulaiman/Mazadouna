import apiClient from "../../axios";

export default {
    namespaced: true,
    state: {
        stats: {
            auctions_seen: 0,
            auctions_won: 0,
            auctions_created: 0,
            auctions_sold: 0,
        },
        orders: [],
    },
    mutations: {
        setStats(state, stats) {
            state.stats = stats;
        },
        setOrders(state, orders) {
            state.orders = orders;
        },
    },
    actions: {
        async fetchStats({ commit }) {
            try {
                const response = await apiClient.get('/user/dashboard/stats');
                commit('setStats', response.data);
            } catch (error) {
                console.error('Failed to fetch stats:', error);
            }
        },
        async fetchOrders({ commit }) {
            try {
                const response = await apiClient.get('/user/dashboard/orders');
                commit('setOrders', response.data);
            } catch (error) {
                console.error('Failed to fetch orders:', error);
            }
        },
    },
    getters: {
        getStats: (state) => state.stats,
        getOrders: (state) => state.orders,
    },
};
