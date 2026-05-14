<script setup>
import { RouterView } from 'vue-router'
import AppNav from '@/components/AppNav.vue'
</script>

<template>
  <div class="min-h-screen bg-surface text-on-surface">
    <AppNav />
    <main class="max-w-300 mx-auto px-5 md:px-16 py-12">
      <!-- Header -->
      <header class="mb-12 px-5 md:px-16">
        <h1 class="mb-3">Panel de Administración</h1>
      </header>
      <RouterView />
    </main>
  </div>
</template>
