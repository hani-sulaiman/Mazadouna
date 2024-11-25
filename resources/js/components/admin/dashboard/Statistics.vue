<template>
    <div class="cardBox">
        <div class="card" v-for="(stat, index) in statistics" :key="index">
            <div>
                <div class="numbers">{{ formatNumber(stat.value) }}</div>
                <div class="cardName">{{ stat.label }}</div>
            </div>
            <div class="iconBx">
                <i :class="stat.icon"></i>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
    computed: {
        ...mapGetters('adminDashboard', ['getStatistics']),
        statistics() {
            // Format statistics for display with icons and labels
            const stats = this.getStatistics;
            return [
                { label: 'Daily Views', value: stats.daily_views, icon: 'bi bi-eye' },
                { label: 'Winning Auctions', value: stats.winning_auctions, icon: 'bi bi-award' },
                { label: 'Monthly Auctions', value: stats.monthly_auctions, icon: 'bi bi-calendar-event' },
                { label: 'Earning', value: `$${stats.earning.toFixed(2)}`, icon: 'bi bi-currency-bitcoin' },
            ];
        },
    },
    methods: {
        ...mapActions('adminDashboard', ['fetchStatistics']),
        formatNumber(value) {
            return typeof value === 'number' ? value.toLocaleString() : value;
        },
    },
    async mounted() {
        await this.fetchStatistics();
    },
};
</script>

<style scoped>
.cardBox {
    display: flex;
    gap: 20px;
}
.card {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.card .numbers {
    font-size: 24px;
    font-weight: bold;
}
.card .cardName {
    font-size: 14px;
    color: #888;
}
.iconBx {
    font-size: 30px;
    color: #333;
}
</style>
