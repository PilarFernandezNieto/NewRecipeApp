<script setup>
import { ref, computed } from 'vue'
import { useRecipes, useDeleteRecipe } from '@/composables/useRecipes'
import PaginationBar from '@/components/PaginationBar.vue'
import RecipeAdminCard from '@/components/RecipeAdminCard.vue'

const page = ref(1)
const params = computed(() => ({ per_page: 9, page: page.value }))
const { data, isLoading, isFetching } = useRecipes(params)
const recipes = computed(() => data.value?.data ?? [])
const meta = computed(() => data.value?.meta ?? null)

const { mutate: deleteRecipe, isPending: deleting } = useDeleteRecipe()
const confirmDeleteId = ref(null)

function confirmDelete() {
  deleteRecipe(confirmDeleteId.value, {
    onSuccess: () => {
      confirmDeleteId.value = null
    },
  })
}
</script>

<template>
  <div>
    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="i in 6" :key="i" class="animate-pulse">
        <div class="aspect-4/3 bg-surface-container-high mb-4"></div>
        <div class="h-4 bg-surface-container-high w-3/4 mb-2"></div>
        <div class="h-3 bg-surface-container-high w-1/2"></div>
      </div>
    </div>

    <div v-else>
      <div
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 transition-opacity duration-200"
        :class="{ 'opacity-60': isFetching }"
      >
        <RecipeAdminCard
          v-for="recipe in recipes"
          :key="recipe.id"
          :recipe="recipe"
          @delete="confirmDeleteId = $event"
        />

        <RouterLink
          :to="{ name: 'recipe-create' }"
          class="border-2 border-dashed border-primary/15 flex flex-col items-center justify-center p-8 text-center group hover:border-secondary transition-colors min-h-64"
        >
          <div
            class="w-14 h-14 rounded-full bg-secondary/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform"
          >
            <span class="material-symbols-outlined text-secondary text-2xl">add</span>
          </div>
          <h3 class="mb-1">Añadir receta</h3>
          <p class="text-sm text-on-surface-variant">Inmortaliza tu creación</p>
        </RouterLink>
      </div>

      <PaginationBar
        v-if="meta"
        :current-page="meta.current_page"
        :last-page="meta.last_page"
        :loading="isFetching"
        @update:current-page="page = $event"
      />
    </div>

    <!-- Modal confirmar borrar -->
    <div
      v-if="confirmDeleteId"
      class="fixed inset-0 bg-primary/40 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click.self="confirmDeleteId = null"
    >
      <div class="bg-surface max-w-sm w-full p-8 border border-primary/10">
        <h3 class="mb-2">¿Eliminar receta?</h3>
        <p class="text-sm text-on-surface-variant mb-6">Esta acción no se puede deshacer.</p>
        <div class="flex gap-3">
          <button
            @click="confirmDelete"
            :disabled="deleting"
            class="flex-1 py-2 bg-error text-on-error text-sm font-semibold tracking-wide uppercase hover:opacity-80 disabled:opacity-50 transition-opacity"
          >
            {{ deleting ? 'Eliminando...' : 'Eliminar' }}
          </button>
          <button
            @click="confirmDeleteId = null"
            class="flex-1 py-2 border border-primary/20 text-sm font-semibold tracking-wide uppercase hover:bg-surface-container-high transition-colors"
          >
            Cancelar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
