import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AdminLayout from '@/layouts/AdminLayout.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior: () => ({ top: 0 }),
  routes: [
    {
      path: '/',
      component: () => import('@/layouts/AppLayout.vue'),
      children: [
        {
          path: '',
          name: 'home',
          component: () => import('@/views/HomeView.vue'),
        },
        {
          path: 'recetas',
          name: 'recipes',
          component: () => import('@/views/RecipesView.vue'),
        },
        {
          path: 'recetas/:slug',
          name: 'recipe-detail',
          component: () => import('@/views/RecipeDetailView.vue'),
        },
      ],
    },
    {
      path: '/dashboard',
      component: AdminLayout,
      meta: { requiresAuth: true, requiresAdmin: true },
      children: [
        {
          path: '',
          redirect: { name: 'dashboard-recipes' },
        },
        {
          path: 'recetas',
          name: 'dashboard-recipes',
          component: () => import('@/views/admin/DashboardRecipesView.vue'),
        },
        {
          path: 'ingredientes',
          name: 'dashboard-ingredients',
          component: () => import('@/views/admin/DashboardIngredientsView.vue'),
        },
        {
          path: 'crear',
          name: 'recipe-create',
          component: () => import('@/views/admin/RecipeFormView.vue'),
        },
        {
          path: 'editar/:slug',
          name: 'recipe-edit',
          component: () => import('@/views/admin/RecipeFormView.vue'),
        },
      ],
    },
    {
      path: '/auth',
      component: () => import('@/layouts/AuthLayout.vue'),
      children: [
        {
          path: 'login',
          name: 'login',
          component: () => import('@/views/auth/LoginView.vue'),
        },
        {
          path: 'register',
          name: 'register',
          component: () => import('@/views/auth/RegisterView.vue'),
        },
      ],
    },
  ],
})

router.beforeEach((to) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login' }
  }
})

export default router
