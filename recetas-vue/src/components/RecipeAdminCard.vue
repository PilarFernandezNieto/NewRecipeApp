<script setup>
defineProps({
  recipe: {
    type: Object,
    required: true,
  },
})

defineEmits(['delete'])

const FALLBACK_IMAGE = 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&q=80'

function recipeImage(recipe) {
  return recipe.image ?? FALLBACK_IMAGE
}

function formatTime(minutes) {
  if (!minutes) return '—'
  if (minutes < 60) return `${minutes} min`
  const h = Math.floor(minutes / 60)
  const m = minutes % 60
  return m ? `${h} h ${m} min` : `${h} h`
}
</script>

<template>
  <!-- RECIPE ADMIN CARD -->
  <article
    class="border border-primary/10 bg-surface-container-lowest flex flex-col group shadow-md"
  >
    <RouterLink
      :to="{ name: 'recipe-detail', params: { slug: recipe.slug } }"
      class="aspect-3/2 overflow-hidden block"
    >
      <img
        :src="recipeImage(recipe)"
        :alt="recipe.title"
        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
      />
    </RouterLink>

    <div class="p-5 flex flex-col grow">
      <span class="text-xs font-semibold text-secondary tracking-widest uppercase mb-1">
        {{ recipe.category?.name ?? 'Sin categoría' }}
      </span>
      <h3 class="mb-2 leading-snug grow">{{ recipe.title }}</h3>

      <div class="flex items-center gap-4 text-on-surface-variant mb-4">
        <div class="flex items-center gap-1">
          <span class="material-symbols-outlined text-secondary text-base">schedule</span>
          <span class="text-xs font-semibold uppercase">
            {{ formatTime((recipe.prep_time ?? 0) + (recipe.cook_time ?? 0)) }}
          </span>
        </div>
        <div class="flex items-center gap-1">
          <span class="material-symbols-outlined text-secondary text-base">group</span>
          <span class="text-xs font-semibold uppercase">{{ recipe.servings }} pers.</span>
        </div>
      </div>

      <div class="flex gap-2 border-t border-primary/10 pt-4">
        <RouterLink
          :to="{ name: 'recipe-edit', params: { slug: recipe.slug } }"
          class="flex-1 flex items-center justify-center gap-1 py-2 text-xs font-semibold tracking-wide uppercase border border-secondary hover:bg-secondary hover:text-on-primary transition-all"
        >
          <span class="material-symbols-outlined text-sm">edit</span>
          Editar
        </RouterLink>
        <button
          @click="$emit('delete', recipe.id)"
          class="flex items-center justify-center px-4 py-2 border border-error/30 text-error hover:bg-error hover:text-on-error transition-all"
        >
          <span class="material-symbols-outlined text-sm">delete</span>
        </button>
      </div>
    </div>
  </article>
</template>
