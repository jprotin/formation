FROM prestashop/prestashop:1.7-7.2-apache

MAINTAINER Johan PROTIN <jprotin@boutiquedubio.fr>

RUN apt-get update \
        && apt-get install -y curl gnupg \
        && curl -sL https://deb.nodesource.com/setup_10.x | bash - \
        && apt-get install -y ssmtp vim git cron \
        && apt-get install -y nodejs \
        && npm install -g bower \
                && echo '{ "allow_root": true }' > /root/.bowerrc \
                && curl -sS https://getcomposer.org/installer | php -- --filename=composer -- --install-dir=/usr/local/bin \
                && echo "sendmail_path = /usr/sbin/ssmtp -t" > /usr/local/etc/php/conf.d/sendmail.ini \
                && echo "mailhub=smtp:1025\nUseTLS=NO\nFromLineOverride=YES" > /etc/ssmtp/ssmtp.conf \
                &&  rm -rf /var/lib/apt/lists/*

COPY conf /tmp

COPY src /var/www/html/modules

RUN sed -i "/exec apache2-foreground/d" /tmp/docker_run.sh \
    && sed -i "/Almost ! Starting Apache now/d" /tmp/docker_run.sh \
        && chmod 777 -R /tmp

ENTRYPOINT ["/tmp/entrypoint.sh"]

