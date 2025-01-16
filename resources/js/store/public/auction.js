import apiClient from "../../axios";

export default {
    namespaced: true,
    state: {
        auctions: [],
        auctionDetails: null,
        winner: null,
        highestBid: null,
    },
    mutations: {
        setAuctions(state, auctions) {
            state.auctions = auctions;
        },
        setAuctionDetails(state, details) {
            state.auctionDetails = details;
        },
        setWinner(state, winner) {
            state.winner = winner;
        },
        setHighestBid(state, highestBid) {
            state.highestBid = highestBid;
        },
    },
    actions: {
        async searchAuctions({ commit }, filters) {
            const response = await apiClient.get("/public/auction/filter", {
                params: filters,
            });
            commit("setAuctions", response.data.auctions || []);
        },

        async getAuctionDetailsById({ commit }, auctionId) {
            try {
                const response = await apiClient.get(`/public/auctions/${auctionId}`);
                const auction = response.data.auction;
                commit("setAuctionDetails", auction);

                // Fetch winner and highest bid if auction has ended
                if (new Date(auction.end_date) < new Date()) {
                    await this.dispatch("publicAuction/fetchWinnerAndHighestBid", auctionId);
                }
            } catch (error) {
                console.error("Error fetching auction details:", error);
                commit("setAuctionDetails", null);
            }
        },

        async fetchWinnerAndHighestBid({ commit }, auctionId) {
            try {
                const response = await apiClient.get(`/public/auctions/${auctionId}/winner-highest-bid`);
                commit("setWinner", response.data.winner || null);
                commit("setHighestBid", response.data.highest_bid || null);
            } catch (error) {
                console.error("Error fetching winner and highest bid:", error);
                commit("setWinner", null);
                commit("setHighestBid", null);
            }
        },
        async incrementViewCount({ commit }, auctionId) {
            try {
                await apiClient.patch(`/public/auctions/${auctionId}/increment-view`);
            } catch (error) {
                console.error("Error incrementing view count:", error);
            }
        },
        async fetchLatestApprovedAuctions({ commit }) {
            try {
                const response = await apiClient.get("/auctions/latest");
                commit("setAuctions", response.data.latest_auctions || []);
            } catch (error) {
                console.error("Error fetching latest approved auctions:", error);
            }
        },
    },
    getters: {
        getAuctions: (state) => state.auctions,
        getAuctionDetails: (state) => state.auctionDetails,
        getWinner: (state) => state.winner,
        getHighestBid: (state) => state.highestBid,
    },
};
