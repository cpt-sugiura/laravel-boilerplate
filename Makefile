install:
	cp .env.example .env
	docker-compose up -d --build
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app composer dump-autoload
	docker-compose exec app php artisan migrate:fresh --seed
	npm install
	npm run dev
create-skeleton-model-all:
	docker-compose exec app php artisan dacapo:generate
	docker-compose exec app php artisan migrate:fresh
	docker-compose exec app php artisan dump:model-from-db --all
cache-clear:
	docker-compose exec app php artisan optimize:clear
install-staging-server:
	composer install
	php artisan key:generate
	composer dump-autoload
	php artisan migrate:fresh --seed
	npm install
	npm run dev
	chmod 777 -R storage/logs
	chmod 777 -R storage/framework
