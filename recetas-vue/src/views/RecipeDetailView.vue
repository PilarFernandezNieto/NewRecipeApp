<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useRecipe } from '@/composables/useRecipes'

const FALLBACK_IMAGE = 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&q=80'

const route = useRoute()
const slug = computed(() => route.params.slug)
const { data: recipe, isLoading, isError } = useRecipe(slug)

// --- Ingredientes tachables ---
const checked = ref(new Set())

function toggle(ingredientId) {
  const next = new Set(checked.value)
  if (next.has(ingredientId)) {
    next.delete(ingredientId)
  } else {
    next.add(ingredientId)
  }
  checked.value = next
}

// --- Tiempo ---
function formatTime(minutes) {
  if (!minutes) return '—'
  if (minutes < 60) return `${minutes} min`

  const hours = Math.floor(minutes / 60)
  const remaining = minutes % 60
  return remaining ? `${hours} h ${remaining} min` : `${hours} h`
}

const totalTime = computed(() => {
  if (!recipe.value) return '—'
  const prep = recipe.value.prep_time ?? 0
  const cook = recipe.value.cook_time ?? 0
  return formatTime(prep + cook)
})

// --- Imagen ---
function getImage(r) {
  return r.image ?? FALLBACK_IMAGE
}

// --- Compartir ---
// function share() {
//   if (navigator.share) {
//     navigator.share({ title: recipe.value?.title, url: window.location.href })
//   } else {
//     navigator.clipboard.writeText(window.location.href)
//   }
// }
</script>

<template>
  <div>
    <!-- Loading skeleton -->
    <div v-if="isLoading" class="max-w-300 mx-auto px-5 md:px-16 pt-12 animate-pulse">
      <div class="h-4 bg-surface-container-high w-32 mb-4 rounded"></div>
      <div class="h-12 bg-surface-container-high w-2/3 mb-3 rounded"></div>
      <div class="h-6 bg-surface-container-high w-full mb-8 rounded"></div>
      <div class="aspect-21/9 bg-surface-container-high"></div>
    </div>

    <!-- Error -->
    <div v-else-if="isError" class="max-w-300 mx-auto px-5 md:px-16 py-24 text-center">
      <span class="material-symbols-outlined text-5xl text-outline-variant mb-4 block">error</span>
      <p class="text-on-surface-variant">No se encontró la receta.</p>
    </div>

    <template v-else-if="recipe">
      <!-- Hero -->
      <header class="max-w-300 mx-auto px-5 md:px-16 pt-12">
        <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-8">
          <div class="max-w-2xl">
            <!-- Category + difficulty tags -->
            <div class="flex gap-2 mb-4 flex-wrap">
              <span
                v-if="recipe.category"
                class="bg-secondary/10 text-secondary px-3 py-1 text-xs font-semibold tracking-widest uppercase"
              >
                {{ recipe.category.name }}
              </span>
              <span
                v-if="recipe.difficulty"
                class="bg-surface-container-high text-on-surface-variant px-3 py-1 text-xs font-semibold tracking-widest uppercase"
              >
                {{ recipe.difficulty.name }}
              </span>
            </div>

            <h1 class="mb-3 leading-tight">
              {{ recipe.title }}
            </h1>
            <p class="text-lg text-on-surface-variant italic leading-relaxed">
              {{ recipe.description }}
            </p>
          </div>
        </div>

        <!-- Hero image -->
        <div class="relative w-full aspect-21/9 overflow-hidden mb-12 border border-primary/5">
          <img :src="getImage(recipe)" :alt="recipe.title" class="w-full h-full object-cover" />
        </div>

        <!-- Stats bento -->
        <div class="grid grid-cols-2 md:grid-cols-3 border-y border-primary/10 py-8 mb-16">
          <div class="text-center py-2 md:border-r border-primary/10">
            <span class="block text-xs font-semibold tracking-widest uppercase text-outline mb-1"
              >Tiempo total</span
            >
            <span class="block text-2xl font-bold text-primary">{{ totalTime }}</span>
          </div>
          <div class="text-center py-2 md:border-r border-primary/10">
            <span class="block text-xs font-semibold tracking-widest uppercase text-outline mb-1"
              >Porciones</span
            >
            <span class="block text-2xl font-bold text-primary"
              >{{ recipe.servings }} personas</span
            >
          </div>
          <div
            class="text-center py-2 col-span-2 md:col-span-1 border-t md:border-t-0 border-primary/10 mt-4 md:mt-0 pt-6 md:pt-2"
          >
            <span class="block text-xs font-semibold tracking-widest uppercase text-outline mb-1"
              >Dificultad</span
            >
            <span class="block text-2xl font-bold text-primary">{{
              recipe.difficulty?.name ?? '—'
            }}</span>
          </div>
        </div>
      </header>

      <!-- Content grid -->
      <section
        class="max-w-300 mx-auto px-5 md:px-16 grid grid-cols-1 lg:grid-cols-12 gap-16 pb-24"
      >
        <!-- Ingredients sidebar -->
        <aside class="lg:col-span-4">
          <div class="lg:sticky lg:top-24">
            <h2 class="mb-8 border-b border-primary pb-4">Ingredientes</h2>

            <ul class="space-y-4">
              <li
                v-for="ingredient in recipe.ingredients"
                :key="ingredient.id"
                class="flex items-start gap-3 group cursor-pointer select-none"
                @click="toggle(ingredient.id)"
              >
                <!-- Checkbox circular -->
                <div
                  class="w-5 h-5 rounded-full border shrink-0 mt-0.5 flex items-center justify-center transition-colors"
                  :class="
                    checked.has(ingredient.id)
                      ? 'bg-secondary border-secondary'
                      : 'border-secondary group-hover:bg-secondary/10'
                  "
                >
                  <span
                    v-if="checked.has(ingredient.id)"
                    class="material-symbols-outlined text-white leading-none"
                    style="font-size: 12px; font-variation-settings: 'FILL' 1"
                    >check</span
                  >
                </div>

                <span
                  class="text-base leading-snug transition-colors"
                  :class="
                    checked.has(ingredient.id) ? 'text-outline line-through' : 'text-on-surface'
                  "
                >
                  <template v-if="ingredient.quantity || ingredient.unit">
                    {{ ingredient.quantity }}{{ ingredient.unit ? ' ' + ingredient.unit : '' }} de
                  </template>
                  {{ ingredient.name }}
                </span>
              </li>
            </ul>

            <!-- Source -->
            <div
              v-if="recipe.source"
              class="mt-12 p-6 bg-surface-container-low border border-primary/5"
            >
              <p class="text-xs font-semibold tracking-widest uppercase text-outline mb-1">
                Fuente
              </p>
              <p class="text-base font-medium text-primary wrap-break-word">{{ recipe.source }}</p>
            </div>
          </div>
        </aside>

        <!-- Steps -->
        <article class="lg:col-span-8">
          <h2 class="mb-8 border-b border-primary pb-4">Preparación</h2>

          <div class="instructions-content" v-html="recipe.instructions"></div>

          <!-- Actions -->
          <div class="mt-16 pt-8 border-t border-primary/10 flex flex-wrap gap-3">
            <!-- <button
              @click="share"
              class="flex items-center gap-2 border border-primary px-5 py-2 text-sm font-semibold tracking-widest uppercase hover:bg-primary hover:text-on-primary transition-all"
            >
              <span class="material-symbols-outlined text-xl">share</span>
              Compartir
            </button> -->
            <button
              @click="window.print()"
              class="flex items-center gap-2 border border-secondary px-5 py-2 text-sm font-semibold tracking-widest uppercase hover:bg-secondary hover:text-on-secondary transition-all"
            >
              <span class="material-symbols-outlined text-xl">print</span>
              Imprimir
            </button>
          </div>
        </article>
      </section>
    </template>
  </div>
</template>

<style scoped>
/* Estilos para el HTML generado por TipTap */
.instructions-content :deep(p) {
  font-size: 1.125rem;
  line-height: 1.75rem;
  color: var(--color-on-surface);
  margin-bottom: 1rem;
}

.instructions-content :deep(h2) {
  font-family: var(--font-display);
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--color-primary);
  margin-top: 2rem;
  margin-bottom: 1rem;
}

.instructions-content :deep(h3) {
  font-family: var(--font-display);
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--color-primary);
  margin-top: 1.5rem;
  margin-bottom: 0.75rem;
}

.instructions-content :deep(ul) {
  list-style-type: disc;
  padding-left: 1.5rem;
  margin-bottom: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.instructions-content :deep(ol) {
  list-style-type: decimal;
  padding-left: 1.5rem;
  margin-bottom: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.instructions-content :deep(li) {
  font-size: 1.125rem;
  line-height: 1.75rem;
  color: var(--color-on-surface);
}

.instructions-content :deep(strong) {
  font-weight: 600;
  color: var(--color-primary);
}

.instructions-content :deep(em) {
  font-style: italic;
}
</style>
