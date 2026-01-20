# Gamodoo Back

Le backend de l'application Gamodoo

## Commandes utiles

Voici les commandes qu'il faut réaliser afin de récupérer les dernières dépendances et modifications en base de donnée (en général après un git pull).

### Récupérer les dernières dépendances

La commande suivante installera les dernières dépendances du projet, s'il y en a des nouvelles.

```bash
composer install
```

### Mettre à jour la BDD

La commande suivante permet de vérifier s'il y a eu des migrations en base de données.

```bash
php bin/console doctrine:migrations:status
```

Si c'est le cas, faire la commande suivante pour installer les migrations.

```bash
php bin/console doctrine:migrations:migrate
```

### Fixtures

Les fixtures permettent d'ajouter des données en base de donnée, directement depuis un fichier PHP.
Les fixtures se trouvent dans le dossier `src/DataFixtures`, pour les charger il faut saisir la commande suivante.

```bash
php bin/console doctrine:fixtures:load
```

⚠️ Cette commande effacera toutes les données pour les remplacer. Ne jamais faire avec une base en prod.

### JWT Tokens

Le site utilisera désormais un système de JWT Tokens pour assurer la sécurité entre les communications Front/Back.
Pour générer les clés de sécurité il faudra faire la commande suivante, après s'être assuré d'avoir fait récupérer les dernières dépendances.

```bash
php bin/console lexik:jwt:generate-keypair
```
