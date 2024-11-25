import { createStore } from "vuex";
import auth from "./auth";
import adminCategory from "./admin/category";
import userAuction from "./user/auction";
import adminAuction from "./admin/auction";
import publicAuction from "./public/auction";
import BidAuction from "./user/bid";
import payment from "./user/payment";
import userOrder from "./user/order";
import dashboardUser from "./user/dashboard";
import favorite from "./user/favorite";
import adminDashboard from "./admin/dashboard";
const store = createStore({
    modules: {
        auth,
        adminCategory,
        userAuction,
        adminAuction,
        publicAuction,
        BidAuction,
        payment,
        userOrder,
        dashboardUser,
        favorite,
        adminDashboard
    },
});

export default store;
