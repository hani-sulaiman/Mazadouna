import apiClient from "../../axios";

export default {
    namespaced: true,
    state: {
        userAuctions: [],
        categories: [], 
    },
    mutations: {
        setUserAuctions(state, auctions) {
            state.userAuctions = auctions;
        },
        addUserAuction(state, auction) {
            state.userAuctions.push(auction);
        },
        updateUserAuction(state, updatedAuction) {
            const index = state.userAuctions.findIndex(
                (auction) => auction.id === updatedAuction.id
            );
            if (index !== -1) {
                state.userAuctions.splice(index, 1, updatedAuction);
            }
        },
        removeUserAuction(state, auctionId) {
            state.userAuctions = state.userAuctions.filter(
                (auction) => auction.id !== auctionId
            );
        },
        setCategories(state, categories) {
            state.categories = categories;
        },
    },
    actions: {
        // Fetch user's auctions
        async fetchUserAuctions({ commit }) {
            try {
                const response = await apiClient.get("/user/auctions");
                commit("setUserAuctions", response.data.auctions);
            } catch (error) {
                console.error("Error fetching user auctions:", error);
            }
        },

        // Create a new auction
        async createAuction({ commit }, auctionData) {
            try {
                const response = await apiClient.post("/user/auction/create", auctionData);
                commit("addUserAuction", response.data.auction);
            } catch (error) {
                console.error("Error creating auction:", error);
                throw new Error("Failed to create auction");
            }
        },

        // Update an existing auction
        async updateAuction({ commit }, { auctionId, updatedData }) {
            try {
                const response = await apiClient.post(
                    `/user/auction/edit/${auctionId}`,
                    updatedData
                );
                commit("updateUserAuction", response.data.auction);
            } catch (error) {
                console.error("Error updating auction:", error);
                throw new Error("Failed to update auction");
            }
        },

        // Delete an auction
        async deleteAuction({ commit }, auctionId) {
            try {
                await apiClient.delete(`/user/auction/delete/${auctionId}`);
                commit("removeUserAuction", auctionId);
            } catch (error) {
                console.error("Error deleting auction:", error);
                throw new Error("Failed to delete auction");
            }
        },
        // Fetch all categories from public endpoint
        async fetchCategories({ commit }) {
            try {
                const response = await apiClient.get("/category"); // نقطة النهاية العامة
                commit("setCategories", response.data.categories);
            } catch (error) {
                console.error("Error fetching categories:", error);
                throw new Error("Failed to load categories.");
            }
        },
    },
    getters: {
        getCategories: (state) => state.categories,
        getUserAuctions: (state) => state.userAuctions,
    },
};
