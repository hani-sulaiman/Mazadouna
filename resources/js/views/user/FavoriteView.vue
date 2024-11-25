<template>
    <div class="details">
        <div class="recentOrders">
            <div class="cardHeader">
                <h2>Favorite Auctions</h2>
            </div>

            <div class="products">
                <Auction
                    v-for="auction in favorites"
                    :key="auction.id"
                    :product_id="auction.auction.id"
                    :name="auction.auction.name"
                    :thumbnail="auction.auction.thumbnail_image"
                    :start_date="auction.auction.start_date"
                    :end_date="auction.auction.end_date"
                    :category="auction.auction.category_name"
                    :product_type="auction.auction.product_type"
                    :price="auction.auction.starting_price"
                    :status="auctionStatus(auction.auction)"
                    :isFavorite="isFavorite(auction.auction.id)"
                    @toggleFavorite="toggleFavorite"
                />
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import Auction from '../../components/inc/Auction.vue';

export default {
    components: { Auction },
    computed: {
        ...mapGetters('favorite', ['getFavorites']),
        favorites() {
            return this.getFavorites;
        },
    },
    methods: {
        ...mapActions('favorite', ['fetchFavorites', 'removeFavorite', 'addFavorite']),
        
        async toggleFavorite(auctionId) {
            const isFavorited = this.favorites.some(fav => fav.auction.id === auctionId);
            if (isFavorited) {
                const reminderId = this.favorites.find(fav => fav.auction.id === auctionId).id;
                await this.removeFavorite(reminderId);
            } else {
                await this.addFavorite(auctionId);
            }
        },
        isFavorite(auctionId) {
            return this.favorites.some(fav => fav.auction.id === auctionId);
        },
        auctionStatus(auction) {
            const now = new Date();
            const startDate = new Date(auction.start_date);
            const endDate = new Date(auction.end_date);
            if (now < startDate) return 'planned';
            if (now >= startDate && now <= endDate) return 'live';
            return 'ended';
        }
    },
    async mounted() {
        await this.fetchFavorites();
    },
};
</script>
