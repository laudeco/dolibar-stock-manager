code-style:

	docker-compose run --rm php /app/vendor/bin/php-cs-fixer -v fix --allow-risky yes

## -- Composer

composer-install: ## Allows to manually launch the composer install command in case you did some manual changes

	docker-compose run --rm composer install $(p)

composer-require: ## Allows to require new composer vendors. Usage: make composer-require p=symfony/assets

	docker-compose run --rm composer require $(p)

composer-update: ## Allows to update 1 or all composer vendors. Usage: make compage-update p=symfony/assets (when leaving p empty, it will update all packages)

	docker-compose run --rm composer update $(p)


## -- Docker compose

build: ## Builds the docker images and executes the vendors
	docker-compose build

compose-build: ## Run build and composer install
	build
	composer-install

upgrade:	## Upgrades the docker images
	docker-compose build --pull
	docker-compose pull


## -- Docker

start: up

up: ## Up
	docker-compose up -d

stop: ## Stops all containers
	docker-compose stop

sh: ## Gets inside a container, use 's' variable to select a service. make s=php sh
	docker-compose run --rm $(s) sh -l

logs: ## Shows the logs of a container. Use 's' variable to filter on a specific container.
	docker-compose logs -f $(s)


## -- Symfony

console: ## Executes the Symfony command
	docker-compose exec php sh -lc '/app/bin/console $(c)'

## -- browser

open: ## Open the browser on the right port
	$(eval PORT := $(shell port=$$(docker-compose port nginx 80) && echo $${port##*:}))
	open http://localhost:$(PORT)/index.php
