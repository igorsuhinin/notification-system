up:
	docker-compose up

down:
	docker-compose down

cli:
	docker-compose exec app bash

composer-install:
	docker-compose exec app composer install

migrate:
	docker-compose exec app bin/console doctrine:migrations:migrate --no-interaction

consume:
	docker-compose exec app bin/console messenger:consume --all

phpstan:
	docker-compose exec app vendor/bin/phpstan analyse src -l 7

psalm:
	docker-compose exec app vendor/bin/psalm --no-cache

phpcs:
	docker-compose exec app vendor/bin/phpcs --standard=PSR12 --no-cache --colors -p src

phpcs-fixer-check:
	docker-compose exec app vendor/bin/php-cs-fixer check src --using-cache=no

test:
	docker-compose exec -e APP_ENV=test app vendor/bin/phpunit --colors=always
