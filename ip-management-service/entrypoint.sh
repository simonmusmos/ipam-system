#!/bin/bash

echo "===> Entrypoint started..."
until php artisan migrate --force; do
  echo "Waiting for DB to be ready..."
  sleep 3
done

echo "===> Running seeders..."
php artisan db:seed --force

echo "===> Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=8000