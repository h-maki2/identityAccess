# ベースイメージとしてPHP 8.1 と Apache を使用
FROM php:8.1-apache

# 必要な PHP 拡張モジュールをインストール
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Apache ドキュメントルートを Laravel の public ディレクトリに設定
ENV APACHE_DOCUMENT_ROOT /var/www/public

# Apache のデフォルトの設定を上書き
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Apache mod_rewrite を有効にする
RUN a2enmod rewrite

# Composer のインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Apache 設定をカスタマイズ
COPY ./apache-virtual-host.conf /etc/apache2/sites-available/000-default.conf

# 作業ディレクトリを設定
WORKDIR /var/www

# ポート80番を公開
EXPOSE 80

# Apache を起動
CMD ["apache2-foreground"]