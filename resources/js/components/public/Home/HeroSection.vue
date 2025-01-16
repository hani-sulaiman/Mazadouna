<template>
    <div class="slider">
        <!-- Main Image Display -->
        <div class="list">
            <div class="item active">
                <img :src="currentImage.src" />
                <div class="content">
                    <p>
                        <i v-for="star in Math.floor(currentImage.stars)" :key="star" class="bi bi-star-fill"></i>
                        <i v-if="currentImage.stars % 1 !== 0" class="bi bi-star-half"></i>
                    </p>
                    <h2>{{ currentImage.title }} - Your Dream Ride Awaits!</h2>
                    <p>{{ currentImage.description }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation Arrows -->
        <div class="arrows">
            <button @click="prevImage">
                < </button>
                    <button @click="nextImage">></button>
        </div>

        <!-- Thumbnail Navigation -->
        <div class="thumbnail">
            <div v-for="(image, index) in images" :key="index" :class="['item', { active: index === currentIndex }]"
                @click="selectImage(index)">
                <img :src="image.src" />
                <div class="content">{{ image.title }}</div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            currentIndex: 0, // Tracks the current image index
            intervalId: null, // To store the interval ID
            images: [
                {
                    src: '/assets/img/wallpaperflare.com_wallpaper (3).jpg',
                    title: 'Lamborghini',
                    description: 'Discover unparalleled value at our auto auction event...',
                    stars: 4.5
                },
                {
                    src: '/assets/img/wallpaperflare.com_wallpaper (4).jpg',
                    title: 'Lamborghini',
                    description: 'Discover unparalleled value at our auto auction event...',
                    stars: 4.5
                },
                {
                    src: '/assets/img/wallpaperflare.com_wallpaper (5).jpg',
                    title: 'Lamborghini',
                    description: 'Discover unparalleled value at our auto auction event...',
                    stars: 4.5
                },
                {
                    src: '/assets/img/add-auction-img.jpg',
                    title: 'Lamborghini',
                    description: 'Discover unparalleled value at our auto auction event...',
                    stars: 4.5
                },
                {
                    src: '/assets/img/wallpaperflare.com_wallpaper (8).jpg',
                    title: 'Lamborghini',
                    description: 'Discover unparalleled value at our auto auction event...',
                    stars: 4.5
                }
            ]
        };
    },
    computed: {
        currentImage() {
            return this.images[this.currentIndex];
        }
    },
    methods: {
        nextImage() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
        },
        prevImage() {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        },
        selectImage(index) {
            this.currentIndex = index;
        },
        startAutoSlide() {
            // Starts the automatic image transition
            this.intervalId = setInterval(this.nextImage, 5000); // 5000ms = 5 seconds
        },
        stopAutoSlide() {
            // Stops the automatic image transition
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
    },
    mounted() {
        this.startAutoSlide(); // Start the interval when the component is mounted
    },
    beforeDestroy() {
        this.stopAutoSlide(); // Clear the interval when the component is destroyed
    }
};
</script>
