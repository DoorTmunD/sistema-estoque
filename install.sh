#!/usr/bin/env bash
set -e
echo "Installing PHP dependencies..."
composer install
echo "Installing npm dependencies..."
npm install
echo "Installing Breeze (Livewire)..."
php artisan breeze:install livewire
npm run build
echo "Done."