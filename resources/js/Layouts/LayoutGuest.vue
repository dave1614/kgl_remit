<script setup>
import Toast from '@/Components/Toast.vue'
</script>
<template>

  <div class="bg-gray-50 dark:bg-slate-800 dark:text-slate-100">
    <slot />
  </div>
  <Toast />
</template>
