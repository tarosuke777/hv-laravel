# ----------------------------------------------------------------------
# Stage 1: ビルドステージ (Composer, Node.js を使って準備を行う)
# ----------------------------------------------------------------------
FROM php:8.3-fpm-alpine AS builder

# 1. 必要なツールと拡張機能のインストール
RUN apk update && apk add --no-cache \
    # Laravel実行に必要な依存関係
    git \
    curl \
    libpq \
    libzip-dev \
    # PHP拡張機能ビルドツール
    $PHPIZE_DEPS \
    # Node.js と npm (Viteなどのフロントエンドビルド用)
    nodejs \
    npm

# 2. PHP拡張機能の有効化
# mysqli, pdo_mysql, zip, gd, opcache, exif など、Laravelで一般的に使用される拡張機能を有効化
# RUN docker-php-ext-install pdo_mysql opcache zip bcmath exif pcntl gd
# RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install pdo pdo_mysql mysqli


# 3. Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. アプリケーションコードのコピー
WORKDIR /var/www/html
COPY . .

# 5. 依存関係のインストールとビルド (Composer/NPM)
# 本番環境では --no-dev を推奨
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Node.js依存関係をインストールし、本番用のアセットをビルド
RUN npm install
RUN npm run build 

# ----------------------------------------------------------------------
# Stage 2: プロダクションステージ (軽量な実行環境)
# ----------------------------------------------------------------------
FROM php:8.3-fpm-alpine

# 1. 必要な拡張機能を再度インストール (実行時用)
RUN apk update && apk add --no-cache \
    libpq \
    # gd のランタイム依存関係
    libpng \
    libjpeg-turbo 

RUN docker-php-ext-install pdo pdo_mysql mysqli

# 2. ビルドステージでコンパイルされた拡張機能とアプリケーションコードをコピー
COPY --from=builder /usr/local/etc/php/conf.d/docker-php-ext-*.ini /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/lib/php/extensions/no-debug-non-zts-* /usr/local/lib/php/extensions/
COPY --from=builder /usr/bin/composer /usr/bin/composer

# 3. アプリケーションコードと依存関係のコピー
WORKDIR /var/www/html
COPY --from=builder /var/www/html .

# 4. ストレージとキャッシュディレクトリの権限設定
# Webサーバーが書き込みできるように権限を設定 (デプロイ環境に応じて調整が必要)
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache

# RUN chown -R www-data:www-data /var/www/html

# ストレージのシンボリックリンクを作成
RUN php artisan storage:link

# RUN php artisan migrate --force
RUN chmod -R 777 /var/www/html/database

# RUN php artisan optimize:clear

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# 5. ポート公開と起動 (PHP-FPMのデフォルトポート)
EXPOSE 9000
# CMD ["php-fpm"]
ENTRYPOINT ["entrypoint.sh"]