<template>
    <div>
        <vue-good-table
            :columns="columns"
            :rows="auctions"
            :pagination-options="{ enabled: true, perPage: 5 }"
        >
            <template #table-row="props">
                <span v-if="props.column.field === 'thumbnail_image'">
                    <img :src="props.row.thumbnail_image" alt="Thumbnail" width="50" />
                </span>
                <span v-else-if="props.column.field === 'actions'">
                    <button @click="approve(props.row.id)" class="approve-button">Approve</button>
                    <button @click="reject(props.row.id)" class="reject-button">Reject</button>
                </span>
                <span v-else>{{ props.formattedRow[props.column.field] }}</span>
            </template>
        </vue-good-table>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";

export default {
    components: { VueGoodTable },
    data() {
        return {
            columns: [
                { label: "Product Name", field: "name" },
                { label: "Thumbnail", field: "thumbnail_image" },
                { label: "Price", field: "starting_price" },
                { label: "Start Date", field: "start_date" },
                { label: "End Date", field: "end_date" },
                { label: "User Email", field: "user_email" },
                { label: "Actions", field: "actions" },
            ],
        };
    },
    computed: {
        ...mapGetters("adminAuction", ["pendingAuctions"]),
        auctions() {
            return this.pendingAuctions;
        },
    },
    methods: {
        ...mapActions("adminAuction", ["fetchPendingAuctions", "approveAuction", "rejectAuction"]),
        async approve(auctionId) {
            await this.approveAuction(auctionId);
            this.fetchPendingAuctions();
        },
        async reject(auctionId) {
            await this.rejectAuction(auctionId);
            this.fetchPendingAuctions();
        },
    },
    async created() {
        await this.fetchPendingAuctions();
    },
};
</script>
<style scoped>
.approve-button{
    background-color: rgb(33, 173, 33);

}
.reject-button:hover{
    background-color: rgb(149, 34, 26);
    
}
.reject-button{
    background-color: rgb(189, 51, 41);

}
.approve-button:hover{
    background-color: rgb(24, 145, 24)
}
.approve-button , .reject-button{
    cursor: pointer;
    color: #fff;
    border: none;
    border-radius: 3px;
    padding: 3px 10px;
    margin: 0px 5px;
}
</style>