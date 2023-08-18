PHP = php
VENV = ./vendor/bin/
TEST_DIR = tests

test:
	${VENV}/phpunit --testdox ${TEST_DIR}