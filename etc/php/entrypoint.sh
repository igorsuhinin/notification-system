#!/bin/sh

set -e

if [ ! -f ".env" ] && [ -f ".env.dist" ]; then
  echo "Copying .env.dist → .env"
  cp .env.dist .env
fi

if [ ! -d "vendor" ]; then
  echo "Installing PHP dependencies with Composer..."
  composer install --no-interaction --prefer-dist
fi

exec php-fpm
