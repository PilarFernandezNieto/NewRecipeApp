<script setup>
defineProps({
  recipe: {
    type: Object,
    required: true,
  },
})

const FALLBACK_IMAGE = 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&q=80'

function recipeImage(recipe) {
  return recipe.image ?? FALLBACK_IMAGE
}
</script>

<template>
  <!-- RECIPE CARD-->
  <RouterLink
    :to="{ name: 'recipe-detail', params: { slug: recipe.slug } }"
    class="group cursor-pointer block shadow-md"
  >
    <div class="relative aspect-3/2 overflow-hidden border border-primary/10 mb-6">
      <img
        :src="recipeImage(recipe)"
        :alt="recipe.title"
        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
      />
      <div v-if="(recipe.avg_rating ?? 0) >= 4.5" class="absolute top-4 right-4">
        <span
          class="bg-surface/90 backdrop-blur px-3 py-1 text-xs font-semibold text-secondary border border-secondary/20 uppercase tracking-widest"
        >
          Destacado
        </span>
      </div>
    </div>

    <div class="p-2">
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
</template>
