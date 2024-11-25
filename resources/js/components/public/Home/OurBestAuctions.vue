<template>
    <section class="Our-Beast-Cars ">
        <h3 class="Our-Beast-cars-title">Our Beast Cars!</h3>
        <div class="products">
            <Auction
                v-for="auction in filteredAuctions"
                :key="auction.id"
                :product_id="auction.id"
                :name="auction.name"
                :thumbnail="auction.thumbnail_image"
                :start_date="auction.start_date"
                :end_date="auction.end_date"
                :category="auction.category_name"
                :product_type="auction.product_type"
                :price="auction.starting_price"
                :status="auctionStatus(auction)"
            />
        </div>
    </section>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import Auction from "@/components/inc/Auction.vue";

export default {
    components: {
        Auction,
    },
    computed: {
        ...mapGetters("publicAuction", ["getAuctions"]),
        filteredAuctions() {
            return this.getAuctions;
        },
    },
    methods: {
        ...mapActions("publicAuction", ["fetchLatestApprovedAuctions"]),
        auctionStatus(auction) {
            const now = new Date();
            const startDate = new Date(auction.start_date);
            const endDate = new Date(auction.end_date);
            if (now < startDate) return "planned";
            if (now >= startDate && now <= endDate) return "live";
            return "ended";
        },
    },
    async mounted() {
        await this.fetchLatestApprovedAuctions();
    },
};
</script>
