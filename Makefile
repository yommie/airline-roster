run: build up composer-install generate-key migrate

build:
	docker compose build app

up:
	docker compose up -d

composer-install:
	docker compose exec app composer install

generate-key:
	docker compose exec app php artisan key:generate

migrate:
	docker compose exec app php artisan migrate

down:
	docker compose down

remove-image:
	docker rmi yommie-airline-roster --force

clean: down remove-image

logs:
	docker compose logs nginx

pause:
	docker compose pause

unpause:
	docker compose unpause

tests:
	docker compose exec app ./vendor/bin/phpunit

tests-coverage:
	docker compose exec app ./vendor/bin/phpunit --coverage-html ./public/tests

.PHONY: run build up composer-install generate-key migrate down remove-image clean logs pause unpause tests tests-coverage
