import apiClient from "../../axios";

export default {
    namespaced: true,
    state: {
        paymentLinks: null,
    },
    mutations: {
        setPaymentLinks(state, links) {
            state.paymentLinks = links;
        },
    },
    actions: {
        async order({ commit }, auctionId) {
            try {
                const response = await apiClient.post('/user/generate-payment-links', {
                    auction_id: auctionId
                });
                commit('setPaymentLinks', {
                    adminLink: response.data.admin_payment_link,
                    ownerLink: response.data.owner_payment_link,
                });
            } catch (error) {
                console.error("Error generating payment links:", error.response ? error.response.data : error);
                throw error;  // You can handle this error in your component
            }
        },
    },
    getters: {
        getPaymentLinks: (state) => state.paymentLinks,
    },
};
