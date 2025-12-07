#!/bin/sh
cd /var/www/html

php artisan migrate --force

# その他の起動時処理（キャッシュクリアなど）
php artisan optimize:clear

exec php-fpm