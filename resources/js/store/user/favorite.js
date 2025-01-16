// store/user/favorite.js
import axios from '../../axios';

export default {
    namespaced: true,
    state: {
        favorites: [],
    },
    mutations: {
        setFavorites(state, favorites) {
            state.favorites = favorites;
        },
        addFavorite(state, reminder) {
            state.favorites.push(reminder);
        },
        removeFavorite(state, reminderId) {
            state.favorites = state.favorites.filter(fav => fav.id !== reminderId);
        },
    },
    actions: {
        async fetchFavorites({ commit, rootGetters }) {
            try {
                // Check if user is logged in using a root getter or state
                if (!rootGetters['auth/isAuthenticated']) {
                    console.warn('User is not logged in');
                    return; // Exit if the user is not logged in
                }
        
                const response = await axios.get('/user/reminders');
                commit('setFavorites', response.data.reminders);
            } catch (error) {
                console.error('Error fetching favorites:', error);
            }
        },
        
        async addFavorite({ commit,dispatch }, auctionId) {
            try {
                const response = await axios.post('/user/reminders', { auction_id: auctionId });
                commit('addFavorite', response.data.reminder);
                dispatch('fetchFavorites')
            } catch (error) {
                console.error('Error adding favorite:', error);
            }
        },
        async removeFavorite({ commit,dispatch }, reminderId) {
            try {
                await axios.delete(`/user/reminders/${reminderId}`);
                commit('removeFavorite', reminderId);
                dispatch('fetchFavorites')
            } catch (error) {
                console.error('Error removing favorite:', error);
            }
        },
    },
    getters: {
        getFavorites: (state) => state.favorites,
    },
};
