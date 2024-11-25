import apiClient from "../../axios";

export default {
    namespaced: true,
    state: {
        auctionDetails: {},
        highest_bid: 0,
        current_price: 0, // إضافة current_price هنا
        bidChat: [],
        viewersCount: 0,
        isUserJoined: false,
        isOwner: false,
        isLive: false,
        loggedInUserId: null,
        relatedAuctions: [], // إضافة حالة للمزادات المرتبطة
    },
    mutations: {
        setAuctionDetails(state, details) {
            state.auctionDetails = details.auction;
            state.highest_bid = details.highest_bid || 0;
            state.current_price = details.current_price || state.auctionDetails.starting_price; // تخزين current_price
            state.isLive = details.is_live;
            state.isUserJoined = details.has_joined;
            state.viewersCount = details.viewers_count;
            state.isOwner = details.auction.user_id === state.loggedInUserId;
        },
        setRelatedAuctions(state, relatedAuctions) {
            state.relatedAuctions = relatedAuctions;
        },
        setLoggedInUserId(state, userId) {
            state.loggedInUserId = userId;
        },
        addBidToChat(state, bid) {
            state.bidChat.unshift(bid);
        },
        updateBidsAndViewers(state, { bids, viewers }) {
            state.bidChat = bids;
            state.viewersCount = viewers;
            state.current_price = bids.length
                ? bids[0].bid_value
                : state.auctionDetails.starting_price;
        },
        setJoinStatus(state, status) {
            state.isUserJoined = status;
        },
    },
    actions: {
        async fetchAuctionDetails({ commit, rootState }, auctionId) {
            try {
                const loggedInUserId = rootState.auth.user.id;
                commit("setLoggedInUserId", loggedInUserId);

                const response = await apiClient.get(
                    `/user/auction/${auctionId}/live`
                );
                commit("setAuctionDetails", response.data);
            } catch (error) {
                console.error("Error fetching auction details:", error);
            }
        },

        async joinAuction({ commit, state }, auctionId) {
            if (state.isOwner) return; // Prevent the owner from joining

            try {
                const response = await apiClient.post(
                    `/user/auction/${auctionId}/join`
                );
                if (response.data.success) {
                    commit("setJoinStatus", true);
                }
            } catch (error) {
                console.error("Error joining auction:", error);
            }
        },

        async placeBid({ commit, state, dispatch }, bidAmount) {
            try {
                const auctionId = state.auctionDetails.id;
                const response = await apiClient.post(
                    `/user/auction/${auctionId}/bids`,
                    {
                        bid_value: bidAmount,
                    }
                );
                commit("addBidToChat", response.data.bid);
                await dispatch("fetchBidsAndViewers", auctionId);
            } catch (error) {
                if (error.response && error.response.data.error) {
                    alert(error.response.data.error);
                } else {
                    console.error("Error placing bid:", error);
                }
            }
        },

        async fetchBidsAndViewers({ commit }, auctionId) {
            try {
                const response = await apiClient.get(
                    `/user/auction/${auctionId}/bids`
                );
                commit("updateBidsAndViewers", {
                    bids: response.data.bids || [],
                    viewers: response.data.viewers_count,
                });
            } catch (error) {
                console.error("Error fetching bids and viewers:", error);
            }
        },
        async fetchRelatedAuctions({ commit }, auctionId) {
            try {
                const response = await apiClient.get(`/auction/${auctionId}/related`);
                commit("setRelatedAuctions", response.data.related_auctions);
            } catch (error) {
                console.error("Error fetching related auctions:", error);
            }
        },
    },
    getters: {
        getAuctionDetails: (state) => state.auctionDetails,
        getHighestBid: (state) => state.highest_bid,
        getCurrentPrice: (state) => state.current_price,
        getBidChat: (state) => state.bidChat,
        isUserJoined: (state) => state.isUserJoined,
        isOwner: (state) => state.isOwner,
        isLive: (state) => state.isLive,
        getViewersCount: (state) => state.viewersCount,
        getRelatedAuctions: (state) => state.relatedAuctions, // Getter للمزادات المرتبطة
    },
};
