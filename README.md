# Recetario AI Claude

Aplicación web de recetas construida con Laravel 12 (API REST) y Vue 3 (SPA). Permite crear, explorar, filtrar y gestionar recetas con imágenes, ingredientes, categorías, valoraciones y favoritos.

## Estructura del proyecto

```
Recetario_AI_Claude/
├── recetas-api/     # Backend Laravel 12 (API REST + Sanctum)
├── recetas-vue/     # Frontend Vue 3 (SPA)
└── diseños/         # Archivos de diseño
```

## Stack

| Capa | Tecnología |
|---|---|
| Backend | Laravel 12, PHP 8.3, Laravel Sanctum |
| Frontend | Vue 3, Vite, TailwindCSS 4, TanStack Query |
| Base de datos | MySQL |
| Servidor local | Apache (API en puerto 8000) |
| Autenticación | Token Bearer via Sanctum |

## Requisitos previos

- PHP >= 8.3
- Composer
- Node.js >= 20.19.0
- MySQL
- Apache (XAMPP o similar)

## Puesta en marcha

### 1. Backend (recetas-api)

```bash
cd recetas-api
composer install
cp .env.example .env
php artisan key:generate
```

Configura la base de datos en `.env`:

```env
DB_DATABASE=recetario
DB_USERNAME=root
DB_PASSWORD=tu_password
```

```bash
php artisan migrate
php artisan storage:link
php artisan serve
```

La API quedará disponible en `http://localhost:8000`.

### 2. Frontend (recetas-vue)

```bash
cd recetas-vue
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

## Funcionalidades

- Explorar y buscar recetas por categoría, ingrediente o texto libre
- Crear, editar y eliminar recetas (usuarios autenticados / admin)
- Subir imagen por receta (800 × 533 px recomendado, máx. 2 MB, formato JPG/PNG/WebP)
- Sistema de valoraciones (puntuación + comentario)
- Favoritos por usuario
- Panel de control (dashboard) para gestionar tus recetas
- Control de acceso por roles: `user` y `admin`

## Convenciones de imágenes

| Contexto | Aspect ratio | Tamaño recomendado |
|---|---|---|
| Tarjeta (listado y dashboard) | 3:2 | 800 × 533 px |
| Hero de detalle | 21:9 | 1400 × 600 px |

Usa siempre `object-cover` — la imagen se recorta automáticamente si no coincide exactamente.
