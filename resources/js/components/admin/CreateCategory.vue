<template>
  <div class="form-container">
    <h2>Create Category</h2>
    <form @submit.prevent="addCategory">
      <input
        v-model="categoryName"
        type="text"
        placeholder="Enter category name"
        required
      />
      <button type="submit">Add</button>
    </form>
  </div>
</template>

<script>
import { mapActions } from "vuex";

export default {
  data() {
    return {
      categoryName: "",
    };
  },
  methods: {
    ...mapActions("adminCategory", ["createCategory"]), // ربط action من Vuex

    async addCategory() {
      if (this.categoryName.trim()) {
        try {
          await this.createCategory({ title: this.categoryName });
          alert(`Category "${this.categoryName}" added successfully!`);
          this.categoryName = ""; // تفريغ الحقل بعد الإضافة
        } catch (error) {
          alert("Failed to add category. Please try again.");
        }
      }
    },
  },
};
</script>

  <style scoped>
  /* Basic styling for the form container */
  .form-container {
    max-width: 400px;
    margin: 20px auto;
    padding: 20px;
    background-color: #f9f9f9;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    text-align: center;
  }
  
  .form-container h2 {
    margin-bottom: 15px;
    font-size: 1.5rem;
    color: #333;
  }
  
  /* Input and button styling */
  input[type="text"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
  }
  
  button[type="submit"] {
    width: 100%;
    padding: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  
  button[type="submit"]:hover {
    background-color: #45a049;
  }
  
  /* Responsive design */
  @media (max-width: 600px) {
    .form-container {
      padding: 15px;
    }
    input[type="text"], button[type="submit"] {
      font-size: 0.9rem;
      padding: 8px;
    }
    .form-container h2 {
      font-size: 1.3rem;
    }
  }
  </style>
  