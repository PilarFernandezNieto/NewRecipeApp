<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentPage: { type: Number, required: true },
  lastPage: { type: Number, required: true },
  loading: { type: Boolean, default: false },
})

const emit = defineEmits(['update:currentPage'])

function go(page) {
  if (page < 1 || page > props.lastPage || page === props.currentPage) return
  emit('update:currentPage', page)
}

// Genera el rango de páginas con puntos suspensivos
const pages = computed(() => {
  const { currentPage: c, lastPage: l } = props
  const delta = 2
  const range = []
  let prev

  for (let i = 1; i <= l; i++) {
    if (i === 1 || i === l || (i >= c - delta && i <= c + delta)) {
      if (prev !== undefined) {
        if (i - prev === 2) range.push(prev + 1)
        else if (i - prev > 2) range.push('…')
      }
      range.push(i)
      prev = i
    }
  }

  return range
})
</script>

<template>
  <nav
    v-if="lastPage > 1"
    class="flex items-center justify-center gap-1 mt-16"
    aria-label="Paginación"
  >
    <!-- Anterior -->
    <button
      @click="go(currentPage - 1)"
      :disabled="currentPage === 1 || loading"
      class="flex items-center gap-1 px-3 py-2 text-sm font-medium text-on-surface-variant hover:text-primary disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
      aria-label="Página anterior"
    >
      <span class="material-symbols-outlined text-base">arrow_back</span>
    </button>

    <!-- Números -->
    <template v-for="page in pages" :key="page">
      <span v-if="page === '…'" class="px-2 py-2 text-sm text-outline select-none">…</span>
      <button
        v-else
        @click="go(page)"
        :disabled="loading"
        :aria-current="page === currentPage ? 'page' : undefined"
        class="w-9 h-9 text-sm font-medium transition-colors disabled:cursor-not-allowed"
        :class="
          page === currentPage
            ? 'bg-primary text-on-primary'
            : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-high'
        "
      >
        {{ page }}
      </button>
    </template>

    <!-- Siguiente -->
    <button
      @click="go(currentPage + 1)"
      :disabled="currentPage === lastPage || loading"
      class="flex items-center gap-1 px-3 py-2 text-sm font-medium text-on-surface-variant hover:text-primary disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
      aria-label="Página siguiente"
    >
      <span class="material-symbols-outlined text-base">arrow_forward</span>
    </button>
  </nav>
</template>
