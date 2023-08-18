PHP = php
VENV = ./vendor/bin/
COMPOSER = composer
TEST_DIR = tests

test:
	${VENV}/phpunit --testdox ${TEST_DIR}

install:
	${COMPOSER} update

php_install:
	${PHP} ${COMPOSER}.phar update