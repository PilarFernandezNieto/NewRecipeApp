<script setup>
import { RouterLink } from 'vue-router'
</script>

<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-surface px-4">
    <RouterLink to="/" class="font-display text-2xl font-bold text-primary mb-8">
      Recetario
    </RouterLink>
    <div class="w-full max-w-md">
      <RouterView />
    </div>
  </div>
</template>
