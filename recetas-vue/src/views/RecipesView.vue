<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useRecipes } from '@/composables/useRecipes'
import { useCategories } from '@/composables/useCategories'
import PaginationBar from '@/components/PaginationBar.vue'

const route = useRoute()
const router = useRouter()

const page = ref(Number(route.query.page) || 1)
const search = ref(route.query.search ?? '')
const selectedCategory = ref(route.query.category ?? '')

// Sincroniza los filtros con la URL
watch([page, search, selectedCategory], () => {
  router.replace({
    query: {
      ...(page.value > 1 && { page: page.value }),
      ...(search.value && { search: search.value }),
      ...(selectedCategory.value && { category: selectedCategory.value }),
    },
  })
})

// Cuando cambian filtros, volver a la página 1
watch([search, selectedCategory], () => {
  page.value = 1
})

const params = computed(() => ({
  page: page.value,
  per_page: 12,
  ...(search.value && { search: search.value }),
  ...(selectedCategory.value && { category: selectedCategory.value }),
}))

const { data, isLoading, isFetching } = useRecipes(params)
const { data: categories } = useCategories()

const recipes = computed(() => data.value?.data ?? [])
const meta = computed(() => data.value?.meta ?? null)

function recipeImage(recipe) {
  return recipe.image ?? 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&q=80'
}

function clearFilters() {
  search.value = ''
  selectedCategory.value = ''
  page.value = 1
}

const hasActiveFilters = computed(() => search.value || selectedCategory.value)
</script>

<template>
  <div class="max-w-300 mx-auto px-5 md:px-16 py-12">
    <!-- Header -->
    <div class="mb-10">
      <h1 class="mb-2">Todas las recetas</h1>
      <p v-if="meta" class="text-sm text-on-surface-variant">
        {{ meta.total }} recetas encontradas
      </p>
    </div>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4 mb-10">
      <!-- Search -->
      <div
        class="flex items-center gap-3 border-b border-outline/40 pb-3 focus-within:border-primary transition-colors flex-1"
      >
        <span class="material-symbols-outlined text-outline text-xl">search</span>
        <input
          v-model="search"
          type="text"
          placeholder="Buscar recetas..."
          class="w-full bg-transparent border-none outline-none text-sm text-primary placeholder:text-outline-variant"
        />
        <button
          v-if="search"
          @click="search = ''"
          class="text-outline hover:text-primary transition-colors"
        >
          <span class="material-symbols-outlined text-base">close</span>
        </button>
      </div>

      <!-- Category select -->
      <div
        class="flex items-center gap-3 border-b border-outline/40 pb-3 focus-within:border-primary transition-colors min-w-48"
      >
        <span class="material-symbols-outlined text-outline text-xl">category</span>
        <select
          v-model="selectedCategory"
          class="w-full bg-transparent border-none outline-none text-sm text-primary appearance-none cursor-pointer"
        >
          <option value="">Todas las categorías</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
            {{ cat.name }}
          </option>
        </select>
      </div>

      <!-- Clear filters -->
      <button
        v-if="hasActiveFilters"
        @click="clearFilters"
        class="text-sm font-medium text-secondary border-b border-secondary hover:opacity-70 transition-opacity whitespace-nowrap self-end pb-3"
      >
        Limpiar filtros
      </button>
    </div>

    <!-- Loading skeleton -->
    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-16">
      <div v-for="i in 12" :key="i" class="animate-pulse">
        <div class="aspect-3/2 bg-surface-container-high mb-6"></div>
        <div class="h-3 bg-surface-container-high w-24 mb-3"></div>
        <div class="h-6 bg-surface-container-high w-3/4 mb-3"></div>
        <div class="h-4 bg-surface-container-high w-full mb-2"></div>
        <div class="h-4 bg-surface-container-high w-2/3"></div>
      </div>
    </div>

    <!-- Recipe grid — isFetching añade opacidad mientras carga la siguiente página -->
    <div
      v-else-if="recipes.length"
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-16 transition-opacity duration-200"
      :class="{ 'opacity-60': isFetching }"
    >
      <RouterLink
        v-for="recipe in recipes"
        :key="recipe.id"
        :to="{ name: 'recipe-detail', params: { slug: recipe.slug } }"
        class="group block"
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
        </div>

        <div>
          <span class="text-xs font-semibold text-secondary mb-2 block tracking-widest uppercase">
            {{ recipe.category?.name ?? 'Sin categoría' }}
          </span>
          <h3 class="mb-3 leading-snug group-hover:opacity-75 transition-opacity">
            {{ recipe.title }}
          </h3>
          <p class="text-sm text-on-surface-variant line-clamp-2">
            {{ recipe.description }}
          </p>
        </div>
      </RouterLink>
    </div>

    <!-- Empty state -->
    <div v-else class="text-center py-24">
      <span class="material-symbols-outlined text-5xl text-outline-variant mb-4 block"
        >search_off</span
      >
      <p class="text-on-surface-variant mb-2">No se encontraron recetas.</p>
      <button
        v-if="hasActiveFilters"
        @click="clearFilters"
        class="text-sm font-medium text-secondary border-b border-secondary"
      >
        Quitar filtros
      </button>
    </div>

    <!-- Pagination -->
    <PaginationBar
      v-if="meta"
      :current-page="meta.current_page"
      :last-page="meta.last_page"
      :loading="isFetching"
      @update:current-page="page = $event"
    />
  </div>
</template>
