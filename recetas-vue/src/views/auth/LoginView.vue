<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppButton from '@/components/AppButton.vue'

const router = useRouter()
const auth = useAuthStore()

const form = ref({ email: '', password: '' })
const error = ref('')
const loading = ref(false)

async function handleSubmit() {
  error.value = ''
  loading.value = true
  try {
    await auth.login(form.value)
    router.push({ name: 'home' })
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Credenciales incorrectas.'
    console.log(error.value)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div>
    <div class="mb-8 text-center">
      <h1 class="mb-2">Bienvenido</h1>
      <p class="text-sm text-on-surface-variant">Accede a tu colección de recetas</p>
    </div>

    <form @submit.prevent="handleSubmit" class="flex flex-col gap-6">
      <!-- Error -->
      <p
        v-if="error"
        class="text-sm text-error bg-error-container px-4 py-3 border border-error/20"
      >
        {{ error }}
      </p>

      <!-- Email -->
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant">
          Correo electrónico
        </label>
        <div
          class="flex items-center gap-3 border-b border-outline/40 pb-3 focus-within:border-primary transition-colors"
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
      </div>

      <!-- Password -->
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold tracking-widest uppercase text-on-surface-variant">
          Contraseña
        </label>
        <div
          class="flex items-center gap-3 border-b border-outline/40 pb-3 focus-within:border-primary transition-colors"
        >
          <span class="material-symbols-outlined text-outline text-xl">lock</span>
          <input
            v-model="form.password"
            type="password"
            autocomplete="current-password"
            required
            placeholder="••••••••"
            class="w-full bg-transparent border-none outline-none text-base text-primary placeholder:text-outline-variant"
          />
        </div>
      </div>

      <!-- Submit -->
      <AppButton type="submit" :loading="loading" loading-text="Entrando..." class="mt-2 w-full">
        Entrar
      </AppButton>
    </form>

    <!-- Register link -->
    <p class="mt-8 text-center text-sm text-on-surface-variant">
      ¿No tienes cuenta?
      <RouterLink
        to="/auth/register"
        class="text-secondary border-b border-secondary hover:opacity-70 transition-opacity"
      >
        Regístrate
      </RouterLink>
    </p>
  </div>
</template>
