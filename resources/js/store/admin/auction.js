import apiClient from "../../axios";
export default {
    namespaced: true,
    state: {
        pendingAuctions: [],
    },
    mutations: {
        setPendingAuctions(state, auctions) {
            state.pendingAuctions = auctions;
        },
    },
    actions: {
        async fetchPendingAuctions({ commit }) {
            try {
                const response = await apiClient.get("/admin/auctions/pending");
                commit("setPendingAuctions", response.data);
            } catch (error) {
                console.error("Error fetching pending auctions:", error);
            }
        },

        async approveAuction(_, auctionId) {
            try {
                await apiClient.post(`/admin/auctions/${auctionId}/approve`);
            } catch (error) {
                console.error("Error approving auction:", error);
            }
        },

        async rejectAuction(_, auctionId) {
            try {
                await apiClient.post(`/admin/auctions/${auctionId}/reject`);
            } catch (error) {
                console.error("Error rejecting auction:", error);
            }
        },
    },
    getters: {
        pendingAuctions: (state) => state.pendingAuctions,
    },
};
