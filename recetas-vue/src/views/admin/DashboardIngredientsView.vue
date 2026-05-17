<script setup>
import { ref, computed, watch } from 'vue'
import {
  useIngredients,
  useCreateIngredient,
  useUpdateIngredient,
  useDeleteIngredient,
} from '@/composables/useIngredients'
import PaginationBar from '@/components/PaginationBar.vue'
import AppButton from '@/components/AppButton.vue'

const search = ref('')
const page = ref(1)
const params = computed(() => ({
  search: search.value || undefined,
  per_page: 12,
  page: page.value,
}))
const { data, isLoading, isFetching } = useIngredients(params)
const ingredients = computed(() => data.value?.data ?? [])
const meta = computed(() => data.value?.meta ?? null)

watch(search, () => {
  page.value = 1
})

// Modal crear / editar
const modal = ref(false)
const editing = ref(null)
const name = ref('')
const description = ref('')
const error = ref('')

function openCreate() {
  editing.value = null
  name.value = ''
  description.value = ''
  error.value = ''
  modal.value = true
}

function openEdit(ingredient) {
  editing.value = ingredient
  name.value = ingredient.name
  description.value = ingredient.description ?? ''
  error.value = ''
  modal.value = true
}

function closeModal() {
  modal.value = false
}

const { mutate: createIngredient, isPending: creating } = useCreateIngredient()
const { mutate: updateIngredient, isPending: updating } = useUpdateIngredient()
const { mutate: deleteIngredient, isPending: deleting } = useDeleteIngredient()
const saving = computed(() => creating.value || updating.value)

const confirmDeleteId = ref(null)

function save() {
  error.value = ''
  const trimmed = name.value.trim()
  if (!trimmed) {
    error.value = 'El nombre es obligatorio.'
    return
  }

  const payload = { name: trimmed, description: description.value.trim() || null }

  if (editing.value) {
    updateIngredient(
      { id: editing.value.id, data: payload },
      {
        onSuccess: closeModal,
        onError: (e) => {
          error.value = e.response?.data?.message ?? 'Error al guardar.'
        },
      },
    )
  } else {
    createIngredient(payload, {
      onSuccess: closeModal,
      onError: (e) => {
        error.value = e.response?.data?.message ?? 'Error al guardar.'
      },
    })
  }
}

function confirmDelete() {
  deleteIngredient(confirmDeleteId.value, {
    onSuccess: () => {
      confirmDeleteId.value = null
    },
  })
}
</script>

<template>
  <div>
    <!-- Cabecera con buscador y botón añadir -->
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between mb-8">
      <div
        class="flex items-center gap-3 border-b border-outline/40 pb-3 focus-within:border-primary transition-colors w-full sm:max-w-sm"
      >
        <span class="material-symbols-outlined text-outline text-xl">search</span>
        <input
          v-model="search"
          type="text"
          placeholder="Buscar ingrediente..."
          class="w-full bg-transparent border-none outline-none text-sm text-primary placeholder:text-outline-variant"
        />
      </div>

      <AppButton @click="openCreate" class="flex items-center gap-2">
        <span class="material-symbols-outlined text-base">add</span>
        Nuevo ingrediente
      </AppButton>
    </div>

    <!-- Skeleton -->
    <div v-if="isLoading" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div v-for="i in 8" :key="i" class="animate-pulse border border-primary/10 p-4">
        <div class="h-5 bg-surface-container-high w-3/4 mb-2"></div>
        <div class="h-3 bg-surface-container-high w-1/2"></div>
      </div>
    </div>

    <!-- Grid -->
    <div
      v-else
      class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 transition-opacity duration-200"
      :class="{ 'opacity-60': isFetching }"
    >
      <div
        v-for="ingredient in ingredients"
        :key="ingredient.id"
        class="border border-primary/10 bg-surface p-4 flex flex-col gap-3"
      >
        <div class="flex items-start justify-between gap-2">
          <h3 class="leading-tight">{{ ingredient.name }}</h3>
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
            @click="openEdit(ingredient)"
            class="flex-1 flex items-center justify-center gap-1 py-1.5 text-xs font-semibold tracking-wide uppercase border border-secondary hover:bg-secondary hover:text-on-primary transition-all"
          >
            <span class="material-symbols-outlined text-sm">edit</span>
            Editar
          </button>
          <button
            @click="confirmDeleteId = ingredient.id"
            class="flex items-center justify-center px-3 py-1.5 border border-error/30 text-error hover:bg-error hover:text-on-error transition-all"
          >
            <span class="material-symbols-outlined text-sm">delete</span>
          </button>
        </div>
      </div>
    </div>

    <PaginationBar
      v-if="meta"
      :current-page="meta.current_page"
      :last-page="meta.last_page"
      :loading="isFetching"
      @update:current-page="page = $event"
    />

    <!-- Modal crear/editar -->
    <div
      v-if="modal"
      class="fixed inset-0 bg-primary/40 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-surface max-w-sm w-full p-8 border border-primary/10">
        <h3 class="mb-6">{{ editing ? 'Editar ingrediente' : 'Nuevo ingrediente' }}</h3>
        <div class="flex flex-col gap-6 mb-6">
          <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant"
              >Nombre</label
            >
            <input
              v-model="name"
              type="text"
              placeholder="Ej: Harina de fuerza"
              class="bg-transparent border-b pb-2 outline-none text-base text-primary placeholder:text-outline-variant transition-colors"
              :class="error ? 'border-error' : 'border-outline/40 focus:border-primary'"
              autofocus
            />
            <p v-if="error" class="text-xs text-error mt-1">{{ error }}</p>
          </div>
          <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant"
              >Descripción</label
            >
            <textarea
              v-model="description"
              rows="3"
              placeholder="Breve descripción del ingrediente..."
              class="bg-transparent border-b border-outline/40 pb-2 outline-none text-sm text-primary placeholder:text-outline-variant resize-none focus:border-primary transition-colors"
            ></textarea>
          </div>
        </div>
        <div class="flex gap-3">
          <button
            @click="save"
            :disabled="saving"
            class="flex-1 py-2 bg-secondary text-on-secondary text-sm font-semibold tracking-wide uppercase hover:opacity-80 disabled:opacity-50 transition-opacity"
          >
            {{ saving ? 'Guardando...' : 'Guardar' }}
          </button>
          <button
            @click="closeModal"
            class="flex-1 py-2 border border-primary/20 text-sm font-semibold tracking-wide uppercase hover:bg-surface-container-high transition-colors"
          >
            Cancelar
          </button>
        </div>
      </div>
    </div>

    <!-- Modal confirmar borrar -->
    <div
      v-if="confirmDeleteId"
      class="fixed inset-0 bg-primary/40 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click.self="confirmDeleteId = null"
    >
      <div class="bg-surface max-w-sm w-full p-8 border border-primary/10">
        <h3 class="mb-2">¿Eliminar ingrediente?</h3>
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
