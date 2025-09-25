<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
});
</script>

<template>
  <div class="flex items-center justify-between mb-3">
    <h1 class="text-2xl font-bold text-center mb-4 dark:text-slate-400" v-html="title">

    </h1>
    <slot />
  </div>
</template>
