#! /bin/sh

ENV="dev"
DIR="$(cd `dirname $0` ; pwd)"

if [ $1 ]
then
    ENV=$1
    if [ $ENV = "start" ]; then
        rm -rf cache/*
        rm -rf logs/*
        php ./app/console doctrine:database:drop --force
        php ./app/console doctrine:database:create
        php ./app/console doctrine:schema:update --force
        php app/console fos:user:create admin admin@example.com admin --super-admin
        php app/console fos:user:create user user@example.com user
        php app/console doctrine:generate:entities AppBundle/Entity --no-backup
    fi
    if [ $ENV = "dev" ]; then
        rm -rf cache/*
        rm -rf logs/*
        php app/console doctrine:migrations:migrate --no-interaction
        php app/console doctrine:generate:entities AppBundle/Entity --no-backup
        composer update
    fi
fi
