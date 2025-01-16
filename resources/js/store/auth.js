import axios from "../axios";
import router from "../router";

export default {
    namespaced: true,
    state: {
        token: localStorage.getItem("token") || null,
        user: JSON.parse(localStorage.getItem("user")) || null,
        is_admin: localStorage.getItem("is_admin") === "1", // Convert to boolean
        userId: null,
        emailUser: "",
    },
    mutations: {
        setUserId(state, userId) {
            state.userId = userId;
        },
        setEmailUser(state, email) {
            state.emailUser = email;
        },
        setToken(state, token) {
            state.token = token;
            localStorage.setItem("token", token);
        },
        setUser(state, user) {
            state.user = user;
            state.is_admin = user.is_admin;
            localStorage.setItem("user", JSON.stringify(user));
            localStorage.setItem("is_admin", user.is_admin ? "1" : "0");
        },
        clearAuthData(state) {
            state.token = null;
            state.user = null;
            state.is_admin = false;
            localStorage.removeItem("token");
            localStorage.removeItem("user");
            localStorage.removeItem("is_admin");
        },
    },
    actions: {
        async login({ commit }, credentials) {
            try {
                const response = await axios.post("/login", credentials);
                const user = response.data.user;
                const access_token = response.data.access_token;

                // Store the token and user details
                commit("setToken", access_token);
                commit("setUser", user);

                // Set Axios authorization header
                axios.defaults.headers.common[
                    "Authorization"
                ] = `Bearer ${access_token}`;

                // Redirect based on user role
                if (user.is_admin) {
                    router.push("/admin");
                } else {
                    router.push("/user");
                }
            } catch (error) {
                throw new Error("Invalid login credentials");
            }
        },

        async register({ commit }, userData) {
                const response = await axios.post("/register", userData);

                const user = response.data.user;
                const access_token = response.data.access_token;

                // Store the token and user details
                commit("setToken", access_token);
                commit("setUser", user);

                // Set Axios authorization header
                axios.defaults.headers.common[
                    "Authorization"
                ] = `Bearer ${access_token}`;
                return response;
        },

        logout({ commit }) {
            commit("clearAuthData");
            delete axios.defaults.headers.common["Authorization"];
            router.push("/sign");
        },
        async fetchUser({ commit }) {
            try {
                const response = await axios.get("/user/profile");
                commit("setUserId", response.data.id); // Assuming response contains user ID
                commit("setEmailUser", response.data.email); // Assuming response contains user ID
            } catch (error) {
                console.error("Error fetching user:", error);
            }
        },
        initializeAuth({ state, commit }) {
            if (state.token) {
                axios.defaults.headers.common[
                    "Authorization"
                ] = `Bearer ${state.token}`;
            } else {
                commit("clearAuthData");
            }
        },
    },
    getters: {
        isAuthenticated: (state) => !!state.token,
        isAdmin: (state) => state.is_admin,
        getUserId: (state) => state.userId,
        getEmailUser: (state) => state.emailUser,
    },
};
