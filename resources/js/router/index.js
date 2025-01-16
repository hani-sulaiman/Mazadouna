import { createRouter, createWebHistory } from "vue-router";
import store from "../store";

import PublicLayout from "@/components/layouts/PublicLayout.vue";
import UserLayout from "@/components/layouts/UserLayout.vue";
import AdminLayout from "@/components/layouts/AdminLayout.vue";

import HomeView from "../views/public/HomeView.vue";
import AboutUsView from "../views/public/AboutUsView.vue";
import ContactUsView from "../views/public/ContactUsView.vue";
import SearchView from "../views/public/SearchView.vue";
import AuctionDetailsView from "../views/public/AuctionDetailsView.vue";
import SignView from "../views/public/SignView.vue";

import AdminDashboard from "../views/admin/DashboardView.vue";
import CategoriesView from "../views/admin/CategoriesView.vue";
import CreateCategory from "../components/admin/CreateCategory.vue";
import ManageOrdersView from "../views/admin/ManageOrdersView.vue";

import DashboardView from "../views/user/DashboardView.vue";
import ManageAuctionsView from "../views/user/ManageAuctionsView.vue";
import FavoritesView from "../views/user/FavoriteView.vue";
import ManageOrdersViewUser from "../views/user/ManageOrdersView.vue";
import CreateNewAuction from "../components/user/CreateAuction.vue";
import EditAuction from "../components/user/EditAuction.vue";

import LiveAuctionView from "../views/user/LiveAuctionView.vue";
import EmailVerificationView from "../views/public/EmailVerificationView.vue";
import EmailSentView from "../views/public/EmailSentView.vue";
const routes = [
    {
        path: "/",
        component: PublicLayout,
        meta: { layout: "public" },
        children: [
            { path: "", name: "home", component: HomeView },
            { path: "about-us", name: "AboutUs", component: AboutUsView },
            { path: "contact-us", name: "ContactUs", component: ContactUsView },
            { path: "search", name: "SearchPage", component: SearchView },
            {
                path: "auctions/:id",
                name: "AuctionDetails",
                component: AuctionDetailsView,
            },
            { path: "sign", name: "SignUser", component: SignView },
            {
                path: "auction/live/:id",
                name: "LiveAuction",
                component: LiveAuctionView,
                meta: { requiresAuth: true },
            },
            {
                path: "/email/verify/:id/:hash",
                name: "EmailVerification",
                component: EmailVerificationView,
                meta: { layout: "public" },
            },
            {
                path: '/email-sent',
                name: 'EmailSent',
                component: EmailSentView,
                props: route => ({ email: route.params.email || '' }),
                meta: { layout: 'public' } 
              },
        ],
    },
    {
        path: "/user",
        component: UserLayout,
        meta: { layout: "user", requiresAuth: true },
        children: [
            { path: "", name: "UserDashboard", component: DashboardView },
            {
                path: "manage-auctions",
                name: "ManageAuctions",
                component: ManageAuctionsView,
            },
            { path: "favorites", name: "Favorites", component: FavoritesView },
            {
                path: "my-orders",
                name: "UserOrders",
                component: ManageOrdersViewUser,
            },
            {
                path: "manage-auctions/create",
                name: "CreateAuction",
                component: CreateNewAuction,
            },
            {
                path: "manage-auctions/edit/:id",
                name: "EditAuction",
                component: EditAuction,
            },
        ],
    },
    {
        path: "/admin",
        component: AdminLayout,
        meta: { layout: "admin", requiresAuth: true, isAdmin: true },
        children: [
            { path: "", name: "AdminDashboard", component: AdminDashboard },
            {
                path: "categories",
                name: "CategoriesView",
                component: CategoriesView,
            },
            {
                path: "categories/create",
                name: "CreateCategory",
                component: CreateCategory,
            },
            {
                path: "orders",
                name: "ManageOrdersAdmin",
                component: ManageOrdersView,
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Route Guard for Authentication and Role Checking
router.beforeEach((to, from, next) => {
    const { requiresAuth, isAdmin } = to.meta; // Destructure meta properties
    const isAuthenticated = store.getters["auth/isAuthenticated"]; // Check if the user is authenticated
    const isUserAdmin = store.getters["auth/isAdmin"]; // Check if the user is an admin

    if (requiresAuth && !isAuthenticated) {
        // If route requires authentication but user is not authenticated, redirect to sign-in
        return next("/sign");
    }

    if (requiresAuth) {
        // If the route requires authentication:
        if (isAdmin && !isUserAdmin) {
            // Redirect non-admins trying to access admin routes
            return next({ name: "UserDashboard" }); // Redirect to a default user page
        } else if (!isAdmin && isUserAdmin) {
            // Redirect admins trying to access user routes
            return next({ name: "AdminDashboard" }); // Redirect to a default admin page
        }
    }

    // If everything is good, continue to the requested route
    next();
});
export default router;
