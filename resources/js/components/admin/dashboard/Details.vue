<template>
    <div class="details-admin">
        <div class="recentOrders">
            <div class="cardHeader">
                <h2>Recent Orders</h2>
                <a href="#" class="btn">View All</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <td>Name</td>
                        <td>Price</td>
                        <td>Payment</td>
                        <td>Status</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="order in recentOrders" :key="order.id">
                        <td>{{ order.user_name }}</td>
                        <td>₿{{ order.price }}</td>
                        <td>{{ order.payment_status }}</td>
                        <td><span :class="statusClass(order.status)">{{ order.status }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Recent Customers -->
        <div class="recentCustomers">
            <div class="cardHeader">
                <h2>Recent Customers</h2>
            </div>
            <table>
                <tbody>
                    <tr v-for="user in recentUsers" :key="user.id">
                        <td>
                            <h4>{{ user.name }} <br> <span>{{ user.email }}</span></h4>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
    computed: {
        ...mapGetters('adminDashboard', ['getRecentOrders', 'getRecentUsers']),
        recentOrders() {
            return this.getRecentOrders;
        },
        recentUsers() {
            return this.getRecentUsers;
        },
    },
    methods: {
        ...mapActions('adminDashboard', ['fetchRecentOrders', 'fetchRecentUsers']),
        statusClass(status) {
            return {
                delivered: 'delivered',
                pending: 'pending',
                return: 'return',
                inProgress: 'inProgress',
            }[status] || '';
        },
    },
    async mounted() {
        await this.fetchRecentOrders();
        await this.fetchRecentUsers();
    },
};
</script>

<style scoped>
.details-admin {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.cardHeader {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

table {
    width: 100%;
    border-collapse: collapse;
}
.details-admin .recentOrders{
    min-height: unset;
}
.cardHeader {
    height: fit-content;
}
table thead tr td {
    font-weight: bold;
    padding: 10px;
}

table tbody tr td {
    padding: 10px;
    text-align: center;
}

.status {
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
}

.delivered { background-color: #4caf50; color: white; }
.pending { background-color: #ffa500; color: white; }
.return { background-color: #f44336; color: white; }
.inProgress { background-color: #00bcd4; color: white; }

.imgBx img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}
</style>
