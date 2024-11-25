<template>
    <div class="form-container">
        <h2>Edit Auction</h2>
        <form @submit.prevent="submitAuction">
            <!-- Auction Title -->
            <div class="form-group">
                <label for="name">Title:</label>
                <input type="text" v-model="auction.name" id="name" placeholder="Enter auction title" required maxlength="255" :disabled="isCompleted" />
            </div>

            <!-- Starting Price -->
            <div class="form-group">
                <label for="starting_price">Opening Price:</label>
                <input type="number" v-model="auction.starting_price" id="starting_price" placeholder="Enter starting price" required :disabled="isCompleted" />
            </div>

            <!-- Minimum Increment -->
            <div class="form-group">
                <label for="min_increment">Minimum Increment:</label>
                <input type="number" v-model="auction.min_increment" id="min_increment" placeholder="Enter minimum increment" required :disabled="isCompleted" />
            </div>

            <!-- Start and End Date -->
            <div class="form-group">
                <label for="start_date">Auction Start Date and Time:</label>
                <input type="datetime-local" v-model="auction.start_date" id="start_date" required :disabled="isCompleted" />
            </div>

            <div class="form-group">
                <label for="end_date">Auction End Date and Time:</label>
                <input type="datetime-local" v-model="auction.end_date" id="end_date" required :disabled="isCompleted" />
            </div>

            <!-- Category Selection -->
            <div class="form-group">
                <label for="categories">Category:</label>
                <select v-model="auction.categories" id="categories" multiple required :disabled="isCompleted">
                    <option disabled value="">Select at least one category</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.title }}
                    </option>
                </select>
            </div>

            <!-- Product Type -->
            <div class="form-group">
                <label for="product_type">Product Type:</label>
                <input type="text" v-model="auction.product_type" id="product_type" placeholder="Enter product type" required :disabled="isCompleted" />
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea v-model="auction.description" id="description" maxlength="2000" placeholder="Enter description (max 2000 characters)" :disabled="isCompleted"></textarea>
            </div>

            <!-- Shipping Details -->
            <div class="form-group">
                <label for="shipping_details">Shipping Details:</label>
                <textarea v-model="auction.shipping_details" id="shipping_details" maxlength="2000" placeholder="Enter shipping details" required :disabled="isCompleted"></textarea>
            </div>

            <!-- Tags -->
            <div class="form-group">
                <label for="tags">Tags:</label>
                <input type="text" v-model="auction.tags" id="tags" placeholder="Enter tags separated by commas" :disabled="isCompleted" />
            </div>

            <!-- Main Image Upload with Preview -->
            <div class="form-group">
                <label for="thumbnail_image">Main Image:</label>
                <input type="file" @change="handleMainImageUpload" id="thumbnail_image" accept="image/jpg,image/jpeg,image/png" :disabled="isCompleted" />
                <div v-if="thumbnailPreview" class="image-preview">
                    <img :src="thumbnailPreview" alt="Thumbnail Preview" />
                </div>
            </div>

            <!-- Additional Images Upload with Preview -->
            <div class="form-group">
                <label for="more_images">Additional Images:</label>
                <input type="file" @change="handleAdditionalImagesUpload" id="more_images" accept="image/jpg,image/jpeg,image/png" multiple :disabled="isCompleted" />
                <div class="image-preview" v-if="moreImagesPreview.length">
                    <img v-for="(src, index) in moreImagesPreview" :key="index" :src="src" alt="Additional Image Preview" />
                </div>
            </div>

            <!-- Submit button disabled if auction is completed -->
            <button type="submit" class="submit-button" :disabled="isCompleted">Save Changes</button>
        </form>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import apiClient from "../../axios";

export default {
    data() {
        return {
            auction: {
                name: "",
                starting_price: "",
                min_increment: "",
                start_date: "",
                end_date: "",
                categories: [],
                product_type: "",
                description: "",
                shipping_details: "",
                tags: [],
                thumbnail_image: null,
                more_images: [],
                status: ""
            },
            thumbnailPreview: null,
            moreImagesPreview: [],
        };
    },
    computed: {
        ...mapGetters("userAuction", ["getCategories"]),
        categories() {
            return this.getCategories;
        },
        isCompleted() {
            return this.auction.status === "completed";
        }
    },
    methods: {
        ...mapActions("userAuction", ["fetchCategories", "updateAuction"]),

        async fetchAuctionDetails() {
            const auctionId = this.$route.params.id;
            try {
                const response = await apiClient.get(`/user/auction/${auctionId}`);
                const auctionData = response.data.auction;
                this.auction = {
                    ...auctionData,
                    tags: auctionData.tags.join(", "),
                    categories: auctionData.categories.map((cat) => cat.id),
                };
                this.thumbnailPreview = auctionData.thumbnail_image;
                this.moreImagesPreview = auctionData.more_images || [];
            } catch (error) {
                console.error("Failed to fetch auction details:", error);
            }
        },

        async submitAuction() {
            if (this.isCompleted) return;

            const formData = new FormData();
            formData.append("name", this.auction.name || "");
            formData.append("starting_price", this.auction.starting_price || "");
            formData.append("min_increment", this.auction.min_increment || "");
            formData.append("start_date", this.auction.start_date || "");
            formData.append("end_date", this.auction.end_date || "");
            formData.append("product_type", this.auction.product_type || "");
            formData.append("description", this.auction.description || "");
            formData.append("shipping_details", this.auction.shipping_details || "");

            const tagsArray = Array.isArray(this.auction.tags)
                ? this.auction.tags
                : this.auction.tags.split(",").map((tag) => tag.trim());
            tagsArray.forEach((tag) => formData.append("tags[]", tag));

            if (this.auction.thumbnail_image instanceof File) {
                formData.append("thumbnail_image", this.auction.thumbnail_image);
            }

            this.auction.more_images.forEach((file, index) => {
                if (file instanceof File) {
                    formData.append(`more_images[${index}]`, file);
                }
            });

            if (this.auction.categories.length > 0) {
                this.auction.categories.forEach((cat) => formData.append("categories[]", cat));
            } else {
                alert("Please select at least one category.");
                return;
            }

            try {
                const auctionId = this.$route.params.id;
                await this.updateAuction({ auctionId, updatedData: formData });
                alert("Auction updated successfully!");
            } catch (error) {
                alert("Failed to update auction.");
            }
        },

        handleMainImageUpload(event) {
            const file = event.target.files[0];
            this.auction.thumbnail_image = file || null;
            if (file) {
                this.thumbnailPreview = URL.createObjectURL(file);
            }
        },

        handleAdditionalImagesUpload(event) {
            const files = Array.from(event.target.files);
            this.auction.more_images = files;
            this.moreImagesPreview = files.map((file) => URL.createObjectURL(file));
        },
    },
    async mounted() {
        await this.fetchCategories();
        await this.fetchAuctionDetails();
    },
};
</script>

<style scoped>
/* Styling same as the Create Auction form */
.form-container {
    max-width: 800px;
    margin: auto;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.form-group {
    margin-bottom: 20px;
}
.form-container label, h2 {
    color: #000;
}
.form-group label {
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border-radius: 4px;
    border: 1px solid #ccc;
}

.image-preview {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.image-preview img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.submit-button {
    background-color: #28a745;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
</style>
