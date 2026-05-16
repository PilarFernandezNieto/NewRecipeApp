<script setup>
import { ref, computed, watch } from 'vue'
import { useRecipes, useDeleteRecipe } from '@/composables/useRecipes'
import {
  useIngredients,
  useCreateIngredient,
  useUpdateIngredient,
  useDeleteIngredient,
} from '@/composables/useIngredients'
import PaginationBar from '@/components/PaginationBar.vue'
import RecipeAdminCard from '@/components/RecipeAdminCard.vue'
import AppButton from '@/components/AppButton.vue'

const activeTab = ref('recipes')

// --- Tab Mis Recetas ---
const recipePage = ref(1)
const recipeParams = computed(() => ({ per_page: 9, page: recipePage.value }))
const {
  data: recipesData,
  isLoading: loadingRecipes,
  isFetching: fetchingRecipes,
} = useRecipes(recipeParams)
const myRecipes = computed(() => recipesData.value?.data ?? [])
const recipesMeta = computed(() => recipesData.value?.meta ?? null)

const { mutate: deleteRecipe, isPending: deletingRecipe } = useDeleteRecipe()
const confirmDeleteRecipeId = ref(null)

function confirmDeleteRecipe() {
  deleteRecipe(confirmDeleteRecipeId.value, {
    onSuccess: () => {
      confirmDeleteRecipeId.value = null
    },
  })
}

// --- Tab Ingredientes ---
const ingredientSearch = ref('')
const ingredientPage = ref(1)
const ingredientParams = computed(() => ({
  search: ingredientSearch.value || undefined,
  per_page: 24,
  page: ingredientPage.value,
}))
const { data: ingredientsData, isLoading: loadingIngredients, isFetching: fetchingIngredients } = useIngredients(ingredientParams)
const ingredients = computed(() => ingredientsData.value?.data ?? [])
const ingredientsMeta = computed(() => ingredientsData.value?.meta ?? null)

watch(ingredientSearch, () => { ingredientPage.value = 1 })

// Modal crear / editar ingrediente
const ingredientModal = ref(false)
const editingIngredient = ref(null)
const ingredientName = ref('')
const ingredientDescription = ref('')
const ingredientError = ref('')

function openCreateIngredient() {
  editingIngredient.value = null
  ingredientName.value = ''
  ingredientDescription.value = ''
  ingredientError.value = ''
  ingredientModal.value = true
}

function openEditIngredient(ingredient) {
  editingIngredient.value = ingredient
  ingredientName.value = ingredient.name
  ingredientDescription.value = ingredient.description ?? ''
  ingredientError.value = ''
  ingredientModal.value = true
}

function closeIngredientModal() {
  ingredientModal.value = false
}

const { mutate: createIngredient, isPending: creating } = useCreateIngredient()
const { mutate: updateIngredient, isPending: updating } = useUpdateIngredient()
const { mutate: deleteIngredient, isPending: deletingIngredient } = useDeleteIngredient()
const savingIngredient = computed(() => creating.value || updating.value)

const confirmDeleteIngredientId = ref(null)

function saveIngredient() {
  ingredientError.value = ''
  const name = ingredientName.value.trim()
  if (!name) {
    ingredientError.value = 'El nombre es obligatorio.'
    return
  }

  const payload = { name, description: ingredientDescription.value.trim() || null }

  if (editingIngredient.value) {
    updateIngredient(
      { id: editingIngredient.value.id, data: payload },
      {
        onSuccess: closeIngredientModal,
        onError: (e) => {
          ingredientError.value = e.response?.data?.message ?? 'Error al guardar.'
        },
      },
    )
  } else {
    createIngredient(payload, {
      onSuccess: closeIngredientModal,
      onError: (e) => {
        ingredientError.value = e.response?.data?.message ?? 'Error al guardar.'
      },
    })
  }
}

function confirmDeleteIngredient() {
  deleteIngredient(confirmDeleteIngredientId.value, {
    onSuccess: () => {
      confirmDeleteIngredientId.value = null
    },
  })
}
</script>

<template>
  <div class="mx-auto px-5 md:px-16 py-12">
    <!-- Tabs -->
    <div class="flex gap-8 border-b border-primary/10 mb-12 overflow-x-auto">
      <button
        @click="activeTab = 'recipes'"
        class="pb-4 text-sm font-semibold tracking-wide transition-colors whitespace-nowrap"
        :class="
          activeTab === 'recipes'
            ? 'text-primary border-b-2 border-secondary -mb-px'
            : 'text-on-surface-variant hover:text-primary'
        "
      >
        Mis Recetas
        <span
          v-if="recipesMeta"
          class="ml-2 px-2 py-0.5 text-xs font-bold bg-secondary/10 text-secondary"
        >
          {{ recipesMeta.total }}
        </span>
      </button>
      <button
        @click="activeTab = 'ingredients'"
        class="pb-4 text-sm font-semibold tracking-wide transition-colors whitespace-nowrap"
        :class="
          activeTab === 'ingredients'
            ? 'text-primary border-b-2 border-secondary -mb-px'
            : 'text-on-surface-variant hover:text-primary'
        "
      >
        Ingredientes
      </button>
    </div>

    <!-- ======== TAB: MIS RECETAS ======== -->
    <section v-if="activeTab === 'recipes'">
      <div v-if="loadingRecipes" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="i in 6" :key="i" class="animate-pulse">
          <div class="aspect-4/3 bg-surface-container-high mb-4"></div>
          <div class="h-4 bg-surface-container-high w-3/4 mb-2"></div>
          <div class="h-3 bg-surface-container-high w-1/2"></div>
        </div>
      </div>

      <div v-else>
        <div
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 transition-opacity duration-200"
          :class="{ 'opacity-60': fetchingRecipes }"
        >
          <RecipeAdminCard
            v-for="recipe in myRecipes"
            :key="recipe.id"
            :recipe="recipe"
            @delete="confirmDeleteRecipeId = $event"
          />

          <!-- Añadir receta -->
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
          v-if="recipesMeta"
          :current-page="recipesMeta.current_page"
          :last-page="recipesMeta.last_page"
          :loading="fetchingRecipes"
          @update:current-page="recipePage = $event"
        />
      </div>
    </section>

    <!-- ======== TAB: INGREDIENTES ======== -->
    <section v-if="activeTab === 'ingredients'">
      <!-- Cabecera con buscador y botón añadir -->
      <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between mb-8">
        <div
          class="flex items-center gap-3 border-b border-outline/40 pb-3 focus-within:border-primary transition-colors w-full sm:max-w-sm"
        >
          <span class="material-symbols-outlined text-outline text-xl">search</span>
          <input
            v-model="ingredientSearch"
            type="text"
            placeholder="Buscar ingrediente..."
            class="w-full bg-transparent border-none outline-none text-sm text-primary placeholder:text-outline-variant"
          />
        </div>

        <AppButton
          type="submit"
          :loading="isPending"
          loading-text="Guardando..."
          @click="openCreateIngredient"
          class="flex items-center gap-2"
        >
          <span class="material-symbols-outlined text-base">add</span>
          Nuevo ingrendiente
        </AppButton>
      </div>

      <!-- Skeleton -->
      <div v-if="loadingIngredients" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div v-for="i in 8" :key="i" class="animate-pulse border border-primary/10 p-4">
          <div class="h-5 bg-surface-container-high w-3/4 mb-2"></div>
          <div class="h-3 bg-surface-container-high w-1/2"></div>
        </div>
      </div>

      <!-- Grid -->
      <div
        v-else
        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 transition-opacity duration-200"
        :class="{ 'opacity-60': fetchingIngredients }"
      >
        <div
          v-for="ingredient in ingredients"
          :key="ingredient.id"
          class="border border-primary/10 bg-surface p-4 flex flex-col gap-3"
        >
          <div class="flex items-start justify-between gap-2">
            <h3 class="leading-tight">
              {{ ingredient.name }}
            </h3>
          </div>
          <p
            v-if="ingredient.description"
            class="text-xs text-on-surface-variant leading-relaxed line-clamp-2"
          >
            {{ ingredient.description }}
          </p>
          <p v-if="ingredient.recipes_count !== undefined" class="text-xs text-on-surface-variant">
            {{ ingredient.recipes_count }}
            {{ ingredient.recipes_count === 1 ? 'receta' : 'recetas' }}
          </p>
          <div class="flex gap-2 pt-2 border-t border-primary/10 mt-auto">
            <button
              @click="openEditIngredient(ingredient)"
              class="flex-1 flex items-center justify-center gap-1 py-1.5 text-xs font-semibold tracking-wide uppercase border border-secondary hover:bg-secondary hover:text-on-primary transition-all"
            >
              <span class="material-symbols-outlined text-sm">edit</span>
              Editar
            </button>
            <button
              @click="confirmDeleteIngredientId = ingredient.id"
              class="flex items-center justify-center px-3 py-1.5 border border-error/30 text-error hover:bg-error hover:text-on-error transition-all"
            >
              <span class="material-symbols-outlined text-sm">delete</span>
            </button>
          </div>
        </div>
      </div>

      <PaginationBar
        v-if="ingredientsMeta"
        :current-page="ingredientsMeta.current_page"
        :last-page="ingredientsMeta.last_page"
        :loading="fetchingIngredients"
        @update:current-page="ingredientPage = $event"
      />
    </section>

    <!-- ======== MODALES ======== -->

    <!-- Modal crear/editar ingrediente -->
    <div
      v-if="ingredientModal"
      class="fixed inset-0 bg-primary/40 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click.self="closeIngredientModal"
    >
      <div class="bg-surface max-w-sm w-full p-8 border border-primary/10">
        <h3 class="mb-6">
          {{ editingIngredient ? 'Editar ingrediente' : 'Nuevo ingrediente' }}
        </h3>
        <div class="flex flex-col gap-6 mb-6">
          <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant"
              >Nombre</label
            >
            <input
              v-model="ingredientName"
              type="text"
              placeholder="Ej: Harina de fuerza"
              class="bg-transparent border-b pb-2 outline-none text-base text-primary placeholder:text-outline-variant transition-colors"
              :class="ingredientError ? 'border-error' : 'border-outline/40 focus:border-primary'"
              autofocus
            />
            <p v-if="ingredientError" class="text-xs text-error mt-1">{{ ingredientError }}</p>
          </div>
          <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant"
              >Descripción</label
            >
            <textarea
              v-model="ingredientDescription"
              rows="3"
              placeholder="Breve descripción del ingrediente..."
              class="bg-transparent border-b border-outline/40 pb-2 outline-none text-sm text-primary placeholder:text-outline-variant resize-none focus:border-primary transition-colors"
            ></textarea>
          </div>
        </div>
        <div class="flex gap-3">
          <button
            @click="saveIngredient"
            :disabled="savingIngredient"
            class="flex-1 py-2 bg-secondary text-on-secondary text-sm font-semibold tracking-wide uppercase hover:opacity-80 disabled:opacity-50 transition-opacity"
          >
            {{ savingIngredient ? 'Guardando...' : 'Guardar' }}
          </button>
          <button
            @click="closeIngredientModal"
            class="flex-1 py-2 border border-primary/20 text-sm font-semibold tracking-wide uppercase hover:bg-surface-container-high transition-colors"
          >
            Cancelar
          </button>
        </div>
      </div>
    </div>

    <!-- Modal confirmar borrar receta -->
    <div
      v-if="confirmDeleteRecipeId"
      class="fixed inset-0 bg-primary/40 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click.self="confirmDeleteRecipeId = null"
    >
      <div class="bg-surface max-w-sm w-full p-8 border border-primary/10">
        <h3 class="mb-2">¿Eliminar receta?</h3>
        <p class="text-sm text-on-surface-variant mb-6">Esta acción no se puede deshacer.</p>
        <div class="flex gap-3">
          <button
            @click="confirmDeleteRecipe"
            :disabled="deletingRecipe"
            class="flex-1 py-2 bg-error text-on-error text-sm font-semibold tracking-wide uppercase hover:opacity-80 disabled:opacity-50 transition-opacity"
          >
            {{ deletingRecipe ? 'Eliminando...' : 'Eliminar' }}
          </button>
          <button
            @click="confirmDeleteRecipeId = null"
            class="flex-1 py-2 border border-primary/20 text-sm font-semibold tracking-wide uppercase hover:bg-surface-container-high transition-colors"
          >
            Cancelar
          </button>
        </div>
      </div>
    </div>

    <!-- Modal confirmar borrar ingrediente -->
    <div
      v-if="confirmDeleteIngredientId"
      class="fixed inset-0 bg-primary/40 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click.self="confirmDeleteIngredientId = null"
    >
      <div class="bg-surface max-w-sm w-full p-8 border border-primary/10">
        <h3 class="mb-2">¿Eliminar ingrediente?</h3>
        <p class="text-sm text-on-surface-variant mb-6">Esta acción no se puede deshacer.</p>
        <div class="flex gap-3">
          <button
            @click="confirmDeleteIngredient"
            :disabled="deletingIngredient"
            class="flex-1 py-2 bg-error text-on-error text-sm font-semibold tracking-wide uppercase hover:opacity-80 disabled:opacity-50 transition-opacity"
          >
            {{ deletingIngredient ? 'Eliminando...' : 'Eliminar' }}
          </button>
          <button
            @click="confirmDeleteIngredientId = null"
            class="flex-1 py-2 border border-primary/20 text-sm font-semibold tracking-wide uppercase hover:bg-surface-container-high transition-colors"
          >
            Cancelar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
