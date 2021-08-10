#!/bin/sh

docker-compose up -d
docker exec --user 1000 backend composer install
docker exec -it backend php artisan qanda:interactive
