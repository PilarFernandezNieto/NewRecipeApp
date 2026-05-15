<script setup>
defineProps({
  loading: {
    type: Boolean,
    default: false,
  },
  loadingText: {
    type: String,
    default: null,
  },
  type: {
    type: String,
    default: 'button',
  },
})
</script>

<template>
  <button
    :type="type"
    :disabled="loading"
    class="py-3 px-8 bg-secondary text-on-primary text-sm font-semibold tracking-widest uppercase hover:opacity-80 disabled:opacity-50 transition-opacity"
  >
    <span v-if="loading" class="material-symbols-outlined text-base animate-spin align-middle mr-1"
      >progress_activity</span
    >
    <template v-if="loading && loadingText">{{ loadingText }}</template>
    <slot v-else />
  </button>
</template>
