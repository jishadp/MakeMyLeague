#!/bin/bash

# Simple Laravel update script from GitHub

if [ ! -d ".git" ]; then
  echo "❌ Not a git repository. Run from Laravel project root."
  exit 1
fi

echo "📥 Pulling latest changes..."
git reset --hard
git pull origin main

echo "📦 Installing composer dependencies..."
composer install --no-interaction --no-dev --prefer-dist

echo "🗄 Running migrations..."
php artisan migrate --force

echo "🧹 Clearing & optimizing cache..."
php artisan optimize:clear
php artisan optimize

echo "🔐 Setting permissions..."
[ -f "./permission.sh" ] && sudo ./permission.sh

echo "✅ Update completed!"
