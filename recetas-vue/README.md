# recetas-vue — Frontend Vue 3

SPA del proyecto Recetario. Consume la API REST de `recetas-api` y ofrece una interfaz moderna y responsive para explorar, crear y gestionar recetas.

## Requisitos

- Node.js >= 20.19.0
- npm

## Instalación

```bash
npm install
cp .env.example .env
```

Configura la URL de la API en `.env`:

```env
VITE_API_URL=http://localhost:8000/api
```

```bash
npm run dev
```

La app quedará disponible en `http://localhost:5173`.

## Scripts disponibles

```bash
npm run dev      # Servidor de desarrollo con HMR
npm run build    # Build de producción
npm run preview  # Previsualizar el build
npm run lint     # Linter ESLint + Oxlint
```

## Stack y dependencias clave

| Librería | Versión | Uso |
|---|---|---|
| Vue 3 | ^3.5 | Framework UI (Composition API + script setup) |
| Vue Router | ^5.0 | Enrutamiento SPA |
| Pinia | ^3.0 | Estado global (auth) |
| TanStack Vue Query | ^5.100 | Fetching, caché y sincronización de datos |
| Axios | ^1.16 | Cliente HTTP |
| TailwindCSS | ^4.3 | Estilos utility-first |
| Tiptap | ^3.23 | Editor de texto enriquecido (instrucciones) |
| VueUse | ^14.3 | Composables de utilidad |

## Estructura de carpetas

```
src/
├── assets/
│   └── main.css              # Estilos globales y variables de Tailwind
├── components/
│   ├── AppNav.vue            # Barra de navegación principal
│   ├── AppFooter.vue         # Pie de página
│   ├── IngredientPicker.vue  # Selector de ingredientes con cantidad y unidad
│   ├── PaginationBar.vue     # Paginación reutilizable
│   └── RichTextEditor.vue    # Editor Tiptap para instrucciones
├── composables/
│   ├── useRecipes.js         # Queries y mutaciones de recetas (Vue Query)
│   ├── useCategories.js      # Query de categorías
│   └── useIngredients.js     # Query de ingredientes
├── layouts/
│   ├── AppLayout.vue         # Layout principal con nav y footer
│   └── AuthLayout.vue        # Layout centrado para login/register
├── lib/
│   └── axios.js              # Instancia de Axios configurada con interceptores
├── router/
│   └── index.js              # Rutas y guards de navegación
├── stores/
│   └── auth.js               # Store Pinia: usuario, token, login/logout
└── views/
    ├── auth/
    │   ├── LoginView.vue
    │   └── RegisterView.vue
    ├── HomeView.vue           # Portada con hero y grid de recetas destacadas
    ├── RecipesView.vue        # Listado paginado con filtros
    ├── RecipeDetailView.vue   # Detalle completo de una receta
    ├── RecipeFormView.vue     # Formulario crear/editar receta
    └── DashboardView.vue      # Panel del usuario autenticado
```

## Rutas de la app

| Ruta | Vista | Requiere auth |
|---|---|---|
| `/` | HomeView | No |
| `/recetas` | RecipesView | No |
| `/recetas/:slug` | RecipeDetailView | No |
| `/dashboard` | DashboardView | Sí |
| `/dashboard/crear` | RecipeFormView | Sí |
| `/dashboard/editar/:slug` | RecipeFormView | Sí |
| `/auth/login` | LoginView | No |
| `/auth/register` | RegisterView | No |

## Autenticación

El token Bearer se guarda en `localStorage` al hacer login o registro. El interceptor de Axios lo añade automáticamente en cada petición. Si la API devuelve `401`, el usuario es redirigido a `/auth/login`.

El store de Pinia (`stores/auth.js`) expone:
- `isAuthenticated` — booleano
- `isAdmin` — booleano (rol `admin`)
- `login()`, `logout()`, `register()`, `fetchUser()`

## Caché de datos (Vue Query)

Todas las peticiones de datos pasan por TanStack Vue Query. Los composables en `composables/` envuelven las llamadas a la API con `useQuery` y `useMutation`.

- **Stale time por defecto:** 5 minutos
- Al crear, editar o eliminar una receta, se invalida automáticamente la caché de listados.

## Imágenes de recetas

- Se envían al backend como `multipart/form-data`
- Formato recomendado: JPG o WebP
- Tamaño recomendado: **800 × 533 px** (aspect ratio 3:2)
- Peso máximo: **2 MB** (validado en el backend)
- Se muestran con `object-cover` — el recorte es automático si las proporciones difieren
