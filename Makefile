DC=docker-compose
DCEXEC=${DC} exec -u 1000
DCEXEC_PHP=${DCEXEC} php
DCRUN = ${DC} run --rm -u 1000

up:
	${DC} up -d --build

down:
	${DC} down

bash:
	${DCEXEC_PHP} bash

composer-install:
	${DCRUN} composer composer install --ignore-platform-reqs --no-scripts

composer-bash:
	${DCRUN} composer bash

migrate:
	${DCEXEC_PHP} bin/console d:m:m

fixtures:
	${DCEXEC_PHP} bin/console doctrine:fixtures:load

install: up composer-install migrate