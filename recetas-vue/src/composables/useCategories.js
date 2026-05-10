import { useQuery } from '@tanstack/vue-query'
import api from '@/lib/axios'

export function useCategories() {
  return useQuery({
    queryKey: ['categories'],
    queryFn: () => api.get('/categories').then((r) => r.data.data ?? r.data),
    staleTime: Infinity, // las categorías no cambian a menudo, no hay que refetchear
  })
}
