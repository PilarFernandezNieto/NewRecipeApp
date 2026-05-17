<script setup>
import { RouterView } from 'vue-router'
import AppNav from '@/components/AppNav.vue'

const navLinks = [
  { name: 'dashboard-recipes', label: 'Recetas' },
  { name: 'dashboard-ingredients', label: 'Ingredientes' },
]
</script>

<template>
  <div class="min-h-screen bg-surface text-on-surface">
    <AppNav />
    <main class="max-w-300 mx-auto px-5 md:px-16 py-12">
      <header class="mb-8">
        <h1 class="mb-6">Panel de Administración</h1>
        <nav class="flex gap-8 border-b border-primary/10">
          <RouterLink
            v-for="link in navLinks"
            :key="link.name"
            :to="{ name: link.name }"
            class="pb-4 text-sm font-semibold tracking-wide transition-colors whitespace-nowrap"
            active-class="text-primary border-b-2 border-secondary -mb-px"
            inactive-class="text-on-surface-variant hover:text-primary"
          >
            {{ link.label }}
          </RouterLink>
        </nav>
      </header>
      <RouterView />
    </main>
  </div>
</template>
