<template>
    <HeroSection/>
    <!-- <ExperienceSection/> -->
    <OurBestAuctions/>
    <AddAuctionSection/>
    <ServicesSection/>
</template>
<script>
import HeroSection from '@/components/public/Home/HeroSection.vue';
import ExperienceSection from '@/components/public/Home/ExperienceSection.vue';
import OurBestAuctions from '@/components/public/Home/OurBestAuctions.vue';
import AddAuctionSection from '@/components/public/Home/AddAuctionSection.vue';
import ServicesSection from '@/components/public/Home/ServicesSection.vue';
export default{
    components: { HeroSection ,ExperienceSection,OurBestAuctions,AddAuctionSection,ServicesSection },
}
</script>