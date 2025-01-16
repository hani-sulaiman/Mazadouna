<template>
    <main class="live-action">
        <!-- Bid Increment/Decrement Section (Only for Joined Users, Not for Owners) -->
        <div class="flow-bidding-box" v-if="isUserJoined && !isOwner">
            <div class="input-pay">
                <button class="decrease" @click="decreaseBid">-</button>
                <input class="text" type="number" v-model="currentBid" step="0.0001" readonly />
                <button class="increase" @click="increaseBid">+</button>
            </div>
            <button class="bid" @click="bid_auction">Place Bid</button>
        </div>
        <div class="container-live">
            <div class="image">
                <img :src="auctionDetails.thumbnail_image" class="live-img" alt="Auction Image" />
            </div>

            <div class="content-container">
                <div class="auction-head">
                    <div class="more-content">
                        <div class="title-car">
                            <h2>{{ auctionDetails.name }}</h2>
                            <p>{{ auctionDetails.description }}</p>
                        </div>



                    </div>


                    <!-- Auction Information -->
                    <div class="info-car">
                        <p><span class="clr">Type:</span> <span>{{ auctionDetails.product_type }}</span></p>
                        <p><span class="clr">Auction Start:</span> <span>{{ auctionDetails.start_date }}</span></p>
                        <p><span class="clr">Auction End:</span> <span>{{ auctionDetails.end_date }}</span></p>
                    </div>

                    <!-- Current Price Display -->
                    <div class="Opening-price">
                        Current price: <p>₿{{ current_price.toFixed(4) }}</p>
                    </div>
                    <!-- Join or Bid Section -->
                    <div v-if="!isUserJoined && !isOwner" class="join-section">
                        <button @click="join">Join</button>
                    </div>
                </div>
                <!-- Live Status and Bid Chat -->
                <div class="chat-action">
                    <div class="veiw">
                        <div class="live">Live</div>
                        <div class="count">
                            <i class="bi bi-eye"></i>
                            <span>{{ viewersCount }}</span>
                        </div>
                    </div>

                    <div class="chat">
                        <div v-for="(bid, index) in bidChat" :key="index" class="chat-1">
                            <p><i class="bi bi-currency-bitcoin"></i> <span>{{ bid.bid_value }} : {{ bid.username
                                    }}</span> </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Auction Image and Details -->

    </main>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import Auction from "../../components/inc/Auction.vue";

export default {
    components: {
        Auction
    },
    data() {
        return {
            currentBid: 0,
            updateInterval: null,

        };
    },
    computed: {
        ...mapGetters("BidAuction", ["getRelatedAuctions", "getAuctionDetails", "getCurrentPrice", "getHighestBid", "getBidChat", "isUserJoined", "isOwner", "isLive", "getViewersCount"]),
        relatedAuctions() {
            return this.getRelatedAuctions;
        },
        auctionDetails() {
            return this.getAuctionDetails;
        },
        current_price() {
            return this.getCurrentPrice;
        },
        HighestBid() {
            return this.getHighestBid;
        },
        bidChat() {
            return this.getBidChat;
        },
        viewersCount() {
            return this.getViewersCount;
        },
    },
    methods: {
        ...mapActions("BidAuction", ["fetchAuctionDetails", 'fetchRelatedAuctions', "joinAuction", "placeBid", "fetchBidsAndViewers"]),
        auctionStatus(auction) {
            const now = new Date();
            const startDate = new Date(auction.start_date);
            const endDate = new Date(auction.end_date);

            if (now < startDate) return 'planned';
            if (now >= startDate && now <= endDate) return 'live';
            if (now > endDate) return 'ended';
            return '';
        },
        async bid_auction() {
            await this.placeBid(this.currentBid);
            this.updateCurrentBid();
        },

        async join() {
            const auctionId = this.$route.params.id;
            await this.joinAuction(auctionId);
            alert('Joined Success');
            window.location.reload();
        },

        async loadAuctionDetails() {
            const auctionId = this.$route.params.id;
            await this.fetchAuctionDetails(auctionId);
            if (new Date() > new Date(this.auctionDetails.end_date)) {
                this.$router.push({ name: "AuctionDetails", params: { id: auctionId } });
                return;
            }
            this.updateCurrentBid(); // تعيين قيمة `currentBid` بناءً على `current_price`
        },

        increaseBid() {
            const minIncrement = parseFloat(this.auctionDetails.min_increment);
            this.currentBid = (parseFloat(this.currentBid) + minIncrement).toFixed(4);
            this.currentBid = parseFloat(this.currentBid); // Optional: Convert back to a number if needed
        },

        decreaseBid() {
            const minIncrement = parseFloat(this.auctionDetails.min_increment);
            const minimumBid = parseFloat(this.current_price) + minIncrement;
            if (this.currentBid > minimumBid) {
                this.currentBid = (this.currentBid - minIncrement).toFixed(4);
                this.currentBid = parseFloat(this.currentBid); // Optional: Convert back to a number if needed
            }
        },

        updateCurrentBid() {
            const minIncrement = parseFloat(this.auctionDetails.min_increment) || 1;
            const highestOrStartingPrice = Math.max(parseFloat(this.HighestBid) || 0, parseFloat(this.current_price) || 0);

            if (this.currentBid <= highestOrStartingPrice) {
                this.currentBid = (highestOrStartingPrice + minIncrement).toFixed(4);
                this.currentBid = parseFloat(this.currentBid); // Optional: Convert back to a number if needed
            }
        },

        startBidUpdates() {
            const auctionId = this.$route.params.id;
            this.updateInterval = setInterval(async () => {
                await this.fetchBidsAndViewers(auctionId);
                this.updateCurrentBid(); // تحديث `currentBid` بعد كل جلب
            }, 5000);
        }
    },
    async mounted() {
        const auctionId = this.$route.params.id;
        await this.fetchBidsAndViewers(auctionId);
        await this.loadAuctionDetails();
        this.startBidUpdates();
    },
    beforeDestroy() {
        if (this.updateInterval) clearInterval(this.updateInterval);
    },
};
</script>

<style scoped>
.live-img {
    width: 80%;
    border-radius: 10px;
    margin: 20px 32px 0px 68px
}

.image {
    flex-basis: 50%;
}

.chat-1 {
    align-items: start;
}

.chat-1 p span {
    display: block;
    margin-left: 33px;
}

.join-section button {
    background-color: #4CAF50;
    border-radius: 3px;
    color: #fff;
    font-size: 18px;
    font-weight: 500;
    border: none;
    cursor: pointer;

    padding: 5px 15px;
}

.join-section button:hover {
    background-color: #307b32;
}

.chat {
    width: 305px;
    max-height: 80vh;
}

.live-action .chat-action {
    width: 350px !important;
    position: absolute;
    right: 0;
}

.container-live {
    display: flex;
    padding: 82px 0px;

}

.live-action .chat-action {
    width: 100%;
}

.content-container {
    display: flex;
}

.auction-head {
    display: flex;
    flex-direction: column;
    max-width: 360px;
}

/* تنسيقات CSS */
.chat-1 {
    height: 45px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.text {
    width: fit-content;
}

.bi-currency-bitcoin {
    top: 14% !important;
    left: 2% !important;
    font-size: 25px !important;
}

.flow-bidding-box {
    left: 50%;
    top: 70%;
    transform: translateX(-50%);
}

@media only screen and (max-width: 600px) {
    .live-action .chat-action {
        width: 100% !important;
    }

    .live-action .chat-action .chat .chat-1 {
        width: 92%;
    }

    .flow-bidding-box {
        top: 78vh !important;
        width: 70%;
    }

    .container-live {
        flex-direction: column;
    }

    .live-img {
        margin: 28px 0px 0px 39px;
    }

    .chat-action {
        right: unset;
        left: 0;
    }

    .live-action .chat-action .chat {
        width: 100% !important;
    }
}
</style>
