<template>
  <header>
    <div class="nav">
      <div class="logo">Mazadouna.</div>
      <ul class="menu">
        <router-link to="/">Home</router-link>
        <router-link :to="{ name: 'SearchPage' }">Auctions</router-link>
        <router-link :to="{ name: 'AboutUs' }">About</router-link>
        <router-link :to="{ name: 'ContactUs' }">Contact</router-link>
      </ul>
      <div class="btn">
        <div v-if="isAuthenticated" class="profile-section">
          <router-link :to="profileLink" class="btn1">Profile</router-link>
        </div>
        <router-link v-else :to="{ name: 'SignUser' }" class="btn1">Sign</router-link>
      </div>
      <!-- Hamburger Icon -->
      <div class="toggle_btn" @click="toggleMenu">
        <i class="bi bi-list header-icon"></i>
      </div>
      <!-- Dropdown Menu -->
      <div v-show="menuOpen" class="dropdown_menu">
        <router-link to="/">Home</router-link>
        <router-link :to="{ name: 'SearchPage' }">Auctions</router-link>
        <router-link :to="{ name: 'AboutUs' }">About</router-link>
        <router-link :to="{ name: 'ContactUs' }">Contact</router-link>
        <div class="ic">
          <a href="#"><i class="bi bi-instagram"></i></a>
          <a href="#"><i class="bi bi-twitter"></i></a>
          <a href="#"><i class="bi bi-whatsapp"></i></a>
          <a href="#"><i class="bi bi-facebook"></i></a>
        </div>
      </div>
    </div>
  </header>

  <router-view />

  <footer class="ftr">
    <div class="container">
      <div class="info-pages">
        <h3 class="title-ftr-pages">Mazadouna.</h3>
        <ul class="#">

          <li class="ftr-link"><router-link to="/" class="link">Home</router-link></li>
          <li class="ftr-link"><router-link :to="{ name: 'SearchPage' }" class="link">Auctions</router-link></li>
          <li class="ftr-link"><router-link :to="{ name: 'AboutUs' }" class="link">About Us</router-link></li>
          <li class="ftr-link"><router-link :to="{ name: 'ContactUs' }" class="link">Contact Us</router-link></li>
        </ul>
      </div><!-- info-pages -->
      <!-- -------------------------------------------------- -->
      <div class="info-input">
        <div class="info-overflow">
          <h3 class="title-info-input">mail us</h3>
          <p>You can ask your inquiry here</p>
          <input class="btn2" type="text" placeholder="Type your Masseg....">
          <input class="btn2 bn" type="button" value="Send">

          <li class="ftr-link"><router-link :to="{ name: 'ContactUs' }" class="link"><i
                class=" bi-geo-alt-fill sdicon"></i>location</router-link ></li>
          <li class="ftr-link"><router-link :to="{ name: 'ContactUs' }" class="link">Contact us:+963982389899</router-link ></li>

          <li class="ftr-link"><router-link :to="{ name: 'ContactUs' }" class="link">Email:mazadouna@gmail.com</router-link ></li>
        </div>

      </div><!-- info-input -->
      <!-- -------------------------------------------------- -->
      <div class="info-pages info-pages-2 ">
        <ul class="#">
          <a href=""><i class="bi bi-instagram ic"></i></a>
          <a href=""><i class="bi bi-twitter ic"></i></a>
          <a href=""><i class="bi bi-whatsapp ic"></i></a>
          <a href=""><i class="bi bi-facebook ic"></i></a>
          <router-link :to="{ name: 'ContactUs' }" class="link" > <i class="ftr-link h"></i>info</router-link >
          <router-link :to="{ name: 'ContactUs' }" class="link" > <i class=" bi-geo-alt-fill sdicon"></i>location</router-link >
          <router-link :to="{ name: 'ContactUs' }" class="link" > <i class="ftr-link"></i> Address:lattakia</router-link >


        </ul>
      </div>
    </div>


  </footer>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  name: "PublicLayout",
  data() {
    return {
      menuOpen: false // Track the dropdown menu's open state
    };
  },
  computed: {
    ...mapGetters("auth", ["isAuthenticated", "isAdmin"]),
    profileLink() {
      return this.isAdmin ? "/admin" : "/user";
    }
  },
  methods: {
    toggleMenu() {
      this.menuOpen = !this.menuOpen;
    }
  }
};
</script>