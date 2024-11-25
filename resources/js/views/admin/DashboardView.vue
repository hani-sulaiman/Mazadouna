<template>
    <Statistics />
    <Details />
</template>
<script>
import TopBar from '../../components/admin/dashboard/TopBar.vue';
import Statistics from '../../components/admin/dashboard/Statistics.vue';
import Details from '../../components/admin/dashboard/Details.vue';
export default {
    components: { TopBar, Statistics, Details },
}
</script>