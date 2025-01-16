<template>
  <div class="container">
    <div :class="['navigation', { open: isOpen }]">
      <ul>
        <RouterLink to="/" style="text-decoration: none;">
        <li>
          <a href="/">
            <span class="icon">
              <i class="bi bi-mastodon"></i>
            </span>
            <span class="title">Mazadouna.</span>
          </a>
        </li>
      </RouterLink>
        <li>
          <router-link :to="{ name: 'UserDashboard' }">
            <span class="icon">
              <i class="bi bi-speedometer2"></i>
            </span>
            <span class="title">Home</span>
          </router-link>
        </li>
        <li>
          <router-link :to="{ name: 'ManageAuctions' }">
            <span class="icon">
              <i class="bi bi-vector-pen"></i>
            </span>
            <span class="title">Manage Auctions</span>
          </router-link>
        </li>
        <li>
          <router-link :to="{ name: 'Favorites' }">
            <span class="icon">
              <i class="bi bi-bell-fill"></i>
            </span>
            <span class="title">Favorite Auction</span>
          </router-link>
        </li>
        <li>
          <router-link :to="{ name: 'UserOrders' }">
            <span class="icon">
              <i class="bi bi-car-front-fill"></i>
            </span>
            <span class="title">Order</span>
          </router-link>
        </li>
        <!-- Sign Out Button -->
        <li>
          <button @click="logout" class="logout-button">
            <span class="icon">
              <i class="bi bi-box-arrow-right"></i>
            </span>
            <span class="title">Sign Out</span>
          </button>
        </li>
      </ul>
      <p class="myemail">Email: {{ userEmail }}</p>
    </div>
    
    <div class="main">
      <div class="topbar">
        <div class="toggle" @click="toggleNavigation">
          <i class="bi bi-list"></i>
        </div>
        <div class="user">
          <!-- Profile Image or Icon Here -->
        </div>
      </div>
      <router-view />
    </div>
  </div>
</template>

<script>
import { RouterLink } from 'vue-router';
import { mapActions, mapGetters } from 'vuex';

export default {
  name: 'UserLayout',
  data() {
    return {
      isOpen: false,
    };
  },
  computed: {
    ...mapGetters({
      userEmail: 'auth/getEmailUser' // Assuming the getter for user email is defined
    })
  },
  methods: {
    ...mapActions('auth', ['logout', 'fetchUser']), // Import the logout action from the auth module
    toggleNavigation() {
      this.isOpen = !this.isOpen;
    }
  },
  mounted() {
    this.fetchUser();
  }
};
</script>

<style scoped>
.logout-button {
  cursor: pointer;
  border: none;
  background-color: transparent;
  position: relative;
  width: 100%;
  display: flex;
  text-decoration: none;
  color: var(--white);
}
.logout-button:hover {
  color: #000;
}
.logout-button .icon {
  position: relative;
  display: block;
  min-width: 60px;
  height: 60px;
  line-height: 64px;
  text-align: center;
}
.logout-button .title {
  position: relative;
  display: block;
  padding: 0 10px;
  height: 60px;
  line-height: 60px;
  text-align: start;
  white-space: nowrap;
}
.icon i {
  font-size: 1.75rem;
}
.logout-button:hover {
  text-decoration: underline;
}

.navigation.open{
    width: 100%;
    left: 0%;
}
.navigation ul{
  top:55px
}
</style>
