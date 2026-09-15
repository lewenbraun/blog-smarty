## Blog Smarty

A test assignment to build a blog with categories and articles.

### Stack

Native PHP application without a framework.

- PHP 8.5
- Smarty 5
- MySQL 8.4
- Doctrine ORM
- Doctrine Migrations
- SCSS
- Docker

### Deployment

To use the Makefile, install [GNU Make](https://www.gnu.org/software/make/). If it is unavailable, follow the Docker Compose instructions below.

#### With Makefile

Prepare the environment:

```bash
cp .env.example .env
```

Start the project:

```bash
make build
make up
make composer
make db-seed
```

`make db-seed` applies migrations and seeds 6 categories and 50 articles.

#### Without Makefile

Prepare the environment:

```bash
cp .env.example .env
```

Start the project:

```bash
docker compose build
docker compose up -d
docker compose exec app composer install
docker compose exec app php vendor/bin/doctrine-migrations migrate --no-interaction
docker compose exec app php database/seed.php
```

Open:

```text
http://localhost:8080/
```

### Additional commands

| Action | Makefile | Docker Compose |
|---|---|---|
| Stop containers | `make down` | `docker compose down` |
| Start containers | `make up` | `docker compose up -d` |
| Restart containers | `make restart` | `docker compose restart` |

### Styles

The project already includes compiled CSS. If you want to change it, edit the SCSS sources, install Dart Sass, and rebuild.

With Makefile:

```bash
make css
```

Without Makefile:

```bash
sass --style=expanded --no-source-map assets/scss/main.scss public/assets/css/main.css
```

Both commands update `public/assets/css/main.css`.
