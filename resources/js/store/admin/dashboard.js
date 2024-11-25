import axios from '../../axios';

export default {
    namespaced: true,
    state: {
        statistics: {
            daily_views: 0,
            winning_auctions: 0,
            monthly_auctions: 0,
            earning: 0,
            recentOrders: [],
            recentUsers: [],
        },
    },
    getters: {
        getStatistics: (state) => state.statistics,
        getRecentOrders: (state) => state.recentOrders,
        getRecentUsers: (state) => state.recentUsers,
    },
    mutations: {
        setStatistics(state, stats) {
            state.statistics = stats;
        },
        setRecentOrders(state, orders) {
            state.recentOrders = orders;
        },
        setRecentUsers(state, users) {
            state.recentUsers = users;
        },
    },
    actions: {
        async fetchRecentOrders({ commit }) {
            try {
                const response = await axios.get('/admin/recent-orders');
                commit('setRecentOrders', response.data.orders);
            } catch (error) {
                console.error('Failed to fetch recent orders:', error);
            }
        },
        async fetchRecentUsers({ commit }) {
            try {
                const response = await axios.get('/admin/recent-users');
                commit('setRecentUsers', response.data.users);
            } catch (error) {
                console.error('Failed to fetch recent users:', error);
            }
        },
        async fetchStatistics({ commit }) {
            try {
                const response = await axios.get('/admin/statistics');
                commit('setStatistics', response.data);
            } catch (error) {
                console.error('Failed to fetch statistics:', error);
            }
        },
    },
};
