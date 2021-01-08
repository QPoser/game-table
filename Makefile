ifeq ($(PHP_NATIVE), true)
	DOCKER_COMPOSE=docker-compose -f docker-compose.php-native.yml
	SF=php bin/console
	COMPOSER=composer
else
	DOCKER_COMPOSE=docker-compose -f docker-compose.yml
	SF=docker-compose -f docker-compose.yml exec php php bin/console
	COMPOSER=docker-compose -f docker-compose.yml exec php composer
endif

ifeq ($(IS_MUTAGEN), true)
	DOCKER_COMPOSE=mutagen compose -f docker-compose-mutagen.yml
	SF=mutagen compose -f docker-compose-mutagen.yml exec php php bin/console
	COMPOSER=mutagen compose -f docker-compose-mutagen.yml exec php composer
endif

install-project:
	$(DOCKER_COMPOSE) build
	$(DOCKER_COMPOSE) up -d
	$(COMPOSER) create-project symfony/website-skeleton app
	cp -a ./app/. ./
	sudo rm -R ./app

init:
ifeq ($(IS_MUTAGEN), true)
	mutagen daemon start
endif
	$(DOCKER_COMPOSE) stop
	$(DOCKER_COMPOSE) build
	$(DOCKER_COMPOSE) up -d
	$(COMPOSER) install
	$(SF) cache:clear --env=dev
	$(SF) d:d:d --force --env=dev
	$(SF) d:d:c --env=dev
	$(SF) d:s:u -f --env=dev
	$(SF) d:f:l --env=dev -q
#ifeq ($(PHP_NATIVE), true)
#    symfony proxy:start
#    symfony serve -d || symfony server:status
#endif

init-mutagen:

