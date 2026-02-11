## Prueba tecnica Laravel

### Requisitos

- PHP 8.2+
- Composer
- SQLite o la base configurada en `.env`

### Pasos para correr el sitio

Clonar el proyecto, situarse en la carpeta del sitio y correr:

1) `docker compose up --build`
2) Abrir `http://0.0.0.0:8000`

Si es la primera vez, el contenedor crea la base SQLite y corre migraciones automaticamente.


### Endpoints de la API

- `POST /api/posts`
- `GET /api/posts`
- `GET /api/posts/{post}`
- `POST /api/posts/{post}/comments`

- `PATCH /api/posts/{post}` (actualizar estado, p.ej. draft -> published)

### Filtros

- `status` = `draft` o `published`
- `search` = busqueda parcial por titulo
- `published_from` / `published_to` = rango de fechas
- `per_page` = 1..50
- `include_comments` = true|false|1|0

### Reglas de negocio

- Un post publicado no puede volver a estado `draft`. Intentarlo devuelve un 422 con un mensaje de negocio.
- La regla de título único se aplica solo a los posts que están `published` y solo para el mismo día de creación: no impide que existan borradores con el mismo título.
- Al publicar un post, el campo `published_at` se establece automáticamente si no se provee; 
- No se pueden crear comentarios en posts que no estén `published`; intentar hacerlo devuelve un 422 con detalle en `errors`.
- Existe un límite configurable de comentarios por post (`config('posts.max_comments_per_post', 10)` por defecto). Si se alcanza, la API devuelve un 422 con el motivo.

Formato de errores de negocio (HTTP 422) — ejemplo resumido:

```json
{
	"message": "Descripción breve del fallo de negocio",
	"errors": {
		"campo": ["Mensaje específico"]
	}
}
```

### Probar con Postman

1) Crear una coleccion nueva.
2) Agregar una variable de coleccion `baseUrl` con valor `http://127.0.0.1:8000`.
3) Crear requests con `Accept: application/json` y `Content-Type: application/json`.

También puedes importar la colección `postman_collection.json` incluida en este repositorio para probar rápidamente los endpoints (Posts y Comments). En Postman: File > Import > seleccioná `postman_collection.json`.


#### Crear post (publicado)

- Metodo: `POST`
- URL: `{{baseUrl}}/api/posts`
- Body (raw JSON):

```json
{
	"title": "Mi primer post",
	"body": "Contenido de prueba con al menos diez caracteres.",
	"status": "published"
}
```

#### Listar posts (con filtros)

- Metodo: `GET`
- URL: `{{baseUrl}}/api/posts?status=published&per_page=5&include_comments=true`

#### Obtener post por id

- Metodo: `GET`
- URL: `{{baseUrl}}/api/posts/1`

#### Crear comentario

- Metodo: `POST`
- URL: `{{baseUrl}}/api/posts/1/comments`
- Body (raw JSON):

```json
{
	"author_name": "Ana Test",
	"body": "Muy buen post"
}
```

#### Publicar un post (cambiar de draft a published)

- Metodo: `PATCH`
- URL: `{{baseUrl}}/api/posts/1`
- Body (raw JSON):

```json
{
	"status": "published"
}
```

Respuesta esperada (ejemplo):

```json
{
	"data": {
		"id": 1,
		"title": "Mi primer post",
		"body": "Contenido de prueba...",
		"status": "published",
		"published_at": "2026-02-11T00:00:00.000000Z",
		...
	}
}
```

Respuesta de error 422 (ejemplo):

```json
{
	"message": "No se puede publicar el post porque ya existe otro post con este título hoy.",
	"errors": {
		"title": [
			"Ya existe un post publicado con este título hoy."
		]
	}
}
```

### Tests

- `php artisan test`

## Que mejoraría con más tiempo

- Documentación OpenAPI/Swagger.
- Autenticación y autorización (Laravel Sanctum/Passport), roles y policies para proteger endpoints.
- Más tests: unitarios, integración y E2E.
- Cache (Redis) para listados y conteos; optimización de consultas e índices en DB.
- Límite de comentarios configurable con tests y mensajes claros.
- Versionado de API y pruebas de contrato para evitar rupturas.
