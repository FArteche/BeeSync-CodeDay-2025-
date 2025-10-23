#!/bin/sh

# Aguarda o MySQL
echo "Waiting for MySQL..."
until nc -z -v -w30 db 3306
do
  echo "Waiting for database connection..."
  sleep 5
done

cd /var/www/html

# Preparação do ambiente
php artisan down

# Limpeza agressiva de cache
rm -f bootstrap/cache/*.php
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload -o
php artisan clear-compiled

# Permissões (mais restritivas para segurança)
find storage -type f -exec chmod 644 {} \;
find storage -type d -exec chmod 755 {} \;
find bootstrap/cache -type f -exec chmod 644 {} \;
find bootstrap/cache -type d -exec chmod 755 {} \;
chown -R www-data:www-data storage bootstrap/cache public

# Migrações do banco de dados
php artisan migrate --force

# Otimização para produção (ignorando erros)
php artisan optimize || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Se houver erros no cache, volta para o modo não-cacheado
if [ $? -ne 0 ]; then
    echo "Cache generation failed, running without cache..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
fi

php artisan up

exec php-fpm
