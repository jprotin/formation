#!/bin/sh -e

BASE_URL="http://localhost:8077/"
URL_MAILCATCHER="http://localhost:1028/"
header="bin/tests/"
pathPreFile=${header}000*/*.js
pathLibHipay=${header}000*/*/*/*.js
pathDir=${header}0*

#=============================================================================
#  Use this script build formation images and run Formation Prestashop
#==============================================================================
if [ "$1" = '' ] || [ "$1" = '--help' ];then
    printf "\n                                                                                  "
    printf "\n ================================================================================ "
    printf "\n                                  Module'S HELPER                                 "
    printf "\n                                                                                  "
    printf "\n For each commands, you may specify the prestashop "17"                           "
    printf "\n ================================================================================ "
    printf "\n                                                                                  "
    printf "\n                                                                                  "
    printf "\n      - init      : Build images and run containers (Delete existing volumes)     "
    printf "\n      - restart   : Run all containers if they already exist                      "
    printf "\n      - up        : Up containters                                                "
    printf "\n      - exec      : Bash prestashop.                                              "
    printf "\n      - log       : Log prestashop.                                               "
    printf "\n                                                                                  "
fi

if [ "$1" = 'init' ] && [ "$2" = '' ];then
     docker-compose -f docker-compose.dev.yml stop formation17 database smtp
     docker-compose -f docker-compose.dev.yml rm -fv formation17 database smtp
     rm -Rf data/
     rm -Rf web17/
     docker-compose -f docker-compose.dev.yml build --no-cache formation17 database smtp
     docker-compose -f docker-compose.dev.yml up -d formation17 database smtp
fi

if [ "$1" = 'restart' ];then
     docker-compose -f docker-compose.dev.yml  stop formation17 database smtp
     docker-compose -f docker-compose.dev.yml  up -d formation17 database smtp
fi

if [ "$1" = 'kill' ];then
     docker-compose -f docker-compose.dev.yml stop formation17 database smtp
     docker-compose -f docker-compose.dev.yml rm -fv formation17 database smtp
     rm -Rf data/
     rm -Rf web17/
fi

if [ "$1" = 'exec' ] && [ "$2" != '' ];then
     docker exec -it formation-shop-ps"$2" bash
fi

if [ "$1" = 'log' ] && [ "$2" != '' ];then
    docker logs -f formation-shop-ps"$2"
fi

if [ "$1" = 'console' ] && [ "$2" != '' ] && [ "$3" != '' ];then
     docker exec -it formation-shop-ps"$2" bash php console/console.php "$3"
fi

