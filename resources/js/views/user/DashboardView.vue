<template>
    <div class="cardBox">
        <div v-for="(stat, label) in stats" :key="label" class="card">
            <div>
                <div class="numbers">{{ stat }}</div>
                <div class="cardName">{{ capitalize(label) }}</div>

            </div>
            <div class="iconBx">
                <i :class="iconForStat(label)"></i>
            </div>
        </div>
    </div>
    <div class="details">
        <div class="recentOrders">
            <div class="cardHeader">
                <h2>Recent winners of your auctions</h2>
            </div>

            <table>
                <thead>
                    <tr>
                        <td>Name</td>
                        <td>Payment ID</td>
                        <td>Order ID</td>
                        <td>Winner Name</td>
                        <td>Winner Email</td>
                        <td>Price</td>
                        <td>Payment link</td>
                        <td>Payment Status</td>
                        <td>Created | Last Update</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(order, index) in orders" :key="index">
                        <td>{{ order.name }}</td>
                        <td>{{ order.payment_id }}</td>
                        <td>{{ order.order_id }}</td>
                        <td>{{ order.winner_name }}</td>
                        <td>{{ order.winner_email }}</td>
                        <td>₿{{ order.price }}</td>
                        <td>
                            <button @click="copyLink(order.payment_link)">
                                Copy Link
                            </button>
                        </td>
                        <td>{{ order.payment_status }}</td>
                        <td>{{ order.created_at }} | {{ order.updated_at }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
    computed: {
        ...mapGetters("dashboardUser", ["getStats", "getOrders"]),
        stats() {
            return this.getStats;
        },
        orders() {
            return this.getOrders;
        },
    },
    methods: {
        ...mapActions("dashboardUser", ["fetchStats", "fetchOrders"]),
        capitalize(text) {
            // Splitting the text by underscore, capitalizing each word, and then joining them
            return text
                .split('_')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        },
        iconForStat(stat) {
            switch (stat) {
                case "auctions_seen":
                    return "bi bi-eye";
                case "auctions_won":
                    return "bi bi-award";
                case "auctions_created":
                    return "bi bi-vector-pen";
                case "auctions_sold":
                    return "bi bi-truck";
                default:
                    return "";
            }
        },
        copyLink(link) {
            navigator.clipboard.writeText(link).then(() => alert("Copied!"));
        },
    },
    async mounted() {
        await this.fetchStats();
        await this.fetchOrders();
    },
};
</script>

<style scoped>
.details .recentOrders table tr td button {
    padding: 5px 15px;
    background-color: #990000;
    border: none;
    border-radius: 5px;
    color: #fff;
    cursor: pointer;
}
</style>
