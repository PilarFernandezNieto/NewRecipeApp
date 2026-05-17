<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppLogo from '@/components/AppLogo.vue'

const auth = useAuthStore()
const router = useRouter()
const search = ref('')

function handleSearch() {
  if (search.value.trim()) {
    router.push({ name: 'recipes', query: { search: search.value.trim() } })
    search.value = ''
  }
}

async function logout() {
  await auth.logout()
  router.push({ name: 'home' })
}
</script>

<template>
  <header class="w-full bg-surface border-b border-primary/10 sticky top-0 z-50">
    <nav class="flex justify-between items-center w-full px-5 md:px-16 py-4 max-w-300 mx-auto">
      <!-- Logo -->
      <RouterLink :to="{ name: 'home' }">
        <AppLogo size="md" />
      </RouterLink>

      <!-- Links -->
      <div class="hidden md:flex items-center gap-8">
        <RouterLink
          :to="{ name: 'recipes' }"
          class="text-sm font-medium tracking-wide text-on-surface-variant hover:text-primary transition-colors"
          active-class="text-primary font-bold border-b-2 border-primary pb-1"
        >
          Explorar
        </RouterLink>
        <RouterLink
          v-if="auth.isAuthenticated"
          :to="{ name: 'dashboard-recipes' }"
          class="text-sm font-medium tracking-wide text-on-surface-variant hover:text-primary transition-colors"
          active-class="text-primary font-bold border-b-2 border-primary pb-1"
        >
          Mis Recetas
        </RouterLink>
      </div>

      <!-- Search + User -->
      <div class="flex items-center gap-4">
        <div class="hidden md:flex items-center border-b border-outline/30 px-2 py-1 gap-2">
          <span class="material-symbols-outlined text-outline text-xl">search</span>
          <input
            v-model="search"
            @keyup.enter="handleSearch"
            type="text"
            placeholder="Buscar..."
            class="bg-transparent border-none outline-none text-sm w-32 text-primary placeholder:text-outline-variant"
          />
        </div>

        <template v-if="auth.isAuthenticated">
          <button
            @click="logout"
            class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors"
          >
            Salir
          </button>
        </template>
        <template v-else>
          <RouterLink
            to="/auth/login"
            class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors"
          >
            Entrar
          </RouterLink>
        </template>

        <RouterLink v-if="auth.isAuthenticated" :to="{ name: 'dashboard-recipes' }">
          <span class="material-symbols-outlined text-primary">account_circle</span>
        </RouterLink>
      </div>
    </nav>
  </header>
</template>
