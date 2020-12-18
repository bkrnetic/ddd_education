.PHONY: dev composer_install clean_db up down start stop status help
.DEFAULT_GOAL := help

ROOT_DIR := $(shell pwd)
DOCKER_COMPOSE := $(shell which docker-compose)
DOCKER := $(shell which docker)

help:
	@echo "--> You are running default target. Look at the Makefile to see other available targets."

# setups dev environment
dev: composer_install up clean_db

# runs composer install and installs project dependencies
composer_install:
	@echo "--> Installing php dependencies"
	@${DOCKER} run --rm --interactive --tty --volume ${ROOT_DIR}:/app composer install

clean_db: sync_doctrine_storage
	@echo "--> refreshing database"
	# Migrate down all
	@${DOCKER_COMPOSE} exec backend bin/console doctrine:schema:drop --full-database
	# Migrate up all
	@${DOCKER_COMPOSE} exec backend bin/console d:m:m --no-interaction --quiet

sync_doctrine_storage:
	# Sync meta data database
	@${DOCKER_COMPOSE} exec backend bin/console doctrine:migrations:sync-metadata-storage --quiet

up:
	@echo "--> starting project containers"
	@${DOCKER_COMPOSE} up -d

start:
	@echo "--> starting existing project containers"
	@${DOCKER_COMPOSE} start

stop:
	@echo "--> stoping project containers"
	@${DOCKER_COMPOSE} stop

down:
	@echo "--> removing project containers"
	@${DOCKER_COMPOSE} down

setup_hooks:
	# Setup commit msg hook
	@cp ${ROOT_DIR}/__hooks/commit-msg ${ROOT_DIR}/.git/hooks/commit-msg
	# Setup pre commit hook
	@cp ${ROOT_DIR}/__hooks/pre-commit ${ROOT_DIR}/.git/hooks/pre-commit
	# Setup pre push hook
	@cp ${ROOT_DIR}/__hooks/pre-push ${ROOT_DIR}/.git/hooks/pre-push

clear_hooks:
	# Clear commit msg hook
	@rm ${ROOT_DIR}/.git/hooks/commit-msg
	# Clear pre commit hook
	@rm ${ROOT_DIR}/.git/hooks/pre-commit
	# Clear pre push hook
	@rm ${ROOT_DIR}/.git/hooks/pre-push

status:
	@$(DOCKER) ps

stan:
	@${DOCKER_COMPOSE} exec backend vendor/bin/phpstan analyze -l 8 src fixtures

csfix:
	@${DOCKER_COMPOSE} exec backend vendor/bin/php-cs-fixer fix --ansi -v --config=.php_cs.dist --path-mode=intersection src fixtures

tests:
	@${DOCKER_COMPOSE} exec backend vendor/bin/phpunit src

clear_cache:
	@${DOCKER_COMPOSE} exec backend bin/console cache:clear

