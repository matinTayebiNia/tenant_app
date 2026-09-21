.PHONY: setup up down restart build logs ps test shell artisan seed migrate

# One-command bootstrap: build images, bring the stack up. The `migrate` service runs
# migrations then seeds demo data (idempotent) before app/queue/scheduler start.
setup:
	@[ -f .env ] || cp .env.example .env
	docker compose up -d --build

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose restart

build:
	docker compose build

logs:
	docker compose logs -f

ps:
	docker compose ps

stock-movement-test:
	docker compose exec app php artisan test Modules/Stock/tests/Feature/StockMovementServiceTest.php

stock-level-test:
	docker compose exec app php artisan test Modules/Stock/tests/Feature/StockLevelTestApi.php

shell:
	docker compose exec app bash

artisan:
	docker compose exec app php artisan $(filter-out $@,$(MAKECMDGOALS))

%:
	@:

migrate:
	docker compose exec app php artisan migrate

seed:
	docker compose exec app php artisan db:seed
