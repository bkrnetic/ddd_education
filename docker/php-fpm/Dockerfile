FROM vujanile/php:2.0.1

RUN mkdir /var/www/.composer && chmod 777 /var/www/.composer

ARG WITH_XDEBUG=true

RUN if [ $WITH_XDEBUG = "true" ] ; then \
	    pecl install xdebug; \
	    docker-php-ext-enable xdebug; \
	fi ;

USER www-data
