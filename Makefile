help: ## Prints this help.
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

up: ## Builds the docker containers and installs dependencies
	docker-compose up -d

stop: ## stops the docker containers
	docker-compose stop

down: ## destroy all container
	docker-compose down
	
composer: ## run composer command using: make composer argument=install
	docker exec -it jedy-php-fpm php composer.phar $(argument)

log: ## see prod logs
	 tail -f $(PWD)/var/logs/prod.log

install: ## Install database
	docker exec -it jedy-php-fpm php bin/console doctrine:schema:create