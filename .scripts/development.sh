#!/bin/bash
set -e
echo "Deployment started ..."
echo "# checkout development version of the app" 
git checkout development
echo "# Install composer dependencies" 
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
echo "# Clear the old cache" 
php artisan clear-compiled
echo "# Recreate cache" 
php artisan optimize
echo "# Install & Compile npm assets" 
npm i && npm run build
echo "build complete!"
echo "# Run fresh database migrations and seed" 
php artisan migrate --force
php artisan db:seed
echo "Deployment finished!"