<script setup>
defineProps({
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
})

const sizes = {
  sm: 'h-15',
  md: 'h-20',
  lg: 'h-25',
}
</script>

<template>
  <img src="/img/logo.png" alt="Recetario" :class="['object-contain w-auto', sizes[size]]" />
</template>
