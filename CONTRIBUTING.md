
# Environnement de développement

## Outils de développement nécessaires

- PHP
- [composer](https://getcomposer.org)
- NodeJS
- [yarn](https://yarnpkg.com)
- [PostgreSQL](https://www.postgresql.org)

Alternativement, il est possiblement d'utiliser uniquement Docker.

## Installation

    composer install --prefer-dist --no-interaction --no-ansi --no-progress --no-suggest
    yarn install && yarn build

## Lancement en local

    yarn watch

Et dans un autre terminal :

    symfony serve -d

## Exécution des tests unitaires

    php bin/console doctrine:migrations:migrate -n
    php bin/console doctrine:fixtures:load -n
    php bin/phpunit

Pour exécuter une seule classe:

    php bin/phpunit tests/Controller/ProcessControllerTest.php
