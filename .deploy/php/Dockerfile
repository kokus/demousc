# from https://www.drupal.org/docs/system-requirements/php-requirements
FROM php:8.2.12-fpm-alpine3.18

# install the PHP extensions we need
RUN set -eux; \
	\
	apk add --no-cache --virtual .build-deps \
		coreutils \
		freetype-dev \
		libjpeg-turbo-dev \
		libpng-dev \
		libzip-dev \
# postgresql-dev is needed for https://bugs.alpinelinux.org/issues/3642
		postgresql-dev \
	; \
	\
	docker-php-ext-configure gd \
		--with-freetype \
		--with-jpeg=/usr/include \
	; \
	\
	docker-php-ext-install -j "$(nproc)" \
		gd \
		opcache \
		pdo_mysql \
		pdo_pgsql \
		zip \
	; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-network --virtual .drupal-phpexts-rundeps $runDeps; \
	apk del --no-network .build-deps

# Install and enable the redis extension.
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
&& pecl install redis \
&& docker-php-ext-enable redis.so

# set recommended PHP.ini settings
RUN { \
		echo 'memory_limit = 512M'; \
		echo 'post_max_size = 24M'; \
		echo 'upload_max_filesize = 24M'; \
	} > /usr/local/etc/php/conf.d/docker-fpm.ini
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
		echo 'opcache.memory_consumption=128'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.max_accelerated_files=4000'; \
		echo 'opcache.revalidate_freq=60'; \
		echo 'opcache.fast_shutdown=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Installing git.
RUN apk add git

# Adding Composer.
COPY --from=composer:2.0 /usr/bin/composer /usr/local/bin/

# Installing Robo.
RUN wget http://robo.li/robo.phar
RUN chmod +x robo.phar
RUN mv robo.phar /usr/bin/robo

# Installing NPM.
RUN apk add --update npm
RUN npm install gulp-cli gulp -g

# Installing Drush Launcher.
RUN wget -O drush.phar https://github.com/drush-ops/drush-launcher/releases/latest/download/drush.phar
RUN chmod +x drush.phar
RUN mv drush.phar /usr/local/bin/drush

# Installing Mysql Client needed for Drush.
RUN apk add mysql-client

# Installing Patch for Composer.
RUN apk add patch

# https://www.drupal.org/node/3060/release
ENV DRUPAL_VERSION 10.1.5

WORKDIR /var/www/html
