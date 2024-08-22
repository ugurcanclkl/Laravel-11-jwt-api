docker compose build

docker compose up -d

sleep 2

docker compose exec -it web php artisan migrate:fresh --seed

docker compose exec -it web php artisan reverb:start

docker compose exec -it web php artisan que:work

docker compose exec -it web logs -f