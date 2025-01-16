<template>
    <div class="product1">
        <div class="btn-planner" :class="statusClass">{{ statusLabel }}</div>

        <div class="pr1">
            <div v-if="isAuthenticated">
                <!-- Bell icon for favorite with dynamic styling based on favorite status -->
                <i class="bi bi-bell-fill" :class="{ 'favorited': isFavorite }" @click="toggleFavorite"></i>
            </div>
            <img :src="thumbnail" alt="Auction Image">
        </div>

        <div class="pr2">
            <p class="pr2-p1">{{ name }}</p>
            <div class="pr2-info-1">
                <i class="bi bi-calendar"></i>
                <span class="pr2-info">Auction Start: {{ start_date }}</span>
                <br>
                <i class="bi bi-calendar-check"></i>
                <span class="pr2-info">Auction End: {{ end_date }}</span>
                <br>
                <i class="bi bi-box"></i>
                <span class="pr2-info">Auction Type: {{ product_type }}</span>
                <span class="pr2-info">{{ category }}</span>
            </div>
            <span class="pr2-p1 prs">{{ price }} ₿</span>
            <div class="product-link">
                <button @click="navigateToProduct(product_id)">
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
    props: {
        product_id: String,
        name: String,
        thumbnail: String,
        start_date: String,
        end_date: String,
        product_type: String,
        category: String,
        price: Number,
        status: String,
    },
    computed: {
        ...mapGetters('auth', ['isAuthenticated']),
        ...mapGetters('favorite', ['getFavorites']),
        isFavorite() {
            // Check if this auction is in the list of favorites
            return this.getFavorites.some(fav => fav.auction_id === this.product_id);
        },
        statusLabel() {
            if (this.status === 'planned') return 'Planned';
            if (this.status === 'live') return 'Live';
            if (this.status === 'ended') return 'Ended';
            return '';
        },
        statusClass() {
            if (this.status === 'planned') return 'planned-tag';
            if (this.status === 'live') return 'live-tag';
            if (this.status === 'ended') return 'ended-tag';
            return '';
        },
    },
    methods: {
        ...mapActions('favorite', ['addFavorite', 'removeFavorite', 'fetchFavorites']),
        navigateToProduct(product_id) {
            this.$router.push({ name: 'AuctionDetails', params: { id: product_id } });
        },
        async toggleFavorite() {
            if (!this.isAuthenticated) return; // Prevent if user is not authenticated
            if (this.isFavorite) {
                // Find the favorite ID to remove by auction_id
                const reminder = this.getFavorites.find(fav => fav.auction_id === this.product_id);
                if (reminder) {
                    await this.removeFavorite(this.product_id); // Remove from favorites
                }
            } else {
                await this.addFavorite(this.product_id); // Add to favorites
            }
        },
    },
    created() {
        this.fetchFavorites()
    }
};
</script>

<style scoped>
/* تصميم العلامة */
.status-tag {
    color: #fff;
    font-weight: bold;
    padding: 4px 8px;
    border-radius: 4px;
    text-align: center;
    margin-bottom: 8px;
    z-index: 1;
    right: 11px;
    top: 20px;
    position: absolute;
}
.favorited{
    color: yellow;
}
.prs{
    font-size: 20px !important;
    color: #990000;
}
.pr1 img {
    width: 100% !important;
    height: 100% !important;
    margin: unset !important;
}
.planned-tag {
    background-color: blue;
}
.live-tag {
    background-color: red;
}
.ended-tag {
    background-color: black;
}
</style>
