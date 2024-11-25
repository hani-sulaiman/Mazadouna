<template>
  <div class="details">
    <div class="recentOrders">
      <div class="cardHeader">
        <h2>My Auctions</h2>
        <div class="btn1">
          <router-link :to="{ name: 'CreateAuction' }" class="btn">Create Auction</router-link>
        </div>
      </div>

      <table>
        <thead>
          <tr>
            <td>Image</td>
            <td>Product Name</td>
            <td>Price</td>
            <td>Category</td>
            <td>Status</td>
            <td>Option</td>
          </tr>
        </thead>

        <tbody>
          <tr v-for="auction in userAuctions" :key="auction.id">
            <td>
              <img
                :src="auction.thumbnail_image"
                alt="Product Image"
                style="width: 100px;"
              />
            </td>
            <td>{{ auction.name }}</td>
            <td>₿{{ auction.starting_price }}</td>
            <td>
              <span v-if="auction.categories && auction.categories.length">
                {{ auction.categories[0].title }}
              </span>
            </td>
            <td>
              <span :class="auction.status === 'approved' ? 'approved' : 'pending'">
                {{ auction.status }}
              </span>
            </td>
            <td>
              <!-- Only show Edit and Delete options if auction is not completed -->
              <router-link
                v-if="auction.status !== 'completed'"
                :to="{ name: 'EditAuction', params: { id: auction.id } }"
                class="status delivered"
                >Edit</router-link
              >
              <button
                v-if="auction.status !== 'completed'"
                @click="removeAuction(auction.id)"
                class="status return"
              >
                Delete
              </button>
              <!-- Show "Completed" message if auction status is completed -->
              <span v-else class="status completed">Completed</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
  computed: {
    ...mapGetters("userAuction", ["getUserAuctions"]),
    userAuctions() {
      return this.getUserAuctions;
    },
  },
  methods: {
    ...mapActions("userAuction", ["fetchUserAuctions", "deleteAuction"]),
    async removeAuction(auctionId) {
      try {
        await this.deleteAuction(auctionId);
        alert("Auction deleted successfully.");
      } catch (error) {
        alert("Failed to delete auction.");
      }
    },
  },
  async mounted() {
    await this.fetchUserAuctions();
  },
};
</script>

<style scoped>
.details {
  padding: 20px;
}

.recentOrders {
  background: #fff;
  border-radius: 10px;
  padding: 20px;
  margin-top: 20px;
}

.cardHeader {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.btn1 .btn {
  background: #009688;
  color: white;
  padding: 8px 20px;
  border-radius: 5px;
  text-decoration: none;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

table thead tr td {
  font-weight: bold;
  padding: 10px;
}

table tbody tr td {
  padding: 10px;
  text-align: center;
}

.status {
  padding: 5px 10px;
  border-radius: 5px;
  cursor: pointer;
}

.delivered {
  background-color: #4caf50;
  color: white;
}

.return {
  background-color: #f44336;
  color: white;
}

.completed {
  background-color: #888;
  color: white;
  cursor: default;
}
</style>
