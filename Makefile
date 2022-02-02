COPY = cp

ifeq ($(OS),Windows_NT)
    COPY = copy
endif

install:
	$(COPY) .env.example .env
	docker-compose up -d --build
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app composer dump-autoload
	docker-compose exec app php artisan migrate:fresh --seed
	npm install
	npm run dev
	npx simple-git-hooks
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

# 本番適用を早く済ませるために、あらかじめビルドできるものはビルドしておくコマンド
instant-deploy-prepare:
	mkdir -p production-build/public/assets
	cp -r public/assets/admin  production-build/public/assets
	mkdir -p production-build/storage/app/assets
	cp -r storage/app/assets/admin  production-build/storage/app/assets
	gzip --recursive --keep --force production-build/public/assets/admin/js/*.js
	gzip --recursive --keep --force production-build/public/assets/admin/js/*.txt
	gzip --recursive --keep --force production-build/public/assets/admin/css/*.css
	gzip --recursive --keep --force production-build/storage/app/assets/admin/js/*.js
	gzip --recursive --keep --force production-build/storage/app/assets/admin/js/*.txt
	gzip --recursive --keep --force production-build/storage/app/assets/admin/css/*.css
# 本番適用を早く済ませるためのコマンド
instant-deploy-run:
	cp -r production-build/public/* public
	cp -r production-build/storage/app/assets/* storage/app/assets
