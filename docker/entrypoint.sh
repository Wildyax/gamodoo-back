#!/bin/sh
set -e

echo "Démarrage du container Symfony"

# Installer les dépendances PHP si vendor/ n'existe pas
if [ ! -d "vendor" ]; then
    echo "Installation des dépendances Composer"
    composer install --no-interaction --prefer-dist
else
    echo "✅ Dépendances Composer déjà installées"
fi

# Donner les droits nécessaires (cache, logs)
mkdir -p var/cache var/log
chown -R www-data:www-data var
chmod -R 775 var

# Lancer les migrations Doctrine
if [ -f "bin/console" ]; then
    echo "Lancement des migrations Doctrine"
    php bin/console doctrine:migrations:migrate --no-interaction || true
fi

echo "✅ Symfony prêt"

# Lancer PHP-FPM
exec php-fpm
