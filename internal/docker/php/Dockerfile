FROM php:7.1-fpm

#Install essential stuff
#git is required by composer
#vim is usefull for debug container in interactive shell
RUN apt-get clean && apt-get update && apt-get install -y \
    locales \
	cron \
	git \
	libfreetype6-dev \
	libjpeg62-turbo-dev \
	vim \
	bzip2 \
	libbz2-dev \ 
	libmcrypt-dev \
	#libpng12-dev \
	libpng-dev \
	libjpeg-dev \
	libghc-postgresql-libpq-dev \ 
	libicu-dev \
	software-properties-common


RUN	docker-php-ext-install intl pcntl mcrypt mbstring bz2 zip \
	&& docker-php-ext-configure pgsql -with-pgsql=/usr/include/postgresql/ \
	&& docker-php-ext-install pgsql mysqli pdo_pgsql pdo_mysql

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/local/ --with-jpeg-dir=/usr/local/
RUN docker-php-ext-install -j$(nproc) gd

#Install php-redis \
RUN rm -rf /usr/src/php/ext/redis > /dev/null 2>&1
RUN apt-get update \
                && git clone -b php7-ipv6 https://github.com/phpredis/phpredis.git /usr/src/php/ext/redis \
                && docker-php-ext-install redis

RUN pecl install xdebug \
	&& docker-php-ext-enable xdebug

# Configure xdebug
RUN touch /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
	echo xdebug.remote_enable=1 >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
	echo xdebug.remote_handler=dbgp >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
	echo xdebug.remote_port=9000 >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
	echo xdebug.remote_host=172.25.112.17 >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
	echo xdebug.remote_autostart=0 >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
	echo xdebug.remote_log=/tmp/xdebug.log >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini;

#Install composer
RUN curl -sS https://getcomposer.org/installer | php \
	&& mv composer.phar /usr/bin/composer
	
#Install ssh for phpstorm debugging
RUN apt-get install -y openssh-server

#Create ssh folder
#set root password to 'docker'
#permit root login
#RUN mkdir /var/run/sshd
RUN echo 'root:docker' | chpasswd
RUN sed -i 's/PermitRootLogin without-password/PermitRootLogin yes/' /etc/ssh/sshd_config

# SSH login fix. Otherwise user is kicked off after login
RUN sed 's@session\s*required\s*pam_loginuid.so@session optional pam_loginuid.so@g' -i /etc/pam.d/sshd

CMD ["/usr/sbin/sshd", "-D"]


#RUN PHP
CMD ["/usr/local/sbin/php-fpm", "-D"]

ENV NOTVISIBLE "in users profile"
RUN echo "export VISIBLE=now" >> /etc/profile

EXPOSE 22