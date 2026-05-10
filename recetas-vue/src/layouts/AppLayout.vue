<script setup>
import { RouterView } from 'vue-router'
import AppNav from '@/components/AppNav.vue'
import AppFooter from '@/components/AppFooter.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col bg-surface text-on-surface">
    <AppNav />
    <main class="grow">
      <RouterView />
    </main>
    <AppFooter />
  </div>
</template>
