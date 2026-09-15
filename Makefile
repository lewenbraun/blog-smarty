build:
	docker compose build

css:
	sass --style=expanded --no-source-map assets/scss/main.scss public/assets/css/main.css

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose restart

logs:
	docker compose logs -f

db-migrate:
	docker compose exec app php vendor/bin/doctrine-migrations migrate --no-interaction

db-seed: db-migrate
	docker compose exec app php database/seed.php
