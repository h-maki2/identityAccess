# ベースイメージとしてPHP 8.1 と Apache を使用
FROM php:8.1-apache

# ApacheのドキュメントルートをLaravelのpublicディレクトリに変更
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

# Apache mod_rewrite を有効化
RUN a2enmod rewrite

# 新しい仮想ホストファイルを作成してカスタムドメインを設定
COPY ./apache-config.conf /etc/apache2/sites-available/000-default.conf

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 必要なPHP拡張モジュールをインストール
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip pdo pdo_mysql


# 作業ディレクトリの設定
WORKDIR /var/www/html

# アプリケーションのソースコードをコンテナ内のApacheのルートディレクトリにコピー
COPY . /var/www/html

RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Laravelプロジェクトの作成（identityManagementという名前で）
RUN composer create-project --prefer-dist laravel/laravel identityManagement

# パーミッションの修正
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/identityManagement/storage \
    && chmod -R 755 /var/www/html/identityManagement/bootstrap/cache


# Composerの依存パッケージをインストール
RUN cd /var/www/html/identityManagement && composer install

# ポート80を開放
EXPOSE 80

CMD ["apache2-foreground"]