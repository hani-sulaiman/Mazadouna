<template>
  <div class="container">
    <div :class="['navigation', { open: isOpen }]">
      <ul>
        <li>
          <router-link to="/">
            <span class="icon">
              <i class="bi bi-mastodon"></i>
            </span>
            <span class="title">Mazadouna.</span>
          </router-link>
        </li>
        <li>
          <router-link :to="{ name: 'AdminDashboard' }">
            <span class="icon">
              <i class="bi bi-speedometer2"></i>
            </span>
            <span class="title">Dashboard</span>
          </router-link>
        </li>
        <li>
          <router-link :to="{ name: 'CategoriesView' }">
            <span class="icon">
              <i class="bi bi-pencil-square"></i>
            </span>
            <span class="title">Categories</span>
          </router-link>
        </li>
        <li>
          <router-link :to="{ name: 'ManageOrdersAdmin' }">
            <span class="icon">
              <i class="bi bi-car-front-fill"></i>
            </span>
            <span class="title">Order</span>
          </router-link>
        </li>
        <li>
          <button @click="logout" class="logout-button">
            <span class="icon">
              <i class="bi bi-box-arrow-right"></i>
            </span>
            <span class="title">Sign Out</span>
          </button>
        </li>
      </ul>
    </div>
    <div class="main">
      <div class="topbar">
        <div class="toggle" @click="toggleNavigation">
          <i class="bi bi-list"></i>
        </div>
        <div class="user">
          <!-- <img src="" alt=""> -->
        </div>
      </div>
      <router-view />
    </div>
  </div>
</template>

<script>
import { mapActions } from "vuex";

export default {
  name: "AdminLayout",
  data() {
    return {
      isOpen: false,
    };
  },
  methods: {
    ...mapActions("auth", ["logout"]),
    toggleNavigation() {
      this.isOpen = !this.isOpen;
    },
  },
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

.navigation ul {
  top: 90px;
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

.navigation.open {
  /* Add your styles for the open state */
  transform: translateX(0);
  /* Example animation */
}
</style>
