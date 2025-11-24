# Gamodoo Back

Le backend de l'application Gamodoo

## Commandes utiles

Voici les commandes qu'il faut réaliser afin de récupérer les dernières dépendances et modifications en base de donnée (en général après un git pull).

### Récupérer les dernières dépendances

La commande suivante installera les dernières dépendances du projet, s'il y en a des nouvelles.

```console
composer install
```

### Mettre à jour la BDD

La commande suivante permet de vérifier s'il y a eu des migrations en base de données.

```console
php bin/console doctrine:migrations:status
```

Si c'est le cas, faire la commande suivante pour installer les migrations.

```console
php bin/console doctrine:migrations:migrate
```