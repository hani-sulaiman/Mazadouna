<template>
    <div class="email-verification">
      <h2>Email Verification</h2>
      <div v-if="loading">Verifying your email...</div>
      <div v-else>
        <div v-if="verified">
          <p>Your email has been successfully verified! You can now log in.</p>
          <router-link to="/sign" class="btn">Go to Sign In</router-link>
        </div>
        <div v-else>
          <p>Verification failed. Please try again or contact support.</p>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from '@/axios';
  
  export default {
    data() {
      return {
        loading: true,
        verified: false,
      };
    },
    async mounted() {
      const { id, hash } = this.$route.params;
      try {
        await axios.get(`/email/verify/${id}/${hash}`);
        this.verified = true;
      } catch (error) {
        this.verified = false;
        console.error(error);
      } finally {
        this.loading = false;
      }
    },
  };
  </script>
  
  <style scoped>
  .email-verification {
    text-align: center;
    margin-top: 2rem;
  }
  .email-verification .btn {
    display: inline-block;
    margin-top: 1rem;
    padding: 0.75rem 1.5rem;
    background: #1976d2;
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
  }
  </style>
  