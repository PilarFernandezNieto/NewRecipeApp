<script setup>
import { ref, computed } from 'vue'
import { useIngredients } from '@/composables/useIngredients'

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
})
const emit = defineEmits(['update:modelValue'])

const search = ref('')
const showDropdown = ref(false)

const searchParams = computed(() => ({
  search: search.value || undefined,
  per_page: 20,
}))

const { data } = useIngredients(searchParams)
const allIngredients = computed(() => data.value?.data ?? data.value ?? [])

// Ingredientes que aún no están añadidos
const suggestions = computed(() =>
  allIngredients.value.filter(
    (ing) => !props.modelValue.some((added) => added.id === ing.id)
  )
)

function add(ingredient) {
  emit('update:modelValue', [
    ...props.modelValue,
    { id: ingredient.id, name: ingredient.name, quantity: '', unit: '' },
  ])
  search.value = ''
  showDropdown.value = false
}

function remove(id) {
  emit('update:modelValue', props.modelValue.filter((ing) => ing.id !== id))
}

function update(id, field, value) {
  emit(
    'update:modelValue',
    props.modelValue.map((ing) => (ing.id === id ? { ...ing, [field]: value } : ing))
  )
}

function hideDropdown() {
  setTimeout(() => { showDropdown.value = false }, 150)
}
</script>

<template>
  <div class="flex flex-col gap-4">
    <!-- Ingredientes añadidos -->
    <div v-if="modelValue.length" class="flex flex-col gap-3">
      <div
        v-for="ing in modelValue"
        :key="ing.id"
        class="flex items-center gap-3 p-3 bg-surface-container-low border border-outline/20"
      >
        <span class="text-sm font-medium text-primary flex-1">{{ ing.name }}</span>
        <input
          :value="ing.quantity"
          @input="update(ing.id, 'quantity', $event.target.value)"
          type="text"
          placeholder="Cantidad"
          class="w-24 bg-transparent border-b border-outline/40 text-sm text-primary placeholder:text-outline-variant outline-none pb-1"
        />
        <input
          :value="ing.unit"
          @input="update(ing.id, 'unit', $event.target.value)"
          type="text"
          placeholder="Unidad"
          class="w-20 bg-transparent border-b border-outline/40 text-sm text-primary placeholder:text-outline-variant outline-none pb-1"
        />
        <button type="button" @click="remove(ing.id)" class="text-outline hover:text-error transition-colors">
          <span class="material-symbols-outlined text-base">close</span>
        </button>
      </div>
    </div>

    <!-- Buscador -->
    <div class="relative">
      <div class="flex items-center gap-3 border-b border-outline/40 pb-2 focus-within:border-primary transition-colors">
        <span class="material-symbols-outlined text-outline text-xl">search</span>
        <input
          v-model="search"
          @focus="showDropdown = true"
          @blur="hideDropdown"
          type="text"
          placeholder="Buscar y añadir ingrediente..."
          class="w-full bg-transparent border-none outline-none text-sm text-primary placeholder:text-outline-variant"
        />
      </div>

      <!-- Dropdown sugerencias -->
      <ul
        v-if="showDropdown && suggestions.length"
        class="absolute z-10 w-full bg-surface border border-outline/20 mt-1 max-h-48 overflow-y-auto shadow-lg"
      >
        <li
          v-for="ing in suggestions"
          :key="ing.id"
          @mousedown="add(ing)"
          class="px-4 py-2 text-sm text-on-surface hover:bg-surface-container-high cursor-pointer transition-colors"
        >
          {{ ing.name }}
        </li>
      </ul>
    </div>
  </div>
</template>
