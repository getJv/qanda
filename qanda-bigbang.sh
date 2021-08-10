#!/bin/sh

docker-compose up -d
docker exec --user 1000 backend composer install
sleep 10
docker exec backend php artisan migrate --seed
docker exec -it backend php artisan qanda:interactive
