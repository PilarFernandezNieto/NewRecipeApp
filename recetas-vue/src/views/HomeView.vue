<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useRecipes } from '@/composables/useRecipes'
import { useCategories } from '@/composables/useCategories'

const router = useRouter()
const search = ref('')

const { data: recipesData, isLoading } = useRecipes({ per_page: 3 })
const { data: categories } = useCategories()

const recipes = computed(() => recipesData.value?.data ?? [])

function handleSearch() {
  const q = search.value.trim()
  if (q) router.push({ name: 'recipes', query: { search: q } })
}

function filterByCategory(slug) {
  router.push({ name: 'recipes', query: { category: slug } })
}

function recipeImage(recipe) {
  return recipe.image ?? 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&q=80'
}
</script>

<template>
  <div>
    <!-- Hero -->
    <section
      class="max-w-300 mx-auto px-5 md:px-16 pt-12 pb-24 flex flex-col items-center text-center"
    >
      <!-- Editorial hero image -->
      <div class="w-full mb-16 overflow-hidden border border-primary/5">
        <img
          src="https://images.unsplash.com/photo-1466637574441-749b8f19452f?w=1400&q=80"
          alt="Recetario — Cocina con sentido"
          class="w-full h-72 md:h-96 object-cover"
        />
      </div>

      <!-- Search bar -->
      <div class="w-full max-w-2xl">
        <div class="flex items-center gap-4 border-b border-primary pb-4">
          <span class="material-symbols-outlined text-outline text-2xl">search</span>
          <input
            v-model="search"
            @keyup.enter="handleSearch"
            type="text"
            placeholder="Busca una receta, ingrediente o categoría..."
            class="w-full bg-transparent border-none outline-none text-lg text-primary placeholder:text-outline-variant"
          />
        </div>

        <!-- Category tags -->
        <div class="flex gap-2 mt-6 overflow-x-auto pb-2 no-scrollbar">
          <button
            v-for="cat in categories"
            :key="cat.id"
            @click="filterByCategory(cat.slug)"
            class="px-3 py-1 bg-surface-container-high text-on-surface-variant text-xs font-semibold tracking-wide rounded-lg whitespace-nowrap hover:bg-secondary-container transition-colors"
          >
            {{ cat.name }}
          </button>
        </div>
      </div>
    </section>

    <!-- Recetas de Portada -->
    <section class="max-w-300 mx-auto px-5 md:px-16 pb-24">
      <div class="flex justify-between items-end mb-12">
        <h2 class="font-display text-3xl md:text-4xl font-bold text-primary leading-tight">
          Recetas de Portada
        </h2>
        <RouterLink
          :to="{ name: 'recipes' }"
          class="text-sm font-medium text-secondary border-b border-secondary hover:opacity-70 transition-all"
        >
          Ver todas
        </RouterLink>
      </div>

      <!-- Loading skeleton -->
      <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-16">
        <div v-for="i in 3" :key="i" class="animate-pulse">
          <div class="aspect-3/2 bg-surface-container-high mb-6"></div>
          <div class="h-3 bg-surface-container-high w-24 mb-3"></div>
          <div class="h-6 bg-surface-container-high w-3/4 mb-3"></div>
          <div class="h-4 bg-surface-container-high w-full mb-2"></div>
          <div class="h-4 bg-surface-container-high w-2/3"></div>
        </div>
      </div>

      <!-- Recipe grid -->
      <div v-else-if="recipes.length" class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-16">
        <RouterLink
          v-for="recipe in recipes"
          :key="recipe.id"
          :to="{ name: 'recipe-detail', params: { slug: recipe.slug } }"
          class="group cursor-pointer block"
        >
          <div
            class="relative aspect-3/2 overflow-hidden border border-primary/10 mb-6"
            style="box-shadow: 0 20px 40px -20px rgba(0, 0, 0, 0.04)"
          >
            <img
              :src="recipeImage(recipe)"
              :alt="recipe.title"
              class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
            />
            <div v-if="recipe.avg_rating >= 4.5" class="absolute top-4 right-4">
              <span
                class="bg-surface/90 backdrop-blur px-3 py-1 text-xs font-semibold text-secondary border border-secondary/20 uppercase tracking-widest"
              >
                Destacado
              </span>
            </div>
          </div>

          <div>
            <span class="text-xs font-semibold text-secondary mb-2 block tracking-widest uppercase">
              {{ recipe.category?.name ?? 'Sin categoría' }}
            </span>
            <h3
              class="font-display text-xl md:text-2xl font-bold text-primary mb-3 leading-snug group-hover:opacity-75 transition-opacity"
            >
              {{ recipe.title }}
            </h3>
            <p class="text-base text-on-surface-variant line-clamp-2">
              {{ recipe.description }}
            </p>
          </div>
        </RouterLink>
      </div>

      <!-- Empty state -->
      <div v-else class="text-center py-24">
        <span class="material-symbols-outlined text-5xl text-outline-variant mb-4 block">menu_book</span>
        <p class="text-on-surface-variant">Todavía no hay recetas publicadas.</p>
        <RouterLink
          to="/dashboard"
          class="inline-block mt-4 text-sm font-medium text-secondary border-b border-secondary"
        >
          Crea la primera
        </RouterLink>
      </div>
    </section>
  </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
