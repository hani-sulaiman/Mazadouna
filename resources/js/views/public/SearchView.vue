<template>
    <div class="search-page">
        <h3 class="Our-Beast-cars-title filter-til">See More Action</h3>

        <!-- Filter Buttons -->
        <div id="buttons">
            <div class="container-buttons">
                <button class="button-value" :class="{ 'selected-filter': filterType === 'all' }"
                    @click="filterAuctions('all')">
                    All
                </button>
                <button class="button-value" :class="{ 'selected-filter': filterType === 'planned' }"
                    @click="filterAuctions('planned')">
                    Planned Auctions
                </button>
                <button class="button-value" :class="{ 'selected-filter': filterType === 'live' }"
                    @click="filterAuctions('live')">
                    Live Auctions
                </button>
                <button class="button-value" :class="{ 'selected-filter': filterType === 'ended' }"
                    @click="filterAuctions('ended')">
                    Ended Auctions
                </button>
            </div>


            <!-- Search Input -->
            <div class="search">
                <i class="bi bi-search"></i>
                <input type="search" placeholder="Search Here..." class="button-value" v-model="searchQuery"
                    @input="performSearch" />
            </div>
        </div>

        <!-- Auction Results -->
        <section class="Our-Beast-Cars">
            <div class="products">
                <Auction v-for="auction in filteredAuctions" :key="auction.id" :product_id="auction.id"
                    :name="auction.name" :thumbnail="auction.thumbnail_image" :start_date="auction.start_date"
                    :end_date="auction.end_date" :category="auction.category_name" :product_type="auction.product_type"
                    :price="auction.starting_price" :status="auctionStatus(auction)" />
            </div>
        </section>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import Auction from "../../components/inc/Auction.vue";
export default {
    data() {
        return {
            searchQuery: "",
            filterType: "all",
        };
    },
    components: {
        Auction
    },
    computed: {
        ...mapGetters("publicAuction", ["getAuctions"]),
        filteredAuctions() {
            return this.getAuctions.filter((auction) => {
                if (this.filterType === "planned") return new Date(auction.start_date) > new Date();
                if (this.filterType === "live") return new Date() >= new Date(auction.start_date) && new Date() <= new Date(auction.end_date);
                if (this.filterType === "ended") return new Date(auction.end_date) < new Date();
                return true;
            });
        },
    },
    methods: {
        ...mapActions("publicAuction", ["searchAuctions"]),
        auctionStatus(auction) {
            const now = new Date();
            const startDate = new Date(auction.start_date);
            const endDate = new Date(auction.end_date);

            if (now < startDate) return 'planned';
            if (now >= startDate && now <= endDate) return 'live';
            if (now > endDate) return 'ended';
            return '';
        },
        async performSearch() {
            await this.searchAuctions({
                search: this.searchQuery,
                status: "approved",
            });
        },
        async filterAuctions(type) {
            this.filterType = type;
            await this.performSearch();
        },
    },
    async mounted() {
        this.filterAuctions("all");
    },
};
</script>

<style scoped>
.selected-filter {
    background-color: #eac452; /* You can change this color */
    color: #000; /* Text color when selected */
    transition: 0.2s;
}

</style>
