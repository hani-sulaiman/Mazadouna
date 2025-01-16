<template>
    <main class="live-action">
        <div class="show-sction details" v-if="auctionDetails">
            <!-- Countdown Timer if Auction is Planned -->
            <div v-if="isAuctionPlanned" class="flow">
                <div class="close"><i class="bi bi-x-circle-fill"></i></div>
                <div class="timer timer2">
                    <p>{{ remainingTime.days }} Days</p>
                    <p>{{ remainingTime.hours }}:{{ remainingTime.minutes }}:{{ remainingTime.seconds }}</p>
                </div>
            </div>

            <!-- Winner Section if Auction has Ended -->
            <div v-if="isAuctionEnded" class="flow">
                <div class="container">


                    <div class="close"><i class="bi bi-x-circle-fill"></i></div>
                    <div class="winner-name">
                        <span class="wn">Winner:</span>
                        <span>{{ winnerName }} - ₿{{ highestBidValue }}</span>
                    </div>
                    <div class="timer">
                        <p>Auction has ended</p>
                    </div>
                    <div class="bidding" v-if="isUserWinner">
                        <div class="pay" @click="Payment">
                            <button>Pay Now</button>
                            <p>Payment deadline: {{ paymentDeadline }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auction Details and Images -->
            <div class="viewer">
                <div class="image">
                    <!-- Use selectedImage as source -->
                    <img :src="selectedImage" alt="Auction Image">
                </div>

                <div class="side_img">
                    <img v-for="(image, index) in auctionDetails.more_images" :key="index" :src="image"
                        alt="Additional Image" style="cursor:pointer;" @click="selectedImage = image">
                    <!-- Update selectedImage on click -->
                </div>
            </div>
            <div class="content-side">
                <div class="title-car title-details">
                    <h2>{{ auctionDetails.name }}</h2>
                </div>

                <div class="info-car info-details">
                    <p><span class="clr">Type:</span> <span>{{ auctionDetails.product_type }}</span></p>
                    <p><span class="clr">Auction start date and time:</span> <span>{{ auctionDetails.start_date
                            }}</span>
                    </p>
                    <p><span class="clr">Auction End Date and Time:</span> <span>{{ auctionDetails.end_date }}</span>
                    </p>
                </div>

                <div class="information">
                    <div class="div123">
                        <div class="discrption">Description</div>
                    </div>
                    <div class="ifo-discription">{{ auctionDetails.description }}</div>
                </div>

                <div class="Opening-price details-Opening-price">
                    <p> Opening price: ₿ {{ auctionDetails.starting_price }}</p>
                </div>

                <!-- Join and Bid Button with Animated Eye Icon if Auction is Live -->
                <div v-if="isAuctionLive" class="join-bid-section">
                    <button @click="joinAndBid" class="join-bid-btn">
                        <i class="bi bi-eye-fill animated-eye"></i> Auction is Live! Join and Bid
                    </button>
                </div>
            </div>

        </div>
    </main>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
    data() {
        return {
            remainingTime: {
                days: 0,
                hours: "00",
                minutes: "00",
                seconds: "00",
            },
            countdownInterval: null,
            selectedImage: null,
        };
    },
    computed: {
        ...mapGetters("publicAuction", ["getAuctionDetails", "getWinner", "getHighestBid"]),
        ...mapGetters("auth", ["getUserId"]),
        auctionDetails() {
            return this.getAuctionDetails;
        },
        isAuctionPlanned() {
            const now = new Date();
            const start = new Date(this.auctionDetails.start_date);
            return now < start;
        },
        isAuctionLive() {
            const now = new Date();
            const start = new Date(this.auctionDetails.start_date);
            const end = new Date(this.auctionDetails.end_date);
            return now >= start && now <= end;
        },
        isAuctionEnded() {
            const now = new Date();
            const end = new Date(this.auctionDetails.end_date);
            return now > end;
        },
        winnerName() {
            return this.getWinner ? this.getWinner.name : "No winner";
        },
        highestBidValue() {
            return this.getHighestBid || this.auctionDetails.starting_price;
        },
        isUserWinner() {
            return this.getWinner && this.getWinner.id === this.getUserId;
        },
        paymentDeadline() {
            const paymentDeadlineDate = new Date(this.auctionDetails.end_date);
            paymentDeadlineDate.setDate(paymentDeadlineDate.getDate() + 7); // 7 days after auction end
            return paymentDeadlineDate.toISOString().split("T")[0];
        },
    },
    methods: {
        ...mapActions("publicAuction", ["getAuctionDetailsById", 'incrementViewCount']),
        ...mapActions("auth", ["fetchUser"]),
        ...mapActions("payment", ["order"]),
        joinAndBid() {
            this.$router.push({ name: "LiveAuction", params: { id: this.$route.params.id } });
        },
        async Payment() {
            await this.order(this.$route.params.id);
            alert('ordered !')
            // this.$router.push({ name: "PaymentPage", params: { auctionId: this.auctionDetails.id } });
        },
        updateCountdown() {
            const start = new Date(this.auctionDetails.start_date);
            const now = new Date();
            const timeDiff = start - now;

            if (timeDiff <= 0) {
                clearInterval(this.countdownInterval);
                this.remainingTime = { days: 0, hours: "00", minutes: "00", seconds: "00" };
            } else {
                const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);

                this.remainingTime = {
                    days,
                    hours: hours.toString().padStart(2, "0"),
                    minutes: minutes.toString().padStart(2, "0"),
                    seconds: seconds.toString().padStart(2, "0"),
                };
            }
        },
    },
    async mounted() {
        if(localStorage.getItem('user')){

            this.fetchUser();
        }
        const auctionId = this.$route.params.id;
        await this.getAuctionDetailsById(auctionId);

        // Set selectedImage to the thumbnail image initially
        if(this.auctionDetails) {
            this.selectedImage = this.auctionDetails.thumbnail_image;
        }

        if (this.isAuctionPlanned) {
            this.updateCountdown();
            this.countdownInterval = setInterval(this.updateCountdown, 1000);
        }
        await this.$store.dispatch("publicAuction/incrementViewCount", auctionId);
    },
    beforeDestroy() {
        if (this.countdownInterval) clearInterval(this.countdownInterval);
    },
};
</script>

<style scoped>
.flow {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 20px;
}

.close {
    margin-right: 10px;
}

.timer2 {
    display: flex;
    flex-direction: column;
    text-align: center;
    font-size: 24px;
    font-weight: bold;
    color: #ff4a4a;
}

.join-bid-section {
    text-align: center;
    margin-top: 20px;
    position: relative;
}

.join-bid-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    font-size: 18px;
    font-weight: bold;
    color: #fff;
    background: linear-gradient(135deg, #ff4a4a, #ff8c1a);
    border: none;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0px 4px 10px rgba(255, 72, 72, 0.5);
}

.join-bid-btn:hover {
    background: linear-gradient(135deg, #ff8c1a, #ff4a4a);
    box-shadow: 0px 6px 14px rgba(255, 72, 72, 0.7);
    transform: translateY(-2px);
}

.animated-eye {
    animation: eye-blink 1.5s infinite ease-in-out;
}

@keyframes eye-blink {

    0%,
    100% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.2);
    }
}

.winner-name {
    font-size: 20px;
    font-weight: bold;
    color: #4caf50;
}

.live-action .show-sction .flow .bidding .pay {
    cursor: pointer !important;
    flex-direction: column;
}

.live-action .show-sction .flow .bidding .pay button {
    background-color: transparent !important;
    border: none !important;
    color: #fff;
    font-weight: 900;
    font-size: 20px;
}

.live-action .show-sction .flow .container {
    position: static !important;
}

.live-action .show-sction .flow .bidding .pay p {
    font-size: 15px;
}

.live-action .show-sction .image img {
    width: 500px;
    border-radius: 30px;
    object-fit: cover;
    height: 500px;
}

.live-action .show-sction .viewer .image {
    width: 500px;
    height: 500px;
}

.live-action .show-sction .viewer {
    display: flex;
    gap: 30px;
}
</style>
