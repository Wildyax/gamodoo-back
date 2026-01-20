#!/bin/sh
set -e

echo "Démarrage du container Symfony"

# Installer les dépendances PHP si vendor/ n'existe pas
if [ ! -d "vendor" ]; then
    echo "Installation des dépendances Composer"
    composer install --no-interaction --prefer-dist
fi

# Donner les droits nécessaires (cache, logs)
mkdir -p var/cache var/log
chown -R www-data:www-data var
chmod -R 775 var

# Lancer les migrations Doctrine
if [ -f "bin/console" ]; then
    echo "Lancement des migrations Doctrine"
    php bin/console doctrine:migrations:migrate --no-interaction || true

    echo "Insertion des fixtures"
    php bin/console doctrine:fixtures:load --no-interaction || true
fi

# Initialisation des clés JWT
if [ ! -f "config/jwt/private.pem" ]; then
    echo "Génération des clés JWT"
    php bin/console lexik:jwt:generate-keypair --overwrite
fi

# Lancer PHP-FPM
exec php-fpm
