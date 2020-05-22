install-project:
	docker-compose build
	docker-compose up -d
	docker-compose exec -u www-data php composer create-project symfony/website-skeleton app
	cp -a ./app/. ./
	sudo rm -R ./app

init:
	docker-compose stop
	docker-compose build
	docker-compose up -d
	docker-compose exec -u www-data php composer install
	docker-compose exec -u www-data php php bin/console cache:clear --env=dev
	docker-compose exec -u www-data php php bin/console d:d:d --force --env=dev
	docker-compose exec -u www-data php php bin/console d:d:c --env=dev
	docker-compose exec -u www-data php php bin/console d:s:u -f --env=dev
	docker-compose exec -u www-data php php bin/console d:f:l --env=dev -q
