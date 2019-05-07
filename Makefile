.PHONY: up down build bash install

up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build

bash:
	docker-compose exec php-fpm bash

install:
	docker-compose build \
	docker-compose up -d \
	docker-compose exec php-fpm bash composer install \
	docker-compose exec php-fpm bash bin/console doctrine:migrations:migrate \