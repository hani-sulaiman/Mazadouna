<template>
    <div class="details">
        <div class="recentOrders">
            <div class="cardHeader">
                <h2>My Orders</h2>
            </div>

            <table v-if="userOrders">
                <thead>
                    <tr>
                        <td>Name</td>
                        <td>Payment ID</td>
                        <td>Order ID</td>
                        <td>Price</td>
                        <td>Payment Link</td>
                        <td>Payment Status</td>
                        <td>Status</td>
                        <td>Created | Last Update</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(transaction, tIndex) in userOrders.transactions" :key="tIndex">
                        <!-- Display order details in the first row -->
                        <td >{{ userOrders.name }}</td>

                        
                        <td>{{ transaction.payment_id }}</td>
                        <td>{{ userOrders.order_id }}</td>
                        <td>₿{{ transaction.price }}</td>
                        <td>
                            <button @click="copyToClipboard(transaction.payment_link)" class="copy-btn">
                                Copy
                            </button>
                        </td>
                        <td>{{ transaction.payment_status }}</td>
                        <td>
                            <span :class="'status ' + statusClass(userOrders.status)">
                                {{ userOrders.status }}
                            </span>
                        </td>
                        <td>{{ userOrders.created_at }} | {{ userOrders.updated_at }}</td>
                    </tr>
                </tbody>
            </table>
            <p v-else>No orders found.</p>
        </div>
    </div>
</template>


<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    computed: {
        ...mapGetters('userOrder', ['getUserOrders']),
        userOrders() {
            return this.getUserOrders;
        }
    },
    methods: {
        ...mapActions('userOrder', ['fetchUserOrders']),
        statusClass(status) {
            switch (status.toLowerCase()) {
                case 'delivered': return 'delivered';
                case 'pending': return 'pending';
                case 'return': return 'return';
                case 'in progress': return 'inProgress';
                default: return '';
            }
        },
        copyToClipboard(link) {
            navigator.clipboard.writeText(link).then(() => {
                alert("Link copied to clipboard!");
            }).catch((error) => {
                console.error("Failed to copy link: ", error);
            });
        }
    },
    async mounted() {
        await this.fetchUserOrders();
    }
};
</script>

<style scoped>
/* Styling for the copy button */
.copy-btn {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 5px;
    font-size: 12px;
    transition: background-color 0.3s ease;
}

.copy-btn:hover {
    background-color: #0056b3;
}

.status.delivered { background-color: green; }
.status.pending { background-color: orange; }
.status.return { background-color: red; }
.status.inProgress { background-color: blue; }
</style>
