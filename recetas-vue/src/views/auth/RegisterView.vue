<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = ref({ name: '', email: '', password: '', password_confirmation: '' })
const errors = ref({})
const loading = ref(false)

async function handleSubmit() {
  errors.value = {}
  loading.value = true
  try {
    await auth.register(form.value)
    router.push({ name: 'home' })
  } catch (e) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors ?? {}
    } else {
      errors.value = { general: [e.response?.data?.message ?? 'Error al crear la cuenta.'] }
    }
  } finally {
    loading.value = false
  }
}

function fieldError(field) {
  return errors.value[field]?.[0] ?? ''
}
</script>

<template>
  <div>
    <div class="mb-8 text-center">
      <h1 class="font-display text-3xl font-bold text-primary mb-2">Crear cuenta</h1>
      <p class="text-sm text-on-surface-variant">Empieza a guardar tus recetas</p>
    </div>

    <form @submit.prevent="handleSubmit" class="flex flex-col gap-6">
      <!-- Error general -->
      <p v-if="errors.general" class="text-sm text-error bg-error-container px-4 py-3 border border-error/20">
        {{ errors.general[0] }}
      </p>

      <!-- Nombre -->
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant">
          Nombre
        </label>
        <div
          class="flex items-center gap-3 border-b pb-3 transition-colors"
          :class="fieldError('name') ? 'border-error' : 'border-outline/40 focus-within:border-primary'"
        >
          <span class="material-symbols-outlined text-outline text-xl">person</span>
          <input
            v-model="form.name"
            type="text"
            autocomplete="name"
            required
            placeholder="Tu nombre"
            class="w-full bg-transparent border-none outline-none text-base text-primary placeholder:text-outline-variant"
          />
        </div>
        <p v-if="fieldError('name')" class="text-xs text-error mt-1">{{ fieldError('name') }}</p>
      </div>

      <!-- Email -->
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant">
          Correo electrónico
        </label>
        <div
          class="flex items-center gap-3 border-b pb-3 transition-colors"
          :class="fieldError('email') ? 'border-error' : 'border-outline/40 focus-within:border-primary'"
        >
          <span class="material-symbols-outlined text-outline text-xl">mail</span>
          <input
            v-model="form.email"
            type="email"
            autocomplete="email"
            required
            placeholder="tu@correo.com"
            class="w-full bg-transparent border-none outline-none text-base text-primary placeholder:text-outline-variant"
          />
        </div>
        <p v-if="fieldError('email')" class="text-xs text-error mt-1">{{ fieldError('email') }}</p>
      </div>

      <!-- Password -->
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant">
          Contraseña
        </label>
        <div
          class="flex items-center gap-3 border-b pb-3 transition-colors"
          :class="fieldError('password') ? 'border-error' : 'border-outline/40 focus-within:border-primary'"
        >
          <span class="material-symbols-outlined text-outline text-xl">lock</span>
          <input
            v-model="form.password"
            type="password"
            autocomplete="new-password"
            required
            placeholder="Mínimo 8 caracteres"
            class="w-full bg-transparent border-none outline-none text-base text-primary placeholder:text-outline-variant"
          />
        </div>
        <p v-if="fieldError('password')" class="text-xs text-error mt-1">{{ fieldError('password') }}</p>
      </div>

      <!-- Confirm password -->
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant">
          Confirmar contraseña
        </label>
        <div class="flex items-center gap-3 border-b border-outline/40 pb-3 focus-within:border-primary transition-colors">
          <span class="material-symbols-outlined text-outline text-xl">lock_reset</span>
          <input
            v-model="form.password_confirmation"
            type="password"
            autocomplete="new-password"
            required
            placeholder="Repite tu contraseña"
            class="w-full bg-transparent border-none outline-none text-base text-primary placeholder:text-outline-variant"
          />
        </div>
      </div>

      <!-- Submit -->
      <button
        type="submit"
        :disabled="loading"
        class="mt-2 w-full py-3 bg-primary text-on-primary text-sm font-semibold tracking-widest uppercase transition-opacity hover:opacity-80 disabled:opacity-50"
      >
        <span v-if="loading" class="material-symbols-outlined text-base animate-spin align-middle mr-1">progress_activity</span>
        {{ loading ? 'Creando cuenta...' : 'Crear cuenta' }}
      </button>
    </form>

    <!-- Login link -->
    <p class="mt-8 text-center text-sm text-on-surface-variant">
      ¿Ya tienes cuenta?
      <RouterLink to="/auth/login" class="text-secondary border-b border-secondary hover:opacity-70 transition-opacity">
        Entrar
      </RouterLink>
    </p>
  </div>
</template>
