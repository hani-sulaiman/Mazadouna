import apiClient from '../../axios';

export default {
  namespaced: true,
  state: {
    categories: [],
  },
  mutations: {
    setCategories(state, categories) {
      state.categories = categories;
    },
    addCategory(state, category) {
      state.categories.push(category);
    },
    removeCategory(state, categoryId) {
      state.categories = state.categories.filter(cat => cat.id !== categoryId);
    },
  },
  actions: {
    async fetchCategories({ commit }) {
      try {
        const response = await apiClient.get('/admin/categories');
        commit('setCategories', response.data.categories);
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    },
    async createCategory({ commit }, categoryData) {
      try {
        const response = await apiClient.post('/admin/categories', categoryData);
        commit('addCategory', response.data.category);
      } catch (error) {
        console.error('Error creating category:', error);
        throw new Error('Failed to create category');
      }
    },
    async deleteCategory({ commit }, categoryId) {
      try {
        await apiClient.delete(`/admin/categories/${categoryId}`);
        commit('removeCategory', categoryId);
      } catch (error) {
        console.error('Error deleting category:', error);
        throw new Error('Failed to delete category');
      }
    },
  },
  getters: {
    allCategories: (state) => state.categories,
  },
};
