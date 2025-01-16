<template>
    <section class="add-auction">

        <div class="auction">
            <div class="action-info">
                <h3 class="auction-title">Add Your Auction Now</h3>
                <div class="action-span1">
                    <span class="auction-span">Rev Up Your Savings at Our
                        Exclusive Car Auction
                        Unleash your car and elevate your journey</span>
                </div>
                <input class="add-product-btn" @click="AddAuction" type="button" value="+Add Product">
            </div>
            <div class="auction-img"></div>
        </div>
    </section>
</template>
<script>
export default{
    methods: {
        AddAuction() {
            this.$router.push('/user/manage-auctions/create')
        }
    }
}
</script>
<style>
.add-product-btn{
    cursor: pointer;
}
</style>