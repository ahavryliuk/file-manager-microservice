build:
	docker-compose up -d --build

test:
	docker-compose exec app sh -c "php vendor/bin/phpunit"

.PHONY: