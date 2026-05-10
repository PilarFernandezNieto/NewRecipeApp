# recetas-api — Backend Laravel 12

API REST del proyecto Recetario. Gestiona autenticación, recetas, categorías, ingredientes, valoraciones y favoritos.

## Requisitos

- PHP >= 8.3
- Composer
- MySQL
- Extensiones PHP: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`

## Instalación

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve
```

## Variables de entorno (.env)

Copia `.env.example` y rellena los valores:

```env
APP_NAME="Recetario API"
APP_ENV=local
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=recetario
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public
```

Nunca subas `.env` a git. Está incluido en `.gitignore`.

## Estructura relevante

```
recetas-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/     # AuthController, RecipeController, CategoryController,
│   │   │                        # IngredientController, DifficultyController
│   │   ├── Middleware/          # EnsureUserIsAdmin
│   │   ├── Requests/            # Form Requests con validación (Auth, Recipe, Category, Ingredient)
│   │   └── Resources/           # API Resources: RecipeResource, RecipeCollection, etc.
│   ├── Models/                  # User, Recipe, Category, Ingredient, Rating, Favorite, Difficulty
│   └── Policies/                # RecipePolicy, UserPolicy
├── database/
│   ├── migrations/              # Esquema completo de la base de datos
│   └── seeders/
├── routes/
│   └── api.php                  # Todas las rutas de la API
└── storage/
    └── app/public/recipes/      # Imágenes subidas (accesibles via /storage/recipes/)
```

## Rutas de la API

Base URL: `http://localhost:8000/api`

### Autenticación

| Método | Ruta | Acceso | Descripción |
|---|---|---|---|
| POST | `/auth/register` | Público | Registro de usuario |
| POST | `/auth/login` | Público | Login, devuelve token |
| POST | `/auth/logout` | Auth | Cierra sesión |
| GET | `/auth/me` | Auth | Datos del usuario actual |
| POST | `/auth/forgot-password` | Público | Solicitar reset de contraseña |
| POST | `/auth/reset-password` | Público | Resetear contraseña |

### Recetas

| Método | Ruta | Acceso | Descripción |
|---|---|---|---|
| GET | `/recipes` | Público | Listado paginado con filtros |
| GET | `/recipes/{slug}` | Público | Detalle de receta |
| POST | `/recipes` | Admin | Crear receta |
| POST | `/recipes/{slug}` | Admin | Actualizar receta |
| DELETE | `/recipes/{slug}` | Admin | Eliminar receta |

**Filtros disponibles en GET /recipes:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `page` | int | Número de página |
| `per_page` | int | Resultados por página |
| `search` | string | Búsqueda por título o descripción |
| `category` | string | Slug de categoría |
| `difficulty` | string | Slug de dificultad |
| `ingredient` | string | Slug de ingrediente |
| `mine` | boolean | Solo recetas del usuario autenticado |

### Categorías

| Método | Ruta | Acceso | Descripción |
|---|---|---|---|
| GET | `/categories` | Público | Listado de categorías |
| GET | `/categories/{slug}` | Público | Detalle de categoría |
| POST | `/categories` | Admin | Crear categoría |
| POST | `/categories/{slug}` | Admin | Actualizar categoría |
| DELETE | `/categories/{slug}` | Admin | Eliminar categoría |

### Ingredientes

| Método | Ruta | Acceso | Descripción |
|---|---|---|---|
| GET | `/ingredients` | Público | Listado de ingredientes |
| GET | `/ingredients/{slug}` | Público | Detalle de ingrediente |
| POST | `/ingredients` | Admin | Crear ingrediente |
| PUT | `/ingredients/{slug}` | Admin | Actualizar ingrediente |
| DELETE | `/ingredients/{slug}` | Admin | Eliminar ingrediente |

### Dificultades

| Método | Ruta | Acceso | Descripción |
|---|---|---|---|
| GET | `/difficulties` | Público | Listado de dificultades |

## Modelos y relaciones

```
User ──────────< Recipe >────────── Category
                   │  └──────────── Difficulty
                   │
                   ├──< Rating (score, comment)
                   ├──< Favorite
                   └──>< Ingredient (pivot: quantity, unit)
```

## Autenticación

Se usa **Laravel Sanctum** con tokens Bearer. El frontend debe enviar:

```
Authorization: Bearer {token}
```

El token se obtiene al hacer login o register y debe guardarse en el cliente (localStorage).

## Imágenes de recetas

- Se almacenan en `storage/app/public/recipes/`
- Accesibles públicamente en `/storage/recipes/{filename}` (requiere `php artisan storage:link`)
- Validación actual: `nullable | image | max:2048` (2 MB máximo)
- Tamaño recomendado de subida: **800 × 533 px**, formato JPG/PNG/WebP

## Roles de usuario

| Rol | Permisos |
|---|---|
| `user` | Leer recetas, crear favoritos, valorar |
| `admin` | Todo lo anterior + crear/editar/eliminar recetas, categorías e ingredientes |

## Comandos útiles

```bash
# Limpiar caché de configuración
php artisan config:clear

# Ver todas las rutas registradas
php artisan route:list

# Crear enlace simbólico para storage
php artisan storage:link

# Ejecutar tests
php artisan test
```
