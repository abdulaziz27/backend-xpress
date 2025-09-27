.PHONY: help install start stop restart build logs shell test migrate seed fresh

# Default target
help:
	@echo "Available commands:"
	@echo "  install    - Install dependencies and setup project"
	@echo "  start      - Start Docker containers"
	@echo "  stop       - Stop Docker containers"
	@echo "  restart    - Restart Docker containers"
	@echo "  build      - Build Docker images"
	@echo "  logs       - Show container logs"
	@echo "  shell      - Access app container shell"
	@echo "  test       - Run tests"
	@echo "  migrate    - Run database migrations"
	@echo "  seed       - Run database seeders"
	@echo "  fresh      - Fresh install (migrate:fresh + seed)"

# Install dependencies and setup project
install:
	composer install
	cp .env.example .env
	php artisan key:generate
	php artisan migrate
	php artisan db:seed

# Docker commands
start:
	docker-compose -f docker-compose.yml -f docker-compose.local.yml up -d

stop:
	docker-compose down

restart:
	docker-compose restart

build:
	docker-compose build

logs:
	docker-compose logs -f

shell:
	docker-compose exec app bash

# Database commands
migrate:
	php artisan migrate

seed:
	php artisan db:seed

fresh:
	php artisan migrate:fresh --seed

# Testing
test:
	php artisan test

# Docker database commands
docker-migrate:
	docker-compose exec app php artisan migrate

docker-seed:
	docker-compose exec app php artisan db:seed

docker-fresh:
	docker-compose exec app php artisan migrate:fresh --seed

docker-test:
	docker-compose exec app php artisan test

# Production commands
prod-start:
	docker-compose up -d

prod-deploy:
	docker-compose exec app php artisan migrate --force
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache