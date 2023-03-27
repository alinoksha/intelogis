PHP_BASH = docker exec -it intelogis-php

setup:
	docker-compose up -d
	${PHP_BASH} composer install

test:
	${PHP_BASH} ./vendor/bin/phpunit ./tests/ShipmentModuleTest.php
