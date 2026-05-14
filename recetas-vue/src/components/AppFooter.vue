<script setup lang="ts">
import AppLogo from './AppLogo.vue'
</script>

<template>
  <footer class="w-full bg-surface-container-low border-t border-primary/10">
    <div
      class="w-full py-12 px-5 md:px-16 flex flex-col justify-between items-center gap-4 max-w-300 mx-auto"
    >
      <div class="flex flex-col items-center gap-3">
        <AppLogo size="lg" />
        <p class="text-sm text-on-surface-variant">© {{ new Date().getFullYear() }} Recetario.</p>
      </div>
      <div class="flex gap-8">
        <a href="#" class="text-sm text-on-surface-variant hover:text-primary transition-colors"
          >Privacidad</a
        >
        <a href="#" class="text-sm text-on-surface-variant hover:text-primary transition-colors"
          >Términos</a
        >
        <a href="#" class="text-sm text-on-surface-variant hover:text-primary transition-colors"
          >Contacto</a
        >
      </div>
    </div>
  </footer>
</template>
