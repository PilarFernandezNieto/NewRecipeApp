import { computed, toValue } from 'vue'
import { useQuery, useMutation, useQueryClient, keepPreviousData } from '@tanstack/vue-query'
import api from '@/lib/axios'

/**
 * Lista paginada de recetas con filtros opcionales.
 * @param {Ref | Reactive | Object} params - { page, search, category, difficulty, per_page }
 */
export function useRecipes(params) {
  return useQuery({
    queryKey: computed(() => ['recipes', toValue(params)]),
    queryFn: () => api.get('/recipes', { params: toValue(params) }).then((r) => r.data),
    placeholderData: keepPreviousData, // mantiene datos anteriores mientras carga la nueva página
  })
}

/**
 * Receta individual por slug.
 */
export function useRecipe(slug) {
  return useQuery({
    queryKey: computed(() => ['recipes', toValue(slug)]),
    queryFn: () => api.get(`/recipes/${toValue(slug)}`).then((r) => r.data.data),
    enabled: computed(() => !!toValue(slug)),
  })
}

export function useCreateRecipe() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (formData) =>
      api
        .post('/recipes', formData, { headers: { 'Content-Type': 'multipart/form-data' } })
        .then((r) => r.data.data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['recipes'] }),
  })
}

export function useUpdateRecipe() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: ({ slug, formData }) =>
      api
        .post(`/recipes/${slug}`, formData, { headers: { 'Content-Type': 'multipart/form-data' } })
        .then((r) => r.data.data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['recipes'] }),
  })
}

export function useDeleteRecipe() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (id) => api.delete(`/recipes/${id}`),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['recipes'] }),
  })
}
