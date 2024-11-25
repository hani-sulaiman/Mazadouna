<template>
  <div class="category-table-container">
    <!-- Create Category Button -->
    <router-link :to="{ name: 'CreateCategory' }" class="create-category-btn">
      Create Category
    </router-link>

    <!-- Category Table -->
    <vue-good-table
      :columns="columns"
      :rows="categories"
      :paginate="true"
      :lineNumbers="true"
      :searchOptions="{ enabled: true }"
    >
      <template #table-row="{ row, column }">
        <span v-if="column.field === 'actions'">
          <button @click="removeCategory(row.id)" class="delete-btn">
            Delete
          </button>
        </span>
      </template>
    </vue-good-table>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";

export default {
  components: {
    VueGoodTable,
  },
  data() {
    return {
      columns: [
        {
          label: "Category Name",
          field: "title",
        },
        {
          label: "Actions",
          field: "actions",
          sortable: false,
        },
      ],
    };
  },
  computed: {
    ...mapGetters("adminCategory", ["allCategories"]),
    categories() {
      return this.allCategories;
    },
  },
  methods: {
    ...mapActions("adminCategory", ["fetchCategories", "deleteCategory"]),
    async removeCategory(id) {
      try {
        await this.deleteCategory(id);
        alert("Category deleted successfully.");
      } catch (error) {
        alert("Failed to delete category.");
      }
    },
  },
  async mounted() {
    await this.fetchCategories();
  },
};
</script>

<style scoped>
/* Styling as provided in the initial code */
.category-table-container {
  max-width: 600px;
  margin: 20px auto;
  text-align: center;
}
.create-category-btn {
  display: inline-block;
  margin-bottom: 15px;
  padding: 10px 20px;
  background-color: #4caf50;
  color: white;
  border-radius: 4px;
  text-decoration: none;
  font-size: 16px;
  font-weight: bold;
  transition: background-color 0.3s;
}
.create-category-btn:hover {
  background-color: #45a049;
}
.delete-btn {
  padding: 5px 10px;
  color: white;
  background-color: #f44336;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
.delete-btn:hover {
  background-color: #d32f2f;
}
</style>
