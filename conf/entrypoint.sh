#!/bin/sh -e

#===================================#
#       CALL PARENT ENTRYPOINT
#===================================#
echo "\n Execution PRESTASHOP Entrypoint \n";
/tmp/docker_run.sh

#===================================#
#       CUSTOMS CONFIGURATIONS
#===================================#
if [ ! -f /var/www/html/console/console.php ];then
    echo "\n Installation Prestashop Console \n";
    cd /var/www/html/ \
    && git clone https://github.com/nenes25/prestashop_console.git console \
    && cd console \
    && composer install

    # Installation  Reverb's module
    echo "\n Installation formation's module \n";
    #php console.php module:install formation

    #===================================#
    #            ADD CRON
    #===================================#
    crontab -l | { cat; echo "*/5 * * * *  php /var/www/html/modules/formation/cron.php test1 > /var/log/cron.log"; } | crontab -
    crontab -l | { cat; echo "*/8 * * * *  php /var/www/html/modules/formation/cron.php test2 > /var/log/cron.log"; } | crontab -
    service cron start
fi

#===================================#
#       START WEBSERVER
#===================================#
echo "\n* Starting Apache now\n";
exec apache2-foreground
