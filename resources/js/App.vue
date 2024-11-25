<template>
    <router-view />
</template>

<script>
import { watch } from 'vue';
import { useRoute } from 'vue-router';

export default {
  setup() {
    const route = useRoute();

    // وظيفة لتفعيل أو تعطيل ملفات CSS بناءً على نوع layout
    const updateCSS = (layout) => {
      // تعطيل جميع ملفات CSS المرفقة
      document.getElementById('admin-css').disabled = true;
      document.getElementById('user-created-auction-css').disabled = true;
      document.getElementById('user-favorite-auction-css').disabled = true;
      document.getElementById('user-manage-css').disabled = true;
      document.getElementById('user-style-css').disabled = true;
      document.getElementById('public-style-css').disabled = true;
      document.getElementById('public-responsive-css').disabled = true;

      // تفعيل الملفات المناسبة بناءً على layout
      if (layout === 'admin') {
        document.getElementById('admin-css').disabled = false;
      } else if (layout === 'user') {
        document.getElementById('user-created-auction-css').disabled = false;
        document.getElementById('user-favorite-auction-css').disabled = false;
        document.getElementById('user-manage-css').disabled = false;
        document.getElementById('user-style-css').disabled = false;
      } else {
        document.getElementById('public-style-css').disabled = false;
        document.getElementById('public-responsive-css').disabled = false;
      }
    };

    // مراقبة تغيير المسار وتحديث ملفات CSS
    watch(
      () => route.meta.layout,
      (newLayout) => {
        updateCSS(newLayout);
      },
      { immediate: true } // تفعيل الشروط عند تحميل الصفحة
    );
  },
};
</script>