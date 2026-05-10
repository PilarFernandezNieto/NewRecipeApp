<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useQuery } from '@tanstack/vue-query'
import { useCreateRecipe, useUpdateRecipe, useRecipe } from '@/composables/useRecipes'
import { useCategories } from '@/composables/useCategories'
import RichTextEditor from '@/components/RichTextEditor.vue'
import IngredientPicker from '@/components/IngredientPicker.vue'
import api from '@/lib/axios'

const router = useRouter()
const route = useRoute()

const isEditing = computed(() => !!route.params.slug)

const { data: categories } = useCategories()

const { data: difficultiesRes } = useQuery({
  queryKey: ['difficulties'],
  queryFn: () => api.get('/difficulties').then((r) => r.data.data ?? r.data),
  staleTime: Infinity,
})
const difficultyList = computed(() => difficultiesRes.value ?? [])

// Si es edición, cargamos la receta actual por slug
const slug = computed(() => route.params.slug ?? null)
const { data: existingRecipe } = useRecipe(slug)

const form = ref({
  title: '',
  description: '',
  category_id: '',
  difficulty_id: '',
  prep_time: '',
  cook_time: '',
  servings: '',
  instructions: '',
  ingredients: [],
})
const imageFile = ref(null)
const imagePreview = ref(null)
const errors = ref({})

// Precarga los datos cuando estamos editando
watch(existingRecipe, (recipe) => {
  if (!recipe) return
  form.value = {
    title: recipe.title,
    description: recipe.description ?? '',
    category_id: recipe.category?.id ?? '',
    difficulty_id: recipe.difficulty?.id ?? '',
    prep_time: recipe.prep_time ?? '',
    cook_time: recipe.cook_time ?? '',
    servings: recipe.servings ?? '',
    instructions: recipe.instructions ?? '',
    ingredients:
      recipe.ingredients?.map((ing) => ({
        id: ing.id,
        name: ing.name,
        quantity: ing.quantity ?? '',
        unit: ing.unit ?? '',
      })) ?? [],
  }
  imagePreview.value = recipe.image ?? null
}, { immediate: true })

function onImageChange(event) {
  const file = event.target.files[0]
  if (!file) return
  imageFile.value = file
  imagePreview.value = URL.createObjectURL(file)
}

const { mutateAsync: createRecipe, isPending: creating } = useCreateRecipe()
const { mutateAsync: updateRecipe, isPending: updating } = useUpdateRecipe()
const isPending = computed(() => creating.value || updating.value)

function fieldError(field) {
  return errors.value[field]?.[0] ?? ''
}

async function handleSubmit() {
  errors.value = {}

  const formData = new FormData()
  formData.append('title', form.value.title)
  formData.append('description', form.value.description)
  formData.append('category_id', form.value.category_id)
  formData.append('difficulty_id', form.value.difficulty_id)
  formData.append('prep_time', form.value.prep_time)
  formData.append('cook_time', form.value.cook_time)
  formData.append('servings', form.value.servings)
  formData.append('instructions', form.value.instructions)

  if (imageFile.value) {
    formData.append('image', imageFile.value)
  }

  form.value.ingredients.forEach((ing, i) => {
    formData.append(`ingredients[${i}][id]`, ing.id)
    formData.append(`ingredients[${i}][quantity]`, ing.quantity)
    if (ing.unit) formData.append(`ingredients[${i}][unit]`, ing.unit)
  })

  try {
    if (isEditing.value) {
      await updateRecipe({ slug: slug.value, formData })
    } else {
      await createRecipe(formData)
    }
    router.push({ name: 'dashboard' })
  } catch (e) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors ?? {}
    }
  }
}
</script>

<template>
  <div class="max-w-container-max mx-auto px-5 md:px-16 py-12">
    <!-- Header -->
    <div class="mb-10">
      <RouterLink
        to="/dashboard"
        class="flex items-center gap-1 text-sm text-on-surface-variant hover:text-primary mb-6 transition-colors"
      >
        <span class="material-symbols-outlined text-base">arrow_back</span>
        Volver al panel
      </RouterLink>
      <h1 class="font-display text-3xl md:text-4xl font-bold text-primary">
        {{ isEditing ? 'Editar receta' : 'Nueva receta' }}
      </h1>
    </div>

    <form @submit.prevent="handleSubmit" class="flex flex-col gap-10 max-w-3xl">
      <!-- Título -->
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant"
          >Título *</label
        >
        <input
          v-model="form.title"
          type="text"
          required
          placeholder="El nombre de tu receta"
          class="bg-transparent border-b pb-3 outline-none text-lg text-primary placeholder:text-outline-variant transition-colors"
          :class="fieldError('title') ? 'border-error' : 'border-outline/40 focus:border-primary'"
        />
        <p v-if="fieldError('title')" class="text-xs text-error">{{ fieldError('title') }}</p>
      </div>

      <!-- Descripción -->
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant"
          >Descripción</label
        >
        <textarea
          v-model="form.description"
          rows="3"
          placeholder="Una frase que capture la esencia del plato"
          class="bg-transparent border-b border-outline/40 pb-3 outline-none text-base text-primary placeholder:text-outline-variant resize-none focus:border-primary transition-colors"
        ></textarea>
      </div>

      <!-- Categoría + Dificultad -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant"
            >Categoría *</label
          >
          <select
            v-model="form.category_id"
            required
            class="bg-transparent border-b border-outline/40 pb-3 outline-none text-base text-primary appearance-none cursor-pointer focus:border-primary transition-colors"
            :class="fieldError('category_id') ? 'border-error' : ''"
          >
            <option value="" disabled>Selecciona una categoría</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
          <p v-if="fieldError('category_id')" class="text-xs text-error">
            {{ fieldError('category_id') }}
          </p>
        </div>

        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant"
            >Dificultad *</label
          >
          <select
            v-model="form.difficulty_id"
            required
            class="bg-transparent border-b border-outline/40 pb-3 outline-none text-base text-primary appearance-none cursor-pointer focus:border-primary transition-colors"
          >
            <option value="" disabled>Selecciona la dificultad</option>
            <option v-for="diff in difficultyList" :key="diff.id" :value="diff.id">
              {{ diff.name }}
            </option>
          </select>
        </div>
      </div>

      <!-- Tiempos + Porciones -->
      <div class="grid grid-cols-3 gap-6">
        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant"
            >Prep. (min) *</label
          >
          <input
            v-model="form.prep_time"
            type="number"
            min="1"
            required
            placeholder="30"
            class="bg-transparent border-b border-outline/40 pb-3 outline-none text-base text-primary placeholder:text-outline-variant focus:border-primary transition-colors"
          />
        </div>
        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant"
            >Cocción (min)</label
          >
          <input
            v-model="form.cook_time"
            type="number"
            min="0"
            placeholder="20"
            class="bg-transparent border-b border-outline/40 pb-3 outline-none text-base text-primary placeholder:text-outline-variant focus:border-primary transition-colors"
          />
        </div>
        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant"
            >Porciones *</label
          >
          <input
            v-model="form.servings"
            type="number"
            min="1"
            required
            placeholder="4"
            class="bg-transparent border-b border-outline/40 pb-3 outline-none text-base text-primary placeholder:text-outline-variant focus:border-primary transition-colors"
          />
        </div>
      </div>

      <!-- Imagen -->
      <div class="flex flex-col gap-3">
        <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant"
          >Imagen</label
        >
        <div class="flex flex-col gap-4">
          <div
            v-if="imagePreview"
            class="relative aspect-3/2 max-w-sm overflow-hidden border border-primary/10"
          >
            <img :src="imagePreview" alt="Preview" class="w-full h-full object-cover" />
            <button
              type="button"
              @click="((imageFile = null), (imagePreview = null))"
              class="absolute top-2 right-2 bg-surface/90 p-1 hover:bg-surface transition-colors"
            >
              <span class="material-symbols-outlined text-sm">close</span>
            </button>
          </div>
          <label
            class="flex items-center gap-3 cursor-pointer w-fit border border-outline/30 px-4 py-2 hover:bg-surface-container-high transition-colors"
          >
            <span class="material-symbols-outlined text-outline text-xl">upload</span>
            <span class="text-sm text-on-surface-variant">{{
              imagePreview ? 'Cambiar imagen' : 'Subir imagen'
            }}</span>
            <input type="file" accept="image/*" @change="onImageChange" class="hidden" />
          </label>
        </div>
      </div>

      <!-- Ingredientes -->
      <div class="flex flex-col gap-3">
        <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant"
          >Ingredientes *</label
        >
        <IngredientPicker v-model="form.ingredients" />
        <p v-if="fieldError('ingredients')" class="text-xs text-error">
          {{ fieldError('ingredients') }}
        </p>
      </div>

      <!-- Instrucciones -->
      <div class="flex flex-col gap-3">
        <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant"
          >Instrucciones *</label
        >
        <RichTextEditor v-model="form.instructions" />
        <p v-if="fieldError('instructions')" class="text-xs text-error">
          {{ fieldError('instructions') }}
        </p>
      </div>

      <!-- Submit -->
      <div class="flex items-center gap-4 pt-4 border-t border-primary/10">
        <button
          type="submit"
          :disabled="isPending"
          class="px-8 py-3 bg-primary text-on-primary text-sm font-semibold tracking-widest uppercase hover:opacity-80 disabled:opacity-50 transition-opacity"
        >
          {{ isPending ? 'Guardando...' : isEditing ? 'Guardar cambios' : 'Publicar receta' }}
        </button>
        <RouterLink
          to="/dashboard"
          class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors"
        >
          Cancelar
        </RouterLink>
      </div>
    </form>
  </div>
</template>
