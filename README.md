## Prueba tecnica Laravel

### Requisitos

- PHP 8.2+
- Composer
- SQLite o la base configurada en `.env`

### Pasos para correr el sitio

1) `composer install`
2) `cp .env.example .env`
3) `php artisan key:generate`
4) `php artisan migrate`
5) `php artisan serve`

El sitio queda en `http://127.0.0.1:8000`.

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

### Docker

1) `docker compose up --build`
2) Abrir `http://0.0.0.0:8000`

Si es la primera vez, el contenedor crea la base SQLite y corre migraciones automaticamente.
